<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class LoginControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function testSuccessfulLogin()
    {
        $userPassword = $this->faker->password(16);

        // Create a user with a hashed password
        $user = User::factory()->create([
            'name' => "Test",
            'email' => $this->faker->email,
            'password' => $userPassword
        ]);

        // Mock data for login request
        $data = [
            'email' => $user->email,
            'password' => $userPassword,
        ];

        // Mock the login request
        $response = $this->json('POST', '/api/login', $data);

        // Assert the response
        $response->assertStatus(200); // Adjust the expected status code
        $response->assertJsonStructure(['token']); // Adjust the expected JSON structure
    }

    public function testInvalidCredentials()
    {
        // Mock data for login request with invalid credentials
        $data = [
            'email' => $this->faker->email,
            'password' => $this->faker->password,
        ];

        // Mock the login request
        $response = $this->json('POST', '/api/login', $data);

        // Assert the response
        $response->assertStatus(401); // Invalid credentials should return 401 Unauthorized
        $response->assertJson(['message' => 'Invalid credentials']); // Adjust the expected error message
    }

    public function testMissingCredentials()
    {
        // Mock data for login request with missing credentials
        $data = [];

        // Mock the login request
        $response = $this->json('POST', '/api/login', $data);

        // Assert the response
        $response->assertStatus(422); // Missing credentials should return 422 Unprocessable Entity (validation error)
    }
}
