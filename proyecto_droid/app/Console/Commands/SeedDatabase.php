<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:seed-all {--fresh : Ejecutar migraciones frescas antes de los seeders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecutar todos los seeders en el orden correcto';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Iniciando proceso de seeding de la base de datos...');

        // Verificar si se debe ejecutar migraciones frescas
        if ($this->option('fresh')) {
            $this->info('🔄 Ejecutando migraciones frescas...');
            $this->call('migrate:fresh');
        }

        try {
            // 1. Datos básicos (sin dependencias)
            $this->info('📋 1. Creando categorías de comida...');
            $this->call('db:seed', ['--class' => 'CategoriaComidaSeeder']);

            $this->info('🏪 2. Creando lugares de comida...');
            $this->call('db:seed', ['--class' => 'LugarComidaSeeder']);

            $this->info('💪 3. Creando tipos de ejercicio...');
            $this->call('db:seed', ['--class' => 'TipoEjercicioSeeder']);

            // 2. Usuarios
            $this->info('👥 4. Creando usuarios...');
            $this->call('db:seed', ['--class' => 'UsuarioSeeder']);

            // 3. Datos que dependen de categorías y lugares
            $this->info('🍽️ 5. Creando platos...');
            $this->call('db:seed', ['--class' => 'PlatoSeeder']);

            // 4. Datos que dependen de tipos de ejercicio
            $this->info('🏃‍♂️ 6. Creando rutinas de ejercicio...');
            $this->call('db:seed', ['--class' => 'RutinaEjercicioSeeder']);

            // 5. Desafíos, eventos y talleres (sin dependencias)
            $this->info('🎯 7. Creando desafíos...');
            $this->call('db:seed', ['--class' => 'DesafioSeeder']);

            $this->info('📚 8. Creando eventos académicos...');
            $this->call('db:seed', ['--class' => 'EventoAcademicoSeeder']);

            $this->info('🎨 9. Creando talleres recreativos...');
            $this->call('db:seed', ['--class' => 'TallerRecreativoSeeder']);

            // 6. Datos relacionados (dependen de usuarios, platos, etc.)
            $this->info('🔗 10. Creando datos relacionados...');
            $this->call('db:seed', ['--class' => 'DatosRelacionadosSeeder']);

            $this->info('✅ ¡Proceso de seeding completado exitosamente!');
            $this->info('');
            $this->info('📊 Resumen de datos creados:');
            $this->info('   • Categorías de comida: ' . DB::table('categorias_comida')->count());
            $this->info('   • Lugares de comida: ' . DB::table('lugares_comida')->count());
            $this->info('   • Tipos de ejercicio: ' . DB::table('tipos_ejercicio')->count());
            $this->info('   • Usuarios: ' . DB::table('usuarios')->count());
            $this->info('   • Platos: ' . DB::table('platos')->count());
            $this->info('   • Rutinas de ejercicio: ' . DB::table('rutinas_ejercicio')->count());
            $this->info('   • Desafíos: ' . DB::table('desafios')->count());
            $this->info('   • Eventos académicos: ' . DB::table('eventos_academicos')->count());
            $this->info('   • Talleres recreativos: ' . DB::table('talleres_recreativos')->count());
            $this->info('   • Favoritos: ' . DB::table('favoritos_platos')->count());
            $this->info('   • Registros de consumo: ' . DB::table('registro_consumo')->count());
            $this->info('   • Registros de actividad: ' . DB::table('registro_actividad_fisica')->count());
            $this->info('');
            $this->info('🔑 Credenciales de prueba:');
            $this->info('   • juan@example.com / password123');
            $this->info('   • maria@example.com / password123');
            $this->info('   • carlos@example.com / password123');

        } catch (\Exception $e) {
            $this->error('❌ Error durante el proceso de seeding:');
            $this->error($e->getMessage());
            $this->error('');
            $this->error('💡 Sugerencias:');
            $this->error('   • Verifica que la base de datos esté configurada correctamente');
            $this->error('   • Asegúrate de que todas las migraciones se hayan ejecutado');
            $this->error('   • Intenta ejecutar: php artisan db:seed-all --fresh');
            
            return 1;
        }

        return 0;
    }
} 