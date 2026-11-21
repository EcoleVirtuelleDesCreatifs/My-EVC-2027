/* ============ MY EVC — Bibliothèque numérique (espace étudiant) ============
   Une vraie bibliothèque : chaque ressource est un livre (couverture, tranche, pages),
   rangé sur les étagères de son rayon. L'étudiant y trouve logiciels, e-books, polices,
   ressources graphiques, modèles, supports de cours et tutoriels, et les télécharge.

   - Chaque étudiant voit les ressources de SES formations (DG, CM, GIA) et celles destinées à tous.
     Le sélecteur de formation de la barre du haut filtre la bibliothèque comme les autres écrans.
   - Les ressources viennent de S.lib (données : LIB dans noyau/data.js). L'administration et les
     formateurs les publient depuis admin/bibliotheque.js : un ajout apparaît aussitôt en rayon.
   - Logiciels et polices du commerce : lien vers le site officiel (jamais de copie de logiciel payant).

   Écran : #e-bibliotheque (menu principal, après « Mes projets »).
     - Accueil : étagères « Indispensables », « Nouveautés », puis un rayon par catégorie ;
     - un rayon, une recherche ou « Mon étagère » : une bibliothèque pleine page, livres sur planches ;
     - un livre : sa fiche (grande couverture, informations, Télécharger / Site officiel). */

/* ---------- 1. Rayons (catégories) ---------- */
const LIB_CATS = {
  logiciels:  {l:'Logiciels',             k:'Logiciel',          icon:'fa-laptop-code', tone:'lav'},
  ebooks:     {l:'E-books',               k:'E-book',            icon:'fa-book',        tone:'cor'},
  polices:    {l:'Polices',               k:'Polices',           icon:'fa-font',        tone:'ok'},
  graphiques: {l:'Ressources graphiques', k:'Ressources',        icon:'fa-images',      tone:'warn'},
  modeles:    {l:'Modèles',               k:'Modèle',            icon:'fa-file-lines',  tone:'info'},
  supports:   {l:'Supports de cours',     k:'Support de cours',  icon:'fa-chalkboard',  tone:'lav'},
  tutos:      {l:'Tutoriels vidéo',       k:'Tutoriel vidéo',    icon:'fa-circle-play', tone:'cor'}
};
const LIB_NOUVEAU = 7;                                  // « Nouveau » pendant 7 jours

/* Couleurs de couverture par rayon : [fond, accent] — choisies selon l'identifiant, toujours les mêmes */
const LIB_COUV = {
  ebooks:     [['#1E1B4B','#F2C76E'],['#7A1F3D','#F6D7A7'],['#0F4C5C','#F2C76E'],['#4B2A7B','#FF8A6B'],['#8C3B1F','#F6D7A7'],['#1F5130','#E9D8A6']],
  logiciels:  [['#26346B','#8FB3FF'],['#0E5E6F','#9FE3D6'],['#3C3489','#FF8A6B'],['#173B2F','#A7E3B5']],
  polices:    [['#F4EEE2','#7A1F3D'],['#ECE8F6','#3A3591'],['#E6EFE8','#1F5130'],['#F7E9E2','#C2401F']],
  graphiques: [['#EE5A36','#FFD9A8'],['#C2255C','#FFC9DC'],['#2F6BD8','#BFD4FF'],['#D88A17','#FFF0C2']],
  modeles:    [['#2F5FB8','#DCE7FF'],['#1F8F5A','#D5F2E3'],['#3A3591','#E4E2F3']],
  supports:   [['#1E1B4B','#EE5A36'],['#2B2766','#FF8A6B'],['#123B4A','#EE5A36']],
  tutos:      [['#14182B','#EE5A36'],['#1B1640','#FF8A6B']]
};

/* ---------- 2. Outils communs (aussi utilisés par l'administration et la fiche formation) ---------- */
const libCat = r => LIB_CATS[r.cat] || LIB_CATS.supports;
const libIsNew = r => r.d >= -LIB_NOUVEAU;
const libExterne = r => !!r.url;
const libAdded = r => r.d === 0 ? "Aujourd'hui" : fmtD(addDays(r.d), {day:'numeric', month:'long', year:'numeric'});
const libFChip = r => r.f === 'ALL' ? pill('Toutes formations', 'mute', 1) : fchip(r.f);

const libFor = fs => S.lib.filter(r => r.pub !== false && (r.f === 'ALL' || fs.includes(r.f)));
const libDone = () => (S.st.libDl = S.st.libDl || []);
const libGot = r => !!S.st && libDone().includes(r.id);

/* Titre de couverture : sans la parenthèse finale (« Adobe Creative Cloud (Photoshop…) » → « Adobe Creative Cloud ») */
const libTitreCouv = r => r.t.replace(/\s*\([^)]*\)\s*$/, '');

/* Un livre. Taille : '' (étagère), 'lg' (fiche), 'xs' (vignette de tableau ou de liste) */
function libBook(r, size = ''){
  const h = hueOf(r.id), pal = LIB_COUV[r.cat] || LIB_COUV.supports, [c, a] = pal[h % pal.length];
  if (size === 'xs') return `<span class="book bk-xs bk-${r.cat}" style="--c:${c};--a:${a}" aria-hidden="true"><i class="fa-solid ${libCat(r).icon}"></i></span>`;
  const dims = size ? '' : `--w:${124 + h % 3 * 8}px;--h:${178 + h % 4 * 8}px;`;
  const art = { logiciels:`<span class="bk-art"><i class="fa-solid fa-laptop-code"></i></span>`, polices:`<span class="bk-art bk-aa">Aa</span>`,
    tutos:`<span class="bk-art bk-play"><i class="fa-solid fa-play"></i></span>`, graphiques:`<span class="bk-art bk-shapes"><i></i><i></i><i></i></span>` }[r.cat] || '';
  return `<span class="book bk-${r.cat} ${size ? 'bk-' + size : ''}" style="${dims}--c:${c};--a:${a}">
    <span class="bk-band"></span><span class="bk-k">${esc(libCat(r).k)}</span>${art}
    <span class="bk-t">${esc(libTitreCouv(r))}</span>
    <span class="bk-f"><span>${r.f === 'ALL' ? 'EVC' : r.f}</span><span>${libExterne(r) ? 'Web' : esc(r.fmt.split(' · ')[0])}</span></span>
    ${libIsNew(r) ? '<span class="bk-new">Nouveau</span>' : ''}${libGot(r) ? '<span class="bk-mark" title="Téléchargé"><i class="fa-solid fa-check"></i></span>' : ''}
  </span>`;
}
/* Livre cliquable, posé sur une étagère */
const libSlot = r => `<button type="button" class="bk-slot" data-act="lib-voir" data-id="${r.id}" title="${esc(r.t)}" aria-label="${esc(libCat(r).k)} : ${esc(r.t)}">${libBook(r)}</button>`;

/* Ligne compacte (fiche formation) */
const libRow = r => `<div class="lib-row">${libBook(r, 'xs')}<div class="lr-m"><div class="lr-t">${esc(r.t)}</div><div class="lr-s">${esc(libCat(r).l)} · ${esc(r.fmt)}${r.size !== '—' ? ` · ${esc(r.size)}` : ''}</div></div>${libBtn(r, true)}</div>`;

/* Bouton principal : « Télécharger », « Regarder » ou « Site officiel » */
function libBtn(r, icon = false, cls = 'btn-sm'){
  const ext = libExterne(r), vid = r.fmt === 'Vidéo', label = ext ? 'Site officiel' : vid ? 'Regarder' : 'Télécharger';
  const ic = ext ? 'fa-arrow-up-right-from-square' : vid ? 'fa-play' : 'fa-download';
  return icon
    ? `<button class="btn btn-quiet btn-sm btn-icon" data-act="lib-dl" data-id="${r.id}" aria-label="${label} : ${esc(r.t)}" title="${label}"><i class="fa-solid ${ic}"></i></button>`
    : `<button class="btn btn-primary ${cls}" data-act="lib-dl" data-id="${r.id}"><i class="fa-solid ${ic}"></i>${libGot(r) && !ext ? `${label} à nouveau` : label}</button>`;
}

/* ---------- 3. Étagères ---------- */
const libTri = L => { const t = (S.ui.lib || {}).tri || 'recent';
  return L.slice().sort({recent:(a,b)=>b.d-a.d, populaire:(a,b)=>b.dl-a.dl, az:(a,b)=>a.t.localeCompare(b.t,'fr')}[t]); };
const libN = n => `${n} ouvrage${n > 1 ? 's' : ''}`;

/* Une étagère horizontale (accueil de la bibliothèque) */
const libShelf = (plaque, titre, L, voir = '') => !L.length ? '' : `
  <section class="shelf">
    <header class="shelf-h"><span class="shelf-plate">${plaque}</span><h2>${titre}</h2><span class="shelf-n">${libN(L.length)}</span>${voir ? `<button type="button" class="shelf-all" data-act="lib-cat" data-k="${voir}">Tout le rayon<i class="fa-solid fa-chevron-right"></i></button>` : ''}</header>
    <div class="shelf-books">${L.map(libSlot).join('')}</div>
    <div class="shelf-plank" aria-hidden="true"></div>
  </section>`;

/* Bibliothèque pleine page : les livres se rangent sur autant de planches que nécessaire */
const libCase = L => `<div class="shelf-grid">${L.map(libSlot).join('')}</div>`;
const libVide = (t, s, btn = '') => `<div class="case-empty"><i class="fa-solid fa-book-open"></i><h3>${t}</h3><p>${s}</p>${btn}</div>`;

function libContenu(){
  const U = S.ui.lib, all = libFor(vf()), q = U.q.trim().toLowerCase();
  if (q) { const L = libTri(all.filter(r => (r.t + ' ' + r.desc + ' ' + r.fmt + ' ' + libCat(r).l).toLowerCase().includes(q)));
    return `<div class="case-h"><h2>Résultats pour « ${esc(U.q)} »</h2><span class="shelf-n">${libN(L.length)}</span></div>${L.length ? libCase(L) : libVide('Aucun ouvrage trouvé', 'Essayez un autre mot : « Excel », « polices », « mockup »…')}`; }
  if (U.cat === 'mes') { const L = libTri(all.filter(libGot));
    return `<div class="case-h"><span class="shelf-plate"><i class="fa-solid fa-bookmark"></i></span><h2>Mon étagère</h2><span class="shelf-n">${libN(L.length)}</span></div>${L.length ? libCase(L) : libVide('Votre étagère est vide', 'Les ouvrages que vous téléchargez se rangent ici.', `<button class="btn btn-ghost btn-sm" data-act="lib-cat" data-k="">Parcourir les rayons</button>`)}`; }
  if (U.cat) { const L = libTri(all.filter(r => r.cat === U.cat)), c = LIB_CATS[U.cat];
    return `<div class="case-h"><span class="shelf-plate">Rayon ${Object.keys(LIB_CATS).indexOf(U.cat) + 1}</span><h2>${esc(c.l)}</h2><span class="shelf-n">${libN(L.length)}</span></div>${L.length ? libCase(L) : libVide('Rayon vide pour le moment', 'Vos formateurs le complètent au fil des séances.')}`; }
  return libShelf('<i class="fa-solid fa-star"></i>', 'Indispensables de votre formation', all.filter(r => r.key))
    + libShelf('<i class="fa-solid fa-wand-magic-sparkles"></i>', 'Nouveautés', libTri(all.filter(libIsNew)))
    + Object.entries(LIB_CATS).map(([k, c], i) => libShelf(`Rayon ${i + 1}`, esc(c.l), libTri(all.filter(r => r.cat === k)), k)).join('');
}

/* ---------- 4. Écran ---------- */
view('e-bibliotheque', 'etu', 'Bibliothèque', 'fa-book-bookmark', () => {
  const U = S.ui.lib = S.ui.lib || {cat:'', q:'', tri:'recent'}, all = libFor(vf()), n = k => all.filter(r => r.cat === k).length;
  const rayons = [['', 'Tous les rayons', all.length, 'fa-building-columns'], ...Object.entries(LIB_CATS).map(([k, c]) => [k, c.l, n(k), c.icon]), ['mes', 'Mon étagère', all.filter(libGot).length, 'fa-bookmark']];
  const maj = all.length ? Math.max(...all.map(r => r.d)) : 0, vitrine = libTri(all.filter(r => r.cat === 'ebooks')).slice(0, 4);
  return `
  <section class="lib-hall">
    <div class="lh-txt">
      <span class="lh-k"><i class="fa-solid fa-building-columns"></i>Bibliothèque numérique EVC</span>
      <h2>Tous vos ouvrages, rangés par rayon</h2>
      <p>${libN(all.length)} · ${Object.keys(LIB_CATS).filter(n).length} rayons · ${vf().length > 1 ? 'formations ' + vf().join(', ') : esc(uFShort(vf()[0]))} · mise à jour ${maj === 0 ? "aujourd'hui" : `il y a ${-maj} jour${maj < -1 ? 's' : ''}`}</p>
      <div class="lh-search">
        <label class="search-field mb-0 flex-grow-1"><i class="fa-solid fa-magnifying-glass"></i><input class="form-control" placeholder="Rechercher un livre, un logiciel, une police…" value="${esc(U.q)}" data-inp="lib-q" aria-label="Rechercher dans la bibliothèque"></label>
        <select class="form-select" data-chg="lib-tri" aria-label="Trier les ouvrages">${[['recent','Plus récents'],['populaire','Plus téléchargés'],['az','De A à Z']].map(([k,l]) => `<option value="${k}" ${U.tri===k?'selected':''}>${l}</option>`).join('')}</select>
      </div>
    </div>
    <div class="lh-stack" aria-hidden="true">${vitrine.map(r => libBook(r)).join('')}</div>
  </section>
  <div class="lib-layout">
    <nav class="lib-rayons" aria-label="Rayons de la bibliothèque">${rayons.map(([k, l, c, ic]) => `<button type="button" class="ray ${U.cat===k && !U.q ?'on':''}" data-act="lib-cat" data-k="${k}"><i class="fa-solid ${ic}"></i><span>${esc(l)}</span><b>${c}</b></button>`).join('')}</nav>
    <div class="bookcase" id="lib-region">${libContenu()}</div>
  </div>`;
});
Object.assign(VIEWS['e-bibliotheque'], {
  crumb: () => `Espace étudiant <span class="sep">›</span> Bibliothèque`,
  title: 'Bibliothèque'
});

/* ---------- 5. Fiche d'un livre et téléchargement ---------- */
ACT['lib-voir'] = el => {
  const r = S.lib.find(x => x.id === el.dataset.id); if (!r) return;
  const ligne = (k, v) => v ? `<div class="lib-dl-row"><span>${k}</span><b>${v}</b></div>` : '';
  openModal({ title: `<i class="fa-solid ${libCat(r).icon} me-2"></i>${esc(libCat(r).l)}`, size: 'modal-lg',
    body: `<div class="bk-fiche">
      <div class="bf-cover">${libBook(r, 'lg')}</div>
      <div class="bf-info">
        <h3>${esc(r.t)}</h3>
        <p class="bf-desc">${esc(r.desc)}</p>
        <div class="lib-dl">${ligne('Formation', r.f === 'ALL' ? 'Toutes les formations' : `${fchip(r.f)} ${esc(uFShort(r.f))}`)}${ligne('Format', esc(r.fmt))}${r.size !== '—' ? ligne('Taille', esc(r.size)) : ''}${ligne('Licence', esc(r.lic))}${ligne('Compatible', esc(r.os || ''))}${ligne('En rayon depuis', libAdded(r))}${ligne('Téléchargements', nfr(r.dl))}</div>
        ${libExterne(r) ? `<div class="alert-strip as-info mt-3"><i class="fa-solid fa-shield-halved"></i><span>Téléchargez toujours depuis le site officiel de l'éditeur.${r.cat === 'logiciels' ? ' Un tutoriel d’installation est disponible dans « Vidéos des cours ».' : ''}</span></div>` : ''}
        ${r.lic === 'Réservé EVC' ? `<p class="small muted mt-3 mb-0"><i class="fa-solid fa-lock me-1"></i>Ouvrage réservé aux étudiants de l'EVC : merci de ne pas le partager en dehors de l'école.</p>` : ''}
        <div class="bf-act">${libBtn(r, false, 'btn-lg')}${libGot(r) ? '<span class="small muted"><i class="fa-solid fa-bookmark me-1"></i>Dans mon étagère</span>' : ''}</div>
      </div></div>` });
};

ACT['lib-dl'] = el => {
  const r = S.lib.find(x => x.id === el.dataset.id); if (!r) return;
  if (S.role === 'etu' && S.st) {
    if (!libGot(r)) { libDone().push(r.id); r.dl++; }
    if (typeof livePush === 'function') livePush({uid: liveMe().id, txt: `${libExterne(r) ? 'Ouvre le site officiel de' : 'Télécharge'} « ${r.t} »`, ic: 'fa-solid fa-download', kind: 'dl'});
  }
  if (libExterne(r)) { window.open(r.url, '_blank', 'noopener'); toast(`Site officiel ouvert : <b>${esc(r.t)}</b>`, 'info'); }
  else toast(`${r.fmt === 'Vidéo' ? 'Lecture' : 'Téléchargement'} : <b>${esc(r.t)}</b>${r.size !== '—' ? ` (${esc(r.size)})` : ''} · rangé dans « Mon étagère »`, 'ok');
  if ($('#modal').classList.contains('show')) closeModal();
  if (S.view === 'e-bibliotheque') rerender();
};

/* ---------- 6. Rayons, recherche, tri ---------- */
const libPaint = () => { const z = $('#lib-region'); if (z) z.innerHTML = libContenu();
  $$('.lib-rayons .ray').forEach(b => b.classList.toggle('on', !S.ui.lib.q && b.dataset.k === S.ui.lib.cat)); };
INP['lib-q'] = el => { S.ui.lib.q = el.value; libPaint(); };
INP['lib-tri'] = el => { S.ui.lib.tri = el.value; libPaint(); };
ACT['lib-cat'] = el => { S.ui.lib = S.ui.lib || {cat:'', q:'', tri:'recent'}; Object.assign(S.ui.lib, {cat: el.dataset.k, q: ''}); rerender(); };

/* ---------- 7. Raccordements : menu, pastille « nouveautés », notifications ---------- */
(() => {
  const m = MENUS.etu[0][1]; if (!m.includes('e-bibliotheque')) m.splice(m.indexOf('e-todo') + 1, 0, 'e-bibliotheque');
  MENUS.etu.forEach(g => { if (g[0]) g[1] = g[1].filter(x => x !== 'e-bibliotheque'); });

  /* Notifications : catégorie « Bibliothèque » (nouvelle ressource publiée) */
  if (typeof U_NCAT === 'object') U_NCAT['Bibliothèque'] = ['fa-book-bookmark', 'lav', 'Ouvrir la bibliothèque', 'e-bibliotheque'];

  /* Pastille du menu : nouveautés pas encore téléchargées */
  const base = menuCount;
  menuCount = id => id === 'e-bibliotheque' && S.role === 'etu' && S.st ? libFor(fl()).filter(r => libIsNew(r) && !libGot(r)).length : base(id);
})();
