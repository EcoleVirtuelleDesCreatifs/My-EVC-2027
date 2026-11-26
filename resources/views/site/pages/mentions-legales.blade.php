@extends('layouts.site')
@section('title', 'Mentions légales')
@section('description', "Identité de l'EVC SARL, numéro d'enregistrement, direction de la publication, hébergement et règles applicables au site.")
@section('og_image', '/assets/site/img/ceremonie.jpg')
@section('page', 'legal')

@section('content')
@verbatim
<header class="page-head">
  <div class="bg"><img decoding="async" src="/assets/site/img/direction.jpg" alt=""></div>
  <div class="container">
    <nav class="crumb" aria-label="Fil d'Ariane"><a href="/">Accueil</a><span>/</span><span>Informations légales</span><span>/</span><span aria-current="page">Mentions légales</span></nav>
    <h1 class="mb-3 reveal">Mentions légales</h1>
    <p class="mb-0 reveal d1">Les informations administratives et les règles de publication du site de l’École Virtuelle des Créatifs.</p>
    <p class="lg-upd mt-3 mb-0">Dernière mise à jour : <time datetime="2026-11-22">22 novembre 2026</time></p>
  </div>
</header>
<section class="section">
  <div class="container"><div class="row g-5">
    <aside class="col-lg-4"><div class="lg-aside">
      <h6>Sommaire</h6>
      <nav class="lg-toc"><a href="#s1"><span>01</span>Éditeur du site</a><a href="#s2"><span>02</span>Hébergement</a><a href="#s3"><span>03</span>Objet du site</a><a href="#s4"><span>04</span>Propriété intellectuelle</a><a href="#s5"><span>05</span>Données personnelles</a><a href="#s6"><span>06</span>Cookies</a><a href="#s7"><span>07</span>Responsabilité et liens</a><a href="#s8"><span>08</span>Droit applicable</a><a href="#s9"><span>09</span>Contact</a></nav>
      <h6 class="mt-4">Informations légales</h6><nav class="lg-docs"><a href="/mentions-legales" class="on" aria-current="page">Mentions légales</a><a href="/confidentialite">Politique de confidentialité</a><a href="/conditions-utilisation">Conditions générales d'utilisation</a><a href="/conditions-generales-vente">Conditions générales d'inscription et de vente</a><a href="/reglement-interieur">Règlement intérieur</a></nav>
      <a href="mailto:info@ecolevirtuelledescreatifs.com" class="btn btn-outline-navy w-100 mt-4">Une question ? Écrivez-nous</a>
    </div></aside>
    <div class="col-lg-8 lg-doc">
      <div class="lg-note"><svg class="ico"><use href="#i-shield"/></svg><div><b>École Virtuelle des Créatifs — EVC.</b> Société à responsabilité limitée établie à Abidjan, Côte d’Ivoire.</div></div>
      <section id="s1" class="lg-sec">
        <h2><span>01</span>Éditeur du site</h2>
        <p>Le site <a href="https://www.ecolevirtuelledescreatifs.com">www.ecolevirtuelledescreatifs.com</a> est édité par l’École Virtuelle des Créatifs.</p>
        <ul class="lg-id">
          <li><b>Raison sociale :</b> École Virtuelle des Créatifs (EVC)</li>
          <li><b>Forme juridique :</b> Société à responsabilité limitée (SARL)</li>
          <li><b>Capital social :</b> 1 000 000 FCFA</li>
          <li><b>Numéro d’enregistrement :</b> CI-ABJ-03-2024-B12-00430</li>
          <li><b>Siège social :</b> Abidjan, Côte d’Ivoire</li>
          <li><b>Directeur de la publication :</b> Monsieur Bilé Bossombra</li>
          <li><b>E-mail :</b> <a href="mailto:info@ecolevirtuelledescreatifs.com">info@ecolevirtuelledescreatifs.com</a></li>
          <li><b>Téléphone :</b> <a href="tel:+2250747259507">+225 07 47 25 95 07</a></li>
        </ul>
      </section>
      <section id="s2" class="lg-sec">
        <h2><span>02</span>Hébergement</h2>
        <p>L’hébergement du site public est assuré par <b>LWS (Ligne Web Services)</b>, dont les coordonnées publiées par l’EVC sont les suivantes :</p>
        <ul><li><b>Adresse :</b> 10 rue Penthièvre, 75008 Paris, France</li><li><b>Site internet :</b> <a href="https://www.lws.fr" target="_blank" rel="noopener noreferrer">www.lws.fr</a></li></ul>
      </section>
      <section id="s3" class="lg-sec">
        <h2><span>03</span>Objet du site</h2>
        <p>Le site présente l’école, ses formations, son organisation pédagogique, ses actualités et ses modalités d’admission. Il permet de contacter l’EVC et de déposer une demande de préinscription.</p>
        <p>L’espace MY EVC est destiné au suivi de la formation : ressources pédagogiques, projets, évaluations, échanges, paiements enregistrés et documents de scolarité, selon les droits attribués à chaque utilisateur.</p>
        <p>Les modalités d’utilisation sont décrites dans les <a href="/conditions-utilisation">CGU</a>. Les conditions d’inscription et de paiement figurent dans les <a href="/conditions-generales-vente">CGV</a> et les documents communiqués à l’apprenant lors de son inscription.</p>
      </section>
      <section id="s4" class="lg-sec">
        <h2><span>04</span>Propriété intellectuelle</h2>
        <p>Les textes, logos, graphismes, photographies, vidéos, cours et supports pédagogiques publiés par l’EVC sont protégés par les droits de propriété intellectuelle. Ils appartiennent à l’EVC ou à leurs titulaires respectifs, sauf mention contraire.</p>
        <p>Toute reproduction, distribution, modification, adaptation, retransmission ou publication de ces éléments nécessite une autorisation écrite préalable du titulaire des droits, en dehors des usages autorisés par la loi ou par une licence expressément indiquée.</p>
        <p>Les travaux et projets réalisés par les étudiants restent la propriété de leurs auteurs. Leur présentation sur le site ne transfère aucun droit de réutilisation au visiteur. Une demande d’autorisation ou de retrait peut être adressée à l’EVC.</p>
      </section>
      <section id="s5" class="lg-sec">
        <h2><span>05</span>Données personnelles</h2>
        <p>L’EVC traite les données nécessaires à la gestion des demandes, des inscriptions et de la scolarité. Vous disposez notamment de droits d’accès, de rectification et, dans les conditions applicables, de suppression de vos données.</p>
        <p>Les finalités, destinataires, durées de conservation et modalités d’exercice de vos droits sont détaillés dans la <a href="/confidentialite">politique de confidentialité</a>. Le contact dédié est <a href="mailto:info@ecolevirtuelledescreatifs.com">info@ecolevirtuelledescreatifs.com</a>.</p>
      </section>
      <section id="s6" class="lg-sec">
        <h2><span>06</span>Cookies et préférences</h2>
        <p>Les cookies techniques et de session permettent notamment l’authentification et la sécurité des formulaires. Des préférences d’affichage peuvent également être mémorisées dans le navigateur.</p>
        <p>Les éventuels traceurs de mesure d’audience ou de publicité qui ne sont pas nécessaires au service doivent faire l’objet d’un choix préalable lorsque celui-ci est requis. La seule navigation ne vaut pas consentement à ces traceurs. Vous pouvez gérer les données de navigation dans les paramètres de votre navigateur ; le blocage des cookies indispensables peut empêcher certaines fonctionnalités.</p>
      </section>
      <section id="s7" class="lg-sec">
        <h2><span>07</span>Responsabilité et liens hypertextes</h2>
        <p>L’EVC s’efforce de publier des informations exactes et à jour. Les contenus peuvent être corrigés ou actualisés. Une erreur, un lien défectueux ou un contenu litigieux peut être signalé au contact indiqué ci-dessous.</p>
        <p>Les sites externes restent sous la responsabilité de leurs éditeurs. L’EVC n’en contrôle ni les contenus ni les pratiques de traitement des données. Une référence à un site tiers n’emporte pas approbation de l’ensemble de ses contenus.</p>
        <p>Conformément aux conditions publiées par l’EVC, la création d’un lien vers son site doit faire l’objet d’une autorisation écrite préalable. Les présentes mentions ne limitent pas les responsabilités ou droits auxquels la loi ne permet pas de déroger.</p>
      </section>
      <section id="s8" class="lg-sec">
        <h2><span>08</span>Droit applicable</h2>
        <p>Les présentes mentions sont régies par le droit ivoirien. Les litiges relatifs à l’utilisation du site relèvent des juridictions compétentes d’Abidjan, sous réserve des dispositions légales impératives applicables.</p>
      </section>
      <section id="s9" class="lg-sec">
        <h2><span>09</span>Contact</h2>
        <p>École Virtuelle des Créatifs — Abidjan, Côte d’Ivoire.</p>
        <p><a href="mailto:info@ecolevirtuelledescreatifs.com">info@ecolevirtuelledescreatifs.com</a> · <a href="tel:+2250747259507">+225 07 47 25 95 07</a></p>
      </section>
    </div>
  </div></div>
</section>
@endverbatim
@endsection
