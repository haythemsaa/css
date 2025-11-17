<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_with_valid_data()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'user_type',
                        'loyalty_points',
                        'loyalty_level',
                    ],
                    'token',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => [
                        'name' => 'Test User',
                        'email' => 'test@example.com',
                        'user_type' => 'free',
                        'loyalty_points' => 0,
                        'loyalty_level' => 'bronze',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'user_type' => 'free',
        ]);
    }

    /** @test */
    public function registration_fails_with_duplicate_email()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function registration_fails_without_required_fields()
    {
        $response = $this->postJson('/api/v1/auth/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user',
                    'token',
                ],
            ])
            ->assertJson([
                'success' => true,
            ]);

        $this->assertNotNull($response->json('data.token'));
    }

    /** @test */
    public function login_fails_with_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('correctpassword'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function login_fails_for_inactive_user()
    {
        $user = User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => bcrypt('password123'),
            'is_active' => false,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'inactive@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);
    }

    /** @test */
    public function authenticated_user_can_get_profile()
    {
        $user = User::factory()->create([
            'user_type' => 'premium',
            'loyalty_points' => 1500,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/auth/profile');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'user_type' => 'premium',
                    'loyalty_points' => 1500,
                ],
            ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_get_profile()
    {
        $response = $this->getJson('/api/v1/auth/profile');

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_update_profile()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/v1/auth/profile', [
                'name' => 'Updated Name',
                'phone' => '+216 20 123 456',
                'city' => 'Tunis',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Updated Name',
                    'phone' => '+216 20 123 456',
                    'city' => 'Tunis',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'phone' => '+216 20 123 456',
        ]);
    }

    /** @test */
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // Verify token is deleted
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'test-token',
        ]);
    }

    /** @test */
    public function user_can_change_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword'),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/change-password', [
                'current_password' => 'oldpassword',
                'new_password' => 'newpassword123',
                'new_password_confirmation' => 'newpassword123',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // Verify can login with new password
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'newpassword123',
        ]);

        $loginResponse->assertStatus(200);
    }

    /** @test */
    public function change_password_fails_with_wrong_current_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('correctpassword'),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/change-password', [
                'current_password' => 'wrongpassword',
                'new_password' => 'newpassword123',
                'new_password_confirmation' => 'newpassword123',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }
}
