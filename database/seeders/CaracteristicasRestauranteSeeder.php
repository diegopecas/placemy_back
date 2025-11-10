<?php

namespace Database\Seeders\Restaurante;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CaracteristicasRestauranteSeeder extends Seeder
{
    public function run(): void
    {
        $caracteristicas = [
            ['nombre' => 'WiFi Gratis', 'descripcion' => 'Conexión WiFi disponible para clientes', 'icono' => 'wifi', 'activo' => true],
            ['nombre' => 'Estacionamiento', 'descripcion' => 'Estacionamiento disponible', 'icono' => 'parking', 'activo' => true],
            ['nombre' => 'Aire Acondicionado', 'descripcion' => 'Local climatizado', 'icono' => 'snowflake', 'activo' => true],
            ['nombre' => 'Terraza', 'descripcion' => 'Cuenta con terraza o área al aire libre', 'icono' => 'umbrella-beach', 'activo' => true],
            ['nombre' => 'Bar', 'descripcion' => 'Cuenta con servicio de bar', 'icono' => 'glass-martini-alt', 'activo' => true],
            ['nombre' => 'Música en Vivo', 'descripcion' => 'Ofrece música en vivo', 'icono' => 'music', 'activo' => true],
            ['nombre' => 'Accesible', 'descripcion' => 'Accesible para personas con discapacidad', 'icono' => 'wheelchair', 'activo' => true],
            ['nombre' => 'Pet Friendly', 'descripcion' => 'Acepta mascotas', 'icono' => 'paw', 'activo' => true],
            ['nombre' => 'Menú Infantil', 'descripcion' => 'Cuenta con menú para niños', 'icono' => 'child', 'activo' => true],
            ['nombre' => 'Delivery', 'descripcion' => 'Servicio de entrega a domicilio', 'icono' => 'motorcycle', 'activo' => true],
            ['nombre' => 'Reservas Online', 'descripcion' => 'Acepta reservas en línea', 'icono' => 'calendar-check', 'activo' => true],
            ['nombre' => 'Pagos con Tarjeta', 'descripcion' => 'Acepta pagos con tarjeta', 'icono' => 'credit-card', 'activo' => true],
            ['nombre' => 'Salón Privado', 'descripcion' => 'Cuenta con salones privados', 'icono' => 'door-closed', 'activo' => true],
            ['nombre' => 'Eventos', 'descripcion' => 'Organiza eventos especiales', 'icono' => 'birthday-cake', 'activo' => true],
            ['nombre' => 'Desayunos', 'descripcion' => 'Sirve desayunos', 'icono' => 'coffee', 'activo' => true],
            ['nombre' => 'Buffet', 'descripcion' => 'Ofrece servicio de buffet', 'icono' => 'utensils', 'activo' => true],
            ['nombre' => 'Vista Panorámica', 'descripcion' => 'Cuenta con vista panorámica', 'icono' => 'binoculars', 'activo' => true],
            ['nombre' => 'Opciones Veganas', 'descripcion' => 'Ofrece opciones veganas', 'icono' => 'leaf', 'activo' => true],
            ['nombre' => 'Opciones Sin Gluten', 'descripcion' => 'Ofrece opciones sin gluten', 'icono' => 'allergies', 'activo' => true],
            ['nombre' => 'TV/Pantallas', 'descripcion' => 'Cuenta con televisores o pantallas', 'icono' => 'tv', 'activo' => true],
        ];
        
        foreach ($caracteristicas as $caracteristica) {
            DB::table('caracteristicas_restaurante')->insert($caracteristica);
        }
    }
}
