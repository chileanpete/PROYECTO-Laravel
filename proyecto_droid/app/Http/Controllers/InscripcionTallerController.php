<?php

namespace App\Http\Controllers;

use App\Models\InscripcionTaller;
use App\Models\TallerRecreativo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InscripcionTallerController extends Controller
{
    /**
     * Obtener todas las inscripciones del usuario autenticado
     */
    public function index(): JsonResponse
    {
        $inscripciones = InscripcionTaller::with(['taller'])
            ->where('id_usuario', Auth::id())
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
    public function show($id): JsonResponse
    {
        $inscripcion = InscripcionTaller::with(['taller'])
            ->where('id_usuario', Auth::id())
            ->find($id);

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
     * Crear una nueva inscripción a taller
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_taller' => 'required|exists:talleres_recreativos,id_taller',
            'calificacion' => 'nullable|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar si ya está inscrito
        $inscripcionExistente = InscripcionTaller::where('id_usuario', Auth::id())
            ->where('id_taller', $request->id_taller)
            ->first();

        if ($inscripcionExistente) {
            return response()->json([
                'success' => false,
                'message' => 'Ya estás inscrito en este taller'
            ], 409);
        }

        // Verificar si el taller está activo y tiene cupo
        $taller = TallerRecreativo::find($request->id_taller);
        if (!$taller->activo) {
            return response()->json([
                'success' => false,
                'message' => 'El taller no está disponible'
            ], 400);
        }

        $inscritos = InscripcionTaller::where('id_taller', $request->id_taller)->count();
        if ($inscritos >= $taller->cupo_maximo) {
            return response()->json([
                'success' => false,
                'message' => 'El taller ya no tiene cupos disponibles'
            ], 400);
        }

        $inscripcion = InscripcionTaller::create([
            'id_usuario' => Auth::id(),
            'id_taller' => $request->id_taller,
            'fecha_inscripcion' => now(),
            'estado' => 'inscrito',
            'calificacion' => $request->calificacion,
            'comentario' => $request->comentario,
            'puntos_obtenidos' => 20 // Puntos por inscribirse a un taller
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inscripción al taller creada exitosamente',
            'data' => $inscripcion->load('taller')
        ], 201);
    }

    /**
     * Actualizar una inscripción
     */
    public function update(Request $request, $id): JsonResponse
    {
        $inscripcion = InscripcionTaller::where('id_usuario', Auth::id())->find($id);

        if (!$inscripcion) {
            return response()->json([
                'success' => false,
                'message' => 'Inscripción no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'estado' => 'sometimes|in:inscrito,completado,cancelado',
            'calificacion' => 'nullable|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['estado', 'calificacion', 'comentario']);
        
        if ($request->has('estado') && $request->estado === 'completado') {
            $data['puntos_obtenidos'] = 50; // Puntos por completar el taller
        }

        $inscripcion->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Inscripción actualizada exitosamente',
            'data' => $inscripcion->load('taller')
        ]);
    }

    /**
     * Eliminar una inscripción
     */
    public function destroy($id): JsonResponse
    {
        $inscripcion = InscripcionTaller::where('id_usuario', Auth::id())->find($id);

        if (!$inscripcion) {
            return response()->json([
                'success' => false,
                'message' => 'Inscripción no encontrada'
            ], 404);
        }

        $inscripcion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inscripción eliminada exitosamente'
        ]);
    }

    /**
     * Obtener talleres disponibles
     */
    public function talleresDisponibles(): JsonResponse
    {
        $talleres = TallerRecreativo::where('activo', true)
            ->where('fecha_inicio', '>', now())
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $talleres
        ]);
    }

    /**
     * Obtener estadísticas de talleres del usuario
     */
    public function estadisticas(): JsonResponse
    {
        $estadisticas = [
            'total_inscripciones' => InscripcionTaller::where('id_usuario', Auth::id())->count(),
            'talleres_completados' => InscripcionTaller::where('id_usuario', Auth::id())
                ->where('estado', 'completado')
                ->count(),
            'talleres_activos' => InscripcionTaller::where('id_usuario', Auth::id())
                ->where('estado', 'inscrito')
                ->count(),
            'puntos_totales' => InscripcionTaller::where('id_usuario', Auth::id())
                ->sum('puntos_obtenidos'),
            'calificacion_promedio' => InscripcionTaller::where('id_usuario', Auth::id())
                ->whereNotNull('calificacion')
                ->avg('calificacion')
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }
}
