<?php

namespace App\Domain\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Auth\Requests\LoginRequest;
use App\Domain\Auth\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Login de usuario
     * 
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            // DEBUG: Ver qué está llegando
            \Log::info('Login attempt', [
                'identifier' => $request->input('identifier'),
                'has_password' => !empty($request->input('password'))
            ]);

            $result = $this->authService->login(
                $request->input('identifier'),
                $request->input('password')
            );

            \Log::info('Login success', ['result' => $result]);

            return response()->json([
                'success' => true,
                'message' => 'Login exitoso',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            // DEBUG: Ver el error exacto
            \Log::error('Login error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }

    /**
     * Refresh access token
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $result = $this->authService->refreshToken($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Token renovado exitosamente',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }

    /**
     * Logout - Revocar tokens
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Logout exitoso'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener información del usuario autenticado
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $result = $this->authService->me($request->user());

            return response()->json([
                'success' => true,
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
