<?php

namespace App\Http\Controllers;

use App\Models\UsuarioDesafio;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class UsuarioDesafioController extends Controller
{
    /**
     * Obtener desafíos de un usuario
     */
    public function porUsuario(int $idUsuario): JsonResponse
    {
        $usuarioDesafios = UsuarioDesafio::with(['usuario', 'desafio'])
            ->where('id_usuario', $idUsuario)
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $usuarioDesafios
        ]);
    }

    /**
     * Obtener un registro específico
     */
    public function show(int $id): JsonResponse
    {
        $usuarioDesafio = UsuarioDesafio::with(['usuario', 'desafio'])->find($id);

        if (!$usuarioDesafio) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $usuarioDesafio
        ]);
    }

    /**
     * Inscribir usuario a un desafío
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'id_desafio' => 'required|exists:desafios,id_desafio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar si ya está inscrito
        $existente = UsuarioDesafio::where('id_usuario', $request->id_usuario)
            ->where('id_desafio', $request->id_desafio)
            ->first();

        if ($existente) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario ya está inscrito en este desafío'
            ], 400);
        }

        // Obtener información del desafío
        $desafio = \App\Models\Desafio::find($request->id_desafio);
        
        if (!$desafio || !$desafio->activo) {
            return response()->json([
                'success' => false,
                'message' => 'Desafío no disponible'
            ], 400);
        }

        $usuarioDesafio = UsuarioDesafio::create([
            'id_usuario' => $request->id_usuario,
            'id_desafio' => $request->id_desafio,
            'fecha_inicio' => now()->toDateString(),
            'fecha_fin' => now()->addDays($desafio->duracion_dias)->toDateString(),
            'progreso_actual' => 0,
            'estado' => 'en_progreso',
            'puntos_obtenidos' => 0,
            'completado' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario inscrito al desafío exitosamente',
            'data' => $usuarioDesafio->load(['usuario', 'desafio'])
        ], 201);
    }

    /**
     * Actualizar progreso del desafío
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $usuarioDesafio = UsuarioDesafio::find($id);

        if (!$usuarioDesafio) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'progreso_actual' => 'required|integer|min:0|max:100',
            'estado' => 'in:en_progreso,pausado,abandonado,completado'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $progresoAnterior = $usuarioDesafio->progreso_actual;
        $nuevoProgreso = $request->progreso_actual;

        // Calcular puntos si se completó
        $puntosObtenidos = $usuarioDesafio->puntos_obtenidos;
        if ($nuevoProgreso >= 100 && $progresoAnterior < 100) {
            $puntosObtenidos = $usuarioDesafio->desafio->puntos_recompensa;
        }

        $usuarioDesafio->update([
            'progreso_actual' => $nuevoProgreso,
            'estado' => $request->estado ?? $usuarioDesafio->estado,
            'puntos_obtenidos' => $puntosObtenidos,
            'completado' => $nuevoProgreso >= 100
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Progreso actualizado exitosamente',
            'data' => $usuarioDesafio->load(['usuario', 'desafio'])
        ]);
    }

    /**
     * Eliminar inscripción
     */
    public function destroy(int $id): JsonResponse
    {
        $usuarioDesafio = UsuarioDesafio::find($id);

        if (!$usuarioDesafio) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado'
            ], 404);
        }

        $usuarioDesafio->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inscripción eliminada exitosamente'
        ]);
    }

    /**
     * Obtener desafíos activos del usuario
     */
    public function activos(int $idUsuario): JsonResponse
    {
        $usuarioDesafios = UsuarioDesafio::with(['usuario', 'desafio'])
            ->where('id_usuario', $idUsuario)
            ->where('estado', 'en_progreso')
            ->where('completado', false)
            ->where('fecha_fin', '>=', now()->toDateString())
            ->orderBy('fecha_fin', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $usuarioDesafios
        ]);
    }

    /**
     * Obtener desafíos completados del usuario
     */
    public function completados(int $idUsuario): JsonResponse
    {
        $usuarioDesafios = UsuarioDesafio::with(['usuario', 'desafio'])
            ->where('id_usuario', $idUsuario)
            ->where('completado', true)
            ->orderBy('fecha_fin', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $usuarioDesafios
        ]);
    }

    /**
     * Obtener desafíos vencidos del usuario
     */
    public function vencidos(int $idUsuario): JsonResponse
    {
        $usuarioDesafios = UsuarioDesafio::with(['usuario', 'desafio'])
            ->where('id_usuario', $idUsuario)
            ->where('fecha_fin', '<', now()->toDateString())
            ->where('completado', false)
            ->orderBy('fecha_fin', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $usuarioDesafios
        ]);
    }

    /**
     * Marcar desafío como completado
     */
    public function marcarCompletado(int $id): JsonResponse
    {
        $usuarioDesafio = UsuarioDesafio::find($id);

        if (!$usuarioDesafio) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado'
            ], 404);
        }

        if ($usuarioDesafio->completado) {
            return response()->json([
                'success' => false,
                'message' => 'El desafío ya está marcado como completado'
            ], 400);
        }

        $usuarioDesafio->update([
            'progreso_actual' => 100,
            'estado' => 'completado',
            'puntos_obtenidos' => $usuarioDesafio->desafio->puntos_recompensa,
            'completado' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Desafío marcado como completado',
            'data' => $usuarioDesafio->load(['usuario', 'desafio'])
        ]);
    }

    /**
     * Obtener estadísticas de desafíos del usuario
     */
    public function estadisticas(int $idUsuario): JsonResponse
    {
        $usuarioDesafios = UsuarioDesafio::where('id_usuario', $idUsuario);

        $estadisticas = [
            'total_desafios' => $usuarioDesafios->count(),
            'desafios_completados' => $usuarioDesafios->where('completado', true)->count(),
            'desafios_activos' => $usuarioDesafios->where('estado', 'en_progreso')
                ->where('completado', false)->count(),
            'desafios_vencidos' => $usuarioDesafios->where('fecha_fin', '<', now()->toDateString())
                ->where('completado', false)->count(),
            'total_puntos_obtenidos' => $usuarioDesafios->sum('puntos_obtenidos'),
            'promedio_progreso' => round($usuarioDesafios->avg('progreso_actual'), 2),
            'por_estado' => $usuarioDesafios->selectRaw('estado, COUNT(*) as total')
                ->groupBy('estado')
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }
}
