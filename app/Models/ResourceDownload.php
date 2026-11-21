<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceDownload extends Model
{
    protected $guarded = ['id'];

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
