<?php

namespace App\Http\Controllers;

use App\Models\MenuDiario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MenuDiarioController extends Controller
{
    /**
     * Obtener menús de un usuario (optimizado)
     */
    public function porUsuario(int $idUsuario): JsonResponse
    {
        try {
            // Optimizar consulta limitando campos y paginando
            $menus = MenuDiario::select([
                'id_menu',
                'id_usuario', 
                'fecha_menu',
                'tipo_comida',
                'id_plato',
                'porcion_planificada',
                'calorias_planificadas',
                'completado',
                'notas'
            ])
            ->with([
                'plato:id_plato,nombre,precio,calorias_por_porcion,id_lugar,id_categoria',
                'plato.lugar:id_lugar,nombre',
                'plato.categoria:id_categoria,nombre'
            ])
            ->where('id_usuario', $idUsuario)
            ->orderBy('fecha_menu', 'desc')
            ->orderBy('tipo_comida')
            ->limit(100) // Límite para evitar respuestas muy grandes
            ->get();

            return response()->json([
                'success' => true,
                'data' => $menus,
                'total' => $menus->count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error en porUsuario: ' . $e->getMessage(), [
                'usuario' => $idUsuario,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => 'Error al cargar menús del usuario'
            ], 500);
        }
    }

    /**
     * Obtener un menú específico
     */
    public function show(int $id): JsonResponse
    {
        $menu = MenuDiario::with(['usuario', 'plato.lugar', 'plato.categoria'])
            ->find($id);

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menú no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $menu
        ]);
    }

    /**
     * Crear un nuevo menú
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'id_plato' => 'required|exists:platos,id_plato',
            'fecha_menu' => 'required|date',
            'tipo_comida' => 'required|in:desayuno,almuerzo,cena,refrigerio',
            'porcion_planificada' => 'required|numeric|min:0.1|max:10',
            'calorias_planificadas' => 'required|integer|min:0',
            'notas' => 'nullable|string',
            'completado' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $menu = MenuDiario::create([
            'id_usuario' => $request->id_usuario,
            'id_plato' => $request->id_plato,
            'fecha_menu' => $request->fecha_menu,
            'tipo_comida' => $request->tipo_comida,
            'porcion_planificada' => $request->porcion_planificada,
            'calorias_planificadas' => $request->calorias_planificadas,
            'notas' => $request->notas,
            'completado' => $request->completado ?? false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Menú creado exitosamente',
            'data' => $menu->load(['usuario', 'plato.lugar', 'plato.categoria'])
        ], 201);
    }

    /**
     * Actualizar un menú
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $menu = MenuDiario::find($id);

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menú no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_plato' => 'exists:platos,id_plato',
            'fecha_menu' => 'date',
            'tipo_comida' => 'in:desayuno,almuerzo,cena,refrigerio',
            'porcion_planificada' => 'numeric|min:0.1|max:10',
            'calorias_planificadas' => 'integer|min:0',
            'notas' => 'nullable|string',
            'completado' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $menu->update($request->only([
            'id_plato', 'fecha_menu', 'tipo_comida', 'porcion_planificada',
            'calorias_planificadas', 'notas', 'completado'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Menú actualizado exitosamente',
            'data' => $menu->load(['usuario', 'plato.lugar', 'plato.categoria'])
        ]);
    }

    /**
     * Eliminar un menú
     */
    public function destroy(int $id): JsonResponse
    {
        $menu = MenuDiario::find($id);

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menú no encontrado'
            ], 404);
        }

        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menú eliminado exitosamente'
        ]);
    }

    /**
     * Obtener menús por fecha (optimizado)
     */
    public function porFecha(Request $request, int $idUsuario): JsonResponse
    {
        try {
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

            // Optimizar consulta cargando solo campos esenciales
            $menus = MenuDiario::select([
                'id_menu',
                'id_usuario', 
                'fecha_menu',
                'tipo_comida',
                'id_plato',
                'porcion_planificada',
                'calorias_planificadas',
                'completado',
                'notas'
            ])
            ->with([
                'plato:id_plato,nombre,precio,calorias_por_porcion,id_lugar,id_categoria',
                'plato.lugar:id_lugar,nombre',
                'plato.categoria:id_categoria,nombre'
            ])
            ->where('id_usuario', $idUsuario)
            ->where('fecha_menu', $request->fecha)
            ->orderBy('tipo_comida')
            ->limit(50) // Límite de seguridad
            ->get();

            return response()->json([
                'success' => true,
                'data' => $menus,
                'total' => $menus->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en porFecha: ' . $e->getMessage(), [
                'usuario' => $idUsuario,
                'fecha' => $request->fecha ?? 'no_fecha',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => 'Error al cargar menús para la fecha especificada'
            ], 500);
        }
    }

    /**
     * Marcar menú como completado
     */
    public function marcarCompletado(int $id): JsonResponse
    {
        $menu = MenuDiario::find($id);

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menú no encontrado'
            ], 404);
        }

        if ($menu->completado) {
            return response()->json([
                'success' => false,
                'message' => 'El menú ya está marcado como completado'
            ], 400);
        }

        $menu->update(['completado' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Menú marcado como completado'
        ]);
    }

    /**
     * Obtener resumen de menús por semana
     */
    public function resumenSemanal(Request $request, int $idUsuario): JsonResponse
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

        $menus = MenuDiario::with(['plato'])
            ->where('id_usuario', $idUsuario)
            ->whereBetween('fecha_menu', [$request->fecha_inicio, $request->fecha_fin])
            ->get();

        $resumen = [
            'total_menus' => $menus->count(),
            'menus_completados' => $menus->where('completado', true)->count(),
            'total_calorias_planificadas' => $menus->sum('calorias_planificadas'),
            'por_tipo_comida' => $menus->groupBy('tipo_comida')->map(function($grupo) {
                return [
                    'cantidad' => $grupo->count(),
                    'calorias_totales' => $grupo->sum('calorias_planificadas'),
                    'completados' => $grupo->where('completado', true)->count()
                ];
            }),
            'por_fecha' => $menus->groupBy('fecha_menu')->map(function($grupo) {
                return [
                    'cantidad' => $grupo->count(),
                    'calorias_totales' => $grupo->sum('calorias_planificadas'),
                    'completados' => $grupo->where('completado', true)->count()
                ];
            })
        ];

        return response()->json([
            'success' => true,
            'data' => $resumen
        ]);
    }
}
