<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre préinscription · EVC</title>
</head>
<body style="margin:0;padding:0;background-color:#F4F3F8;font-family:Arial,Helvetica,sans-serif;color:#1E1B4B;-webkit-text-size-adjust:100%;">
    <div style="display:none;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;">Votre dossier {{ $candidature->reference }} : {{ $titre ?? 'préinscription reçue' }}. Découvrez les prochaines étapes.</div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F4F3F8;">
        <tr><td align="center" style="padding:32px 12px;">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:100%;max-width:600px;">
                <tr><td style="padding:0 12px 24px;">
                    <p style="margin:0;font-size:28px;font-weight:bold;letter-spacing:-1px;color:#1E1B4B;">MY <span style="color:#C2401F;">EVC</span></p>
                    <p style="margin:6px 0 0;font-size:12px;line-height:18px;color:#5E6470;">École Virtuelle des Créatifs</p>
                </td></tr>
                <tr><td style="background-color:#FFFFFF;border-radius:16px;overflow:hidden;border:1px solid #E4E2F3;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                        <tr><td style="padding:30px 28px;background-color:#1E1B4B;border-top:5px solid #EE5A36;">
                            <p style="margin:0 0 12px;font-size:11px;line-height:16px;letter-spacing:2px;font-weight:bold;color:#FF8A6B;">ADMISSIONS · PRÉINSCRIPTION</p>
                            <h1 style="margin:0;font-size:28px;line-height:36px;font-weight:bold;color:#FFFFFF;">{{ $titre ?? 'Votre préinscription est confirmée' }}</h1>
                            <p style="margin:12px 0 0;font-size:15px;line-height:23px;color:#E4E2F3;">{{ isset($messageEtape) ? 'Suivez les nouvelles étapes de votre candidature.' : 'La première étape de votre parcours créatif est franchie.' }}</p>
                        </td></tr>
                        <tr><td style="padding:28px;">
                            <p style="margin:0 0 16px;font-size:18px;line-height:26px;font-weight:bold;">Bonjour {{ $candidature->first_name }},</p>
                            <p style="margin:0 0 24px;font-size:15px;line-height:25px;color:#5E6470;">{{ $messageEtape ?? 'Merci pour votre confiance. Nous avons bien reçu votre dossier de préinscription à l’École Virtuelle des Créatifs.' }}</p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F4F3F8;border:1px solid #E4E2F3;border-radius:10px;">
                                <tr><td style="padding:20px;">
                                    <p style="margin:0 0 8px;font-size:11px;letter-spacing:1px;font-weight:bold;color:#5E6470;">VOTRE RÉFÉRENCE DE DOSSIER</p>
                                    <p style="margin:0;font-family:Consolas,'Courier New',monospace;font-size:23px;line-height:30px;font-weight:bold;color:#1E1B4B;">{{ $candidature->reference }}</p>
                                    <p style="margin:8px 0 0;font-size:12px;line-height:19px;color:#5E6470;">Conservez cette référence pour vos échanges avec notre équipe.</p>
                                </td></tr>
                            </table>
                            @isset($questionnaireUrl)
                            <table role="presentation" cellpadding="0" cellspacing="0" align="center" style="margin:24px auto;">
                                <tr><td bgcolor="#1E1B4B" style="border-radius:8px;"><a href="{{ $questionnaireUrl }}" style="display:inline-block;padding:16px 24px;color:#FFFFFF;font-size:15px;font-weight:bold;text-decoration:none;">Accéder à mon questionnaire SAOP</a></td></tr>
                            </table>
                            <p style="font-size:12px;line-height:19px;color:#5E6470;">30 minutes au démarrage. Si le bouton ne fonctionne pas, copiez ce lien personnel :<br><a href="{{ $questionnaireUrl }}" style="color:#3A3591;word-break:break-all;">{{ $questionnaireUrl }}</a></p>
                            @endisset
                            <h2 style="margin:26px 0 16px;font-size:18px;line-height:26px;">Et maintenant ?</h2>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:23px;color:#5E6470;">
                                @unless (isset($messageEtape))
                                <tr><td width="30" valign="top" style="padding:0 8px 16px 0;font-weight:bold;color:#C2401F;">01</td><td style="padding-bottom:16px;"><strong style="color:#1E1B4B;">Étude de votre dossier</strong><br>Un conseiller examine votre profil sous 48 heures et vous recontacte.</td></tr>
                                @endunless
                                <tr><td width="30" valign="top" style="padding:0 8px 0 0;font-weight:bold;color:#C2401F;">02</td><td><strong style="color:#1E1B4B;">Suivi de votre candidature</strong><br>Consultez l’avancement de votre dossier grâce à votre lien personnel ci-dessous.</td></tr>
                            </table>
                            <table role="presentation" cellpadding="0" cellspacing="0" align="center" style="margin:28px auto 16px;">
                                <tr><td align="center" bgcolor="#C2401F" style="border-radius:8px;background-color:#C2401F;"><a href="{{ $suiviUrl }}" style="display:inline-block;padding:16px 24px;border:1px solid #C2401F;border-radius:8px;font-size:15px;font-weight:bold;line-height:20px;color:#FFFFFF;text-decoration:none;">Suivre ma candidature</a></td></tr>
                            </table>
                            <p style="margin:0 0 28px;text-align:center;font-size:12px;line-height:19px;color:#5E6470;">Ce lien est personnel et valable pendant 90 jours.<br>Pour protéger votre dossier, ne le partagez pas.</p>
                            <p style="margin:0;font-size:14px;line-height:23px;color:#5E6470;">À très bientôt,<br><strong style="color:#1E1B4B;">L’équipe des admissions EVC</strong></p>
                        </td></tr>
                        <tr><td style="padding:20px 28px;border-top:1px solid #E4E2F3;background-color:#FAFAFC;">
                            <p style="margin:0 0 8px;font-size:12px;line-height:19px;color:#5E6470;">Le bouton ne fonctionne pas ? Copiez ce lien dans votre navigateur :</p>
                            <p style="margin:0;font-size:11px;line-height:18px;word-break:break-all;overflow-wrap:anywhere;"><a href="{{ $suiviUrl }}" style="color:#3A3591;text-decoration:underline;word-break:break-all;">{{ $suiviUrl }}</a></p>
                        </td></tr>
                    </table>
                </td></tr>
                <tr><td align="center" style="padding:24px 16px;font-size:11px;line-height:19px;color:#5E6470;">Cet e-mail vous est adressé à la suite de votre préinscription à l’EVC.<br>© {{ date('Y') }} École Virtuelle des Créatifs. Tous droits réservés.</td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
