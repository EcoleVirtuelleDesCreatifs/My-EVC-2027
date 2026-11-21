<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presence extends Model
{
    protected $guarded = ['id'];

    public const STATUSES = ['present' => ['Présent', 'ok'], 'retard' => ['En retard', 'warn'], 'absent' => ['Absent', 'bad']];

    protected function casts(): array
    {
        return ['checked_at' => 'datetime'];
    }

    public function seance(): BelongsTo
    {
        return $this->belongsTo(Seance::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
