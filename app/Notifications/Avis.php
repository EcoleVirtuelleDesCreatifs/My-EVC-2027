<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification générique de MY EVC : enregistrée dans la cloche (base de données)
 * et, si demandé, envoyée aussi par e-mail.
 */
class Avis extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body,
        public ?string $url = null,
        public string $icon = 'fa-bell',
        public string $tone = 'lav',
        public bool $mail = false,
        public array $mailData = [],
    ) {}

    public function via(object $notifiable): array
    {
        return $this->mail ? ['database', 'mail'] : ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->subject($this->title.' · MY EVC')
            ->view(['html' => 'mail-avis', 'text' => 'mail-avis-texte'], array_replace([
                'nom' => $notifiable->name ?? '', 'titre' => $this->title, 'categorie' => 'VOTRE ESPACE MY EVC',
                'introduction' => $this->body, 'miseEnAvant' => null, 'libelleMiseEnAvant' => '',
                'details' => [], 'etapes' => [], 'conseil' => null, 'actionLabel' => 'Ouvrir MY EVC',
            ], $this->mailData, ['url' => $this->url]));
    }

    public function toArray(object $notifiable): array
    {
        return ['title' => $this->title, 'body' => $this->body, 'url' => $this->url, 'icon' => $this->icon, 'tone' => $this->tone];
    }
}
