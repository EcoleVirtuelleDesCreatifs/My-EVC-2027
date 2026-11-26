<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Outil de démarrage : logiciel, support PDF ou programme de formation, en fichier hébergé ou en lien officiel. */
class StarterTool extends Model
{
    protected $guarded = ['id'];

    /** Type => [libellé au pluriel, libellé, icône, description de la rubrique]. */
    public const KINDS = [
        'logiciel' => ['Logiciels', 'Logiciel', 'fa-laptop-code', 'À installer avant le premier cours.'],
        'support' => ['Supports de cours', 'Support PDF', 'fa-file-pdf', 'Guides et fiches à garder sous la main.'],
        'programme' => ['Programmes de formation', 'Programme PDF', 'fa-list-check', 'Le déroulé de votre formation, module par module.'],
        'autre' => ['Autres ressources', 'Ressource', 'fa-toolbox', 'Ressources complémentaires.'],
    ];

    public const MIMES = 'pdf,zip,rar,7z,dmg,exe,msi,pkg,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png';

    public const MAX_KB = 512000;

    protected function casts(): array
    {
        return ['published' => 'boolean', 'size' => 'integer'];
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function scopeVisibleFor(Builder $q, Student $s): Builder
    {
        return $q->where('published', true)->where(fn ($w) => $w->whereNull('formation_id')->orWhereIn('formation_id', $s->formations->pluck('id')))
            ->orderBy('position')->orderBy('title');
    }

    public function extension(): string
    {
        return strtoupper(pathinfo((string) $this->original_name, PATHINFO_EXTENSION));
    }
}
