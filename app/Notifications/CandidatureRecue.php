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
            ->view(['html' => 'site.mail-preinscription', 'text' => 'site.mail-preinscription-texte'], [
                'candidature' => $c,
                'suiviUrl' => FormController::suiviUrl($c),
            ]);
    }
}
