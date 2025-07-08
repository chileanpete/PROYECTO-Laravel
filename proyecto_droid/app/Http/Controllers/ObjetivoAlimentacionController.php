<?php

namespace App\Http\Controllers;

use App\Models\ObjetivoAlimentacion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ObjetivoAlimentacionController extends Controller
{
    /**
     * Obtener objetivos de un usuario
     */
    public function porUsuario(int $idUsuario): JsonResponse
    {
        $objetivos = ObjetivoAlimentacion::with('usuario')
            ->where('id_usuario', $idUsuario)
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $objetivos
        ]);
    }

    /**
     * Obtener un objetivo específico
     */
    public function show(int $id): JsonResponse
    {
        $objetivo = ObjetivoAlimentacion::with('usuario')->find($id);

        if (!$objetivo) {
            return response()->json([
                'success' => false,
                'message' => 'Objetivo no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $objetivo
        ]);
    }

    /**
     * Crear un nuevo objetivo
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'tipo_objetivo' => 'required|in:perder_peso,ganar_peso,mantener_peso,ganar_musculo,mejorar_salud',
            'descripcion' => 'required|string|max:500',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'meta_calorias_diarias' => 'required|integer|min:1000|max:5000',
            'meta_proteinas_diarias' => 'nullable|numeric|min:0',
            'meta_carbohidratos_diarios' => 'nullable|numeric|min:0',
            'meta_grasas_diarias' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $objetivo = ObjetivoAlimentacion::create([
            'id_usuario' => $request->id_usuario,
            'tipo_objetivo' => $request->tipo_objetivo,
            'descripcion' => $request->descripcion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'meta_calorias_diarias' => $request->meta_calorias_diarias,
            'meta_proteinas_diarias' => $request->meta_proteinas_diarias ?? 0,
            'meta_carbohidratos_diarios' => $request->meta_carbohidratos_diarios ?? 0,
            'meta_grasas_diarias' => $request->meta_grasas_diarias ?? 0,
            'progreso_actual' => 0,
            'completado' => false,
            'notas' => $request->notas
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Objetivo creado exitosamente',
            'data' => $objetivo->load('usuario')
        ], 201);
    }

    /**
     * Actualizar un objetivo
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $objetivo = ObjetivoAlimentacion::find($id);

        if (!$objetivo) {
            return response()->json([
                'success' => false,
                'message' => 'Objetivo no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tipo_objetivo' => 'in:perder_peso,ganar_peso,mantener_peso,ganar_musculo,mejorar_salud',
            'descripcion' => 'string|max:500',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date|after:fecha_inicio',
            'meta_calorias_diarias' => 'integer|min:1000|max:5000',
            'meta_proteinas_diarias' => 'nullable|numeric|min:0',
            'meta_carbohidratos_diarios' => 'nullable|numeric|min:0',
            'meta_grasas_diarias' => 'nullable|numeric|min:0',
            'progreso_actual' => 'numeric|min:0|max:100',
            'notas' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $objetivo->update($request->only([
            'tipo_objetivo', 'descripcion', 'fecha_inicio', 'fecha_fin',
            'meta_calorias_diarias', 'meta_proteinas_diarias', 'meta_carbohidratos_diarios',
            'meta_grasas_diarias', 'progreso_actual', 'notas'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Objetivo actualizado exitosamente',
            'data' => $objetivo->load('usuario')
        ]);
    }

    /**
     * Eliminar un objetivo
     */
    public function destroy(int $id): JsonResponse
    {
        $objetivo = ObjetivoAlimentacion::find($id);

        if (!$objetivo) {
            return response()->json([
                'success' => false,
                'message' => 'Objetivo no encontrado'
            ], 404);
        }

        $objetivo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Objetivo eliminado exitosamente'
        ]);
    }

    /**
     * Marcar objetivo como completado
     */
    public function marcarCompletado(int $id): JsonResponse
    {
        $objetivo = ObjetivoAlimentacion::find($id);

        if (!$objetivo) {
            return response()->json([
                'success' => false,
                'message' => 'Objetivo no encontrado'
            ], 404);
        }

        if ($objetivo->completado) {
            return response()->json([
                'success' => false,
                'message' => 'El objetivo ya está marcado como completado'
            ], 400);
        }

        $objetivo->update([
            'completado' => true,
            'progreso_actual' => 100
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Objetivo marcado como completado'
        ]);
    }

    /**
     * Actualizar progreso del objetivo
     */
    public function actualizarProgreso(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'progreso_actual' => 'required|numeric|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Progreso inválido',
                'errors' => $validator->errors()
            ], 422);
        }

        $objetivo = ObjetivoAlimentacion::find($id);

        if (!$objetivo) {
            return response()->json([
                'success' => false,
                'message' => 'Objetivo no encontrado'
            ], 404);
        }

        $objetivo->update([
            'progreso_actual' => $request->progreso_actual,
            'completado' => $request->progreso_actual >= 100
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Progreso actualizado exitosamente',
            'data' => $objetivo
        ]);
    }

    /**
     * Obtener objetivos activos
     */
    public function activos(int $idUsuario): JsonResponse
    {
        $objetivos = ObjetivoAlimentacion::with('usuario')
            ->where('id_usuario', $idUsuario)
            ->where('completado', false)
            ->where('fecha_fin', '>=', now()->toDateString())
            ->orderBy('fecha_fin', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $objetivos
        ]);
    }

    /**
     * Obtener estadísticas de objetivos
     */
    public function estadisticas(int $idUsuario): JsonResponse
    {
        $objetivos = ObjetivoAlimentacion::where('id_usuario', $idUsuario);

        $estadisticas = [
            'total_objetivos' => $objetivos->count(),
            'objetivos_completados' => $objetivos->where('completado', true)->count(),
            'objetivos_activos' => $objetivos->where('completado', false)
                ->where('fecha_fin', '>=', now()->toDateString())->count(),
            'objetivos_vencidos' => $objetivos->where('completado', false)
                ->where('fecha_fin', '<', now()->toDateString())->count(),
            'promedio_progreso' => round($objetivos->avg('progreso_actual'), 2),
            'por_tipo' => $objetivos->selectRaw('tipo_objetivo, COUNT(*) as total')
                ->groupBy('tipo_objetivo')
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }
}
