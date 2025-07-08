<?php

namespace App\Http\Controllers;

use App\Models\Desafio;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class DesafioController extends Controller
{
    /**
     * Obtener todos los desafíos
     */

    public function index()
    {
        return Desafio::where('activo', true)->get();
    }
    /**public function index(): JsonResponse
    {
        $desafios = Desafio::where('activo', true)
            ->orderBy('fecha_inicio', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $desafios
        ]);
    }

    /**
     * Obtener un desafío específico
     */
    public function show(int $id): JsonResponse
    {
        $desafio = Desafio::with('usuarioDesafios.usuario')->find($id);

        if (!$desafio) {
            return response()->json([
                'success' => false,
                'message' => 'Desafío no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $desafio
        ]);
    }

    /**
     * Crear un nuevo desafío
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:100',
            'descripcion' => 'required|string|max:1000',
            'tipo_desafio' => 'required|in:actividad_fisica,nutricion,peso,consistencia',
            'duracion_dias' => 'required|integer|min:1|max:365',
            'meta_objetivo' => 'required|integer|min:1',
            'puntos_recompensa' => 'required|integer|min:1',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'dificultad' => 'required|integer|min:1|max:5',
            'imagen_url' => 'nullable|url|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $desafio = Desafio::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'tipo_desafio' => $request->tipo_desafio,
            'duracion_dias' => $request->duracion_dias,
            'meta_objetivo' => $request->meta_objetivo,
            'puntos_recompensa' => $request->puntos_recompensa,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'dificultad' => $request->dificultad,
            'activo' => true,
            'imagen_url' => $request->imagen_url
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Desafío creado exitosamente',
            'data' => $desafio
        ], 201);
    }

    /**
     * Actualizar un desafío
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $desafio = Desafio::find($id);

        if (!$desafio) {
            return response()->json([
                'success' => false,
                'message' => 'Desafío no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'titulo' => 'string|max:100',
            'descripcion' => 'string|max:1000',
            'tipo_desafio' => 'in:actividad_fisica,nutricion,peso,consistencia',
            'duracion_dias' => 'integer|min:1|max:365',
            'meta_objetivo' => 'integer|min:1',
            'puntos_recompensa' => 'integer|min:1',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date|after:fecha_inicio',
            'dificultad' => 'integer|min:1|max:5',
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

        $desafio->update($request->only([
            'titulo',
            'descripcion',
            'tipo_desafio',
            'duracion_dias',
            'meta_objetivo',
            'puntos_recompensa',
            'fecha_inicio',
            'fecha_fin',
            'dificultad',
            'activo',
            'imagen_url'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Desafío actualizado exitosamente',
            'data' => $desafio
        ]);
    }

    /**
     * Eliminar un desafío (soft delete)
     */
    public function destroy(int $id): JsonResponse
    {
        $desafio = Desafio::find($id);

        if (!$desafio) {
            return response()->json([
                'success' => false,
                'message' => 'Desafío no encontrado'
            ], 404);
        }

        $desafio->update(['activo' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Desafío desactivado exitosamente'
        ]);
    }

    /**
     * Obtener desafíos activos
     */
    public function activos(): JsonResponse
    {
        $desafios = Desafio::where('activo', true)
            ->where('fecha_fin', '>=', now()->toDateString())
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $desafios
        ]);
    }

    /**
     * Obtener desafíos de un usuario
     */
    public function porUsuario(int $idUsuario): JsonResponse
    {
        $desafios = Desafio::with([
            'usuarioDesafios' => function ($query) use ($idUsuario) {
                $query->where('id_usuario', $idUsuario);
            }
        ])
            ->whereHas('usuarioDesafios', function ($query) use ($idUsuario) {
                $query->where('id_usuario', $idUsuario);
            })
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $desafios
        ]);
    }

    /**
     * Obtener desafíos por tipo
     */
    public function porTipo(string $tipo): JsonResponse
    {
        $desafios = Desafio::where('tipo_desafio', $tipo)
            ->where('activo', true)
            ->where('fecha_fin', '>=', now()->toDateString())
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $desafios
        ]);
    }

    /**
     * Obtener desafíos por dificultad
     */
    public function porDificultad(int $dificultad): JsonResponse
    {
        $desafios = Desafio::where('dificultad', $dificultad)
            ->where('activo', true)
            ->where('fecha_fin', '>=', now()->toDateString())
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $desafios
        ]);
    }

    /**
     * Obtener estadísticas de desafíos
     */
    public function estadisticas(): JsonResponse
    {
        $desafios = Desafio::where('activo', true);

        $estadisticas = [
            'total_desafios' => $desafios->count(),
            'desafios_activos' => $desafios->where('fecha_fin', '>=', now()->toDateString())->count(),
            'desafios_vencidos' => $desafios->where('fecha_fin', '<', now()->toDateString())->count(),
            'por_tipo' => $desafios->selectRaw('tipo_desafio, COUNT(*) as total')
                ->groupBy('tipo_desafio')
                ->get(),
            'por_dificultad' => $desafios->selectRaw('dificultad, COUNT(*) as total')
                ->groupBy('dificultad')
                ->orderBy('dificultad')
                ->get(),
            'promedio_duracion' => round($desafios->avg('duracion_dias'), 2),
            'promedio_puntos' => round($desafios->avg('puntos_recompensa'), 2)
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }
}
