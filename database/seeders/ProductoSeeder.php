<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'nombre'        => 'Desarrollo de software a medida',
                'descripcion'   => 'Servicio de desarrollo de aplicaciones web y móviles',
                'clave_sat'     => '81111501',
                'unidad_medida' => 'E48',
                'precio'        => 15000.00,
                'iva_aplicable' => true,
            ],
            [
                'nombre'        => 'Consultoría en sistemas',
                'descripcion'   => 'Asesoría técnica y análisis de requerimientos',
                'clave_sat'     => '81111507',
                'unidad_medida' => 'HUR',
                'precio'        => 1200.00,
                'iva_aplicable' => true,
            ],
            [
                'nombre'        => 'Licencia de software mensual',
                'descripcion'   => 'Acceso mensual a la plataforma SaaS',
                'clave_sat'     => '43232408',
                'unidad_medida' => 'MES',
                'precio'        => 2500.00,
                'iva_aplicable' => true,
            ],
            [
                'nombre'        => 'Capacitación técnica',
                'descripcion'   => 'Curso presencial o remoto de tecnología',
                'clave_sat'     => '86111604',
                'unidad_medida' => 'HUR',
                'precio'        => 800.00,
                'iva_aplicable' => false,
            ],
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
}
