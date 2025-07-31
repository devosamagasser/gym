<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_signup()
    {
        $response = $this->postJson('/api/signup', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['code', 'data' => ['token', 'user'], 'message']);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    public function test_login_requires_email_verification()
    {
        $this->postJson('/api/signup', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_verify_email_and_login()
    {
        $signup = $this->postJson('/api/signup', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->json('data');

        $user = User::first();

        DB::table('otps')->insert([
            'identifier' => $user->email,
            'token' => Hash::make('123456'),
            'validity' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $verify = $this->withHeader('Authorization', 'Bearer '.$signup['token'])
            ->postJson('/api/verify-email', ['otp' => '123456']);

        $verify->assertStatus(200);

        $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ])->assertStatus(200);
    }

    public function test_user_can_reset_password()
    {
        $user = User::factory()->create();

        $this->postJson('/api/password/forgot', ['email' => $user->email])
            ->assertStatus(200);

        DB::table('otps')->insert([
            'identifier' => $user->email,
            'token' => Hash::make('111111'),
            'validity' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $verification = $this->postJson('/api/password/verification', [
            'email' => $user->email,
            'otp' => '111111',
        ])->json('data');

        $token = $verification['token'];

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/password/reset', [
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])->assertStatus(200);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'new-password',
        ])->assertStatus(200);
    }
}
