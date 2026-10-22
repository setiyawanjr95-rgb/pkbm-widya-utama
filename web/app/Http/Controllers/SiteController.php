<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use App\Models\Post;
use App\Models\Program;
use App\Models\Registration;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Halaman publik website PKBM. */
class SiteController extends Controller
{
    public function home()
    {
        return view('site.home', [
            'programs' => Program::orderBy('sort')->get(),
            'posts' => Post::published()->limit(6)->get(),
            'testimonials' => Testimonial::where('visible', true)->latest()->limit(6)->get(),
            'gallery' => GalleryItem::latest('taken_at')->latest()->limit(6)->get(),
        ]);
    }

    public function profile()
    {
        return view('site.profile', ['staff' => Staff::orderBy('sort')->orderBy('name')->get()]);
    }

    public function programs()
    {
        return view('site.programs', ['programs' => Program::orderBy('sort')->get()]);
    }

    public function program(Program $program)
    {
        return view('site.program', [
            'program' => $program,
            'others' => Program::whereKeyNot($program->id)->orderBy('sort')->get(),
        ]);
    }

    public function posts(Request $request)
    {
        $category = $request->query('kategori');

        return view('site.posts', [
            'posts' => Post::published()
                ->when(isset(Post::CATEGORIES[$category]), fn ($q) => $q->where('category', $category))
                ->paginate(9)->withQueryString(),
            'category' => $category,
        ]);
    }

    public function post(Post $post)
    {
        abort_unless($post->published_at && $post->published_at->isPast(), 404);

        return view('site.post', [
            'post' => $post,
            'latest' => Post::published()->whereKeyNot($post->id)->limit(4)->get(),
        ]);
    }

    public function gallery()
    {
        return view('site.gallery', ['items' => GalleryItem::latest('taken_at')->latest()->paginate(24)]);
    }

    public function contact()
    {
        return view('site.contact');
    }

    public function registerForm(Request $request)
    {
        return view('site.register', [
            'programs' => Program::where('open_registration', true)->orderBy('sort')->get(),
            'selected' => $request->query('program'),
        ]);
    }

    public function register(Request $request)
    {
        abort_unless(Setting::get('registration_open'), 403, 'Pendaftaran sedang ditutup.');

        $data = $request->validate([
            'program_id' => ['required', Rule::exists('programs', 'id')->where('open_registration', true)],
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['nullable', 'digits:16'],
            'birth_place' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date', 'before:today'],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'phone' => ['required', 'string', 'min:9', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'last_education' => ['nullable', Rule::in(array_keys(Registration::EDUCATION))],
            'notes' => ['nullable', 'string', 'max:1000'],
            'website' => ['prohibited'], // jebakan bot spam (field tersembunyi)
        ], [], [
            'program_id' => 'program', 'name' => 'nama lengkap', 'nik' => 'NIK', 'birth_place' => 'tempat lahir',
            'birth_date' => 'tanggal lahir', 'gender' => 'jenis kelamin', 'phone' => 'nomor HP/WA', 'address' => 'alamat',
        ]);

        $registration = Registration::create($data);

        return redirect()->route('site.register.done')->with('registration_id', $registration->id);
    }

    /** Halaman sukses hanya bisa dibuka sekali, langsung setelah mengisi formulir. */
    public function registerDone(Request $request)
    {
        $registration = Registration::with('program')->find($request->session()->get('registration_id'));

        return $registration
            ? view('site.register-done', ['registration' => $registration])
            : redirect()->route('site.register');
    }
}
