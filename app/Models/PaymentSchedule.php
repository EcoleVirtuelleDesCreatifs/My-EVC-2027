<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentSchedule extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['due_on' => 'date', 'reminded_at' => 'datetime'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    /** Montant réglé sur cette échéance : les versements sont imputés aux échéances les plus anciennes d'abord. */
    public function paid(): int
    {
        return $this->student->allocation()[$this->id] ?? 0;
    }

    public function remaining(): int
    {
        return max(0, $this->amount - $this->paid());
    }

    /** [libellé, ton] : Payée, En retard, Partielle, À venir. */
    public function state(): array
    {
        return match (true) {
            $this->remaining() === 0 => ['Payée', 'ok'],
            $this->due_on->endOfDay()->isPast() => ['En retard', 'bad'],
            $this->paid() > 0 => ['Partielle', 'warn'],
            default => ['À venir', 'mute'],
        };
    }
}
