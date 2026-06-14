<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_puede_hacer_login_con_credenciales_correctas(): void
    {
        // 1. Creamos un usuario en la base de datos de testing
        $user = User::factory()->create([
            'email'    => 'test@apicfdi.com',
            'password' => bcrypt('password123'),
        ]);

        // 2. Hacemos la petición POST /api/login
        $response = $this->postJson('/api/login', [
            'email'    => 'test@apicfdi.com',
            'password' => 'password123',
        ]);

        // 3. Verificamos que la respuesta sea correcta
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'token'
                 ]);
    }

    public function test_login_falla_con_credenciales_incorrectas(): void
    {
        $user = User::factory()->create([
            'email'    => 'test@apicfdi.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'test@apicfdi.com',
            'password' => 'contraseña_incorrecta',
        ]);

        $response->assertStatus(401);
    }
}
