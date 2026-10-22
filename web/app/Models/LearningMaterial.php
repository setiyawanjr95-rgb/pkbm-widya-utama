<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable(['course_id', 'title', 'type', 'description', 'file_path', 'file_name', 'file_size', 'url', 'duration_minutes', 'sort'])]
class LearningMaterial extends Model
{
    public const TYPES = ['pdf' => 'PDF / Dokumen', 'video' => 'Video', 'quiz' => 'Kuis'];

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
        static::created(function (LearningMaterial $m) {
            StudentNotification::broadcast(
                $m->course->students()->pluck('id'), 'materi', 'Materi baru', "{$m->course->name}: {$m->title}",
            );
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort')->orderBy('id');
    }

    /** Teks kecil di bawah judul, contoh "PDF · 2.4 MB" atau "Kuis · 10 soal". */
    public function meta(): string
    {
        return match ($this->type) {
            'video' => 'Video'.($this->duration_minutes ? " · {$this->duration_minutes} menit" : ''),
            'quiz' => 'Kuis · '.$this->questions()->count().' soal',
            default => 'PDF'.($this->file_size ? ' · '.format_bytes($this->file_size) : ''),
        };
    }
}
