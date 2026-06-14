<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacturaTest extends TestCase
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

    // Método auxiliar que crea un cliente y productos listos para facturar
    private function crearDatosBase(string $token): array
    {
        $cliente = $this->withToken($token)
                        ->postJson('/api/clientes', [
                            'nombre'         => 'Cliente Test SA',
                            'rfc'            => 'DCE200115GH4',
                            'email'          => 'test@cliente.mx',
                            'telefono'       => '5512345678',
                            'direccion'      => 'Av. Reforma 1, CDMX',
                            'regimen_fiscal' => '601',
                        ])->json('id');

        $producto = $this->withToken($token)
                         ->postJson('/api/productos', [
                             'nombre'         => 'Servicio de Desarrollo',
                             'clave_sat'      => '81111600',
                             'unidad_medida'  => 'E48',
                             'precio'         => 1000.00,
                             'iva_aplicable'  => true,
                         ])->json('id');

        return [
            'cliente_id' => $cliente,
            'producto_id' => $producto,
        ];
    }

    public function test_puede_listar_facturas(): void
    {
        $token = $this->getToken();

        $response = $this->withToken($token)
                         ->getJson('/api/facturas');

        $response->assertStatus(200);
    }

    public function test_puede_crear_factura_y_calcula_iva_correctamente(): void
    {
        $token = $this->getToken();
        $datos = $this->crearDatosBase($token);

        $response = $this->withToken($token)
                         ->postJson('/api/facturas', [
                             'cliente_id' => $datos['cliente_id'],
                             'conceptos'  => [
                                 [
                                     'producto_id' => $datos['producto_id'],
                                     'cantidad'    => 2,
                                 ]
                             ],
                         ]);

        $response->assertStatus(201);

        // Verificamos que el cálculo de IVA sea correcto
        // 2 unidades x $1,000 = $2,000 subtotal
        // IVA 16% = $320
        // Total = $2,320
        $response->assertJson([
            'subtotal' => '2000.00',
            'iva'      => '320.00',
            'total'    => '2320.00',
            'estado'   => 'emitida',
        ]);

        // Verificamos que se generó un folio UUID
        $this->assertNotNull($response->json('folio'));
    }

    public function test_factura_recien_creada_tiene_estado_emitida(): void
    {
        $token = $this->getToken();
        $datos = $this->crearDatosBase($token);

        $response = $this->withToken($token)
                         ->postJson('/api/facturas', [
                             'cliente_id' => $datos['cliente_id'],
                             'conceptos'  => [
                                 ['producto_id' => $datos['producto_id'], 'cantidad' => 1]
                             ],
                         ]);

        $response->assertJson(['estado' => 'emitida']);
    }

    public function test_puede_cancelar_factura_emitida(): void
    {
        $token = $this->getToken();
        $datos = $this->crearDatosBase($token);

        // Primero creamos la factura
        $facturaId = $this->withToken($token)
                          ->postJson('/api/facturas', [
                              'cliente_id' => $datos['cliente_id'],
                              'conceptos'  => [
                                  ['producto_id' => $datos['producto_id'], 'cantidad' => 1]
                              ],
                          ])->json('id');

        // Luego la cancelamos
        $response = $this->withToken($token)
                         ->postJson("/api/facturas/{$facturaId}/cancelar", [
                             'motivo' => 'Error en los conceptos facturados',
                         ]);

        $response->assertStatus(200)
                 ->assertJson(['factura' => ['estado' => 'cancelada']]);
    }

    public function test_no_puede_crear_factura_sin_conceptos(): void
    {
        $token = $this->getToken();
        $datos = $this->crearDatosBase($token);

        $response = $this->withToken($token)
                         ->postJson('/api/facturas', [
                             'cliente_id' => $datos['cliente_id'],
                             'conceptos'  => [],
                         ]);

        $response->assertStatus(422);
    }
}
