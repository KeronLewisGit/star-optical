<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', ['settings' => Setting::all_cached()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:150'],
            'tagline' => ['nullable', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp_number' => ['required', 'string', 'regex:/^\+?[\d\s\-\(\)]{10,20}$/'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'hours_weekdays' => ['nullable', 'string', 'max:100'],
            'hours_saturday' => ['nullable', 'string', 'max:100'],
            'hours_sunday' => ['nullable', 'string', 'max:100'],
            'facebook_url' => ['nullable', 'url:https', 'max:255'],
            'instagram_url' => ['nullable', 'url:https', 'max:255'],
            'tiktok_url' => ['nullable', 'url:https', 'max:255'],
            'notification_email' => ['nullable', 'email:rfc', 'max:255'],
            // Google tracking. Strict formats so nothing but a real ID can be injected into the page.
            'ga_measurement_id' => ['nullable', 'string', 'regex:/^G-[A-Z0-9]{4,15}$/'],
            'gtm_container_id' => ['nullable', 'string', 'regex:/^GTM-[A-Z0-9]{4,12}$/'],
            'google_site_verification' => ['nullable', 'string', 'regex:/^[A-Za-z0-9_\-]{10,100}$/'],
            'require_two_factor' => ['nullable', 'boolean'],
        ], [
            'ga_measurement_id.regex' => 'The GA4 Measurement ID looks like G-XXXXXXXXXX.',
            'gtm_container_id.regex' => 'The Tag Manager container ID looks like GTM-XXXXXXX.',
        ]);

        $data['require_two_factor'] = $request->boolean('require_two_factor') ? '1' : '0';
        $data['whatsapp_number'] = preg_replace('/\D+/', '', $data['whatsapp_number']);

        foreach ($data as $key => $value) {
            Setting::set($key, $value === null ? '' : (string) $value);
        }

        return back()->with('success', 'Settings saved.');
    }
}
