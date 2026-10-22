<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['student_id', 'type', 'title', 'body', 'read_at'])]
class StudentNotification extends Model
{
    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /** Kirim notifikasi yang sama ke banyak warga belajar sekaligus. */
    public static function broadcast(iterable $studentIds, string $type, string $title, string $body): void
    {
        $now = now();
        $rows = [];
        foreach ($studentIds as $id) {
            $rows[] = ['student_id' => $id, 'type' => $type, 'title' => $title, 'body' => $body,
                'created_at' => $now, 'updated_at' => $now];
        }
        foreach (array_chunk($rows, 500) as $chunk) {
            self::insert($chunk);
        }
    }
}
