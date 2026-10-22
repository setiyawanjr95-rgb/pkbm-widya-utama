<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'level', 'summary', 'body', 'features', 'duration', 'image', 'icon', 'open_registration', 'sort'])]
class Program extends Model
{
    protected function casts(): array
    {
        return ['features' => 'array', 'open_registration' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saving(fn (Program $p) => $p->slug = $p->slug ?: Str::slug($p->name));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
