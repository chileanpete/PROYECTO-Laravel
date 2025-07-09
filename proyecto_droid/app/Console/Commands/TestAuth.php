<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestAuth extends Command
{
    protected $signature = 'test:auth';
    protected $description = 'Prueba la autenticación con Sanctum';

    public function handle()
    {
        $this->info('Probando autenticación con Sanctum...');

        // Buscar o crear un usuario de prueba
        $usuario = Usuario::firstOrCreate(
            ['email' => 'test@example.com'],
            [
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
            ]
        );

        // Generar token
        $token = $usuario->createToken('test-token')->plainTextToken;

        $this->info('Usuario de prueba:');
        $this->info('ID: ' . $usuario->id_usuario);
        $this->info('Email: ' . $usuario->email);
        $this->info('Nombre: ' . $usuario->nombre . ' ' . $usuario->apellidos);
        $this->info('');
        $this->info('Token generado:');
        $this->info($token);
        $this->info('');
        $this->info('Prueba con cURL:');
        $this->info('curl -H "Authorization: Bearer ' . $token . '" http://localhost:8000/api/usuarios/' . $usuario->id_usuario);
        $this->info('');
        $this->info('O desde Android usa:');
        $this->info('GET http://10.0.2.2:8000/api/usuarios/' . $usuario->id_usuario);
        $this->info('Header: Authorization: Bearer ' . $token);
    }
} 