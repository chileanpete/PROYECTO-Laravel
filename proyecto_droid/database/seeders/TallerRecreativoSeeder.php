<?php

namespace Database\Seeders;

use App\Models\TallerRecreativo;
use Illuminate\Database\Seeder;

class TallerRecreativoSeeder extends Seeder
{
    public function run(): void
    {
        $talleres = [
            [
                'nombre' => 'Zumba Fitness',
                'descripcion' => 'Clase de zumba para quemar calorías bailando',
                'instructor' => 'Prof. Sandra López',
                'categoria' => 'deportes',
                'duracion_minutos' => 60,
                'nivel_dificultad' => 1,
                'cupo_maximo' => 25,
                'costo' => 15.00,
                'ubicacion' => 'Gimnasio Principal',
                'fecha_inicio' => now()->addDays(5)->setTime(18, 0, 0),
                'fecha_fin' => now()->addDays(5)->setTime(19, 0, 0),
                'activo' => true,
                'imagen_url' => 'https://example.com/zumba.jpg',
                'requisitos' => 'Ropa cómoda y zapatos deportivos'
            ],
            [
                'nombre' => 'Pintura al Óleo',
                'descripcion' => 'Aprende técnicas básicas de pintura al óleo',
                'instructor' => 'Artista Pedro Silva',
                'categoria' => 'arte',
                'duracion_minutos' => 120,
                'nivel_dificultad' => 2,
                'cupo_maximo' => 15,
                'costo' => 25.00,
                'ubicacion' => 'Sala de Arte',
                'fecha_inicio' => now()->addDays(10)->setTime(16, 0, 0),
                'fecha_fin' => now()->addDays(10)->setTime(18, 0, 0),
                'activo' => true,
                'imagen_url' => 'https://example.com/pintura.jpg',
                'requisitos' => 'Materiales incluidos'
            ],
            [
                'nombre' => 'Guitarra Acústica',
                'descripcion' => 'Taller básico de guitarra acústica',
                'instructor' => 'Músico Roberto Vega',
                'categoria' => 'musica',
                'duracion_minutos' => 120,
                'nivel_dificultad' => 1,
                'cupo_maximo' => 12,
                'costo' => 20.00,
                'ubicacion' => 'Sala de Música',
                'fecha_inicio' => now()->addDays(15)->setTime(17, 0, 0),
                'fecha_fin' => now()->addDays(15)->setTime(19, 0, 0),
                'activo' => true,
                'imagen_url' => 'https://example.com/guitarra.jpg',
                'requisitos' => 'Guitarra propia (opcional)'
            ],
            [
                'nombre' => 'Cocina Vegetariana',
                'descripcion' => 'Aprende a cocinar deliciosos platillos vegetarianos',
                'instructor' => 'Chef Vegetariana Ana Ruiz',
                'categoria' => 'cocina',
                'duracion_minutos' => 120,
                'nivel_dificultad' => 2,
                'cupo_maximo' => 18,
                'costo' => 30.00,
                'ubicacion' => 'Cocina Experimental',
                'fecha_inicio' => now()->addDays(20)->setTime(14, 0, 0),
                'fecha_fin' => now()->addDays(20)->setTime(16, 0, 0),
                'activo' => true,
                'imagen_url' => 'https://example.com/cocina-vegetariana.jpg',
                'requisitos' => 'Delantal y gorro incluidos'
            ],
            [
                'nombre' => 'Origami Básico',
                'descripcion' => 'Taller gratuito de origami para principiantes',
                'instructor' => 'Prof. Yuki Tanaka',
                'categoria' => 'manualidades',
                'duracion_minutos' => 90,
                'nivel_dificultad' => 1,
                'cupo_maximo' => 20,
                'costo' => 0.00,
                'ubicacion' => 'Biblioteca',
                'fecha_inicio' => now()->addDays(25)->setTime(15, 0, 0),
                'fecha_fin' => now()->addDays(25)->setTime(16, 30, 0),
                'activo' => true,
                'imagen_url' => 'https://example.com/origami.jpg',
                'requisitos' => 'Papel incluido'
            ],
        ];

        foreach ($talleres as $taller) {
            TallerRecreativo::create($taller);
        }
    }
} 