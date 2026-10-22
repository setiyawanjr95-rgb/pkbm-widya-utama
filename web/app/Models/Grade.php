<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['student_id', 'course_id', 'category', 'title', 'score', 'source_type', 'source_id'])]
class Grade extends Model
{
    public const CATEGORIES = ['Tugas' => 'Tugas', 'Kuis' => 'Kuis', 'Ujian' => 'Ujian'];

    protected function casts(): array
    {
        return ['score' => 'integer'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}
