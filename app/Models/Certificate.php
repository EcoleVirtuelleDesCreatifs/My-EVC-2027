<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['issued_on' => 'date'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function registryRequest(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CertificateRequest::class);
    }

    public static function nextCode(): string
    {
        $prefix = 'EVC-CERT-'.now()->year.'-';
        $last = static::where('code', 'like', $prefix.'%')->orderByDesc('code')->value('code');

        return $prefix.str_pad((string) ((int) substr((string) $last, -4) + 1), 4, '0', STR_PAD_LEFT);
    }

    /** Code saisi par un visiteur : majuscules, espaces et tirets tolérés. */
    public static function normalize(string $code): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]+/', '-', trim($code)));
    }
}
