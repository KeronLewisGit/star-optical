<?php

namespace Database\Seeders;

use App\Models\Promotion;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Persist the defaults so administrators can see and edit every setting.
        foreach (Setting::DEFAULTS as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        if (Promotion::count() === 0) {
            $promotions = [
                ['title' => 'Buy 1 frame, get the 2nd 50% off', 'kicker' => 'Limited time', 'theme' => 'blue', 'body' => 'Mix and match a second pair for work, driving or the beach.', 'sort_order' => 1],
                ['title' => 'Back-to-school kids package', 'kicker' => 'Back to school', 'theme' => 'gold', 'body' => 'Free eye exam + durable kids frame + scratch-resistant lenses.', 'sort_order' => 2],
                ['title' => '20% off all sunglasses', 'kicker' => 'Sunglasses', 'theme' => 'dark', 'body' => 'UV400 protection on every pair. Polarised options available.', 'sort_order' => 3],
                ['title' => 'Free eye examinations', 'kicker' => 'Always on', 'theme' => 'navy', 'body' => 'No purchase required. Book on WhatsApp in seconds.', 'sort_order' => 4],
                ['title' => 'Frame + lenses bundle deals', 'kicker' => 'Complete packages', 'theme' => 'teal', 'body' => 'Ask us about complete eyeglass packages to suit your budget.', 'sort_order' => 5],
            ];

            foreach ($promotions as $p) {
                Promotion::create($p + ['is_active' => true, 'cta_text' => 'Claim on WhatsApp']);
            }
        }
    }
}
