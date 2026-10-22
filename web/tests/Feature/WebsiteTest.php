<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSettings;
use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Models\Post;
use App\Models\Program;
use App\Models\Registration;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WebsiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_public_pages_render(): void
    {
        $post = Post::published()->first();
        foreach (['/', '/profil', '/program', '/program/paket-c', '/berita', "/berita/{$post->slug}", '/galeri', '/kontak', '/pendaftaran'] as $uri) {
            $this->get($uri)->assertOk()->assertSee('PKBM Widya Utama');
        }
    }

    public function test_draft_post_is_hidden(): void
    {
        $draft = Post::create(['title' => 'Draf rahasia', 'body' => 'isi', 'published_at' => null]);

        $this->get("/berita/{$draft->slug}")->assertNotFound();
        $this->get('/berita')->assertDontSee('Draf rahasia');
    }

    public function test_online_registration(): void
    {
        $program = Program::where('slug', 'paket-c')->first();

        $this->post('/pendaftaran', [
            'program_id' => $program->id, 'name' => 'Andi Pratama', 'birth_place' => 'Bandung',
            'birth_date' => '2000-05-01', 'gender' => 'L', 'phone' => '081234567890', 'address' => 'Jl. Mawar 1',
        ])->assertRedirect('/pendaftaran/berhasil');

        $registration = Registration::first();
        $this->assertSame('baru', $registration->status);
        $this->assertStringStartsWith('PKBM-', $registration->code);
        $this->get('/pendaftaran/berhasil')->assertOk()->assertSee($registration->code);
        $this->assertSame('https://wa.me/6281234567890', $registration->whatsappUrl());
    }

    public function test_registration_rejects_bots_and_closed_form(): void
    {
        $data = ['program_id' => Program::first()->id, 'name' => 'Bot', 'birth_place' => 'X', 'birth_date' => '2000-01-01',
            'gender' => 'L', 'phone' => '081234567890', 'address' => 'X'];

        $this->post('/pendaftaran', [...$data, 'website' => 'spam'])->assertSessionHasErrors('website');

        Setting::put(['registration_open' => false]);
        $this->post('/pendaftaran', $data)->assertForbidden();
        $this->assertSame(0, Registration::count());
    }

    public function test_admin_saves_settings_and_site_updates(): void
    {
        $this->actingAs(User::where('username', 'admin')->first());

        Livewire::test(ManageSettings::class)
            ->fillForm([
                'hero_title' => 'Sekolah Lagi Yuk!',
                'whatsapp' => '0812-1111-2222',
                'maps_embed' => '<iframe src="https://www.google.com/maps/embed?pb=abc&amp;x=1" width="600"></iframe>',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('https://www.google.com/maps/embed?pb=abc&x=1', Setting::get('maps_embed'));
        $this->get('/')->assertSee('Sekolah Lagi Yuk!')->assertSee('https://wa.me/6281211112222', false);
    }

    public function test_student_form_prefilled_from_registration(): void
    {
        $this->actingAs(User::where('username', 'admin')->first());
        $registration = Registration::create([
            'program_id' => Program::where('slug', 'paket-b')->first()->id, 'name' => 'Dewi', 'phone' => '0811',
            'address' => 'Jl. Kenanga', 'status' => 'diterima',
        ]);

        Livewire::withQueryParams(['pendaftar' => $registration->id])
            ->test(CreateStudent::class)
            ->assertSchemaStateSet(['name' => 'Dewi', 'paket' => 'Paket B', 'address' => 'Jl. Kenanga']);
    }

    public function test_admin_website_pages_load(): void
    {
        $this->actingAs(User::where('username', 'admin')->first());
        foreach (['settings' => '/admin/manage-settings', '/admin/programs', '/admin/posts', '/admin/gallery-items', '/admin/testimonials', '/admin/staff', '/admin/registrations'] as $uri) {
            $this->get($uri)->assertOk();
        }
    }
}
