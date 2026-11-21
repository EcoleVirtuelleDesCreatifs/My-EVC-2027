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
    ) {}

    public function via(object $notifiable): array
    {
        return $this->mail ? ['database', 'mail'] : ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $m = (new MailMessage)->subject($this->title.' · MY EVC')->greeting('Bonjour,')->line($this->body);
        if ($this->url) {
            $m->action('Ouvrir MY EVC', $this->url);
        }

        return $m->salutation('L\'équipe de l\'École Virtuelle des Créatifs');
    }

    public function toArray(object $notifiable): array
    {
        return ['title' => $this->title, 'body' => $this->body, 'url' => $this->url, 'icon' => $this->icon, 'tone' => $this->tone];
    }
}
