<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidatureFile extends Model
{
    protected $guarded = ['id'];

    public function candidature(): BelongsTo
    {
        return $this->belongsTo(Candidature::class);
    }
}
