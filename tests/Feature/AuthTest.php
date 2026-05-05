<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

// Pruebas unitarias de Heriberto (Autenticación y usuarios)
class AuthTest extends TestCase
{
    use RefreshDatabase;

    //Prueba para verificar que un usuario se pueda registrarse correctamente
    public function test_user_can_register_successfully()
    {
        $userData = [
            'name' => 'Heriberto ',
            'email' => 'heri@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect('/dashboard'); 
        $this->assertAuthenticated(); 
        
        // Verifica que el usuario se registró en la base de datos
        $this->assertDatabaseHas('users', [
            'name' => 'Heriberto ',
            'email' => 'heri@gmail.com',
        ]);
    }

    // Prueba para verificar que un usuario pueda iniciar sesión con datos válidos
    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::create([
            'name' => 'Heriberto ',
            'email' => 'heri@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'heri@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    
    // Prueba que verifica que un usuario NO pueda iniciar sesión con credenciales inválidas
    public function test_user_cannot_login_with_incorrect_credentials()
    {
        User::create([
            'name' => 'Heriberto ',
            'email' => 'heri@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'heri@gmail.com',
            'password' => 'contraseña_incorrecta',
        ]);

        $response->assertSessionHasErrors('email'); 
        $this->assertGuest(); 
    }

}