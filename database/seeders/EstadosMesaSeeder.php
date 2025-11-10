<?php

namespace Database\Seeders\Restaurante;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosMesaSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            ['nombre' => 'Disponible', 'descripcion' => 'Mesa disponible para asignar', 'color' => '#10b981'],
            ['nombre' => 'Ocupada', 'descripcion' => 'Mesa ocupada con clientes', 'color' => '#ef4444'],
            ['nombre' => 'Reservada', 'descripcion' => 'Mesa reservada para cliente', 'color' => '#f59e0b'],
            ['nombre' => 'En Limpieza', 'descripcion' => 'Mesa en proceso de limpieza', 'color' => '#3b82f6'],
            ['nombre' => 'Fuera de Servicio', 'descripcion' => 'Mesa temporalmente fuera de servicio', 'color' => '#6b7280'],
        ];
        
        foreach ($estados as $estado) {
            DB::table('estados_mesa')->insert($estado);
        }
    }
}
