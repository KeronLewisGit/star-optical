<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_has_seo_metadata_and_structured_data(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('<title>Optician in Cunupia, Trinidad | Free Eye Exams | Star Optical</title>', false)
            ->assertSee('"@context":"https://schema.org"', false)
            ->assertDontSee('__contextArgs')
            ->assertDontSee('<meta name="robots"', false)
            ->assertSee('<link rel="canonical" href="', false)
            ->assertSee('property="og:title"', false)
            ->assertSee('"@type":"Optician"', false)
            ->assertSee('"@type":"FAQPage"', false)
            ->assertDontSee('"sameAs":[]', false)
            ->assertSee('<meta property="og:image" content="'.asset('assets/images/photo-07.jpg').'"', false)
            ->assertSee('<link rel="icon" href="'.asset('favicon.ico').'"', false)
            ->assertSee('<link rel="apple-touch-icon"', false)
            ->assertDontSee('role="tablist"', false)
            ->assertSee('Cunupia, Trinidad')
            ->assertSee('Designed and Developed by')
            ->assertSee('https://linkedin.com/in/keronlewis', false)
            ->assertDontSee('Website mockup');
    }

    public function test_landing_pages_render_with_unique_titles(): void
    {
        $this->get('/free-eye-exam-cunupia-trinidad')->assertOk()
            ->assertSee('<title>Free Eye Exam in Cunupia, Trinidad', false)
            ->assertSee('"@type":"BreadcrumbList"', false)
            ->assertSee('Is the eye exam really free in Trinidad?');

        // Landing pages use their own JPEG for social previews and declare true image dimensions.
        $this->get('/eyeglasses-frames-trinidad')->assertOk()
            ->assertSee('Affordable Eyeglasses &amp; Frames in Trinidad', false)
            ->assertSee('og:image" content="'.asset('assets/images/photo-05.jpg').'"', false)
            ->assertSee('src="'.asset('assets/images/photo-05.webp').'" alt="', false)
            ->assertSee('width="933" height="1400"', false);
        $this->get('/sunglasses-trinidad')->assertOk()->assertSee('Polarised Sunglasses in Trinidad');
    }

    public function test_sitemap_lists_public_pages_only(): void
    {
        $this->get('/sitemap.xml')->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<loc>'.route('home').'</loc>', false)
            ->assertSee('/free-eye-exam-cunupia-trinidad', false)
            ->assertDontSee('/admin');
        $this->assertMatchesRegularExpression('/<lastmod>\d{4}-\d{2}-\d{2}<\/lastmod>/', $this->get('/sitemap.xml')->getContent());
    }

    public function test_thank_you_and_admin_pages_are_not_indexable(): void
    {
        $this->get('/login')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->get('/robots.txt')->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /admin')
            ->assertSee('Sitemap: '.route('sitemap'));
    }
}
