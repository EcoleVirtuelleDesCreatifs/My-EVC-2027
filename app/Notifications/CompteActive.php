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
            ->view(['html' => 'auth.mail-activation', 'text' => 'auth.mail-activation-texte'], [
                'prenom' => explode(' ', trim($notifiable->name))[0],
                'matricule' => $this->matricule,
                'activationUrl' => route('password.reset', ['token' => $this->token, 'email' => $notifiable->email, 'activation' => 1]),
            ]);
    }
}
