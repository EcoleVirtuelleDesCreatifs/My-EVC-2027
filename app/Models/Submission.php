<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['links' => 'array'];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(SubmissionFile::class);
    }
}
