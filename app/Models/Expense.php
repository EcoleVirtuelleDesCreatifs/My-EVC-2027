<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Dépense de l'école, sortie d'une caisse (espèces, mobile money ou banque). */
class Expense extends Model
{
    protected $guarded = ['id'];

    public const CATEGORIES = [
        'Loyer et charges', 'Salaires et honoraires', 'Formateurs et intervenants', 'Marketing et publicité', 'Matériel et équipement',
        'Internet, logiciels et abonnements', 'Transport et déplacements', 'Fournitures et impressions', 'Événements et cérémonies',
        'Impôts, taxes et frais bancaires', 'Divers',
    ];

    protected function casts(): array
    {
        return ['spent_on' => 'date', 'amount' => 'integer'];
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
