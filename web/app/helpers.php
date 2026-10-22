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
