<?php

namespace App\Http\Controllers;

use App\Models\FavoritoPlato;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class FavoritoPlatoController extends Controller
{
    /**
     * Obtener favoritos de un usuario
     */
    public function porUsuario(int $idUsuario): JsonResponse
    {
        $favoritos = FavoritoPlato::with(['usuario', 'plato.lugar', 'plato.categoria'])
            ->where('id_usuario', $idUsuario)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $favoritos
        ]);
    }

    /**
     * Agregar plato a favoritos
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'id_plato' => 'required|exists:platos,id_plato'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar si ya existe
        $existe = FavoritoPlato::where('id_usuario', $request->id_usuario)
            ->where('id_plato', $request->id_plato)
            ->exists();

        if ($existe) {
            return response()->json([
                'success' => false,
                'message' => 'El plato ya está en favoritos'
            ], 400);
        }

        $favorito = FavoritoPlato::create([
            'id_usuario' => $request->id_usuario,
            'id_plato' => $request->id_plato,
            'fecha_agregado' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Plato agregado a favoritos exitosamente',
            'data' => $favorito->load(['usuario', 'plato.lugar', 'plato.categoria'])
        ], 201);
    }

    /**
     * Eliminar de favoritos
     */
    public function destroy(int $id): JsonResponse
    {
        $favorito = FavoritoPlato::find($id);

        if (!$favorito) {
            return response()->json([
                'success' => false,
                'message' => 'Favorito no encontrado'
            ], 404);
        }

        $favorito->delete();

        return response()->json([
            'success' => true,
            'message' => 'Plato eliminado de favoritos exitosamente'
        ]);
    }

    /**
     * Verificar si un plato es favorito
     */
    public function verificarFavorito(int $idUsuario, int $idPlato): JsonResponse
    {
        $esFavorito = FavoritoPlato::where('id_usuario', $idUsuario)
            ->where('id_plato', $idPlato)
            ->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'es_favorito' => $esFavorito
            ]
        ]);
    }
}
