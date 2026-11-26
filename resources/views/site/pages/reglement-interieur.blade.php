@extends('layouts.site')
@section('title', 'Règlement intérieur')
@section('description', "Engagements EVC et étudiants, cinq critères de certification, rattrapages, discipline et accès MY EVC après la formation.")
@section('og_image', '/assets/site/img/ceremonie.jpg')
@section('page', 'legal')

@section('content')
@verbatim
<header class="page-head">
  <div class="bg"><img decoding="async" src="/assets/site/img/direction.jpg" alt=""></div>
  <div class="container">
    <nav class="crumb" aria-label="Fil d'Ariane"><a href="/">Accueil</a><span>/</span><span>Informations légales</span><span>/</span><span aria-current="page">Règlement intérieur</span></nav>
    <h1 class="mb-3 reveal">Règlement intérieur</h1>
    <p class="mb-0 reveal d1">L’engagement mutuel de l’EVC et de chaque étudiant pour une formation sérieuse, respectueuse et fondée sur les compétences.</p>
    <p class="lg-upd mt-3 mb-0">Dernière mise à jour : <time datetime="2026-11-22">22 novembre 2026</time></p>
  </div>
</header>
<section class="section">
  <div class="container"><div class="row g-5">
    <aside class="col-lg-4"><div class="lg-aside">
      <h6>Sommaire</h6>
      <nav class="lg-toc"><a href="#s1"><span>01</span>Objet et application</a><a href="#s2"><span>02</span>Engagements de l’EVC</a><a href="#s3"><span>03</span>Engagements de l’étudiant</a><a href="#s4"><span>04</span>Principes de certification</a><a href="#s5"><span>05</span>Critères et rattrapages</a><a href="#s6"><span>06</span>Inactivité et exclusion</a><a href="#s7"><span>07</span>Fraude et plagiat</a><a href="#s8"><span>08</span>Respect et communication</a><a href="#s9"><span>09</span>Travaux et évaluations</a><a href="#s10"><span>10</span>Suspension et réintégration</a><a href="#s11"><span>11</span>Accès après la formation</a><a href="#s12"><span>12</span>Modification du règlement</a><a href="#s13"><span>13</span>Contact</a></nav>
      <h6 class="mt-4">Informations légales</h6><nav class="lg-docs"><a href="/mentions-legales">Mentions légales</a><a href="/confidentialite">Politique de confidentialité</a><a href="/conditions-utilisation">Conditions générales d'utilisation</a><a href="/conditions-generales-vente">Conditions générales d'inscription et de vente</a><a href="/reglement-interieur" class="on" aria-current="page">Règlement intérieur</a></nav>
      <a href="mailto:info@ecolevirtuelledescreatifs.com" class="btn btn-outline-navy w-100 mt-4">Contacter l’équipe EVC</a>
    </div></aside>
    <div class="col-lg-8 lg-doc">
      <div class="lg-note"><svg class="ico"><use href="#i-shield"/></svg><div><b>Le certificat n’est pas automatique.</b> L’étudiant doit remplir les critères d’éligibilité et démontrer les compétences attendues lors de l’évaluation pédagogique.</div></div>
      <section id="s1" class="lg-sec">
        <h2><span>01</span>Objet et champ d’application</h2>
        <p>Le présent règlement fixe les règles de fonctionnement, de discipline et d’engagement applicables aux étudiants inscrits à l’École Virtuelle des Créatifs (EVC), SARL enregistrée sous le numéro <b>CI-ABJ-03-2024-B12-00430</b>, à Abidjan, Côte d’Ivoire.</p>
        <p>Il constitue un engagement mutuel destiné à garantir une formation efficace, un suivi sérieux et un climat de respect. Il s’applique aux cours, ateliers, travaux, évaluations et espaces numériques de la formation, en ligne comme en présentiel.</p>
        <p>L’inscription à une formation EVC implique l’acceptation de ce règlement.</p>
      </section>
      <section id="s2" class="lg-sec">
        <h2><span>02</span>Engagements de l’EVC</h2>
        <p>L’EVC s’engage à :</p>
        <ul><li>mettre à disposition un programme structuré, des supports pédagogiques et un encadrement adapté ;</li><li>assurer un suivi selon les modalités prévues : cours, travaux pratiques, projets, évaluations et retours pédagogiques ;</li><li>informer l’étudiant des échéances importantes, validations, examens et procédures ;</li><li>évaluer les travaux et la progression selon des critères transparents ;</li><li>délivrer le certificat lorsque les conditions de certification sont remplies.</li></ul>
      </section>
      <section id="s3" class="lg-sec">
        <h2><span>03</span>Engagements de l’étudiant</h2>
        <ul><li><b>Assiduité :</b> participer aux cours et ateliers et respecter le planning de formation.</li><li><b>Discipline :</b> respecter les règles, les formateurs, l’équipe EVC et les autres étudiants.</li><li><b>Réactivité :</b> répondre dans un délai raisonnable aux demandes pédagogiques, messages, corrections et convocations.</li><li><b>Qualité de travail :</b> fournir des réalisations personnelles, sérieuses et conformes aux consignes.</li><li><b>Respect des délais :</b> soumettre les travaux pratiques et projets avant les dates limites.</li><li><b>Éthique :</b> s’interdire tout plagiat, fraude, triche ou usurpation d’identité.</li></ul>
      </section>
      <section id="s4" class="lg-sec">
        <h2><span>04</span>Conditions d’obtention du certificat</h2>
        <p>La délivrance du certificat suppose l’assiduité et la participation active, la réalisation et la validation des travaux pratiques et projets requis, le respect de la discipline ainsi que des procédures administratives et pédagogiques.</p>
        <p>En cas de manquement grave ou répété, l’EVC peut refuser la délivrance du certificat, même si l’étudiant a suivi une partie des cours. Le paiement et la présence ne remplacent pas la validation des compétences.</p>
      </section>
      <section id="s5" class="lg-sec">
        <h2><span>05</span>Critères d’éligibilité et rattrapages</h2>
        <h3 class="h5">5.1 — Cinq critères administratifs et pédagogiques cumulatifs</h3>
        <ol><li><b>Solde de la formation :</b> l’intégralité des frais doit être réglée avant toute demande de certification.</li><li><b>Travaux à jour :</b> tous les travaux pratiques, devoirs et évaluations requis doivent être réalisés et validés.</li><li><b>Participation aux activités EVC :</b> l’étudiant prend part activement aux activités, ateliers et sessions de formation.</li><li><b>Rapport de fin de formation :</b> un rapport de fin de formation complet et conforme aux modalités EVC doit être déposé.</li><li><b>Studio Créatif :</b> la participation à au moins un Studio Créatif est obligatoire.</li></ol>
        <h3 class="h5">5.2 — Validation des compétences pratiques</h3>
        <p>Le respect des cinq critères ouvre l’accès à l’évaluation finale, sans garantir à lui seul le certificat. Les formateurs examinent les réalisations et apprécient la maîtrise, la créativité et la qualité professionnelle démontrées.</p>
        <p>Un étudiant éligible dont les réalisations ne sont pas suffisamment abouties n’est pas retenu à ce stade pour la certification. Il bénéficie du rattrapage miroir décrit ci-dessous.</p>
        <h3 class="h5">5.3 — Dispositifs de rattrapage</h3>
        <p><b>Critères non remplis :</b> l’étudiant peut suivre une session de rattrapage d’une durée de <b>une (1) semaine et trois (3) jours</b>, moyennant une participation de <b>35 000 FCFA</b>. Si les critères restent non remplis à l’issue de cette session, il est basculé vers un <b>profil normal</b> et doit procéder à une nouvelle inscription pour reprendre le processus.</p>
        <p><b>Compétences à consolider :</b> l’étudiant éligible mais non retenu bénéficie d’un <b>rattrapage miroir</b> pour corriger les insuffisances, renforcer ses compétences et démontrer une progression concrète. Ses réalisations sont ensuite réévaluées. En l’absence d’amélioration significative, une <b>attestation de fin de formation</b> est délivrée en lieu et place du certificat.</p>
        <p>Ces dispositifs visent à garantir que chaque certificat délivré reflète des compétences réellement acquises et démontrées.</p>
        <h3 class="h5">5.4 — Retard de paiement</h3>
        <p>Le règlement du solde est obligatoire. Tout retard de paiement expose l’étudiant à une pénalité de <b>5 350 FCFA</b>.</p>
        <h3 class="h5">5.5 — Réalisation et soumission des projets</h3>
        <p>Les projets sont attribués depuis l’espace personnel de l’étudiant. Ils doivent être réalisés dans les délais impartis et soumis dans l’espace de classe virtuelle prévu à cet effet.</p>
      </section>
      <section id="s6" class="lg-sec">
        <h2><span>06</span>Inactivité, indisponibilité et exclusion</h2>
        <p>Sauf force majeure signalée et justifiée, une indisponibilité prolongée compromet la progression. Si l’étudiant ne se rend pas disponible, ne répond pas et ne participe pas pendant une durée continue de <b>2 mois</b>, l’EVC peut procéder à une exclusion du programme.</p>
        <p>Avant toute exclusion, l’EVC peut relancer l’étudiant par message, appel ou e-mail, demander une justification et proposer une solution de rattrapage lorsque la situation le permet.</p>
        <p>L’exclusion peut entraîner l’arrêt du suivi et des évaluations, ainsi que l’impossibilité d’obtenir le certificat.</p>
      </section>
      <section id="s7" class="lg-sec">
        <h2><span>07</span>Plagiat, fraude et intégrité académique</h2>
        <p>Le plagiat, l’utilisation d’un travail d’autrui, la duplication, la falsification et l’usurpation d’identité sont interdits. Un travail plagié peut être automatiquement invalidé. En cas de récidive ou de fraude grave, l’EVC peut prononcer une sanction allant jusqu’à l’exclusion.</p>
      </section>
      <section id="s8" class="lg-sec">
        <h2><span>08</span>Respect, conduite et communication</h2>
        <p>La communication avec les formateurs et l’équipe EVC doit rester professionnelle et respectueuse. Les propos insultants, menaçants ou discriminatoires, le harcèlement et les comportements perturbateurs sont interdits.</p>
        <p>Les canaux officiels et des horaires raisonnables doivent être respectés. L’EVC peut limiter l’accès aux groupes et espaces de discussion en cas d’abus.</p>
      </section>
      <section id="s9" class="lg-sec">
        <h2><span>09</span>Organisation pédagogique</h2>
        <p>L’étudiant respecte les consignes et les formats demandés, soumet ses travaux dans les délais, accepte les corrections et les itérations et conserve les fichiers sources et livrables lorsqu’ils sont demandés.</p>
        <p>La validation dépend de la qualité attendue et des critères de l’EVC. Le simple dépôt d’un fichier ne constitue pas une validation pédagogique.</p>
      </section>
      <section id="s10" class="lg-sec">
        <h2><span>10</span>Suspension et réintégration</h2>
        <p>Une suspension temporaire peut être proposée dans certaines situations, notamment médicales ou liées à une contrainte majeure, sur demande accompagnée d’un justificatif.</p>
        <p>La réintégration peut nécessiter une mise à niveau, un nouveau planning ou des conditions particulières de suivi, définis avec l’EVC.</p>
      </section>
      <section id="s11" class="lg-sec">
        <h2><span>11</span>Accès au compte après la fin de formation</h2>
        <p>À l’issue de sa formation, l’étudiant perd l’accès aux espaces de travail collaboratifs actifs. Son compte MY EVC est conservé en <b>lecture seule</b> et n’est plus mis à jour au titre du parcours terminé.</p>
        <ul><li>Les classes virtuelles de travail actives ne sont plus accessibles.</li><li>Les anciens supports archivés dans l’espace personnel restent consultables et téléchargeables.</li><li>L’étudiant ne peut plus publier, soumettre de travaux ni interagir dans les classes virtuelles.</li></ul>
      </section>
      <section id="s12" class="lg-sec">
        <h2><span>12</span>Modification du règlement</h2>
        <p>L’EVC peut faire évoluer ce règlement pour tenir compte de l’organisation, de la pédagogie et de la réglementation. Les mises à jour sont publiées sur cette page et s’appliquent dès leur publication, dans le respect des dispositions légales applicables.</p>
      </section>
      <section id="s13" class="lg-sec">
        <h2><span>13</span>Contact</h2>
        <p>École Virtuelle des Créatifs — Abidjan, Côte d’Ivoire.</p>
        <p><a href="mailto:info@ecolevirtuelledescreatifs.com">info@ecolevirtuelledescreatifs.com</a> · <a href="tel:+2250747259507">+225 07 47 25 95 07</a></p>
      </section>
    </div>
  </div></div>
</section>
@endverbatim
@endsection
