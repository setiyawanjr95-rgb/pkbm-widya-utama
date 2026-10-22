<x-site.layout>
    {{-- Hero biru + kotak daftar cepat (gaya kotak pencarian Traveloka) --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-brand-500 via-brand-600 to-brand-800 pb-28 sm:pb-32">
        @if ($site['hero_image'])
            <img src="{{ media_url($site['hero_image']) }}" alt="" class="absolute inset-0 size-full object-cover opacity-25 mix-blend-luminosity">
        @endif
        <div class="absolute -top-24 -right-16 size-96 rounded-full bg-white/10"></div>
        <div class="absolute top-40 -left-24 size-72 rounded-full bg-brand-300/20"></div>
        <div class="container-site relative pt-10 text-white sm:pt-14">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-bold backdrop-blur">
                <x-heroicon-s-sparkles class="size-4 text-gold-400" />
                {{ $site['registration_open'] ? 'Pendaftaran warga belajar baru dibuka' : 'Pusat Kegiatan Belajar Masyarakat' }}
            </span>
            <h1 class="mt-4 max-w-3xl text-3xl leading-tight font-extrabold tracking-tight sm:text-5xl">{{ $site['hero_title'] }}</h1>
            <p class="mt-3 max-w-2xl text-base text-brand-50 sm:text-lg">{{ $site['hero_subtitle'] }}</p>
        </div>
    </section>

    <section class="container-site relative -mt-20 sm:-mt-24">
        <form action="{{ route('site.register') }}" method="GET" class="rounded-2xl bg-white p-4 shadow-xl shadow-brand-900/10 ring-1 ring-slate-200/70 sm:p-6">
            <p class="text-sm font-bold text-ink">Mau ikut program apa?</p>
            <div class="scroller mt-3 !pb-1">
                @foreach ($programs->where('open_registration', true) as $program)
                    <label class="cursor-pointer">
                        <input type="radio" name="program" value="{{ $program->id }}" class="peer sr-only" @checked($loop->first)>
                        <span class="flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-600">
                            <x-site.program-icon :icon="$program->icon" class="size-5" /> {{ $program->name }}
                        </span>
                    </label>
                @endforeach
            </div>
            <div class="mt-4 grid gap-3 md:grid-cols-[1fr_1fr_auto]">
                <label class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-2 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-100">
                    <x-heroicon-o-user class="size-5 shrink-0 text-brand-500" />
                    <span class="flex-1">
                        <span class="block text-[11px] font-semibold text-muted">Nama lengkap</span>
                        <input name="name" class="w-full text-sm font-semibold outline-none placeholder:font-normal placeholder:text-slate-400" placeholder="Tulis namamu">
                    </span>
                </label>
                <label class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-2 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-100">
                    <x-heroicon-o-phone class="size-5 shrink-0 text-brand-500" />
                    <span class="flex-1">
                        <span class="block text-[11px] font-semibold text-muted">Nomor WhatsApp</span>
                        <input name="phone" type="tel" inputmode="tel" class="w-full text-sm font-semibold outline-none placeholder:font-normal placeholder:text-slate-400" placeholder="08xxxxxxxxxx">
                    </span>
                </label>
                <button class="btn-gold !px-8 text-base"><x-heroicon-m-magnifying-glass class="size-5" /> Daftar Sekarang</button>
            </div>
        </form>

        {{-- Menu ikon (HP), seperti deretan produk di Traveloka --}}
        <div class="mt-5 grid grid-cols-4 gap-y-4 lg:hidden">
            @foreach ([['site.programs', 'academic-cap', 'Program'], ['site.register', 'pencil-square', 'Daftar'], ['site.posts', 'newspaper', 'Berita'], ['site.gallery', 'photo', 'Galeri'], ['site.profile', 'building-library', 'Profil'], ['site.contact', 'phone', 'Kontak']] as [$route, $icon, $label])
                <a href="{{ route($route) }}" class="flex flex-col items-center gap-1.5 text-xs font-semibold text-slate-700">
                    <span class="grid size-12 place-items-center rounded-2xl bg-white text-brand-500 shadow-sm ring-1 ring-slate-200/70"><x-dynamic-component :component="'heroicon-o-'.$icon" class="size-6" /></span>
                    {{ $label }}
                </a>
            @endforeach
            @if ($site['apk_url'])
                <a href="{{ $site['apk_url'] }}" class="flex flex-col items-center gap-1.5 text-xs font-semibold text-slate-700">
                    <span class="grid size-12 place-items-center rounded-2xl bg-white text-brand-500 shadow-sm ring-1 ring-slate-200/70"><x-heroicon-o-device-phone-mobile class="size-6" /></span>
                    Unduh App
                </a>
            @endif
        </div>

        {{-- Baris kepercayaan --}}
        <div class="scroller mt-5">
            @foreach ([['check-badge', 'Ijazah resmi negara'], ['clock', 'Jadwal fleksibel'], ['device-phone-mobile', 'Belajar dari HP'], ['users', 'Untuk semua usia']] as [$icon, $label])
                <span class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <span class="grid size-8 place-items-center rounded-full bg-brand-50 text-brand-500"><x-dynamic-component :component="'heroicon-s-'.$icon" class="size-5" /></span>
                    {{ $label }}
                </span>
            @endforeach
        </div>
    </section>

    {{-- Banner promo (dari Keunggulan) --}}
    <section class="container-site pt-10">
        <h2 class="text-xl font-extrabold text-ink sm:text-2xl">Kenapa belajar di sini?</h2>
        <div class="scroller mt-4 lg:grid lg:grid-cols-4">
            @php($gradients = ['from-brand-500 to-brand-700', 'from-sky-400 to-brand-600', 'from-indigo-500 to-brand-600', 'from-cyan-500 to-brand-500'])
            @foreach ($site['features'] as $i => $feature)
                <div class="relative flex w-72 flex-col overflow-hidden rounded-2xl bg-gradient-to-br p-5 text-white sm:w-80 lg:w-auto {{ $gradients[$i % 4] }}">
                    <div class="absolute -right-8 -bottom-8 size-32 rounded-full bg-white/15"></div>
                    <span class="grid size-11 place-items-center rounded-xl bg-white/20">
                        <x-dynamic-component :component="'heroicon-o-'.(['clock', 'document-check', 'device-phone-mobile', 'user-group'][$i % 4])" class="size-6" />
                    </span>
                    <h3 class="mt-4 text-lg font-extrabold">{{ $feature['title'] }}</h3>
                    <p class="mt-1 text-sm text-white/85">{{ $feature['text'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Statistik --}}
    <section class="container-site pt-8">
        <div class="card grid grid-cols-2 divide-slate-100 lg:grid-cols-4 lg:divide-x">
            @foreach ($site['stats'] as $stat)
                <div class="p-5 text-center">
                    <p class="text-2xl font-extrabold text-brand-500 sm:text-3xl">{{ $stat['value'] }}</p>
                    <p class="mt-1 text-sm text-muted">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Program --}}
    @if ($programs->isNotEmpty())
        <section class="container-site pt-12">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-xl font-extrabold text-ink sm:text-2xl">Program belajar pilihan</h2>
                    <p class="mt-1 text-sm text-muted">Pilih jenjang sesuai kebutuhanmu</p>
                </div>
                <a href="{{ route('site.programs') }}" class="shrink-0 text-sm font-bold text-brand-500 hover:text-brand-700">Lihat semua</a>
            </div>
            <div class="scroller mt-4">
                @foreach ($programs as $program)
                    <a href="{{ route('site.program', $program) }}" class="card group flex w-64 flex-col overflow-hidden transition hover:-translate-y-0.5 hover:shadow-lg sm:w-72">
                        <div class="relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-brand-400 to-brand-700">
                            @if ($program->image)
                                <img src="{{ media_url($program->image) }}" alt="" class="size-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                            @else
                                <div class="grid size-full place-items-center text-white/85"><x-site.program-icon :icon="$program->icon" class="size-16" /></div>
                            @endif
                            @if ($program->level)
                                <span class="absolute top-3 left-3 rounded-md bg-white/95 px-2 py-1 text-[11px] font-bold text-brand-600">{{ $program->level }}</span>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col p-4">
                            <h3 class="font-extrabold text-ink group-hover:text-brand-600">{{ $program->name }}</h3>
                            <p class="mt-1 line-clamp-2 text-sm text-muted">{{ $program->summary }}</p>
                            <div class="mt-auto flex items-center justify-between gap-3 pt-4">
                                <span class="truncate text-xs text-muted">{{ $program->duration ?: 'Jadwal fleksibel' }}</span>
                                <span class="shrink-0 text-sm font-extrabold text-accent-500">{{ $program->open_registration ? 'Daftar' : 'Info' }} &rarr;</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Banner aplikasi --}}
    @if ($site['apk_url'])
        <section class="container-site pt-10">
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-brand-700 to-brand-500 p-6 text-white sm:p-10">
                <div class="absolute -top-20 right-10 size-64 rounded-full bg-white/10"></div>
                <div class="relative flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
                    <div class="flex items-center gap-4">
                        <span class="grid size-14 shrink-0 place-items-center rounded-2xl bg-white text-brand-600"><x-heroicon-s-device-phone-mobile class="size-8" /></span>
                        <div>
                            <h2 class="text-xl font-extrabold sm:text-2xl">Belajar dari HP, kapan saja</h2>
                            <p class="mt-1 max-w-xl text-sm text-brand-50">Materi, kuis, tugas, nilai, dan jadwal ada di aplikasi {{ $site['site_name'] }}.</p>
                        </div>
                    </div>
                    <a href="{{ $site['apk_url'] }}" class="btn shrink-0 bg-white text-brand-600 hover:bg-brand-50"><x-heroicon-o-arrow-down-tray class="size-5" /> Unduh Aplikasi</a>
                </div>
            </div>
        </section>
    @endif

    {{-- Berita --}}
    @if ($posts->isNotEmpty())
        <section class="container-site pt-12">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-xl font-extrabold text-ink sm:text-2xl">Kabar terbaru</h2>
                    <p class="mt-1 text-sm text-muted">Berita, kegiatan, dan pengumuman</p>
                </div>
                <a href="{{ route('site.posts') }}" class="shrink-0 text-sm font-bold text-brand-500 hover:text-brand-700">Lihat semua</a>
            </div>
            <div class="scroller mt-4">
                @foreach ($posts as $post)
                    <x-site.post-card :post="$post" class="w-72 sm:w-80" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Sambutan --}}
    <section class="container-site pt-12">
        <div class="card grid items-center gap-6 overflow-hidden md:grid-cols-[16rem_1fr]">
            <div class="aspect-[16/9] bg-brand-100 md:aspect-auto md:h-full">
                @if ($site['head_photo'])
                    <img src="{{ media_url($site['head_photo']) }}" alt="{{ $site['head_name'] }}" class="size-full object-cover">
                @else
                    <div class="grid size-full place-items-center bg-gradient-to-br from-brand-100 to-brand-200 text-brand-500"><x-heroicon-o-user class="size-24" /></div>
                @endif
            </div>
            <div class="p-6 pt-0 md:p-8 md:pl-0">
                <span class="eyebrow">Sambutan kepala PKBM</span>
                <blockquote class="mt-4 text-lg leading-relaxed text-slate-700">"{{ $site['head_message'] }}"</blockquote>
                <p class="mt-4 font-extrabold text-ink">{{ $site['head_name'] }}</p>
                <p class="text-sm text-muted">Kepala {{ $site['site_name'] }}</p>
                <a href="{{ route('site.profile') }}" class="mt-4 inline-block text-sm font-bold text-brand-500 hover:text-brand-700">Kenali kami lebih dekat &rarr;</a>
            </div>
        </div>
    </section>

    {{-- Testimoni gaya ulasan --}}
    @if ($testimonials->isNotEmpty())
        <section class="container-site pt-12">
            <h2 class="text-xl font-extrabold text-ink sm:text-2xl">Kata alumni & warga belajar</h2>
            <div class="scroller mt-4">
                @foreach ($testimonials as $t)
                    <figure class="card flex w-80 flex-col p-5">
                        <div class="flex items-center gap-3">
                            @if ($t->photo)
                                <img src="{{ media_url($t->photo) }}" alt="" class="size-11 rounded-full object-cover">
                            @else
                                <span class="grid size-11 place-items-center rounded-full bg-brand-500 font-bold text-white">{{ mb_substr($t->name, 0, 1) }}</span>
                            @endif
                            <div>
                                <p class="font-bold text-ink">{{ $t->name }}</p>
                                <p class="text-xs text-muted">{{ $t->role }}</p>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center gap-0.5">
                            @for ($s = 0; $s < 5; $s++)
                                <x-heroicon-s-star class="size-4 text-gold-500" />
                            @endfor
                        </div>
                        <blockquote class="mt-3 text-sm leading-relaxed text-slate-700">"{{ $t->quote }}"</blockquote>
                    </figure>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Galeri singkat --}}
    @if ($gallery->isNotEmpty())
        <section class="container-site pt-12">
            <div class="flex items-end justify-between gap-4">
                <h2 class="text-xl font-extrabold text-ink sm:text-2xl">Suasana belajar</h2>
                <a href="{{ route('site.gallery') }}" class="shrink-0 text-sm font-bold text-brand-500 hover:text-brand-700">Lihat galeri</a>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-3">
                @foreach ($gallery as $item)
                    <a href="{{ route('site.gallery') }}" class="group aspect-[4/3] overflow-hidden rounded-2xl bg-brand-100">
                        <img src="{{ media_url($item->image) }}" alt="{{ $item->title }}" class="size-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <x-site.cta />
</x-site.layout>
