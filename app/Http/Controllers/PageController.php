<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\Setting;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * Keyword-focused landing pages. Each targets one cluster of Trinidad eyewear
 * searches so Google can rank a dedicated URL for it, and the XML sitemap.
 */
class PageController extends Controller
{
    public static function pages(): array
    {
        return [
            'eye-exams' => [
                'route' => 'page.eye-exams',
                'title' => 'Free Eye Exam in Cunupia, Trinidad | Eye Test Near Chaguanas | Star Optical',
                'description' => 'Book a free eye examination at Star Optical, #267 Southern Main Road, Cunupia. Comprehensive eye tests for adults, seniors and children in Trinidad, no purchase required. Book on WhatsApp.',
                'h1' => 'Free Eye Exam in Cunupia, Trinidad',
                'lead' => 'Looking for an eye test near Chaguanas, Cunupia or Caroni? Star Optical offers comprehensive eye examinations at no cost, with friendly staff who take the time to explain your results.',
                'image' => 'photo-10.jpg',
                'image_alt' => 'Optician checking a customer\'s eyeglasses during a free eye exam in Cunupia, Trinidad',
                'sections' => [
                    ['What is included in your free eye examination?', 'Every free eye test at our Cunupia optical store includes a vision assessment for distance and reading, a prescription check or update, and advice on the lenses and frames that suit your lifestyle. If you already wear glasses in Trinidad, bring them along so we can compare.'],
                    ['Eye tests for the whole family', 'We examine children, students, working adults and seniors. Regular eye exams catch changes in your vision early, help with headaches and eye strain from screens, and make sure kids can see the board clearly at school.'],
                    ['Do you need an appointment?', 'Walk-ins are welcome, but booking on WhatsApp guarantees your time. Message us with your preferred day and we will confirm within business hours.'],
                ],
                'bullets' => ['No purchase required', 'Adults, seniors and children', 'Prescription checks and updates', 'Same-week appointments via WhatsApp'],
                'faq' => [
                    ['Is the eye exam really free in Trinidad?', 'Yes. Star Optical provides free eye examinations at our Cunupia store for every customer, with no obligation to buy glasses.'],
                    ['How long does a free eye test take?', 'Most examinations take 20 to 30 minutes including frame advice.'],
                    ['Where can I get an eye test near Chaguanas?', 'Star Optical is at #267 Southern Main Road, Cunupia, a few minutes from Chaguanas, Caroni and Couva.'],
                    ['How often should I have my eyes tested?', 'Every one to two years for most adults, and yearly for children, seniors, and anyone with diabetes or a family history of eye disease.'],
                ],
                'cta' => "Hi Star Optical! I'd like to book a free eye examination.",
            ],
            'eyeglasses' => [
                'route' => 'page.eyeglasses',
                'title' => 'Eyeglasses & Frames in Trinidad | Affordable Prescription Glasses | Star Optical Cunupia',
                'description' => 'Affordable prescription eyeglasses and stylish frames for men, women and children in Trinidad. Visit Star Optical in Cunupia for designer and budget frames with lenses made to your prescription.',
                'h1' => 'Affordable Eyeglasses & Frames in Trinidad',
                'lead' => 'From everyday prescription glasses to designer frames, Star Optical in Cunupia stocks hundreds of styles at prices that suit every budget, with lenses made to your prescription.',
                'image' => 'photo-05.jpg',
                'image_alt' => 'Woman trying on stylish prescription eyeglasses at Star Optical in Cunupia, Trinidad',
                'sections' => [
                    ['Frames for men, women and children', 'Choose from acetate, metal, semi-rimless and flexible kids frames. Our team helps you find a shape that fits your face and your daily routine, whether you need glasses for work, driving or school.'],
                    ['Lenses made to your prescription', 'Single-vision, bifocal and progressive lenses, with options for anti-glare, scratch-resistant and blue-light coatings for screen use. Most prescription glasses are ready within days.'],
                    ['Complete glasses packages in Cunupia', 'Ask about our frame-and-lens bundles and monthly promotions to get complete prescription glasses at an affordable price in Trinidad.'],
                ],
                'bullets' => ['Designer and budget-friendly frames', 'Single-vision, bifocal and progressive lenses', 'Blue-light and anti-glare coatings', 'Frame repairs and adjustments'],
                'faq' => [
                    ['How much do prescription glasses cost in Trinidad?', 'Prices depend on the frame and lens type. Star Optical keeps eyewear affordable and runs regular promotions, so message us on WhatsApp for current package prices.'],
                    ['Can I use my existing prescription?', 'Yes. Bring a recent prescription, or have a free eye exam with us and we will make your lenses from it.'],
                    ['Do you sell children\'s glasses?', 'Yes. We stock durable, flexible frames made for kids and students.'],
                    ['How long until my glasses are ready?', 'Most orders are ready within a few days; we will message you on WhatsApp when they are.'],
                ],
                'cta' => "Hi Star Optical! I'm looking for prescription eyeglasses.",
            ],
            'sunglasses' => [
                'route' => 'page.sunglasses',
                'title' => 'Sunglasses in Trinidad | Prescription & Polarised Sunglasses | Star Optical Cunupia',
                'description' => 'Prescription sunglasses and polarised sunglasses in Trinidad with UV400 protection. Visit Star Optical in Cunupia for sunglasses made to your prescription, ideal for driving and the beach.',
                'h1' => 'Prescription & Polarised Sunglasses in Trinidad',
                'lead' => 'Protect your eyes from the Caribbean sun without giving up clear vision. Star Optical in Cunupia fits sunglasses to your prescription and stocks polarised lenses that cut glare on the road and at the beach.',
                'image' => 'photo-17.jpg',
                'image_alt' => 'Man wearing aviator sunglasses from Star Optical, Cunupia, Trinidad',
                'sections' => [
                    ['Prescription sunglasses', 'Any of our frames can be fitted with tinted prescription lenses, so you see clearly outdoors without carrying two pairs of glasses.'],
                    ['Polarised sunglasses for driving and the beach', 'Polarised lenses remove reflected glare from roads, water and car bonnets, reducing eye strain on long drives and sunny days in Trinidad and Tobago.'],
                    ['UV protection for the whole family', 'All our sunglasses block UVA and UVB rays. Long-term sun exposure contributes to cataracts, so sunglasses are an investment in your eye health.'],
                ],
                'bullets' => ['UV400 protection on every pair', 'Polarised lens options', 'Made to your prescription', 'Styles for men, women and kids'],
                'faq' => [
                    ['Can I get sunglasses in my prescription in Trinidad?', 'Yes. Star Optical makes prescription sunglasses in Cunupia, including single-vision and progressive lenses.'],
                    ['What is the difference between polarised and tinted sunglasses?', 'Tinted lenses reduce brightness; polarised lenses also remove reflected glare, which is better for driving and being near water.'],
                    ['Do you run sunglasses promotions?', 'Yes. Check the promotions on our home page or message us on WhatsApp for this month\'s sunglasses offers.'],
                ],
                'cta' => "Hi Star Optical! I'm interested in sunglasses.",
            ],
        ];
    }

    public function show(string $slug): View
    {
        $page = self::pages()[$slug] ?? abort(404);

        return view('public.service', [
            'settings' => Setting::all_cached(),
            'page' => $page,
            'slug' => $slug,
            'promotions' => Promotion::live()->limit(3)->get(),
            'related' => collect(self::pages())->except($slug),
        ]);
    }

    public function sitemap(): Response
    {
        $urls = [['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'weekly']];
        foreach (self::pages() as $page) {
            $urls[] = ['loc' => route($page['route']), 'priority' => '0.8', 'changefreq' => 'monthly'];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $u) {
            $xml .= "  <url><loc>{$u['loc']}</loc><changefreq>{$u['changefreq']}</changefreq><priority>{$u['priority']}</priority></url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
