<?php

namespace Database\Seeders;

use App\Models\RutinaEjercicio;
use Illuminate\Database\Seeder;

class RutinaEjercicioSeeder extends Seeder
{
    public function run(): void
    {
        $rutinas = [
            [
                'nombre' => 'Rutina Cardio Básica',
                'descripcion' => 'Rutina de cardio para principiantes',
                'tipo_rutina' => 'cardio',
                'nivel_dificultad' => 1,
                'duracion_minutos' => 30,
                'calorias_estimadas' => 200,
                'activa' => true,
                'imagen_url' => 'https://example.com/cardio-basico.jpg'
            ],
            [
                'nombre' => 'Rutina de Fuerza',
                'descripcion' => 'Rutina de fuerza para tonificar músculos',
                'tipo_rutina' => 'fuerza',
                'nivel_dificultad' => 2,
                'duracion_minutos' => 45,
                'calorias_estimadas' => 300,
                'activa' => true,
                'imagen_url' => 'https://example.com/fuerza.jpg'
            ],
            [
                'nombre' => 'Yoga para Principiantes',
                'descripcion' => 'Sesión de yoga suave para principiantes',
                'tipo_rutina' => 'flexibilidad',
                'nivel_dificultad' => 1,
                'duracion_minutos' => 40,
                'calorias_estimadas' => 150,
                'activa' => true,
                'imagen_url' => 'https://example.com/yoga-principiantes.jpg'
            ],
            [
                'nombre' => 'Rutina Mixta Avanzada',
                'descripcion' => 'Rutina combinada de cardio y fuerza',
                'tipo_rutina' => 'mixta',
                'nivel_dificultad' => 4,
                'duracion_minutos' => 60,
                'calorias_estimadas' => 450,
                'activa' => true,
                'imagen_url' => 'https://example.com/mixta-avanzada.jpg'
            ],
            [
                'nombre' => 'Pilates Básico',
                'descripcion' => 'Sesión de pilates para fortalecer el core',
                'tipo_rutina' => 'flexibilidad',
                'nivel_dificultad' => 2,
                'duracion_minutos' => 35,
                'calorias_estimadas' => 180,
                'activa' => true,
                'imagen_url' => 'https://example.com/pilates-basico.jpg'
            ],
        ];

        foreach ($rutinas as $rutina) {
            $rutina['creado_por'] = 'sistema';
            RutinaEjercicio::create($rutina);
        }
    }
} 