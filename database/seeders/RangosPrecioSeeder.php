<?php

namespace Database\Seeders\Restaurante;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RangosPrecioSeeder extends Seeder
{
    public function run(): void
    {
        $rangos = [
            [
                'nombre' => 'Económico',
                'rango_inferior' => 0,
                'rango_superior' => 20000,
                'simbolo' => '$',
                'orden' => 1,
                'activo' => true
            ],
            [
                'nombre' => 'Moderado',
                'rango_inferior' => 20001,
                'rango_superior' => 50000,
                'simbolo' => '$$',
                'orden' => 2,
                'activo' => true
            ],
            [
                'nombre' => 'Costoso',
                'rango_inferior' => 50001,
                'rango_superior' => 100000,
                'simbolo' => '$$$',
                'orden' => 3,
                'activo' => true
            ],
            [
                'nombre' => 'Muy Costoso',
                'rango_inferior' => 100001,
                'rango_superior' => null,
                'simbolo' => '$$$$',
                'orden' => 4,
                'activo' => true
            ],
        ];
        
        foreach ($rangos as $rango) {
            DB::table('rangos_precio')->insert($rango);
        }
    }
}
