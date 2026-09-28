<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Replace over-long home page title/description saved by an administrator with the
 * defaults, so Google stops truncating them. Values already within limits are kept.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['seo_title' => Setting::SEO_TITLE_MAX, 'seo_description' => Setting::SEO_DESCRIPTION_MAX] as $key => $max) {
            DB::table('settings')
                ->where('key', $key)
                ->whereRaw('LENGTH(value) > ?', [$max])
                ->update(['value' => Setting::DEFAULTS[$key], 'updated_at' => now()]);
        }

        Cache::forget(Setting::CACHE_KEY);
    }

    public function down(): void
    {
        // Nothing to restore: the previous values were intentionally discarded.
    }
};
