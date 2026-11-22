<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ReinitialisationMotDePasse extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $minutes = (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)->subject('Réinitialisez votre mot de passe · MY EVC')
            ->view(['html' => 'mail-avis', 'text' => 'mail-avis-texte'], [
                'nom' => $notifiable->name ?? '', 'titre' => 'Réinitialisez votre mot de passe', 'categorie' => 'SÉCURITÉ DE VOTRE COMPTE',
                'introduction' => 'Nous avons reçu une demande de réinitialisation du mot de passe de votre compte MY EVC. Utilisez le bouton ci-dessous pour choisir un nouveau mot de passe.',
                'libelleMiseEnAvant' => 'DURÉE DE VALIDITÉ DU LIEN', 'miseEnAvant' => $minutes.' '.($minutes === 1 ? 'minute' : 'minutes'),
                'details' => [], 'url' => $this->resetUrl($notifiable), 'actionLabel' => 'Réinitialiser mon mot de passe',
                'aideAction' => 'Ce lien personnel fonctionne sans connexion préalable et ne peut être utilisé qu’une seule fois.',
                'etapes' => ['Choisissez un mot de passe unique d’au moins 8 caractères contenant des lettres et des chiffres.', 'Confirmez votre nouveau mot de passe, puis connectez-vous à MY EVC.'],
                'conseil' => 'Vous n’êtes pas à l’origine de cette demande ? Ignorez cet e-mail : cette demande seule ne modifie pas votre mot de passe. Ne partagez jamais ce lien. L’équipe EVC ne vous demandera jamais de lui communiquer votre mot de passe.',
                'signature' => 'La sécurité de votre compte est essentielle.',
                'notePiedDePage' => 'Cet e-mail fait suite à une demande de réinitialisation du mot de passe de votre compte MY EVC.',
            ]);
    }
}
