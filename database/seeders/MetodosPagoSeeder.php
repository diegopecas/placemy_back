<?php

namespace Database\Seeders\Restaurante;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodosPagoSeeder extends Seeder
{
    public function run(): void
    {
        $metodos = [
            ['nombre' => 'Efectivo', 'descripcion' => 'Pago en efectivo', 'icono' => 'money-bill', 'activo' => true],
            ['nombre' => 'Tarjeta de Crédito', 'descripcion' => 'Pago con tarjeta de crédito', 'icono' => 'credit-card', 'activo' => true],
            ['nombre' => 'Tarjeta de Débito', 'descripcion' => 'Pago con tarjeta de débito', 'icono' => 'credit-card', 'activo' => true],
            ['nombre' => 'Transferencia', 'descripcion' => 'Transferencia bancaria', 'icono' => 'exchange-alt', 'activo' => true],
            ['nombre' => 'PSE', 'descripcion' => 'Pago PSE', 'icono' => 'bank', 'activo' => true],
            ['nombre' => 'Nequi', 'descripcion' => 'Pago con Nequi', 'icono' => 'mobile-alt', 'activo' => true],
            ['nombre' => 'Daviplata', 'descripcion' => 'Pago con Daviplata', 'icono' => 'mobile-alt', 'activo' => true],
            ['nombre' => 'PayPal', 'descripcion' => 'Pago con PayPal', 'icono' => 'paypal', 'activo' => true],
            ['nombre' => 'Rappi Pay', 'descripcion' => 'Pago con Rappi Pay', 'icono' => 'wallet', 'activo' => true],
            ['nombre' => 'Vale de Alimentación', 'descripcion' => 'Vale de alimentación', 'icono' => 'ticket-alt', 'activo' => true],
        ];
        
        foreach ($metodos as $metodo) {
            DB::table('metodos_pago')->insert($metodo);
        }
    }
}
