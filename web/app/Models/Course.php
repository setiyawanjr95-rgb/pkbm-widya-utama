<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'paket', 'kelas', 'tutor', 'description', 'icon', 'color', 'finished'])]
class Course extends Model
{
    public const ICONS = [
        'calculate' => 'Matematika', 'menu_book' => 'Bahasa / Buku', 'translate' => 'Bahasa Asing',
        'science' => 'IPA', 'public' => 'IPS / Geografi', 'flag' => 'Pancasila / PKn',
        'mosque' => 'Agama', 'palette' => 'Seni', 'computer' => 'Komputer', 'sports' => 'Olahraga',
        'work' => 'Keterampilan', 'book' => 'Umum',
    ];

    public const COLORS = ['primary' => 'Biru', 'success' => 'Hijau', 'danger' => 'Merah', 'warning' => 'Oranye'];

    protected function casts(): array
    {
        return ['finished' => 'boolean'];
    }

    public function getLabelAttribute(): string
    {
        return "{$this->name} ({$this->paket} · {$this->kelas})";
    }

    public function materials(): HasMany
    {
        return $this->hasMany(LearningMaterial::class)->orderBy('sort')->orderBy('id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function students()
    {
        return Student::query()->inGroup($this->paket, $this->kelas);
    }
}
