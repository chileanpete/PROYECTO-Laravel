<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateTestUser extends Command
{
    protected $signature = 'test:create-user';
    protected $description = 'Crea un usuario de prueba para testing';

    public function handle()
    {
        $this->info('Creando usuario de prueba...');

        // Verificar si ya existe un usuario con ID 1
        $existingUser = Usuario::find(1);
        
        if ($existingUser) {
            $this->info('Usuario con ID 1 ya existe:');
            $this->info('Email: ' . $existingUser->email);
            $this->info('Nombre: ' . $existingUser->nombre . ' ' . $existingUser->apellidos);
            return;
        }

        // Crear usuario de prueba
        $usuario = Usuario::create([
            'email' => 'test@example.com',
            'password_hash' => Hash::make('password123'),
            'nombre' => 'Usuario',
            'apellidos' => 'De Prueba',
            'fecha_nacimiento' => '1990-01-01',
            'genero' => 'M',
            'altura_cm' => 175,
            'peso_kg' => 70.5,
            'nivel_actividad' => 'moderado',
            'objetivo_principal' => 'mantener_peso',
            'fecha_registro' => now(),
            'activo' => true,
            'puntos_totales' => 100,
            'preferencias_alimentarias' => 'Sin preferencias especiales',
            'alergias' => 'Ninguna'
        ]);

        $this->info('Usuario de prueba creado exitosamente:');
        $this->info('ID: ' . $usuario->id_usuario);
        $this->info('Email: ' . $usuario->email);
        $this->info('Nombre: ' . $usuario->nombre . ' ' . $usuario->apellidos);
        $this->info('Puedes probarlo en: GET /api/usuarios/' . $usuario->id_usuario . '/debug');
    }
} 