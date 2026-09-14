<?php

namespace App\Tenant\Service;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

final class FileService
{
    /**
     * Ensambla fragmentos de archivos de forma agnóstica.
     *
     * @param string $uuid Identificador único de la carga.
     * @param int $totalChunks Cantidad total de segmentos.
     * @param string $fileName Nombre original del archivo.
     * @param string $subFolder Carpeta de destino (ej: 'projects', 'logos', 'evidence').
     * @return string Ruta relativa final del archivo.
     */
    public function assembleChunks(string $uuid, int $totalChunks, string $fileName, string $subFolder = 'general'): string
    {
        $tempDisk = Storage::disk('local');
        $tenantDisk = Storage::disk('tenants');
        $tenantId = tenant('id');

        // Estructura: {tenant_id}/{subFolder}/{uuid}_{name}
        $relativeDir = "{$tenantId}/{$subFolder}";
        $finalFileName = Str::uuid() . "_" . $fileName;
        $relativeFilePath = "{$relativeDir}/{$finalFileName}";

        // 1. Asegurar que el directorio del tenant y la subcarpeta existan
        if (!$tenantDisk->exists($relativeDir)) {
            $tenantDisk->makeDirectory($relativeDir);
        }

        // 2. Crear el archivo base
        $tenantDisk->put($relativeFilePath, '');
        $fullPath = $tenantDisk->path($relativeFilePath);

        // 3. Bucle de ensamblaje
        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkPath = "temp_chunks/{$uuid}/{$i}";

            if (!$tempDisk->exists($chunkPath)) {
                throw new \Exception("PROTOCOL_ERROR: Missing chunk index {$i} for upload {$uuid}");
            }

            // Append del contenido
            file_put_contents($fullPath, $tempDisk->get($chunkPath), FILE_APPEND);

            // Limpieza proactiva del chunk
            $tempDisk->delete($chunkPath);
        }

        // 4. Limpieza del directorio temporal
        $tempDisk->deleteDirectory("temp_chunks/{$uuid}");

        return $relativeFilePath;
    }
    /**
     * Genera una URL firmada y temporal para acceso externo (ej: n8n).
     * @param string $path Ruta relativa guardada en la DB.
     * @param int $expiration Minutos de validez.
     * @return string
     */
    public function generateTemporaryUrl(string $path, int $expiration = 10): string
    {
        // Si usamos S3 en el futuro, Storage::temporaryUrl() funcionará nativamente.
        // Para local, usamos las rutas firmadas de Laravel.
        return URL::temporarySignedRoute(
            'tenant.files.download', // Nombre de la ruta que crearemos en el paso 2
            now()->addMinutes($expiration),
            ['tenant' => tenant('id'),'path' => $path]
        );
    }
}
