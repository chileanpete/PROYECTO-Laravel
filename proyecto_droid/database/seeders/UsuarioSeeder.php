<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
                'nombre' => 'Juan',
                'apellidos' => 'Pérez',
                'email' => 'juan@example.com',
                'password_hash' => Hash::make('password123'),
                'fecha_nacimiento' => '1995-05-15',
                'genero' => 'M',
                'altura_cm' => 175,
                'peso_kg' => 70.5,
                'nivel_actividad' => 'moderado',
                'objetivo_principal' => 'mantener_peso',
                'fecha_registro' => now(),
                'puntos_totales' => 0,
                'activo' => true,
                'preferencias_alimentarias' => 'Vegetariano',
                'alergias' => 'Ninguna'
            ],
            [
                'nombre' => 'María',
                'apellidos' => 'García',
                'email' => 'maria@example.com',
                'password_hash' => Hash::make('password123'),
                'fecha_nacimiento' => '1992-08-22',
                'genero' => 'F',
                'altura_cm' => 165,
                'peso_kg' => 58.0,
                'nivel_actividad' => 'activo',
                'objetivo_principal' => 'perder_peso',
                'fecha_registro' => now(),
                'puntos_totales' => 150,
                'activo' => true,
                'preferencias_alimentarias' => 'Sin restricciones',
                'alergias' => 'Lactosa'
            ],
            [
                'nombre' => 'Carlos',
                'apellidos' => 'López',
                'email' => 'carlos@example.com',
                'password_hash' => Hash::make('password123'),
                'fecha_nacimiento' => '1988-12-10',
                'genero' => 'M',
                'altura_cm' => 180,
                'peso_kg' => 85.0,
                'nivel_actividad' => 'sedentario',
                'objetivo_principal' => 'ganar_musculo',
                'fecha_registro' => now(),
                'puntos_totales' => 75,
                'activo' => true,
                'preferencias_alimentarias' => 'Alto en proteínas',
                'alergias' => 'Ninguna'
            ],
        ];

        foreach ($usuarios as $usuario) {
            Usuario::create($usuario);
        }
    }
} 