@extends('layouts.site')
@section('title', "Préinscription · EVC — École Virtuelle des Créatifs")
@section('description', "Préinscription en ligne à l'École Virtuelle des Créatifs : choisissez une ou plusieurs formations, présentez votre profil et votre portfolio.")
@section('og_image', '/assets/site/img/ceremonie.jpg')
@section('page', 'preinscription')

@section('content')
@verbatim
<header class="page-head">
  <div class="bg"><img decoding="async" src="/assets/site/img/remise-1.jpg" alt=""></div>
  <div class="container">
    <nav class="crumb" aria-label="Fil d'Ariane"><a href="/">Accueil</a><span>/</span><span>Préinscription</span></nav>
    <h1 class="mb-3 reveal">Préinscription <span>2026–2027</span></h1>
    <p class="mb-0 reveal d1">Six étapes simples, environ 8 minutes. Vous pouvez choisir plusieurs formations. Un conseiller vous recontacte ensuite pour finaliser votre admission.</p>
    
  </div>
</header>
<section class="section pi">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-8">
        <ol class="pi-stepper" id="piStepper"><li data-s="0"><i>1</i><span>Formations</span></li><li data-s="1"><i>2</i><span>Identité</span></li><li data-s="2"><i>3</i><span>Profil & niveau</span></li><li data-s="3"><i>4</i><span>Portfolio & documents</span></li><li data-s="4"><i>5</i><span>Personne à contacter</span></li><li data-s="5"><i>6</i><span>Validation</span></li></ol>
        <div class="pi-bar"><i id="piBar"></i></div>
        <p class="pi-mstep d-md-none" id="piMstep"></p>
        <form id="piForm" class="form-evc form-panel" novalidate>

          <!-- 1 FORMATIONS -->
          <fieldset class="pi-step" data-step="0">
            <legend>Quelle(s) formation(s) vous intéresse(nt) ?</legend>
            <p class="pi-help">Vous pouvez en cocher plusieurs.</p>
            <div class="pick-grid" data-need-one="Formations"><label class="pick"><input type="checkbox" name="Formations" value="Infographie & design graphique" data-key="design" data-price="185 000"><span><i class="pk-l" style="background:var(--orange)">A</i><b>Infographie & design graphique</b><small>4 mois · 185 000 FCFA</small><em class="pk-c"><svg class="ico"><use href="#i-check"/></svg></em></span></label><label class="pick"><input type="checkbox" name="Formations" value="Community management & social media" data-key="cm" data-price="165 000"><span><i class="pk-l" style="background:var(--royal)">B</i><b>Community management & social media</b><small>2 mois · 165 000 FCFA</small><em class="pk-c"><svg class="ico"><use href="#i-check"/></svg></em></span></label><label class="pick"><input type="checkbox" name="Formations" value="Bureautique & informatique (GIA)" data-key="gia" data-price="265 000"><span><i class="pk-l" style="background:var(--blue)">C</i><b>Bureautique & informatique (GIA)</b><small>2 mois · 265 000 FCFA</small><em class="pk-c"><svg class="ico"><use href="#i-check"/></svg></em></span></label></div>
            <div class="pi-err" data-for="Formations">Choisissez au moins une formation.</div>
            <div class="row g-3 mt-2">
              <div class="col-md-6"><label class="form-label">Modalité souhaitée *</label><div class="chips-in"><label class="cin"><input type="radio" name="Modalité" value="En présentiel (Abidjan)" required><span>En présentiel (Abidjan)</span></label><label class="cin"><input type="radio" name="Modalité" value="En ligne"><span>En ligne</span></label></div></div>
              <div class="col-md-6"><label class="form-label" for="piRythme">Disponibilités</label><select class="form-select" id="piRythme" name="Disponibilités"><option>En semaine</option><option>Le week-end</option><option>Soirs</option><option>Flexible</option></select></div>
            </div>
          </fieldset>

          <!-- 2 IDENTITÉ -->
          <fieldset class="pi-step" data-step="1" hidden>
            <legend>Faisons connaissance</legend>
            <div class="row g-4 align-items-start">
              <div class="col-md-4">
                <label class="form-label">Photo d'identité</label>
                <label class="avatar-up" id="avatarUp"><input type="file" name="Photo" accept="image/*" data-preview="avatar"><span class="av-ph"><svg class="ico"><use href="#i-user"/></svg><small>Ajouter une photo</small></span><img loading="lazy" decoding="async" alt="" hidden></label>
                <small class="pi-help d-block mt-2">JPG ou PNG, visage bien visible.</small>
              </div>
              <div class="col-md-8"><div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="piNom">Nom *</label><input class="form-control" id="piNom" name="Nom" required autocomplete="family-name"></div>
                <div class="col-md-6"><label class="form-label" for="piPrenom">Prénoms *</label><input class="form-control" id="piPrenom" name="Prénoms" required autocomplete="given-name"></div>
                <div class="col-md-6"><label class="form-label">Genre</label><div class="chips-in"><label class="cin"><input type="radio" name="Genre" value="Femme"><span>Femme</span></label><label class="cin"><input type="radio" name="Genre" value="Homme"><span>Homme</span></label></div></div>
                <div class="col-md-6"><label class="form-label" for="piNais">Date de naissance *</label><input class="form-control" id="piNais" name="Date de naissance" type="date" required></div>
                <div class="col-md-6"><label class="form-label" for="piNat">Nationalité</label><input class="form-control" id="piNat" name="Nationalité" placeholder="Ivoirienne"></div>
                <div class="col-md-6"><label class="form-label" for="piTel">Téléphone / WhatsApp *</label><input class="form-control" id="piTel" name="Téléphone" type="tel" inputmode="tel" required placeholder="+225 …" autocomplete="tel"></div>
                <div class="col-12"><label class="form-label" for="piMail">E-mail *</label><input class="form-control" id="piMail" name="E-mail" type="email" inputmode="email" required autocomplete="email"></div>
              </div></div>
              <div class="col-12"><h6 class="pi-sub">Lieu de résidence</h6><div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="piPays">Pays *</label><input class="form-control" id="piPays" name="Pays" required value="Côte d'Ivoire"></div>
                <div class="col-md-4"><label class="form-label" for="piVille">Ville *</label><input class="form-control" id="piVille" name="Ville" required placeholder="Abidjan"></div>
                <div class="col-md-4"><label class="form-label" for="piCommune">Commune / quartier</label><input class="form-control" id="piCommune" name="Commune" placeholder="Cocody, Yopougon…"></div>
              </div></div>
            </div>
          </fieldset>

          <!-- 3 PROFIL -->
          <fieldset class="pi-step" data-step="2" hidden>
            <legend>Votre profil et votre niveau</legend>
            <div class="row g-3">
              <div class="col-12"><label class="form-label">Situation actuelle *</label><div class="chips-in"><label class="cin"><input type="radio" name="Situation" value="Élève" required><span>Élève</span></label><label class="cin"><input type="radio" name="Situation" value="Étudiant(e)"><span>Étudiant(e)</span></label><label class="cin"><input type="radio" name="Situation" value="Jeune diplômé(e)"><span>Jeune diplômé(e)</span></label><label class="cin"><input type="radio" name="Situation" value="Salarié(e)"><span>Salarié(e)</span></label><label class="cin"><input type="radio" name="Situation" value="Demandeur d'emploi"><span>Demandeur d'emploi</span></label><label class="cin"><input type="radio" name="Situation" value="Entrepreneur / freelance"><span>Entrepreneur / freelance</span></label><label class="cin"><input type="radio" name="Situation" value="En reconversion"><span>En reconversion</span></label></div></div>
              <div class="col-md-6"><label class="form-label" for="piNiv">Niveau d'études *</label><select class="form-select" id="piNiv" name="Niveau d'études" required><option value="">Choisir…</option><option>Sans diplôme</option><option>BEPC</option><option>BAC</option><option>BTS / BAC+2</option><option>Licence / BAC+3</option><option>Master / BAC+5</option><option>Doctorat</option><option>Autre</option></select></div>
              <div class="col-md-6" data-show-if="Situation=Élève|Étudiant(e)"><label class="form-label" for="piEcole">Établissement et filière</label><input class="form-control" id="piEcole" name="Établissement" placeholder="Ex. Université FHB, Communication"></div>
              <div class="col-md-6" data-show-if="Situation=Salarié(e)|Entrepreneur / freelance"><label class="form-label" for="piPoste">Poste / activité</label><input class="form-control" id="piPoste" name="Poste"></div>
              <div class="col-12"><label class="form-label">Votre niveau dans le domaine choisi *</label><div class="chips-in"><label class="cin"><input type="radio" name="Niveau dans le domaine" value="Débutant complet" required><span>Débutant complet</span></label><label class="cin"><input type="radio" name="Niveau dans le domaine" value="Notions de base"><span>Notions de base</span></label><label class="cin"><input type="radio" name="Niveau dans le domaine" value="Intermédiaire"><span>Intermédiaire</span></label><label class="cin"><input type="radio" name="Niveau dans le domaine" value="Avancé"><span>Avancé</span></label></div></div>
              <div class="col-12"><label class="form-label">Outils déjà utilisés</label><div class="chips-in"><label class="cin"><input type="checkbox" name="Outils" value="Photoshop"><span>Photoshop</span></label><label class="cin"><input type="checkbox" name="Outils" value="Illustrator"><span>Illustrator</span></label><label class="cin"><input type="checkbox" name="Outils" value="InDesign"><span>InDesign</span></label><label class="cin"><input type="checkbox" name="Outils" value="Canva"><span>Canva</span></label><label class="cin"><input type="checkbox" name="Outils" value="Word"><span>Word</span></label><label class="cin"><input type="checkbox" name="Outils" value="Excel"><span>Excel</span></label><label class="cin"><input type="checkbox" name="Outils" value="PowerPoint"><span>PowerPoint</span></label><label class="cin"><input type="checkbox" name="Outils" value="Réseaux sociaux (pro)"><span>Réseaux sociaux (pro)</span></label></div></div>
              <div class="col-md-6"><label class="form-label">Avez-vous un ordinateur ? *</label><div class="chips-in"><label class="cin"><input type="radio" name="Ordinateur" value="Oui, portable" required><span>Oui, portable</span></label><label class="cin"><input type="radio" name="Ordinateur" value="Oui, fixe"><span>Oui, fixe</span></label><label class="cin"><input type="radio" name="Ordinateur" value="Non"><span>Non</span></label></div></div>
              <div class="col-md-6"><label class="form-label">Connexion Internet *</label><div class="chips-in"><label class="cin"><input type="radio" name="Connexion" value="Bonne" required><span>Bonne</span></label><label class="cin"><input type="radio" name="Connexion" value="Moyenne"><span>Moyenne</span></label><label class="cin"><input type="radio" name="Connexion" value="Faible"><span>Faible</span></label></div></div>
              <div class="col-12"><label class="form-label" for="piMotiv">Pourquoi voulez-vous suivre cette formation ? *</label><textarea class="form-control" id="piMotiv" name="Motivation" rows="3" required minlength="20" placeholder="Votre projet, ce que vous voulez faire après…"></textarea><small class="pi-help">20 caractères minimum.</small></div>
              <div class="col-md-6"><label class="form-label" for="piObj">Votre objectif principal</label><select class="form-select" id="piObj" name="Objectif"><option>Trouver un emploi</option><option>Obtenir un stage</option><option>Me lancer en freelance</option><option>Évoluer dans mon poste</option><option>Développer mon entreprise</option><option>Me reconvertir</option></select></div>
              <div class="col-md-6"><label class="form-label" for="piSrc">Comment avez-vous connu l'EVC ?</label><select class="form-select" id="piSrc" name="Source"><option>Réseaux sociaux</option><option>Bouche-à-oreille</option><option>Ancien apprenant</option><option>Recherche Google</option><option>Événement / Caravane Digitale</option><option>Autre</option></select></div>
            </div>
          </fieldset>

          <!-- 4 DOCUMENTS -->
          <fieldset class="pi-step" data-step="3" hidden>
            <legend>Portfolio et documents</legend>
            <p class="pi-help">Tous ces éléments sont facultatifs, mais ils aident à mieux vous orienter.</p>
            <div class="row g-3">
              <div class="col-12"><label class="form-label">Vos réalisations (images)</label>
                <label class="drop" data-drop><input type="file" name="Réalisations" accept="image/*" multiple data-preview="grid" data-max="8"><span><svg class="ico"><use href="#i-layers"/></svg><b>Glissez vos images ici</b><small>ou cliquez pour parcourir · 8 images max</small></span></label>
                <div class="thumbs" data-thumbs="Réalisations"></div></div>
              <div class="col-md-6"><label class="form-label">Pressbook / portfolio (PDF)</label>
                <label class="drop sm" data-drop><input type="file" name="Pressbook" accept="application/pdf" data-preview="list"><span><svg class="ico"><use href="#i-doc"/></svg><b>Ajouter un PDF</b></span></label><div class="files" data-files="Pressbook"></div></div>
              <div class="col-md-6"><label class="form-label" for="piLien">ou lien vers votre portfolio</label><input class="form-control" id="piLien" name="Lien portfolio" type="url" placeholder="https://behance.net/…"></div>
              <div class="col-12"><h6 class="pi-sub">Pièces justificatives <span class="chip ms-1">facultatif</span></h6></div>
              <div class="col-md-6"><label class="form-label">Copie de la pièce d'identité</label>
                <label class="drop sm" data-drop><input type="file" name="Pièce d'identité" accept="image/*,application/pdf" data-preview="list"><span><svg class="ico"><use href="#i-shield"/></svg><b>CNI, passeport ou attestation</b></span></label><div class="files" data-files="Pièce d'identité"></div></div>
              <div class="col-md-6"><label class="form-label">Bulletins / relevés de notes (si étudiant)</label>
                <label class="drop sm" data-drop><input type="file" name="Bulletins" accept="image/*,application/pdf" multiple data-preview="list"><span><svg class="ico"><use href="#i-book"/></svg><b>Derniers bulletins</b></span></label><div class="files" data-files="Bulletins"></div></div>
              <div class="col-md-6"><label class="form-label">Autre document (CV, diplôme…)</label>
                <label class="drop sm" data-drop><input type="file" name="Autres documents" accept="image/*,application/pdf" multiple data-preview="list"><span><svg class="ico"><use href="#i-doc"/></svg><b>Ajouter un fichier</b></span></label><div class="files" data-files="Autres documents"></div></div>
            </div>
            <p class="pi-help mt-3"><svg class="ico"><use href="#i-shield"/></svg> Vos documents sont traités de façon confidentielle, conformément à notre <a href="/confidentialite" target="_blank">politique de confidentialité</a>.</p>
          </fieldset>

          <!-- 5 CONTACT -->
          <fieldset class="pi-step" data-step="4" hidden>
            <legend>Personne à contacter</legend>
            <p class="pi-help">Un parent, un tuteur ou un proche que nous pouvons joindre en cas de besoin. Obligatoire pour les mineurs.</p>
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label" for="pcNom">Nom et prénoms *</label><input class="form-control" id="pcNom" name="Contact - Nom" required></div>
              <div class="col-md-6"><label class="form-label" for="pcLien">Lien avec vous *</label><select class="form-select" id="pcLien" name="Contact - Lien" required><option value="">Choisir…</option><option>Père</option><option>Mère</option><option>Tuteur / tutrice</option><option>Conjoint(e)</option><option>Frère / sœur</option><option>Ami(e)</option><option>Employeur</option><option>Autre</option></select></div>
              <div class="col-md-6"><label class="form-label" for="pcTel">Téléphone *</label><input class="form-control" id="pcTel" name="Contact - Téléphone" type="tel" inputmode="tel" required placeholder="+225 …"></div>
              <div class="col-md-6"><label class="form-label" for="pcMail">E-mail</label><input class="form-control" id="pcMail" name="Contact - E-mail" type="email"></div>
              <div class="col-md-6"><label class="form-label" for="pcFct">Fonction / profession</label><input class="form-control" id="pcFct" name="Contact - Fonction"></div>
              <div class="col-md-6"><label class="form-label" for="pcLieu">Lieu de résidence</label><input class="form-control" id="pcLieu" name="Contact - Résidence" placeholder="Ville, commune"></div>
              <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" id="pcPay" name="Contact - Prend en charge les frais" value="Oui"><label class="form-check-label" for="pcPay">Cette personne prend en charge les frais de formation</label></div></div>
            </div>
          </fieldset>

          <!-- 6 VALIDATION -->
          <fieldset class="pi-step" data-step="5" hidden>
            <legend>Vérifiez et envoyez</legend>
            <div class="recap" id="piRecap"></div>
            <div class="pi-consents">
              <div class="form-check"><input class="form-check-input" type="checkbox" id="c1" required><label class="form-check-label" for="c1">J'ai lu et j'accepte les <a href="/conditions-generales-vente" target="_blank">conditions d'inscription</a> et le <a href="/reglement-interieur" target="_blank">règlement intérieur</a>. *</label></div>
              <div class="form-check"><input class="form-check-input" type="checkbox" id="c2" required><label class="form-check-label" for="c2">J'accepte que l'EVC traite mes données pour ma préinscription, selon sa <a href="/confidentialite" target="_blank">politique de confidentialité</a>. *</label></div>
              <div class="form-check"><input class="form-check-input" type="checkbox" id="c4" required><label class="form-check-label" for="c4">Je comprends que le certificat <b>n'est pas automatique</b> : il est conditionné à la régularité administrative et financière, à la validation des projets, à l'assiduité, à la réussite du dispositif pratique et au respect du règlement intérieur. Je peux être déclaré(e) <b>non éligible</b> ; dans ce cas, je pourrai suivre une <b>session de rattrapage</b>, soumise à des frais selon les modalités fixées par l'école. *</label></div>
              <div class="form-check"><input class="form-check-input" type="checkbox" id="c3" name="Droit à l'image" value="Accepté" required><label class="form-check-label" for="c3">J'autorise l'EVC à diffuser des photos de moi prises lors des activités de l'école. *</label></div>
            </div>
          </fieldset>

          <div class="pi-nav">
            <button type="button" class="btn btn-outline-navy" id="piPrev" hidden><svg class="ico"><use href="#i-left"/></svg> Précédent</button>
            <span class="pi-saved" id="piSaved">Brouillon enregistré</span>
            <button type="button" class="btn btn-navy ms-auto" id="piNext">Continuer <svg class="ico"><use href="#i-right"/></svg></button>
            <button type="submit" class="btn btn-evc ms-auto" id="piSubmit" hidden>Envoyer ma préinscription</button>
          </div>
        </form>

        <div class="form-panel pi-done" id="piDone" hidden>
          <div class="ok"><svg class="ico"><use href="#i-check"/></svg></div>
          <h3>Merci <span id="piName"></span>, votre préinscription est enregistrée !</h3>
          <p class="mb-2">Votre référence : <b id="piRef" style="font-size:1.15rem;letter-spacing:.04em"></b></p>
          <p class="text-muted-evc">Un e-mail de confirmation vient de vous être envoyé. Un conseiller étudie votre profil sous 48 heures. Pour aller plus vite, envoyez aussi votre dossier sur WhatsApp.</p>
          <a href="#" id="piWa" target="_blank" rel="noopener" class="btn btn-wa btn-lg d-inline-flex align-items-center gap-2"><svg class="ico"><use href="#i-wa"/></svg> Envoyer au conseiller</a>
          <a href="/candidature/suivi" id="piSuivi" class="btn btn-outline-navy btn-lg d-inline-flex align-items-center gap-2 ms-sm-2 mt-2 mt-sm-0"><svg class="ico"><use href="#i-user"/></svg> Suivre ma candidature</a>
          <p class="small text-muted-evc mt-3 mb-0" id="piFilesNote" hidden>Vos fichiers sont bien joints à votre dossier.</p>
        </div>
      </div>

      <aside class="col-lg-4">
        <div class="pi-side">
          <h6>Votre sélection</h6>
          <div id="piSel" class="pi-sel"><span class="text-muted-evc small">Aucune formation choisie.</span></div>
          <div class="pi-total" id="piTotal" hidden><span>Total indicatif</span><b></b></div>
          <hr>
          <ul class="check-list small">
            <li><svg class="ico"><use href="#i-check"/></svg><span>Aucun paiement demandé à cette étape</span></li>
            <li><svg class="ico"><use href="#i-check"/></svg><span>Échange d'orientation avec un conseiller</span></li>
            <li><svg class="ico"><use href="#i-check"/></svg><span>Paiement en 2 fois à l'inscription définitive</span></li>
            <li><svg class="ico"><use href="#i-award"/></svg><span>Certificat délivré sous conditions</span></li>
          </ul>
          <div class="cert-cond"><b><svg class="ico"><use href="#i-award"/></svg> Le certificat n'est pas automatique</b><p>Il est délivré uniquement si l'apprenant remplit <u>toutes</u> les conditions suivantes :</p><ol><li>Régularité administrative et financière</li><li>Validation des travaux, projets et évaluations</li><li>Assiduité et engagement</li><li>Réussite du dispositif pratique (Studio Creative ou RECO 60)</li><li>Respect du règlement intérieur</li></ol><p class="mb-2"><b>Non éligible ?</b> Un apprenant peut échouer à la certification. Il peut alors suivre une <b>session de rattrapage</b>, payante, selon les modalités fixées par l'école.</p><a href="/jury-certification#rattrapage">Conditions de rattrapage</a></div>
          <div class="pi-help-box"><b>Besoin d'aide ?</b><a href="https://wa.me/2250747259507?text=PREINSCRIPTION" target="_blank" rel="noopener"><svg class="ico"><use href="#i-wa"/></svg> +225 07 47 25 95 07</a><a href="https://wa.me/33605668823?text=PREINSCRIPTION" target="_blank" rel="noopener"><svg class="ico"><use href="#i-wa"/></svg> +33 6 05 66 88 23</a></div>
        </div>
      </aside>
    </div>
  </div>
</section>
<script src="/assets/site/js/preinscription.js" defer></script>
@endverbatim
@endsection
