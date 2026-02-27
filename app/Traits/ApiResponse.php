<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public function response(mixed $data, int $status = 200, string $message = '', array $headers = []): JsonResponse
    {
        return response()->json([
            'content' => $data,
            'success' => $status >= 200 && $status < 400,
            'message' => $message
        ], $status, $headers);
    }
    /**
     * Helper para respuestas exitosas rápidas
     */
    public function success(mixed $data, string $message = '', int $status = 200): JsonResponse
    {
        return $this->response($data, $status, $message);
    }

    /**
     * Helper para errores (capturados manualmente)
     */
    public function error(string $message, int $status = 400, mixed $data = null): JsonResponse
    {
        return $this->response($data, $status, $message);
    }
}
