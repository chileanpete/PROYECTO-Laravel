<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\TalleresEventosProgramadosSeeder;

class SeedTalleresEventosProgramados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:talleres-eventos-programados 
                            {--fresh : Truncar tablas antes de sembrar}
                            {--only-talleres : Solo sembrar talleres recreativos}
                            {--only-eventos : Solo sembrar eventos académicos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta el seeder de talleres y eventos de salud física y alimentaria que simulan datos de API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌱 Iniciando seeder de talleres y eventos de salud...');
        
        if ($this->option('fresh')) {
            $this->warn('⚠️  Eliminando datos existentes...');
            
            if (!$this->option('only-eventos')) {
                \App\Models\TallerRecreativo::truncate();
                $this->info('✅ Tabla talleres_recreativos truncada');
            }
            
            if (!$this->option('only-talleres')) {
                \App\Models\EventoAcademico::truncate();
                $this->info('✅ Tabla eventos_academicos truncada');
            }
        }

        // Crear una instancia del seeder
        $seeder = new TalleresEventosProgramadosSeeder();
        
        // Ejecutar solo la parte específica si se especifica
        if ($this->option('only-talleres')) {
            $this->info('🎯 Sembrando solo talleres de salud física...');
            $seeder->seedTalleresProgramados();
        } elseif ($this->option('only-eventos')) {
            $this->info('🎯 Sembrando solo eventos de salud alimentaria...');
            $seeder->seedEventosProgramados();
        } else {
            $this->info('🎯 Sembrando talleres y eventos de salud...');
            $seeder->run();
        }

        $this->info('✅ Seeder completado exitosamente!');
        
        // Mostrar estadísticas
        $this->showStats();
    }

    /**
     * Mostrar estadísticas de los datos sembrados
     */
    private function showStats()
    {
        $this->info('📊 Estadísticas:');
        
        $talleres = \App\Models\TallerRecreativo::count();
        $eventos = \App\Models\EventoAcademico::count();
        
        $this->table(
            ['Tipo', 'Cantidad'],
            [
                ['Talleres de Salud Física', $talleres],
                ['Eventos de Salud Alimentaria', $eventos],
                ['Total', $talleres + $eventos]
            ]
        );
        
        $this->info('💡 Tip: Usa --fresh para limpiar datos existentes');
        $this->info('💡 Tip: Usa --only-talleres (salud física) o --only-eventos (salud alimentaria) para sembrar tipos específicos');
    }
} 