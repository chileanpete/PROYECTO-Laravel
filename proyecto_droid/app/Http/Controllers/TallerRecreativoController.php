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
            'instructor' => 'required|string|max:100',
            'categoria' => 'required|string|max:100',
            'duracion_minutos' => 'required|integer|min:1',
            'nivel_dificultad' => 'required|integer|min:1|max:5',
            'cupo_maximo' => 'required|integer|min:1',
            'costo' => 'required|numeric|min:0',
            'ubicacion' => 'required|string|max:200',
            'imagen_url' => 'nullable|url|max:500',
            'requisitos' => 'nullable|string|max:500'
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
            'instructor' => $request->instructor,
            'categoria' => $request->categoria,
            'duracion_minutos' => $request->duracion_minutos,
            'nivel_dificultad' => $request->nivel_dificultad,
            'cupo_maximo' => $request->cupo_maximo,
            'costo' => $request->costo,
            'ubicacion' => $request->ubicacion,
            'activo' => true,
            'imagen_url' => $request->imagen_url,
            'requisitos' => $request->requisitos
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
            'instructor' => 'string|max:100',
            'categoria' => 'string|max:100',
            'duracion_minutos' => 'integer|min:1',
            'nivel_dificultad' => 'integer|min:1|max:5',
            'cupo_maximo' => 'integer|min:1',
            'costo' => 'numeric|min:0',
            'ubicacion' => 'string|max:200',
            'activo' => 'boolean',
            'imagen_url' => 'nullable|url|max:500',
            'requisitos' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $taller->update($request->only([
            'nombre', 'descripcion', 'fecha_inicio', 'fecha_fin', 'instructor',
            'categoria', 'duracion_minutos', 'nivel_dificultad', 'cupo_maximo',
            'costo', 'ubicacion', 'activo', 'imagen_url', 'requisitos'
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
        try {
            // Usar la misma consulta que funciona en el debug
            $talleres = \DB::table('talleres_recreativos')
                ->where('activo', 1)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $talleres,
                'message' => 'Talleres activos obtenidos exitosamente',
                'status_code' => 200,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error interno del servidor: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'status_code' => 500,
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Obtener talleres activos (versión simple)
     */
    public function activosSimple(): JsonResponse
    {
        try {
            // Usar SQL directo para evitar problemas con Eloquent
            $talleres = \DB::table('talleres_recreativos')
                ->where('activo', 1)
                ->select('*')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $talleres,
                'message' => 'Talleres obtenidos exitosamente (SQL directo)',
                'count' => $talleres->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Obtener talleres por tipo
     */
    public function porTipo(string $tipo): JsonResponse
    {
        $talleres = TallerRecreativo::where('categoria', $tipo)
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
            ->where('costo', 0)
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
        try {
            \Log::info('TallerController: Iniciando obtención de talleres por usuario', ['user_id' => $idUsuario]);
            
            $talleres = TallerRecreativo::with(['inscripciones' => function($query) use ($idUsuario) {
                $query->where('id_usuario', $idUsuario);
            }])
            ->whereHas('inscripciones', function($query) use ($idUsuario) {
                $query->where('id_usuario', $idUsuario);
            })
            ->orderBy('fecha_inicio', 'desc')
            ->get();

            \Log::info('TallerController: Talleres por usuario obtenidos', ['count' => $talleres->count()]);

            return $this->successResponse($talleres, 'Talleres del usuario obtenidos exitosamente');
        } catch (\Exception $e) {
            \Log::error('TallerController: Error en porUsuario()', [
                'user_id' => $idUsuario,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse('Error interno del servidor: ' . $e->getMessage(), 500);
        }
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
            'total_capacidad' => $talleres->sum('cupo_maximo'),
            'por_categoria' => $talleres->selectRaw('categoria, COUNT(*) as total')
                ->groupBy('categoria')
                ->get(),
            'promedio_costo' => round($talleres->avg('costo'), 2)
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }

    /**
     * Endpoint de prueba para debugging
     */
    public function test(): JsonResponse
    {
        try {
            // Solo verificar tablas disponibles
            $tables = \DB::select('SHOW TABLES');
            
            $data = [
                'tablas_disponibles' => $tables,
                'conexion_db' => 'OK',
                'timestamp' => now()->toISOString(),
                'server_status' => 'OK'
            ];
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Test básico de base de datos'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en DB: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
