<?php

namespace App\Http\Controllers;

use App\Models\Recomendacion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class RecomendacionController extends Controller
{
    /**
     * Obtener recomendaciones de un usuario
     */
    public function porUsuario(int $idUsuario): JsonResponse
    {
        $recomendaciones = Recomendacion::with('usuario')
            ->where('id_usuario', $idUsuario)
            ->orderBy('fecha_generacion', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $recomendaciones
        ]);
    }

    /**
     * Obtener una recomendación específica
     */
    public function show(int $id): JsonResponse
    {
        $recomendacion = Recomendacion::with('usuario')->find($id);

        if (!$recomendacion) {
            return response()->json([
                'success' => false,
                'message' => 'Recomendación no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $recomendacion
        ]);
    }

    /**
     * Crear una nueva recomendación
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'tipo_recomendacion' => 'required|in:nutricion,actividad_fisica,peso,salud,general',
            'titulo' => 'required|string|max:100',
            'descripcion' => 'required|string|max:200',
            'contenido' => 'required|string|max:2000',
            'prioridad' => 'required|integer|min:1|max:5',
            'categoria' => 'nullable|string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $recomendacion = Recomendacion::create([
            'id_usuario' => $request->id_usuario,
            'tipo_recomendacion' => $request->tipo_recomendacion,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'contenido' => $request->contenido,
            'fecha_generacion' => now(),
            'leida' => false,
            'prioridad' => $request->prioridad,
            'categoria' => $request->categoria
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Recomendación creada exitosamente',
            'data' => $recomendacion->load('usuario')
        ], 201);
    }

    /**
     * Actualizar una recomendación
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $recomendacion = Recomendacion::find($id);

        if (!$recomendacion) {
            return response()->json([
                'success' => false,
                'message' => 'Recomendación no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tipo_recomendacion' => 'in:nutricion,actividad_fisica,peso,salud,general',
            'titulo' => 'string|max:100',
            'descripcion' => 'string|max:200',
            'contenido' => 'string|max:2000',
            'prioridad' => 'integer|min:1|max:5',
            'categoria' => 'nullable|string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $recomendacion->update($request->only([
            'tipo_recomendacion', 'titulo', 'descripcion', 'contenido', 'prioridad', 'categoria'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Recomendación actualizada exitosamente',
            'data' => $recomendacion->load('usuario')
        ]);
    }

    /**
     * Eliminar una recomendación
     */
    public function destroy(int $id): JsonResponse
    {
        $recomendacion = Recomendacion::find($id);

        if (!$recomendacion) {
            return response()->json([
                'success' => false,
                'message' => 'Recomendación no encontrada'
            ], 404);
        }

        $recomendacion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Recomendación eliminada exitosamente'
        ]);
    }

    /**
     * Marcar recomendación como leída
     */
    public function marcarLeida(int $id): JsonResponse
    {
        $recomendacion = Recomendacion::find($id);

        if (!$recomendacion) {
            return response()->json([
                'success' => false,
                'message' => 'Recomendación no encontrada'
            ], 404);
        }

        if ($recomendacion->leida) {
            return response()->json([
                'success' => false,
                'message' => 'La recomendación ya está marcada como leída'
            ], 400);
        }

        $recomendacion->update([
            'leida' => true,
            'fecha_lectura' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Recomendación marcada como leída'
        ]);
    }

    /**
     * Obtener recomendaciones no leídas
     */
    public function noLeidas(int $idUsuario): JsonResponse
    {
        $recomendaciones = Recomendacion::with('usuario')
            ->where('id_usuario', $idUsuario)
            ->where('leida', false)
            ->orderBy('prioridad', 'desc')
            ->orderBy('fecha_generacion', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $recomendaciones
        ]);
    }

    /**
     * Obtener recomendaciones por tipo
     */
    public function porTipo(int $idUsuario, string $tipo): JsonResponse
    {
        $recomendaciones = Recomendacion::with('usuario')
            ->where('id_usuario', $idUsuario)
            ->where('tipo_recomendacion', $tipo)
            ->orderBy('fecha_generacion', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $recomendaciones
        ]);
    }

    /**
     * Obtener recomendaciones por prioridad
     */
    public function porPrioridad(int $idUsuario, int $prioridad): JsonResponse
    {
        $recomendaciones = Recomendacion::with('usuario')
            ->where('id_usuario', $idUsuario)
            ->where('prioridad', $prioridad)
            ->orderBy('fecha_generacion', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $recomendaciones
        ]);
    }

    /**
     * Obtener recomendaciones recientes
     */
    public function recientes(int $idUsuario, int $dias = 7): JsonResponse
    {
        $fechaLimite = now()->subDays($dias);

        $recomendaciones = Recomendacion::with('usuario')
            ->where('id_usuario', $idUsuario)
            ->where('fecha_generacion', '>=', $fechaLimite)
            ->orderBy('fecha_generacion', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $recomendaciones
        ]);
    }

    /**
     * Obtener estadísticas de recomendaciones
     */
    public function estadisticas(int $idUsuario): JsonResponse
    {
        $recomendaciones = Recomendacion::where('id_usuario', $idUsuario);

        $estadisticas = [
            'total_recomendaciones' => $recomendaciones->count(),
            'recomendaciones_leidas' => $recomendaciones->where('leida', true)->count(),
            'recomendaciones_no_leidas' => $recomendaciones->where('leida', false)->count(),
            'por_tipo' => $recomendaciones->selectRaw('tipo_recomendacion, COUNT(*) as total')
                ->groupBy('tipo_recomendacion')
                ->get(),
            'por_prioridad' => $recomendaciones->selectRaw('prioridad, COUNT(*) as total')
                ->groupBy('prioridad')
                ->orderBy('prioridad')
                ->get(),
            'recomendaciones_hoy' => $recomendaciones->whereDate('fecha_generacion', today())->count(),
            'recomendaciones_semana' => $recomendaciones->where('fecha_generacion', '>=', now()->subWeek())->count(),
            'promedio_lectura_dias' => $recomendaciones->whereNotNull('fecha_lectura')
                ->get()
                ->avg(function($rec) {
                    return $rec->fecha_generacion->diffInDays($rec->fecha_lectura);
                })
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }
}
