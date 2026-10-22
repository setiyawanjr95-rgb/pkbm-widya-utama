<x-site.layout title="Kontak">
    <x-site.page-header eyebrow="Kontak" title="Hubungi Kami" subtitle="Punya pertanyaan soal pendaftaran atau program? Kami siap membantu." />

    <section class="container-site relative -mt-10 grid gap-6 pb-12 lg:grid-cols-[1fr_1.3fr]">
        <div class="space-y-4">
            @php
                $items = [
                    ['icon' => 'map-pin', 'label' => 'Alamat', 'value' => $site['address'], 'url' => null],
                    ['icon' => 'phone', 'label' => 'Telepon', 'value' => $site['phone'], 'url' => $site['phone'] ? 'tel:'.preg_replace('/[^\d+]/', '', $site['phone']) : null],
                    ['icon' => 'chat-bubble-left-right', 'label' => 'WhatsApp', 'value' => $site['whatsapp'], 'url' => wa_url($site['whatsapp'])],
                    ['icon' => 'envelope', 'label' => 'Email', 'value' => $site['email'], 'url' => $site['email'] ? 'mailto:'.$site['email'] : null],
                    ['icon' => 'clock', 'label' => 'Jam layanan', 'value' => $site['office_hours'], 'url' => null],
                ];
            @endphp
            @foreach ($items as $item)
                @continue(! $item['value'])
                <div class="card flex gap-4 p-5">
                    <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-brand-100 text-brand-700"><x-dynamic-component :component="'heroicon-o-'.$item['icon']" class="size-6" /></span>
                    <div>
                        <p class="text-sm font-semibold text-muted">{{ $item['label'] }}</p>
                        @if ($item['url'])
                            <a href="{{ $item['url'] }}" class="font-bold text-ink hover:text-brand-700" @if (str_starts_with($item['url'], 'http')) target="_blank" rel="noopener" @endif>{{ $item['value'] }}</a>
                        @else
                            <p class="font-bold text-ink">{{ $item['value'] }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
            @if ($wa = wa_url($site['whatsapp'], 'Halo, saya ingin bertanya tentang program di '.$site['site_name']))
                <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn w-full bg-[#25d366] text-white hover:brightness-95"><x-heroicon-s-chat-bubble-oval-left-ellipsis class="size-5" /> Chat WhatsApp sekarang</a>
            @endif
        </div>
        <div class="card min-h-80 overflow-hidden">
            @if ($site['maps_embed'] && str_starts_with($site['maps_embed'], 'https://www.google.com/maps/embed'))
                <iframe src="{{ $site['maps_embed'] }}" class="size-full min-h-96" style="border:0" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Peta lokasi"></iframe>
            @else
                <div class="grid size-full min-h-96 place-items-center bg-brand-50 p-8 text-center text-muted">
                    <div><x-heroicon-o-map class="mx-auto size-16 text-brand-300" /><p class="mt-3">Peta lokasi bisa diatur dari admin (Pengaturan Website).</p></div>
                </div>
            @endif
        </div>
    </section>
</x-site.layout>
