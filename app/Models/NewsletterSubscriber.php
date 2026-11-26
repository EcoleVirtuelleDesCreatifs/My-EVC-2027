<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['unsubscribed_at' => 'datetime'];

    public function scopeActifs($q)
    {
        return $q->whereNull('unsubscribed_at');
    }

    /* Jeton de désinscription : vérifiable sans être devinable. */
    public function jeton(): string
    {
        return sha1(strtolower($this->email).'|'.config('app.key'));
    }

    public function desinscriptionUrl(): string
    {
        return route('newsletter.desinscription', ['email' => $this->email, 'jeton' => $this->jeton()]);
    }
}
