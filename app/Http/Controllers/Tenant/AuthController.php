<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\LoginRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;
    public function login(LoginRequest $request): JsonResponse
    {
        
        $credentials = $request->only(['email', 'password']);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => ['ACCESO DENEGADO: Las credenciales no coinciden con nuestros registros en este nodo.'],
            ]);
        }

        // Seguridad: Regenerar sesión para evitar ataques de fijación
        $request->session()->regenerate();

        $user = Auth::user();
        
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'token' => $token, // <<-- ¡AQUÍ ESTÁ!
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
            ],
            'tenant' => [
                'id' => tenant('id'),
                'company_name' => tenant('company_name'),
                'plan' => tenant('plan') ?? 'enterprise' // Fallback por si acaso
            ],
        ], 'CONNECTION_ESTABLISHED');
    }

    public function me(): JsonResponse
    {
        $response = [
            'user' => [
                'id' => Auth::user()->id, 
                'name' => Auth::user()->name,
                'role' => Auth::user()->role,
            ],
            'tenant' => [
                'id' => tenant('id'),
                'company_name' => tenant('company_name'),
                'plan' => tenant('plan')
            ]
        ];
        return $this->success($response);
    }
}
