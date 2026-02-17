<?php

use App\Http\Controllers\Api\ActivationController;
use App\Http\Controllers\Api\AppVersionController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\LegalDocumentController;
use App\Http\Controllers\Api\LicenseController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn () => response()->json(['pong' => true]));

Route::post('/license/verify', [LicenseController::class, 'verify']);
Route::post('/license/heartbeat', [LicenseController::class, 'heartbeat']);

Route::post('/device/register', [DeviceController::class, 'register']);

Route::get('/app/versions', [AppVersionController::class, 'index']);
Route::get('/app/versions/{appVersion}/download', [AppVersionController::class, 'download']);
Route::get('/app/latest', [AppVersionController::class, 'latest']);

Route::get('/legal/privacy-policy', [LegalDocumentController::class, 'privacyPolicy']);
Route::get('/legal/terms-and-conditions', [LegalDocumentController::class, 'termsAndConditions']);

Route::post('/activation/activate', [ActivationController::class, 'activate']);
Route::post('/activation/deactivate', [ActivationController::class, 'deactivate']);
