<?php

namespace App\Http\Controllers;

use App\Models\RutinaEjercicio;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class RutinaEjercicioController extends Controller
{
    /**
     * Obtener todas las rutinas
     */
    public function index(): JsonResponse
    {
        $rutinas = RutinaEjercicio::with(['ejercicios.tipoEjercicio'])
            ->where('activo', true)
            ->orderBy('nombre')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $rutinas
        ]);
    }

    /**
     * Obtener una rutina específica
     */
    public function show(int $id): JsonResponse
    {
        $rutina = RutinaEjercicio::with(['ejercicios.tipoEjercicio', 'registrosActividad'])
            ->find($id);

        if (!$rutina) {
            return response()->json([
                'success' => false,
                'message' => 'Rutina no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $rutina
        ]);
    }

    /**
     * Crear una nueva rutina
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'descripcion' => 'required|string|max:1000',
            'tipo_rutina' => 'required|in:fuerza,cardio,flexibilidad,equilibrio,mixta',
            'nivel_dificultad' => 'required|integer|min:1|max:5',
            'duracion_estimada' => 'required|integer|min:5|max:300',
            'calorias_estimadas' => 'required|integer|min:0|max:2000',
            'dias_semana' => 'required|integer|min:1|max:7',
            'imagen_url' => 'nullable|url|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $rutina = RutinaEjercicio::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'tipo_rutina' => $request->tipo_rutina,
            'nivel_dificultad' => $request->nivel_dificultad,
            'duracion_estimada' => $request->duracion_estimada,
            'calorias_estimadas' => $request->calorias_estimadas,
            'dias_semana' => $request->dias_semana,
            'activo' => true,
            'imagen_url' => $request->imagen_url
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rutina creada exitosamente',
            'data' => $rutina
        ], 201);
    }

    /**
     * Actualizar una rutina
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $rutina = RutinaEjercicio::find($id);

        if (!$rutina) {
            return response()->json([
                'success' => false,
                'message' => 'Rutina no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:100',
            'descripcion' => 'string|max:1000',
            'tipo_rutina' => 'in:fuerza,cardio,flexibilidad,equilibrio,mixta',
            'nivel_dificultad' => 'integer|min:1|max:5',
            'duracion_estimada' => 'integer|min:5|max:300',
            'calorias_estimadas' => 'integer|min:0|max:2000',
            'dias_semana' => 'integer|min:1|max:7',
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

        $rutina->update($request->only([
            'nombre', 'descripcion', 'tipo_rutina', 'nivel_dificultad',
            'duracion_estimada', 'calorias_estimadas', 'dias_semana', 'activo', 'imagen_url'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Rutina actualizada exitosamente',
            'data' => $rutina
        ]);
    }

    /**
     * Eliminar una rutina (soft delete)
     */
    public function destroy(int $id): JsonResponse
    {
        $rutina = RutinaEjercicio::find($id);

        if (!$rutina) {
            return response()->json([
                'success' => false,
                'message' => 'Rutina no encontrada'
            ], 404);
        }

        $rutina->update(['activo' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Rutina desactivada exitosamente'
        ]);
    }

    /**
     * Obtener rutinas activas
     */
    public function activas(): JsonResponse
    {
        $rutinas = RutinaEjercicio::with(['ejercicios.tipoEjercicio'])
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $rutinas
        ]);
    }

    /**
     * Obtener rutinas por tipo
     */
    public function porTipo(string $tipo): JsonResponse
    {
        $rutinas = RutinaEjercicio::with(['ejercicios.tipoEjercicio'])
            ->where('tipo_rutina', $tipo)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $rutinas
        ]);
    }

    /**
     * Obtener rutinas por nivel de dificultad
     */
    public function porDificultad(int $nivel): JsonResponse
    {
        $rutinas = RutinaEjercicio::with(['ejercicios.tipoEjercicio'])
            ->where('nivel_dificultad', $nivel)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $rutinas
        ]);
    }

    /**
     * Obtener rutinas por duración
     */
    public function porDuracion(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'duracion_minima' => 'required|integer|min:5',
            'duracion_maxima' => 'required|integer|min:5|gte:duracion_minima'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Duración inválida',
                'errors' => $validator->errors()
            ], 422);
        }

        $rutinas = RutinaEjercicio::with(['ejercicios.tipoEjercicio'])
            ->where('activo', true)
            ->whereBetween('duracion_estimada', [$request->duracion_minima, $request->duracion_maxima])
            ->orderBy('duracion_estimada')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $rutinas
        ]);
    }

    /**
     * Obtener rutinas populares
     */
    public function populares(): JsonResponse
    {
        $rutinas = RutinaEjercicio::with(['ejercicios.tipoEjercicio'])
            ->withCount('registrosActividad')
            ->where('activo', true)
            ->orderBy('registros_actividad_count', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $rutinas
        ]);
    }

    /**
     * Obtener estadísticas de rutinas
     */
    public function estadisticas(): JsonResponse
    {
        $rutinas = RutinaEjercicio::where('activo', true);

        $estadisticas = [
            'total_rutinas' => $rutinas->count(),
            'por_tipo' => $rutinas->selectRaw('tipo_rutina, COUNT(*) as total')
                ->groupBy('tipo_rutina')
                ->get(),
            'por_dificultad' => $rutinas->selectRaw('nivel_dificultad, COUNT(*) as total')
                ->groupBy('nivel_dificultad')
                ->orderBy('nivel_dificultad')
                ->get(),
            'promedio_duracion' => round($rutinas->avg('duracion_estimada'), 2),
            'promedio_calorias' => round($rutinas->avg('calorias_estimadas'), 2),
            'promedio_dias_semana' => round($rutinas->avg('dias_semana'), 2),
            'rutina_mas_larga' => $rutinas->orderBy('duracion_estimada', 'desc')->first(),
            'rutina_mas_corta' => $rutinas->orderBy('duracion_estimada', 'asc')->first(),
            'rutina_mas_calorias' => $rutinas->orderBy('calorias_estimadas', 'desc')->first()
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }
}
