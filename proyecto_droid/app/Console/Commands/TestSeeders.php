<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TestSeeders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:seeders {seeder? : Nombre del seeder específico a probar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar seeders individuales para detectar errores';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $seeder = $this->argument('seeder');

        if ($seeder) {
            $this->testSpecificSeeder($seeder);
        } else {
            $this->testAllSeeders();
        }
    }

    private function testAllSeeders()
    {
        $this->info('🧪 Probando todos los seeders...');

        $seeders = [
            'CategoriaComidaSeeder',
            'LugarComidaSeeder', 
            'TipoEjercicioSeeder',
            'UsuarioSeeder',
            'PlatoSeeder',
            'RutinaEjercicioSeeder',
            'DesafioSeeder',
            'EventoAcademicoSeeder',
            'TallerRecreativoSeeder',
            'DatosRelacionadosSeeder'
        ];

        foreach ($seeders as $seederName) {
            $this->testSpecificSeeder($seederName);
        }
    }

    private function testSpecificSeeder($seederName)
    {
        $this->info("🔍 Probando: {$seederName}");

        try {
            // Limpiar tabla relacionada si existe
            $this->cleanTableForSeeder($seederName);

            // Ejecutar seeder
            $this->call('db:seed', ['--class' => $seederName]);

            // Verificar datos creados
            $count = $this->getTableCountForSeeder($seederName);
            $this->info("✅ {$seederName} - {$count} registros creados");

        } catch (\Exception $e) {
            $this->error("❌ Error en {$seederName}:");
            $this->error($e->getMessage());
            $this->error('');
        }
    }

    private function cleanTableForSeeder($seederName)
    {
        $tableMap = [
            'CategoriaComidaSeeder' => 'categorias_comida',
            'LugarComidaSeeder' => 'lugares_comida',
            'TipoEjercicioSeeder' => 'tipos_ejercicio',
            'UsuarioSeeder' => 'usuarios',
            'PlatoSeeder' => 'platos',
            'RutinaEjercicioSeeder' => 'rutinas_ejercicio',
            'DesafioSeeder' => 'desafios',
            'EventoAcademicoSeeder' => 'evento_academicos',
            'TallerRecreativoSeeder' => 'taller_recreativos',
            'DatosRelacionadosSeeder' => 'favorito_platos'
        ];

        if (isset($tableMap[$seederName])) {
            $table = $tableMap[$seederName];
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->info("🧹 Tabla {$table} limpiada");
            }
        }
    }

    private function getTableCountForSeeder($seederName)
    {
        $tableMap = [
            'CategoriaComidaSeeder' => 'categorias_comida',
            'LugarComidaSeeder' => 'lugares_comida',
            'TipoEjercicioSeeder' => 'tipos_ejercicio',
            'UsuarioSeeder' => 'usuarios',
            'PlatoSeeder' => 'platos',
            'RutinaEjercicioSeeder' => 'rutinas_ejercicio',
            'DesafioSeeder' => 'desafios',
            'EventoAcademicoSeeder' => 'evento_academicos',
            'TallerRecreativoSeeder' => 'taller_recreativos',
            'DatosRelacionadosSeeder' => 'favorito_platos'
        ];

        if (isset($tableMap[$seederName])) {
            $table = $tableMap[$seederName];
            if (Schema::hasTable($table)) {
                return DB::table($table)->count();
            }
        }

        return 0;
    }
} 