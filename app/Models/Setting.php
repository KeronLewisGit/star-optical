<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    use LogsActivity;

    public const CACHE_KEY = 'settings.all';

    /** Google shows roughly 60 title and 135-155 description characters before truncating. */
    public const SEO_TITLE_MAX = 60;

    public const SEO_DESCRIPTION_MAX = 135;

    /** Default values used until an administrator saves their own. */
    public const DEFAULTS = [
        'business_name' => 'Star Optical Company Ltd.',
        'tagline' => 'Affordable Eyewear, Quality Eye Care',
        'address' => '#267 Southern Main Road, Cunupia, Trinidad and Tobago',
        'phone' => '(868) 693-2932',
        'whatsapp_number' => '18683804144',
        'email' => 'staropticaltt@yahoo.com',
        'hours_weekdays' => 'Mon–Fri 9:00am – 5:00pm',
        'hours_saturday' => 'Sat 9:00am – 2:00pm',
        'hours_sunday' => 'Closed',
        'facebook_url' => '',
        'instagram_url' => '',
        'tiktok_url' => '',
        'notification_email' => '',
        'ga_measurement_id' => '',
        'gtm_container_id' => '',
        'google_site_verification' => '',
        'require_two_factor' => '0',
        'seo_title' => 'Cunupia, Trinidad Optician | Free Eye Exams | Star Optical',
        'seo_description' => 'Free eye exams, prescription glasses and sunglasses in Cunupia, Trinidad. Affordable optician near Chaguanas. Book on WhatsApp.',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /** @return array<string, string> */
    public static function all_cached(): array
    {
        $stored = Cache::rememberForever(self::CACHE_KEY, fn () => static::query()->pluck('value', 'key')->all());

        return array_merge(self::DEFAULTS, array_filter($stored, fn ($v) => $v !== null));
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        return self::all_cached()[$key] ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /** Digits-only WhatsApp number in international format, e.g. 18683804144. */
    public static function whatsappNumber(): string
    {
        return preg_replace('/\D+/', '', (string) self::get('whatsapp_number')) ?: '';
    }

    public static function whatsappLink(string $message = ''): string
    {
        $url = 'https://wa.me/'.self::whatsappNumber();

        return $message === '' ? $url : $url.'?text='.rawurlencode($message);
    }
}
