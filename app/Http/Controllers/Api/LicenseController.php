<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'license_key' => 'required|string',
        ]);

        $license = License::with('plan.features')
            ->where('license_key', $request->license_key)
            ->where('is_active', true)
            ->first();

        if (! $license) {
            return response()->json([
                'valid' => false,
                'error' => 'Invalid or inactive license key.',
            ]);
        }

        if ($license->expiry_date && $license->expiry_date->isPast()) {
            return response()->json([
                'valid' => false,
                'error' => 'License has expired.',
            ]);
        }

        $response = [
            'valid' => true,
            'license_type' => $license->license_type,
            'business_name' => $license->business_name,
            'expiry_date' => $license->expiry_date?->toDateString(),
        ];

        if ($license->plan) {
            $response['plan'] = [
                'name' => $license->plan->name,
                'tier_level' => $license->plan->tier_level,
                'max_terminals' => $license->plan->max_terminals,
                'max_users' => $license->plan->max_users,
                'features' => $license->plan->features->pluck('key')->toArray(),
            ];
        }

        $maxTerminals = $license->max_terminals ?? $license->plan?->max_terminals;
        $response['max_terminals'] = $maxTerminals;
        $response['active_device_count'] = $license->activeTerminalCount();

        return response()->json($response);
    }

    public function heartbeat(Request $request): JsonResponse
    {
        $request->validate([
            'license_key' => 'required|string',
            'device_uid' => 'nullable|string',
        ]);

        $license = License::where('license_key', $request->license_key)->first();

        if (!$license) {
            return response()->json([
                'status' => 'revoked',
                'error' => 'License not found.',
            ]);
        }

        $license->update(['last_heartbeat_at' => now()]);

        if ($request->device_uid) {
            $license->terminals()
                ->where('device_identifier', $request->device_uid)
                ->update(['last_seen_at' => now()]);
        }

        // Determine status
        $status = 'active';
        if (!$license->is_active) {
            $status = 'revoked';
        } elseif ($license->expiry_date && $license->expiry_date->isPast()) {
            $status = 'expired';
        }

        return response()->json([
            'status' => $status,
            'expiry_date' => $license->expiry_date?->toDateString(),
            'plan' => $license->plan?->name,
        ]);
    }
}
