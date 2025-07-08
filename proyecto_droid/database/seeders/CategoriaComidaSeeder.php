<?php

namespace Database\Seeders;

use App\Models\CategoriaComida;
use Illuminate\Database\Seeder;

class CategoriaComidaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Desayunos', 'descripcion' => 'Platos típicos del desayuno'],
            ['nombre' => 'Almuerzos', 'descripcion' => 'Platos principales del almuerzo'],
            ['nombre' => 'Cenas', 'descripcion' => 'Platos para la cena'],
            ['nombre' => 'Refrigerios', 'descripcion' => 'Snacks y refrigerios saludables'],
            ['nombre' => 'Ensaladas', 'descripcion' => 'Ensaladas frescas y nutritivas'],
            ['nombre' => 'Sopas', 'descripcion' => 'Sopas y caldos'],
            ['nombre' => 'Postres', 'descripcion' => 'Postres saludables'],
            ['nombre' => 'Bebidas', 'descripcion' => 'Bebidas saludables'],
            ['nombre' => 'Vegetariano', 'descripcion' => 'Platos vegetarianos'],
            ['nombre' => 'Vegano', 'descripcion' => 'Platos veganos'],
        ];

        foreach ($categorias as $categoria) {
            CategoriaComida::create($categoria);
        }
    }
} 