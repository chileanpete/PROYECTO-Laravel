<?php

namespace Database\Seeders;

use App\Models\TipoEjercicio;
use Illuminate\Database\Seeder;

class TipoEjercicioSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'nombre' => 'Cardio',
                'categoria' => 'cardiovascular',
                'descripcion' => 'Ejercicios cardiovasculares que aumentan la frecuencia cardíaca',
                'calorias_por_minuto' => 8.5,
                'nivel_dificultad' => 2,
                'equipamiento_necesario' => 'Ropa deportiva, zapatillas',
                'icono' => 'heart-pulse',
                'instrucciones' => 'Mantén un ritmo constante y respira profundamente'
            ],
            [
                'nombre' => 'Fuerza',
                'categoria' => 'muscular',
                'descripcion' => 'Ejercicios de fuerza muscular y resistencia',
                'calorias_por_minuto' => 6.0,
                'nivel_dificultad' => 3,
                'equipamiento_necesario' => 'Pesas, bandas de resistencia',
                'icono' => 'dumbbell',
                'instrucciones' => 'Controla el movimiento y mantén la postura correcta'
            ],
            [
                'nombre' => 'Flexibilidad',
                'categoria' => 'estiramiento',
                'descripcion' => 'Ejercicios de estiramiento y flexibilidad',
                'calorias_por_minuto' => 2.5,
                'nivel_dificultad' => 1,
                'equipamiento_necesario' => 'Mat de yoga',
                'icono' => 'stretch',
                'instrucciones' => 'Respira profundamente y no fuerces los estiramientos'
            ],
            [
                'nombre' => 'Equilibrio',
                'categoria' => 'coordinacion',
                'descripcion' => 'Ejercicios para mejorar el equilibrio y coordinación',
                'calorias_por_minuto' => 3.0,
                'nivel_dificultad' => 2,
                'equipamiento_necesario' => 'Superficie estable',
                'icono' => 'balance',
                'instrucciones' => 'Mantén la concentración y respiración controlada'
            ],
            [
                'nombre' => 'Yoga',
                'categoria' => 'mind-body',
                'descripcion' => 'Ejercicios de yoga para mente y cuerpo',
                'calorias_por_minuto' => 4.0,
                'nivel_dificultad' => 2,
                'equipamiento_necesario' => 'Mat de yoga, ropa cómoda',
                'icono' => 'lotus',
                'instrucciones' => 'Conecta respiración con movimiento'
            ],
            [
                'nombre' => 'Pilates',
                'categoria' => 'core',
                'descripcion' => 'Ejercicios de pilates para fortalecer el core',
                'calorias_por_minuto' => 5.5,
                'nivel_dificultad' => 3,
                'equipamiento_necesario' => 'Mat de pilates',
                'icono' => 'core',
                'instrucciones' => 'Enfócate en el control y la precisión'
            ],
            [
                'nombre' => 'Baile',
                'categoria' => 'cardio',
                'descripcion' => 'Ejercicios de baile para cardio y diversión',
                'calorias_por_minuto' => 7.0,
                'nivel_dificultad' => 2,
                'equipamiento_necesario' => 'Ropa cómoda, música',
                'icono' => 'dance',
                'instrucciones' => 'Diviértete y mantén el ritmo'
            ],
            [
                'nombre' => 'Natación',
                'categoria' => 'cardio',
                'descripcion' => 'Ejercicios de natación de bajo impacto',
                'calorias_por_minuto' => 9.0,
                'nivel_dificultad' => 3,
                'equipamiento_necesario' => 'Traje de baño, gafas',
                'icono' => 'swimming',
                'instrucciones' => 'Mantén técnica correcta y respiración controlada'
            ],
            [
                'nombre' => 'Ciclismo',
                'categoria' => 'cardio',
                'descripcion' => 'Ejercicios de ciclismo indoor o outdoor',
                'calorias_por_minuto' => 8.0,
                'nivel_dificultad' => 2,
                'equipamiento_necesario' => 'Bicicleta, casco',
                'icono' => 'bicycle',
                'instrucciones' => 'Ajusta la resistencia según tu nivel'
            ],
            [
                'nombre' => 'Caminata',
                'categoria' => 'cardio',
                'descripcion' => 'Caminata rápida o senderismo',
                'calorias_por_minuto' => 5.5,
                'nivel_dificultad' => 1,
                'equipamiento_necesario' => 'Zapatillas cómodas',
                'icono' => 'walking',
                'instrucciones' => 'Mantén un paso constante y erguido'
            ],
        ];

        foreach ($tipos as $tipo) {
            TipoEjercicio::create($tipo);
        }
    }
} 