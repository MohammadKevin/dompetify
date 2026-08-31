<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_login_and_register_pages(): void
    {
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200)
            ->assertSee('Selamat Datang Kembali')
            ->assertSee('90 hari');

        $registerResponse = $this->get('/register');
        $registerResponse->assertStatus(200)
            ->assertSee('Buat Akun Dompetify')
            ->assertSee('Starter Portfolio');
    }

    public function test_user_can_register_via_web_and_auto_provisions_starter_wallets(): void
    {
        $response = $this->post('/register', [
            'name' => 'Kevin Pratama',
            'email' => 'kevin@dompetify.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = User::where('email', 'kevin@dompetify.test')->first();
        $this->assertNotNull($user);

        // Verify 5 starter wallets were automatically provisioned
        $this->assertEquals(5, $user->wallets()->count());
        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            'name' => 'BCA',
        ]);
        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            'name' => 'BRImo',
        ]);
        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            'name' => 'GoPay',
        ]);
        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            'name' => 'DANA',
        ]);
        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            'name' => 'Dompet Tunai',
        ]);
    }

    public function test_user_can_login_and_logout_via_web(): void
    {
        $user = User::factory()->create([
            'email' => 'andi@dompetify.test',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'andi@dompetify.test',
            'password' => 'secret123',
            'remember' => '1',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        // Logout
        $logoutResponse = $this->post('/logout');
        $logoutResponse->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_user_can_register_via_api_and_receives_sanctum_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Budi API User',
            'email' => 'budi@dompetify.test',
            'password' => 'secretPass123',
            'password_confirmation' => 'secretPass123',
            'device_name' => 'Android Device SM-G998B',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'token',
                'token_type',
                'user' => ['id', 'name', 'email'],
            ]);

        $user = User::where('email', 'budi@dompetify.test')->first();
        $this->assertNotNull($user);
        $this->assertEquals(5, $user->wallets()->count());
    }

    public function test_user_can_login_via_api_and_receives_sanctum_token(): void
    {
        $user = User::factory()->create([
            'email' => 'mobile@dompetify.test',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'mobile@dompetify.test',
            'password' => 'password123',
            'device_name' => 'Flutter Mobile App',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['token', 'token_type', 'user']);
    }

    public function test_user_can_fetch_profile_and_stats_via_api_me(): void
    {
        $user = User::factory()->create();
        $user->provisionDefaultData();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/me');
        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.stats.active_wallets_count', 5);
    }

    public function test_user_can_logout_via_api(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');
        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
