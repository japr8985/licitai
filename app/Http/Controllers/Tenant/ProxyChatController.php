<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProxyChatController extends Controller
{
    /**
     * Recibe un mensaje desde el frontend
     * y lo envia a N8N
     */
    public function message_from_frontend_to_n8n(Request $request)
    {

    }
    /**
     * Recibe un mensaje desde n8n
     * y lo envia al frontend
     */
    public function message_from_n8n_to_frontend(Request $request)
    {
        
    }
}
