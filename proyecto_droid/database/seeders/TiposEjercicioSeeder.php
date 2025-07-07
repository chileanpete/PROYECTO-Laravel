<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposEjercicioSeeder extends Seeder
{
    public function run()
    {
        DB::table('tipos_ejercicio')->insert([
            [
                'nombre' => 'Cardio',
                'descripcion' => 'Ejercicios cardiovasculares',
                'categoria' => 'Aeróbico',
                'intensidad_recomendada' => 3
            ],
            [
                'nombre' => 'Pesas',
                'descripcion' => 'Entrenamiento de fuerza',
                'categoria' => 'Anaeróbico',
                'intensidad_recomendada' => 4
            ],
            [
                'nombre' => 'Yoga',
                'descripcion' => 'Flexibilidad y equilibrio',
                'categoria' => 'Flexibilidad',
                'intensidad_recomendada' => 2
            ]
        ]);
    }
} 