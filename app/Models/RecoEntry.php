<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecoEntry extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['entry_date' => 'date', 'feedback_at' => 'datetime', 'hours' => 'float'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(RecoProject::class, 'reco_project_id');
    }

    public function feedbackAuthor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'feedback_by');
    }
}
