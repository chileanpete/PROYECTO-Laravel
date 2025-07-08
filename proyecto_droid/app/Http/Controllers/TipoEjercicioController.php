<?php

namespace App\Http\Controllers;

use App\Models\TipoEjercicio;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class TipoEjercicioController extends Controller
{
    /**
     * Obtener todos los tipos de ejercicio
     */
    public function index(): JsonResponse
    {
        $tipos = TipoEjercicio::where('activo', true)->get();

        return response()->json([
            'success' => true,
            'data' => $tipos
        ]);
    }

    /**
     * Obtener un tipo de ejercicio específico
     */
    public function show(int $id): JsonResponse
    {
        $tipo = TipoEjercicio::find($id);

        if (!$tipo) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de ejercicio no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tipo
        ]);
    }

    /**
     * Crear un nuevo tipo de ejercicio
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:tipos_ejercicio,nombre',
            'descripcion' => 'nullable|string',
            'categoria' => 'required|in:cardio,fuerza,flexibilidad,equilibrio,deportes',
            'intensidad_promedio' => 'required|integer|min:1|max:5',
            'calorias_por_hora' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $tipo = TipoEjercicio::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'categoria' => $request->categoria,
            'intensidad_promedio' => $request->intensidad_promedio,
            'calorias_por_hora' => $request->calorias_por_hora,
            'activo' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tipo de ejercicio creado exitosamente',
            'data' => $tipo
        ], 201);
    }

    /**
     * Actualizar un tipo de ejercicio
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $tipo = TipoEjercicio::find($id);

        if (!$tipo) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de ejercicio no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:100|unique:tipos_ejercicio,nombre,' . $id . ',id_tipo_ejercicio',
            'descripcion' => 'nullable|string',
            'categoria' => 'in:cardio,fuerza,flexibilidad,equilibrio,deportes',
            'intensidad_promedio' => 'integer|min:1|max:5',
            'calorias_por_hora' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $tipo->update($request->only([
            'nombre', 'descripcion', 'categoria', 'intensidad_promedio', 'calorias_por_hora'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Tipo de ejercicio actualizado exitosamente',
            'data' => $tipo
        ]);
    }

    /**
     * Eliminar un tipo de ejercicio (soft delete)
     */
    public function destroy(int $id): JsonResponse
    {
        $tipo = TipoEjercicio::find($id);

        if (!$tipo) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de ejercicio no encontrado'
            ], 404);
        }

        $tipo->update(['activo' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Tipo de ejercicio desactivado exitosamente'
        ]);
    }

    /**
     * Obtener tipos por categoría
     */
    public function porCategoria(string $categoria): JsonResponse
    {
        $tipos = TipoEjercicio::where('categoria', $categoria)
            ->where('activo', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tipos
        ]);
    }
}
