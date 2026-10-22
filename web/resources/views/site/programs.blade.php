<x-site.layout title="Program">
    <x-site.page-header eyebrow="Program belajar" title="Program Pendidikan" subtitle="Pendidikan kesetaraan dan keterampilan untuk remaja maupun dewasa, dengan jadwal yang menyesuaikan kesibukan." />

    <section class="container-site relative -mt-10 pb-12">
        <div class="grid gap-5">
            @forelse ($programs as $program)
                <article class="card grid overflow-hidden lg:grid-cols-[22rem_1fr]">
                    <div class="relative min-h-56 bg-gradient-to-br from-brand-600 to-brand-900">
                        @if ($program->image)
                            <img src="{{ media_url($program->image) }}" alt="{{ $program->name }}" class="absolute inset-0 size-full object-cover">
                        @else
                            <div class="absolute inset-0 grid place-items-center text-white/80"><x-site.program-icon :icon="$program->icon" class="size-24" /></div>
                        @endif
                    </div>
                    <div class="p-7 sm:p-9">
                        <div class="flex flex-wrap items-center gap-2">
                            @if ($program->level)
                                <span class="rounded-md bg-accent-500/10 px-2.5 py-1 text-xs font-bold text-accent-600">{{ $program->level }}</span>
                            @endif
                            @if ($program->duration)
                                <span class="rounded-md bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-600">{{ $program->duration }}</span>
                            @endif
                        </div>
                        <h2 class="mt-4 text-2xl font-extrabold text-ink">{{ $program->name }}</h2>
                        <p class="mt-3 leading-relaxed text-muted">{{ $program->summary }}</p>
                        @if ($program->features)
                            <ul class="mt-5 grid gap-2 text-sm text-slate-700 sm:grid-cols-2">
                                @foreach ($program->features as $f)
                                    <li class="flex gap-2"><x-heroicon-s-check-circle class="size-5 shrink-0 text-brand-600" /> {{ $f }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('site.program', $program) }}" class="btn-ghost">Detail program</a>
                            @if ($program->open_registration)
                                <a href="{{ route('site.register', ['program' => $program->id]) }}" class="btn-primary">Daftar program ini</a>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <p class="text-center text-muted">Program belum ditambahkan.</p>
            @endforelse
        </div>
    </section>

    <x-site.cta />
</x-site.layout>
