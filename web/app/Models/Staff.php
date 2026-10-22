<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'position', 'photo', 'sort'])]
class Staff extends Model
{
    protected $table = 'staff';
}
