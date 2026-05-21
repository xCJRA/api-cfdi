<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'nombre'         => 'Empresa Tecnológica SA de CV',
                'rfc'            => 'ETE210401AB1',
                'email'          => 'contacto@empresa-tec.mx',
                'telefono'       => '5512345678',
                'direccion'      => 'Av. Insurgentes Sur 1234, CDMX',
                'regimen_fiscal' => '601',
            ],
            [
                'nombre'         => 'Comercializadora del Norte SA de CV',
                'rfc'            => 'CNO190801CD2',
                'email'          => 'admin@comnorte.mx',
                'telefono'       => '8112345678',
                'direccion'      => 'Av. Constitución 456, Monterrey NL',
                'regimen_fiscal' => '601',
            ],
            [
                'nombre'         => 'Juan Carlos Ramírez López',
                'rfc'            => 'RALJ850312EF3',
                'email'          => 'jc.ramirez@gmail.com',
                'telefono'       => '3312345678',
                'direccion'      => 'Calle Juárez 789, Guadalajara JAL',
                'regimen_fiscal' => '612',
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
