@extends('layouts.site')
@section('title', "Conditions d'inscription et de vente")
@section('description', "Inscription aux formations EVC, tarifs actuels, paiements, rattrapages, certification et demandes d'annulation.")
@section('og_image', '/assets/site/img/ceremonie.jpg')
@section('page', 'legal')

@section('content')
@verbatim
<header class="page-head">
  <div class="bg"><img decoding="async" src="/assets/site/img/direction.jpg" alt=""></div>
  <div class="container">
    <nav class="crumb" aria-label="Fil d'Ariane"><a href="/">Accueil</a><span>/</span><span>Informations légales</span><span>/</span><span aria-current="page">Conditions d’inscription et de vente</span></nav>
    <h1 class="mb-3 reveal">Conditions générales d’inscription et de vente</h1>
    <p class="mb-0 reveal d1">Les conditions de votre parcours : admission, frais de formation, paiements et certification.</p>
    <p class="lg-upd mt-3 mb-0">Dernière mise à jour : <time datetime="2026-11-22">22 novembre 2026</time></p>
  </div>
</header>
<section class="section">
  <div class="container"><div class="row g-5">
    <aside class="col-lg-4"><div class="lg-aside">
      <h6>Sommaire</h6>
      <nav class="lg-toc"><a href="#s1"><span>01</span>Organisme et champ d’application</a><a href="#s2"><span>02</span>Offre et tarifs</a><a href="#s3"><span>03</span>Inscription</a><a href="#s4"><span>04</span>Paiements et retards</a><a href="#s5"><span>05</span>Déroulement de la formation</a><a href="#s6"><span>06</span>Certification et rattrapages</a><a href="#s7"><span>07</span>Report et annulation</a><a href="#s8"><span>08</span>Inactivité et suspension</a><a href="#s9"><span>09</span>Supports et compte MY EVC</a><a href="#s10"><span>10</span>Réclamations et litiges</a></nav>
      <h6 class="mt-4">Informations légales</h6><nav class="lg-docs"><a href="/mentions-legales">Mentions légales</a><a href="/confidentialite">Politique de confidentialité</a><a href="/conditions-utilisation">Conditions générales d'utilisation</a><a href="/conditions-generales-vente" class="on" aria-current="page">Conditions générales d'inscription et de vente</a><a href="/reglement-interieur">Règlement intérieur</a></nav>
      <a href="mailto:info@ecolevirtuelledescreatifs.com" class="btn btn-outline-navy w-100 mt-4">Une question sur mon inscription</a>
    </div></aside>
    <div class="col-lg-8 lg-doc">
      <div class="lg-note"><svg class="ico"><use href="#i-shield"/></svg><div><b>Avant votre inscription.</b> Vérifiez le programme, le tarif total, les échéances et les conditions particulières communiquées par l’EVC. Une préinscription ne constitue pas, à elle seule, une confirmation de place ou une activation du compte étudiant.</div></div>
      <section id="s1" class="lg-sec">
        <h2><span>01</span>Organisme et champ d’application</h2>
        <p>Les présentes conditions concernent les formations dispensées par l’École Virtuelle des Créatifs (EVC), SARL enregistrée sous le numéro <b>CI-ABJ-03-2024-B12-00430</b>, établie à Abidjan, Côte d’Ivoire.</p>
        <p>Elles s’articulent avec le document d’inscription, les conditions particulières du parcours et le <a href="/reglement-interieur">règlement intérieur</a>. Les dispositions légales impératives restent applicables. Toute différence entre une information générale du site et une proposition individuelle doit être clarifiée par écrit avant l’inscription.</p>
      </section>
      <section id="s2" class="lg-sec">
        <h2><span>02</span>Offre de formation et tarifs affichés</h2>
        <p>Les formations ouvertes à la préinscription et leurs montants actuellement affichés sont présentés ci-dessous. Les montants sont exprimés en francs CFA de l’Union économique et monétaire ouest-africaine (XOF).</p>
        <div class="table-responsive"><table class="table lg-table"><thead><tr><th scope="col">Formation</th><th scope="col">Durée</th><th scope="col">Tarif affiché</th><th scope="col">Premier versement</th><th scope="col">Solde</th></tr></thead><tbody>
@endverbatim
          @forelse ($saleFormations as $formation)
            <tr><td>{{ $formation->name }}</td><td>{{ $formation->months }} mois</td><td>{{ number_format($formation->price, 0, ',', ' ') }} FCFA</td><td>{{ number_format($formation->deposit, 0, ',', ' ') }} FCFA</td><td>{{ number_format($formation->balance(), 0, ',', ' ') }} FCFA</td></tr>
          @empty
            <tr><td colspan="5">Aucune formation n’est actuellement ouverte à la préinscription. Contactez l’EVC pour connaître les prochaines possibilités.</td></tr>
          @endforelse
@verbatim
        </tbody></table></div>
        <p>Le programme, les modalités en ligne ou en présentiel, le calendrier, les prestations incluses et les dates de règlement sont précisés pour la formation choisie. Le document d’inscription ou la proposition remise à l’apprenant doit permettre de connaître le montant total dû et les éventuels frais distincts avant son accord.</p>
        <p>Le matériel personnel, les logiciels nécessaires et les frais de connexion doivent être examinés avec l’équipe avant le début du parcours. Les droits d’utilisation des logiciels tiers restent soumis à leurs licences.</p>
      </section>
      <section id="s3" class="lg-sec">
        <h2><span>03</span>Demande d’admission et inscription</h2>
        <p>Le candidat transmet des informations exactes et les pièces nécessaires à l’étude de son dossier. L’EVC procède à l’orientation et aux étapes d’admission prévues pour le parcours.</p>
        <p>L’inscription est confirmée par l’EVC après l’examen du dossier, l’accord sur les conditions du parcours et la prise en compte du règlement demandé. La réception d’un formulaire, un échange d’information ou la seule consultation du site ne suffisent pas à confirmer une inscription.</p>
        <p>Les services sont destinés aux personnes de 16 ans et plus. Un candidat mineur doit obtenir l’accord de ses parents ou de son tuteur légal. L’apprenant doit prendre connaissance du règlement intérieur avant de s’engager.</p>
      </section>
      <section id="s4" class="lg-sec">
        <h2><span>04</span>Paiements, échéances et retards</h2>
        <p>Le règlement s’effectue selon les moyens et les échéances communiqués par l’EVC. L’apprenant conserve les justificatifs de paiement et signale rapidement toute différence entre ses règlements et son dossier financier.</p>
        <p>Le solde de la formation doit être intégralement réglé avant toute demande de certification. Conformément au règlement intérieur publié, tout retard de paiement expose l’étudiant à une pénalité de <b>5 350 FCFA</b>.</p>
        <p>Une difficulté de paiement doit être signalée sans attendre afin d’examiner la situation avec l’administration. Un éventuel aménagement doit être confirmé par l’EVC ; une demande seule ne modifie pas l’échéancier convenu.</p>
      </section>
      <section id="s5" class="lg-sec">
        <h2><span>05</span>Déroulement et engagements</h2>
        <p>L’EVC s’engage à proposer un programme structuré, des supports pédagogiques, un encadrement adapté et un suivi selon les modalités annoncées. Les échéances et critères d’évaluation sont communiqués aux apprenants.</p>
        <p>L’étudiant s’engage à participer aux séances et activités prévues, à répondre aux sollicitations pédagogiques, à réaliser des travaux personnels et à respecter les délais, les consignes et les règles de conduite.</p>
        <p>La formation implique un travail effectif de l’apprenant. L’inscription et le paiement ne suffisent pas à obtenir un certificat : la délivrance dépend des critères administratifs, pédagogiques et de la validation des compétences.</p>
      </section>
      <section id="s6" class="lg-sec">
        <h2><span>06</span>Certification et dispositifs de rattrapage</h2>
        <p>Les cinq critères d’éligibilité sont cumulatifs : règlement intégral de la formation, travaux requis réalisés et validés, participation aux activités EVC, dépôt du rapport de fin de formation et participation à au moins un Studio Créatif. Les compétences pratiques font ensuite l’objet d’une appréciation par l’équipe pédagogique.</p>
        <h3 class="h5">Critères non remplis</h3>
        <p>Le règlement prévoit une session de rattrapage de <b>une (1) semaine et trois (3) jours</b>, moyennant une participation de <b>35 000 FCFA</b>. Si les critères ne sont toujours pas remplis à son issue, l’étudiant est basculé vers un profil normal et doit procéder à une nouvelle inscription pour reprendre le processus.</p>
        <h3 class="h5">Compétences à consolider</h3>
        <p>L’étudiant éligible mais non retenu lors de l’évaluation des compétences bénéficie d’un <b>rattrapage miroir</b>, puis d’une nouvelle évaluation de ses réalisations. En l’absence d’amélioration significative, une <b>attestation de fin de formation</b> est délivrée en lieu et place du certificat.</p>
        <p>Les conditions complètes de certification et de suivi figurent dans le <a href="/reglement-interieur#s5">règlement intérieur</a>.</p>
      </section>
      <section id="s7" class="lg-sec">
        <h2><span>07</span>Report, annulation et remboursement</h2>
        <p>Les conditions de report, d’annulation et de remboursement applicables au parcours doivent être demandées à l’EVC et précisées dans les documents communiqués avant la confirmation de l’inscription. Le candidat doit en prendre connaissance avant de régler les frais demandés.</p>
        <p>Une demande se formule par écrit à <a href="mailto:info@ecolevirtuelledescreatifs.com">info@ecolevirtuelledescreatifs.com</a>, avec la référence du dossier, la formation, les dates concernées et les justificatifs utiles. L’EVC examine les conditions convenues, les prestations déjà réalisées et les règles légales applicables, puis communique sa réponse.</p>
        <p>Les présentes conditions ne fixent ni barème forfaitaire de remboursement ni exclusion générale de remboursement. Elles ne suppriment aucun droit de rétractation, de résolution ou de restitution qui serait applicable à la situation de l’apprenant.</p>
      </section>
      <section id="s8" class="lg-sec">
        <h2><span>08</span>Inactivité, suspension et réintégration</h2>
        <p>Sauf force majeure signalée et justifiée, une absence de disponibilité, de réponse et de participation pendant <b>2 mois</b> continus peut conduire à une exclusion selon le règlement intérieur.</p>
        <p>Une suspension temporaire peut être examinée sur demande justifiée, notamment pour une raison médicale ou une contrainte majeure. La reprise peut être soumise à une mise à niveau, à un nouveau planning ou à des conditions particulières de suivi. Ces situations doivent être traitées avec l’EVC et ne déterminent pas, à elles seules, un droit ou un refus de remboursement.</p>
      </section>
      <section id="s9" class="lg-sec">
        <h2><span>09</span>Supports, créations et compte MY EVC</h2>
        <p>Les supports sont destinés à l’usage pédagogique autorisé. Ils ne peuvent être revendus ou redistribués sans l’autorisation des titulaires de droits. Les projets des étudiants restent la propriété de leurs auteurs.</p>
        <p>Le règlement prévoit, après la fin de formation, un accès au compte MY EVC en lecture seule : consultation et téléchargement des anciens supports archivés, sans accès aux classes virtuelles actives, sans publication ni soumission de nouveaux travaux.</p>
        <p>Les données de scolarité et de paiement sont traitées selon la <a href="/confidentialite">politique de confidentialité</a>.</p>
      </section>
      <section id="s10" class="lg-sec">
        <h2><span>10</span>Réclamations, droit applicable et contact</h2>
        <p>Une réclamation doit être adressée à l’EVC avec les éléments nécessaires à son examen. L’école et l’apprenant recherchent en premier lieu une solution amiable.</p>
        <p>Les présentes conditions sont soumises au droit ivoirien. À défaut d’accord, les juridictions compétentes d’Abidjan peuvent être saisies, sous réserve des dispositions impératives applicables.</p>
        <p><b>École Virtuelle des Créatifs :</b> Abidjan, Côte d’Ivoire · <a href="mailto:info@ecolevirtuelledescreatifs.com">info@ecolevirtuelledescreatifs.com</a> · <a href="tel:+2250747259507">+225 07 47 25 95 07</a>.</p>
      </section>
    </div>
  </div></div>
</section>
@endverbatim
@endsection
