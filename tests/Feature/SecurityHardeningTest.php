<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_does_not_expose_demo_credentials(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertDontSee('Akun demo');
        $response->assertDontSee('admin@bmd.test');
    }

    public function test_login_is_locked_after_five_failed_attempts(): void
    {
        User::factory()->create([
            'email' => 'admin@example.test',
            'password' => 'correct-password',
            'role' => 'admin',
        ]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->from(route('login'))->post(route('login.store'), [
                'email' => 'admin@example.test',
                'password' => 'wrong-password',
            ])->assertRedirect(route('login'));
        }

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => 'admin@example.test',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Terlalu banyak percobaan login.', session('errors')->first('email'));

        RateLimiter::clear('login:email:'.hash('sha256', 'admin@example.test'));
        RateLimiter::clear('login:ip:'.hash('sha256', '127.0.0.1'));
    }

    public function test_login_response_has_browser_security_headers_and_is_not_cached(): void
    {
        $response = $this->get(route('login'));

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(self), geolocation=(), microphone=(), payment=(), usb=()');
        $response->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_non_admin_cannot_access_internal_pages(): void
    {
        $user = User::factory()->create(['role' => 'viewer']);

        $this->actingAs($user)->get(route('dashboard'))->assertForbidden();
        $this->actingAs($user)->get(route('assets.index'))->assertForbidden();
        $this->actingAs($user)->get(route('exports.history'))->assertForbidden();
    }
}
