<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use App\Models\License;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppVersionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'platform' => 'nullable|string',
        ]);

        $versions = AppVersion::where('is_active', true)
            ->when($request->platform, fn ($query, $platform) => $query->where('platform', $platform))
            ->orderByDesc('released_at')
            ->get()
            ->map(fn (AppVersion $version) => [
                'version' => $version->version,
                'platform' => $version->platform,
                'release_notes' => $version->release_notes,
                'download_url' => $version->download_path
                    ? Storage::url($version->download_path)
                    : null,
                'released_at' => $version->released_at->toIso8601String(),
            ]);

        return response()->json(['data' => $versions]);
    }

    public function download(Request $request, AppVersion $appVersion): JsonResponse
    {
        $request->validate([
            'license_key' => 'required|string',
        ]);

        $license = License::where('license_key', $request->license_key)
            ->where('is_active', true)
            ->first();

        if (! $license) {
            return response()->json([
                'error' => 'Invalid or inactive license key.',
            ], 403);
        }

        if ($license->expiry_date && $license->expiry_date->isPast()) {
            return response()->json([
                'error' => 'License has expired.',
            ], 403);
        }

        if (! $appVersion->is_active) {
            return response()->json([
                'error' => 'This version is not available.',
            ], 404);
        }

        if (! $appVersion->download_path) {
            return response()->json([
                'error' => 'No download available for this version.',
            ], 404);
        }

        return response()->json([
            'version' => $appVersion->version,
            'platform' => $appVersion->platform,
            'download_url' => Storage::url($appVersion->download_path),
        ]);
    }

    public function latest(Request $request): JsonResponse
    {
        $request->validate([
            'platform' => 'required|string',
        ]);

        $version = AppVersion::where('platform', $request->platform)
            ->where('is_active', true)
            ->orderByDesc('released_at')
            ->first();

        if (! $version) {
            return response()->json([
                'error' => 'No version found for this platform.',
            ], 404);
        }

        return response()->json([
            'version' => $version->version,
            'release_notes' => $version->release_notes,
            'download_url' => $version->download_path
                ? Storage::url($version->download_path)
                : null,
            'released_at' => $version->released_at->toIso8601String(),
        ]);
    }
}
