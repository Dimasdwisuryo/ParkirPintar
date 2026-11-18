<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible_by_guest(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Masuk ke ParkirPintar');
        $response->assertSee('OPERASIONAL PARKIR TERPADU');
        $response->assertSee('Alokasi Slot Otomatis');
        $response->assertSee('Karcis Digital & Scan Cepat');
        $response->assertSee('Laporan Real-Time');
        $response->assertDontSee('Daftar akun');
    }

    public function test_unauthenticated_guest_is_redirected_to_login(): void
    {
        $response = $this->withHeader('X-Testing-Enforce-Guest', '1')->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_officer_windah_can_login_with_valid_credentials(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->post(route('login.post'), [
            'login'    => 'windah@parkirpintar.id',
            'password' => '12345678',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals('Windah Lamuri', auth()->user()->name);
    }

    public function test_officer_samuel_can_login_with_valid_credentials(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->post(route('login.post'), [
            'login'    => 'samuel@parkirpintar.id',
            'password' => '12345678',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals('Samuel Shiva', auth()->user()->name);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->post(route('login.post'), [
            'login'    => 'windah@parkirpintar.id',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_authenticated_user_is_redirected_away_from_login_page(): void
    {
        $user = User::factory()->create([
            'name'  => 'Windah Lamuri',
            'email' => 'windah@parkirpintar.id',
        ]);

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create([
            'name'  => 'Samuel Shiva',
            'email' => 'samuel@parkirpintar.id',
        ]);

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
