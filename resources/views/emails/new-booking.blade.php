<x-mail::message>
# New website booking

A customer just booked through the website.

**Reference:** {{ $booking->reference }}
**Name:** {{ $booking->name }}
**Phone:** {{ $booking->formatted_phone }}
@if($booking->email)
**Email:** {{ $booking->email }}
@endif
**Service:** {{ $booking->service }}
@if($booking->preferred_date)
**Preferred day:** {{ $booking->preferred_date->format('D j M Y') }}
@endif
@if($booking->preferred_time)
**Preferred time:** {{ $booking->preferred_time }}
@endif
@if($booking->notes)

**Message:**
{{ $booking->notes }}
@endif

<x-mail::button :url="route('admin.bookings.show', $booking)">
Open in admin
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
