<?php

namespace App\Http\Controllers;

use App\Models\Plato;
use App\Models\CategoriaComida;
use App\Models\LugarComida;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PlatoController extends Controller
{
    /**
     * Obtener todos los platos disponibles
     */
    public function index(Request $request): JsonResponse
    {
        $query = Plato::with(['lugar', 'categoria'])->where('disponible', true);

        // Filtros opcionales
        if ($request->has('categoria')) {
            $query->where('id_categoria', $request->categoria);
        }

        if ($request->has('lugar')) {
            $query->where('id_lugar', $request->lugar);
        }

        if ($request->has('vegetariano')) {
            $query->where('es_vegetariano', $request->vegetariano);
        }

        if ($request->has('vegano')) {
            $query->where('es_vegano', $request->vegano);
        }

        if ($request->has('sin_gluten')) {
            $query->where('sin_gluten', $request->sin_gluten);
        }

        $platos = $query->orderBy('nombre')->get();

        return response()->json([
            'success' => true,
            'data' => $platos
        ]);
    }

    /**
     * Obtener un plato específico
     */
    public function show($id): JsonResponse
    {
        $plato = Plato::with(['lugar', 'categoria'])->find($id);

        if (!$plato) {
            return response()->json([
                'success' => false,
                'message' => 'Plato no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $plato
        ]);
    }

    /**
     * Obtener categorías de comida
     */
    public function categorias(): JsonResponse
    {
        $categorias = CategoriaComida::all();

        return response()->json([
            'success' => true,
            'data' => $categorias
        ]);
    }

    /**
     * Obtener lugares de comida
     */
    public function lugares(): JsonResponse
    {
        $lugares = LugarComida::all();

        return response()->json([
            'success' => true,
            'data' => $lugares
        ]);
    }

    /**
     * Buscar platos por nombre
     */
    public function buscar(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Término de búsqueda requerido'
            ], 400);
        }

        $platos = Plato::with(['lugar', 'categoria'])
            ->where('disponible', true)
            ->where('nombre', 'like', "%{$query}%")
            ->orWhere('descripcion', 'like', "%{$query}%")
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $platos
        ]);
    }
}
