<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promotion extends Model
{
    protected $guarded = ['id'];

    public const STATUSES = ['ouverte' => 'Inscriptions ouvertes', 'en_cours' => 'En cours', 'terminee' => 'Terminée'];

    protected function casts(): array
    {
        return ['starts_on' => 'date', 'ends_on' => 'date'];
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class);
    }

    public static function current(): ?self
    {
        return static::whereIn('status', ['en_cours', 'ouverte'])->orderByRaw("status = 'en_cours' desc")->orderBy('starts_on')->first();
    }
}
