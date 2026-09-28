@php
    $nonce = \Illuminate\Support\Facades\Vite::cspNonce();
    $ga = $settings['ga_measurement_id'] ?? '';
    $gtm = $settings['gtm_container_id'] ?? '';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>{{ $title ?? $settings['business_name'].' | '.$settings['tagline'] }}</title>
  <meta name="description" content="{{ $metaDescription ?? $settings['business_name'].', '.$settings['address'].'. Affordable eyewear, quality eye care and free eye examinations. Book your appointment on WhatsApp.' }}" />
  @if($settings['google_site_verification'] ?? false)
  <meta name="google-site-verification" content="{{ $settings['google_site_verification'] }}" />
  @endif
  <link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/png" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ filemtime(public_path('assets/css/style.css')) }}" />

  {{-- Google Tag Manager (container ID is set by the administrator under Settings) --}}
  @if($gtm)
  <script nonce="{{ $nonce }}">(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;j.setAttribute('nonce','{{ $nonce }}');f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $gtm }}');</script>
  @endif

  {{-- Google Analytics 4 (measurement ID is set by the administrator under Settings) --}}
  @if($ga)
  <script async nonce="{{ $nonce }}" src="https://www.googletagmanager.com/gtag/js?id={{ $ga }}"></script>
  <script nonce="{{ $nonce }}">
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ $ga }}', { anonymize_ip: true, cookie_flags: 'SameSite=None;Secure' });
  </script>
  @endif
  {{ $head ?? '' }}
</head>
<body data-wa-number="{{ \App\Models\Setting::whatsappNumber() }}">
  @if($gtm)
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtm }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  @endif

  {{-- ===== Top bar ===== --}}
  <div class="topbar">
    <div class="container topbar__inner">
      <a class="topbar__item" href="{{ route('home') }}#location">
        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
        {{ $settings['address'] }}
      </a>
      <div class="topbar__right">
        <span class="topbar__item"><i class="fa-regular fa-clock" aria-hidden="true"></i> {{ $settings['hours_weekdays'] }} · {{ $settings['hours_saturday'] }}</span>
        <a class="topbar__item" href="tel:{{ preg_replace('/\D+/', '', $settings['phone']) }}"><i class="fa-solid fa-phone" aria-hidden="true"></i> {{ $settings['phone'] }}</a>
      </div>
    </div>
  </div>

  {{-- ===== Header ===== --}}
  <header class="header" id="header">
    <div class="container header__inner">
      <a href="{{ route('home') }}" class="logo" aria-label="{{ $settings['business_name'] }} home">
        <img src="{{ asset('assets/images/logo.png') }}" alt="{{ $settings['business_name'] }}" />
      </a>
      <nav class="nav" id="nav" aria-label="Main navigation">
        <a href="{{ route('home') }}#frames">Frames &amp; Sunglasses</a>
        <a href="{{ route('home') }}#eye-exams">Free Eye Exams</a>
        <a href="{{ route('home') }}#promotions">Promotions</a>
        <a href="{{ route('home') }}#about">About</a>
        <a href="{{ route('home') }}#location">Location</a>
        <a href="{{ route('home') }}#contact">Contact</a>
        <a class="btn btn--wa btn--sm nav__cta" href="{{ \App\Models\Setting::whatsappLink("Hi Star Optical! I'd like to book an appointment.") }}" target="_blank" rel="noopener">
          <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Book on WhatsApp
        </a>
      </nav>
      <button class="nav-toggle" id="navToggle" aria-label="Open menu" aria-expanded="false" aria-controls="nav">
        <span></span><span></span><span></span>
      </button>
    </div>
  </header>

  <main id="top">
    {{ $slot }}
  </main>

  {{-- ===== Footer ===== --}}
  <footer class="footer">
    <div class="container footer__grid">
      <div class="footer__brand">
        <img src="{{ asset('assets/images/logo.png') }}" alt="{{ $settings['business_name'] }}" />
        <p><strong>{{ $settings['tagline'] }}</strong><br />{{ $settings['business_name'] }}, serving Cunupia and all of Trinidad &amp; Tobago.</p>
      </div>
      <div>
        <h4>Quick links</h4>
        <ul>
          <li><a href="{{ route('home') }}#services">Products &amp; Services</a></li>
          <li><a href="{{ route('home') }}#frames">Frames &amp; Sunglasses</a></li>
          <li><a href="{{ route('home') }}#eye-exams">Free Eye Exams</a></li>
          <li><a href="{{ route('home') }}#promotions">Promotions</a></li>
          <li><a href="{{ route('home') }}#about">About</a></li>
        </ul>
      </div>
      <div>
        <h4>Visit</h4>
        <ul>
          <li>{{ $settings['address'] }}</li>
          <li>{{ $settings['hours_weekdays'] }}</li>
          <li>{{ $settings['hours_saturday'] }}</li>
          <li>Sun: {{ $settings['hours_sunday'] }}</li>
        </ul>
      </div>
      <div>
        <h4>Contact</h4>
        <ul>
          <li><a href="{{ \App\Models\Setting::whatsappLink('Hi Star Optical!') }}" target="_blank" rel="noopener">WhatsApp: {{ preg_replace('/^1?(868)(\d{3})(\d{4})$/', '($1) $2-$3', \App\Models\Setting::whatsappNumber()) }}</a></li>
          <li><a href="tel:{{ preg_replace('/\D+/', '', $settings['phone']) }}">Phone: {{ $settings['phone'] }}</a></li>
          <li><a href="mailto:{{ $settings['email'] }}">{{ $settings['email'] }}</a></li>
          <li><a href="{{ route('login') }}" rel="nofollow">Staff login</a></li>
        </ul>
      </div>
    </div>
    <div class="container footer__bottom">
      <span>© {{ date('Y') }} {{ $settings['business_name'] }} All rights reserved.</span>
      <span>{{ $settings['tagline'] }}</span>
    </div>
  </footer>

  <a class="wa-float" href="{{ \App\Models\Setting::whatsappLink("Hi Star Optical! I'd like to book an appointment.") }}" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
    <span>Chat with us</span>
  </a>

  <script src="{{ asset('assets/js/main.js') }}?v={{ filemtime(public_path('assets/js/main.js')) }}" nonce="{{ $nonce }}"></script>
  {{ $scripts ?? '' }}
</body>
</html>
