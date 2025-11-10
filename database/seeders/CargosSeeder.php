<?php

namespace Database\Seeders\Restaurante;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CargosSeeder extends Seeder
{
    public function run(): void
    {
        $cargos = [
            ['nombre' => 'Gerente General', 'descripcion' => 'Gerente general del restaurante', 'nivel_jerarquico' => 1, 'activo' => true],
            ['nombre' => 'Subgerente', 'descripcion' => 'Subgerente del restaurante', 'nivel_jerarquico' => 2, 'activo' => true],
            ['nombre' => 'Chef Ejecutivo', 'descripcion' => 'Chef principal de cocina', 'nivel_jerarquico' => 2, 'activo' => true],
            ['nombre' => 'Sous Chef', 'descripcion' => 'Segundo chef de cocina', 'nivel_jerarquico' => 3, 'activo' => true],
            ['nombre' => 'Jefe de Cocina', 'descripcion' => 'Jefe de área de cocina', 'nivel_jerarquico' => 3, 'activo' => true],
            ['nombre' => 'Cocinero', 'descripcion' => 'Cocinero del restaurante', 'nivel_jerarquico' => 4, 'activo' => true],
            ['nombre' => 'Ayudante de Cocina', 'descripcion' => 'Ayudante en cocina', 'nivel_jerarquico' => 5, 'activo' => true],
            ['nombre' => 'Jefe de Meseros', 'descripcion' => 'Supervisor de meseros', 'nivel_jerarquico' => 3, 'activo' => true],
            ['nombre' => 'Mesero', 'descripcion' => 'Mesero de servicio', 'nivel_jerarquico' => 4, 'activo' => true],
            ['nombre' => 'Bartender', 'descripcion' => 'Encargado de bar', 'nivel_jerarquico' => 4, 'activo' => true],
            ['nombre' => 'Sommelier', 'descripcion' => 'Experto en vinos', 'nivel_jerarquico' => 4, 'activo' => true],
            ['nombre' => 'Host/Hostess', 'descripcion' => 'Encargado de recepción', 'nivel_jerarquico' => 4, 'activo' => true],
            ['nombre' => 'Cajero', 'descripcion' => 'Encargado de caja', 'nivel_jerarquico' => 4, 'activo' => true],
            ['nombre' => 'Steward', 'descripcion' => 'Encargado de limpieza', 'nivel_jerarquico' => 5, 'activo' => true],
            ['nombre' => 'Auxiliar de Servicio', 'descripcion' => 'Auxiliar general', 'nivel_jerarquico' => 5, 'activo' => true],
        ];
        
        foreach ($cargos as $cargo) {
            DB::table('cargos')->insert($cargo);
        }
    }
}
