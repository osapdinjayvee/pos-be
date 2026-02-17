<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Terminal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivationController extends Controller
{
    public function activate(Request $request): JsonResponse
    {
        $request->validate([
            'license_key' => 'required|string',
            'device_identifier' => 'required|string',
            'device_name' => 'nullable|string|max:255',
            'device_type' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
        ]);

        $license = License::with('plan.features')
            ->where('license_key', $request->license_key)
            ->where('is_active', true)
            ->first();

        if (! $license) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid or inactive license key.',
            ], 422);
        }

        if ($license->expiry_date && $license->expiry_date->isPast()) {
            return response()->json([
                'success' => false,
                'error' => 'License has expired.',
            ], 422);
        }

        // Check if device is already registered and active
        $existingTerminal = $license->terminals()
            ->where('device_identifier', $request->device_identifier)
            ->first();

        if ($existingTerminal && $existingTerminal->is_active) {
            // Return existing token — revoke old and issue fresh one
            $license->tokens()->where('name', $request->device_identifier)->delete();
            $token = $license->createToken($request->device_identifier);

            return response()->json([
                'success' => true,
                'token' => $token->plainTextToken,
                'terminal' => $this->terminalData($existingTerminal),
                'plan' => $this->planData($license),
            ]);
        }

        // Check terminal limit — license override takes priority, then plan default
        $maxTerminals = $license->max_terminals ?? $license->plan?->max_terminals;

        if ($maxTerminals !== null) {
            $activeCount = $license->terminals()->where('is_active', true)->count();

            if ($activeCount >= $maxTerminals) {
                return response()->json([
                    'success' => false,
                    'error' => 'Terminal limit reached. Maximum allowed: '.$maxTerminals.'.',
                ], 422);
            }
        }

        // Reactivate or create terminal
        if ($existingTerminal) {
            $existingTerminal->update([
                'is_active' => true,
                'activated_at' => now(),
                'device_name' => $request->device_name ?? $existingTerminal->device_name,
                'device_type' => $request->device_type ?? $existingTerminal->device_type,
                'metadata' => $request->metadata ?? $existingTerminal->metadata,
            ]);
            $terminal = $existingTerminal;
        } else {
            $terminal = Terminal::create([
                'license_id' => $license->id,
                'device_identifier' => $request->device_identifier,
                'device_name' => $request->device_name,
                'device_type' => $request->device_type,
                'is_active' => true,
                'activated_at' => now(),
                'metadata' => $request->metadata,
            ]);
        }

        $token = $license->createToken($request->device_identifier);

        return response()->json([
            'success' => true,
            'token' => $token->plainTextToken,
            'terminal' => $this->terminalData($terminal),
            'plan' => $this->planData($license),
        ], 201);
    }

    public function deactivate(Request $request): JsonResponse
    {
        $request->validate([
            'license_key' => 'required|string',
            'device_identifier' => 'required|string',
        ]);

        $license = License::where('license_key', $request->license_key)
            ->where('is_active', true)
            ->first();

        if (! $license) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid or inactive license key.',
            ], 422);
        }

        $terminal = $license->terminals()
            ->where('device_identifier', $request->device_identifier)
            ->where('is_active', true)
            ->first();

        if (! $terminal) {
            return response()->json([
                'success' => false,
                'error' => 'Terminal not found or already deactivated.',
            ], 404);
        }

        $terminal->update(['is_active' => false]);

        // Revoke Sanctum tokens for this device
        $license->tokens()->where('name', $request->device_identifier)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Terminal deactivated successfully.',
        ]);
    }

    private function terminalData(Terminal $terminal): array
    {
        return [
            'id' => $terminal->id,
            'device_identifier' => $terminal->device_identifier,
            'device_name' => $terminal->device_name,
            'device_type' => $terminal->device_type,
            'activated_at' => $terminal->activated_at->toIso8601String(),
        ];
    }

    private function planData(License $license): ?array
    {
        if (! $license->plan) {
            return null;
        }

        return [
            'name' => $license->plan->name,
            'tier_level' => $license->plan->tier_level,
            'max_terminals' => $license->max_terminals ?? $license->plan->max_terminals,
            'max_users' => $license->plan->max_users,
            'features' => $license->plan->features->pluck('key')->toArray(),
        ];
    }
}
