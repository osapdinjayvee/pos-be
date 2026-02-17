<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Terminal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'license_key' => 'required|string',
            'device_uid' => 'required|string',
            'terminal_id' => 'nullable|string',
            'device_name' => 'nullable|string',
            'platform' => 'nullable|string',
            'app_version' => 'nullable|string',
        ]);

        $license = License::with('plan')
            ->where('license_key', $request->license_key)
            ->where('is_active', true)
            ->first();

        if (!$license) {
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

        // Check if device already registered (re-install scenario)
        $existing = $license->terminals()
            ->where('device_identifier', $request->device_uid)
            ->first();

        if ($existing) {
            $existing->update([
                'device_name' => $request->device_name ?? $existing->device_name,
                'device_type' => $request->platform ?? $existing->device_type,
                'is_active' => true,
                'last_seen_at' => now(),
                'metadata' => array_merge($existing->metadata ?? [], array_filter([
                    'app_version' => $request->app_version,
                    'terminal_id' => $request->terminal_id,
                ])),
            ]);

            return response()->json([
                'success' => true,
                'device_id' => $existing->id,
                'registered_at' => $existing->activated_at->toIso8601String(),
            ]);
        }

        // Check max terminals limit
        $maxTerminals = $license->max_terminals ?? $license->plan?->max_terminals;
        if ($maxTerminals) {
            $activeCount = $license->activeTerminalCount();
            if ($activeCount >= $maxTerminals) {
                return response()->json([
                    'success' => false,
                    'error' => 'max_terminals_exceeded',
                    'max_terminals' => $maxTerminals,
                    'active_count' => $activeCount,
                ], 409);
            }
        }

        // Create new terminal
        $terminal = $license->terminals()->create([
            'device_identifier' => $request->device_uid,
            'device_name' => $request->device_name,
            'device_type' => $request->platform,
            'is_active' => true,
            'activated_at' => now(),
            'last_seen_at' => now(),
            'metadata' => array_filter([
                'app_version' => $request->app_version,
                'terminal_id' => $request->terminal_id,
            ]),
        ]);

        return response()->json([
            'success' => true,
            'device_id' => $terminal->id,
            'registered_at' => $terminal->activated_at->toIso8601String(),
        ]);
    }
}