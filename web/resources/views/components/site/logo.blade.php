@props(['light' => false])
@if ($site['logo'])
    <img src="{{ media_url($site['logo']) }}" alt="Logo {{ $site['site_name'] }}" {{ $attributes->merge(['class' => 'rounded-xl object-contain']) }}>
@else
    <span {{ $attributes->merge(['class' => 'grid place-items-center rounded-xl '.($light ? 'bg-white text-brand-700' : 'bg-brand-600 text-white')]) }}>
        <x-heroicon-s-academic-cap class="size-3/5" />
    </span>
@endif
