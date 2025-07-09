<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class GenerateAuthToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:generate-token {--user-id=1 : ID del usuario para generar el token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera un token de autenticación específico para las APIs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        
        // Buscar o crear el usuario
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("Usuario con ID {$userId} no encontrado.");
            return 1;
        }

        // Token específico proporcionado por el usuario
        $tokenValue = '10|HLJnefF28ohiw89XqJf5W9SoNzbgBoUj0eRt58Xtcb1844f3';
        
        // Eliminar tokens existentes con el mismo nombre
        PersonalAccessToken::where('name', 'API Token')->where('tokenable_id', $user->id)->delete();
        
        // Crear el nuevo token
        $token = $user->tokens()->create([
            'name' => 'API Token',
            'token' => hash('sha256', $tokenValue),
            'abilities' => ['*'],
        ]);

        $this->info("Token generado exitosamente para el usuario: {$user->name}");
        $this->info("Token: {$tokenValue}");
        $this->info("Para usar en las APIs, incluye el header: Authorization: Bearer {$tokenValue}");
        
        return 0;
    }
} 