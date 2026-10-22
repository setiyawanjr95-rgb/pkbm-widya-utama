<x-site.layout title="Galeri">
    <x-site.page-header eyebrow="Galeri" title="Galeri Kegiatan" subtitle="Dokumentasi kegiatan belajar, ujian, wisuda, dan kegiatan lain di PKBM." />

    <section class="container-site relative -mt-10 pb-12">
        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
            @forelse ($items as $item)
                <a href="{{ media_url($item->image) }}" data-lightbox data-title="{{ $item->title }}{{ $item->caption ? ' — '.$item->caption : '' }}" class="group relative aspect-square overflow-hidden rounded-2xl bg-brand-100">
                    <img src="{{ media_url($item->image) }}" alt="{{ $item->title }}" class="size-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                    <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-3 text-sm font-semibold text-white opacity-0 transition group-hover:opacity-100">{{ $item->title }}</span>
                </a>
            @empty
                <p class="col-span-full py-16 text-center text-muted">Foto kegiatan belum ditambahkan.</p>
            @endforelse
        </div>
        <div class="mt-10">{{ $items->links() }}</div>
    </section>

    <dialog id="lightbox" class="m-auto max-h-[90vh] max-w-5xl bg-transparent p-0 backdrop:bg-black/80">
        <div class="relative">
            <button type="button" data-close class="absolute -top-12 right-0 rounded-full bg-white/10 p-2 text-white hover:bg-white/20" aria-label="Tutup"><x-heroicon-o-x-mark class="size-6" /></button>
            <img src="" alt="" class="max-h-[80vh] w-auto rounded-2xl">
            <p data-caption class="mt-3 text-center text-sm text-white"></p>
        </div>
    </dialog>
</x-site.layout>
