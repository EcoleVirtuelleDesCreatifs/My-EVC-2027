<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Notifications\Messages\MailMessage;

class CertificatDelivre extends Avis
{
    public function __construct(public Certificate $certificat)
    {
        parent::__construct('Certificat délivré', 'Félicitations ! Votre certificat « '.$certificat->formation->name.' » est disponible ('.$certificat->code.').',
            route('etudiant.certificat'), 'fa-award', 'ok', true);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->subject('Votre certificat est disponible · MY EVC')
            ->view(['html' => 'mail-certificat', 'text' => 'mail-certificat-texte'], [
                'nom' => $notifiable->name,
                'formation' => $this->certificat->formation->name,
                'matricule' => $this->certificat->student->matricule,
                'reference' => $this->certificat->code,
                'appreciation' => $this->certificat->appreciation,
                'dateDelivrance' => $this->certificat->issued_on->locale('fr')->translatedFormat('j F Y'),
                'certificatUrl' => $this->url,
            ]);
    }
}
