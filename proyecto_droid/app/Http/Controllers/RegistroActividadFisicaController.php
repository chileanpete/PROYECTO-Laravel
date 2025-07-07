<?php

namespace App\Http\Controllers;

use App\Models\RegistroActividadFisica;
use App\Models\TipoEjercicio;
use App\Models\RutinaEjercicio;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegistroActividadFisicaController extends Controller
{
    /**
     * Obtener todos los registros de actividad física del usuario autenticado
     */
    public function index(): JsonResponse
    {
        $registros = RegistroActividadFisica::with(['tipoEjercicio', 'rutinaEjercicio'])
            ->where('id_usuario', Auth::id())
            ->orderBy('fecha_actividad', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $registros
        ]);
    }

    /**
     * Obtener un registro específico
     */
    public function show($id): JsonResponse
    {
        $registro = RegistroActividadFisica::with(['tipoEjercicio', 'rutinaEjercicio'])
            ->where('id_usuario', Auth::id())
            ->find($id);

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado'
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
        $validator = Validator::make($request->all(), [
            'id_tipo_ejercicio' => 'nullable|exists:tipos_ejercicio,id_tipo_ejercicio',
            'id_rutina' => 'nullable|exists:rutinas_ejercicio,id_rutina',
            'fecha_actividad' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'duracion_minutos' => 'required|integer|min:1|max:480',
            'calorias_quemadas' => 'required|integer|min:1',
            'intensidad' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:500',
            'completada' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $registro = RegistroActividadFisica::create([
            'id_usuario' => Auth::id(),
            'id_tipo_ejercicio' => $request->id_tipo_ejercicio,
            'id_rutina' => $request->id_rutina,
            'fecha_actividad' => $request->fecha_actividad,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'duracion_minutos' => $request->duracion_minutos,
            'calorias_quemadas' => $request->calorias_quemadas,
            'intensidad' => $request->intensidad,
            'comentario' => $request->comentario,
            'completada' => $request->completada ?? true,
            'puntos_obtenidos' => $request->calorias_quemadas / 10 // Puntos basados en calorías quemadas
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registro de actividad física creado exitosamente',
            'data' => $registro->load(['tipoEjercicio', 'rutinaEjercicio'])
        ], 201);
    }

    /**
     * Actualizar un registro de actividad física
     */
    public function update(Request $request, $id): JsonResponse
    {
        $registro = RegistroActividadFisica::where('id_usuario', Auth::id())->find($id);

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_tipo_ejercicio' => 'nullable|exists:tipos_ejercicio,id_tipo_ejercicio',
            'id_rutina' => 'nullable|exists:rutinas_ejercicio,id_rutina',
            'fecha_actividad' => 'sometimes|date',
            'hora_inicio' => 'sometimes|date_format:H:i',
            'hora_fin' => 'sometimes|date_format:H:i|after:hora_inicio',
            'duracion_minutos' => 'sometimes|integer|min:1|max:480',
            'calorias_quemadas' => 'sometimes|integer|min:1',
            'intensidad' => 'sometimes|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:500',
            'completada' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only([
            'id_tipo_ejercicio', 'id_rutina', 'fecha_actividad', 'hora_inicio', 
            'hora_fin', 'duracion_minutos', 'calorias_quemadas', 'intensidad', 
            'comentario', 'completada'
        ]);

        if ($request->has('calorias_quemadas')) {
            $data['puntos_obtenidos'] = $request->calorias_quemadas / 10;
        }

        $registro->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Registro de actividad física actualizado exitosamente',
            'data' => $registro->load(['tipoEjercicio', 'rutinaEjercicio'])
        ]);
    }

    /**
     * Eliminar un registro de actividad física
     */
    public function destroy($id): JsonResponse
    {
        $registro = RegistroActividadFisica::where('id_usuario', Auth::id())->find($id);

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado'
            ], 404);
        }

        $registro->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registro de actividad física eliminado exitosamente'
        ]);
    }

    /**
     * Obtener estadísticas de actividad física del usuario
     */
    public function estadisticas(): JsonResponse
    {
        $hoy = now()->toDateString();
        $semana = now()->subDays(7)->toDateString();
        $mes = now()->subDays(30)->toDateString();

        $estadisticas = [
            'hoy' => [
                'total_calorias_quemadas' => RegistroActividadFisica::where('id_usuario', Auth::id())
                    ->where('fecha_actividad', $hoy)
                    ->sum('calorias_quemadas'),
                'total_minutos' => RegistroActividadFisica::where('id_usuario', Auth::id())
                    ->where('fecha_actividad', $hoy)
                    ->sum('duracion_minutos'),
                'total_actividades' => RegistroActividadFisica::where('id_usuario', Auth::id())
                    ->where('fecha_actividad', $hoy)
                    ->count()
            ],
            'semana' => [
                'total_calorias_quemadas' => RegistroActividadFisica::where('id_usuario', Auth::id())
                    ->whereBetween('fecha_actividad', [$semana, $hoy])
                    ->sum('calorias_quemadas'),
                'total_minutos' => RegistroActividadFisica::where('id_usuario', Auth::id())
                    ->whereBetween('fecha_actividad', [$semana, $hoy])
                    ->sum('duracion_minutos'),
                'total_actividades' => RegistroActividadFisica::where('id_usuario', Auth::id())
                    ->whereBetween('fecha_actividad', [$semana, $hoy])
                    ->count()
            ],
            'mes' => [
                'total_calorias_quemadas' => RegistroActividadFisica::where('id_usuario', Auth::id())
                    ->whereBetween('fecha_actividad', [$mes, $hoy])
                    ->sum('calorias_quemadas'),
                'total_minutos' => RegistroActividadFisica::where('id_usuario', Auth::id())
                    ->whereBetween('fecha_actividad', [$mes, $hoy])
                    ->sum('duracion_minutos'),
                'total_actividades' => RegistroActividadFisica::where('id_usuario', Auth::id())
                    ->whereBetween('fecha_actividad', [$mes, $hoy])
                    ->count()
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }

    /**
     * Obtener tipos de ejercicio disponibles
     */
    public function tiposEjercicio(): JsonResponse
    {
        $tipos = TipoEjercicio::all();

        return response()->json([
            'success' => true,
            'data' => $tipos
        ]);
    }

    /**
     * Obtener rutinas de ejercicio disponibles
     */
    public function rutinasEjercicio(): JsonResponse
    {
        $rutinas = RutinaEjercicio::all();

        return response()->json([
            'success' => true,
            'data' => $rutinas
        ]);
    }
}
