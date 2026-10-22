<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Pendaftaran calon warga belajar dari formulir di website. */
#[Fillable(['code', 'program_id', 'name', 'nik', 'birth_place', 'birth_date', 'gender', 'phone', 'email', 'address', 'last_education', 'notes', 'status', 'admin_notes'])]
class Registration extends Model
{
    public const STATUSES = ['baru' => 'Baru', 'dihubungi' => 'Sudah dihubungi', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak'];

    public const EDUCATION = [
        'Tidak sekolah' => 'Tidak sekolah', 'SD tidak tamat' => 'SD tidak tamat', 'SD / sederajat' => 'SD / sederajat',
        'SMP tidak tamat' => 'SMP tidak tamat', 'SMP / sederajat' => 'SMP / sederajat', 'SMA tidak tamat' => 'SMA tidak tamat',
    ];

    protected function casts(): array
    {
        return ['birth_date' => 'date'];
    }

    protected static function booted(): void
    {
        static::creating(function (Registration $r) {
            $r->code ??= 'PKBM-'.now()->format('ymd').'-'.strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
        });
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /** Link WhatsApp untuk menghubungi pendaftar. */
    public function whatsappUrl(): string
    {
        $number = preg_replace('/\D/', '', $this->phone);
        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        }

        return "https://wa.me/{$number}";
    }
}
