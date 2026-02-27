<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadFileController extends Controller
{
    use ApiResponse;
    public function uploadChunk(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file',
            'upload_uuid' => 'required|string',
            'chunk_index' => 'required|integer',
        ]);

        $file = $request->file('file');
        $uuid = $request->upload_uuid;
        $index = $request->chunk_index;

        // Guardamos cada pedazo como: chunks/{uuid}/0, chunks/{uuid}/1, etc.
        // Usamos el disco 'local' temporalmente o 'tenant' si prefieres
        $path = "temp_chunks/{$uuid}";
        
        $file->storeAs($path, (string)$index, 'local');

        return $this->success(['status' => 'CHUNK_RECEIVED'], 'CHUNK_INDEX_' . $index);
    }
}
