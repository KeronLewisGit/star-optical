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
            ->assertSee('<title>Optician in Cunupia, Trinidad | Eyeglasses, Sunglasses &amp; Free Eye Exams | Star Optical</title>', false)
            ->assertSee('<link rel="canonical" href="', false)
            ->assertSee('property="og:title"', false)
            ->assertSee('"@type":"Optician"', false)
            ->assertSee('"@type":"FAQPage"', false)
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

        $this->get('/eyeglasses-frames-trinidad')->assertOk()->assertSee('Affordable Eyeglasses &amp; Frames in Trinidad', false);
        $this->get('/sunglasses-trinidad')->assertOk()->assertSee('Polarised Sunglasses in Trinidad');
    }

    public function test_sitemap_lists_public_pages_only(): void
    {
        $this->get('/sitemap.xml')->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<loc>'.route('home').'</loc>', false)
            ->assertSee('/free-eye-exam-cunupia-trinidad', false)
            ->assertDontSee('/admin');
    }

    public function test_thank_you_and_admin_pages_are_not_indexable(): void
    {
        $this->get('/login')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        // robots.txt is a static file served by the web server, so read it from disk.
        $robots = file_get_contents(public_path('robots.txt'));
        $this->assertStringContainsString('Disallow: /admin', $robots);
        $this->assertStringContainsString('Sitemap:', $robots);
    }
}
