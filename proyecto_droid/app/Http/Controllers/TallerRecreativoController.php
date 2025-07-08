<?php

namespace App\Http\Controllers;

use App\Models\TallerRecreativo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class TallerRecreativoController extends Controller
{
    /**
     * Obtener todos los talleres
     */
    public function index(): JsonResponse
    {
        $talleres = TallerRecreativo::where('activo', true)
            ->orderBy('fecha_inicio', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $talleres
        ]);
    }

    /**
     * Obtener un taller específico
     */
    public function show(int $id): JsonResponse
    {
        $taller = TallerRecreativo::with('inscripciones.usuario')->find($id);

        if (!$taller) {
            return response()->json([
                'success' => false,
                'message' => 'Taller no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $taller
        ]);
    }

    /**
     * Crear un nuevo taller
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'descripcion' => 'required|string|max:1000',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'lugar' => 'required|string|max:200',
            'capacidad_maxima' => 'required|integer|min:1',
            'tipo_taller' => 'required|in:deportes,arte,musica,cocina,manualidades,tecnologia',
            'instructor' => 'required|string|max:100',
            'precio' => 'required|numeric|min:0',
            'imagen_url' => 'nullable|url|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $taller = TallerRecreativo::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'lugar' => $request->lugar,
            'capacidad_maxima' => $request->capacidad_maxima,
            'capacidad_actual' => 0,
            'tipo_taller' => $request->tipo_taller,
            'instructor' => $request->instructor,
            'precio' => $request->precio,
            'activo' => true,
            'imagen_url' => $request->imagen_url
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Taller creado exitosamente',
            'data' => $taller
        ], 201);
    }

    /**
     * Actualizar un taller
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $taller = TallerRecreativo::find($id);

        if (!$taller) {
            return response()->json([
                'success' => false,
                'message' => 'Taller no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:100',
            'descripcion' => 'string|max:1000',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date|after_or_equal:fecha_inicio',
            'hora_inicio' => 'date_format:H:i',
            'hora_fin' => 'date_format:H:i|after:hora_inicio',
            'lugar' => 'string|max:200',
            'capacidad_maxima' => 'integer|min:1',
            'tipo_taller' => 'in:deportes,arte,musica,cocina,manualidades,tecnologia',
            'instructor' => 'string|max:100',
            'precio' => 'numeric|min:0',
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

        $taller->update($request->only([
            'nombre', 'descripcion', 'fecha_inicio', 'fecha_fin', 'hora_inicio',
            'hora_fin', 'lugar', 'capacidad_maxima', 'tipo_taller', 'instructor',
            'precio', 'activo', 'imagen_url'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Taller actualizado exitosamente',
            'data' => $taller
        ]);
    }

    /**
     * Eliminar un taller (soft delete)
     */
    public function destroy(int $id): JsonResponse
    {
        $taller = TallerRecreativo::find($id);

        if (!$taller) {
            return response()->json([
                'success' => false,
                'message' => 'Taller no encontrado'
            ], 404);
        }

        $taller->update(['activo' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Taller desactivado exitosamente'
        ]);
    }

    /**
     * Obtener talleres activos
     */
    public function activos(): JsonResponse
    {
        $talleres = TallerRecreativo::where('activo', true)
            ->where('fecha_fin', '>=', now()->toDateString())
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $talleres
        ]);
    }

    /**
     * Obtener talleres por tipo
     */
    public function porTipo(string $tipo): JsonResponse
    {
        $talleres = TallerRecreativo::where('tipo_taller', $tipo)
            ->where('activo', true)
            ->where('fecha_fin', '>=', now()->toDateString())
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $talleres
        ]);
    }

    /**
     * Obtener talleres por instructor
     */
    public function porInstructor(string $instructor): JsonResponse
    {
        $talleres = TallerRecreativo::where('instructor', 'like', "%{$instructor}%")
            ->where('activo', true)
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $talleres
        ]);
    }

    /**
     * Obtener talleres con cupos disponibles
     */
    public function conCupos(): JsonResponse
    {
        $talleres = TallerRecreativo::where('activo', true)
            ->where('fecha_fin', '>=', now()->toDateString())
            ->whereRaw('capacidad_actual < capacidad_maxima')
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $talleres
        ]);
    }

    /**
     * Obtener talleres gratuitos
     */
    public function gratuitos(): JsonResponse
    {
        $talleres = TallerRecreativo::where('activo', true)
            ->where('fecha_fin', '>=', now()->toDateString())
            ->where('precio', 0)
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $talleres
        ]);
    }

    /**
     * Obtener talleres de un usuario
     */
    public function porUsuario(int $idUsuario): JsonResponse
    {
        $talleres = TallerRecreativo::with(['inscripciones' => function($query) use ($idUsuario) {
            $query->where('id_usuario', $idUsuario);
        }])
        ->whereHas('inscripciones', function($query) use ($idUsuario) {
            $query->where('id_usuario', $idUsuario);
        })
        ->orderBy('fecha_inicio', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $talleres
        ]);
    }

    /**
     * Obtener estadísticas de talleres
     */
    public function estadisticas(): JsonResponse
    {
        $talleres = TallerRecreativo::where('activo', true);

        $estadisticas = [
            'total_talleres' => $talleres->count(),
            'talleres_activos' => $talleres->where('fecha_fin', '>=', now()->toDateString())->count(),
            'talleres_vencidos' => $talleres->where('fecha_fin', '<', now()->toDateString())->count(),
            'total_capacidad' => $talleres->sum('capacidad_maxima'),
            'total_inscritos' => $talleres->sum('capacidad_actual'),
            'total_ingresos' => $talleres->sum(\DB::raw('capacidad_actual * precio')),
            'por_tipo' => $talleres->selectRaw('tipo_taller, COUNT(*) as total')
                ->groupBy('tipo_taller')
                ->get(),
            'talleres_llenos' => $talleres->whereRaw('capacidad_actual >= capacidad_maxima')->count(),
            'talleres_disponibles' => $talleres->whereRaw('capacidad_actual < capacidad_maxima')
                ->where('fecha_fin', '>=', now()->toDateString())->count(),
            'promedio_precio' => round($talleres->avg('precio'), 2)
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }
}
