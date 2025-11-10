<?php

namespace Database\Seeders\Restaurante;

use Illuminate\Database\Seeder;

class CatalogosRestauranteSeeder extends Seeder
{
    /**
     * Seeder maestro para todos los catálogos del dominio Restaurante
     * 
     * Ejecutar con: php artisan db:seed --class=Database\\Seeders\\Restaurante\\CatalogosRestauranteSeeder
     */
    public function run(): void
    {
        $this->call([
            TiposCocinaSeeder::class,
            RangosPrecioSeeder::class,
            MetodosPagoSeeder::class,
            EstadosMesaSeeder::class,
            AlergenosSeeder::class,
            CargosSeeder::class,
            CaracteristicasRestauranteSeeder::class,
        ]);
        
        $this->command->info('✅ Catálogos del dominio Restaurante insertados correctamente');
    }
}
