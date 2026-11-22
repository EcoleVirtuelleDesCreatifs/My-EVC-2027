<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>{{ $titre }} · MY EVC</title>
</head>
<body style="margin:0;padding:0;background-color:#F4F3F8;color:#1E1B4B;font-family:Arial,Helvetica,sans-serif;-webkit-text-size-adjust:100%;">
    <div style="display:none;max-height:0;max-width:0;overflow:hidden;opacity:0;font-size:1px;line-height:1px;mso-hide:all;">{{ $titre }}{{ $miseEnAvant ? ' — '.$miseEnAvant : '' }}</div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F4F3F8;">
        <tr><td align="center" style="padding:32px 12px;">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:100%;max-width:600px;">
                <tr><td style="padding:0 12px 24px;">
                    <p style="margin:0;font-size:28px;font-weight:bold;letter-spacing:-1px;color:#1E1B4B;">MY <span style="color:#C2401F;">EVC</span></p>
                    <p style="margin:6px 0 0;font-size:12px;line-height:18px;color:#5E6470;">École Virtuelle des Créatifs</p>
                </td></tr>
                <tr><td style="background-color:#FFFFFF;border:1px solid #E4E2F3;border-radius:16px;overflow:hidden;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                        <tr><td style="padding:28px 24px;background-color:#1E1B4B;border-top:5px solid #EE5A36;">
                            <p style="margin:0 0 12px;font-size:11px;line-height:17px;letter-spacing:1.5px;font-weight:bold;color:#FF8A6B;">{{ $categorie }}</p>
                            <h1 style="margin:0;font-size:27px;line-height:35px;color:#FFFFFF;">{{ $titre }}</h1>
                        </td></tr>
                        <tr><td style="padding:28px 24px;">
                            <p style="margin:0 0 16px;font-size:18px;line-height:27px;font-weight:bold;color:#1E1B4B;">Bonjour{{ $nom ? ' '.$nom : '' }},</p>
                            <p style="margin:0 0 24px;font-size:15px;line-height:25px;color:#5E6470;white-space:pre-line;">{{ $introduction }}</p>
                            @if ($miseEnAvant)
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F4F3F8;border:1px solid #E4E2F3;border-radius:10px;">
                                    <tr><td style="padding:20px;">
                                        <p style="margin:0 0 8px;font-size:11px;line-height:18px;letter-spacing:1px;font-weight:bold;color:#5E6470;">{{ $libelleMiseEnAvant }}</p>
                                        <p style="margin:0;font-size:23px;line-height:32px;font-weight:bold;color:#1E1B4B;overflow-wrap:anywhere;word-break:break-word;">{{ $miseEnAvant }}</p>
                                    </td></tr>
                                </table>
                            @endif
                            @if ($details)
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:18px;font-size:14px;line-height:23px;">
                                    @foreach ($details as $label => $value)
                                        <tr><td style="padding:10px 0;border-bottom:1px solid #E4E2F3;color:#5E6470;">{{ $label }}<br><strong style="color:#1E1B4B;overflow-wrap:anywhere;word-break:break-word;">{{ $value }}</strong></td></tr>
                                    @endforeach
                                </table>
                            @endif
                            @if ($url)
                                <table role="presentation" cellpadding="0" cellspacing="0" align="center" style="margin:28px auto 12px;">
                                    <tr><td align="center" bgcolor="#C2401F" style="background-color:#C2401F;border-radius:8px;"><a href="{{ $url }}" style="display:inline-block;padding:15px 24px;border:1px solid #C2401F;border-radius:8px;font-size:16px;line-height:22px;font-weight:bold;color:#FFFFFF;text-decoration:none;">{{ $actionLabel }}</a></td></tr>
                                </table>
                                <p style="margin:0 0 24px;text-align:center;font-size:12px;line-height:20px;color:#5E6470;">{{ $aideAction ?? 'Une connexion à votre compte MY EVC est nécessaire.' }}</p>
                            @endif
                            @if ($etapes)
                                <h2 style="margin:24px 0 12px;font-size:18px;line-height:26px;color:#1E1B4B;">Les prochaines étapes</h2>
                                <ol style="margin:0 0 24px;padding-left:20px;font-size:14px;line-height:25px;color:#5E6470;">@foreach ($etapes as $etape)<li style="padding-bottom:6px;">{{ $etape }}</li>@endforeach</ol>
                            @endif
                            @if ($conseil)<p style="margin:24px 0;padding:14px;background-color:#FFF3EF;border-left:3px solid #EE5A36;font-size:13px;line-height:22px;color:#5E6470;">{{ $conseil }}</p>@endif
                            <p style="margin:24px 0 0;font-size:14px;line-height:23px;color:#5E6470;">L’équipe de l’École Virtuelle des Créatifs<br><strong style="color:#1E1B4B;">{{ $signature ?? 'Votre progression, notre engagement.' }}</strong></p>
                        </td></tr>
                        @if ($url)
                            <tr><td style="padding:20px 24px;background-color:#FAFAFC;border-top:1px solid #E4E2F3;">
                                <p style="margin:0 0 8px;font-size:12px;line-height:19px;color:#5E6470;">Le bouton ne fonctionne pas ? Copiez ce lien dans votre navigateur :</p>
                                <p style="margin:0;font-size:11px;line-height:18px;word-break:break-all;overflow-wrap:anywhere;"><a href="{{ $url }}" style="color:#3A3591;text-decoration:underline;word-break:break-all;">{{ $url }}</a></p>
                            </td></tr>
                        @endif
                    </table>
                </td></tr>
                <tr><td align="center" style="padding:24px 16px;font-size:11px;line-height:19px;color:#5E6470;">{{ $notePiedDePage ?? 'Vous recevez cet e-mail dans le cadre du suivi de votre compte MY EVC.' }}<br>© {{ date('Y') }} École Virtuelle des Créatifs. Tous droits réservés.</td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
