<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoTest extends TestCase
{
    use RefreshDatabase;

    private function getToken(): string
    {
        $user = User::factory()->create([
            'email'    => 'test@apicfdi.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'test@apicfdi.com',
            'password' => 'password123',
        ]);

        return $response->json('token');
    }

    public function test_puede_listar_productos(): void
    {
        $token = $this->getToken();

        $response = $this->withToken($token)
                         ->getJson('/api/productos');

        $response->assertStatus(200);
    }

    public function test_puede_crear_producto_con_datos_validos(): void
    {
        $token = $this->getToken();

        $response = $this->withToken($token)
                         ->postJson('/api/productos', [
                             'nombre'        => 'Servicio de Desarrollo',
                             'clave_sat'     => '81111600',
                             'unidad_medida' => 'E48',
                             'precio'        => 5000.00,
                             'iva_aplicable' => true,
                         ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('productos', [
            'clave_sat' => '81111600',
        ]);
    }

    public function test_no_puede_crear_producto_sin_precio(): void
    {
        $token = $this->getToken();

        $response = $this->withToken($token)
                         ->postJson('/api/productos', [
                             'nombre'        => 'Producto sin precio',
                             'clave_sat'     => '81111600',
                             'unidad_medida' => 'E48',
                         ]);

        $response->assertStatus(422);
    }
}
