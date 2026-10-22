<x-site.layout :title="$program->name" :description="$program->summary">
    <x-site.page-header :eyebrow="$program->level ?: 'Program'" :title="$program->name" :subtitle="$program->summary">
        @if ($program->open_registration)
            <a href="{{ route('site.register', ['program' => $program->id]) }}" class="btn-gold mt-8">Daftar program ini</a>
        @endif
    </x-site.page-header>

    <section class="container-site grid gap-12 py-16 lg:grid-cols-[1fr_20rem] lg:py-20">
        <article>
            @if ($program->image)
                <img src="{{ media_url($program->image) }}" alt="{{ $program->name }}" class="mb-8 aspect-[16/9] w-full rounded-3xl object-cover">
            @endif
            <div class="prose-site text-lg">{!! str($program->body ?: e($program->summary))->sanitizeHtml() !!}</div>
        </article>
        <aside class="space-y-6">
            <div class="card p-6">
                <p class="font-extrabold text-ink">Ringkasan</p>
                <dl class="mt-4 space-y-3 text-sm">
                    @if ($program->level)
                        <div class="flex justify-between gap-4"><dt class="text-muted">Jenjang</dt><dd class="font-semibold">{{ $program->level }}</dd></div>
                    @endif
                    @if ($program->duration)
                        <div class="flex justify-between gap-4"><dt class="text-muted">Lama belajar</dt><dd class="font-semibold">{{ $program->duration }}</dd></div>
                    @endif
                    <div class="flex justify-between gap-4"><dt class="text-muted">Pendaftaran</dt><dd class="font-semibold">{{ $program->open_registration ? 'Dibuka' : 'Ditutup' }}</dd></div>
                </dl>
                @if ($program->features)
                    <ul class="mt-5 space-y-2 border-t border-slate-100 pt-5 text-sm text-slate-700">
                        @foreach ($program->features as $f)
                            <li class="flex gap-2"><x-heroicon-s-check-circle class="size-5 shrink-0 text-brand-600" /> {{ $f }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            @if ($others->isNotEmpty())
                <div class="card p-6">
                    <p class="font-extrabold text-ink">Program lainnya</p>
                    <ul class="mt-3 divide-y divide-slate-100">
                        @foreach ($others as $other)
                            <li><a href="{{ route('site.program', $other) }}" class="flex items-center justify-between py-3 text-sm font-semibold text-slate-700 hover:text-brand-700">{{ $other->name }} <x-heroicon-m-chevron-right class="size-4" /></a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </aside>
    </section>
</x-site.layout>
