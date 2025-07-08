<?php

namespace App\Http\Controllers;

use App\Models\CategoriaComida;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CategoriaComidaController extends Controller
{
    /**
     * Obtener todas las categorías
     */
    public function index(): JsonResponse
    {
        $categorias = CategoriaComida::where('activo', true)->get();

        return response()->json([
            'success' => true,
            'data' => $categorias
        ]);
    }

    /**
     * Obtener una categoría específica
     */
    public function show(int $id): JsonResponse
    {
        $categoria = CategoriaComida::with('platos')->find($id);

        if (!$categoria) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $categoria
        ]);
    }

    /**
     * Crear una nueva categoría
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:categorias_comida,nombre',
            'descripcion' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $categoria = CategoriaComida::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Categoría creada exitosamente',
            'data' => $categoria
        ], 201);
    }

    /**
     * Actualizar una categoría
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $categoria = CategoriaComida::find($id);

        if (!$categoria) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:100|unique:categorias_comida,nombre,' . $id . ',id_categoria',
            'descripcion' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $categoria->update($request->only(['nombre', 'descripcion']));

        return response()->json([
            'success' => true,
            'message' => 'Categoría actualizada exitosamente',
            'data' => $categoria
        ]);
    }

    /**
     * Eliminar una categoría (soft delete)
     */
    public function destroy(int $id): JsonResponse
    {
        $categoria = CategoriaComida::find($id);

        if (!$categoria) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        $categoria->update(['activo' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Categoría desactivada exitosamente'
        ]);
    }
}
