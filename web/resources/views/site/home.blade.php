<x-site.layout>
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-gradient-to-b from-brand-50 to-white">
        <div class="absolute -top-32 -right-32 size-[28rem] rounded-full bg-brand-200/50 blur-3xl"></div>
        <div class="container-site relative grid items-center gap-12 py-14 lg:grid-cols-2 lg:py-24">
            <div>
                <span class="eyebrow"><x-heroicon-s-sparkles class="size-4" /> Pendaftaran warga belajar baru dibuka</span>
                <h1 class="mt-5 text-4xl leading-[1.1] font-extrabold tracking-tight text-ink sm:text-5xl lg:text-6xl">
                    {{ $site['hero_title'] }}
                </h1>
                <p class="mt-5 max-w-xl text-lg leading-relaxed text-muted">{{ $site['hero_subtitle'] }}</p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('site.register') }}" class="btn-primary !px-7 !py-3.5 text-base">Daftar Sekarang <x-heroicon-m-arrow-right class="size-5" /></a>
                    <a href="{{ route('site.programs') }}" class="btn-ghost !px-7 !py-3.5 text-base">Lihat Program</a>
                </div>
                <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm font-semibold text-muted">
                    <span class="flex items-center gap-2"><x-heroicon-s-check-badge class="size-5 text-brand-600" /> Ijazah resmi</span>
                    <span class="flex items-center gap-2"><x-heroicon-s-check-badge class="size-5 text-brand-600" /> Jadwal fleksibel</span>
                    <span class="flex items-center gap-2"><x-heroicon-s-check-badge class="size-5 text-brand-600" /> Semua usia</span>
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-lg">
                @if ($site['hero_image'])
                    <img src="{{ media_url($site['hero_image']) }}" alt="Kegiatan belajar di {{ $site['site_name'] }}" class="aspect-[4/5] w-full rounded-[2rem] object-cover shadow-2xl shadow-brand-900/20">
                @else
                    {{-- Ilustrasi: kartu aplikasi belajar --}}
                    <div class="relative aspect-[4/5] w-full overflow-hidden rounded-[2rem] bg-gradient-to-br from-brand-600 to-brand-900 p-8 shadow-2xl shadow-brand-900/30">
                        <div class="absolute -right-10 -bottom-10 size-64 rounded-full bg-white/10"></div>
                        <div class="absolute top-10 -left-10 size-40 rounded-full bg-gold-500/30"></div>
                        <div class="relative mx-auto mt-4 w-56 rounded-[1.75rem] border-[6px] border-ink/80 bg-white p-4 shadow-2xl">
                            <div class="rounded-2xl bg-brand-600 p-3 text-white">
                                <p class="text-[10px] opacity-80">Selamat datang,</p>
                                <p class="text-sm font-bold">Warga Belajar</p>
                                <div class="mt-2 h-1.5 rounded-full bg-white/30"><div class="h-1.5 w-2/3 rounded-full bg-gold-400"></div></div>
                            </div>
                            <div class="mt-3 grid grid-cols-3 gap-2 text-center text-[9px] font-semibold text-ink">
                                @foreach (['book-open' => 'Kelas', 'clipboard-document-list' => 'Tugas', 'chart-bar' => 'Nilai'] as $icon => $label)
                                    <div class="rounded-xl bg-brand-50 p-2"><x-dynamic-component :component="'heroicon-o-'.$icon" class="mx-auto size-5 text-brand-600" />{{ $label }}</div>
                                @endforeach
                            </div>
                            @foreach (['Matematika', 'Bahasa Indonesia', 'IPA'] as $mapel)
                                <div class="mt-2 flex items-center gap-2 rounded-xl border border-slate-100 p-2">
                                    <span class="size-6 rounded-lg bg-brand-100"></span>
                                    <span class="text-[10px] font-semibold">{{ $mapel }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="absolute -bottom-5 -left-3 flex items-center gap-3 rounded-2xl bg-white p-4 shadow-xl sm:-left-8">
                    <span class="grid size-11 place-items-center rounded-xl bg-gold-500/20 text-gold-500"><x-heroicon-s-trophy class="size-6" /></span>
                    <div>
                        <p class="text-lg font-extrabold text-ink">{{ $site['stats'][2]['value'] ?? '' }}</p>
                        <p class="text-xs text-muted">{{ $site['stats'][2]['label'] ?? 'Lulusan' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Statistik --}}
    <section class="container-site -mt-2 lg:-mt-6">
        <div class="grid grid-cols-2 gap-4 rounded-3xl bg-brand-700 p-6 text-white shadow-xl shadow-brand-900/20 sm:p-8 lg:grid-cols-4">
            @foreach ($site['stats'] as $stat)
                <div class="text-center">
                    <p class="text-3xl font-extrabold sm:text-4xl">{{ $stat['value'] }}</p>
                    <p class="mt-1 text-sm text-brand-100">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Keunggulan --}}
    <section class="container-site py-20">
        <div class="mx-auto max-w-2xl text-center">
            <span class="eyebrow">Kenapa memilih kami</span>
            <h2 class="section-title">Belajar nyaman, tetap bisa kerja dan urus keluarga</h2>
        </div>
        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($site['features'] as $i => $feature)
                <div class="card p-6 transition hover:-translate-y-1 hover:shadow-lg">
                    <span class="grid size-12 place-items-center rounded-2xl bg-brand-100 text-brand-700">
                        <x-dynamic-component :component="'heroicon-o-'.(['clock', 'document-check', 'device-phone-mobile', 'user-group'][$i % 4])" class="size-6" />
                    </span>
                    <h3 class="mt-5 text-lg font-bold text-ink">{{ $feature['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-muted">{{ $feature['text'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Program --}}
    @if ($programs->isNotEmpty())
        <section class="bg-slate-50 py-20">
            <div class="container-site">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <span class="eyebrow">Program belajar</span>
                        <h2 class="section-title">Pilih program sesuai jenjangmu</h2>
                    </div>
                    <a href="{{ route('site.programs') }}" class="text-sm font-bold text-brand-600 hover:text-brand-800">Semua program &rarr;</a>
                </div>
                <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($programs->take(6) as $program)
                        <a href="{{ route('site.program', $program) }}" class="card group flex flex-col p-7 transition hover:-translate-y-1 hover:border-brand-200 hover:shadow-xl">
                            <div class="flex items-center justify-between">
                                <span class="grid size-14 place-items-center rounded-2xl bg-brand-600 text-white shadow-lg shadow-brand-600/30">
                                    <x-site.program-icon :icon="$program->icon" class="size-7" />
                                </span>
                                @if ($program->level)
                                    <span class="rounded-full bg-gold-500/15 px-3 py-1 text-xs font-bold text-amber-700">{{ $program->level }}</span>
                                @endif
                            </div>
                            <h3 class="mt-6 text-xl font-extrabold text-ink group-hover:text-brand-700">{{ $program->name }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-muted">{{ $program->summary }}</p>
                            @if ($program->features)
                                <ul class="mt-5 space-y-2 text-sm text-slate-700">
                                    @foreach (array_slice($program->features, 0, 3) as $f)
                                        <li class="flex gap-2"><x-heroicon-s-check-circle class="size-5 shrink-0 text-brand-600" /> {{ $f }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            <span class="mt-auto pt-6 text-sm font-bold text-brand-600">Detail program &rarr;</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Sambutan --}}
    <section class="container-site grid items-center gap-12 py-20 lg:grid-cols-2">
        <div class="relative">
            <div class="aspect-square overflow-hidden rounded-[2rem] bg-brand-100">
                @if ($site['head_photo'])
                    <img src="{{ media_url($site['head_photo']) }}" alt="{{ $site['head_name'] }}" class="size-full object-cover">
                @else
                    <div class="grid size-full place-items-center bg-gradient-to-br from-brand-100 to-brand-200 text-brand-600"><x-heroicon-o-user class="size-32" /></div>
                @endif
            </div>
            <div class="absolute right-4 -bottom-6 left-4 rounded-2xl bg-white p-5 shadow-xl sm:left-auto sm:w-72">
                <p class="font-extrabold text-ink">{{ $site['head_name'] }}</p>
                <p class="text-sm text-muted">Kepala {{ $site['site_name'] }}</p>
            </div>
        </div>
        <div>
            <span class="eyebrow">Sambutan</span>
            <h2 class="section-title">Pendidikan adalah hak semua orang</h2>
            <blockquote class="mt-6 border-l-4 border-gold-500 pl-5 text-lg leading-relaxed text-slate-700 italic">"{{ $site['head_message'] }}"</blockquote>
            <a href="{{ route('site.profile') }}" class="btn-ghost mt-8">Kenali kami lebih dekat</a>
        </div>
    </section>

    {{-- Aplikasi --}}
    @if ($site['apk_url'])
        <section class="container-site pb-4">
            <div class="grid items-center gap-8 rounded-[2rem] bg-gradient-to-r from-brand-50 to-brand-100 p-8 sm:p-12 lg:grid-cols-[1fr_auto]">
                <div>
                    <span class="eyebrow bg-white">Aplikasi Android</span>
                    <h2 class="mt-3 text-2xl font-extrabold text-ink sm:text-3xl">Belajar dari HP, kapan saja</h2>
                    <p class="mt-3 max-w-2xl text-muted">Warga belajar bisa membuka materi, mengerjakan kuis, mengumpulkan tugas, melihat nilai dan jadwal langsung dari aplikasi {{ $site['site_name'] }}.</p>
                </div>
                <a href="{{ $site['apk_url'] }}" class="btn-primary !px-7 !py-4"><x-heroicon-o-arrow-down-tray class="size-5" /> Unduh Aplikasi</a>
            </div>
        </section>
    @endif

    {{-- Berita --}}
    @if ($posts->isNotEmpty())
        <section class="container-site py-20">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <span class="eyebrow">Kabar terbaru</span>
                    <h2 class="section-title">Berita & Pengumuman</h2>
                </div>
                <a href="{{ route('site.posts') }}" class="text-sm font-bold text-brand-600 hover:text-brand-800">Semua berita &rarr;</a>
            </div>
            <div class="mt-10 grid gap-6 md:grid-cols-3">
                @foreach ($posts as $post)
                    <x-site.post-card :post="$post" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Testimoni --}}
    @if ($testimonials->isNotEmpty())
        <section class="bg-brand-900 py-20 text-white">
            <div class="container-site">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="text-sm font-bold tracking-wide text-gold-400 uppercase">Cerita alumni</p>
                    <h2 class="mt-3 text-3xl font-extrabold sm:text-4xl">Mereka sudah membuktikannya</h2>
                </div>
                <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($testimonials as $t)
                        <figure class="rounded-3xl bg-white/5 p-7 ring-1 ring-white/10">
                            <x-heroicon-s-chat-bubble-bottom-center-text class="size-8 text-gold-400" />
                            <blockquote class="mt-4 leading-relaxed text-brand-100">"{{ $t->quote }}"</blockquote>
                            <figcaption class="mt-6 flex items-center gap-3">
                                @if ($t->photo)
                                    <img src="{{ media_url($t->photo) }}" alt="" class="size-11 rounded-full object-cover">
                                @else
                                    <span class="grid size-11 place-items-center rounded-full bg-brand-600 font-bold">{{ mb_substr($t->name, 0, 1) }}</span>
                                @endif
                                <div>
                                    <p class="font-bold">{{ $t->name }}</p>
                                    <p class="text-sm text-brand-200">{{ $t->role }}</p>
                                </div>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Galeri singkat --}}
    @if ($gallery->isNotEmpty())
        <section class="container-site py-20">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <span class="eyebrow">Galeri</span>
                    <h2 class="section-title">Suasana belajar</h2>
                </div>
                <a href="{{ route('site.gallery') }}" class="text-sm font-bold text-brand-600 hover:text-brand-800">Lihat galeri &rarr;</a>
            </div>
            <div class="mt-10 grid grid-cols-2 gap-4 md:grid-cols-3">
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
