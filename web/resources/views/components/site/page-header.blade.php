@props(['title', 'subtitle' => null, 'eyebrow' => null])
<section class="relative overflow-hidden bg-gradient-to-br from-brand-500 via-brand-600 to-brand-800 pb-16">
    <div class="absolute -top-24 -right-20 size-80 rounded-full bg-white/10"></div>
    <div class="absolute -bottom-32 left-1/3 size-72 rounded-full bg-brand-300/20"></div>
    <div class="container-site relative pt-8 text-white sm:pt-10">
        <nav class="flex items-center gap-1.5 text-xs font-semibold text-brand-100">
            <a href="{{ route('site.home') }}" class="hover:text-white">Beranda</a>
            <x-heroicon-m-chevron-right class="size-4" />
            <span class="text-white">{{ $eyebrow ?? $title }}</span>
        </nav>
        <h1 class="mt-3 max-w-3xl text-3xl font-extrabold tracking-tight sm:text-4xl">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-3 max-w-2xl text-sm text-brand-50 sm:text-base">{{ $subtitle }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
