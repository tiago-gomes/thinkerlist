<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Enums\ErrorCode;
use Faker\Factory as FakerFactory;
use Mockery;
use Laravel\Sanctum\Sanctum;
use \Illuminate\Database\QueryException;


class AuthControllerTest extends TestCase
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

        // Mock the register request
        $response = $this->json('POST', '/api/register', $userData);

        // execute required assertations
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

        // Mock the register request
        $response = $this->json('POST', '/api/register', $userData);

        // execute required assertations
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
        // Create a new user with factory pattern
        $existingUser = User::factory()->create();

        // Define an array to store the existing user data
        $existingUserData = [
        'name' => $this->faker->name,
        'email' => $existingUser->email,
        'password' => $this->faker->password,
        ];

        // Mock the register request
        $response = $this->json('POST', '/api/register', $existingUserData);

        // execute required assertations
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

        // Mock the register request
        $response = $this->json('POST', '/api/register', $invalidData);

        // execute required assertations
        $response->assertStatus(ErrorCode::UNPROCESSABLE_ENTITY->value)
            ->assertJson([
                'message' => 'The name field is required. (and 2 more errors)'
            ]);
    }

    public function testLogoutSuccessfully()
    {
        // Create a user and log them in using Sanctum
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Make a request to the logout endpoint
        $response = $this->json('POST', '/api/logout');

        // Assert the response
        $response->assertStatus(ErrorCode::OK->value)
            ->assertJson(['message' => 'Logout successful']);

        // Assert that the user's token has been revoked
        $this->assertCount(0, $user->tokens);
    }

    public function testLogoutWithoutAuthentication()
    {
        // Make a request to the logout endpoint without authentication
        $response = $this->json('POST', '/api/logout');

        // Assert the response
        $response->assertStatus(ErrorCode::UNAUTHORIZED->value)
            ->assertJson(['message' => 'Unauthenticated.']);
    }
}
