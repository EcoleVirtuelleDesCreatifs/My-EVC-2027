<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['published' => 'boolean'];
    }

    public function briefHtml(): string
    {
        return $this->brief_format === 'html' ? \App\Support\ProjectBrief::fromHtml($this->brief) : \App\Support\ProjectBrief::fromText($this->brief);
    }

    public function briefExcerpt(): string
    {
        return $this->brief_format === 'html' ? \App\Support\ProjectBrief::text($this->briefHtml()) : $this->brief;
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }
}
