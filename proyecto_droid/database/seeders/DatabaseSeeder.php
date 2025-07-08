<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Datos básicos (sin dependencias)
            CategoriaComidaSeeder::class,
            LugarComidaSeeder::class,
            TipoEjercicioSeeder::class,
            
            // 2. Usuarios
            UsuarioSeeder::class,
            
            // 3. Datos que dependen de categorías y lugares
            PlatoSeeder::class,
            
            // 4. Datos que dependen de tipos de ejercicio
            RutinaEjercicioSeeder::class,
            
            // 5. Desafíos, eventos y talleres (sin dependencias)
            DesafioSeeder::class,
            EventoAcademicoSeeder::class,
            TallerRecreativoSeeder::class,
            
            // 6. Datos relacionados (dependen de usuarios, platos, etc.)
            DatosRelacionadosSeeder::class,
        ]);
    }
}
