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
}
