<?php

namespace Database\Seeders;

use App\Models\LugarComida;
use Illuminate\Database\Seeder;

class LugarComidaSeeder extends Seeder
{
    public function run(): void
    {
        $lugares = [
            [
                'nombre' => 'Comedor Universitario',
                'tipo' => 'comedor',
                'ubicacion' => 'Edificio Principal, Planta Baja',
                'coordenadas_lat' => 19.4326,
                'coordenadas_lng' => -99.1332,
                'horario_apertura' => '07:00:00',
                'horario_cierre' => '20:00:00',
                'telefono' => '555-0101',
                'calificacion_promedio' => 4.2,
                'precio_promedio' => 25.00,
                'activo' => true,
                'imagen_url' => 'https://example.com/comedor-universitario.jpg'
            ],
            [
                'nombre' => 'Cafetería Central',
                'tipo' => 'cafeteria',
                'ubicacion' => 'Edificio Central, Primer Piso',
                'coordenadas_lat' => 19.4327,
                'coordenadas_lng' => -99.1333,
                'horario_apertura' => '08:00:00',
                'horario_cierre' => '18:00:00',
                'telefono' => '555-0102',
                'calificacion_promedio' => 4.0,
                'precio_promedio' => 15.00,
                'activo' => true,
                'imagen_url' => 'https://example.com/cafeteria-central.jpg'
            ],
            [
                'nombre' => 'Restaurante Saludable',
                'tipo' => 'restaurante',
                'ubicacion' => 'Zona Deportiva, Edificio A',
                'coordenadas_lat' => 19.4328,
                'coordenadas_lng' => -99.1334,
                'horario_apertura' => '11:00:00',
                'horario_cierre' => '22:00:00',
                'telefono' => '555-0103',
                'calificacion_promedio' => 4.5,
                'precio_promedio' => 35.00,
                'activo' => true,
                'imagen_url' => 'https://example.com/restaurante-saludable.jpg'
            ],
            [
                'nombre' => 'Café Express',
                'tipo' => 'cafe',
                'ubicacion' => 'Biblioteca, Planta Baja',
                'coordenadas_lat' => 19.4329,
                'coordenadas_lng' => -99.1335,
                'horario_apertura' => '07:30:00',
                'horario_cierre' => '19:00:00',
                'telefono' => '555-0104',
                'calificacion_promedio' => 4.3,
                'precio_promedio' => 12.00,
                'activo' => true,
                'imagen_url' => 'https://example.com/cafe-express.jpg'
            ],
            [
                'nombre' => 'Bar de Ensaladas',
                'tipo' => 'restaurante',
                'ubicacion' => 'Jardín Botánico, Kiosko 1',
                'coordenadas_lat' => 19.4330,
                'coordenadas_lng' => -99.1336,
                'horario_apertura' => '10:00:00',
                'horario_cierre' => '17:00:00',
                'telefono' => '555-0105',
                'calificacion_promedio' => 4.4,
                'precio_promedio' => 20.00,
                'activo' => true,
                'imagen_url' => 'https://example.com/bar-ensaladas.jpg'
            ],
            [
                'nombre' => 'Pizzería Light',
                'tipo' => 'restaurante',
                'ubicacion' => 'Zona Comercial, Local 15',
                'coordenadas_lat' => 19.4331,
                'coordenadas_lng' => -99.1337,
                'horario_apertura' => '12:00:00',
                'horario_cierre' => '23:00:00',
                'telefono' => '555-0106',
                'calificacion_promedio' => 4.1,
                'precio_promedio' => 30.00,
                'activo' => true,
                'imagen_url' => 'https://example.com/pizzeria-light.jpg'
            ],
            [
                'nombre' => 'Sushi Bar',
                'tipo' => 'restaurante',
                'ubicacion' => 'Zona Comercial, Local 22',
                'coordenadas_lat' => 19.4332,
                'coordenadas_lng' => -99.1338,
                'horario_apertura' => '11:30:00',
                'horario_cierre' => '22:30:00',
                'telefono' => '555-0107',
                'calificacion_promedio' => 4.6,
                'precio_promedio' => 45.00,
                'activo' => true,
                'imagen_url' => 'https://example.com/sushi-bar.jpg'
            ],
            [
                'nombre' => 'Smoothie Bar',
                'tipo' => 'cafeteria',
                'ubicacion' => 'Gimnasio, Planta Baja',
                'coordenadas_lat' => 19.4333,
                'coordenadas_lng' => -99.1339,
                'horario_apertura' => '06:00:00',
                'horario_cierre' => '21:00:00',
                'telefono' => '555-0108',
                'calificacion_promedio' => 4.3,
                'precio_promedio' => 18.00,
                'activo' => true,
                'imagen_url' => 'https://example.com/smoothie-bar.jpg'
            ],
        ];

        foreach ($lugares as $lugar) {
            LugarComida::create($lugar);
        }
    }
} 