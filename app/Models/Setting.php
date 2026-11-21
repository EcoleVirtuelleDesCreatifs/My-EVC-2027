<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $guarded = [];

    /** Paramètres modifiables dans l'administration : clé => [libellé, valeur par défaut]. */
    public const DEFAULTS = [
        'school_email' => ['E-mail de l\'école', 'info@ecolevirtuelledescreatifs.com'],
        'whatsapp_ci' => ['WhatsApp Côte d\'Ivoire (sans +)', '2250747259507'],
        'whatsapp_fr' => ['WhatsApp France (sans +)', '33605668823'],
        'phone' => ['Téléphone (appels)', '+225 07 17 25 86 02'],
        'address' => ['Adresse', 'Abidjan, Côte d\'Ivoire'],
        'director' => ['Directeur général (signature des documents)', 'Bilé Bossombra'],
        'rccm' => ['Numéro RCCM', ''],
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        $all = Cache::rememberForever('settings', fn () => static::pluck('value', 'key')->all());

        return $all[$key] ?? $default ?? (self::DEFAULTS[$key][1] ?? null);
    }

    public static function put(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('settings');
    }
}
