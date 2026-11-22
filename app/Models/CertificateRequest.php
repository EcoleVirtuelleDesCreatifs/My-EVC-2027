<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateRequest extends Model
{
    protected $guarded = ['id'];

    public const STATUSES = [
        'a_transmettre' => 'À transmettre au registre', 'brouillon' => 'Dossier reçu par le registre',
        'en_verification' => 'En vérification', 'approuve' => 'Approuvé, publication en attente',
        'publie' => 'Publié au registre', 'rejete' => 'Dossier à corriger', 'revoque' => 'Certificat révoqué',
    ];

    protected function casts(): array
    {
        return ['synced_at' => 'datetime', 'notified_at' => 'datetime', 'registry_document_id' => 'integer'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? 'Statut à vérifier';
    }
}
