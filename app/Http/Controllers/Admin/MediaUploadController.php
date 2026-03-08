<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\UploadImageRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Handles media file uploads for the admin panel.
 */
class MediaUploadController extends Controller
{
    /**
     * Upload an image file.
     */
    public function __invoke(UploadImageRequest $request): JsonResponse
    {
        $file = $request->file('image');

        if (!$file) {
            Log::error('Media upload: No file received', [
                'has_file' => $request->hasFile('image'),
                'all_files' => array_keys($request->allFiles()),
            ]);

            return response()->json([
                'message' => 'No file was uploaded. Please try again.',
            ], 422);
        }

        if (!$file->isValid()) {
            Log::error('Media upload: Invalid file', [
                'error' => $file->getError(),
                'error_message' => $file->getErrorMessage(),
            ]);

            return response()->json([
                'message' => 'File upload failed: ' . $file->getErrorMessage(),
            ], 422);
        }

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('uploads/images', $filename, 'public');

        // Use relative URL so it works regardless of domain/port
        return response()->json([
            'url' => '/storage/' . $path,
            'path' => $path,
        ]);
    }
}
