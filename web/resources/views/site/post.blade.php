<x-site.layout :title="$post->title" :description="$post->summary">
    <article>
        <header class="bg-brand-50">
            <div class="container-site max-w-3xl py-12 sm:py-16">
                <a href="{{ route('site.posts') }}" class="text-sm font-bold text-brand-600">&larr; Berita & Pengumuman</a>
                <div class="mt-5 flex items-center gap-2 text-sm font-semibold text-muted">
                    <span class="rounded-full bg-white px-3 py-1 text-brand-700">{{ $post->category }}</span>
                    <span>{{ $post->published_at->translatedFormat('l, d F Y') }}</span>
                </div>
                <h1 class="mt-4 text-3xl leading-tight font-extrabold tracking-tight text-ink sm:text-4xl">{{ $post->title }}</h1>
            </div>
        </header>
        <div class="container-site grid max-w-6xl gap-12 py-12 lg:grid-cols-[1fr_18rem]">
            <div class="mx-auto w-full max-w-3xl">
                @if ($post->cover)
                    <img src="{{ media_url($post->cover) }}" alt="" class="mb-8 aspect-[16/9] w-full rounded-3xl object-cover">
                @endif
                <div class="prose-site text-lg">{!! str($post->body)->sanitizeHtml() !!}</div>
            </div>
            @if ($latest->isNotEmpty())
                <aside>
                    <p class="font-extrabold text-ink">Berita lainnya</p>
                    <ul class="mt-4 space-y-4">
                        @foreach ($latest as $item)
                            <li>
                                <a href="{{ route('site.post', $item) }}" class="group block">
                                    <p class="text-xs font-semibold text-muted">{{ $item->published_at->translatedFormat('d M Y') }}</p>
                                    <p class="font-semibold text-ink group-hover:text-brand-700">{{ $item->title }}</p>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </aside>
            @endif
        </div>
    </article>
</x-site.layout>
