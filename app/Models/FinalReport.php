<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Rapport de fin de formation déposé par l'étudiant (un par formation). */
class FinalReport extends Model
{
    protected $guarded = ['id'];

    /** Statut => [libellé, ton]. « manquant » : aucun rapport déposé. */
    public const STATUSES = [
        'manquant' => ['À déposer', 'warn'],
        'depose' => ['Déposé, en relecture', 'info'],
        'a_reprendre' => ['À compléter', 'bad'],
        'valide' => ['Validé', 'ok'],
    ];

    /** Formats acceptés et taille maximale (Ko). */
    public const MIMES = 'pdf,doc,docx,odt,ppt,pptx';

    public const MAX_KB = 20480;

    protected function casts(): array
    {
        return ['submitted_at' => 'datetime', 'reviewed_at' => 'datetime'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public static function label(?self $report): array
    {
        return self::STATUSES[$report?->status ?? 'manquant'];
    }
}
