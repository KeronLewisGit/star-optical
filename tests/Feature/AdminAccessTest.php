<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/admin/patients')->assertRedirect('/login');
        $this->get('/admin/settings')->assertRedirect('/login');
    }

    public function test_public_registration_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', ['name' => 'x', 'email' => 'x@example.com', 'password' => 'Password123!@#', 'password_confirmation' => 'Password123!@#'])->assertNotFound();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_staff_can_use_crm_but_not_administration(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)->get('/admin')->assertOk();
        $this->actingAs($staff)->get('/admin/bookings')->assertOk();
        $this->actingAs($staff)->get('/admin/patients')->assertOk();
        $this->actingAs($staff)->get('/admin/promotions')->assertOk();

        $this->actingAs($staff)->get('/admin/settings')->assertForbidden();
        $this->actingAs($staff)->get('/admin/users')->assertForbidden();
        $this->actingAs($staff)->get('/admin/activity')->assertForbidden();

        $patient = Patient::factory()->create();
        $this->actingAs($staff)->delete(route('admin.patients.destroy', $patient))->assertForbidden();
        $this->assertNull($patient->fresh()->deleted_at);
    }

    public function test_admin_can_access_everything(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin/settings')->assertOk();
        $this->actingAs($admin)->get('/admin/users')->assertOk();
        $this->actingAs($admin)->get('/admin/activity')->assertOk();

        $booking = Booking::factory()->create();
        $this->actingAs($admin)->delete(route('admin.bookings.destroy', $booking))->assertRedirect(route('admin.bookings.index'));
        $this->assertSoftDeleted($booking);
    }

    public function test_deactivated_accounts_cannot_sign_in_or_keep_a_session(): void
    {
        $user = User::factory()->inactive()->create(['password' => 'Password123!@#']);

        $this->post('/login', ['email' => $user->email, 'password' => 'Password123!@#'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        // A session that already exists is terminated on the next request.
        $this->actingAs($user)->get('/admin')->assertRedirect('/login');
    }

    public function test_admin_cannot_demote_or_deactivate_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'name' => $admin->name, 'email' => $admin->email, 'role' => 'staff',
        ])->assertSessionHasErrors('role');
        $this->assertSame('admin', $admin->fresh()->role);

        $this->actingAs($admin)->patch(route('admin.users.toggle', $admin))->assertSessionHasErrors('user');
        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_weak_passwords_are_rejected_when_creating_staff(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'New Staff', 'email' => 'staff@example.com', 'role' => 'staff',
            'password' => 'password', 'password_confirmation' => 'password',
        ])->assertSessionHasErrors('password');
    }

    public function test_admin_pages_are_not_indexable_and_not_cached(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertHeader('Cache-Control', 'no-store, private');
    }
}
