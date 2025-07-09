<?php

namespace App\Http\Controllers;

use App\Models\Plato;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PlatoController extends Controller
{
    /**
     * Obtener lista simplificada de platos (para evitar respuestas muy grandes)
     */
    public function simple(Request $request): JsonResponse
    {
        // Solo campos absolutamente esenciales para reducir tamaño de respuesta
        $platos = Plato::select([
            'id_plato',
            'nombre',
            'precio',
            'calorias_por_porcion'
        ])
        ->where('disponible', true)
        ->orderBy('nombre')
        ->limit(50) // Límite fijo para garantizar respuesta pequeña
        ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'data' => $platos,
                'total' => $platos->count()
            ]
        ]);
    }

    /**
     * Obtener todos los platos
     */
    public function index(Request $request): JsonResponse
    {
        // Cargar solo campos esenciales para evitar JSON muy grandes
        $query = Plato::select([
            'id_plato',
            'nombre', 
            'descripcion',
            'precio',
            'calorias_por_porcion',
            'proteinas_g',
            'carbohidratos_g', 
            'grasas_g',
            'es_vegetariano',
            'es_vegano',
            'sin_gluten',
            'imagen_url',
            'id_categoria',
            'id_lugar'
        ])
        ->where('disponible', true);

        // Filtros
        if ($request->has('categoria')) {
            $query->where('id_categoria', $request->categoria);
        }

        if ($request->has('lugar')) {
            $query->where('id_lugar', $request->lugar);
        }

        if ($request->has('vegetariano')) {
            $query->where('es_vegetariano', $request->vegetariano);
        }

        if ($request->has('vegano')) {
            $query->where('es_vegano', $request->vegano);
        }

        if ($request->has('sin_gluten')) {
            $query->where('sin_gluten', $request->sin_gluten);
        }

        if ($request->has('precio_min')) {
            $query->where('precio', '>=', $request->precio_min);
        }

        if ($request->has('precio_max')) {
            $query->where('precio', '<=', $request->precio_max);
        }

        if ($request->has('calorias_max')) {
            $query->where('calorias_por_porcion', '<=', $request->calorias_max);
        }

        // Búsqueda por nombre
        if ($request->has('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        // Reducir paginación para evitar respuestas muy grandes
        $platos = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $platos
        ]);
    }

    /**
     * Obtener un plato específico
     */
    public function show(int $id): JsonResponse
    {
        $plato = Plato::with(['lugar', 'categoria', 'favoritos.usuario'])
            ->find($id);

        if (!$plato) {
            return response()->json([
                'success' => false,
                'message' => 'Plato no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $plato
        ]);
    }

    /**
     * Crear un nuevo plato
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_lugar' => 'required|exists:lugares_comida,id_lugar',
            'id_categoria' => 'required|exists:categorias_comida,id_categoria',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'calorias_por_porcion' => 'required|integer|min:0',
            'proteinas_g' => 'nullable|numeric|min:0',
            'carbohidratos_g' => 'nullable|numeric|min:0',
            'grasas_g' => 'nullable|numeric|min:0',
            'fibra_g' => 'nullable|numeric|min:0',
            'azucares_g' => 'nullable|numeric|min:0',
            'sodio_mg' => 'nullable|numeric|min:0',
            'es_vegetariano' => 'boolean',
            'es_vegano' => 'boolean',
            'sin_gluten' => 'boolean',
            'imagen_url' => 'nullable|url|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $plato = Plato::create([
            'id_lugar' => $request->id_lugar,
            'id_categoria' => $request->id_categoria,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'calorias_por_porcion' => $request->calorias_por_porcion,
            'proteinas_g' => $request->proteinas_g ?? 0,
            'carbohidratos_g' => $request->carbohidratos_g ?? 0,
            'grasas_g' => $request->grasas_g ?? 0,
            'fibra_g' => $request->fibra_g ?? 0,
            'azucares_g' => $request->azucares_g ?? 0,
            'sodio_mg' => $request->sodio_mg ?? 0,
            'es_vegetariano' => $request->es_vegetariano ?? false,
            'es_vegano' => $request->es_vegano ?? false,
            'sin_gluten' => $request->sin_gluten ?? false,
            'imagen_url' => $request->imagen_url,
            'fecha_creacion' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Plato creado exitosamente',
            'data' => $plato->load(['lugar', 'categoria'])
        ], 201);
    }

    /**
     * Actualizar un plato
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $plato = Plato::find($id);

        if (!$plato) {
            return response()->json([
                'success' => false,
                'message' => 'Plato no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_lugar' => 'exists:lugares_comida,id_lugar',
            'id_categoria' => 'exists:categorias_comida,id_categoria',
            'nombre' => 'string|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'numeric|min:0',
            'calorias_por_porcion' => 'integer|min:0',
            'proteinas_g' => 'nullable|numeric|min:0',
            'carbohidratos_g' => 'nullable|numeric|min:0',
            'grasas_g' => 'nullable|numeric|min:0',
            'fibra_g' => 'nullable|numeric|min:0',
            'azucares_g' => 'nullable|numeric|min:0',
            'sodio_mg' => 'nullable|numeric|min:0',
            'disponible' => 'boolean',
            'es_vegetariano' => 'boolean',
            'es_vegano' => 'boolean',
            'sin_gluten' => 'boolean',
            'imagen_url' => 'nullable|url|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $plato->update($request->only([
            'id_lugar', 'id_categoria', 'nombre', 'descripcion', 'precio',
            'calorias_por_porcion', 'proteinas_g', 'carbohidratos_g', 'grasas_g',
            'fibra_g', 'azucares_g', 'sodio_mg', 'disponible', 'es_vegetariano',
            'es_vegano', 'sin_gluten', 'imagen_url'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Plato actualizado exitosamente',
            'data' => $plato->load(['lugar', 'categoria'])
        ]);
    }

    /**
     * Eliminar un plato (soft delete)
     */
    public function destroy(int $id): JsonResponse
    {
        $plato = Plato::find($id);

        if (!$plato) {
            return response()->json([
                'success' => false,
                'message' => 'Plato no encontrado'
            ], 404);
        }

        $plato->update(['disponible' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Plato desactivado exitosamente'
        ]);
    }

    /**
     * Obtener platos recomendados para un usuario
     */
    public function recomendados(int $idUsuario): JsonResponse
    {
        // Obtener preferencias del usuario
        $usuario = \App\Models\Usuario::find($idUsuario);
        
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $query = Plato::with(['lugar', 'categoria'])
            ->where('disponible', true);

        // Filtrar por alergias
        if ($usuario->alergias) {
            $alergias = explode(',', $usuario->alergias);
            foreach ($alergias as $alergia) {
                $query->where('nombre', 'not like', '%' . trim($alergia) . '%');
            }
        }

        // Filtrar por preferencias alimentarias
        if ($usuario->preferencias_alimentarias) {
            $preferencias = explode(',', $usuario->preferencias_alimentarias);
            foreach ($preferencias as $preferencia) {
                $query->where('nombre', 'like', '%' . trim($preferencia) . '%');
            }
        }

        // Filtrar por objetivo del usuario
        switch ($usuario->objetivo_principal) {
            case 'perder_peso':
                $query->where('calorias_por_porcion', '<=', 400);
                break;
            case 'ganar_musculo':
                $query->where('proteinas_g', '>=', 20);
                break;
            case 'mantener_peso':
                $query->whereBetween('calorias_por_porcion', [300, 600]);
                break;
        }

        $platosRecomendados = $query->inRandomOrder()->limit(10)->get();

        return response()->json([
            'success' => true,
            'data' => $platosRecomendados
        ]);
    }

    /**
     * Buscar platos por nombre
     */
    public function buscar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'termino' => 'required|string|min:2'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Término de búsqueda requerido',
                'errors' => $validator->errors()
            ], 422);
        }

        $platos = Plato::with(['lugar', 'categoria'])
            ->where('disponible', true)
            ->where(function($query) use ($request) {
                $query->where('nombre', 'like', '%' . $request->termino . '%')
                      ->orWhere('descripcion', 'like', '%' . $request->termino . '%');
            })
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $platos
        ]);
    }

    /**
     * Obtener platos por categoría
     */
    public function porCategoria(int $idCategoria): JsonResponse
    {
        $platos = Plato::with(['lugar', 'categoria'])
            ->where('id_categoria', $idCategoria)
            ->where('disponible', true)
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $platos
        ]);
    }

    /**
     * Obtener platos por lugar
     */
    public function porLugar(int $idLugar): JsonResponse
    {
        $platos = Plato::with(['lugar', 'categoria'])
            ->where('id_lugar', $idLugar)
            ->where('disponible', true)
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $platos
        ]);
    }
}
