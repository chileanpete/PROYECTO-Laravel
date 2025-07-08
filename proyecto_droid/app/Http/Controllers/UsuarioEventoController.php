<?php

namespace App\Http\Controllers;

use App\Models\UsuarioEvento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class UsuarioEventoController extends Controller
{
    /**
     * Obtener eventos de un usuario
     */
    public function porUsuario(int $idUsuario): JsonResponse
    {
        $usuarioEventos = UsuarioEvento::with(['usuario', 'eventoAcademico'])
            ->where('id_usuario', $idUsuario)
            ->orderBy('fecha_inscripcion', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $usuarioEventos
        ]);
    }

    /**
     * Obtener un registro específico
     */
    public function show(int $id): JsonResponse
    {
        $usuarioEvento = UsuarioEvento::with(['usuario', 'eventoAcademico'])->find($id);

        if (!$usuarioEvento) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $usuarioEvento
        ]);
    }

    /**
     * Inscribir usuario a un evento
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'id_evento' => 'required|exists:evento_academicos,id_evento'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar si ya está inscrito
        $existente = UsuarioEvento::where('id_usuario', $request->id_usuario)
            ->where('id_evento', $request->id_evento)
            ->first();

        if ($existente) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario ya está inscrito en este evento'
            ], 400);
        }

        // Obtener información del evento
        $evento = \App\Models\EventoAcademico::find($request->id_evento);
        
        if (!$evento || !$evento->activo) {
            return response()->json([
                'success' => false,
                'message' => 'Evento no disponible'
            ], 400);
        }

        // Verificar cupos disponibles
        if ($evento->capacidad_actual >= $evento->capacidad_maxima) {
            return response()->json([
                'success' => false,
                'message' => 'Evento sin cupos disponibles'
            ], 400);
        }

        // Verificar si el evento ya pasó
        if ($evento->fecha_fin < now()->toDateString()) {
            return response()->json([
                'success' => false,
                'message' => 'El evento ya finalizó'
            ], 400);
        }

        $usuarioEvento = UsuarioEvento::create([
            'id_usuario' => $request->id_usuario,
            'id_evento' => $request->id_evento,
            'fecha_inscripcion' => now(),
            'estado' => 'inscrito',
            'asistio' => false
        ]);

        // Actualizar capacidad del evento
        $evento->increment('capacidad_actual');

        return response()->json([
            'success' => true,
            'message' => 'Usuario inscrito al evento exitosamente',
            'data' => $usuarioEvento->load(['usuario', 'eventoAcademico'])
        ], 201);
    }

    /**
     * Actualizar registro de evento
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $usuarioEvento = UsuarioEvento::find($id);

        if (!$usuarioEvento) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'estado' => 'in:inscrito,cancelado,asistio,no_asistio',
            'asistio' => 'boolean',
            'notas' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $usuarioEvento->update($request->only(['estado', 'asistio', 'notas']));

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado exitosamente',
            'data' => $usuarioEvento->load(['usuario', 'eventoAcademico'])
        ]);
    }

    /**
     * Cancelar inscripción
     */
    public function destroy(int $id): JsonResponse
    {
        $usuarioEvento = UsuarioEvento::find($id);

        if (!$usuarioEvento) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado'
            ], 404);
        }

        // Actualizar capacidad del evento
        $evento = $usuarioEvento->eventoAcademico;
        if ($evento) {
            $evento->decrement('capacidad_actual');
        }

        $usuarioEvento->delete();

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
        $usuarioEvento = UsuarioEvento::find($id);

        if (!$usuarioEvento) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado'
            ], 404);
        }

        if ($usuarioEvento->asistio) {
            return response()->json([
                'success' => false,
                'message' => 'La asistencia ya está marcada'
            ], 400);
        }

        $usuarioEvento->update([
            'asistio' => true,
            'estado' => 'asistio'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asistencia marcada exitosamente',
            'data' => $usuarioEvento->load(['usuario', 'eventoAcademico'])
        ]);
    }

    /**
     * Obtener eventos futuros del usuario
     */
    public function futuros(int $idUsuario): JsonResponse
    {
        $usuarioEventos = UsuarioEvento::with(['usuario', 'eventoAcademico'])
            ->where('id_usuario', $idUsuario)
            ->whereHas('eventoAcademico', function($query) {
                $query->where('fecha_fin', '>=', now()->toDateString());
            })
            ->where('estado', 'inscrito')
            ->orderBy('fecha_inscripcion', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $usuarioEventos
        ]);
    }

    /**
     * Obtener eventos pasados del usuario
     */
    public function pasados(int $idUsuario): JsonResponse
    {
        $usuarioEventos = UsuarioEvento::with(['usuario', 'eventoAcademico'])
            ->where('id_usuario', $idUsuario)
            ->whereHas('eventoAcademico', function($query) {
                $query->where('fecha_fin', '<', now()->toDateString());
            })
            ->orderBy('fecha_inscripcion', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $usuarioEventos
        ]);
    }

    /**
     * Obtener eventos a los que asistió el usuario
     */
    public function asistidos(int $idUsuario): JsonResponse
    {
        $usuarioEventos = UsuarioEvento::with(['usuario', 'eventoAcademico'])
            ->where('id_usuario', $idUsuario)
            ->where('asistio', true)
            ->orderBy('fecha_inscripcion', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $usuarioEventos
        ]);
    }

    /**
     * Obtener eventos cancelados del usuario
     */
    public function cancelados(int $idUsuario): JsonResponse
    {
        $usuarioEventos = UsuarioEvento::with(['usuario', 'eventoAcademico'])
            ->where('id_usuario', $idUsuario)
            ->where('estado', 'cancelado')
            ->orderBy('fecha_inscripcion', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $usuarioEventos
        ]);
    }

    /**
     * Obtener estadísticas de eventos del usuario
     */
    public function estadisticas(int $idUsuario): JsonResponse
    {
        $usuarioEventos = UsuarioEvento::where('id_usuario', $idUsuario);

        $estadisticas = [
            'total_inscripciones' => $usuarioEventos->count(),
            'eventos_asistidos' => $usuarioEventos->where('asistio', true)->count(),
            'eventos_futuros' => $usuarioEventos->whereHas('eventoAcademico', function($query) {
                $query->where('fecha_fin', '>=', now()->toDateString());
            })->where('estado', 'inscrito')->count(),
            'eventos_pasados' => $usuarioEventos->whereHas('eventoAcademico', function($query) {
                $query->where('fecha_fin', '<', now()->toDateString());
            })->count(),
            'eventos_cancelados' => $usuarioEventos->where('estado', 'cancelado')->count(),
            'por_estado' => $usuarioEventos->selectRaw('estado, COUNT(*) as total')
                ->groupBy('estado')
                ->get(),
            'tasa_asistencia' => $usuarioEventos->count() > 0 ? 
                round(($usuarioEventos->where('asistio', true)->count() / $usuarioEventos->count()) * 100, 2) : 0
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }
}
