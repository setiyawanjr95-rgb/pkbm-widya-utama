<x-site.layout title="Pendaftaran Berhasil">
    <section class="container-site max-w-2xl py-20 text-center">
        <span class="mx-auto grid size-20 place-items-center rounded-full bg-green-100 text-green-600"><x-heroicon-s-check-circle class="size-12" /></span>
        <h1 class="mt-6 text-3xl font-extrabold text-ink">Pendaftaran berhasil dikirim!</h1>
        <p class="mt-3 text-lg text-muted">Terima kasih, {{ $registration->name }}. Tim kami akan menghubungi kamu lewat WhatsApp di nomor <strong class="text-ink">{{ $registration->phone }}</strong>.</p>
        <div class="card mt-8 p-6 text-left">
            <dl class="grid gap-3 text-sm sm:grid-cols-2">
                <div><dt class="text-muted">Kode pendaftaran</dt><dd class="text-lg font-extrabold text-brand-700">{{ $registration->code }}</dd></div>
                <div><dt class="text-muted">Program</dt><dd class="text-lg font-bold text-ink">{{ $registration->program?->name }}</dd></div>
            </dl>
            <p class="mt-4 text-xs text-muted">Simpan kode ini, sebutkan saat menghubungi kami.</p>
        </div>
        @if ($wa = wa_url($site['whatsapp'], "Halo, saya sudah daftar online dengan kode {$registration->code} atas nama {$registration->name}."))
            <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn mt-8 bg-[#25d366] text-white hover:brightness-95"><x-heroicon-s-chat-bubble-oval-left-ellipsis class="size-5" /> Konfirmasi via WhatsApp</a>
        @endif
        <div class="mt-4"><a href="{{ route('site.home') }}" class="text-sm font-bold text-brand-600">&larr; Kembali ke beranda</a></div>
    </section>
</x-site.layout>
