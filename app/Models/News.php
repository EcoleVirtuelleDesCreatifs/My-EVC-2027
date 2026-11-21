<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['featured' => 'boolean', 'published_at' => 'datetime'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished($q)
    {
        return $q->whereNotNull('published_at')->where('published_at', '<=', now())->orderByDesc('published_at');
    }

    public function readingTime(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->body)) / 220));
    }
}
