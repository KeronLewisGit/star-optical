/* =========================================================
   STAR OPTICAL — Mockup scripts
   Everything here is optional polish. In WordPress/Elementor:
   - WhatsApp links  -> plain links to https://wa.me/<number>?text=...
   - Mobile nav      -> Elementor Nav Menu widget (built-in)
   - Filters         -> Elementor Pro Posts/Loop Grid with taxonomy filter,
                        or Essential Addons "Filterable Gallery"
   - Booking form    -> Elementor Pro Form + "Redirect" action, or the
                        small snippet in README.md
   ========================================================= */

(function () {
  'use strict';

  /* ---- 1. WhatsApp number (ONE place to change) ---- */
  // Full international number, digits only. Trinidad & Tobago = 1868 + 7 digits.
  var WHATSAPP_NUMBER = document.body.getAttribute('data-wa-number') || '18683804144'; // set from admin Settings

  function waLink(message) {
    var base = 'https://wa.me/' + WHATSAPP_NUMBER;
    return message ? base + '?text=' + encodeURIComponent(message) : base;
  }

  // Turn every element with data-wa into a real WhatsApp click-to-chat link
  document.querySelectorAll('[data-wa]').forEach(function (el) {
    el.setAttribute('href', waLink(el.getAttribute('data-wa-msg') || ''));
    el.setAttribute('target', '_blank');
    el.setAttribute('rel', 'noopener');
  });

  /* ---- 2. Mobile navigation ---- */
  var toggle = document.getElementById('navToggle');
  var nav = document.getElementById('nav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    });
    nav.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      });
    });
  }

  /* ---- 3. Sticky header shadow ---- */
  var header = document.getElementById('header');
  function onScroll() {
    header.classList.toggle('is-scrolled', window.scrollY > 10);
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ---- 4. Product filter ---- */
  var filterButtons = document.querySelectorAll('.filter');
  var products = document.querySelectorAll('.product');
  filterButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var cat = btn.getAttribute('data-filter');
      filterButtons.forEach(function (b) { b.classList.remove('is-active'); });
      btn.classList.add('is-active');
      products.forEach(function (p) {
        var show = cat === 'all' || p.getAttribute('data-cat') === cat;
        p.classList.toggle('is-hidden', !show);
      });
    });
  });

  /* ---- 5. Booking form: posts to the server (lead is saved), then continues on WhatsApp ---- */
  var form = document.getElementById('bookingForm');
  if (form) {
    form.addEventListener('submit', function () {
      var btn = form.querySelector('button[type="submit"]');
      if (btn) { btn.disabled = true; btn.textContent = 'Sending…'; }
    });
  }

  /* ---- 5b. Promotions carousel (Elementor: Loop Carousel widget) ---- */
  var track = document.getElementById('promoTrack');
  var dotsWrap = document.getElementById('promoDots');
  if (track && dotsWrap) {
    var slides = Array.prototype.slice.call(track.children);
    var prev = document.querySelector('.carousel__btn--prev');
    var next = document.querySelector('.carousel__btn--next');
    var timer;

    function slideWidth() { return slides[0].getBoundingClientRect().width + 20; }
    function visibleCount() { return Math.max(1, Math.round(track.clientWidth / slideWidth())); }
    function maxIndex() { return slides.length - visibleCount(); }        // last reachable position
    function currentIndex() { return Math.min(maxIndex(), Math.round(track.scrollLeft / slideWidth())); }
    function goTo(i) {
      var max = maxIndex();
      if (i < 0) i = max;
      if (i > max) i = 0;
      track.scrollTo({ left: i * slideWidth(), behavior: 'smooth' });
    }
    function buildDots() {
      dotsWrap.innerHTML = '';
      for (var i = 0; i <= maxIndex(); i++) {
        (function (k) {
          var d = document.createElement('button');
          d.type = 'button';
          d.setAttribute('aria-label', 'Go to promotion ' + (k + 1));
          d.addEventListener('click', function () { goTo(k); restart(); });
          dotsWrap.appendChild(d);
        })(i);
      }
      updateDots();
    }
    function updateDots() {
      var i = currentIndex();
      Array.prototype.forEach.call(dotsWrap.children, function (d, k) { d.classList.toggle('is-active', k === i); });
    }
    function restart() {
      clearInterval(timer);
      timer = setInterval(function () { goTo(currentIndex() + 1); }, 5000);
    }

    prev.addEventListener('click', function () { goTo(currentIndex() - 1); restart(); });
    next.addEventListener('click', function () { goTo(currentIndex() + 1); restart(); });
    track.addEventListener('scroll', updateDots, { passive: true });
    track.addEventListener('mouseenter', function () { clearInterval(timer); });
    track.addEventListener('mouseleave', restart);
    var resizeTimer;
    window.addEventListener('resize', function () { clearTimeout(resizeTimer); resizeTimer = setTimeout(buildDots, 150); });
    buildDots();
    restart();
  }

  /* ---- 6. Scroll reveal (Elementor: Motion Effects > Entrance animation) ---- */
  var revealTargets = document.querySelectorAll(
    '.section-head, .split__text, .split__media, .bigcard, .product, .service, .testimonial, .location__map, .location__card, .contact__info, .contact__form'
  );
  if ('IntersectionObserver' in window) {
    revealTargets.forEach(function (el) { el.classList.add('reveal'); });
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });
    revealTargets.forEach(function (el) { io.observe(el); });
  }

  /* ---- 7. Footer year ---- */
  var year = document.getElementById('year');
  if (year) year.textContent = new Date().getFullYear();
})();
