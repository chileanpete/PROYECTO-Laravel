<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsuarioCompletoSeeder extends Seeder
{
    public function run()
    {
        // Crear usuario
        $id_usuario = DB::table('usuarios')->insertGetId([
            'email' => 'usuario@example.com',
            'password_hash' => bcrypt('password123'),
            'nombre' => 'Juan',
            'apellidos' => 'Pérez Gómez',
            'fecha_nacimiento' => '1990-05-10',
            'genero' => 'M',
            'altura_cm' => 175,
            'peso_kg' => 80.5,
            'nivel_actividad' => 'moderado',
            'objetivo_principal' => 'bajar peso',
            'fecha_registro' => Carbon::now(),
            'fecha_ultimo_acceso' => Carbon::now(),
            'activo' => true,
            'puntos_totales' => 0,
            'preferencias_alimentarias' => 'sin preferencia',
            'alergias' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Crear objetivo alimentación activo
        DB::table('objetivos_alimentacion')->insert([
            'id_usuario' => $id_usuario,
            'tipo_objetivo' => 'bajar peso',
            'valor_objetivo' => 75,
            'unidad' => 'kg',
            'fecha_inicio' => Carbon::now()->subDays(10),
            'fecha_fin' => Carbon::now()->addDays(20),
            'activo' => true,
            'progreso_actual' => 80,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Crear algunos desafíos activos
        DB::table('desafios')->insert([
            [
                'titulo' => 'Caminar 5.000 pasos diarios',
                'descripcion' => 'Incrementa tu actividad física diaria caminando al menos 5.000 pasos cada día.',
                'tipo_desafio' => 'diario',
                'categoria' => 'actividad',
                'objetivo_valor' => 5000,
                'unidad_medida' => 'pasos',
                'puntos_recompensa' => 10,
                'fecha_inicio' => Carbon::now()->subDays(5),
                'fecha_fin' => Carbon::now()->addDays(30),
                'activo' => true,
                'dificultad' => 2,
                'icono' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'titulo' => 'Consumir 5 porciones de verduras',
                'descripcion' => 'Aumenta tu ingesta diaria de verduras a 5 porciones para mejorar tu alimentación.',
                'tipo_desafio' => 'diario',
                'categoria' => 'alimentacion',
                'objetivo_valor' => 5,
                'unidad_medida' => 'porciones',
                'puntos_recompensa' => 15,
                'fecha_inicio' => Carbon::now()->subDays(3),
                'fecha_fin' => Carbon::now()->addDays(27),
                'activo' => true,
                'dificultad' => 2,
                'icono' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
