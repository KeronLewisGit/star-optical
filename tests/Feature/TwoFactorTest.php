<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_enable_two_factor_and_is_challenged_on_next_login(): void
    {
        $user = User::factory()->create(['password' => 'Password123!@#']);
        $engine = new Google2FA;

        // Step 1: start setup (secret kept in session only).
        $this->actingAs($user)->post(route('admin.profile.two-factor.enable'))->assertRedirect();
        $secret = session('two_factor.pending_secret');
        $this->assertNotNull($secret);
        $this->assertNull($user->fresh()->two_factor_secret);

        // Wrong code does not enable it.
        $this->actingAs($user)->post(route('admin.profile.two-factor.confirm'), ['code' => '000000'])->assertSessionHasErrors('code');
        $this->assertNull($user->fresh()->two_factor_secret);

        // Correct code enables it and issues recovery codes.
        $this->actingAs($user)->withSession(['two_factor.pending_secret' => $secret])
            ->post(route('admin.profile.two-factor.confirm'), ['code' => $engine->getCurrentOtp($secret)])
            ->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertTrue($user->hasTwoFactorEnabled());
        $this->assertCount(8, $user->two_factor_recovery_codes);
        $this->assertNotSame($secret, \DB::table('users')->value('two_factor_secret')); // encrypted at rest

        // Fresh login: password alone is not enough.
        $this->post('/logout');
        $this->post('/login', ['email' => $user->email, 'password' => 'Password123!@#'])->assertRedirect(route('two-factor.challenge'));
        $this->get('/admin')->assertRedirect(route('two-factor.challenge'));

        $this->post(route('two-factor.challenge'), ['code' => '123456'])->assertSessionHasErrors('code');
        $this->get('/admin')->assertRedirect(route('two-factor.challenge'));

        $this->post(route('two-factor.challenge'), ['code' => $engine->getCurrentOtp($secret)])->assertRedirect('/admin');
        $this->get('/admin')->assertOk();
    }

    public function test_recovery_code_works_once(): void
    {
        $user = User::factory()->create();
        $service = app(\App\Services\TwoFactorService::class);
        $user->forceFill(['two_factor_secret' => $service->generateSecret(), 'two_factor_confirmed_at' => now()])->save();
        $codes = $service->generateRecoveryCodes($user);

        $this->actingAs($user)->post(route('two-factor.challenge'), ['recovery_code' => $codes[0]])->assertRedirect('/admin');
        $this->assertCount(7, $user->fresh()->two_factor_recovery_codes);

        $this->post('/logout');
        $this->actingAs($user)->post(route('two-factor.challenge'), ['recovery_code' => $codes[0]])->assertSessionHasErrors('code');
    }

    public function test_required_two_factor_setting_forces_setup(): void
    {
        Setting::set('require_two_factor', '1');
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertRedirect(route('admin.profile.edit'));
        $this->actingAs($user)->get(route('admin.profile.edit'))->assertOk()->assertSee('Set up two-factor');
    }
}
