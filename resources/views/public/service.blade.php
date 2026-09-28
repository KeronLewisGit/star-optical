@php
    use App\Models\Setting;
    $wa = fn (string $msg) => Setting::whatsappLink($msg);
    $faqSchema = [
        '@'.'context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(fn ($qa) => [
            '@type' => 'Question', 'name' => $qa[0],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $qa[1]],
        ], $page['faq']),
    ];
    $breadcrumbSchema = [
        '@'.'context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => $page['h1'], 'item' => url()->current()],
        ],
    ];
@endphp
<x-public-layout :settings="$settings" :title="$page['title']" :meta-description="$page['description']">
  <x-slot name="structuredData">
    <script type="application/ld+json" nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json" nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
  </x-slot>

  <section class="page-hero">
    <div class="container">
      <ol class="breadcrumbs" aria-label="Breadcrumb"><li><a href="{{ route('home') }}">Home</a></li><li>›</li><li>{{ $page['h1'] }}</li></ol>
      <h1>{{ $page['h1'] }}</h1>
      <p class="lead">{{ $page['lead'] }}</p>
      <div class="btn-row">
        <a class="btn btn--wa" href="{{ $wa($page['cta']) }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Book on WhatsApp</a>
        <a class="btn btn--outline btn--outline-light" href="tel:{{ preg_replace('/\D+/', '', $settings['phone']) }}">Call {{ $settings['phone'] }}</a>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container split">
      <div class="split__media"><img class="rounded" src="{{ asset('assets/images/'.str_replace('.jpg', '.webp', $page['image'])) }}" alt="{{ $page['image_alt'] }}" width="1400" height="933" loading="eager" /></div>
      <div class="split__text">
        <span class="eyebrow">{{ $settings['business_name'] }}, Cunupia</span>
        <h2>{{ $page['sections'][0][0] }}</h2>
        <p>{{ $page['sections'][0][1] }}</p>
        <ul class="checklist">
          @foreach($page['bullets'] as $b)<li>{{ $b }}</li>@endforeach
        </ul>
      </div>
    </div>
  </section>

  <section class="section section--soft">
    <div class="container prose">
      @foreach(array_slice($page['sections'], 1) as [$heading, $text])
        <h2>{{ $heading }}</h2>
        <p>{{ $text }}</p>
      @endforeach
      <h2>Visit our optical store in Cunupia</h2>
      <p>{{ $settings['business_name'] }} is located at {{ $settings['address'] }}, on the main road between Chaguanas and Caroni. Open {{ $settings['hours_weekdays'] }} and {{ $settings['hours_saturday'] }}. <a href="{{ route('home') }}#location">Get directions</a> or <a href="{{ $wa($page['cta']) }}" target="_blank" rel="noopener">message us on WhatsApp</a>.</p>
      <div class="btn-row">
        <a class="btn btn--wa" href="{{ $wa($page['cta']) }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Book on WhatsApp</a>
        <a class="btn btn--outline" href="{{ route('home') }}#frames">Browse frames</a>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-head"><span class="eyebrow">Common questions</span><h2>{{ $page['h1'] }}: FAQ</h2></div>
      <div class="faq">
        @foreach($page['faq'] as [$q, $a])
          <details @if($loop->first) open @endif><summary>{{ $q }}</summary><p>{{ $a }}</p></details>
        @endforeach
      </div>
    </div>
  </section>

  @if($promotions->isNotEmpty())
  <section class="section section--soft">
    <div class="container">
      <div class="section-head"><span class="eyebrow">This month</span><h2>Current promotions</h2></div>
      <div class="related">
        @foreach($promotions as $promo)
          <a href="{{ $wa($promo->whatsappMessage()) }}" target="_blank" rel="noopener">{{ $promo->title }}<small>{{ $promo->body }}</small></a>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  <section class="section">
    <div class="container">
      <div class="section-head"><span class="eyebrow">More from Star Optical</span><h2>Other services</h2></div>
      <div class="related">
        @foreach($related as $r)
          <a href="{{ route($r['route']) }}">{{ $r['h1'] }}<small>{{ \Illuminate\Support\Str::limit($r['lead'], 90) }}</small></a>
        @endforeach
        <a href="{{ route('home') }}#contact">Book an appointment<small>Save your request and continue on WhatsApp.</small></a>
      </div>
    </div>
  </section>
</x-public-layout>
