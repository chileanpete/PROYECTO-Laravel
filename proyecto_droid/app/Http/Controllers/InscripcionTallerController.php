<?php

namespace App\Http\Controllers;

use App\Models\InscripcionTaller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class InscripcionTallerController extends Controller
{
    /**
     * Obtener inscripciones de un usuario
     */
    public function porUsuario(int $idUsuario): JsonResponse
    {
        $inscripciones = InscripcionTaller::with(['usuario', 'taller'])
            ->where('id_usuario', $idUsuario)
            ->orderBy('fecha_inscripcion', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $inscripciones
        ]);
    }

    /**
     * Obtener una inscripción específica
     */
    public function show(int $id): JsonResponse
    {
        $inscripcion = InscripcionTaller::with(['usuario', 'taller'])->find($id);

        if (!$inscripcion) {
            return response()->json([
                'success' => false,
                'message' => 'Inscripción no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $inscripcion
        ]);
    }

    /**
     * Inscribir usuario a un taller
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'id_taller' => 'required|exists:taller_recreativos,id_taller'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar si ya está inscrito
        $existente = InscripcionTaller::where('id_usuario', $request->id_usuario)
            ->where('id_taller', $request->id_taller)
            ->first();

        if ($existente) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario ya está inscrito en este taller'
            ], 400);
        }

        // Obtener información del taller
        $taller = \App\Models\TallerRecreativo::find($request->id_taller);
        
        if (!$taller || !$taller->activo) {
            return response()->json([
                'success' => false,
                'message' => 'Taller no disponible'
            ], 400);
        }

        // Verificar cupos disponibles
        if ($taller->capacidad_actual >= $taller->capacidad_maxima) {
            return response()->json([
                'success' => false,
                'message' => 'Taller sin cupos disponibles'
            ], 400);
        }

        // Verificar si el taller ya pasó
        if ($taller->fecha_fin < now()->toDateString()) {
            return response()->json([
                'success' => false,
                'message' => 'El taller ya finalizó'
            ], 400);
        }

        $inscripcion = InscripcionTaller::create([
            'id_usuario' => $request->id_usuario,
            'id_taller' => $request->id_taller,
            'fecha_inscripcion' => now(),
            'estado' => 'inscrito',
            'asistio' => false
        ]);

        // Actualizar capacidad del taller
        $taller->increment('capacidad_actual');

        return response()->json([
            'success' => true,
            'message' => 'Usuario inscrito al taller exitosamente',
            'data' => $inscripcion->load(['usuario', 'taller'])
        ], 201);
    }

    /**
     * Actualizar inscripción
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $inscripcion = InscripcionTaller::find($id);

        if (!$inscripcion) {
            return response()->json([
                'success' => false,
                'message' => 'Inscripción no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'estado' => 'in:inscrito,cancelado,asistio,no_asistio',
            'asistio' => 'boolean',
            'calificacion' => 'nullable|integer|min:1|max:5',
            'comentarios' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $inscripcion->update($request->only(['estado', 'asistio', 'calificacion', 'comentarios']));

        return response()->json([
            'success' => true,
            'message' => 'Inscripción actualizada exitosamente',
            'data' => $inscripcion->load(['usuario', 'taller'])
        ]);
    }

    /**
     * Cancelar inscripción
     */
    public function destroy(int $id): JsonResponse
    {
        $inscripcion = InscripcionTaller::find($id);

        if (!$inscripcion) {
            return response()->json([
                'success' => false,
                'message' => 'Inscripción no encontrada'
            ], 404);
        }

        // Actualizar capacidad del taller
        $taller = $inscripcion->taller;
        if ($taller) {
            $taller->decrement('capacidad_actual');
        }

        $inscripcion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inscripción cancelada exitosamente'
        ]);
    }

    /**
     * Marcar asistencia
     */
    public function marcarAsistencia(int $id): JsonResponse
    {
        $inscripcion = InscripcionTaller::find($id);

        if (!$inscripcion) {
            return response()->json([
                'success' => false,
                'message' => 'Inscripción no encontrada'
            ], 404);
        }

        if ($inscripcion->asistio) {
            return response()->json([
                'success' => false,
                'message' => 'La asistencia ya está marcada'
            ], 400);
        }

        $inscripcion->update([
            'asistio' => true,
            'estado' => 'asistio'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asistencia marcada exitosamente',
            'data' => $inscripcion->load(['usuario', 'taller'])
        ]);
    }

    /**
     * Calificar taller
     */
    public function calificar(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'calificacion' => 'required|integer|min:1|max:5',
            'comentarios' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $inscripcion = InscripcionTaller::find($id);

        if (!$inscripcion) {
            return response()->json([
                'success' => false,
                'message' => 'Inscripción no encontrada'
            ], 404);
        }

        if (!$inscripcion->asistio) {
            return response()->json([
                'success' => false,
                'message' => 'Solo se puede calificar talleres a los que se asistió'
            ], 400);
        }

        $inscripcion->update([
            'calificacion' => $request->calificacion,
            'comentarios' => $request->comentarios
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Taller calificado exitosamente',
            'data' => $inscripcion->load(['usuario', 'taller'])
        ]);
    }

    /**
     * Obtener talleres futuros del usuario
     */
    public function futuros(int $idUsuario): JsonResponse
    {
        $inscripciones = InscripcionTaller::with(['usuario', 'taller'])
            ->where('id_usuario', $idUsuario)
            ->whereHas('taller', function($query) {
                $query->where('fecha_fin', '>=', now()->toDateString());
            })
            ->where('estado', 'inscrito')
            ->orderBy('fecha_inscripcion', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $inscripciones
        ]);
    }

    /**
     * Obtener talleres pasados del usuario
     */
    public function pasados(int $idUsuario): JsonResponse
    {
        $inscripciones = InscripcionTaller::with(['usuario', 'taller'])
            ->where('id_usuario', $idUsuario)
            ->whereHas('taller', function($query) {
                $query->where('fecha_fin', '<', now()->toDateString());
            })
            ->orderBy('fecha_inscripcion', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $inscripciones
        ]);
    }

    /**
     * Obtener talleres a los que asistió el usuario
     */
    public function asistidos(int $idUsuario): JsonResponse
    {
        $inscripciones = InscripcionTaller::with(['usuario', 'taller'])
            ->where('id_usuario', $idUsuario)
            ->where('asistio', true)
            ->orderBy('fecha_inscripcion', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $inscripciones
        ]);
    }

    /**
     * Obtener talleres cancelados del usuario
     */
    public function cancelados(int $idUsuario): JsonResponse
    {
        $inscripciones = InscripcionTaller::with(['usuario', 'taller'])
            ->where('id_usuario', $idUsuario)
            ->where('estado', 'cancelado')
            ->orderBy('fecha_inscripcion', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $inscripciones
        ]);
    }

    /**
     * Obtener estadísticas de talleres del usuario
     */
    public function estadisticas(int $idUsuario): JsonResponse
    {
        $inscripciones = InscripcionTaller::where('id_usuario', $idUsuario);

        $estadisticas = [
            'total_inscripciones' => $inscripciones->count(),
            'talleres_asistidos' => $inscripciones->where('asistio', true)->count(),
            'talleres_futuros' => $inscripciones->whereHas('taller', function($query) {
                $query->where('fecha_fin', '>=', now()->toDateString());
            })->where('estado', 'inscrito')->count(),
            'talleres_pasados' => $inscripciones->whereHas('taller', function($query) {
                $query->where('fecha_fin', '<', now()->toDateString());
            })->count(),
            'talleres_cancelados' => $inscripciones->where('estado', 'cancelado')->count(),
            'promedio_calificacion' => round($inscripciones->whereNotNull('calificacion')->avg('calificacion'), 2),
            'por_estado' => $inscripciones->selectRaw('estado, COUNT(*) as total')
                ->groupBy('estado')
                ->get(),
            'tasa_asistencia' => $inscripciones->count() > 0 ? 
                round(($inscripciones->where('asistio', true)->count() / $inscripciones->count()) * 100, 2) : 0
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }
}
