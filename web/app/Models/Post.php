<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['title', 'slug', 'category', 'excerpt', 'body', 'cover', 'published_at'])]
class Post extends Model
{
    public const CATEGORIES = ['Berita' => 'Berita', 'Pengumuman' => 'Pengumuman', 'Kegiatan' => 'Kegiatan'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::saving(function (Post $p) {
            if (! $p->slug) {
                $base = Str::slug($p->title) ?: 'berita';
                $slug = $base;
                for ($i = 2; self::where('slug', $slug)->whereKeyNot($p->getKey())->exists(); $i++) {
                    $slug = "$base-$i";
                }
                $p->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): void
    {
        $query->whereNotNull('published_at')->where('published_at', '<=', now())->latest('published_at');
    }

    public function getSummaryAttribute(): string
    {
        return $this->excerpt ?: Str::limit(strip_tags($this->body), 160);
    }
}
