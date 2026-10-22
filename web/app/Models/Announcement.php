<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'body', 'paket', 'published_at'])]
class Announcement extends Model
{
    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::created(function (Announcement $a) {
            if ($a->published_at) {
                StudentNotification::broadcast(
                    Student::query()->inGroup($a->paket)->pluck('id'), 'pengumuman', $a->title, str($a->body)->limit(120),
                );
            }
        });
    }

    public function scopeVisibleTo(Builder $query, Student $student): void
    {
        $query->whereNotNull('published_at')->where('published_at', '<=', now())
            ->where(fn ($q) => $q->whereNull('paket')->orWhere('paket', $student->paket));
    }
}
