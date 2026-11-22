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

    public static function sanitizeBody(string $body): string
    {
        $body = trim($body);
        if (! preg_match('/<\/?[a-z][^>]*>/i', $body)) {
            $body = collect(preg_split('/\n\s*\n/', $body))->map(fn ($p) => '<p>'.nl2br(e(trim($p))).'</p>')->implode("\n");
        }

        return \App\Support\ProjectBrief::sanitize($body);
    }

    public function bodyHtml(): string
    {
        return self::sanitizeBody($this->body ?? '');
    }

    public function readingTime(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->body)) / 220));
    }
}
