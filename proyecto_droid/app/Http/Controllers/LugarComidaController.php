<?php

namespace App\Http\Controllers;

use App\Models\LugarComida;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class LugarComidaController extends Controller
{
    /**
     * Obtener todos los lugares
     */
    public function index(): JsonResponse
    {
        $lugares = LugarComida::where('activo', true)->get();

        return response()->json([
            'success' => true,
            'data' => $lugares
        ]);
    }

    /**
     * Obtener un lugar específico
     */
    public function show(int $id): JsonResponse
    {
        $lugar = LugarComida::with('platos')->find($id);

        if (!$lugar) {
            return response()->json([
                'success' => false,
                'message' => 'Lugar no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $lugar
        ]);
    }

    /**
     * Crear un nuevo lugar
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'direccion' => 'required|string|max:200',
            'telefono' => 'nullable|string|max:20',
            'horario_apertura' => 'required|date_format:H:i',
            'horario_cierre' => 'required|date_format:H:i|after:horario_apertura',
            'tipo_establecimiento' => 'required|in:restaurante,cafeteria,comedor,bar,otro'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $lugar = LugarComida::create([
            'nombre' => $request->nombre,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono,
            'horario_apertura' => $request->horario_apertura,
            'horario_cierre' => $request->horario_cierre,
            'tipo_establecimiento' => $request->tipo_establecimiento,
            'activo' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lugar creado exitosamente',
            'data' => $lugar
        ], 201);
    }

    /**
     * Actualizar un lugar
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $lugar = LugarComida::find($id);

        if (!$lugar) {
            return response()->json([
                'success' => false,
                'message' => 'Lugar no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:100',
            'direccion' => 'string|max:200',
            'telefono' => 'nullable|string|max:20',
            'horario_apertura' => 'date_format:H:i',
            'horario_cierre' => 'date_format:H:i|after:horario_apertura',
            'tipo_establecimiento' => 'in:restaurante,cafeteria,comedor,bar,otro'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $lugar->update($request->only([
            'nombre', 'direccion', 'telefono', 'horario_apertura', 
            'horario_cierre', 'tipo_establecimiento'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Lugar actualizado exitosamente',
            'data' => $lugar
        ]);
    }

    /**
     * Eliminar un lugar (soft delete)
     */
    public function destroy(int $id): JsonResponse
    {
        $lugar = LugarComida::find($id);

        if (!$lugar) {
            return response()->json([
                'success' => false,
                'message' => 'Lugar no encontrado'
            ], 404);
        }

        $lugar->update(['activo' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Lugar desactivado exitosamente'
        ]);
    }
}
