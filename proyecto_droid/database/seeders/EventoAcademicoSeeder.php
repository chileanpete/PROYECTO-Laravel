<?php

namespace Database\Seeders;

use App\Models\EventoAcademico;
use Illuminate\Database\Seeder;

class EventoAcademicoSeeder extends Seeder
{
    public function run(): void
    {
        $eventos = [
            [
                'titulo' => 'Nutrición y Bienestar',
                'descripcion' => 'Charla sobre nutrición balanceada y hábitos saludables',
                'tipo_evento' => 'charla',
                'fecha_inicio' => now()->addDays(7)->setTime(10, 0, 0),
                'fecha_fin' => now()->addDays(7)->setTime(12, 0, 0),
                'ubicacion' => 'Auditorio Principal',
                'organizador' => 'Dr. Ana Martínez',
                'cupos_disponibles' => 100,
                'inscripcion_requerida' => true,
                'activo' => true,
                'imagen_url' => 'https://example.com/nutricion-bienestar.jpg'
            ],
            [
                'titulo' => 'Taller de Cocina Saludable',
                'descripcion' => 'Aprende a preparar comidas nutritivas y deliciosas',
                'tipo_evento' => 'taller',
                'fecha_inicio' => now()->addDays(14)->setTime(15, 0, 0),
                'fecha_fin' => now()->addDays(14)->setTime(17, 0, 0),
                'ubicacion' => 'Cocina Experimental',
                'organizador' => 'Chef Carlos Rodríguez',
                'cupos_disponibles' => 20,
                'inscripcion_requerida' => true,
                'activo' => true,
                'imagen_url' => 'https://example.com/cocina-saludable.jpg'
            ],
            [
                'titulo' => 'Seminario de Actividad Física',
                'descripcion' => 'Seminario sobre la importancia del ejercicio regular',
                'tipo_evento' => 'seminario',
                'fecha_inicio' => now()->addDays(21)->setTime(14, 0, 0),
                'fecha_fin' => now()->addDays(21)->setTime(16, 0, 0),
                'ubicacion' => 'Sala de Conferencias',
                'organizador' => 'Lic. María González',
                'cupos_disponibles' => 50,
                'inscripcion_requerida' => true,
                'activo' => true,
                'imagen_url' => 'https://example.com/actividad-fisica.jpg'
            ],
            [
                'titulo' => 'Workshop de Mindfulness',
                'descripcion' => 'Taller práctico de mindfulness y meditación',
                'tipo_evento' => 'workshop',
                'fecha_inicio' => now()->addDays(28)->setTime(9, 0, 0),
                'fecha_fin' => now()->addDays(28)->setTime(11, 0, 0),
                'ubicacion' => 'Jardín Botánico',
                'organizador' => 'Psic. Laura Fernández',
                'cupos_disponibles' => 30,
                'inscripcion_requerida' => true,
                'activo' => true,
                'imagen_url' => 'https://example.com/mindfulness.jpg'
            ],
        ];

        foreach ($eventos as $evento) {
            EventoAcademico::create($evento);
        }
    }
} 