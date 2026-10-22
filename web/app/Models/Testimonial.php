<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'role', 'quote', 'photo', 'visible'])]
class Testimonial extends Model
{
    protected function casts(): array
    {
        return ['visible' => 'boolean'];
    }
}
