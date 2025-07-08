<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        
        // Token específico que debe coincidir
        $expectedToken = '10|HLJnefF28ohiw89XqJf5W9SoNzbgBoUj0eRt58Xtcb1844f3';
        
        if (!$token || $token !== $expectedToken) {
            return response()->json([
                'error' => 'Token de autenticación inválido',
                'message' => 'El token proporcionado no es válido. Use: Bearer 10|HLJnefF28ohiw89XqJf5W9SoNzbgBoUj0eRt58Xtcb1844f3'
            ], 401);
        }
        
        return $next($request);
    }
} 