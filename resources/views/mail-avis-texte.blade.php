MY EVC — École Virtuelle des Créatifs

{!! $titre !!}
{!! $categorie !!}

Bonjour{!! $nom ? ' '.$nom : '' !!},

{!! $introduction !!}
@if ($miseEnAvant)

{!! $libelleMiseEnAvant !!}
{!! $miseEnAvant !!}
@endif
@foreach ($details as $label => $value)
{!! $label !!} : {!! $value !!}
@endforeach
@if ($url)

{!! $actionLabel !!}
{!! $url !!}

{!! $aideAction ?? 'Une connexion à votre compte MY EVC est nécessaire.' !!}
@endif
@if ($etapes)

LES PROCHAINES ÉTAPES
@foreach ($etapes as $etape)
{!! $loop->iteration !!}. {!! $etape !!}
@endforeach
@endif
@if ($conseil)

{!! $conseil !!}
@endif

L’équipe de l’École Virtuelle des Créatifs
{!! $signature ?? 'Votre progression, notre engagement.' !!}

{!! $notePiedDePage ?? 'Vous recevez cet e-mail dans le cadre du suivi de votre compte MY EVC.' !!}
© {{ date('Y') }} École Virtuelle des Créatifs. Tous droits réservés.
