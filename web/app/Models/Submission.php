<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['assignment_id', 'student_id', 'file_path', 'file_name', 'file_size', 'score', 'feedback', 'graded_at'])]
class Submission extends Model
{
    protected function casts(): array
    {
        return ['graded_at' => 'datetime', 'score' => 'integer'];
    }

    protected static function booted(): void
    {
        // Nilai tugas otomatis masuk ke rekap nilai + notifikasi ke warga belajar.
        static::saved(function (Submission $s) {
            if ($s->score === null || ! $s->wasChanged('score') && ! $s->wasRecentlyCreated) {
                return;
            }
            $assignment = $s->assignment;
            Grade::updateOrCreate(
                ['source_type' => self::class, 'source_id' => $s->id],
                ['student_id' => $s->student_id, 'course_id' => $assignment->course_id, 'category' => 'Tugas',
                    'title' => $assignment->title, 'score' => $s->score],
            );
            StudentNotification::create([
                'student_id' => $s->student_id, 'type' => 'nilai', 'title' => 'Tugas sudah dinilai',
                'body' => "{$assignment->title}: nilai {$s->score}",
            ]);
        });
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
