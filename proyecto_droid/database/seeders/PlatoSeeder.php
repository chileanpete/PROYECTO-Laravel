<?php

namespace Database\Seeders;

use App\Models\Plato;
use App\Models\CategoriaComida;
use App\Models\LugarComida;
use Illuminate\Database\Seeder;

class PlatoSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener categorías y lugares existentes
        $categorias = CategoriaComida::all();
        $lugares = LugarComida::all();

        // Verificar que existan categorías y lugares
        if ($categorias->isEmpty() || $lugares->isEmpty()) {
            throw new \Exception('Deben existir categorías y lugares antes de crear platos');
        }

        $platos = [
            [
                'nombre' => 'Ensalada César',
                'descripcion' => 'Ensalada fresca con lechuga, crutones y aderezo César',
                'categoria_nombre' => 'Ensaladas',
                'lugar_nombre' => 'Restaurante Saludable',
                'calorias_por_porcion' => 250,
                'proteinas_g' => 15.5,
                'carbohidratos_g' => 12.0,
                'grasas_g' => 18.0,
                'fibra_g' => 3.5,
                'azucares_g' => 2.0,
                'sodio_mg' => 450.0,
                'precio' => 8.50,
                'disponible' => true,
                'es_vegetariano' => false,
                'es_vegano' => false,
                'sin_gluten' => false,
                'imagen_url' => 'https://example.com/ensalada-cesar.jpg'
            ],
            [
                'nombre' => 'Avena con Frutas',
                'descripcion' => 'Avena cocida con frutas frescas y miel',
                'categoria_nombre' => 'Desayunos',
                'lugar_nombre' => 'Café Express',
                'calorias_por_porcion' => 320,
                'proteinas_g' => 12.0,
                'carbohidratos_g' => 55.0,
                'grasas_g' => 8.5,
                'fibra_g' => 8.0,
                'azucares_g' => 25.0,
                'sodio_mg' => 120.0,
                'precio' => 6.00,
                'disponible' => true,
                'es_vegetariano' => true,
                'es_vegano' => false,
                'sin_gluten' => true,
                'imagen_url' => 'https://example.com/avena-frutas.jpg'
            ],
            [
                'nombre' => 'Pollo a la Plancha',
                'descripcion' => 'Pechuga de pollo a la plancha con vegetales',
                'categoria_nombre' => 'Almuerzos',
                'lugar_nombre' => 'Comedor Universitario',
                'calorias_por_porcion' => 380,
                'proteinas_g' => 45.0,
                'carbohidratos_g' => 8.0,
                'grasas_g' => 12.0,
                'fibra_g' => 4.0,
                'azucares_g' => 3.0,
                'sodio_mg' => 380.0,
                'precio' => 12.50,
                'disponible' => true,
                'es_vegetariano' => false,
                'es_vegano' => false,
                'sin_gluten' => true,
                'imagen_url' => 'https://example.com/pollo-plancha.jpg'
            ],
            [
                'nombre' => 'Smoothie Verde',
                'descripcion' => 'Smoothie de espinaca, manzana y plátano',
                'categoria_nombre' => 'Bebidas',
                'lugar_nombre' => 'Smoothie Bar',
                'calorias_por_porcion' => 180,
                'proteinas_g' => 5.0,
                'carbohidratos_g' => 35.0,
                'grasas_g' => 2.0,
                'fibra_g' => 6.0,
                'azucares_g' => 28.0,
                'sodio_mg' => 45.0,
                'precio' => 7.00,
                'disponible' => true,
                'es_vegetariano' => true,
                'es_vegano' => true,
                'sin_gluten' => true,
                'imagen_url' => 'https://example.com/smoothie-verde.jpg'
            ],
            [
                'nombre' => 'Sopa de Verduras',
                'descripcion' => 'Sopa casera de verduras frescas',
                'categoria_nombre' => 'Sopas',
                'lugar_nombre' => 'Cafetería Central',
                'calorias_por_porcion' => 120,
                'proteinas_g' => 8.0,
                'carbohidratos_g' => 15.0,
                'grasas_g' => 4.0,
                'fibra_g' => 5.0,
                'azucares_g' => 8.0,
                'sodio_mg' => 280.0,
                'precio' => 5.50,
                'disponible' => true,
                'es_vegetariano' => true,
                'es_vegano' => true,
                'sin_gluten' => true,
                'imagen_url' => 'https://example.com/sopa-verduras.jpg'
            ],
            [
                'nombre' => 'Pizza Vegetariana',
                'descripcion' => 'Pizza con masa integral y vegetales frescos',
                'categoria_nombre' => 'Cenas',
                'lugar_nombre' => 'Pizzería Light',
                'calorias_por_porcion' => 420,
                'proteinas_g' => 18.0,
                'carbohidratos_g' => 65.0,
                'grasas_g' => 12.0,
                'fibra_g' => 8.0,
                'azucares_g' => 12.0,
                'sodio_mg' => 680.0,
                'precio' => 14.00,
                'disponible' => true,
                'es_vegetariano' => true,
                'es_vegano' => false,
                'sin_gluten' => false,
                'imagen_url' => 'https://example.com/pizza-vegetariana.jpg'
            ],
            [
                'nombre' => 'Sushi Roll California',
                'descripcion' => 'Roll de sushi con aguacate y surimi',
                'categoria_nombre' => 'Almuerzos',
                'lugar_nombre' => 'Sushi Bar',
                'calorias_por_porcion' => 280,
                'proteinas_g' => 12.0,
                'carbohidratos_g' => 45.0,
                'grasas_g' => 8.0,
                'fibra_g' => 2.0,
                'azucares_g' => 5.0,
                'sodio_mg' => 420.0,
                'precio' => 16.00,
                'disponible' => true,
                'es_vegetariano' => false,
                'es_vegano' => false,
                'sin_gluten' => false,
                'imagen_url' => 'https://example.com/sushi-california.jpg'
            ],
            [
                'nombre' => 'Yogur con Granola',
                'descripcion' => 'Yogur griego con granola casera y miel',
                'categoria_nombre' => 'Postres',
                'lugar_nombre' => 'Café Express',
                'calorias_por_porcion' => 220,
                'proteinas_g' => 15.0,
                'carbohidratos_g' => 25.0,
                'grasas_g' => 8.0,
                'fibra_g' => 3.0,
                'azucares_g' => 18.0,
                'sodio_mg' => 85.0,
                'precio' => 4.50,
                'disponible' => true,
                'es_vegetariano' => true,
                'es_vegano' => false,
                'sin_gluten' => false,
                'imagen_url' => 'https://example.com/yogur-granola.jpg'
            ],
        ];

        foreach ($platos as $plato) {
            // Buscar categoría y lugar por nombre
            $categoria = $categorias->where('nombre', $plato['categoria_nombre'])->first();
            $lugar = $lugares->where('nombre', $plato['lugar_nombre'])->first();

            // Si no existe la categoría o lugar, usar el primero disponible
            if (!$categoria) {
                $categoria = $categorias->first();
            }
            if (!$lugar) {
                $lugar = $lugares->first();
            }

            Plato::create([
                'nombre' => $plato['nombre'],
                'descripcion' => $plato['descripcion'],
                'id_categoria' => $categoria->id_categoria,
                'id_lugar' => $lugar->id_lugar,
                'calorias_por_porcion' => $plato['calorias_por_porcion'],
                'proteinas_g' => $plato['proteinas_g'],
                'carbohidratos_g' => $plato['carbohidratos_g'],
                'grasas_g' => $plato['grasas_g'],
                'fibra_g' => $plato['fibra_g'],
                'azucares_g' => $plato['azucares_g'],
                'sodio_mg' => $plato['sodio_mg'],
                'precio' => $plato['precio'],
                'disponible' => $plato['disponible'],
                'es_vegetariano' => $plato['es_vegetariano'],
                'es_vegano' => $plato['es_vegano'],
                'sin_gluten' => $plato['sin_gluten'],
                'imagen_url' => $plato['imagen_url'],
                'fecha_creacion' => now()
            ]);
        }
    }
} 