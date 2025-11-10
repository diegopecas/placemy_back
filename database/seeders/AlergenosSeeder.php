<?php

namespace Database\Seeders\Restaurante;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlergenosSeeder extends Seeder
{
    public function run(): void
    {
        $alergenos = [
            ['nombre' => 'Gluten', 'descripcion' => 'Contiene gluten (trigo, cebada, centeno)', 'icono' => 'bread-slice', 'activo' => true],
            ['nombre' => 'Lácteos', 'descripcion' => 'Contiene productos lácteos', 'icono' => 'cheese', 'activo' => true],
            ['nombre' => 'Huevos', 'descripcion' => 'Contiene huevos', 'icono' => 'egg', 'activo' => true],
            ['nombre' => 'Frutos Secos', 'descripcion' => 'Contiene frutos secos (nueces, almendras, etc)', 'icono' => 'seedling', 'activo' => true],
            ['nombre' => 'Maní', 'descripcion' => 'Contiene maní o cacahuates', 'icono' => 'peanut', 'activo' => true],
            ['nombre' => 'Soja', 'descripcion' => 'Contiene soja', 'icono' => 'leaf', 'activo' => true],
            ['nombre' => 'Pescado', 'descripcion' => 'Contiene pescado', 'icono' => 'fish', 'activo' => true],
            ['nombre' => 'Mariscos', 'descripcion' => 'Contiene mariscos', 'icono' => 'shrimp', 'activo' => true],
            ['nombre' => 'Apio', 'descripcion' => 'Contiene apio', 'icono' => 'carrot', 'activo' => true],
            ['nombre' => 'Mostaza', 'descripcion' => 'Contiene mostaza', 'icono' => 'pepper-hot', 'activo' => true],
            ['nombre' => 'Sésamo', 'descripcion' => 'Contiene sésamo', 'icono' => 'seedling', 'activo' => true],
            ['nombre' => 'Sulfitos', 'descripcion' => 'Contiene sulfitos', 'icono' => 'flask', 'activo' => true],
            ['nombre' => 'Altramuces', 'descripcion' => 'Contiene altramuces', 'icono' => 'leaf', 'activo' => true],
            ['nombre' => 'Moluscos', 'descripcion' => 'Contiene moluscos', 'icono' => 'fish', 'activo' => true],
        ];
        
        foreach ($alergenos as $alergeno) {
            DB::table('alergenos')->insert($alergeno);
        }
    }
}
