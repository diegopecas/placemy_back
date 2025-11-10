<?php

namespace Database\Seeders\Restaurante;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposCocinaSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Italiana', 'descripcion' => 'Cocina tradicional italiana', 'activo' => true],
            ['nombre' => 'Mexicana', 'descripcion' => 'Cocina tradicional mexicana', 'activo' => true],
            ['nombre' => 'China', 'descripcion' => 'Cocina tradicional china', 'activo' => true],
            ['nombre' => 'Japonesa', 'descripcion' => 'Cocina tradicional japonesa', 'activo' => true],
            ['nombre' => 'Colombiana', 'descripcion' => 'Cocina tradicional colombiana', 'activo' => true],
            ['nombre' => 'Española', 'descripcion' => 'Cocina tradicional española', 'activo' => true],
            ['nombre' => 'Francesa', 'descripcion' => 'Cocina tradicional francesa', 'activo' => true],
            ['nombre' => 'Americana', 'descripcion' => 'Cocina estadounidense', 'activo' => true],
            ['nombre' => 'Peruana', 'descripcion' => 'Cocina tradicional peruana', 'activo' => true],
            ['nombre' => 'Argentina', 'descripcion' => 'Cocina tradicional argentina', 'activo' => true],
            ['nombre' => 'Tailandesa', 'descripcion' => 'Cocina tradicional tailandesa', 'activo' => true],
            ['nombre' => 'India', 'descripcion' => 'Cocina tradicional india', 'activo' => true],
            ['nombre' => 'Mediterránea', 'descripcion' => 'Cocina mediterránea', 'activo' => true],
            ['nombre' => 'Fusión', 'descripcion' => 'Fusión de varias cocinas', 'activo' => true],
            ['nombre' => 'Vegetariana', 'descripcion' => 'Cocina vegetariana', 'activo' => true],
            ['nombre' => 'Vegana', 'descripcion' => 'Cocina vegana', 'activo' => true],
            ['nombre' => 'Fast Food', 'descripcion' => 'Comida rápida', 'activo' => true],
            ['nombre' => 'Mariscos', 'descripcion' => 'Especializada en mariscos', 'activo' => true],
            ['nombre' => 'Parrilla', 'descripcion' => 'Especializada en carnes a la parrilla', 'activo' => true],
            ['nombre' => 'Internacional', 'descripcion' => 'Cocina internacional', 'activo' => true],
        ];
        
        foreach ($tipos as $tipo) {
            DB::table('tipos_cocina')->insert($tipo);
        }
    }
}
