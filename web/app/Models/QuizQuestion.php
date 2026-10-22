<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['learning_material_id', 'question', 'options', 'answer', 'sort'])]
class QuizQuestion extends Model
{
    protected function casts(): array
    {
        return ['options' => 'array', 'answer' => 'integer'];
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(LearningMaterial::class, 'learning_material_id');
    }
}
