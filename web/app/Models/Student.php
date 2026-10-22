<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'nisn', 'paket', 'kelas', 'phone', 'address', 'active'])]
class Student extends Model
{
    public const PAKET = ['Paket A' => 'Paket A (SD)', 'Paket B' => 'Paket B (SMP)', 'Paket C' => 'Paket C (SMA)'];

    public const KELAS = [
        'Paket A' => ['Kelas 1', 'Kelas 2', 'Kelas 3', 'Kelas 4', 'Kelas 5', 'Kelas 6'],
        'Paket B' => ['Kelas 7', 'Kelas 8', 'Kelas 9'],
        'Paket C' => ['Kelas 10', 'Kelas 11', 'Kelas 12'],
    ];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public static function kelasOptions(?string $paket = null): array
    {
        $list = $paket ? (self::KELAS[$paket] ?? []) : array_merge(...array_values(self::KELAS));

        return array_combine($list, $list);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(StudentNotification::class);
    }

    public function completedMaterials(): BelongsToMany
    {
        return $this->belongsToMany(LearningMaterial::class, 'material_completions')->withTimestamps();
    }

    /** Mapel yang diikuti: sesuai paket & kelas warga belajar. */
    public function courses(): Builder
    {
        return Course::query()->where('paket', $this->paket)->where('kelas', $this->kelas);
    }

    public function scopeInGroup(Builder $query, ?string $paket, ?string $kelas = null): void
    {
        $query->where('active', true)
            ->when($paket, fn ($q) => $q->where('paket', $paket))
            ->when($kelas, fn ($q) => $q->where('kelas', $kelas));
    }
}
