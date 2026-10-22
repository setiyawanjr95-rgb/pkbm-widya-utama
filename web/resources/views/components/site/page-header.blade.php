@props(['title', 'subtitle' => null, 'eyebrow' => null])
<section class="relative overflow-hidden bg-brand-800">
    <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 20% 20%, #2f5fe0 0, transparent 45%), radial-gradient(circle at 85% 70%, #f2b33d 0, transparent 35%);"></div>
    <div class="container-site relative py-14 text-white sm:py-20">
        @if ($eyebrow)
            <p class="text-sm font-bold tracking-wide text-gold-400 uppercase">{{ $eyebrow }}</p>
        @endif
        <h1 class="mt-2 max-w-3xl text-3xl font-extrabold tracking-tight sm:text-5xl">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-4 max-w-2xl text-base text-brand-100 sm:text-lg">{{ $subtitle }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
