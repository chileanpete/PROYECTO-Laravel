<?php

namespace Database\Seeders;

use App\Models\Desafio;
use Illuminate\Database\Seeder;

class DesafioSeeder extends Seeder
{
    public function run(): void
    {
        $desafios = [
            [
                'titulo' => '30 Días de Ejercicio',
                'descripcion' => 'Completa 30 días consecutivos de ejercicio',
                'tipo_desafio' => 'actividad_fisica',
                'categoria' => 'fitness',
                'objetivo_valor' => 30,
                'unidad_medida' => 'dias',
                'puntos_recompensa' => 500,
                'fecha_inicio' => now()->toDateString(),
                'fecha_fin' => now()->addDays(30)->toDateString(),
                'dificultad' => 3,
                'activo' => true,
                'icono' => 'fitness',
                'objetivos_relacionados' => ['Mejorar salud cardiovascular', 'Aumentar fuerza muscular']
            ],
            [
                'titulo' => 'Desafío de Nutrición',
                'descripcion' => 'Mantén una alimentación saludable por 21 días',
                'tipo_desafio' => 'nutricion',
                'categoria' => 'alimentacion',
                'objetivo_valor' => 21,
                'unidad_medida' => 'dias',
                'puntos_recompensa' => 300,
                'fecha_inicio' => now()->toDateString(),
                'fecha_fin' => now()->addDays(21)->toDateString(),
                'dificultad' => 2,
                'activo' => true,
                'icono' => 'nutrition',
                'objetivos_relacionados' => ['Perder peso', 'Mejorar salud cardiovascular']
            ],
            [
                'titulo' => 'Pérdida de Peso',
                'descripcion' => 'Logra perder 2 kg en 4 semanas',
                'tipo_desafio' => 'peso',
                'categoria' => 'peso',
                'objetivo_valor' => 2,
                'unidad_medida' => 'kg',
                'puntos_recompensa' => 400,
                'fecha_inicio' => now()->toDateString(),
                'fecha_fin' => now()->addDays(28)->toDateString(),
                'dificultad' => 4,
                'activo' => true,
                'icono' => 'weight',
                'objetivos_relacionados' => ['Perder peso']
            ],
            [
                'titulo' => 'Consistencia Diaria',
                'descripcion' => 'Registra tu actividad física por 14 días seguidos',
                'tipo_desafio' => 'consistencia',
                'categoria' => 'habitos',
                'objetivo_valor' => 14,
                'unidad_medida' => 'dias',
                'puntos_recompensa' => 200,
                'fecha_inicio' => now()->toDateString(),
                'fecha_fin' => now()->addDays(14)->toDateString(),
                'dificultad' => 1,
                'activo' => true,
                'icono' => 'consistency',
                'objetivos_relacionados' => ['Mejorar salud cardiovascular', 'Consistencia']
            ],
        ];

        foreach ($desafios as $desafio) {
            Desafio::create($desafio);
        }
    }
} 