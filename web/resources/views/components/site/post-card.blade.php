@props(['post'])
<a href="{{ route('site.post', $post) }}" {{ $attributes->merge(['class' => 'card group flex flex-col overflow-hidden transition hover:-translate-y-0.5 hover:shadow-lg']) }}>
    <div class="relative aspect-[16/10] overflow-hidden bg-brand-100">
        @if ($post->cover)
            <img src="{{ media_url($post->cover) }}" alt="" class="size-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
        @else
            <div class="grid size-full place-items-center bg-gradient-to-br from-brand-400 to-brand-700 text-white/80">
                <x-heroicon-o-newspaper class="size-12" />
            </div>
        @endif
        <span class="absolute top-3 left-3 rounded-md bg-white/95 px-2 py-1 text-[11px] font-bold text-brand-600">{{ $post->category }}</span>
    </div>
    <div class="flex flex-1 flex-col p-4">
        <h3 class="line-clamp-2 leading-snug font-bold text-ink group-hover:text-brand-600">{{ $post->title }}</h3>
        <p class="mt-1.5 line-clamp-2 text-sm text-muted">{{ $post->summary }}</p>
        <p class="mt-auto flex items-center gap-1.5 pt-3 text-xs text-muted"><x-heroicon-o-calendar class="size-4" /> {{ $post->published_at->translatedFormat('d F Y') }}</p>
    </div>
</a>
