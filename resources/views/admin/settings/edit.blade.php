<x-admin-layout title="Settings & tracking">
    <form method="POST" action="{{ route('admin.settings.update') }}" class="grid lg:grid-cols-2 gap-6" data-once>
        @csrf @method('PUT')

        <div class="card"><div class="card-body space-y-4">
            <h2 class="card-title">Business details</h2>
            <p class="text-sm text-gray-500">Shown in the website header, footer, location and contact sections.</p>
            <x-form.input name="business_name" label="Business name" :value="$settings['business_name']" required />
            <x-form.input name="tagline" label="Tagline" :value="$settings['tagline']" />
            <x-form.input name="address" label="Address" :value="$settings['address']" required />
            <div class="grid sm:grid-cols-2 gap-4">
                <x-form.input name="phone" label="Telephone (display)" :value="$settings['phone']" required />
                <x-form.input name="whatsapp_number" label="WhatsApp number" :value="$settings['whatsapp_number']" required help="International format, digits only: 1868XXXXXXX" />
                <x-form.input name="email" label="Public email" type="email" :value="$settings['email']" required />
                <x-form.input name="notification_email" label="Send new-booking alerts to" type="email" :value="$settings['notification_email']" help="Leave blank to disable email alerts." />
            </div>
            <div class="grid sm:grid-cols-3 gap-4">
                <x-form.input name="hours_weekdays" label="Weekday hours" :value="$settings['hours_weekdays']" />
                <x-form.input name="hours_saturday" label="Saturday" :value="$settings['hours_saturday']" />
                <x-form.input name="hours_sunday" label="Sunday" :value="$settings['hours_sunday']" />
            </div>
            <div class="grid sm:grid-cols-3 gap-4">
                <x-form.input name="facebook_url" label="Facebook URL" :value="$settings['facebook_url']" placeholder="https://" />
                <x-form.input name="instagram_url" label="Instagram URL" :value="$settings['instagram_url']" placeholder="https://" />
                <x-form.input name="tiktok_url" label="TikTok URL" :value="$settings['tiktok_url']" placeholder="https://" />
            </div>
        </div></div>

        <div class="space-y-6">
            <div class="card"><div class="card-body space-y-4">
                <h2 class="card-title"><i class="fa-brands fa-google text-gold"></i> Google tracking</h2>
                <p class="text-sm text-gray-500">Paste the IDs from your Google accounts. The tracking code is only added to the public website when an ID is present. Bookings are reported as a <code class="text-xs">generate_lead</code> event.</p>
                <x-form.input name="ga_measurement_id" label="Google Analytics 4 Measurement ID" :value="$settings['ga_measurement_id']" placeholder="G-XXXXXXXXXX" help="Google Analytics → Admin → Data streams → Web stream." />
                <x-form.input name="gtm_container_id" label="Google Tag Manager container ID (optional)" :value="$settings['gtm_container_id']" placeholder="GTM-XXXXXXX" help="Use this instead of, or as well as, GA4 if you manage tags in Tag Manager." />
                <x-form.input name="google_site_verification" label="Search Console verification token (optional)" :value="$settings['google_site_verification']" help="From the HTML-tag verification method in Google Search Console." />
            </div></div>

            <div class="card"><div class="card-body space-y-4">
                <h2 class="card-title"><i class="fa-solid fa-magnifying-glass text-gold"></i> Search engine (SEO)</h2>
                <x-form.input name="seo_title" label="Home page title" :value="$settings['seo_title']" required :maxlength="\App\Models\Setting::SEO_TITLE_MAX" help="The blue link text in Google results. Maximum 60 characters or Google cuts it off; lead with what people search for (optician, eyeglasses, Cunupia, Trinidad)." />
                <x-form.textarea name="seo_description" label="Home page description" :value="$settings['seo_description']" rows="3" required :maxlength="\App\Models\Setting::SEO_DESCRIPTION_MAX" help="The grey text under the link in Google. Maximum 135 characters; put the main service and location first." />
            </div></div>

            <div class="card"><div class="card-body space-y-4">
                <h2 class="card-title"><i class="fa-solid fa-shield-halved text-gold"></i> Security policy</h2>
                <label class="flex items-start gap-3 text-sm"><input type="hidden" name="require_two_factor" value="0"><input type="checkbox" name="require_two_factor" value="1" class="mt-0.5 rounded border-gray-300 text-brand" @checked(old('require_two_factor', $settings['require_two_factor']) === '1')><span><strong>Require two-factor authentication for all staff</strong><br><span class="text-gray-500">Staff without 2FA will be asked to set it up before they can use the admin.</span></span></label>
            </div></div>

            <button class="btn-primary">Save settings</button>
        </div>
    </form>
</x-admin-layout>
