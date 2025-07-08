<?php

namespace App\Http\Controllers;

use App\Models\RegistroConsumo;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class RegistroConsumoController extends Controller
{
    /**
     * Obtener todos los registros de consumo
     */
    public function index(Request $request): JsonResponse
    {
        $query = RegistroConsumo::with([
            'usuario', 'plato.lugar', 'plato.categoria'
        ]);

        // Filtrar por usuario
        if ($request->has('id_usuario')) {
            $query->where('id_usuario', $request->id_usuario);
        }

        // Filtrar por fecha
        if ($request->has('fecha_inicio')) {
            $query->where('fecha_consumo', '>=', $request->fecha_inicio);
        }

        if ($request->has('fecha_fin')) {
            $query->where('fecha_consumo', '<=', $request->fecha_fin);
        }

        // Filtrar por tipo de comida
        if ($request->has('tipo_comida')) {
            $query->where('tipo_comida', $request->tipo_comida);
        }

        $registros = $query->orderBy('fecha_consumo', 'desc')->paginate(20);

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
        $registro = RegistroConsumo::with([
            'usuario', 'plato.lugar', 'plato.categoria'
        ])->find($id);

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'Registro de consumo no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $registro
        ]);
    }

    /**
     * Crear un nuevo registro de consumo
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'id_plato' => 'required|exists:platos,id_plato',
            'fecha_consumo' => 'required|date',
            'hora_consumo' => 'required|date_format:H:i',
            'tipo_comida' => 'required|in:desayuno,almuerzo,cena,refrigerio',
            'porcion_consumida' => 'required|numeric|min:0.1|max:10',
            'calorias_consumidas' => 'required|integer|min:0',
            'comentario' => 'nullable|string',
            'satisfaccion' => 'integer|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $registro = RegistroConsumo::create([
            'id_usuario' => $request->id_usuario,
            'id_plato' => $request->id_plato,
            'fecha_consumo' => $request->fecha_consumo,
            'hora_consumo' => $request->hora_consumo,
            'tipo_comida' => $request->tipo_comida,
            'porcion_consumida' => $request->porcion_consumida,
            'calorias_consumidas' => $request->calorias_consumidas,
            'comentario' => $request->comentario,
            'satisfaccion' => $request->satisfaccion ?? 3
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registro de consumo creado exitosamente',
            'data' => $registro->load(['usuario', 'plato.lugar', 'plato.categoria'])
        ], 201);
    }

    /**
     * Actualizar un registro de consumo
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $registro = RegistroConsumo::find($id);

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'Registro de consumo no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_plato' => 'exists:platos,id_plato',
            'fecha_consumo' => 'date',
            'hora_consumo' => 'date_format:H:i',
            'tipo_comida' => 'in:desayuno,almuerzo,cena,refrigerio',
            'porcion_consumida' => 'numeric|min:0.1|max:10',
            'calorias_consumidas' => 'integer|min:0',
            'comentario' => 'nullable|string',
            'satisfaccion' => 'integer|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $registro->update($request->only([
            'id_plato', 'fecha_consumo', 'hora_consumo', 'tipo_comida',
            'porcion_consumida', 'calorias_consumidas', 'comentario', 'satisfaccion'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Registro de consumo actualizado exitosamente',
            'data' => $registro->load(['usuario', 'plato.lugar', 'plato.categoria'])
        ]);
    }

    /**
     * Eliminar un registro de consumo
     */
    public function destroy(int $id): JsonResponse
    {
        $registro = RegistroConsumo::find($id);

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'Registro de consumo no encontrado'
            ], 404);
        }

        $registro->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registro de consumo eliminado exitosamente'
        ]);
    }

    /**
     * Obtener estadísticas de consumo de un usuario
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

        $registros = $usuario->registrosConsumo();

        $estadisticas = [
            'total_consumos' => $registros->count(),
            'total_calorias' => $registros->sum('calorias_consumidas'),
            'promedio_satisfaccion' => round($registros->avg('satisfaccion'), 2),
            'consumos_ultima_semana' => $registros->where('fecha_consumo', '>=', now()->subWeek())->count(),
            'consumos_ultimo_mes' => $registros->where('fecha_consumo', '>=', now()->subMonth())->count(),
            'por_tipo_comida' => $registros->selectRaw('tipo_comida, COUNT(*) as total, SUM(calorias_consumidas) as calorias_totales')
                ->groupBy('tipo_comida')
                ->get(),
            'platos_mas_consumidos' => $registros->with('plato')
                ->selectRaw('id_plato, COUNT(*) as total')
                ->groupBy('id_plato')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get(),
            'promedio_calorias_diarias' => $registros->where('fecha_consumo', '>=', now()->subWeek())
                ->selectRaw('fecha_consumo, SUM(calorias_consumidas) as calorias_dia')
                ->groupBy('fecha_consumo')
                ->avg('calorias_dia')
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }

    /**
     * Obtener consumos de un usuario por rango de fechas
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

        $consumos = RegistroConsumo::with(['plato.lugar', 'plato.categoria'])
            ->where('id_usuario', $idUsuario)
            ->whereBetween('fecha_consumo', [$request->fecha_inicio, $request->fecha_fin])
            ->orderBy('fecha_consumo', 'desc')
            ->orderBy('hora_consumo', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $consumos
        ]);
    }

    /**
     * Obtener resumen nutricional diario
     */
    public function resumenNutricional(Request $request, int $idUsuario): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Fecha requerida',
                'errors' => $validator->errors()
            ], 422);
        }

        $consumos = RegistroConsumo::with('plato')
            ->where('id_usuario', $idUsuario)
            ->where('fecha_consumo', $request->fecha)
            ->get();

        $resumen = [
            'fecha' => $request->fecha,
            'total_calorias' => $consumos->sum('calorias_consumidas'),
            'total_proteinas' => $consumos->sum(function($consumo) {
                return $consumo->plato->proteinas_g * $consumo->porcion_consumida;
            }),
            'total_carbohidratos' => $consumos->sum(function($consumo) {
                return $consumo->plato->carbohidratos_g * $consumo->porcion_consumida;
            }),
            'total_grasas' => $consumos->sum(function($consumo) {
                return $consumo->plato->grasas_g * $consumo->porcion_consumida;
            }),
            'total_fibra' => $consumos->sum(function($consumo) {
                return $consumo->plato->fibra_g * $consumo->porcion_consumida;
            }),
            'por_tipo_comida' => $consumos->groupBy('tipo_comida')->map(function($grupo) {
                return [
                    'total_calorias' => $grupo->sum('calorias_consumidas'),
                    'cantidad_consumos' => $grupo->count()
                ];
            }),
            'consumos' => $consumos
        ];

        return response()->json([
            'success' => true,
            'data' => $resumen
        ]);
    }
}
