<x-site.layout title="Berita & Pengumuman">
    <x-site.page-header eyebrow="Kabar PKBM" title="Berita & Pengumuman" subtitle="Informasi kegiatan, pengumuman, dan cerita dari warga belajar." />

    <section class="container-site py-12 lg:py-16">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('site.posts') }}" @class(['rounded-full px-4 py-2 text-sm font-semibold', 'bg-brand-600 text-white' => ! $category, 'bg-slate-100 text-slate-700 hover:bg-slate-200' => $category])>Semua</a>
            @foreach (\App\Models\Post::CATEGORIES as $cat)
                <a href="{{ route('site.posts', ['kategori' => $cat]) }}" @class(['rounded-full px-4 py-2 text-sm font-semibold', 'bg-brand-600 text-white' => $category === $cat, 'bg-slate-100 text-slate-700 hover:bg-slate-200' => $category !== $cat])>{{ $cat }}</a>
            @endforeach
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($posts as $post)
                <x-site.post-card :post="$post" />
            @empty
                <p class="col-span-full py-16 text-center text-muted">Belum ada berita.</p>
            @endforelse
        </div>
        <div class="mt-10">{{ $posts->links() }}</div>
    </section>
</x-site.layout>
