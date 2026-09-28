@php
    use App\Models\Setting;
    $wa = fn (string $msg) => Setting::whatsappLink($msg);
    $bookExam = $wa("Hi Star Optical! I'd like to book a free eye examination.");
@endphp
<x-public-layout :settings="$settings">
  {{-- ===== Hero ===== --}}
  <section class="hero hero--photo">
    <img class="hero__bg" src="{{ asset('assets/images/photo-07.webp') }}" alt="Woman wearing round gold prescription eyeglasses from Star Optical, optician in Cunupia, Trinidad" width="1400" height="933" fetchpriority="high" />
    <div class="hero__overlay"></div>
    <div class="container hero__grid">
      <div class="hero__content">
        <span class="eyebrow eyebrow--onDark">{{ $settings['tagline'] }}</span>
        <h1>See better.<br />Feel better.<span class="hero__sub">Eyeglasses, sunglasses &amp; free eye exams in Cunupia, Trinidad</span></h1>
        <p class="lead">Expert eye care from your neighbourhood optician. Stylish frames, prescription &amp; polarised sunglasses, and <strong>free eye examinations</strong> near Chaguanas.</p>
        <div class="btn-row">
          <a class="btn btn--wa" href="{{ $bookExam }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Book a free eye exam</a>
          <a class="btn btn--outline btn--outline-light" href="#frames">Browse frames</a>
        </div>
      </div>
    </div>
    <div class="container hero__props">
      <a class="prop" href="#eye-exams"><span class="prop__icon"><i class="fa-solid fa-eye" aria-hidden="true"></i></span><div><strong>Free eye examinations</strong><small>For every customer</small></div></a>
      <a class="prop" href="#frames"><span class="prop__icon"><i class="fa-solid fa-glasses" aria-hidden="true"></i></span><div><strong>Frames &amp; sunglasses</strong><small>Prescription &amp; polarised</small></div></a>
      <a class="prop" href="#contact"><span class="prop__icon"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></span><div><strong>Book via WhatsApp</strong><small>No forms, no waiting</small></div></a>
      <a class="prop" href="#location"><span class="prop__icon"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span><div><strong>Cunupia</strong><small>#267 Southern Main Road</small></div></a>
    </div>
  </section>

  {{-- ===== Products & services ===== --}}
  <section class="section section--soft" id="services">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Products &amp; services</span>
        <h2>Optical services in Cunupia, Trinidad</h2>
        <p>From your free eye exam to the perfect pair of prescription glasses or sunglasses, we handle it all under one roof.</p>
      </div>
      <div class="grid grid--3 services">
        @foreach([
          ['fa-glasses', 'Prescription eyeglasses', 'A wide variety of lenses and frames made to your prescription.'],
          ['fa-people-roof', 'Frames for men, women & children', 'Stylish, comfortable everyday frames for the whole family.'],
          ['fa-sun', 'Prescription sunglasses', 'See clearly and protect your eyes with sunglasses made to your script.'],
          ['fa-umbrella-beach', 'Polarised sunglasses', 'Cut glare on the road and at the beach with polarised lenses.'],
          ['fa-eye', 'Free eye examinations', 'Comprehensive eye exams to keep your vision healthy, at no cost.'],
          ['fa-wand-magic-sparkles', 'Personal frame styling', 'Friendly help choosing frames that suit your face, style and budget.'],
        ] as [$icon, $title, $text])
        <div class="service">
          <span class="service__icon"><i class="fa-solid {{ $icon }}" aria-hidden="true"></i></span>
          <h3>{{ $title }}</h3>
          <p>{{ $text }}</p>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ===== Eye care cards ===== --}}
  <section class="section" id="eye-exams">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Exceptional eye care</span>
        <h2>Free eye examinations in Cunupia. <span class="txt-blue">Always.</span></h2>
        <p>Friendly, personalised eye tests for adults, seniors and children, designed to keep your vision clear and your eyes healthy for life. <a href="{{ route('page.eye-exams') }}">Learn more about our free eye exams</a>.</p>
      </div>
      <div class="bigcard">
        <div class="bigcard__text">
          <h3>Your eyes deserve expert care</h3>
          <ul class="dots">
            <li>Comprehensive <strong>free</strong> eye examinations for all ages</li>
            <li>Prescription checks and updates</li>
            <li>Frame fitting and style advice included</li>
          </ul>
          <a class="btn btn--wa btn--block" href="{{ $bookExam }}" target="_blank" rel="noopener">Book your free eye exam <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
        <div class="bigcard__media"><img src="{{ asset('assets/images/photo-10.webp') }}" alt="Man trying on clubmaster frames during a free eye exam at Star Optical, Cunupia" width="1400" height="933" loading="lazy" /></div>
      </div>
      <div class="bigcard bigcard--reverse">
        <div class="bigcard__media"><img src="{{ asset('assets/images/photo-05-800.webp') }}" alt="Woman adjusting her new prescription eyeglasses from Star Optical, Trinidad" width="800" height="1200" loading="lazy" /></div>
        <div class="bigcard__text">
          <h3>Eyewear that matches your style</h3>
          <ul class="dots">
            <li>Stylish frames for men, women and children</li>
            <li>Prescription and polarised sunglasses</li>
            <li>Personal help choosing frames to suit your face, style and budget</li>
          </ul>
          <a class="btn btn--primary btn--block" href="#frames"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Browse frames</a>
        </div>
      </div>
    </div>
  </section>

  {{-- ===== Frames & sunglasses ===== --}}
  <section class="section" id="frames">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Our collection</span>
        <h2>Eyeglass Frames &amp; Sunglasses in Trinidad</h2>
        <p>A hand-picked selection of prescription glasses, designer frames and polarised sunglasses for every face, style and budget. Visit us in Cunupia to try them on, or message us about any style you like. See our <a href="{{ route('page.eyeglasses') }}">eyeglasses</a> and <a href="{{ route('page.sunglasses') }}">sunglasses</a> pages for details.</p>
      </div>
      <div class="filters" role="tablist" aria-label="Filter collection">
        <button class="filter is-active" data-filter="all">All</button>
        <button class="filter" data-filter="frames">Frames</button>
        <button class="filter" data-filter="sunglasses">Sunglasses</button>
        <button class="filter" data-filter="kids">Kids</button>
      </div>
      @php
        $products = [
          ['frames', 'photo-02.jpg', 'Tortoise Cat-Eye', 'Bold, feminine and timeless.'],
          ['frames', 'photo-09.jpg', 'Crystal Clubmaster', 'A modern take on a classic browline.'],
          ['frames', 'photo-07.jpg', 'Round Gold Metal', 'Lightweight wire frames with a soft look.'],
          ['kids', 'photo-11.jpg', 'Junior Flex Blue', 'Flexible, durable and school-ready.'],
          ['sunglasses', null, 'Polarised Aviator', 'Polarised lenses, gold metal frame.'],
          ['frames', 'photo-13.jpg', 'Cherry Acetate', 'A pop of colour for everyday wear.'],
          ['kids', 'photo-16.jpg', 'Little Star Pink', 'Bright, bendy and made for playtime.'],
          ['frames', 'photo-17.jpg', 'Metro Aviator', 'Sharp lines, confident look.'],
          ['sunglasses', null, 'Prescription Wayfarer', 'Made to your script, matte black frame.'],
          ['kids', 'photo-08.jpg', 'Scholar Black', 'Sturdy frames for growing readers.'],
          ['frames', 'photo-15.jpg', 'Semi-Rimless Blue Light', 'Screen-friendly lenses in a light frame.'],
          ['sunglasses', null, 'Polarised Oversized Round', 'Gradient polarised lenses, tortoise frame.'],
        ];
        $tagClass = ['frames' => '', 'kids' => ' tag--kids', 'sunglasses' => ' tag--sun'];
      @endphp
      <div class="grid grid--products" id="productGrid">
        @foreach($products as [$cat, $img, $name, $desc])
        <article class="product" data-cat="{{ $cat }}">
          @if($img)
          <div class="product__img"><img src="{{ asset('assets/images/'.str_replace('.jpg', '-800.webp', $img)) }}" alt="{{ $name }} {{ $cat === 'kids' ? 'kids eyeglass frames' : ($cat === 'sunglasses' ? 'sunglasses' : 'eyeglass frames') }} at Star Optical, Trinidad" width="800" height="{{ in_array($img, ['photo-07.jpg','photo-13.jpg','photo-16.jpg','photo-17.jpg','photo-15.jpg']) ? 533 : 1200 }}" loading="lazy" /></div>
          @else
          <div class="product__img product__img--placeholder">
            <svg viewBox="0 0 120 60" class="sun-icon" aria-hidden="true"><path d="M4 22h112M14 22c-2 16 4 28 18 28s22-10 22-28M66 22c0 18 8 28 22 28s20-12 18-28M54 22c3-4 9-4 12 0" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>Sunglasses photo</span>
          </div>
          @endif
          <div class="product__body">
            <span class="tag{{ $tagClass[$cat] }}">{{ ucfirst($cat) }}</span>
            <h3>{{ $name }}</h3>
            <p>{{ $desc }}</p>
            <a class="product__link" href="{{ $wa("Hi Star Optical! I'm interested in the {$name} ".($cat === 'sunglasses' ? 'sunglasses' : 'frames').'.') }}" target="_blank" rel="noopener">Ask about this style →</a>
          </div>
        </article>
        @endforeach
      </div>
      <p class="center muted mt-3">Don't see what you're looking for? <a href="{{ $wa('Hi Star Optical! Do you have any other frame styles available?') }}" target="_blank" rel="noopener">Message us on WhatsApp</a>. We stock many more styles in-store.</p>
    </div>
  </section>

  {{-- ===== Promotions (managed in admin) ===== --}}
  @if($promotions->isNotEmpty())
  <section class="section section--dark" id="promotions">
    <div class="container">
      <div class="section-head section-head--light">
        <span class="eyebrow eyebrow--gold">Latest promotions</span>
        <h2>View our promotions</h2>
        <p>Fresh deals every month. Message us on WhatsApp to claim any offer.</p>
      </div>
      <div class="carousel" id="promoCarousel">
        <button class="carousel__btn carousel__btn--prev" type="button" aria-label="Previous promotion"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
        <div class="carousel__track" id="promoTrack">
          @foreach($promotions as $promo)
          <article class="poster poster--{{ $promo->theme }}">
            <img class="poster__img" src="{{ $promo->image_url ?? asset('assets/images/photo-'.str_pad((($loop->index % 6) * 3 + 1), 2, '0', STR_PAD_LEFT).'-800.webp') }}" alt="{{ $promo->title }} promotion at Star Optical, Cunupia" width="800" height="1000" loading="lazy" />
            <div class="poster__body">
              @if($promo->kicker)<span class="poster__kicker">{{ $promo->kicker }}</span>@endif
              <h3>{{ $promo->title }}</h3>
              @if($promo->body)<p>{{ $promo->body }}</p>@endif
              <a class="btn btn--gold btn--sm" href="{{ $wa($promo->whatsappMessage()) }}" target="_blank" rel="noopener">{{ $promo->cta_text }}</a>
            </div>
            <img class="poster__logo" src="{{ asset('assets/images/logo-400.png') }}" alt="Star Optical logo" width="400" height="170" loading="lazy" />
          </article>
          @endforeach
        </div>
        <button class="carousel__btn carousel__btn--next" type="button" aria-label="Next promotion"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
      </div>
      <div class="carousel__dots" id="promoDots" role="tablist" aria-label="Promotion slides"></div>
      <div class="promo-banner">
        <div><strong>Free eye examinations, all year round.</strong><span>Not a promotion. It's how we do business.</span></div>
        <a class="btn btn--wa btn--sm" href="{{ $bookExam }}" target="_blank" rel="noopener">Book now</a>
      </div>
    </div>
  </section>
  @endif

  {{-- ===== About / why us ===== --}}
  <section class="section" id="about">
    <div class="container split split--reverse">
      <div class="split__media"><img class="rounded" src="{{ asset('assets/images/photo-06-800.webp') }}" alt="Two customers wearing designer eyeglass frames from Star Optical, Cunupia" width="800" height="1200" loading="lazy" /></div>
      <div class="split__text">
        <span class="eyebrow">About {{ $settings['business_name'] }}</span>
        <h2>Affordable eyewear, <span class="txt-blue">quality eye care</span></h2>
        <p>{{ $settings['business_name'] }} is a customer-focused optical business in Cunupia, Trinidad and Tobago. We understand how important good vision is to everyday life, so we make quality eyewear accessible while giving friendly, personalised service to every customer.</p>
        <p>Whether you're looking for stylish frames, comfortable everyday glasses or a new pair of sunglasses, our team is here to help you find eyewear that suits your needs and budget.</p>
        <div class="mv">
          <div class="mv__card"><strong>Our mission</strong><p>To provide affordable eyewear and quality eye care while delivering excellent customer service and helping our customers maintain healthy vision.</p></div>
          <div class="mv__card"><strong>Our vision</strong><p>To become a trusted optical provider in Trinidad and Tobago, recognised for affordable eyewear, quality service and a commitment to customer satisfaction.</p></div>
        </div>
      </div>
    </div>
    <div class="container why">
      <div class="section-head">
        <span class="eyebrow">Why choose Star Optical?</span>
        <h2>Our customers are at the heart of everything we do</h2>
      </div>
      <div class="features features--5">
        @foreach([
          ['fa-solid fa-tags', 'Affordable prices', 'We believe everyone should have access to affordable eyewear.'],
          ['fa-solid fa-glasses', 'Quality products', "A selection of frames and lenses to meet our customers' needs."],
          ['fa-solid fa-handshake', 'Personalised service', 'We take the time to find eyewear that suits your style, comfort and budget.'],
          ['fa-brands fa-whatsapp', 'Convenience', 'Arrange appointments and ask about products on WhatsApp.'],
          ['fa-solid fa-users', 'Community focus', 'Proud to serve Cunupia and build lasting relationships with our customers.'],
        ] as [$icon, $title, $text])
        <div class="feature">
          <span class="feature__num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}.</span>
          <span class="feature__icon feature__icon--fa"><i class="{{ $icon }}" aria-hidden="true"></i></span>
          <div><strong>{{ $title }}</strong><p>{{ $text }}</p></div>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ===== Testimonials ===== --}}
  <section class="section section--soft">
    <div class="container">
      <div class="section-head"><span class="eyebrow">Happy customers</span><h2>What Cunupia is saying</h2></div>
      <div class="grid grid--3">
        @foreach([
          ['Booked on WhatsApp in the morning, had my free exam that afternoon and picked up my glasses within the week. Couldn\'t be easier.', 'Alicia R.', 'Cunupia'],
          ['They were so patient with my son. They let him try on every frame in the store until he found the one he loved.', 'Marcus D.', 'Chaguanas'],
          ['Great selection, honest advice and the prices are really fair. My whole family goes to Star Optical now.', 'Priya S.', 'Caroni'],
        ] as [$quote, $name, $town])
        <blockquote class="testimonial">
          <div class="stars">★★★★★</div>
          <p>"{{ $quote }}"</p>
          <footer><strong>{{ $name }}</strong><span>{{ $town }}</span></footer>
        </blockquote>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ===== FAQ (also emitted as FAQPage structured data for Google) ===== --}}
  @php
    $faqs = [
      ['Do you offer free eye exams in Trinidad?', 'Yes. Star Optical provides free eye examinations at our store in Cunupia for adults, seniors and children, with no purchase required. Book on WhatsApp or walk in during opening hours.'],
      ['Where is Star Optical located?', 'We are at #267 Southern Main Road, Cunupia, Trinidad and Tobago, a short drive from Chaguanas, Caroni, Couva and central Trinidad. Free parking is nearby.'],
      ['How do I book an appointment?', 'Message us on WhatsApp at (868) 380-4144 with your name and preferred day, or use the booking form on this page and we will confirm your time.'],
      ['Do you sell prescription sunglasses and polarised sunglasses?', 'Yes. We make sunglasses to your prescription and stock polarised UV400 sunglasses for driving and the beach, for men, women and kids.'],
      ['How much do glasses cost at Star Optical?', 'We keep eyewear affordable with frames at a range of prices and regular promotions on complete glasses packages. Message us for this month\'s offers.'],
      ['Do you have frames for children?', 'Yes. We stock flexible, durable kids frames and offer free eye tests for school-age children.'],
    ];
  @endphp
  <section class="section section--soft" id="faq">
    <div class="container">
      <div class="section-head"><span class="eyebrow">Common questions</span><h2>Eye care in Trinidad: your questions answered</h2></div>
      <div class="faq">
        @foreach($faqs as [$q, $a])
          <details @if($loop->first) open @endif><summary>{{ $q }}</summary><p>{{ $a }}</p></details>
        @endforeach
      </div>
      <div class="related mt-3">
        <a href="{{ route('page.eye-exams') }}">Free eye exam in Cunupia<small>What's included and who should be tested.</small></a>
        <a href="{{ route('page.eyeglasses') }}">Eyeglasses &amp; frames in Trinidad<small>Frames, lenses and complete packages.</small></a>
        <a href="{{ route('page.sunglasses') }}">Prescription &amp; polarised sunglasses<small>UV400 protection made to your script.</small></a>
      </div>
    </div>
  </section>
  <x-slot name="structuredData">
    <script type="application/ld+json" nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">{!! json_encode(['@'.'context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array_map(fn ($qa) => ['@type' => 'Question', 'name' => $qa[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $qa[1]]], $faqs)], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
  </x-slot>

  {{-- ===== CTA band ===== --}}
  <section class="cta-band">
    <img class="cta-band__bg" src="{{ asset('assets/images/photo-14.webp') }}" alt="Close-up of a customer wearing prescription glasses from Star Optical" width="933" height="1400" loading="lazy" />
    <div class="container cta-band__content">
      <h2>Don't wait any longer.<br />Book your free eye exam today.</h2>
      <a class="btn btn--wa btn--lg" href="{{ $bookExam }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Book now on WhatsApp</a>
      <p>Or call us at <a href="tel:{{ preg_replace('/\D+/', '', $settings['phone']) }}">{{ $settings['phone'] }}</a></p>
    </div>
  </section>

  {{-- ===== Location ===== --}}
  <section class="section" id="location">
    <div class="container">
      <div class="section-head"><span class="eyebrow">Find us</span><h2>Visit us in Cunupia</h2></div>
      <div class="location">
        <div class="location__map">
          <iframe title="{{ $settings['business_name'] }} location map" src="https://www.google.com/maps?q={{ urlencode($settings['address']) }}&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
        </div>
        <div class="location__card">
          <img src="{{ asset('assets/images/logo-400.png') }}" alt="Star Optical logo" class="location__logo" width="400" height="170" loading="lazy" />
          <h3>{{ $settings['business_name'] }}</h3>
          <address>{{ $settings['address'] }}<br /><a href="tel:{{ preg_replace('/\D+/', '', $settings['phone']) }}">{{ $settings['phone'] }}</a></address>
          <dl class="hours">
            <div><dt>Monday – Friday</dt><dd>{{ preg_replace('/^Mon[–-]Fri\s*/i', '', $settings['hours_weekdays']) }}</dd></div>
            <div><dt>Saturday</dt><dd>{{ preg_replace('/^Sat\s*/i', '', $settings['hours_saturday']) }}</dd></div>
            <div><dt>Sunday</dt><dd>{{ $settings['hours_sunday'] }}</dd></div>
          </dl>
          <div class="btn-row">
            <a class="btn btn--primary btn--sm" href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($settings['address']) }}" target="_blank" rel="noopener">Get directions</a>
            <a class="btn btn--outline btn--sm" href="tel:{{ preg_replace('/\D+/', '', $settings['phone']) }}">Call us</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ===== Contact + booking form ===== --}}
  <section class="section section--blue" id="contact">
    <div class="container contact">
      <div class="contact__info">
        <span class="eyebrow eyebrow--gold">Contact us</span>
        <h2>Let's get you seeing better</h2>
        <p>Message, call or visit, whatever's easiest. WhatsApp is the fastest way to reach us.</p>
        <ul class="contact__list">
          <li><span class="contact__icon contact__icon--wa"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></span><div><small>WhatsApp appointments</small><a href="{{ $wa('Hi Star Optical!') }}" target="_blank" rel="noopener">{{ preg_replace('/^1?(868)(\d{3})(\d{4})$/', '($1) $2-$3', Setting::whatsappNumber()) }}</a></div></li>
          <li><span class="contact__icon"><i class="fa-solid fa-phone" aria-hidden="true"></i></span><div><small>Telephone</small><a href="tel:{{ preg_replace('/\D+/', '', $settings['phone']) }}">{{ $settings['phone'] }}</a></div></li>
          <li><span class="contact__icon"><i class="fa-solid fa-envelope" aria-hidden="true"></i></span><div><small>Email</small><a href="mailto:{{ $settings['email'] }}">{{ $settings['email'] }}</a></div></li>
          <li><span class="contact__icon"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span><div><small>Address</small><a href="#location">{{ $settings['address'] }}</a></div></li>
        </ul>
        <div class="socials">
          @if($settings['facebook_url'])<a href="{{ $settings['facebook_url'] }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a>@endif
          @if($settings['instagram_url'])<a href="{{ $settings['instagram_url'] }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a>@endif
          @if($settings['tiktok_url'])<a href="{{ $settings['tiktok_url'] }}" target="_blank" rel="noopener" aria-label="TikTok"><i class="fa-brands fa-tiktok" aria-hidden="true"></i></a>@endif
        </div>
      </div>

      <div class="contact__form">
        <h3>Book an appointment</h3>
        <p class="muted">Fill this in and we'll save your request and open WhatsApp with your details ready to send.</p>
        @if($errors->any())
          <div class="form-alert" role="alert">
            <strong>Please check the form:</strong>
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
          </div>
        @endif
        <form id="bookingForm" method="POST" action="{{ route('booking.store') }}" novalidate>
          @csrf
          <input type="hidden" name="form_token" value="{{ \Illuminate\Support\Facades\Crypt::encryptString((string) time()) }}" />
          {{-- Honeypot: hidden from people, tempting to bots --}}
          <div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off" /></label></div>

          <div class="form-row">
            <label>Your name <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Alicia Ramnath" required maxlength="150" autocomplete="name" /></label>
            <label>Phone <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="(868) 000-0000" required maxlength="30" autocomplete="tel" /></label>
          </div>
          <label>Email <span class="muted">(optional)</span> <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" maxlength="255" autocomplete="email" /></label>
          <label>What do you need?
            <select name="service" required>
              @foreach($services as $service)<option value="{{ $service }}" @selected(old('service') === $service)>{{ $service }}</option>@endforeach
            </select>
          </label>
          <div class="form-row">
            <label>Preferred day <input type="date" name="preferred_date" value="{{ old('preferred_date') }}" min="{{ date('Y-m-d') }}" /></label>
            <label>Preferred time
              <select name="preferred_time">
                @foreach($times as $time)<option value="{{ $time }}" @selected(old('preferred_time') === $time)>{{ $time }}</option>@endforeach
              </select>
            </label>
          </div>
          <label>Anything else? <textarea name="notes" rows="3" maxlength="1000" placeholder="Optional. Tell us about your prescription, a promotion you'd like, etc.">{{ old('notes') }}</textarea></label>
          <button type="submit" class="btn btn--wa btn--block"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Book &amp; continue on WhatsApp</button>
          <p class="form-note muted">We only use your details to arrange your appointment.</p>
        </form>
      </div>
    </div>
  </section>

  @if($errors->any())
  <x-slot name="scripts">
    <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">document.getElementById('contact').scrollIntoView();</script>
  </x-slot>
  @endif
</x-public-layout>
