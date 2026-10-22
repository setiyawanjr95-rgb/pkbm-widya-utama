@props(['title' => null, 'description' => null])
@php
    $nav = [
        'site.home' => ['Beranda', 'home'],
        'site.profile' => ['Profil', 'building-library'],
        'site.programs' => ['Program', 'academic-cap'],
        'site.posts' => ['Berita', 'newspaper'],
        'site.gallery' => ['Galeri', 'photo'],
        'site.contact' => ['Kontak', 'phone'],
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
    {{-- Header biru gaya Traveloka --}}
    <header class="sticky top-0 z-40 bg-gradient-to-r from-brand-600 to-brand-500 text-white shadow-md shadow-brand-900/10">
        <div class="container-site flex h-16 items-center justify-between gap-4">
            <a href="{{ route('site.home') }}" class="flex items-center gap-2.5">
                <x-site.logo class="size-9" light />
                <span class="leading-tight">
                    <span class="block text-base font-extrabold lg:text-lg">{{ $site['site_name'] }}</span>
                    <span class="hidden text-[11px] text-brand-100 sm:block">Pusat Kegiatan Belajar Masyarakat</span>
                </span>
            </a>

            <div class="flex items-center gap-1 sm:gap-2">
                @if ($site['apk_url'])
                    <a href="{{ $site['apk_url'] }}" class="hidden items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-semibold text-white/90 hover:bg-white/10 md:flex">
                        <x-heroicon-o-device-phone-mobile class="size-5" /> Unduh App
                    </a>
                @endif
                <a href="{{ route('site.contact') }}" class="hidden items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-semibold text-white/90 hover:bg-white/10 md:flex">
                    <x-heroicon-o-chat-bubble-left-right class="size-5" /> Bantuan
                </a>
                <a href="{{ route('site.register') }}" class="rounded-lg bg-white px-4 py-2 text-sm font-bold text-brand-600 hover:bg-brand-50">Daftar</a>
                <button type="button" class="rounded-lg p-2 hover:bg-white/10 lg:hidden" data-menu-toggle="mobile-menu" aria-expanded="false" aria-label="Buka menu">
                    <x-heroicon-o-bars-3 class="size-6" />
                </button>
            </div>
        </div>

        {{-- Baris menu ikon --}}
        <nav class="hidden border-t border-white/15 lg:block">
            <div class="container-site flex gap-1 py-1.5">
                @foreach ($nav as $route => [$label, $icon])
                    <a href="{{ route($route) }}" @class([
                        'flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition',
                        'bg-white/20 text-white' => $isActive($route),
                        'text-white/85 hover:bg-white/10 hover:text-white' => ! $isActive($route),
                    ])>
                        <x-dynamic-component :component="'heroicon-o-'.$icon" class="size-5" /> {{ $label }}
                    </a>
                @endforeach
            </div>
        </nav>

        <div id="mobile-menu" class="hidden border-t border-white/15 bg-white text-ink lg:hidden">
            <nav class="container-site grid grid-cols-3 gap-2 py-4">
                @foreach ($nav as $route => [$label, $icon])
                    <a href="{{ route($route) }}" @class([
                        'flex flex-col items-center gap-1.5 rounded-xl p-3 text-xs font-semibold',
                        'bg-brand-50 text-brand-600' => $isActive($route),
                        'text-slate-700' => ! $isActive($route),
                    ])>
                        <x-dynamic-component :component="'heroicon-o-'.$icon" class="size-6 text-brand-500" /> {{ $label }}
                    </a>
                @endforeach
            </nav>
        </div>
    </header>

    <main class="flex-1">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="mt-8 bg-navy text-slate-300">
        <div class="container-site grid gap-10 py-14 md:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <div class="flex items-center gap-3">
                    <x-site.logo class="size-11" light />
                    <div>
                        <p class="text-lg font-extrabold text-white">{{ $site['site_name'] }}</p>
                        <p class="text-sm text-slate-400">{{ $site['tagline'] }}</p>
                    </div>
                </div>
                <p class="mt-5 max-w-md text-sm leading-relaxed text-slate-400">{{ $site['about_short'] }}</p>
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
                    @foreach ($nav as $route => [$label])
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
            <div class="container-site flex flex-col items-center justify-between gap-2 py-5 text-xs text-slate-400 sm:flex-row">
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
