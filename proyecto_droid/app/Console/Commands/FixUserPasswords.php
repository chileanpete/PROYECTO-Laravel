<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class FixUserPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:fix-passwords {--user=*} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Arregla las contraseñas de usuarios que tienen doble hash debido al bug anterior';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Iniciando la corrección de contraseñas de usuarios...');

        if ($this->option('all')) {
            $this->fixAllUsers();
        } elseif ($emails = $this->option('user')) {
            $this->fixSpecificUsers($emails);
        } else {
            $this->error('Debes especificar --all para todos los usuarios o --user=email@example.com para usuarios específicos');
            return 1;
        }

        $this->info('✅ Proceso completado');
        return 0;
    }

    private function fixAllUsers()
    {
        $usuarios = Usuario::where('activo', true)->get();
        
        if ($usuarios->isEmpty()) {
            $this->warn('No se encontraron usuarios activos');
            return;
        }

        $this->info("Encontrados {$usuarios->count()} usuarios activos");

        foreach ($usuarios as $usuario) {
            $this->fixUserPassword($usuario);
        }
    }

    private function fixSpecificUsers(array $emails)
    {
        foreach ($emails as $email) {
            $usuario = Usuario::where('email', $email)->where('activo', true)->first();
            
            if (!$usuario) {
                $this->error("Usuario no encontrado: {$email}");
                continue;
            }

            $this->fixUserPassword($usuario);
        }
    }

    private function fixUserPassword(Usuario $usuario)
    {
        // Para usuarios existentes que se registraron con el bug, necesitamos resetear su contraseña
        // por seguridad, ya que no podemos "des-hashear" la contraseña
        
        $this->warn("⚠️  Usuario: {$usuario->email}");
        $this->warn("    Este usuario necesita restablecer su contraseña debido al bug de doble hash");
        
        // Opción 1: Generar una contraseña temporal
        if ($this->confirm("¿Generar contraseña temporal para {$usuario->email}?", true)) {
            $tempPassword = 'temp' . rand(1000, 9999);
            $usuario->update([
                'password_hash' => Hash::make($tempPassword)
            ]);
            
            $this->info("✅ Contraseña temporal generada para {$usuario->email}: {$tempPassword}");
            $this->warn("⚠️  El usuario debe cambiar esta contraseña temporal en su primer login");
        }
    }
} 