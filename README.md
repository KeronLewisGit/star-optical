# Star Optical – website + booking CRM (Laravel)

Public website for Star Optical Company Ltd. (Cunupia, Trinidad & Tobago) with a secured admin area that turns website bookings (leads) into patient records.

Built with Laravel 13, Blade, Tailwind, and plain JavaScript. No public registration; staff accounts are created by an administrator.

## Quick start (local)

```bash
composer install
npm install && npm run build
cp .env.example .env && php artisan key:generate
php artisan migrate --seed          # SQLite by default; see .env for MySQL
php artisan storage:link            # promotion images
php artisan app:create-admin        # first administrator account
php artisan serve                   # http://localhost:8000  (admin at /admin)
php artisan test                    # 26 feature tests
```

## What's inside

### Public site (`/`)
- Same design as the approved mockup: hero, services, eye-care cards, frames gallery, promotions carousel, about, testimonials, CTA band, map, contact.
- Contact details, hours, socials and the WhatsApp number come from **Admin → Settings**.
- Promotions come from **Admin → Promotions** (with optional poster image and start/end dates).
- Booking form posts to the server (`POST /book`), saves a lead, emails staff (optional), then sends the customer to a thank-you page with a pre-filled WhatsApp message.

### Admin / CRM (`/admin`)
| Area | What it does |
|---|---|
| Dashboard | New leads, monthly counts, patients, lead→patient conversion rate, upcoming appointments, requests by service |
| Bookings | Search/filter leads by status and date; edit status, appointment time, assignee, internal notes; **Convert to patient** (creates a new patient or links to an existing one matched by phone/email); per-record history |
| Patients | Full record: contact, DOB, gender, address, emergency contact, insurance, allergies + medical notes (encrypted), prescriptions (OD/OS sphere, cylinder, axis, add, PD), timeline notes, linked bookings, audit history |
| Promotions | Manage the website carousel |
| Staff accounts *(admin)* | Create/edit staff, roles (admin/staff), deactivate, reset 2FA |
| Settings & tracking *(admin)* | Business details, hours, socials, notification email, **Google Analytics 4 / Tag Manager / Search Console IDs**, require-2FA policy |
| Activity log *(admin)* | Every create/update/delete/login with user, IP and field-level diff |
| Profile & security | Name/email, password change (logs out other devices), TOTP two-factor with QR code and recovery codes |

### Google tracking
1. Admin → Settings & tracking → paste the **GA4 Measurement ID** (`G-…`) and/or **GTM container ID** (`GTM-…`).
2. The snippet is only injected on public pages when an ID is set. IDs are validated by format, so nothing else can be injected into the page.
3. Successful bookings fire a `generate_lead` event (and push `booking_submitted` to the GTM `dataLayer`), so you can set up a conversion in GA4 / Ads.
4. The Search Console field adds the `google-site-verification` meta tag.

## Security measures

**Access control**
- Public registration disabled; admin-created accounts only (`app:create-admin` or Admin → Staff).
- Roles: `admin` (everything) and `staff` (bookings, patients, promotions). Enforced by middleware and policies.
- Deactivated accounts cannot sign in and are logged out on their next request.
- Admins cannot demote or deactivate themselves (prevents lock-out).

**Authentication**
- Password policy: 12+ chars, mixed case, number, symbol; checked against known breaches in production.
- Login throttling (5 failed attempts/min per email+IP, 10 requests/min per IP), password reset throttling.
- Optional TOTP two-factor (Google/Microsoft Authenticator, Authy, 1Password) with 8 hashed one-time recovery codes. Secrets are encrypted at rest. Admin can require 2FA for all staff and reset a user's 2FA.
- Password change invalidates other sessions.

**Data protection**
- Encrypted at rest (Laravel `encrypted` casts): customer messages, internal notes, medical notes, allergies, prescription notes, patient timeline notes, 2FA secrets.
- Sessions encrypted; cookies `HttpOnly`, `SameSite=Lax`; set `SESSION_SECURE_COOKIE=true` in production.
- Soft deletes on bookings/patients (records are archived, never lost; only admins can delete).
- Audit log for every change; encrypted fields are logged as “[changed]”, never their value.
- Booking IPs are stored as a keyed hash, not raw.

**Application hardening**
- Strict `Content-Security-Policy` with per-request nonces (no `unsafe-eval`; Google Analytics/Tag Manager, Google Fonts, Font Awesome CDN and the Google Maps iframe are explicitly allowed), plus `X-Frame-Options: DENY`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`, `Cross-Origin-Opener-Policy`, and HSTS on HTTPS.
- Admin/login pages are `noindex` and `Cache-Control: no-store`; `robots.txt` disallows `/admin`.
- CSRF on every form; all input validated through Form Requests with whitelists (statuses, services, IDs).
- Mass-assignment protection with explicit fillable lists (`Model::preventSilentlyDiscardingAttributes` outside production).
- Public booking form: honeypot field, encrypted time-trap (rejects submissions under 3s), rate limit 5/min and 20/day per IP.
- Uploaded promotion images are validated by content (`image`, mimes, dimensions, 2 MB) and stored under random names.
- HTTPS forced in production; trusted-proxy headers configured for load balancers.

## Production checklist
- `APP_ENV=production`, `APP_DEBUG=false`, strong `APP_KEY` (never rotate without re-encrypting data).
- `SESSION_SECURE_COOKIE=true`, `APP_URL=https://…`.
- MySQL: set `DB_CONNECTION=mysql` and credentials; run `php artisan migrate --force --seed`.
- Mail: configure `MAIL_*` so booking alerts and password resets are delivered; set the notification email in Settings.
- Run `php artisan optimize` and `npm run build` on deploy; schedule regular DB backups (patient data).
- Turn on “Require two-factor for all staff” in Settings once staff have authenticator apps.

## Project layout
```
app/Http/Controllers/BookingController.php     public booking + thank-you
app/Http/Controllers/Admin/*                   CRM controllers
app/Http/Middleware/SecurityHeaders.php        CSP + security headers
app/Http/Middleware/EnsureTwoFactorAuthenticated.php
app/Services/TwoFactorService.php              TOTP + recovery codes + QR
app/Models/Concerns/LogsActivity.php           audit trail
resources/views/public/*                       website (uses public/assets from the mockup)
resources/views/admin/*                        admin UI (Tailwind)
tests/Feature/*                                booking, access control, CRM conversion, 2FA
```
