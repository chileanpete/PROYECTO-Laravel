<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    /**
     * Genera una respuesta exitosa en formato UnifiedApiResponse
     */
    protected function successResponse($data = null, string $message = null, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
            'error' => null,
            'errors' => null,
            'status_code' => $statusCode,
            'timestamp' => now()->toISOString()
        ], $statusCode);
    }
    
    /**
     * Genera una respuesta de error en formato UnifiedApiResponse
     */
    protected function errorResponse(string $message, int $statusCode = 500, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => null,
            'message' => $message,
            'error' => $message,
            'errors' => $errors,
            'status_code' => $statusCode,
            'timestamp' => now()->toISOString()
        ], $statusCode);
    }
    
    /**
     * Genera una respuesta de validación fallida en formato UnifiedApiResponse
     */
    protected function validationErrorResponse(array $errors, string $message = 'Datos de validación incorrectos'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => null,
            'message' => $message,
            'error' => $message,
            'errors' => $errors,
            'status_code' => 422,
            'timestamp' => now()->toISOString()
        ], 422);
    }
}
