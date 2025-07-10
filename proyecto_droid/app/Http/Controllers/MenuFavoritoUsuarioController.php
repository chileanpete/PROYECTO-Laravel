<?php

namespace App\Http\Controllers;

use App\Models\MenuFavoritoUsuario;
use App\Models\MenuFavoritoPlato;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MenuFavoritoUsuarioController extends Controller
{
    /**
     * Obtener menús favoritos de un usuario
     */
    public function porUsuario(int $idUsuario): JsonResponse
    {
        try {
            $menusFavoritos = MenuFavoritoUsuario::with([
                'platos.plato:id_plato,nombre,precio,calorias_por_porcion,imagen_url,id_lugar',
                'platos.plato.lugar:id_lugar,nombre'
            ])
            ->where('id_usuario', $idUsuario)
            ->where('activo', true)
            ->orderBy('fecha_ultimo_uso', 'desc')
            ->orderBy('veces_usado', 'desc')
            ->limit(50)
            ->get();

            return response()->json([
                'success' => true,
                'data' => $menusFavoritos,
                'total' => $menusFavoritos->count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error en porUsuario MenuFavoritos: ' . $e->getMessage(), [
                'usuario' => $idUsuario,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => 'Error al cargar menús favoritos del usuario'
            ], 500);
        }
    }

    /**
     * Crear un nuevo menú favorito
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'nombre_menu' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'tipo_comida' => 'nullable|in:desayuno,almuerzo,cena,snack',
            'platos' => 'required|array|min:1',
            'platos.*.id_plato' => 'required|exists:platos,id_plato',
            'platos.*.cantidad' => 'nullable|numeric|min:0.1|max:10',
            'platos.*.unidad' => 'nullable|string|max:20',
            'platos.*.notas' => 'nullable|string|max:200'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Crear el menú favorito
            $menuFavorito = MenuFavoritoUsuario::create([
                'id_usuario' => $request->id_usuario,
                'nombre_menu' => $request->nombre_menu,
                'descripcion' => $request->descripcion,
                'tipo_comida' => $request->tipo_comida,
                'activo' => true
            ]);

            // Agregar los platos al menú
            foreach ($request->platos as $index => $platoData) {
                MenuFavoritoPlato::create([
                    'id_menu_favorito' => $menuFavorito->id_menu_favorito,
                    'id_plato' => $platoData['id_plato'],
                    'cantidad' => $platoData['cantidad'] ?? 1.0,
                    'unidad' => $platoData['unidad'] ?? 'porcion',
                    'notas' => $platoData['notas'] ?? null,
                    'orden' => $index + 1
                ]);
            }

            // Calcular calorías totales
            $menuFavorito->calcularCaloriasTotales();

            DB::commit();

            // Cargar el menú con relaciones para la respuesta
            $menuFavorito->load([
                'platos.plato:id_plato,nombre,precio,calorias_por_porcion,imagen_url',
                'platos.plato.lugar:id_lugar,nombre'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Menú favorito creado exitosamente',
                'data' => $menuFavorito
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creando menú favorito: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear menú favorito',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Agregar un plato a un menú favorito existente
     */
    public function agregarPlato(Request $request, int $idMenuFavorito): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_plato' => 'required|exists:platos,id_plato',
            'cantidad' => 'nullable|numeric|min:0.1|max:10',
            'unidad' => 'nullable|string|max:20',
            'notas' => 'nullable|string|max:200'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $menuFavorito = MenuFavoritoUsuario::findOrFail($idMenuFavorito);
            
            // Verificar si el plato ya existe en el menú
            $platoExistente = MenuFavoritoPlato::where('id_menu_favorito', $idMenuFavorito)
                ->where('id_plato', $request->id_plato)
                ->first();

            if ($platoExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'El plato ya está en este menú favorito'
                ], 400);
            }

            // Obtener el próximo orden
            $ultimoOrden = MenuFavoritoPlato::where('id_menu_favorito', $idMenuFavorito)
                ->max('orden') ?? 0;

            // Agregar el plato
            $menuPlato = MenuFavoritoPlato::create([
                'id_menu_favorito' => $idMenuFavorito,
                'id_plato' => $request->id_plato,
                'cantidad' => $request->cantidad ?? 1.0,
                'unidad' => $request->unidad ?? 'porcion',
                'notas' => $request->notas,
                'orden' => $ultimoOrden + 1
            ]);

            // Recalcular calorías totales del menú
            $menuFavorito->calcularCaloriasTotales();

            // Cargar el plato agregado con relaciones
            $menuPlato->load('plato:id_plato,nombre,precio,calorias_por_porcion,imagen_url');

            return response()->json([
                'success' => true,
                'message' => 'Plato agregado al menú favorito exitosamente',
                'data' => $menuPlato
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error agregando plato a menú favorito: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar plato al menú favorito',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un plato de un menú favorito
     */
    public function eliminarPlato(int $idMenuFavorito, int $idPlato): JsonResponse
    {
        try {
            $menuPlato = MenuFavoritoPlato::where('id_menu_favorito', $idMenuFavorito)
                ->where('id_plato', $idPlato)
                ->first();

            if (!$menuPlato) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plato no encontrado en el menú favorito'
                ], 404);
            }

            $menuPlato->delete();

            // Recalcular calorías totales del menú
            $menuFavorito = MenuFavoritoUsuario::find($idMenuFavorito);
            $menuFavorito->calcularCaloriasTotales();

            return response()->json([
                'success' => true,
                'message' => 'Plato eliminado del menú favorito exitosamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error eliminando plato de menú favorito: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar plato del menú favorito',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un menú favorito completo
     */
    public function destroy(int $idMenuFavorito): JsonResponse
    {
        try {
            $menuFavorito = MenuFavoritoUsuario::findOrFail($idMenuFavorito);
            $menuFavorito->delete(); // Cascade eliminará automáticamente los platos

            return response()->json([
                'success' => true,
                'message' => 'Menú favorito eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error eliminando menú favorito: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar menú favorito',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Usar un menú favorito (marcarlo como usado)
     */
    public function usar(int $idMenuFavorito): JsonResponse
    {
        try {
            $menuFavorito = MenuFavoritoUsuario::findOrFail($idMenuFavorito);
            $menuFavorito->marcarComoUsado();

            return response()->json([
                'success' => true,
                'message' => 'Menú favorito marcado como usado',
                'data' => $menuFavorito
            ]);

        } catch (\Exception $e) {
            \Log::error('Error marcando menú favorito como usado: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al usar menú favorito',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 