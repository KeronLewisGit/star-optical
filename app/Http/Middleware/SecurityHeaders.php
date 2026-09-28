<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds defensive HTTP headers to every response, including a strict
 * Content-Security-Policy with a per-request nonce for inline scripts.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = Vite::cspNonce();

        /** @var Response $response */
        $response = $next($request);

        $csp = [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "form-action 'self' https://wa.me",
            // Scripts: our own bundles + Google Analytics / Tag Manager, all nonce-gated for inline.
            "script-src 'self' 'nonce-{$nonce}' https://www.googletagmanager.com https://www.google-analytics.com https://cdnjs.cloudflare.com",
            // Styles: Tailwind bundle, Google Fonts, Font Awesome from cdnjs. Inline styles are needed by Blade
            // components and by the promotion poster backgrounds.
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com",
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:",
            "img-src 'self' data: blob: https://www.google-analytics.com https://www.googletagmanager.com https://*.google.com https://*.gstatic.com https://*.googleapis.com",
            "connect-src 'self' https://www.google-analytics.com https://analytics.google.com https://*.google-analytics.com https://www.googletagmanager.com https://stats.g.doubleclick.net",
            // Google Maps embed on the location section.
            "frame-src https://www.google.com https://maps.google.com https://www.googletagmanager.com",
            'upgrade-insecure-requests',
        ];

        $response->headers->set('Content-Security-Policy', implode('; ', $csp));
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        if ($request->is('admin*') || $request->is('login') || $request->is('two-factor*') || $request->is('forgot-password*') || $request->is('reset-password*')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
            $response->headers->set('Cache-Control', 'no-store, private');
        }

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
