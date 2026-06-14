<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;

    // Método auxiliar — crea un usuario y devuelve el token
    // Lo usamos en todos los tests para no repetir código
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

    public function test_puede_listar_clientes(): void
    {
        $token = $this->getToken();

        $response = $this->withToken($token)
                         ->getJson('/api/clientes');

        $response->assertStatus(200);
    }

    public function test_puede_crear_cliente_con_datos_validos(): void
    {
        $token = $this->getToken();

        $response = $this->withToken($token)
                         ->postJson('/api/clientes', [
                             'nombre'         => 'Distribuidora Central SA de CV',
                             'rfc'            => 'DCE200115GH4',
                             'email'          => 'ventas@distcentral.mx',
                             'telefono'       => '5598765432',
                             'direccion'      => 'Calle Morelos 321, CDMX',
                             'regimen_fiscal' => '601',
                         ]);

        $response->assertStatus(201);

        // Verificamos que realmente se guardó en la base de datos
        $this->assertDatabaseHas('clientes', [
            'rfc' => 'DCE200115GH4',
        ]);
    }

    public function test_no_puede_crear_cliente_con_rfc_invalido(): void
    {
        $token = $this->getToken();

        $response = $this->withToken($token)
                         ->postJson('/api/clientes', [
                             'nombre'         => 'Cliente Inválido',
                             'rfc'            => 'RFC_INVALIDO_123',
                             'email'          => 'test@test.mx',
                             'regimen_fiscal' => '601',
                         ]);

        // Debe rechazar el request con error de validación
        $response->assertStatus(422);
    }

    public function test_no_puede_acceder_sin_token(): void
    {
        $response = $this->getJson('/api/clientes');

        $response->assertStatus(401);
    }
}
