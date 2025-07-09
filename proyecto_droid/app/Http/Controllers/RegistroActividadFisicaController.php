<?php

namespace App\Http\Controllers;

use App\Models\RegistroActividadFisica;
use App\Models\TipoEjercicio;
use App\Models\RutinaEjercicio;
use App\Models\ExportacionDatos;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Usuario;
use Illuminate\Support\Facades\Log;


class RegistroActividadFisicaController extends Controller
{
    /**
     * Obtener todos los registros de actividad física
     */
    public function index(Request $request): JsonResponse
    {
        if (!$request->has('id_usuario')) {
            return response()->json([
                'success' => false,
                'message' => 'El parámetro id_usuario es obligatorio'
            ], 400);
        }

        $query = RegistroActividadFisica::with([
            'usuario', 'rutina', 'rutinaEjercicio', 'tipoEjercicio'
        ])->where('id_usuario', $request->id_usuario);

        // Filtrar por fecha
        if ($request->has('fecha_inicio')) {
            $query->where('fecha_actividad', '>=', $request->fecha_inicio);
        }

        if ($request->has('fecha_fin')) {
            $query->where('fecha_actividad', '<=', $request->fecha_fin);
        }

        // Filtrar por tipo de ejercicio
        if ($request->has('id_tipo_ejercicio')) {
            $query->where('id_tipo_ejercicio', $request->id_tipo_ejercicio);
        }

        // Filtrar por estado de completado
        if ($request->has('completada')) {
            $query->where('completada', $request->completada);
        }

        $registros = $query->orderBy('fecha_actividad', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $registros
        ]);
    }

    /**
     * Obtener un registro específico
     */
    public function show(int $id): JsonResponse
    {
        $registro = RegistroActividadFisica::with([
            'usuario', 'rutina', 'rutinaEjercicio', 'tipoEjercicio'
        ])->find($id);

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'Registro de actividad no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $registro
        ]);
    }

    /**
     * Crear un nuevo registro de actividad física
     */
    public function store(Request $request): JsonResponse
    {
        Log::info('Datos recibidos en registro actividad:', $request->all());

        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'id_rutina' => 'nullable|exists:rutinas_ejercicio,id_rutina',
            'id_rutina_ejercicio' => 'nullable|exists:rutinas_ejercicios,id_rutina_ejercicio',
            'id_tipo_ejercicio' => 'nullable|exists:tipos_ejercicio,id_tipo_ejercicio',
            'fecha_actividad' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'duracion_minutos' => 'required|integer|min:1',
            'calorias_quemadas' => 'required|integer|min:0',
            'intensidad' => 'integer|min:1|max:5',
            'comentario' => 'nullable|string',
            'completada' => 'boolean'
        ]);

        if ($validator->fails()) {
            Log::error('Validación fallida:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Calcular puntos basados en duración e intensidad
        $puntos = $this->calcularPuntos($request->duracion_minutos, $request->intensidad);

        try {
            $registro = RegistroActividadFisica::create([
                'id_usuario' => $request->id_usuario,
                'id_rutina' => $request->id_rutina,
                'id_rutina_ejercicio' => $request->id_rutina_ejercicio,
                'id_tipo_ejercicio' => $request->id_tipo_ejercicio,
                'fecha_actividad' => $request->fecha_actividad,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'duracion_minutos' => $request->duracion_minutos,
                'calorias_quemadas' => $request->calorias_quemadas,
                'intensidad' => $request->intensidad ?? 3,
                'comentario' => $request->comentario,
                'puntos_obtenidos' => $puntos,
                'completada' => $request->completada ?? true
            ]);
        } catch (\Exception $e) {
            Log::error('Error al crear registro:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Error interno al guardar',
                'error' => $e->getMessage()
            ], 500);
        }

        // Actualizar puntos totales del usuario
        $usuario = Usuario::find($request->id_usuario);
        $usuario->increment('puntos_totales', $puntos);

        return response()->json([
            'success' => true,
            'message' => 'Registro de actividad creado exitosamente',
            'data' => $registro->load(['usuario', 'tipoEjercicio'])
        ], 201);
    }

    /**
     * Actualizar un registro de actividad física
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $registro = RegistroActividadFisica::find($id);

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'Registro de actividad no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_rutina' => 'nullable|exists:rutinas_ejercicio,id_rutina',
            'id_rutina_ejercicio' => 'nullable|exists:rutinas_ejercicios,id_rutina_ejercicio',
            'id_tipo_ejercicio' => 'nullable|exists:tipos_ejercicio,id_tipo_ejercicio',
            'fecha_actividad' => 'date',
            'hora_inicio' => 'date_format:H:i',
            'hora_fin' => 'date_format:H:i|after:hora_inicio',
            'duracion_minutos' => 'integer|min:1',
            'calorias_quemadas' => 'integer|min:0',
            'intensidad' => 'integer|min:1|max:5',
            'comentario' => 'nullable|string',
            'completada' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Si se actualiza la duración o intensidad, recalcular puntos
        $puntosAnteriores = $registro->puntos_obtenidos;
        $nuevosPuntos = $puntosAnteriores;

        if ($request->has('duracion_minutos') || $request->has('intensidad')) {
            $duracion = $request->duracion_minutos ?? $registro->duracion_minutos;
            $intensidad = $request->intensidad ?? $registro->intensidad;
            $nuevosPuntos = $this->calcularPuntos($duracion, $intensidad);
        }

        $registro->update(array_merge($request->only([
            'id_rutina', 'id_rutina_ejercicio', 'id_tipo_ejercicio', 'fecha_actividad',
            'hora_inicio', 'hora_fin', 'duracion_minutos', 'calorias_quemadas',
            'intensidad', 'comentario', 'completada'
        ]), ['puntos_obtenidos' => $nuevosPuntos]));

        // Actualizar puntos totales del usuario si cambiaron
        if ($nuevosPuntos != $puntosAnteriores) {
            $usuario = Usuario::find($registro->id_usuario);
            $usuario->increment('puntos_totales', $nuevosPuntos - $puntosAnteriores);
        }

        return response()->json([
            'success' => true,
            'message' => 'Registro de actividad actualizado exitosamente',
            'data' => $registro->load(['usuario', 'tipoEjercicio'])
        ]);
    }

    /**
     * Eliminar un registro de actividad física
     */
    public function destroy(int $id): JsonResponse
    {
        $registro = RegistroActividadFisica::find($id);

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'Registro de actividad no encontrado'
            ], 404);
        }

        // Restar puntos del usuario
        $usuario = Usuario::find($registro->id_usuario);
        $usuario->decrement('puntos_totales', $registro->puntos_obtenidos);

        $registro->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registro de actividad eliminado exitosamente'
        ]);
    }

    /**
     * Obtener estadísticas de actividad física de un usuario
     */
    public function estadisticas(int $idUsuario): JsonResponse
    {
        $usuario = Usuario::find($idUsuario);

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $registros = $usuario->registrosActividad();

        $estadisticas = [
            'total_actividades' => $registros->count(),
            'total_minutos' => $registros->sum('duracion_minutos'),
            'total_calorias' => $registros->sum('calorias_quemadas'),
            'total_puntos' => $registros->sum('puntos_obtenidos'),
            'actividades_completadas' => $registros->where('completada', true)->count(),
            'promedio_intensidad' => round($registros->avg('intensidad'), 2),
            'promedio_duracion' => round($registros->avg('duracion_minutos'), 2),
            'actividad_ultima_semana' => $registros->where('fecha_actividad', '>=', now()->subWeek())->count(),
            'actividad_ultimo_mes' => $registros->where('fecha_actividad', '>=', now()->subMonth())->count(),
            'tipos_ejercicio_mas_usados' => $registros->with('tipoEjercicio')
                ->selectRaw('id_tipo_ejercicio, COUNT(*) as total')
                ->groupBy('id_tipo_ejercicio')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }

    /**
     * Obtener actividades de un usuario por rango de fechas
     */
    public function porFecha(Request $request, int $idUsuario): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Fechas inválidas',
                'errors' => $validator->errors()
            ], 422);
        }

        $actividades = RegistroActividadFisica::with(['tipoEjercicio', 'rutina'])
            ->where('id_usuario', $idUsuario)
            ->whereBetween('fecha_actividad', [$request->fecha_inicio, $request->fecha_fin])
            ->orderBy('fecha_actividad', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $actividades
        ]);
    }

    /**
     * Marcar actividad como completada
     */
    public function marcarCompletada(int $id): JsonResponse
    {
        $registro = RegistroActividadFisica::find($id);

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'Registro de actividad no encontrado'
            ], 404);
        }

        if ($registro->completada) {
            return response()->json([
                'success' => false,
                'message' => 'La actividad ya está marcada como completada'
            ], 400);
        }

        $registro->update(['completada' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Actividad marcada como completada'
        ]);
    }

    /**
     * Calcular puntos basados en duración e intensidad
     */
    private function calcularPuntos(int $duracion, int $intensidad): int
    {
        // Fórmula: (duración en minutos / 10) * intensidad
        return (int) (($duracion / 10) * $intensidad);
    }
}
