<?php

namespace App\Notifications;

use App\Models\Candidature;
use Illuminate\Notifications\Messages\MailMessage;

class CandidatureMiseAJour extends CandidatureRecue
{
    public function __construct(Candidature $candidature, public string $stage)
    {
        parent::__construct($candidature);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = parent::toMail($notifiable);
        $mail->subject('Votre candidature '.$this->candidature->reference.' : '.Candidature::STAGES[$this->stage][0]);
        $mail->viewData['titre'] = Candidature::STAGES[$this->stage][0];
        $mail->viewData['messageEtape'] = match ($this->stage) {
            'eligible' => 'Votre profil est éligible. Complétez votre questionnaire SAOP : vous disposez de 30 minutes à partir du clic sur « Démarrer le questionnaire ». Vos réponses seront évaluées par notre équipe.',
            'test' => 'Félicitations, vous avez réussi votre test d’admission. La prochaine étape est le premier versement. Notre équipe reste à votre disposition pour les modalités de paiement.',
            'paiement' => 'Nous confirmons la réception de votre premier versement. Notre équipe prépare votre affectation à une promotion et l’activation de votre compte MY EVC.',
            'refusee' => 'Votre candidature n’a pas été retenue pour cette session. Un conseiller peut vous orienter vers une autre formation ou une prochaine rentrée.',
            default => 'Votre dossier a bien été reçu. Notre équipe étudie votre profil et vous recontactera.',
        };

        if ($this->stage === 'eligible') {
            $mail->viewData['questionnaireUrl'] = $this->candidature->saopUrl();
        }

        return $mail;
    }
}
