<?php

namespace App\Http\Controllers;

use App\Models\EventoAcademico;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class EventoAcademicoController extends Controller
{
    /**
     * Obtener todos los eventos
     */
    public function index(): JsonResponse
    {
        $eventos = EventoAcademico::where('activo', true)
            ->orderBy('fecha_inicio', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $eventos
        ]);
    }

    /**
     * Obtener un evento específico
     */
    public function show(int $id): JsonResponse
    {
        $evento = EventoAcademico::with('usuarioEventos.usuario')->find($id);

        if (!$evento) {
            return response()->json([
                'success' => false,
                'message' => 'Evento no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $evento
        ]);
    }

    /**
     * Crear un nuevo evento
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:100',
            'descripcion' => 'required|string|max:1000',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'lugar' => 'required|string|max:200',
            'capacidad_maxima' => 'required|integer|min:1',
            'tipo_evento' => 'required|in:conferencia,taller,seminario,charla,workshop',
            'ponente' => 'required|string|max:100',
            'imagen_url' => 'nullable|url|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $evento = EventoAcademico::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'lugar' => $request->lugar,
            'capacidad_maxima' => $request->capacidad_maxima,
            'capacidad_actual' => 0,
            'tipo_evento' => $request->tipo_evento,
            'ponente' => $request->ponente,
            'activo' => true,
            'imagen_url' => $request->imagen_url
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Evento creado exitosamente',
            'data' => $evento
        ], 201);
    }

    /**
     * Actualizar un evento
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $evento = EventoAcademico::find($id);

        if (!$evento) {
            return response()->json([
                'success' => false,
                'message' => 'Evento no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'titulo' => 'string|max:100',
            'descripcion' => 'string|max:1000',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date|after_or_equal:fecha_inicio',
            'hora_inicio' => 'date_format:H:i',
            'hora_fin' => 'date_format:H:i|after:hora_inicio',
            'lugar' => 'string|max:200',
            'capacidad_maxima' => 'integer|min:1',
            'tipo_evento' => 'in:conferencia,taller,seminario,charla,workshop',
            'ponente' => 'string|max:100',
            'activo' => 'boolean',
            'imagen_url' => 'nullable|url|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $evento->update($request->only([
            'titulo', 'descripcion', 'fecha_inicio', 'fecha_fin', 'hora_inicio',
            'hora_fin', 'lugar', 'capacidad_maxima', 'tipo_evento', 'ponente',
            'activo', 'imagen_url'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Evento actualizado exitosamente',
            'data' => $evento
        ]);
    }

    /**
     * Eliminar un evento (soft delete)
     */
    public function destroy(int $id): JsonResponse
    {
        $evento = EventoAcademico::find($id);

        if (!$evento) {
            return response()->json([
                'success' => false,
                'message' => 'Evento no encontrado'
            ], 404);
        }

        $evento->update(['activo' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Evento desactivado exitosamente'
        ]);
    }

    /**
     * Obtener eventos activos
     */
    public function activos(): JsonResponse
    {
        $eventos = EventoAcademico::where('activo', true)
            ->where('fecha_fin', '>=', now()->toDateString())
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $eventos
        ]);
    }

    /**
     * Obtener eventos por tipo
     */
    public function porTipo(string $tipo): JsonResponse
    {
        $eventos = EventoAcademico::where('tipo_evento', $tipo)
            ->where('activo', true)
            ->where('fecha_fin', '>=', now()->toDateString())
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $eventos
        ]);
    }

    /**
     * Obtener eventos por ponente
     */
    public function porPonente(string $ponente): JsonResponse
    {
        $eventos = EventoAcademico::where('ponente', 'like', "%{$ponente}%")
            ->where('activo', true)
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $eventos
        ]);
    }

    /**
     * Obtener eventos con cupos disponibles
     */
    public function conCupos(): JsonResponse
    {
        $eventos = EventoAcademico::where('activo', true)
            ->where('fecha_fin', '>=', now()->toDateString())
            ->whereRaw('capacidad_actual < capacidad_maxima')
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $eventos
        ]);
    }

    /**
     * Obtener eventos de un usuario
     */
    public function porUsuario(int $idUsuario): JsonResponse
    {
        $eventos = EventoAcademico::with(['usuarioEventos' => function($query) use ($idUsuario) {
            $query->where('id_usuario', $idUsuario);
        }])
        ->whereHas('usuarioEventos', function($query) use ($idUsuario) {
            $query->where('id_usuario', $idUsuario);
        })
        ->orderBy('fecha_inicio', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $eventos
        ]);
    }

    /**
     * Obtener estadísticas de eventos
     */
    public function estadisticas(): JsonResponse
    {
        $eventos = EventoAcademico::where('activo', true);

        $estadisticas = [
            'total_eventos' => $eventos->count(),
            'eventos_activos' => $eventos->where('fecha_fin', '>=', now()->toDateString())->count(),
            'eventos_vencidos' => $eventos->where('fecha_fin', '<', now()->toDateString())->count(),
            'total_capacidad' => $eventos->sum('capacidad_maxima'),
            'total_inscritos' => $eventos->sum('capacidad_actual'),
            'por_tipo' => $eventos->selectRaw('tipo_evento, COUNT(*) as total')
                ->groupBy('tipo_evento')
                ->get(),
            'eventos_llenos' => $eventos->whereRaw('capacidad_actual >= capacidad_maxima')->count(),
            'eventos_disponibles' => $eventos->whereRaw('capacidad_actual < capacidad_maxima')
                ->where('fecha_fin', '>=', now()->toDateString())->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }
}
