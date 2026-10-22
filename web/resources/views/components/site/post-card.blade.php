@props(['post'])
<a href="{{ route('site.post', $post) }}" class="card group flex flex-col overflow-hidden transition hover:-translate-y-1 hover:shadow-xl">
    <div class="aspect-[16/10] overflow-hidden bg-brand-100">
        @if ($post->cover)
            <img src="{{ media_url($post->cover) }}" alt="" class="size-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
        @else
            <div class="grid size-full place-items-center bg-gradient-to-br from-brand-500 to-brand-800 text-white/80">
                <x-heroicon-o-newspaper class="size-14" />
            </div>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-6">
        <div class="flex items-center gap-2 text-xs font-semibold text-muted">
            <span class="rounded-full bg-brand-50 px-2.5 py-1 text-brand-700">{{ $post->category }}</span>
            <span>{{ $post->published_at->translatedFormat('d F Y') }}</span>
        </div>
        <h3 class="mt-3 text-lg leading-snug font-bold text-ink group-hover:text-brand-700">{{ $post->title }}</h3>
        <p class="mt-2 line-clamp-3 text-sm text-muted">{{ $post->summary }}</p>
        <span class="mt-auto pt-4 text-sm font-bold text-brand-600">Baca selengkapnya &rarr;</span>
    </div>
</a>
