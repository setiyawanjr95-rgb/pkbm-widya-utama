<x-site.layout title="Profil">
    <x-site.page-header eyebrow="Tentang kami" title="Profil {{ $site['site_name'] }}" :subtitle="$site['tagline']" />

    <section class="container-site grid gap-12 py-16 lg:grid-cols-[1.4fr_1fr] lg:py-20">
        <div>
            <span class="eyebrow">Sejarah singkat</span>
            <h2 class="section-title">Siapa kami</h2>
            <div class="prose-site mt-6 text-lg">
                @foreach (preg_split("/\n\s*\n/", trim($site['history'])) as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </div>
        </div>
        <div class="card overflow-hidden">
            <div class="aspect-[4/3] bg-brand-100">
                @if ($site['head_photo'])
                    <img src="{{ media_url($site['head_photo']) }}" alt="{{ $site['head_name'] }}" class="size-full object-cover">
                @else
                    <div class="grid size-full place-items-center text-brand-500"><x-heroicon-o-user class="size-24" /></div>
                @endif
            </div>
            <div class="p-6">
                <p class="text-lg font-extrabold text-ink">{{ $site['head_name'] }}</p>
                <p class="text-sm text-muted">Kepala {{ $site['site_name'] }}</p>
                <p class="mt-4 leading-relaxed text-slate-700 italic">"{{ $site['head_message'] }}"</p>
            </div>
        </div>
    </section>

    <section class="bg-slate-50 py-16 lg:py-20">
        <div class="container-site grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl bg-brand-700 p-8 text-white sm:p-10">
                <span class="grid size-12 place-items-center rounded-2xl bg-white/15"><x-heroicon-o-eye class="size-6" /></span>
                <h2 class="mt-6 text-2xl font-extrabold">Visi</h2>
                <p class="mt-3 text-lg leading-relaxed text-brand-100">{{ $site['vision'] }}</p>
            </div>
            <div class="card p-8 sm:p-10">
                <span class="grid size-12 place-items-center rounded-2xl bg-brand-100 text-brand-700"><x-heroicon-o-flag class="size-6" /></span>
                <h2 class="mt-6 text-2xl font-extrabold text-ink">Misi</h2>
                <ol class="mt-4 space-y-3">
                    @foreach ((array) $site['missions'] as $i => $mission)
                        <li class="flex gap-3 text-slate-700">
                            <span class="grid size-7 shrink-0 place-items-center rounded-full bg-brand-600 text-xs font-bold text-white">{{ $i + 1 }}</span>
                            <span>{{ $mission }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>

    @if ($staff->isNotEmpty())
        <section class="container-site py-16 lg:py-20">
            <div class="mx-auto max-w-2xl text-center">
                <span class="eyebrow">Tim kami</span>
                <h2 class="section-title">Pengelola & Tutor</h2>
            </div>
            <div class="mt-12 grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($staff as $person)
                    <div class="card overflow-hidden text-center">
                        <div class="aspect-square bg-brand-50">
                            @if ($person->photo)
                                <img src="{{ media_url($person->photo) }}" alt="{{ $person->name }}" class="size-full object-cover" loading="lazy">
                            @else
                                <div class="grid size-full place-items-center text-4xl font-extrabold text-brand-300">{{ mb_substr($person->name, 0, 1) }}</div>
                            @endif
                        </div>
                        <div class="p-4">
                            <p class="font-bold text-ink">{{ $person->name }}</p>
                            <p class="text-sm text-muted">{{ $person->position }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <x-site.cta />
</x-site.layout>
