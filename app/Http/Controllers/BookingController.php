<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Mail\NewBookingNotification;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class BookingController extends Controller
{
    /** Save the lead, notify staff, then hand the customer over to WhatsApp. */
    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $booking = Booking::create($request->bookingData() + [
            'ip_hash' => hash('sha256', $request->ip().config('app.key')),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        if ($to = Setting::get('notification_email')) {
            try {
                Mail::to($to)->send(new NewBookingNotification($booking));
            } catch (\Throwable $e) {
                Log::warning('Booking notification email failed', ['booking' => $booking->id, 'error' => $e->getMessage()]);
            }
        }

        // Only the creator's session may open the thank-you page for this reference.
        $request->session()->put('booking.'.$booking->reference, true);

        return redirect()->route('booking.thanks', $booking);
    }

    public function thanks(Booking $booking): View
    {
        abort_unless(session()->get('booking.'.$booking->reference), 404);

        return view('public.thanks', [
            'booking' => $booking,
            'settings' => Setting::all_cached(),
            'whatsappUrl' => Setting::whatsappLink($booking->whatsappMessage()),
        ]);
    }
}
