MY EVC — École Virtuelle des Créatifs

{{ $titre ?? 'Votre préinscription est confirmée' }}

Bonjour {{ $candidature->first_name }},

{{ $messageEtape ?? 'Merci pour votre confiance. Nous avons bien reçu votre dossier de préinscription à l’École Virtuelle des Créatifs.' }}

Votre référence de dossier : {{ $candidature->reference }}
Conservez cette référence pour vos échanges avec notre équipe.

@isset($questionnaireUrl)
Accéder à mon questionnaire SAOP (30 minutes au démarrage) :
{!! $questionnaireUrl !!}
@endisset

Et maintenant ?

@unless (isset($messageEtape))
1. Étude de votre dossier : un conseiller examine votre profil sous 48 heures et vous recontacte.
@endunless
2. Suivi de votre candidature : consultez l’avancement de votre dossier grâce à votre lien personnel.

Suivre ma candidature :
{!! $suiviUrl !!}

Ce lien est personnel et valable pendant 90 jours. Pour protéger votre dossier, ne le partagez pas.

À très bientôt,
L’équipe des admissions EVC

Cet e-mail vous est adressé à la suite de votre préinscription à l’EVC.
© {{ date('Y') }} École Virtuelle des Créatifs. Tous droits réservés.
