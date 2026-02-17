<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LegalDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class LegalDocumentController extends Controller
{
    public function privacyPolicy(): JsonResponse
    {
        $document = LegalDocument::active()
            ->ofType('privacy_policy')
            ->latest()
            ->first();

        if (! $document) {
            return response()->json([
                'error' => 'No active privacy policy found.',
            ], 404);
        }

        return response()->json($this->formatDocument($document));
    }

    public function termsAndConditions(): JsonResponse
    {
        $document = LegalDocument::active()
            ->ofType('terms_and_conditions')
            ->latest()
            ->first();

        if (! $document) {
            return response()->json([
                'error' => 'No active terms and conditions found.',
            ], 404);
        }

        return response()->json($this->formatDocument($document));
    }

    private function formatDocument(LegalDocument $document): array
    {
        return [
            'title' => $document->title,
            'type' => $document->type,
            'version' => $document->version,
            'content' => $document->content,
            'file_url' => $document->file_path
                ? Storage::url($document->file_path)
                : null,
            'effective_date' => $document->effective_date?->toIso8601String(),
        ];
    }
}
