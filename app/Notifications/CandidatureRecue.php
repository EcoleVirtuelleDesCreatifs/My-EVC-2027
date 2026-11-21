<?php

namespace App\Notifications;

use App\Http\Controllers\Site\FormController;
use App\Models\Candidature;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CandidatureRecue extends Notification
{
    public function __construct(public Candidature $candidature) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $c = $this->candidature;

        return (new MailMessage)
            ->subject('Votre préinscription '.$c->reference.' · EVC')
            ->greeting('Bonjour '.$c->first_name.',')
            ->line('Nous avons bien reçu votre préinscription à l\'École Virtuelle des Créatifs.')
            ->line('Votre référence : **'.$c->reference.'**. Un conseiller étudie votre profil sous 48 heures et vous recontacte.')
            ->action('Suivre ma candidature', FormController::suiviUrl($c))
            ->salutation('L\'équipe de l\'EVC');
    }
}
