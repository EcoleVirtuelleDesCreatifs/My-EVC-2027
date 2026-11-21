<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** E-mail d'activation : l'étudiant choisit son mot de passe (jeton de réinitialisation Laravel). */
class CompteActive extends Notification
{
    public function __construct(public string $token, public string $matricule) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bienvenue à l\'EVC : activez votre espace MY EVC')
            ->greeting('Bienvenue '.explode(' ', $notifiable->name)[0].' !')
            ->line('Votre inscription est confirmée. Votre matricule : **'.$this->matricule.'**.')
            ->line('Choisissez votre mot de passe pour accéder à MY EVC : vos cours, vos projets, la bibliothèque et vos paiements.')
            ->action('Activer mon compte', route('password.reset', ['token' => $this->token, 'email' => $notifiable->email, 'activation' => 1]))
            ->line('Ce lien est valable 7 jours.')
            ->salutation('L\'équipe de l\'EVC');
    }
}
