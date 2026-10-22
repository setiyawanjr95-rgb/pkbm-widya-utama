@props(['icon' => 'book'])
@php
    $map = [
        'paket-a' => 'heroicon-o-pencil', 'paket-b' => 'heroicon-o-book-open', 'paket-c' => 'heroicon-o-academic-cap',
        'skill' => 'heroicon-o-wrench-screwdriver', 'computer' => 'heroicon-o-computer-desktop', 'book' => 'heroicon-o-book-open',
    ];
@endphp
<x-dynamic-component :component="$map[$icon] ?? 'heroicon-o-book-open'" {{ $attributes }} />
