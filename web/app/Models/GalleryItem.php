<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'image', 'caption', 'taken_at'])]
class GalleryItem extends Model
{
    protected function casts(): array
    {
        return ['taken_at' => 'date'];
    }
}
