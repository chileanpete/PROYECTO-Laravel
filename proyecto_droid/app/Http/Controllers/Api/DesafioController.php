<?php

namespace App\Http\Controllers\Api;

use App\Models\Desafio;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class DesafioController extends Controller
{
    public function index()
    {
        // Esto devolverá todos los desafíos activos
        return Desafio::where('activo', true)
                      ->whereDate('fecha_inicio', '<=', now())
                      ->whereDate('fecha_fin', '>=', now())
                      ->get();
    }
}
