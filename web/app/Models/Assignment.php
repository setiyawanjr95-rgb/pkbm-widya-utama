<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable(['course_id', 'title', 'instruction', 'deadline', 'file_path', 'file_name', 'file_size'])]
class Assignment extends Model
{
    protected function casts(): array
    {
        return ['deadline' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::saving(function (self $m) {
            if ($m->isDirty('file_path')) {
                if ($old = $m->getOriginal('file_path')) {
                    Storage::disk('public')->delete($old);
                }
                $m->file_size = $m->file_path ? Storage::disk('public')->size($m->file_path) : null;
            }
        });
        static::deleted(fn (self $m) => $m->file_path && Storage::disk('public')->delete($m->file_path));
        static::created(function (Assignment $a) {
            StudentNotification::broadcast(
                $a->course->students()->pluck('id'), 'tugas', 'Tugas baru',
                "{$a->course->name}: {$a->title}, batas {$a->deadline->translatedFormat('d M Y H:i')}",
            );
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
