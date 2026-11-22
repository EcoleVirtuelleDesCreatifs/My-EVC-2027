<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>Votre certificat est disponible · MY EVC</title>
</head>
<body style="margin:0;padding:0;background-color:#F4F3F8;color:#1E1B4B;font-family:Arial,Helvetica,sans-serif;-webkit-text-size-adjust:100%;">
    <div style="display:none;max-height:0;max-width:0;overflow:hidden;opacity:0;font-size:1px;line-height:1px;mso-hide:all;">Félicitations {{ $nom }} ! Retrouvez votre certificat {{ $reference }} dans votre espace étudiant.</div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F4F3F8;">
        <tr><td align="center" style="padding:32px 12px;">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:100%;max-width:600px;">
                <tr><td style="padding:0 12px 24px;">
                    <p style="margin:0;font-size:28px;font-weight:bold;letter-spacing:-1px;color:#1E1B4B;">MY <span style="color:#C2401F;">EVC</span></p>
                    <p style="margin:6px 0 0;font-size:12px;line-height:18px;color:#5E6470;">École Virtuelle des Créatifs</p>
                </td></tr>
                <tr><td style="background-color:#FFFFFF;border:1px solid #E4E2F3;border-radius:16px;overflow:hidden;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                        <tr><td style="padding:30px 28px;background-color:#1E1B4B;border-top:5px solid #EE5A36;">
                            <p style="margin:0 0 12px;font-size:11px;line-height:16px;letter-spacing:2px;font-weight:bold;color:#FF8A6B;">CERTIFICAT DÉLIVRÉ · MY EVC</p>
                            <h1 style="margin:0;font-size:28px;line-height:36px;color:#FFFFFF;">Votre certificat est disponible</h1>
                            <p style="margin:12px 0 0;font-size:15px;line-height:24px;color:#E4E2F3;">Une étape importante de votre parcours créatif.</p>
                        </td></tr>
                        <tr><td style="padding:28px;">
                            <p style="margin:0 0 16px;font-size:19px;line-height:27px;font-weight:bold;">Félicitations {{ $nom }} !</p>
                            <p style="margin:0 0 24px;font-size:15px;line-height:25px;color:#5E6470;">Votre certificat pour la formation <strong style="color:#1E1B4B;">{{ $formation }}</strong> a été délivré. Il est désormais accessible depuis votre espace étudiant MY EVC.</p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F4F3F8;border:1px solid #E4E2F3;border-radius:10px;">
                                <tr><td style="padding:20px;">
                                    <p style="margin:0 0 8px;font-size:11px;line-height:18px;letter-spacing:1px;font-weight:bold;color:#5E6470;">RÉFÉRENCE DE VOTRE CERTIFICAT</p>
                                    <p style="margin:0;font-size:22px;line-height:32px;font-family:Consolas,'Courier New',monospace;font-weight:bold;color:#1E1B4B;word-break:break-word;">{{ $reference }}</p>
                                    <p style="margin:8px 0 0;font-size:12px;line-height:19px;color:#5E6470;">Conservez cette référence pour identifier votre document.</p>
                                </td></tr>
                            </table>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px;font-size:14px;line-height:23px;">
                                <tr><td style="padding:10px 0;border-bottom:1px solid #E4E2F3;color:#5E6470;">Titulaire<br><strong style="color:#1E1B4B;">{{ $nom }}</strong></td></tr>
                                <tr><td style="padding:10px 0;border-bottom:1px solid #E4E2F3;color:#5E6470;">Formation<br><strong style="color:#1E1B4B;">{{ $formation }}</strong></td></tr>
                                <tr><td style="padding:10px 0;border-bottom:1px solid #E4E2F3;color:#5E6470;">Matricule étudiant<br><strong style="color:#1E1B4B;">{{ $matricule }}</strong></td></tr>
                                <tr><td style="padding:10px 0;border-bottom:1px solid #E4E2F3;color:#5E6470;">Date de délivrance<br><strong style="color:#1E1B4B;">{{ $dateDelivrance }}</strong></td></tr>
                                <tr><td style="padding:10px 0;color:#5E6470;">Appréciation du jury<br><strong style="color:#1E1B4B;">{{ $appreciation }}</strong></td></tr>
                            </table>
                            <table role="presentation" cellpadding="0" cellspacing="0" align="center" style="margin:26px auto 16px;">
                                <tr><td align="center" bgcolor="#C2401F" style="background-color:#C2401F;border-radius:8px;"><a href="{{ $certificatUrl }}" style="display:inline-block;padding:16px 28px;border:1px solid #C2401F;border-radius:8px;font-size:16px;line-height:22px;font-weight:bold;color:#FFFFFF;text-decoration:none;">Consulter mon certificat</a></td></tr>
                            </table>
                            <p style="margin:0 0 24px;text-align:center;font-size:12px;line-height:20px;color:#5E6470;">Une connexion à votre espace étudiant est nécessaire.</p>
                            <h2 style="margin:0 0 12px;font-size:18px;line-height:26px;">Comment récupérer votre document ?</h2>
                            <ol style="margin:0 0 24px;padding-left:20px;font-size:14px;line-height:25px;color:#5E6470;">
                                <li>Connectez-vous à votre compte MY EVC.</li>
                                <li>Ouvrez la rubrique <strong style="color:#1E1B4B;">Certificat</strong>.</li>
                                <li>Téléchargez votre certificat au format PDF et conservez-en une copie.</li>
                            </ol>
                            <p style="margin:0 0 24px;padding:14px;background-color:#FFF3EF;border-left:3px solid #EE5A36;font-size:13px;line-height:21px;color:#5E6470;">Une information à corriger sur votre document ? Contactez l’administration via la messagerie MY EVC en précisant la référence de votre certificat.</p>
                            <p style="margin:0;font-size:14px;line-height:23px;color:#5E6470;">Nous vous souhaitons beaucoup de réussite dans vos projets.<br><strong style="color:#1E1B4B;">L’équipe de l’École Virtuelle des Créatifs</strong></p>
                        </td></tr>
                        <tr><td style="padding:20px 28px;background-color:#FAFAFC;border-top:1px solid #E4E2F3;">
                            <p style="margin:0 0 8px;font-size:12px;line-height:19px;color:#5E6470;">Le bouton ne fonctionne pas ? Copiez ce lien dans votre navigateur :</p>
                            <p style="margin:0;font-size:11px;line-height:18px;word-break:break-all;overflow-wrap:anywhere;"><a href="{{ $certificatUrl }}" style="color:#3A3591;text-decoration:underline;word-break:break-all;">{{ $certificatUrl }}</a></p>
                        </td></tr>
                    </table>
                </td></tr>
                <tr><td align="center" style="padding:24px 16px;font-size:11px;line-height:19px;color:#5E6470;">Cet e-mail vous informe de la délivrance de votre certificat à l’EVC.<br>© {{ date('Y') }} École Virtuelle des Créatifs. Tous droits réservés.</td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
