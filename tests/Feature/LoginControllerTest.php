<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Enums\ErrorCode;
use Faker\Factory as FakerFactory;
use Mockery;

class LoginControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    protected function tearDown(): void
    {
        parent::tearDown();

        // Clean up mocked objects
        Mockery::close();
    }

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

    public function testUserRegistration()
    {
        $faker = FakerFactory::create();

        $userData = [
            'name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            'password' => $faker->password,
        ];

        $response = $this->json('POST', '/api/register', $userData);
        $response->assertStatus(ErrorCode::CREATED->value)
            ->assertJsonStructure([
                'message',
                'token',
                'type',
                'expires_in',
            ]);

        // Check if the user is stored in the database
        $this->assertDatabaseHas('users', ['email' => $userData['email']]);
    }

     public function testSuccessfulUserRegistration()
    {
        $faker = FakerFactory::create();

        $userData = [
            'name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            'password' => $faker->password,
        ];

        $response = $this->json('POST', '/api/register', $userData);
        $response->assertStatus(ErrorCode::CREATED->value)
            ->assertJsonStructure([
                'message',
                'token',
                'type',
                'expires_in',
            ]);

        $this->assertDatabaseHas('users', ['email' => $userData['email']]);
    }

    public function testUserAlreadyExists()
    {
        $existingUser = User::factory()->create();
        $existingUserData = [
            'name' => $this->faker->name,
            'email' => $existingUser->email,
            'password' => $this->faker->password,
        ];

        $response = $this->json('POST', '/api/register', $existingUserData);
        $response->assertStatus(ErrorCode::BAD_REQUEST->value)
            ->assertJson([
                'message' => 'User already exists.',
            ]);
    }

    public function testValidationErrors()
    {
        $invalidData = [
            // Missing 'name', 'email', 'password', etc.
        ];

        $response = $this->json('POST', '/api/register', $invalidData);
        $response->assertStatus(ErrorCode::UNPROCESSABLE_ENTITY->value)
            ->assertJson([
                'message' => 'The name field is required. (and 2 more errors)'
            ]);
    }
}
