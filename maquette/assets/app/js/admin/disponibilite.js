/* ============ MY EVC — Disponibilité des étudiants (administration) ============
   Répond à une question simple : « Qui a reçu un projet, qui travaille dessus, qui a terminé et est libre ? »

   États d'un étudiant (calculés à partir de TOUS ses projets : fiche étudiant + module Projets) :
     - « Projet reçu »  : au moins un projet attribué mais pas encore démarré (statut afaire) ;
     - « En cours »     : au moins un projet démarré (encours) ou à reprendre (reprendre) ;
     - « Libre »        : aucun projet à faire. Ses projets sont rendus (soumis) ou validés (valide),
                          ou il n'a encore reçu aucun projet. Il peut recevoir un nouveau projet.
   Un projet rendu reste « à valider » côté administration, mais l'étudiant, lui, a fini : il est libre.

   Écrans et points d'entrée :
     - #a-disponibilite      tableau complet, filtres, sélection groupée, attribution en lot ;
     - tableau de bord admin  bandeau « Projets : qui est libre ? » ;
     - module Projets         bouton « Disponibilité des étudiants ».
   En production : une requête SQL agrégée par étudiant (voir docs/INTEGRATION.md, § Disponibilité). */

/* ---------- 1. Projets de la fiche étudiant : un jeu de démonstration réaliste ----------
   Seuls les projets réellement ATTRIBUÉS figurent sur la fiche. Les projets du catalogue
   qui n'ont pas encore été donnés restent dans « Catalogue disponible ». */
uSProj = function(s){
  S.sproj = S.sproj || {};
  if (s.id===U_DEMO) { if (!S.st) loadStudent();
    return S.st.todos.map(t=>({id:t.id, f:t.f, t:t.t, type:t.type, brief:t.brief, due:t.due, status:t.status, grade:t.grade, files:t.files, by:t.by||F[t.f].trainer, mandatory:t.mandatory, admin:!!t.admin, live:true})); }
  if (!S.sproj[s.id]) { const h = hueOf(s.id+'·dispo'), L = [];
    const slug = t => t.split(' — ')[0].toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/[^a-z]+/g,'_');
    /* Un état global par étudiant : ~40 % libres, ~20 % projet reçu, ~40 % en cours (dont quelques retards) */
    const k = h % 10, etat = s.status==='Certifié' ? 'valide' : k<3 ? 'valide' : k===3 ? 'soumis' : k<6 ? 'afaire' : k<9 ? 'encours' : 'reprendre';
    const fActive = s.f[h % s.f.length];
    const push = (f, p, i, status) => { const due = status==='valide' ? -(2 + (h+i*5)%9 + i*4) : status==='soumis' ? -((h%3)+1) : status==='afaire' ? 5 + h%9 : status==='reprendre' ? 2 : ((h>>3)%3===0 ? -2 : 3 + h%6);
      L.push({id:`${s.id}-${f}-${i}`, f, t:p[0], type:p[1], brief:p[2], due, status, grade: status==='valide' ? 11+((h+i*3)%8) : null,
        files: ['afaire','encours'].includes(status) ? [] : [slug(p[0])+'.pdf'], by:F[f].trainer, mandatory:true, admin:false}); };
    s.f.forEach((f, fi) => { const n = s.status==='Certifié' ? 4 : (k===0 && fi===0) ? 0 : 1 + (h + fi) % 3;
      U_PCAT[f].slice(0, n).forEach((p, i) => push(f, p, i, i < n-1 || f!==fActive ? 'valide' : etat)); });
    /* Les projets publiés du module « Projets » qui visent cet étudiant figurent aussi sur sa fiche */
    if (typeof uProjSeed==='function') uProjSeed().filter(p=>p.status==='Publié' && !L.some(x=>x.t===p.t) && uPTargets(p).some(x=>x.id===s.id)).forEach((p,j) => {
      const c = U_PCAT[p.f].find(x=>x[0]===p.t) || [p.t, p.type, p.brief]; push(p.f, c, 10+j, ['afaire','encours','reprendre'].includes(etat) && j===0 && p.f!==fActive ? 'afaire' : (h+j)%3===0 ? 'soumis' : 'valide'); });
    S.sproj[s.id] = L; }
  return S.sproj[s.id];
};

/* ---------- 2. Un seul état par projet ----------
   Le module Projets (uSub) et la fiche étudiant (uSProj) partagent désormais le même statut :
   approuver un dépôt dans « Projets » le valide aussi sur la fiche, et inversement. */
(() => { const base = uSub;
  uSub = function(p, s){ const r = base(p, s);
    if (s.id===U_DEMO || r._lié) return r;
    const it = uSProj(s).find(x=>x.t===p.t); if (!it) return r;
    if (['afaire','encours'].includes(it.status)) Object.assign(r, {files:[], links:[], video:'', msg:'', d:null, avis:null});
    else if (!r.files.length) Object.assign(r, {files:it.files.slice(), d:Math.min(0,it.due)});
    Object.defineProperty(r, 'status', {get:()=>it.status, set:v=>{ it.status = v; if (v==='valide' && it.grade==null) it.grade = 14; }, enumerable:true, configurable:true});
    r._lié = true; return r; };
})();

/* ---------- 3. Calcul de la charge d'un étudiant ---------- */
const D_ETATS = {
  libre:   {l:'Libre',        tone:'ok',   icon:'fa-circle-check', txt:'Prêt pour un nouveau projet'},
  recu:    {l:'Projet reçu',  tone:'warn', icon:'fa-envelope-open-text', txt:'Pas encore démarré'},
  encours: {l:'En cours',     tone:'info', icon:'fa-pen-nib', txt:'Travaille sur son projet'}
};
const D_ACTIF = ['afaire','encours','reprendre'];
/* Tous les projets d'un étudiant, sans doublon : fiche étudiant + projets publiés du module Projets */
function dProjets(s){
  const L = uSProj(s).map(p=>({...p, src:'fiche'})), seen = new Set(L.map(p=>p.t));
  if (typeof uProjSeed==='function') uProjSeed().filter(p=>p.status==='Publié' && !seen.has(p.t) && uPTargets(p).some(x=>x.id===s.id)).forEach(p => {
    const sub = uSub(p, s); seen.add(p.t);
    L.push({id:p.id, f:p.f, t:p.t, type:p.type, due:p.due, status:sub.status, files:sub.files||[], by:F[p.f].trainer, src:'module', pid:p.id}); });
  return L;
}
function dCharge(s){
  const P = dProjets(s), actifs = P.filter(p=>D_ACTIF.includes(p.status)).sort((a,b)=>a.due-b.due), finis = P.filter(p=>!D_ACTIF.includes(p.status));
  const etat = actifs.some(p=>p.status!=='afaire') ? 'encours' : actifs.length ? 'recu' : 'libre';
  const enRetard = actifs.filter(p=>p.due<0).length, aValider = P.filter(p=>p.status==='soumis').length;
  const dernierFini = finis.length ? Math.max(...finis.map(p=>Math.min(0,p.due))) : null;       // jours (≤ 0)
  return {s, P, actifs, finis, etat, enRetard, aValider, valides:P.filter(p=>p.status==='valide').length,
    courant: actifs[0] || null, libreDepuis: etat==='libre' && dernierFini!=null ? -dernierFini : null, jamais: !P.length};
}
const dEligibles = () => S.students.filter(s=>['Actif','À activer'].includes(s.status));
const dToutes = () => dEligibles().map(dCharge);
const dCompte = L => ({libre:L.filter(c=>c.etat==='libre').length, recu:L.filter(c=>c.etat==='recu').length, encours:L.filter(c=>c.etat==='encours').length,
  retard:L.filter(c=>c.enRetard).length, valider:L.reduce((a,c)=>a+c.aValider,0), jamais:L.filter(c=>c.jamais).length});

/* ---------- 4. Petits composants ---------- */
const dEtatPill = c => { const e = D_ETATS[c.etat]; return `<span class="dispo-pill dp-${c.etat}"><i class="fa-solid ${e.icon}"></i>${e.l}</span>`; };
const dEtatSous = c => c.etat==='libre' ? (c.jamais ? 'Aucun projet reçu' : c.libreDepuis===0 ? 'A terminé aujourd\'hui' : `Libre depuis ${c.libreDepuis} j`) + (c.aValider ? ` · ${c.aValider} rendu${c.aValider>1?'s':''} à valider` : '')
  : c.enRetard ? `<span class="tx-bad fw-sb">En retard de ${-c.courant.due} j</span>` : c.etat==='recu' ? `Reçu · à rendre ${dueTxt(c.courant.due).toLowerCase()}` : `À rendre ${dueTxt(c.courant.due).toLowerCase()}`;
const dStack = (n, tot) => `<div class="dispo-stack" role="img" aria-label="${n.libre} libres, ${n.recu} projet reçu, ${n.encours} en cours">${['encours','recu','libre'].map(k=>`<span class="ds-${k}" style="flex:${n[k]||0}" title="${D_ETATS[k].l} : ${n[k]}"></span>`).join('')}${tot?'':'<span class="ds-vide" style="flex:1"></span>'}</div>`;

/* ---------- 5. Écran « Disponibilité des étudiants » ---------- */
function dListe(){
  const F0 = S.ui.dsp = S.ui.dsp || {}, q = (F0.q||'').toLowerCase().trim(), etat = F0.etat || 'tous';
  const ordre = {encours:1, recu:2, libre:0};
  return dToutes().filter(c => (!F0.f || c.s.f.includes(F0.f)) && (!F0.se || c.s.sessionId===F0.se) && (!F0.lv || c.s.group===F0.lv)
      && (!q || (c.s.name+' '+c.s.id).toLowerCase().includes(q))
      && (etat==='tous' || (etat==='retard' ? c.enRetard>0 : c.etat===etat)))
    .sort((a,b) => etat==='libre' || (etat==='tous' && a.etat==='libre' && b.etat==='libre') ? (b.jamais-a.jamais) || ((b.libreDepuis||0)-(a.libreDepuis||0))
      : (ordre[a.etat]-ordre[b.etat]) || (b.enRetard-a.enRetard) || ((a.courant?.due??99)-(b.courant?.due??99)));
}
function dTable(){
  const L = dListe(), sel = S.ui.dsel = S.ui.dsel || [], per = 12, pages = Math.max(1, Math.ceil(L.length/per)), pg = Math.min(S.ui.dpg||1, pages), rows = L.slice((pg-1)*per, pg*per);
  const allOn = rows.length && rows.every(c=>sel.includes(c.s.id));
  const pager = () => { const P = []; for (let i=1;i<=pages;i++) if (i<=2 || i===pages || Math.abs(i-pg)<=1) P.push(i); else if (P[P.length-1]!=='…') P.push('…'); return P.map(i=>i==='…'?'<span>…</span>':`<button class="${i===pg?'on':''}" data-act="d-pg" data-p="${i}">${i}</button>`).join(''); };
  const libresSel = sel.filter(id => { const s = S.students.find(x=>x.id===id); return s && dCharge(s).etat==='libre'; }).length;
  return `${sel.length?`<div class="bulk-bar"><b>${sel.length} sélectionné${sel.length>1?'s':''}${libresSel&&libresSel<sel.length?` <span class="fw-normal opacity-75">· ${libresSel} libre${libresSel>1?'s':''}</span>`:''}</b><div class="ms-auto d-flex gap-2 flex-wrap">
      <button class="btn btn-primary btn-sm" data-act="d-attribuer"><i class="fa-solid fa-paper-plane"></i>Attribuer un projet</button>
      <button class="btn btn-white-line btn-sm" data-act="d-relancer">Relancer</button>
      <button class="btn btn-white-line btn-sm" data-act="d-message">Envoyer un message</button>
      <button class="btn btn-white-line btn-sm btn-icon" data-act="d-vider" aria-label="Vider la sélection"><i class="fa-solid fa-xmark"></i></button></div></div>`:''}
  <section class="panel"><div class="tbl"><table class="table dispo-table"><thead><tr>
    <th style="width:48px"><input class="form-check-input" type="checkbox" ${allOn?'checked':''} data-chg="d-sel-all" aria-label="Tout sélectionner sur la page"></th>
    <th>Étudiant</th><th>État</th><th>Projet actuel</th><th class="dt-parcours">Parcours projets</th><th class="text-end">Action</th></tr></thead>
  <tbody>${rows.map(c => { const s = c.s, p = c.courant, on = sel.includes(s.id), tot = c.P.length;
    return `<tr class="${on?'sel':''}">
      <td><input class="form-check-input" type="checkbox" ${on?'checked':''} data-chg="d-sel" data-id="${s.id}" aria-label="Sélectionner ${esc(s.name)}"></td>
      <td><div class="d-flex align-items-center gap-3">${uIni(s.name,'sm')}<div style="min-width:0"><button class="link-plain tdn" data-act="u-fiche" data-id="${s.id}">${esc(s.name)}</button><div class="tds"><span class="mono">${s.id}</span> ${s.f.map(f=>`<span class="fdot f-${f}" title="${esc(F[f].name)}">${f}</span>`).join(' ')}</div></div></div></td>
      <td>${dEtatPill(c)}<div class="tds mt-1">${dEtatSous(c)}</div></td>
      <td>${p ? `<div class="d-flex align-items-center gap-2" style="min-width:0">${fchip(p.f)}<span class="small fw-sb text-truncate" style="max-width:260px" title="${esc(p.t)}">${esc(p.t)}</span></div><div class="tds mt-1">${pill(uPStatus(p.status)[0], uPStatus(p.status)[1])}${c.actifs.length>1?` <span class="ms-1">+ ${c.actifs.length-1} autre${c.actifs.length>2?'s':''}</span>`:''}</div>`
        : `<span class="small muted">${c.jamais ? '—' : 'Aucun projet en attente'}</span>`}</td>
      <td class="dt-parcours"><div class="d-flex align-items-center gap-2"><div class="pj-dots" aria-label="${c.valides} validés sur ${tot} reçus">${c.P.slice(0,8).map(x=>`<i class="pd-${x.status}" title="${esc(x.t)} · ${uPStatus(x.status)[0]}"></i>`).join('')}</div><span class="small muted text-nowrap">${c.valides} / ${tot} validés</span></div></td>
      <td class="text-end text-nowrap">${c.etat==='libre'
        ? `<button class="btn btn-primary btn-sm" data-act="u-pj-new" data-sid="${s.id}"><i class="fa-solid fa-plus"></i>Attribuer</button>`
        : `<button class="btn btn-ghost btn-sm" data-act="d-relancer-un" data-id="${s.id}">${c.etat==='recu'?'Rappeler':'Relancer'}</button>`}
        <button class="btn btn-quiet btn-sm btn-icon" data-act="d-fiche" data-id="${s.id}" aria-label="Voir les projets de ${esc(s.name)}" title="Voir ses projets"><i class="fa-solid fa-chevron-right"></i></button></td></tr>`; }).join('')
    || `<tr><td colspan="6">${uEmpty('Aucun étudiant dans cette catégorie', 'Modifiez les filtres ou choisissez un autre état.')}</td></tr>`}</tbody></table></div>
  <div class="table-foot"><span>${L.length?`${(pg-1)*per+1}–${Math.min(pg*per,L.length)} sur ${L.length} étudiant${L.length>1?'s':''}`:'0 résultat'}</span><div class="pager">${pager()}</div></div></section>`;
}
view('a-disponibilite','admin','Disponibilité','fa-user-clock', () => {
  const T = dToutes(), n = dCompte(T), F0 = S.ui.dsp = S.ui.dsp || {}, etat = F0.etat || 'tous';
  const tuile = (k, val, label, sub, tone, icon) => `<button type="button" class="dispo-tile dt-${tone} ${etat===k?'on':''}" data-act="d-etat" data-k="${etat===k?'tous':k}" aria-pressed="${etat===k}">
      <span class="dt-i"><i class="fa-solid ${icon}"></i></span><span class="dt-v">${val}</span><span class="dt-l">${label}</span><span class="dt-s">${sub}</span></button>`;
  const parF = FORDER.map(f => { const L = T.filter(c=>c.s.f.includes(f)), m = dCompte(L);
    return `<div class="dispo-frow"><div class="d-flex align-items-center gap-2 mb-2">${fchip(f)}<span class="small fw-sb">${esc(uFShort(f))}</span><span class="small muted ms-auto">${m.libre} libre${m.libre>1?'s':''} sur ${L.length}</span></div>${dStack(m, L.length)}</div>`; }).join('');
  return `
  <div class="dispo-tiles">
    ${tuile('libre', n.libre, 'Libres', n.jamais ? `dont ${n.jamais} sans aucun projet` : 'Ont terminé leur projet', 'ok', 'fa-circle-check')}
    ${tuile('recu', n.recu, 'Projet reçu', 'Pas encore démarré', 'warn', 'fa-envelope-open-text')}
    ${tuile('encours', n.encours, 'En cours', 'Travaillent sur un projet', 'info', 'fa-pen-nib')}
    ${tuile('retard', n.retard, 'En retard', n.retard ? 'Échéance dépassée' : 'Aucun retard', 'bad', 'fa-clock')}
  </div>
  <div class="u-grid u-2-1 mt-4 align-items-start">
    <div class="dispo-legend panel card-pad">
      <div class="d-flex align-items-center flex-wrap gap-3 mb-3"><h2 class="h6 mb-0">Répartition par formation</h2>
        <span class="legend ms-auto"><span><i class="ds-libre"></i>Libre</span><span><i class="ds-recu"></i>Projet reçu</span><span><i class="ds-encours"></i>En cours</span></span></div>
      <div class="dispo-frows">${parF}</div></div>
    <div class="panel card-pad dispo-help">
      <h2 class="h6">À valider de votre côté</h2>
      <p class="small muted mb-3">Ces projets sont terminés par les étudiants. Ils restent libres pendant que vous validez.</p>
      <div class="d-flex align-items-baseline gap-2"><span class="num-key" style="font-size:34px">${n.valider}</span><span class="small muted">rendu${n.valider>1?'s':''} en attente</span></div>
      <button class="btn btn-ghost btn-sm mt-3" data-act="d-valider"><i class="fa-solid fa-eye"></i>Ouvrir la validation</button></div>
  </div>
  <div class="filters-row mt-4">
    <label class="search-field flex-grow-1 mb-0" style="min-width:220px"><i class="fa-solid fa-magnifying-glass"></i><input class="form-control" style="background:var(--surface)" placeholder="Nom ou matricule…" value="${esc(F0.q||'')}" data-inp="d-q" aria-label="Rechercher un étudiant"></label>
    <select class="form-select" data-chg="d-filtre" data-k="f" aria-label="Formation"><option value="">Toutes les formations</option>${FORDER.map(f=>`<option value="${f}" ${F0.f===f?'selected':''}>${esc(F[f].name)}</option>`).join('')}</select>
    <select class="form-select" data-chg="d-filtre" data-k="se" aria-label="Session"><option value="">Toutes les sessions</option>${S.sessions.map(s=>`<option value="${s.id}" ${F0.se===s.id?'selected':''}>${esc(s.name)}</option>`).join('')}</select>
    <select class="form-select" data-chg="d-filtre" data-k="lv" aria-label="Niveau"><option value="">Tous les niveaux</option>${U_LEVELS.map(l=>`<option ${F0.lv===l?'selected':''}>${l}</option>`).join('')}</select></div>
  <div class="mt-3 mb-3 d-flex align-items-center gap-2 flex-wrap">${uSeg([['tous',`Tous <span class="cnt">${T.length}</span>`],['libre',`Libres <span class="cnt">${n.libre}</span>`],['recu',`Projet reçu <span class="cnt">${n.recu}</span>`],['encours',`En cours <span class="cnt">${n.encours}</span>`],['retard',`En retard <span class="cnt">${n.retard}</span>`]], etat, 'd-etat', {soft:true, cls:'dispo-seg'})}<button class="btn btn-ghost btn-sm ms-auto" data-act="d-export"><i class="fa-solid fa-download"></i>Exporter</button></div>
  <div id="d-region">${dTable()}</div>`;
});
Object.assign(VIEWS['a-disponibilite'], {
  parent:'a-disponibilite',
  crumb: () => `Administration <span class="sep">›</span> Projets <span class="sep">›</span> Disponibilité des étudiants`,
  title: 'Disponibilité des étudiants',
  cta: () => `<button class="btn btn-primary t-cta" data-act="d-libres"><i class="fa-solid fa-paper-plane"></i>Donner un projet aux libres</button>`
});

/* ---------- 6. Actions ---------- */
const dPaint = () => { const r = $('#d-region'); if (r) r.innerHTML = dTable(); };
INP['d-q'] = el => { S.ui.dsp.q = el.value; S.ui.dpg = 1; dPaint(); };
INP['d-filtre'] = el => { S.ui.dsp[el.dataset.k] = el.value; S.ui.dpg = 1; rerender(); };
ACT['d-etat'] = el => { S.ui.dsp = S.ui.dsp || {}; S.ui.dsp.etat = el.dataset.k; S.ui.dpg = 1; rerender(); };
ACT['d-pg'] = el => { S.ui.dpg = +el.dataset.p; dPaint(); };
INP['d-sel'] = el => { const a = S.ui.dsel, i = a.indexOf(el.dataset.id); el.checked ? (i<0 && a.push(el.dataset.id)) : (i>=0 && a.splice(i,1)); dPaint(); };
INP['d-sel-all'] = el => { const L = dListe(), per = 12, pg = S.ui.dpg||1, ids = L.slice((pg-1)*per, pg*per).map(c=>c.s.id); S.ui.dsel = el.checked ? [...new Set([...S.ui.dsel, ...ids])] : S.ui.dsel.filter(x=>!ids.includes(x)); dPaint(); };
ACT['d-vider'] = () => { S.ui.dsel = []; dPaint(); };
ACT['d-fiche'] = el => uGo('a-fiche', {sid:el.dataset.id, ftb:'proj'});
ACT['d-valider'] = () => uGo('a-projets', {apt:'val'});
ACT['d-message'] = () => { S.ui.cmMode = 'sel'; S.ui.cmSel = [...S.ui.dsel]; go('a-communication'); };
ACT['d-libres'] = () => { S.ui.dsel = dListe().filter(c=>c.etat==='libre').map(c=>c.s.id); if (!S.ui.dsel.length) { toast('Aucun étudiant libre avec ces filtres.','info'); return; } ACT['d-attribuer'](); };
function dRelance(ids){ let n = 0; ids.forEach(id => { const s = S.students.find(x=>x.id===id), c = s && dCharge(s); if (!c || !c.courant) return; n++;
  if (s.id===U_DEMO) S.st.notifs.unshift({id:'n'+Date.now()+n, cat:'Travaux', f:c.courant.f, t:`Rappel : « ${c.courant.t} » est attendu ${dueTxt(c.courant.due).toLowerCase()}.`, d:"À l'instant", read:false}); });
  if (n) audit('Relance projets', `${n} étudiant(s)`); return n; }
ACT['d-relancer-un'] = el => { const s = S.students.find(x=>x.id===el.dataset.id); dRelance([s.id]); toast(`Rappel envoyé à ${esc(s.name)} (notification, e-mail et WhatsApp).`); };
ACT['d-relancer'] = () => { const n = dRelance(S.ui.dsel); toast(n ? `Rappel envoyé à ${n} étudiant${n>1?'s':''}. Les étudiants libres sont ignorés.` : 'Aucun étudiant sélectionné n\'a de projet en attente.', n?'ok':'info'); };
ACT['d-export'] = () => { const L = dListe(); showCSV('disponibilite-etudiants.csv', [['Matricule','Étudiant','Formations','Session','Niveau','État','Projet actuel','Échéance (j)','Projets validés','Projets reçus','Rendus à valider'],
  ...L.map(c=>[c.s.id, c.s.name, c.s.f.join('+'), c.s.session, c.s.group, D_ETATS[c.etat].l + (c.enRetard?' (en retard)':''), c.courant?.t||'', c.courant?.due??'', c.valides, c.P.length, c.aValider])]); };

/* Attribution en lot : un projet du catalogue (ou sur mesure) à tous les étudiants sélectionnés */
ACT['d-attribuer'] = () => { const ids = S.ui.dsel || [], L = ids.map(id=>S.students.find(s=>s.id===id)).filter(Boolean);
  if (!L.length) { toast('Sélectionnez au moins un étudiant.','warn'); return; }
  const fs = FORDER.filter(f=>L.some(s=>s.f.includes(f))), f0 = fs.sort((a,b)=>L.filter(s=>s.f.includes(b)).length - L.filter(s=>s.f.includes(a)).length)[0];
  const occ = L.filter(s=>dCharge(s).etat!=='libre').length;
  openModal({title:`Attribuer un projet à ${L.length} étudiant${L.length>1?'s':''}`, size:'modal-lg', body:`<form id="d-form" data-submit="d-attribuer-ok" novalidate class="row g-3">
    ${occ?`<div class="col-12"><div class="alert-strip as-warn slim"><i class="fa-solid fa-triangle-exclamation"></i><span>${occ} étudiant${occ>1?'s ont':' a'} déjà un projet en attente. Le nouveau projet s'ajoutera au sien.</span></div></div>`:''}
    <div class="col-sm-5"><label class="form-label" for="d-f">Formation</label><select class="form-select" id="d-f" data-chg="d-f">${fs.map(f=>`<option value="${f}" ${f===f0?'selected':''}>${esc(F[f].name)} (${L.filter(s=>s.f.includes(f)).length})</option>`).join('')}</select></div>
    <div class="col-sm-7"><label class="form-label" for="d-c">Projet</label><select class="form-select" id="d-c" data-chg="d-c"></select></div>
    <div class="col-12 d-none" id="d-tw"><label class="form-label" for="d-t">Intitulé</label><input class="form-control" id="d-t" placeholder="Ex. Affiche — Journée portes ouvertes EVC"><div class="invalid-feedback">Indiquez l'intitulé.</div></div>
    <div class="col-sm-6"><label class="form-label" for="d-d">À rendre le</label><input class="form-control" type="date" id="d-d" required value="${addDays(10).toISOString().slice(0,10)}" min="${TODAY.toISOString().slice(0,10)}"></div>
    <div class="col-sm-6 d-flex align-items-end"><label class="d-flex gap-2 align-items-center small"><input class="form-check-input mt-0" type="checkbox" id="d-n" checked>Prévenir (notification, e-mail, WhatsApp)</label></div>
    <div class="col-12"><label class="form-label" for="d-b">Consignes</label><textarea class="form-control" id="d-b" rows="3" required></textarea><div class="invalid-feedback">Ajoutez les consignes.</div></div>
    <div class="col-12"><div class="small-caps mb-2">Destinataires</div><div class="d-flex flex-wrap gap-2" id="d-who"></div></div></form>`,
    foot:`<button class="btn btn-ghost" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary" type="submit" form="d-form"><i class="fa-solid fa-paper-plane"></i>Attribuer</button>`,
    onShow: () => INP['d-f']() });
};
INP['d-f'] = () => { const f = $('#d-f').value, L = (S.ui.dsel||[]).map(id=>S.students.find(s=>s.id===id)).filter(Boolean);
  const déjà = t => L.filter(s=>s.f.includes(f) && dProjets(s).some(p=>p.t===t)).length;
  $('#d-c').innerHTML = U_PCAT[f].map(c=>{ const k = déjà(c[0]); return `<option value="${esc(c[0])}">${esc(c[0])}${k?` (déjà reçu par ${k})`:''}</option>`; }).join('') + '<option value="__custom">Projet sur mesure…</option>';
  const free = U_PCAT[f].find(c=>!déjà(c[0])); if (free) $('#d-c').value = free[0];
  $('#d-who').innerHTML = L.map(s => { const ok = s.f.includes(f), c = dCharge(s); return `<span class="who-chip ${ok?'':'off'}" title="${ok?D_ETATS[c.etat].l:'Ne suit pas cette formation'}">${uIni(s.name,'xs')}${esc(s.name)}${ok?'':' · ignoré'}</span>`; }).join('');
  INP['d-c'](); };
INP['d-c'] = () => { const f = $('#d-f').value, v = $('#d-c').value, c = U_PCAT[f].find(x=>x[0]===v), custom = v==='__custom';
  $('#d-tw').classList.toggle('d-none', !custom); $('#d-t').required = custom; $('#d-b').value = c ? c[2] : ''; };
ACT['d-attribuer-ok'] = form => {
  const bad = [...form.querySelectorAll('input,select,textarea')].filter(x=>!x.checkValidity()); if (bad.length) { form.classList.add('was-validated'); bad[0].focus(); return; }
  const f = $('#d-f').value, custom = $('#d-c').value==='__custom', t = custom ? $('#d-t').value.trim() : $('#d-c').value, cat = U_PCAT[f].find(x=>x[0]===t);
  const due = Math.round((new Date($('#d-d').value+'T09:00') - TODAY)/864e5), notify = $('#d-n').checked, brief = $('#d-b').value.trim();
  let n = 0, skip = 0;
  (S.ui.dsel||[]).map(id=>S.students.find(s=>s.id===id)).filter(Boolean).forEach(s => {
    if (!s.f.includes(f) || dProjets(s).some(p=>p.t===t && p.status!=='valide')) { skip++; return; }
    const P = {id:'pj'+Date.now()+'-'+n, f, t, type:cat?cat[1]:'Projet', brief, due, status:'afaire', grade:null, files:[], by:F[f].trainer, mandatory:true, admin:true, given:0};
    if (s.id===U_DEMO) { const T = {...P, hist:[{w:'Administration EVC', d:0, c:`Projet attribué. Formateur référent : ${P.by}.`}]}; S.st.todos.unshift(T); S.assigned = S.assigned || []; S.assigned.push(clone(T));
      if (notify) S.st.notifs.unshift({id:'n'+Date.now(), cat:'Travaux', f, t:`Nouveau projet : « ${t} » — à rendre le ${fmtDM(addDays(due))}.`, d:"À l'instant", read:false}); }
    else uSProj(s).unshift(P);
    n++; });
  audit('Projet attribué en lot', `${t} → ${n} étudiant(s)`); closeModal(); S.ui.dsel = []; rerender();
  toast(n ? `« ${esc(t)} » attribué à ${n} étudiant${n>1?'s':''}${notify?' · prévenus':''}.${skip?` ${skip} ignoré${skip>1?'s':''} (autre formation ou déjà en cours).`:''}` : 'Aucun étudiant ne pouvait recevoir ce projet.', n?'ok':'warn');
};

/* ---------- 7. Raccordements : menu, droits, tableau de bord, module Projets, fiche ---------- */
(() => {
  const m = MENUS.admin[0][1]; if (!m.includes('a-disponibilite')) m.splice(m.indexOf('a-projets')+1, 0, 'a-disponibilite');
  [S.perms, PERM_DEFAULT].forEach(P => Object.values(P).forEach(l => { if (l.includes('a-projets') && !l.includes('a-disponibilite')) l.push('a-disponibilite'); }));

  /* Tableau de bord : bandeau compact juste sous les chiffres clés */
  const dash = VIEWS['a-dashboard'].render;
  VIEWS['a-dashboard'].render = () => { const html = dash();
    if (!S.perms[S.adminRole].includes('a-disponibilite')) return html;
    const T = dToutes(), n = dCompte(T);
    const band = `<section class="panel dispo-band mt-4"><div class="db-h"><div><h2>Projets : qui est libre ?</h2><p class="small muted mb-0">${T.length} étudiants actifs · ${n.valider} rendu${n.valider>1?'s':''} à valider</p></div>
      <div class="ms-auto d-flex gap-2"><button class="btn btn-ghost btn-sm" data-act="d-go" data-k="tous">Voir le détail</button><button class="btn btn-primary btn-sm" data-act="d-go" data-k="libre"><i class="fa-solid fa-paper-plane"></i>Donner un projet</button></div></div>
      ${dStack(n, T.length)}
      <div class="db-legend">${[['libre','Libres'],['recu','Projet reçu'],['encours','En cours']].map(([k,l])=>`<button class="db-item" data-act="d-go" data-k="${k}"><i class="ds-${k}"></i><b>${n[k]}</b><span>${l}</span></button>`).join('')}${n.retard?`<button class="db-item" data-act="d-go" data-k="retard"><i class="ds-retard"></i><b>${n.retard}</b><span>en retard</span></button>`:''}</div></section>`;
    const i = html.indexOf('<div class="u-grid u-3-2 mt-4">'); return i>0 ? html.slice(0,i) + band + html.slice(i) : band + html; };
  ACT['d-go'] = el => { S.ui.dsp = {etat:el.dataset.k}; S.ui.dsel = []; S.ui.dpg = 1; go('a-disponibilite'); };

  /* Module Projets : accès direct */
  const pj = VIEWS['a-projets'].render;
  VIEWS['a-projets'].render = () => { const n = dCompte(dToutes());
    return `<button type="button" class="dispo-link" data-act="d-go" data-k="tous"><span class="dl-i"><i class="fa-solid fa-user-clock"></i></span><span class="lr-m"><b>Disponibilité des étudiants</b><span class="small muted d-block">${n.libre} libres · ${n.recu} projet reçu · ${n.encours} en cours${n.retard?` · ${n.retard} en retard`:''}</span></span><i class="fa-solid fa-chevron-right muted"></i></button>` + pj(); };

  /* Fiche étudiant : état de disponibilité sous l'en-tête */
  const fiche = VIEWS['a-fiche'].render;
  VIEWS['a-fiche'].render = () => { const s = S.students.find(x=>x.id===S.ui.sid) || S.students[0], c = dCharge(s);
    return `<div class="dispo-fiche"><span class="small-caps me-1">Disponibilité projets</span>${dEtatPill(c)}<span class="small muted">${dEtatSous(c)}</span>${c.etat==='libre'?`<button class="btn btn-primary btn-sm ms-auto" data-act="u-pj-new" data-sid="${s.id}"><i class="fa-solid fa-plus"></i>Attribuer un projet</button>`:''}</div>` + fiche(); };
})();
