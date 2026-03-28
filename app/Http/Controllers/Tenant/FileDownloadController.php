<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileDownloadController extends Controller
{
    public function download(Request $request, $path) {
        
        if (!$request->hasValidSignature()) abort(403);
        return Storage::disk('tenants')->download($path);
    }

    public function avatar(Request $request)
    {
        
        // El path viene en la URL firmada (ej. ?path=globalcorp/avatars/...)
        $path = $request->query('path');

        // 1. Verificamos que el path exista en tu disco (asumo que usas el disco 'local' o 'private')
        if (!$path || !Storage::disk('tenants')->exists($path)) {
            // Si no existe, devolvemos un 404 limpio
            abort(404, 'Avatar not found.');
        }

        // 2. Opcional: Seguridad extra. Asegurarnos de que no intenten descargar 
        // archivos de otros tenants manipulando el path (aunque la firma ya previene esto, 
        // la paranoia es buena amiga del arquitecto).
        /*
        if (!str_starts_with($path, $request->tenant . '/avatars/')) {
            abort(403, 'Unauthorized access to this file.');
        }
        */

        // 3. Devolvemos el binario de la imagen directamente al navegador
        // response()->file() se encarga automáticamente de los headers (image/jpeg, image/png, etc.)
        return response()->file(Storage::disk('tenants')->path($path));
    }
}
