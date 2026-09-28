<?php

namespace Tests\Feature;

use App\Mail\NewBookingNotification;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublicBookingTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Alicia Ramnath',
            'phone' => '(868) 555-1234',
            'email' => 'alicia@example.com',
            'service' => 'Free eye examination',
            'preferred_date' => now()->addDays(3)->toDateString(),
            'preferred_time' => 'Morning',
            'notes' => 'Need new glasses',
            'form_token' => Crypt::encryptString((string) (time() - 10)),
        ], $overrides);
    }

    public function test_home_page_renders_with_booking_form_and_security_headers(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Book an appointment')
            ->assertSee('name="form_token"', false)
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->assertStringContainsString("script-src 'self' 'nonce-", $response->headers->get('Content-Security-Policy'));
    }

    public function test_booking_is_saved_and_customer_is_sent_to_thank_you_page(): void
    {
        Mail::fake();
        Setting::set('notification_email', 'owner@example.com');

        $response = $this->post('/book', $this->payload());

        $booking = Booking::first();
        $this->assertNotNull($booking);
        $this->assertSame('18685551234', $booking->phone);
        $this->assertSame('new', $booking->status);
        $this->assertSame('Need new glasses', $booking->notes);

        // Notes are encrypted at rest.
        $raw = \DB::table('bookings')->value('notes');
        $this->assertNotSame('Need new glasses', $raw);

        $response->assertRedirect(route('booking.thanks', $booking));
        $this->followRedirects($response)
            ->assertOk()
            ->assertSee($booking->reference)
            ->assertSee('wa.me/18683804144', false);

        Mail::assertSent(NewBookingNotification::class, fn ($mail) => $mail->hasTo('owner@example.com'));
    }

    public function test_honeypot_submissions_are_rejected(): void
    {
        $this->post('/book', $this->payload(['website' => 'http://spam.example']))
            ->assertSessionHasErrors('website');

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_submissions_faster_than_three_seconds_are_rejected(): void
    {
        $this->post('/book', $this->payload(['form_token' => Crypt::encryptString((string) time())]))
            ->assertSessionHasErrors('form_token');

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_invalid_input_is_rejected(): void
    {
        $this->post('/book', $this->payload(['name' => '<script>', 'phone' => 'abc', 'service' => 'Hack']))
            ->assertSessionHasErrors(['name', 'phone', 'service']);
    }

    public function test_thank_you_page_is_not_accessible_from_another_session(): void
    {
        $booking = Booking::factory()->create();

        $this->get(route('booking.thanks', $booking))->assertNotFound();
    }

    public function test_booking_endpoint_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/book', $this->payload(['form_token' => 'x']));
        }

        $this->post('/book', $this->payload())->assertStatus(429);
    }

    public function test_analytics_snippet_only_renders_when_configured(): void
    {
        $this->get('/')->assertDontSee('googletagmanager.com/gtag/js');

        Setting::set('ga_measurement_id', 'G-TEST12345');

        $this->get('/')->assertSee('googletagmanager.com/gtag/js?id=G-TEST12345', false);
    }
}
