@props(['title' => null, 'description' => null])
@php
    $nav = [
        'site.home' => 'Beranda',
        'site.profile' => 'Profil',
        'site.programs' => 'Program',
        'site.posts' => 'Berita',
        'site.gallery' => 'Galeri',
        'site.contact' => 'Kontak',
    ];
    $pageTitle = $title ? "{$title} | {$site['site_name']}" : "{$site['site_name']} | {$site['tagline']}";
    $isActive = fn (string $route) => request()->routeIs($route) || (str_ends_with($route, 's') && request()->routeIs(substr($route, 0, -1)));
    $wa = wa_url($site['whatsapp'], 'Halo '.$site['site_name'].', saya ingin bertanya tentang pendaftaran.');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $description ?? $site['about_short'] }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $description ?? $site['about_short'] }}">
    @if ($site['logo'])
        <link rel="icon" href="{{ media_url($site['logo']) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col">
    {{-- Header --}}
    <header class="sticky top-0 z-40 border-b border-slate-100 bg-white/90 backdrop-blur">
        <div class="container-site flex h-16 items-center justify-between gap-4 lg:h-20">
            <a href="{{ route('site.home') }}" class="flex items-center gap-3">
                <x-site.logo class="size-10 lg:size-11" />
                <span class="leading-tight">
                    <span class="block text-base font-extrabold text-ink lg:text-lg">{{ $site['site_name'] }}</span>
                    <span class="hidden text-xs text-muted sm:block">Pusat Kegiatan Belajar Masyarakat</span>
                </span>
            </a>

            <nav class="hidden items-center gap-1 lg:flex">
                @foreach ($nav as $route => $label)
                    <a href="{{ route($route) }}" @class([
                        'rounded-full px-4 py-2 text-sm font-semibold transition',
                        'bg-brand-50 text-brand-700' => $isActive($route),
                        'text-slate-600 hover:text-brand-700' => ! $isActive($route),
                    ])>{{ $label }}</a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ route('site.register') }}" class="btn-primary hidden !py-2.5 sm:inline-flex">Daftar Sekarang</a>
                <button type="button" class="rounded-xl p-2 text-ink hover:bg-slate-100 lg:hidden" data-menu-toggle="mobile-menu" aria-expanded="false" aria-label="Buka menu">
                    <x-heroicon-o-bars-3 class="size-7" />
                </button>
            </div>
        </div>
        <div id="mobile-menu" class="hidden border-t border-slate-100 bg-white lg:hidden">
            <nav class="container-site flex flex-col py-3">
                @foreach ($nav as $route => $label)
                    <a href="{{ route($route) }}" @class([
                        'rounded-xl px-4 py-3 font-semibold',
                        'bg-brand-50 text-brand-700' => $isActive($route),
                        'text-slate-700' => ! $isActive($route),
                    ])>{{ $label }}</a>
                @endforeach
                <a href="{{ route('site.register') }}" class="btn-primary mt-3">Daftar Sekarang</a>
            </nav>
        </div>
    </header>

    <main class="flex-1">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="bg-brand-900 text-brand-100">
        <div class="container-site grid gap-10 py-14 md:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <div class="flex items-center gap-3">
                    <x-site.logo class="size-11" light />
                    <div>
                        <p class="text-lg font-extrabold text-white">{{ $site['site_name'] }}</p>
                        <p class="text-sm text-brand-200">{{ $site['tagline'] }}</p>
                    </div>
                </div>
                <p class="mt-5 max-w-md text-sm leading-relaxed text-brand-200">{{ $site['about_short'] }}</p>
                <div class="mt-5 flex gap-3">
                    @foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube'] as $key => $label)
                        @if ($site[$key])
                            <a href="{{ $site[$key] }}" target="_blank" rel="noopener" class="rounded-full bg-white/10 px-4 py-2 text-xs font-semibold text-white hover:bg-white/20">{{ $label }}</a>
                        @endif
                    @endforeach
                </div>
            </div>
            <div>
                <p class="font-bold text-white">Jelajahi</p>
                <ul class="mt-4 space-y-2 text-sm">
                    @foreach ($nav as $route => $label)
                        <li><a href="{{ route($route) }}" class="hover:text-white">{{ $label }}</a></li>
                    @endforeach
                    <li><a href="{{ route('site.register') }}" class="hover:text-white">Pendaftaran</a></li>
                </ul>
            </div>
            <div>
                <p class="font-bold text-white">Hubungi Kami</p>
                <ul class="mt-4 space-y-3 text-sm">
                    <li class="flex gap-2"><x-heroicon-o-map-pin class="size-5 shrink-0" /> {{ $site['address'] }}</li>
                    @if ($site['phone'])
                        <li class="flex gap-2"><x-heroicon-o-phone class="size-5 shrink-0" /> {{ $site['phone'] }}</li>
                    @endif
                    @if ($site['email'])
                        <li class="flex gap-2"><x-heroicon-o-envelope class="size-5 shrink-0" /> {{ $site['email'] }}</li>
                    @endif
                    <li class="flex gap-2"><x-heroicon-o-clock class="size-5 shrink-0" /> {{ $site['office_hours'] }}</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="container-site flex flex-col items-center justify-between gap-2 py-5 text-xs text-brand-200 sm:flex-row">
                <p>&copy; {{ date('Y') }} {{ $site['site_name'] }}. Hak cipta dilindungi.</p>
                <a href="{{ url('/admin') }}" class="hover:text-white">Masuk pengelola</a>
            </div>
        </div>
    </footer>

    @if ($wa)
        <a href="{{ $wa }}" target="_blank" rel="noopener" class="fixed right-4 bottom-4 z-40 flex items-center gap-2 rounded-full bg-[#25d366] px-4 py-3 text-sm font-bold text-white shadow-xl hover:brightness-95 sm:right-6 sm:bottom-6">
            <x-heroicon-s-chat-bubble-oval-left-ellipsis class="size-5" /> Tanya via WA
        </a>
    @endif
</body>
</html>
