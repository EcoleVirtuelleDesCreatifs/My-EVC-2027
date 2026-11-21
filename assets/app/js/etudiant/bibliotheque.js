/* ============ MY EVC — Bibliothèque (espace étudiant) ============
   Une rubrique où l'étudiant télécharge tout ce qu'il lui faut pour travailler :
   logiciels, e-books, polices, ressources graphiques, modèles, supports de cours et tutoriels.

   - Chaque étudiant voit les ressources de SES formations (DG, CM, GIA) et celles destinées à tous.
     Le sélecteur de formation de la barre du haut filtre la bibliothèque comme les autres écrans.
   - Les ressources viennent de S.lib (données : LIB dans noyau/data.js). L'administration et les
     formateurs les publient depuis admin/bibliotheque.js : un ajout apparaît aussitôt ici.
   - Logiciels et polices du commerce : lien vers le site officiel (jamais de copie de logiciel payant).

   Écran : #e-bibliotheque (menu principal, après « Mes projets »). */

/* ---------- 1. Catégories et outils communs (aussi utilisés par l'administration) ---------- */
const LIB_CATS = {
  logiciels:  {l:'Logiciels',              icon:'fa-laptop-code',  tone:'lav'},
  ebooks:     {l:'E-books',                icon:'fa-book',         tone:'cor'},
  polices:    {l:'Polices',                icon:'fa-font',         tone:'ok'},
  graphiques: {l:'Ressources graphiques',  icon:'fa-images',       tone:'warn'},
  modeles:    {l:'Modèles',                icon:'fa-file-lines',   tone:'info'},
  supports:   {l:'Supports de cours',      icon:'fa-chalkboard',   tone:'lav'},
  tutos:      {l:'Tutoriels vidéo',        icon:'fa-circle-play',  tone:'cor'}
};
const LIB_NOUVEAU = 7;                                  // « Nouveau » pendant 7 jours
const libCat = r => LIB_CATS[r.cat] || LIB_CATS.supports;
const libIsNew = r => r.d >= -LIB_NOUVEAU;
const libExterne = r => !!r.url;
const libAdded = r => r.d === 0 ? "Aujourd'hui" : fmtD(addDays(r.d), {day:'numeric', month:'long', year:'numeric'});
const libFChip = r => r.f === 'ALL' ? pill('Toutes formations', 'mute', 1) : fchip(r.f);
const libIcon = (r, cls = '') => `<span class="lib-ic t-${libCat(r).tone} ${cls}" aria-hidden="true"><i class="fa-solid ${libCat(r).icon}"></i></span>`;

/* Ressources publiées visibles pour une liste de formations */
const libFor = fs => S.lib.filter(r => r.pub !== false && (r.f === 'ALL' || fs.includes(r.f)));
const libDone = () => (S.st.libDl = S.st.libDl || []);
const libGot = r => libDone().includes(r.id);

/* Ligne compacte (fiche formation, tableau de bord de formation) */
const libRow = r => `<div class="lib-row">${libIcon(r, 'sm')}<div class="lr-m"><div class="lr-t">${esc(r.t)}</div><div class="lr-s">${esc(libCat(r).l)} · ${esc(r.fmt)}${r.size !== '—' ? ` · ${esc(r.size)}` : ''}</div></div>${libBtn(r, true)}</div>`;

/* Bouton principal : « Télécharger » ou « Site officiel » selon la ressource */
function libBtn(r, icon = false){
  const ext = libExterne(r), label = ext ? 'Site officiel' : r.fmt === 'Vidéo' ? 'Regarder' : 'Télécharger';
  const ic = ext ? 'fa-arrow-up-right-from-square' : r.fmt === 'Vidéo' ? 'fa-play' : 'fa-download';
  return icon
    ? `<button class="btn btn-quiet btn-sm btn-icon" data-act="lib-dl" data-id="${r.id}" aria-label="${label} : ${esc(r.t)}" title="${label}"><i class="fa-solid ${ic}"></i></button>`
    : `<button class="btn ${libGot(r) ? 'btn-ghost' : 'btn-primary'} btn-sm" data-act="lib-dl" data-id="${r.id}"><i class="fa-solid ${libGot(r) && !ext ? 'fa-check' : ic}"></i>${libGot(r) && !ext ? 'Téléchargé' : label}</button>`;
}

/* ---------- 2. Écran étudiant ---------- */
function libFiltered(){
  const U = S.ui.lib = S.ui.lib || {cat:'', q:'', tri:'recent'};
  let L = libFor(vf());
  if (U.cat === 'mes') L = L.filter(libGot);
  else if (U.cat) L = L.filter(r => r.cat === U.cat);
  const q = U.q.trim().toLowerCase();
  if (q) L = L.filter(r => (r.t + ' ' + r.desc + ' ' + r.fmt).toLowerCase().includes(q));
  const tri = {recent:(a,b)=>b.d-a.d, populaire:(a,b)=>b.dl-a.dl, az:(a,b)=>a.t.localeCompare(b.t,'fr')}[U.tri];
  return L.slice().sort(tri);
}

function libCard(r){
  return `<article class="lib-item" data-act="lib-voir" data-id="${r.id}" role="button" tabindex="0" aria-label="${esc(r.t)}">
    <div class="li-top">${libIcon(r)}<div class="li-badges">${libIsNew(r) ? pill('Nouveau', 'acc', 1) : ''}${libFChip(r)}</div></div>
    <h3 class="li-t">${esc(r.t)}</h3>
    <p class="li-d">${esc(r.desc)}</p>
    <div class="li-meta"><span>${esc(r.fmt)}</span>${r.size !== '—' ? `<span>${esc(r.size)}</span>` : ''}<span>${esc(r.lic)}</span></div>
    <div class="li-foot">${libBtn(r)}<span class="small muted ms-auto" title="Téléchargements"><i class="fa-solid fa-arrow-down me-1"></i>${nfr(r.dl)}</span></div>
  </article>`;
}

function libGrid(){
  const L = libFiltered(), U = S.ui.lib;
  if (!L.length) return U.cat === 'mes'
    ? uEmpty('Aucun téléchargement pour le moment', 'Les ressources que vous téléchargez apparaîtront ici.', `<button class="btn btn-ghost btn-sm" data-act="lib-cat" data-k="">Parcourir la bibliothèque</button>`)
    : uEmpty('Aucune ressource trouvée', U.q ? `Aucun résultat pour « ${esc(U.q)} ».` : 'Cette rubrique sera bientôt complétée par vos formateurs.');
  return `<div class="lib-grid">${L.map(libCard).join('')}</div>`;
}

view('e-bibliotheque', 'etu', 'Bibliothèque', 'fa-book-bookmark', () => {
  const U = S.ui.lib = S.ui.lib || {cat:'', q:'', tri:'recent'}, all = libFor(vf());
  const n = k => all.filter(r => r.cat === k).length, mes = all.filter(libGot).length;
  const cles = !U.cat && !U.q ? all.filter(r => r.key) : [];
  const chips = [['', 'Tout', all.length, 'fa-layer-group'], ...Object.entries(LIB_CATS).filter(([k]) => n(k)).map(([k, c]) => [k, c.l, n(k), c.icon]), ['mes', 'Mes téléchargements', mes, 'fa-clock-rotate-left']];
  return `
  <div class="lib-head">
    <p class="muted mb-0">Logiciels, e-books, polices, modèles et supports de ${vf().length > 1 ? 'vos formations' : `la formation ${esc(uFShort(vf()[0]))}`}, à télécharger quand vous voulez.</p>
    <div class="lib-tools">
      <label class="search-field mb-0 flex-grow-1"><i class="fa-solid fa-magnifying-glass"></i><input class="form-control" style="background:var(--surface)" placeholder="Rechercher une ressource…" value="${esc(U.q)}" data-inp="lib-q" aria-label="Rechercher une ressource"></label>
      <select class="form-select" data-chg="lib-tri" aria-label="Trier" style="max-width:190px">${[['recent','Plus récentes'],['populaire','Plus téléchargées'],['az','De A à Z']].map(([k,l]) => `<option value="${k}" ${U.tri===k?'selected':''}>${l}</option>`).join('')}</select>
    </div>
  </div>
  <div class="lib-cats" role="tablist" aria-label="Catégories">${chips.map(([k, l, c, ic]) => `<button type="button" role="tab" class="lib-cat ${U.cat===k?'on':''}" data-act="lib-cat" data-k="${k}" aria-selected="${U.cat===k}"><i class="fa-solid ${ic}"></i>${esc(l)}<span class="cnt">${c}</span></button>`).join('')}</div>
  ${cles.length ? `<section class="lib-key"><div class="lk-h"><h2>Pour bien démarrer</h2><span class="small muted">Les indispensables de votre formation</span></div><div class="lk-list">${cles.map(r => `<div class="lk-item">${libIcon(r, 'sm')}<div class="lr-m"><div class="lr-t">${esc(r.t)}</div><div class="lr-s">${esc(libCat(r).l)} · ${esc(r.lic)}</div></div>${libBtn(r, true)}</div>`).join('')}</div></section>` : ''}
  <div id="lib-region">${libGrid()}</div>`;
});
Object.assign(VIEWS['e-bibliotheque'], {
  crumb: () => `Espace étudiant <span class="sep">›</span> Bibliothèque`,
  title: 'Bibliothèque'
});

/* ---------- 3. Fiche d'une ressource et téléchargement ---------- */
ACT['lib-voir'] = el => {
  const r = S.lib.find(x => x.id === el.dataset.id); if (!r) return;
  const ligne = (k, v) => v ? `<div class="lib-dl-row"><span>${k}</span><b>${v}</b></div>` : '';
  openModal({ title: `${libIcon(r, 'sm')}<span class="ms-2">${esc(r.t)}</span>`,
    body: `<p>${esc(r.desc)}</p>
      <div class="lib-dl">${ligne('Catégorie', esc(libCat(r).l))}${ligne('Formation', r.f === 'ALL' ? 'Toutes les formations' : `${fchip(r.f)} ${esc(uFShort(r.f))}`)}${ligne('Format', esc(r.fmt))}${r.size !== '—' ? ligne('Taille', esc(r.size)) : ''}${ligne('Licence', esc(r.lic))}${ligne('Compatible', esc(r.os || ''))}${ligne('Ajoutée le', libAdded(r))}${ligne('Téléchargements', nfr(r.dl))}</div>
      ${libExterne(r) ? `<div class="alert-strip as-info mt-3"><i class="fa-solid fa-shield-halved"></i><span>Téléchargez toujours depuis le site officiel de l'éditeur. ${r.cat === 'logiciels' ? 'Un tutoriel d’installation est disponible dans « Vidéos des cours ».' : ''}</span></div>` : ''}
      ${r.lic === 'Réservé EVC' ? `<p class="small muted mt-3 mb-0"><i class="fa-solid fa-lock me-1"></i>Ressource réservée aux étudiants de l'EVC : merci de ne pas la partager en dehors de l'école.</p>` : ''}`,
    foot: `<button class="btn btn-ghost" data-bs-dismiss="modal">Fermer</button>${libBtn(r).replace('btn-sm', '')}` });
};
document.addEventListener('keydown', e => { if ((e.key === 'Enter' || e.key === ' ') && e.target.matches?.('.lib-item')) { e.preventDefault(); ACT['lib-voir'](e.target); } });

ACT['lib-dl'] = el => {
  const r = S.lib.find(x => x.id === el.dataset.id); if (!r) return;
  if (S.role === 'etu' && S.st) {
    if (!libGot(r)) { libDone().push(r.id); r.dl++; }
    if (typeof livePush === 'function') livePush({uid: liveMe().id, txt: `${libExterne(r) ? 'Ouvre le site officiel de' : 'Télécharge'} « ${r.t} »`, ic: 'fa-solid fa-download', kind: 'dl'});
  }
  if (libExterne(r)) { window.open(r.url, '_blank', 'noopener'); toast(`Site officiel ouvert : <b>${esc(r.t)}</b>`, 'info'); }
  else toast(`${r.fmt === 'Vidéo' ? 'Lecture' : 'Téléchargement'} : <b>${esc(r.t)}</b>${r.size !== '—' ? ` (${esc(r.size)})` : ''}`, 'ok');
  if ($('#modal').classList.contains('show')) closeModal();
  if (S.view === 'e-bibliotheque') rerender();
};

/* ---------- 4. Filtres ---------- */
const libPaint = () => { const z = $('#lib-region'); if (z) z.innerHTML = libGrid(); };
INP['lib-q'] = el => { S.ui.lib.q = el.value; libPaint(); };
INP['lib-tri'] = el => { S.ui.lib.tri = el.value; libPaint(); };
ACT['lib-cat'] = el => { S.ui.lib = S.ui.lib || {cat:'', q:'', tri:'recent'}; S.ui.lib.cat = el.dataset.k; rerender(); };

/* ---------- 5. Raccordements : menu, pastille « nouveautés », accueil ---------- */
(() => {
  const m = MENUS.etu[0][1]; if (!m.includes('e-bibliotheque')) m.splice(m.indexOf('e-todo') + 1, 0, 'e-bibliotheque');
  MENUS.etu.forEach(g => { if (g[0]) g[1] = g[1].filter(x => x !== 'e-bibliotheque'); });

  /* Notifications : catégorie « Bibliothèque » (nouvelle ressource publiée) */
  if (typeof U_NCAT === 'object') U_NCAT['Bibliothèque'] = ['fa-book-bookmark', 'lav', 'Ouvrir la bibliothèque', 'e-bibliotheque'];

  /* Pastille du menu : nouvelles ressources pas encore téléchargées */
  const base = menuCount;
  menuCount = id => id === 'e-bibliotheque' && S.role === 'etu' && S.st ? libFor(fl()).filter(r => libIsNew(r) && !libGot(r)).length : base(id);
})();
