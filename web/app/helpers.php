<?php

if (! function_exists('format_bytes')) {
    function format_bytes(?int $bytes): string
    {
        if (! $bytes) {
            return '';
        }

        return $bytes >= 1048576
            ? number_format($bytes / 1048576, 1).' MB'
            : max(1, round($bytes / 1024)).' KB';
    }
}

if (! function_exists('media_url')) {
    /** URL publik file upload (disk public), atau null kalau kosong. */
    function media_url(?string $path): ?string
    {
        return $path ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : null;
    }
}

if (! function_exists('wa_url')) {
    /** Ubah nomor 08xx menjadi link wa.me/628xx. */
    function wa_url(?string $number, string $text = ''): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $number);
        if ($digits === '') {
            return null;
        }
        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        }

        return "https://wa.me/{$digits}".($text !== '' ? '?text='.rawurlencode($text) : '');
    }
}
