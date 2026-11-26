<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>{{ $titre }} · EVC</title>
</head>
<body style="margin:0;padding:0;background-color:#F4F3F8;color:#1E1B4B;font-family:Arial,Helvetica,sans-serif;-webkit-text-size-adjust:100%;">
    <div style="display:none;max-height:0;max-width:0;overflow:hidden;opacity:0;font-size:1px;line-height:1px;mso-hide:all;">{{ $chapeau }}</div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F4F3F8;">
        <tr><td align="center" style="padding:32px 12px;">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:100%;max-width:600px;">
                <tr><td style="padding:0 12px 24px;">
                    <p style="margin:0;font-size:28px;font-weight:bold;letter-spacing:-1px;color:#1E1B4B;">MY <span style="color:#C2401F;">EVC</span></p>
                    <p style="margin:6px 0 0;font-size:12px;line-height:18px;color:#5E6470;">École Virtuelle des Créatifs · Actualités</p>
                </td></tr>
                <tr><td style="background-color:#FFFFFF;border:1px solid #E4E2F3;border-radius:16px;overflow:hidden;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                        @if ($couverture)
                            <tr><td><a href="{{ $articleUrl }}"><img src="{{ $couverture }}" alt="" width="600" style="display:block;width:100%;max-width:600px;height:auto;"></a></td></tr>
                        @endif
                        <tr><td style="padding:28px;background-color:#1E1B4B;@unless($couverture) border-top:5px solid #EE5A36;@endunless">
                            <p style="margin:0 0 12px;font-size:11px;line-height:16px;letter-spacing:2px;font-weight:bold;color:#FF8A6B;">{{ mb_strtoupper($categorie) }}</p>
                            <h1 style="margin:0;font-size:26px;line-height:34px;color:#FFFFFF;">{{ $titre }}</h1>
                        </td></tr>
                        <tr><td style="padding:28px;">
                            <p style="margin:0 0 24px;font-size:15px;line-height:25px;color:#5E6470;">{{ $chapeau }}</p>
                            <table role="presentation" cellpadding="0" cellspacing="0" align="center" style="margin:4px auto 16px;">
                                <tr><td align="center" bgcolor="#C2401F" style="background-color:#C2401F;border-radius:8px;"><a href="{{ $articleUrl }}" style="display:inline-block;padding:15px 28px;border:1px solid #C2401F;border-radius:8px;font-size:16px;line-height:22px;font-weight:bold;color:#FFFFFF;text-decoration:none;">Lire l’article</a></td></tr>
                            </table>
                            <p style="margin:0;font-size:14px;line-height:23px;color:#5E6470;">À très vite,<br><strong style="color:#1E1B4B;">L’équipe de l’École Virtuelle des Créatifs</strong></p>
                        </td></tr>
                        <tr><td style="padding:20px 28px;background-color:#FAFAFC;border-top:1px solid #E4E2F3;">
                            <p style="margin:0 0 8px;font-size:12px;line-height:19px;color:#5E6470;">Le bouton ne fonctionne pas ? Copiez ce lien dans votre navigateur :</p>
                            <p style="margin:0;font-size:11px;line-height:18px;word-break:break-all;"><a href="{{ $articleUrl }}" style="color:#3A3591;text-decoration:underline;word-break:break-all;">{{ $articleUrl }}</a></p>
                        </td></tr>
                    </table>
                </td></tr>
                <tr><td align="center" style="padding:24px 16px;font-size:11px;line-height:19px;color:#5E6470;">Vous recevez cet e-mail car vous êtes abonné à la newsletter de l’EVC.<br><a href="{{ $desinscriptionUrl }}" style="color:#5E6470;text-decoration:underline;">Se désabonner de la newsletter</a><br><br>© {{ date('Y') }} École Virtuelle des Créatifs. Tous droits réservés.</td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
