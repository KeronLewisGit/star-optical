@php
    $nonce = \Illuminate\Support\Facades\Vite::cspNonce();
    $ga = $settings['ga_measurement_id'] ?? '';
    $gtm = $settings['gtm_container_id'] ?? '';
@endphp
<!DOCTYPE html>
<html lang="en-TT">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  @php
    $seoTitle = $title ?? $settings['seo_title'];
    $seoDescription = $metaDescription ?? $settings['seo_description'];
    $canonical = $canonical ?? url()->current();
    // JPEG for social previews: WhatsApp and Facebook do not reliably render WebP.
    $ogImage = $ogImage ?? asset('assets/images/photo-07.jpg');
    $sameAs = array_values(array_filter([$settings['facebook_url'], $settings['instagram_url'], $settings['tiktok_url']]));
    $phoneDigits = preg_replace('/\D+/', '', $settings['phone']);
    $phoneE164 = '+'.(strlen($phoneDigits) === 7 ? '1868'.$phoneDigits : (strlen($phoneDigits) === 10 ? '1'.$phoneDigits : $phoneDigits));
  @endphp
  <title>{{ $seoTitle }}</title>
  <meta name="description" content="{{ $seoDescription }}" />
  <link rel="canonical" href="{{ $canonical }}" />
  <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ route('sitemap') }}" />
  @isset($robots)<meta name="robots" content="{{ $robots }}" />@endisset
  <link rel="alternate" hreflang="en-tt" href="{{ $canonical }}" />
  <link rel="alternate" hreflang="x-default" href="{{ $canonical }}" />
  <meta name="geo.region" content="TT" />
  <meta name="geo.placename" content="Cunupia, Trinidad and Tobago" />
  {{-- Open Graph / social sharing --}}
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="{{ $settings['business_name'] }}" />
  <meta property="og:title" content="{{ $seoTitle }}" />
  <meta property="og:description" content="{{ $seoDescription }}" />
  <meta property="og:url" content="{{ $canonical }}" />
  <meta property="og:image" content="{{ $ogImage }}" />
  <meta property="og:locale" content="en_TT" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="{{ $seoTitle }}" />
  <meta name="twitter:description" content="{{ $seoDescription }}" />
  <meta name="twitter:image" content="{{ $ogImage }}" />
  {{-- Structured data: local business (Optician) for Google Maps and rich results --}}
  <script type="application/ld+json" nonce="{{ $nonce }}">
  {!! json_encode([
    '@'.'context' => 'https://schema.org',
    '@type' => 'Optician',
    '@id' => url('/').'#business',
    'name' => $settings['business_name'],
    'alternateName' => 'Star Optical',
    'slogan' => $settings['tagline'],
    'description' => $settings['seo_description'],
    'url' => url('/'),
    'logo' => asset('assets/images/logo.png'),
    'image' => $ogImage,
    'telephone' => $phoneE164,
    'email' => $settings['email'],
    'priceRange' => '$$',
    'currenciesAccepted' => 'TTD',
    'address' => [
      '@type' => 'PostalAddress',
      'streetAddress' => '#267 Southern Main Road',
      'addressLocality' => 'Cunupia',
      'addressRegion' => 'Chaguanas',
      'addressCountry' => 'TT',
    ],
    'geo' => ['@type' => 'GeoCoordinates', 'latitude' => 10.5565, 'longitude' => -61.3862],
    'areaServed' => ['Cunupia', 'Chaguanas', 'Caroni', 'Couva', 'Arima', 'Trinidad and Tobago'],
    'hasMap' => 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($settings['address']),
    'openingHoursSpecification' => [
      ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'], 'opens' => '09:00', 'closes' => '17:00'],
      ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => ['Saturday'], 'opens' => '09:00', 'closes' => '14:00'],
    ],
    'makesOffer' => [
      ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Free eye examination'], 'price' => '0', 'priceCurrency' => 'TTD'],
      ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Product', 'name' => 'Prescription eyeglasses']],
      ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Product', 'name' => 'Prescription and polarised sunglasses']],
    ],
    'contactPoint' => ['@type' => 'ContactPoint', 'contactType' => 'appointments', 'telephone' => '+'.\App\Models\Setting::whatsappNumber(), 'url' => \App\Models\Setting::whatsappLink(''), 'availableLanguage' => 'en'],
  ] + ($sameAs ? ['sameAs' => $sameAs] : []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
  </script>
  {{ $structuredData ?? '' }}
  @if($settings['google_site_verification'] ?? false)
  <meta name="google-site-verification" content="{{ $settings['google_site_verification'] }}" />
  @endif
  <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="48x48" />
  <link rel="icon" href="{{ asset('assets/images/icon-192.png') }}" type="image/png" sizes="192x192" />
  <link rel="apple-touch-icon" href="{{ asset('assets/images/icon-180.png') }}" sizes="180x180" />
  @if(request()->routeIs('home'))<link rel="preload" as="image" href="{{ asset('assets/images/photo-07.webp') }}" fetchpriority="high" />@endif
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ filemtime(public_path('assets/css/style.css')) }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/seo.css') }}?v={{ filemtime(public_path('assets/css/seo.css')) }}" />

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
      <a class="topbar__item" href="{{ url('/') }}/#location">
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
        <img src="{{ asset('assets/images/logo-400.png') }}" alt="{{ $settings['business_name'] }} logo" width="400" height="170" />
      </a>
      <nav class="nav" id="nav" aria-label="Main navigation">
        <a href="{{ url('/') }}/#frames">Frames &amp; Sunglasses</a>
        <a href="{{ url('/') }}/#eye-exams">Free Eye Exams</a>
        <a href="{{ url('/') }}/#promotions">Promotions</a>
        <a href="{{ url('/') }}/#about">About</a>
        <a href="{{ url('/') }}/#location">Location</a>
        <a href="{{ url('/') }}/#contact">Contact</a>
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
        <img src="{{ asset('assets/images/logo-400.png') }}" alt="{{ $settings['business_name'] }} logo" width="400" height="170" loading="lazy" />
        <p><strong>{{ $settings['tagline'] }}</strong><br />{{ $settings['business_name'] }}, serving Cunupia and all of Trinidad &amp; Tobago.</p>
      </div>
      <div>
        <h4>Quick links</h4>
        <ul>
          <li><a href="{{ route('page.eye-exams') }}">Free Eye Exams in Cunupia</a></li>
          <li><a href="{{ route('page.eyeglasses') }}">Eyeglasses &amp; Frames in Trinidad</a></li>
          <li><a href="{{ route('page.sunglasses') }}">Prescription &amp; Polarised Sunglasses</a></li>
          <li><a href="{{ url('/') }}/#frames">Frames &amp; Sunglasses</a></li>
          <li><a href="{{ url('/') }}/#eye-exams">Free Eye Exams</a></li>
          <li><a href="{{ url('/') }}/#promotions">Promotions</a></li>
          <li><a href="{{ url('/') }}/#about">About</a></li>
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
      <span>Designed and Developed by <a href="https://linkedin.com/in/keronlewis" target="_blank" rel="noopener">Code Canvas Consultants Ltd.</a></span>
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
