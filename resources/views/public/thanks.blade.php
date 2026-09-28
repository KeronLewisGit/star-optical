<x-public-layout :settings="$settings" title="Booking received | {{ $settings['business_name'] }}" meta-description="Your appointment request has been received.">
  <x-slot name="head"><meta name="robots" content="noindex" /></x-slot>

  <section class="section thanks">
    <div class="container thanks__card">
      <span class="thanks__icon"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
      <span class="eyebrow">Booking received</span>
      <h1>Thanks, {{ $booking->name }}!</h1>
      <p class="lead">We've saved your request for a <strong>{{ strtolower($booking->service) }}</strong>. Your reference is <strong>{{ $booking->reference }}</strong>.</p>
      <p>To confirm your appointment, send us the pre-filled message on WhatsApp. It only takes a second and lets us reply to you directly.</p>
      <a class="btn btn--wa btn--lg" id="thanksWa" href="{{ $whatsappUrl }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Continue on WhatsApp</a>
      <p class="muted thanks__alt">Prefer to call? <a href="tel:{{ preg_replace('/\D+/', '', $settings['phone']) }}">{{ $settings['phone'] }}</a> · <a href="{{ route('home') }}">Back to the website</a></p>

      <dl class="thanks__summary">
        <div><dt>Name</dt><dd>{{ $booking->name }}</dd></div>
        <div><dt>Phone</dt><dd>{{ $booking->formatted_phone }}</dd></div>
        <div><dt>Service</dt><dd>{{ $booking->service }}</dd></div>
        @if($booking->preferred_date)<div><dt>Preferred day</dt><dd>{{ $booking->preferred_date->format('l j F Y') }}</dd></div>@endif
        @if($booking->preferred_time)<div><dt>Preferred time</dt><dd>{{ $booking->preferred_time }}</dd></div>@endif
      </dl>
    </div>
  </section>

  <x-slot name="scripts">
    {{-- Reports a lead conversion to Google Analytics when it is configured. --}}
    <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
      if (typeof gtag === 'function') {
        gtag('event', 'generate_lead', { event_category: 'booking', event_label: @json($booking->service) });
      }
      if (window.dataLayer) { window.dataLayer.push({ event: 'booking_submitted', service: @json($booking->service) }); }
    </script>
  </x-slot>
</x-public-layout>
