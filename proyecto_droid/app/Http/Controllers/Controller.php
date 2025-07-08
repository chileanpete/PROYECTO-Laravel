<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Configura headers de respuesta personalizados
     */
    protected function setCustomHeaders($response, $headers = [])
    {
        $defaultHeaders = [
            'X-API-Version' => 'v1',
            'X-Response-Time' => microtime(true) - LARAVEL_START,
        ];

        $headers = array_merge($defaultHeaders, $headers);

        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }

        return $response;
    }
}
