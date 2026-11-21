/* ============ MY EVC — v9 : connexion et préinscription selon la charte UI ============ */
const uLoginAfter = VIEWS.login.after;
view('login','public','Connexion','fa-key', () => `
<div class="login2">
  <div class="l2-art">${motif('l2-motif')}<a href="#login" class="l2-logo" aria-label="MY EVC">${logoH('negatif')}</a>
    <div class="l2-copy"><h1>${BRAND.slogan}</h1><p>Formations, séances, travaux pratiques, évaluations, paiements et certificats : tout votre parcours EVC au même endroit.</p></div></div>
  <div class="l2-form"><div class="box">
    <div class="l2-mlogo">${logoH('couleur')}</div>
    <h2 class="l2-t">Connexion à votre espace</h2><p class="muted mb-4">Étudiants, formateurs et équipe administrative.</p>
    <form data-submit="login" novalidate class="d-flex flex-column gap-3">
      <div class="seg seg-soft block role-seg" role="radiogroup" aria-label="Espace">
        <input type="radio" name="role" id="r-etu" value="etu" checked><label for="r-etu">Étudiant</label>
        <input type="radio" name="role" id="r-form" value="form"><label for="r-form">Formateur</label>
        <input type="radio" name="role" id="r-admin" value="admin"><label for="r-admin">Administration</label>
      </div>
      <div id="login-arole" hidden><label class="form-label" for="l-arole">Rôle administratif (démo)</label><select class="form-select" id="l-arole">${ADMIN_ROLES.map(r=>`<option ${r===S.adminRole?'selected':''}>${esc(r)}</option>`).join('')}</select></div>
      <div><label class="form-label" for="l-email">Adresse e-mail ou matricule</label><input class="form-control" id="l-email" value="EVC-26-0142" required autocomplete="username"><div class="invalid-feedback">Saisissez votre e-mail ou votre matricule.</div></div>
      <div><div class="d-flex justify-content-between align-items-baseline"><label class="form-label" for="l-pass">Mot de passe</label><a href="#login" class="link-u small" data-act="forgot">Mot de passe oublié ?</a></div><input class="form-control" type="password" id="l-pass" value="demo-evc-2026" required autocomplete="current-password"><div class="invalid-feedback">Saisissez votre mot de passe.</div></div>
      <div id="login-mfa" hidden><label class="form-label" for="l-otp">Code de vérification</label><input class="form-control mono" id="l-otp" inputmode="numeric" maxlength="6" value="482913"><div class="form-text">Compte sensible : code à 6 chiffres envoyé par SMS ou par l'application d'authentification.</div></div>
      <label class="d-flex gap-2 align-items-center small"><input class="form-check-input mt-0" type="checkbox" checked id="l-rem">Rester connecté sur cet appareil</label>
      <button class="btn btn-primary btn-lg w-100" type="submit">Se connecter</button>
      <div class="divider-t">Nouveau à l'EVC ?</div>
      <a class="btn btn-ghost btn-lg w-100" href="#preinscription">Faire ma préinscription</a>
    </form>
    <details class="l2-demo mt-4"><summary><i class="fa-solid fa-sliders"></i>Options de la démonstration</summary>
      <div id="login-profile" class="mt-3"><div class="form-label">Profil étudiant à simuler</div><div class="profile-pick">${PROFILE_KEYS.map((p,i)=>`<input type="radio" name="profile" id="lp-${i}" value="${p}" ${p===S.profile?'checked':''}><label for="lp-${i}">${PROFILES[p].f.map(fchip).join('')}</label>`).join('')}</div></div>
      <div class="d-flex flex-column gap-2 small mt-3"><a href="#candidature" class="link-u small">Suivre ma candidature (test, paiement, activation)</a><a href="#verifier" class="link-u small">Vérifier l'authenticité d'un certificat</a><button type="button" class="link-u small text-start" data-act="theme">Changer de thème (clair / sombre)</button></div></details>
  </div></div>
</div>`, uLoginAfter);

/* ----- Préinscription en 4 étapes, récapitulatif à droite ----- */
const U_PST = [['Identité','Vos informations'],['Formation','Choisissez votre formation'],['Documents','Votre profil et vos documents'],['Validation','Vérifiez et envoyez']];
view('preinscription','public','Préinscription','fa-user-plus', () => `
<header class="pub-top"><a href="#login" class="pt-logo" aria-label="MY EVC">${logoSym('couleur')}${logoType('couleur')}</a><span class="small muted ms-auto">Déjà inscrit ? <a class="link-u small" href="#login">Se connecter</a></span></header>
<div class="pub-wrap">
  <div class="small-caps tx-c" id="pst-k">Préinscription · étape 1 sur 4</div><h1 class="pub-h1" id="pst-h">${U_PST[0][1]}</h1>
  <ol class="pst" id="pst">${U_PST.map((s,i)=>`<li data-i="${i}"><span class="pst-bar"></span><span class="pst-l"><span class="pst-n">${i+1}</span>${s[0]}</span></li>`).join('')}</ol>
  <div class="u-grid u-2-1 mt-4">
  <form id="prein-form" data-submit="prein" novalidate class="panel card-pad">
    <div class="pst-p" data-p="0"><h2 class="h3 mb-3">Identité</h2><div class="row g-3">
      <div class="col-md-6"><label class="form-label" for="pi-first">Prénom</label><input class="form-control" id="pi-first" required autocomplete="given-name"><div class="invalid-feedback">Champ obligatoire.</div></div>
      <div class="col-md-6"><label class="form-label" for="pi-last">Nom</label><input class="form-control" id="pi-last" required autocomplete="family-name"><div class="invalid-feedback">Champ obligatoire.</div></div>
      <div class="col-md-6"><label class="form-label" for="pi-mail">Adresse e-mail</label><input class="form-control" type="email" id="pi-mail" required placeholder="nom@exemple.com" autocomplete="email"><div class="invalid-feedback">E-mail invalide.</div></div>
      <div class="col-md-6"><label class="form-label" for="pi-tel">Téléphone (WhatsApp)</label><input class="form-control" id="pi-tel" required placeholder="07 00 00 00 00" pattern="[+0-9 ]{10,}" autocomplete="tel"><div class="invalid-feedback">Numéro incomplet : 10 chiffres attendus.</div></div>
      <div class="col-md-4"><label class="form-label" for="pi-birth">Date de naissance</label><input class="form-control" type="date" id="pi-birth" required value="2003-06-15"></div>
      <div class="col-md-4"><label class="form-label" for="pi-country">Pays</label><select class="form-select" id="pi-country">${[...new Set(COUNTRIES)].map(c=>`<option>${esc(c)}</option>`).join('')}</select></div>
      <div class="col-md-4"><label class="form-label" for="pi-city">Ville / commune</label><input class="form-control" id="pi-city" placeholder="Ex. Cocody"></div></div></div>
    <div class="pst-p d-none" data-p="1"><h2 class="h3 mb-3">Formation</h2><div class="u-grid u-1-1" style="gap:16px">${FORDER.map(f=>`<label class="fopt"><input class="form-check-input mt-1" type="checkbox" name="pf" value="${f}" data-chg="u-prein" ${f==='DG'?'checked':''}><span><b>${esc(F[f].name)}</b><span class="small muted d-block">${F[f].months} mois · ${S.formations[f].prog.length} séances · ${esc(F[f].format.split(':')[0].toLowerCase())}</span><b class="num d-block mt-1">${fcfa(F[f].price)}</b></span></label>`).join('')}</div><p class="small muted mt-2 mb-0">Vous pouvez combiner plusieurs formations : un seul compte, un suivi par formation.</p>
      <div class="row g-3 mt-1"><div class="col-md-6"><label class="form-label" for="pi-session">Session</label><select class="form-select" id="pi-session"><option>Rentrée de janvier 2027</option><option>Rentrée d'avril 2027</option></select></div><div class="col-md-6"><label class="form-label" for="pi-mode">Modalité</label><select class="form-select" id="pi-mode" data-chg="u-prein"><option>Présentiel (Abidjan)</option><option>En ligne</option><option>Hybride</option></select></div></div>
      <div class="form-label mt-3">Mode de paiement souhaité</div><div class="d-flex flex-column gap-2">${['En deux versements (inscription, puis solde)','En une seule fois','Prise en charge par un employeur ou un organisme'].map((x,i)=>`<label class="d-flex gap-2 align-items-center"><input class="form-check-input mt-0" type="radio" name="ppay" ${i===0?'checked':''} data-chg="u-prein" value="${esc(x)}">${x}</label>`).join('')}</div></div>
    <div class="pst-p d-none" data-p="2"><h2 class="h3 mb-3">Profil</h2><div class="row g-3">
      <div class="col-md-4"><label class="form-label" for="pi-level">Dernier diplôme</label><select class="form-select" id="pi-level"><option>BEPC</option><option selected>Baccalauréat</option><option>BTS</option><option>Licence</option><option>Master</option></select></div>
      <div class="col-md-4"><label class="form-label" for="pi-sit">Situation actuelle</label><select class="form-select" id="pi-sit"><option>Étudiant(e)</option><option>Salarié(e)</option><option>En recherche d'emploi</option><option>Entrepreneur / commerçant(e)</option></select></div>
      <div class="col-md-4"><label class="form-label" for="pi-eq">Équipement</label><select class="form-select" id="pi-eq"><option>Ordinateur portable + connexion internet</option><option>Ordinateur fixe + connexion internet</option><option>Smartphone uniquement</option></select></div>
      <div class="col-md-6"><label class="form-label" for="pi-exp">Expérience dans le domaine</label><input class="form-control" id="pi-exp" placeholder="Ex. affiches pour mon association"></div>
      <div class="col-md-6"><label class="form-label" for="pi-link">Lien vers vos réalisations (facultatif)</label><input class="form-control" id="pi-link" placeholder="Instagram, Behance, page Facebook…"></div>
      <div class="col-12"><label class="form-label" for="pi-mot">Pourquoi cette formation ?</label><textarea class="form-control" id="pi-mot" rows="3" minlength="80" required placeholder="Votre projet, vos attentes, ce que vous voulez faire après la formation" data-inp="u-pmot"></textarea><div class="d-flex justify-content-between"><div class="invalid-feedback d-block-on-invalid">Décrivez votre motivation en 80 caractères minimum.</div><span class="small muted ms-auto" id="pmot-c">0 / 80 caractères minimum</span></div></div></div>
      <h2 class="h3 mt-4 mb-3">Documents</h2><label class="dropzone d-block text-center mb-0" for="pi-doc"><i class="fa-solid fa-upload"></i><span class="dz-t">Glissez votre pièce d'identité ici</span>ou <u>parcourir</u> · PDF, JPG, PNG · 50 Mo max</label><input class="d-none" type="file" id="pi-doc" accept=".pdf,image/*" data-chg="u-pdoc"><div id="pdoc-l" class="small mt-2"></div><p class="small muted mt-2 mb-0">Photo d'identité, dernier diplôme et justificatif de domicile pourront être ajoutés après l'étude de votre profil.</p></div>
    <div class="pst-p d-none" data-p="3"><h2 class="h3 mb-3">Vérifiez vos informations</h2><div id="prein-review"></div><label class="d-flex gap-2 small mt-3"><input class="form-check-input" type="checkbox" required id="pi-cgu">J'accepte le règlement intérieur et la politique de protection des données personnelles.</label><div class="invalid-feedback">Vous devez accepter le règlement pour continuer.</div>
      <div class="alert-strip as-info slim mt-3"><i class="fa-solid fa-circle-info"></i><span>Après l'étude de votre profil sous 48 h, vous recevrez un test d'éligibilité en ligne. Le premier versement valide ensuite votre compte.</span></div></div>
    <div class="d-flex justify-content-between gap-2 mt-4 pt-3 border-top-soft"><button type="button" class="btn btn-ghost" data-act="u-pst" data-d="-1" id="pst-prev">Précédent</button><button type="button" class="btn btn-primary" data-act="u-pst" data-d="1" id="pst-next">Continuer : formation</button><button class="btn btn-primary d-none" type="submit" id="pst-send">Envoyer ma préinscription</button></div>
  </form>
  <div class="stack"><section class="dark-card" id="prein-recap"></section><section class="panel card-pad"><h3 class="h3 mb-2" style="font-size:17px">Pièces à prévoir (étape 3)</h3><p class="small muted mb-0">Pièce d'identité · photo d'identité · dernier diplôme obtenu · justificatif de domicile</p></section></div></div>
</div>`, () => { S.ui.pstep = 0; uPstPaint(); INP['u-prein'](); });
function uPstPaint(){ const i = S.ui.pstep || 0;
  $$('.pst-p').forEach(p=>p.classList.toggle('d-none', +p.dataset.p!==i)); $$('#pst li').forEach(li=>{ const k = +li.dataset.i; li.className = k<i?'done':k===i?'cur':''; li.querySelector('.pst-n').innerHTML = k<i ? '<i class="fa-solid fa-check"></i>' : k+1; });
  $('#pst-k').textContent = `Préinscription · étape ${i+1} sur 4`; $('#pst-h').textContent = U_PST[i][1];
  $('#pst-prev').style.visibility = i ? 'visible' : 'hidden'; $('#pst-next').classList.toggle('d-none', i===3); $('#pst-send').classList.toggle('d-none', i!==3);
  if (i<3) $('#pst-next').textContent = `Continuer : ${U_PST[i+1][0].toLowerCase()}`;
  if (i===3) { const sel = $$('input[name=pf]:checked').map(x=>x.value), v = id => esc($('#'+id)?.value||'—');
    $('#prein-review').innerHTML = [['Identité', `${v('pi-first')} ${v('pi-last')} · ${v('pi-mail')} · ${v('pi-tel')}`],['Formation', `${sel.map(f=>esc(F[f].name)).join(' + ')||'—'} · ${v('pi-session')} · ${v('pi-mode')}`],['Profil', `${v('pi-level')} · ${v('pi-sit')}`],["Pièce d'identité", $('#pi-doc').files.length?esc($('#pi-doc').files[0].name):'À envoyer plus tard']].map(([a,b])=>`<div class="kv border-bottom-soft py-2"><span>${a}</span><b class="text-end">${b}</b></div>`).join(''); }
}
ACT['u-pst'] = el => { const d = +el.dataset.d, i = S.ui.pstep || 0;
  if (d>0) { const p = $(`.pst-p[data-p="${i}"]`), bad = [...p.querySelectorAll('input,select,textarea')].filter(x=>!x.checkValidity());
    if (i===1 && !$$('input[name=pf]:checked').length) { toast('Choisissez au moins une formation.','warn'); return; }
    if (bad.length) { p.classList.add('was-validated'); bad[0].focus(); toast('Vérifiez les champs signalés en rouge.','warn'); return; } }
  S.ui.pstep = Math.max(0, Math.min(3, i+d)); uPstPaint(); window.scrollTo({top:0, behavior:'smooth'}); };
INP['u-prein'] = () => { const sel = $$('input[name=pf]:checked').map(i=>i.value), key = FORDER.filter(f=>sel.includes(f)).join('+'), p = PROFILES[key], r = $('#prein-recap'); if (!r) return;
  $$('.fopt').forEach(l=>l.classList.toggle('on', l.querySelector('input').checked));
  r.innerHTML = `${motif('mini-motif rc-motif')}${star('rc-star')}<div class="kc mb-2">Récapitulatif</div><h3 class="h3 mb-3" style="font-size:22px;line-height:28px">${sel.length ? sel.map(f=>esc(F[f].name)).join(' + ') : 'Aucune formation choisie'}</h3>${sel.length?`<div class="rc-kv"><span>Durée</span><b>${Math.max(...sel.map(f=>F[f].months))} mois</b></div><div class="rc-kv"><span>Session</span><b>Janvier 2027</b></div><div class="rc-kv"><span>Modalité</span><b>${esc($('#pi-mode')?.value||'')}</b></div><div class="rc-kv"><span>À l'inscription</span><b>${fcfa(sel.reduce((a,f)=>a+F[f].deposit,0))}</b></div><div class="rc-kv rc-total"><span>Coût de la formation</span><b>${fcfa(p.price)}</b></div>`:''}`; };
INP['u-pmot'] = el => { const c = $('#pmot-c'); if (c) { c.textContent = `${el.value.length} / 80 caractères minimum`; c.classList.toggle('tx-ok', el.value.length>=80); } };
INP['u-pdoc'] = el => { $('#pdoc-l').innerHTML = fileNames(el).map(n=>`<div class="file-row"><span class="ftype">${frFile(n)}</span><div class="lr-m"><b class="small">${esc(n)}</b>${uBar(100,'ok thin')}</div></div>`).join(''); };
