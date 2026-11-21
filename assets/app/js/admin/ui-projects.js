/* ============ MY EVC — v10 : projets de l'étudiant (fiche admin) ============
   Projets réalisés et en cours sur la fiche étudiant, attribution de projets par l'administration.
   Pour le compte de démonstration (Aya Kouassi, EVC-26-0142), les projets sont reliés aux travaux de l'espace étudiant. */

const U_PCAT = {
  DG: [['Logo — Maquis "Chez Tantie Rose"','Projet graphique',"Concevoir le logo d'un maquis familial. 3 pistes N&B puis la piste retenue en couleur."],
       ['Affiche — Festival Abidjan Urban Arts','Travail pratique','Affiche A2 portrait, CMJN, fonds perdus 3 mm.'],
       ['Carte de visite — charte EVC','Exercice','Recto-verso 85×55 mm selon la charte EVC.'],
       ['Mise en page — magazine 8 pages','Travail pratique','Chemin de fer + maquette InDesign, grille 12 colonnes.'],
       ['Identité visuelle — boutique de pagnes','Projet graphique','Logo, palette, typographies et 3 déclinaisons (sac, étiquette, enseigne).'],
       ['Packaging — jus de bissap','Projet graphique','Étiquette de bouteille 50 cl + présentoir comptoir.'],
       ['Studio Creative — brief jury','Projet jury','Projet complet présenté devant le jury EVC.'],
       ['Pressbook — portfolio de fin de formation','Projet de fin de formation','Sélection de 10 réalisations commentées, format PDF et Behance.']],
  CM: [['Calendrier éditorial — novembre','Livrable','20 publications sur 3 plateformes, objectif et format pour chacune.'],
       ['Personas — marque de jus locale','Exercice','3 personas documentés à partir du questionnaire fourni.'],
       ['Script vidéo 30 s — lancement produit','Travail pratique','Script découpé en plans avec indications sonores.'],
       ['Audit social media — restaurant','Étude de cas','Audit des comptes, benchmark de 3 concurrents et recommandations.'],
       ['Campagne sponsorisée — salon de coiffure','Projet','Ciblage, budget, 3 visuels et rapport de performance simulé.'],
       ['RECO 60 — projet professionnel','Projet de fin de formation','60 jours de gestion réelle d\'une page, rapport final.']],
  GIA:[['Tableau de gestion des stocks — Excel','Travail pratique','Entrées, sorties, stock d\'alerte, graphique mensuel.'],
       ['Lettre de motivation — Word','Exercice','Respecter la norme AFNOR.'],
       ['Formulaire d\'inscription — Google Forms','Exercice','Logique conditionnelle, réponses vers Google Sheets.'],
       ['Présentation PowerPoint — rapport d\'activité','Travail pratique','12 diapositives, masque personnalisé, graphiques liés.'],
       ['Facturier automatisé — Excel','Projet','Numérotation automatique, TVA, export PDF.'],
       ['RECO 60 — projet bureautique','Projet de fin de formation','Dossier complet de gestion pour une PME réelle.']]
};
const U_DEMO = 'EVC-26-0142';
const uSF = s => [...new Set([...s.f, ...(s.id===U_DEMO && S.st ? S.st.todos.map(t=>t.f) : [])])];
const uPStatus = st => st==='valide' ? ['Réalisé','ok'] : st==='soumis' ? ['Rendu · à valider','warn'] : st==='reprendre' ? ['À reprendre','bad'] : st==='encours' ? ['En cours','info'] : ['À démarrer','mute'];

/* Projets d'un étudiant : reliés aux travaux réels pour Aya, générés de façon stable pour les autres */
function uSProj(s){
  S.sproj = S.sproj || {};
  if (s.id===U_DEMO) { if (!S.st) loadStudent();
    return S.st.todos.map(t=>({id:t.id, f:t.f, t:t.t, type:t.type, brief:t.brief, due:t.due, status:t.status, grade:t.grade, files:t.files, by:t.by||F[t.f].trainer, mandatory:t.mandatory, admin:!!t.admin, live:true})); }
  if (!S.sproj[s.id]) { const h = hueOf(s.id), done = Math.round(s.progress/100*3); const L = [];
    s.f.forEach((f,fi)=>{ U_PCAT[f].slice(0, 4).forEach((p,i)=>{ const k = (h+i+fi)%7;
      const status = s.status==='Certifié' || i<done ? 'valide' : i===done ? ['encours','soumis','reprendre'][k%3] : 'afaire';
      L.push({id:`${s.id}-${f}-${i}`, f, t:p[0], type:p[1], brief:p[2], due: status==='valide' ? -(30-i*6) : (i-done)*6 + (k%3===2?-2:3), status, grade: status==='valide' ? 11+((h+i*3)%8) : null, files: status==='afaire' ? [] : [p[0].split(' — ')[0].toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g,'').replace(/[^a-z]+/g,'_')+'.pdf'], by:F[f].trainer, mandatory:true, admin:false}); }); });
    S.sproj[s.id] = L; }
  return S.sproj[s.id];
}
/* Les projets attribués à Aya survivent à un rechargement du compte étudiant */
function uMergeAssigned(){ if (!S.st || !S.assigned) return; S.assigned.forEach(t=>{ if (!S.st.todos.find(x=>x.id===t.id)) S.st.todos.unshift(clone(t)); }); }

function uProjRow(p, sid){
  const st = uPStatus(p.status), done = p.status==='valide';
  return `<div class="box-row proj-row c-${p.f}"><span class="proj-ic"><i class="fa-solid ${done?'fa-check':p.type.includes('graphique')||p.f==='DG'?'fa-pen-nib':p.f==='CM'?'fa-bullhorn':'fa-file-lines'}"></i></span>
    <div class="lr-m"><div class="d-flex align-items-center gap-2 flex-wrap"><b class="proj-t">${esc(p.t)}</b>${p.admin?pill('Attribué par l\'admin','info'):''}${p.mandatory?'':pill('Facultatif','grey')}</div>
    <div class="small muted mt-1">${fchip(p.f)} <span class="ms-1">${esc(p.type)} · ${esc(p.by)} · ${done?`terminé le ${fmtDM(addDays(p.due))}`:`échéance ${fmtDM(addDays(p.due))}`}${p.files&&p.files.length?` · ${p.files.length} fichier${p.files.length>1?'s':''}`:''}</span></div></div>
    ${done?`${p.grade!=null?uAppPill(p.grade):''}`:`<span class="d-none d-md-inline">${pill(dueTxt(p.due), dueTone(p.due))}</span>`}
    ${pill(st[0], st[1])}
    <div class="dropdown"><button class="btn btn-quiet btn-sm btn-icon" data-bs-toggle="dropdown" aria-label="Actions"><i class="fa-solid fa-ellipsis"></i></button><ul class="dropdown-menu dropdown-menu-end">
      <li><button class="dropdown-item" data-act="u-pj-view" data-sid="${sid}" data-id="${p.id}">Voir le détail</button></li>
      ${done?`<li><button class="dropdown-item" data-act="fake-dl" data-n="${esc(p.files?.[0]||'rendu.pdf')}">Télécharger le rendu</button></li>`:`<li><button class="dropdown-item" data-act="u-pj-remind" data-sid="${sid}" data-id="${p.id}">Relancer l'étudiant</button></li><li><button class="dropdown-item" data-act="u-pj-due" data-sid="${sid}" data-id="${p.id}">Reporter l'échéance (+7 j)</button></li>`}
      ${p.admin && p.status==='afaire'?`<li><hr class="dropdown-divider"></li><li><button class="dropdown-item text-danger" data-act="u-pj-del" data-sid="${sid}" data-id="${p.id}">Retirer le projet</button></li>`:''}</ul></div></div>`;
}

function uFicheProjects(s){
  const P = uSProj(s), f = S.ui.pjf || 'all', L = P.filter(p=>f==='all'||p.f===f);
  const done = L.filter(p=>p.status==='valide'), cur = L.filter(p=>p.status!=='valide').sort((a,b)=>a.due-b.due), late = cur.filter(p=>p.due<0).length;
  const avg = done.filter(p=>p.grade!=null), m = avg.length ? avg.reduce((a,p)=>a+p.grade,0)/avg.length : null;
  return `<div class="d-flex align-items-center gap-2 flex-wrap mb-3">${uSF(s).length>1?uSeg([['all','Toutes'],...uSF(s).map(x=>[x,uFShort(x)])], f, 'u-pjf'):`<span class="muted">${esc(F[s.f[0]].name)}</span>`}<button class="btn btn-primary ms-auto" data-act="u-pj-new" data-sid="${s.id}"><i class="fa-solid fa-plus me-1"></i>Attribuer un projet</button></div>
    <div class="grid g-kpi mb-3">${uKpi('Projets réalisés', done.length, `sur ${L.length} projets`, 'fa-check', 'ok', '', 'compact')}${uKpi('En cours', cur.length, cur.filter(p=>p.status==='soumis').length+' à valider', 'fa-pen-nib', 'lav', '', 'compact')}${uKpi('En retard', late, late?'À relancer':'Aucun retard', 'fa-clock', late?'cor':'ok', late?'bad':'ok', 'compact')}${uKpi('Appréciation globale', m==null?'—':uApp(m)[0], m==null?'Aucun projet approuvé':'sur les projets approuvés', 'fa-chart-column', 'warn', '', 'compact')}</div>
    <div class="u-grid u-2-1"><div class="stack">
      ${uCard(`Projets en cours <span class="cnt">${cur.length}</span>`, cur.length ? cur.map(p=>uProjRow(p, s.id)).join('') : uEmpty('Aucun projet en cours','Attribuez un projet pour lancer un nouveau travail.', `<button class="btn btn-ghost btn-sm" data-act="u-pj-new" data-sid="${s.id}">Attribuer un projet</button>`))}
      ${uCard(`Projets réalisés <span class="cnt">${done.length}</span>`, done.length ? done.sort((a,b)=>b.due-a.due).map(p=>uProjRow(p, s.id)).join('') : uEmpty('Aucun projet réalisé pour le moment'))}</div>
    <div class="stack">${uCard('Avancement par formation', uSF(s).map(x=>{ const A = P.filter(p=>p.f===x), d = A.filter(p=>p.status==='valide').length; return `<div class="mb-3"><div class="d-flex justify-content-between small fw-sb mb-1"><span>${fchip(x)} <span class="ms-1">${uFShort(x)}</span></span><b>${d} / ${A.length}</b></div>${uBar(A.length?d/A.length*100:0)}</div>`; }).join(''))}
      ${uCard('Catalogue disponible', uSF(s).map(x=>{ const used = P.map(p=>p.t), free = U_PCAT[x].filter(c=>!used.includes(c[0])); return free.length ? `<div class="small-caps mb-2 mt-1">${uFShort(x)}</div>${free.slice(0,3).map(c=>`<div class="d-flex align-items-center gap-2 py-2 border-bottom-soft"><div class="lr-m"><div class="fw-sb small">${esc(c[0])}</div><div class="small muted">${esc(c[1])}</div></div><button class="btn btn-ghost btn-sm" data-act="u-pj-new" data-sid="${s.id}" data-f="${x}" data-t="${esc(c[0])}">Attribuer</button></div>`).join('')}` : ''; }).join('') || '<p class="small muted mb-0">Tous les projets du catalogue sont déjà attribués.</p>')}</div></div>`;
}
uReg('pjf');

ACT['u-pj-new'] = el => { const s = S.students.find(x=>x.id===el.dataset.sid), used = uSProj(s).map(p=>p.t), firstFree = uSF(s).find(f=>U_PCAT[f].some(c=>!used.includes(c[0]))) || uSF(s)[0];
  const f0 = el.dataset.f || firstFree, t0 = el.dataset.t || (U_PCAT[f0].find(c=>!used.includes(c[0]))||[''])[0];
  const opts = f => U_PCAT[f].map(c=>`<option ${c[0]===t0?'selected':''} ${used.includes(c[0])?'disabled':''}>${esc(c[0])}${used.includes(c[0])?' (déjà attribué)':''}</option>`).join('') + '<option value="__custom">Projet personnalisé…</option>';
  openModal({title:`Attribuer un projet à ${esc(s.name)}`, size:'modal-lg', body:`<form id="pj-form" data-submit="u-pj-save" data-sid="${s.id}" novalidate class="row g-3">
    <div class="col-sm-5"><label class="form-label" for="pj-f">Formation</label><select class="form-select" id="pj-f" data-chg="u-pj-f">${uSF(s).map(f=>`<option value="${f}" ${f===f0?'selected':''}>${esc(F[f].name)}</option>`).join('')}</select></div>
    <div class="col-sm-7"><label class="form-label" for="pj-c">Projet</label><select class="form-select" id="pj-c" data-chg="u-pj-c">${opts(f0)}</select></div>
    <div class="col-12 d-none" id="pj-tw"><label class="form-label" for="pj-t">Intitulé du projet</label><input class="form-control" id="pj-t" placeholder="Ex. Affiche — Journée portes ouvertes EVC"><div class="invalid-feedback">Indiquez l'intitulé.</div></div>
    <div class="col-sm-6"><label class="form-label" for="pj-ty">Type</label><select class="form-select" id="pj-ty">${['Projet graphique','Travail pratique','Exercice','Livrable','Étude de cas','Projet','Projet jury','Projet de fin de formation'].map(x=>`<option>${x}</option>`).join('')}</select></div>
    <div class="col-sm-6"><label class="form-label" for="pj-d">Échéance</label><input class="form-control" type="date" id="pj-d" required value="${addDays(14).toISOString().slice(0,10)}" min="${TODAY.toISOString().slice(0,10)}"><div class="invalid-feedback">Choisissez une date à venir.</div></div>
    <div class="col-12"><label class="form-label" for="pj-b">Consignes</label><textarea class="form-control" id="pj-b" rows="3" required></textarea><div class="invalid-feedback">Ajoutez les consignes du projet.</div></div>
    <div class="col-sm-6"><label class="form-label" for="pj-by">Formateur référent</label><select class="form-select" id="pj-by">${[...new Set([F[f0].trainer, ...S.trainers.map(t=>t.name)])].map(n=>`<option>${esc(n)}</option>`).join('')}</select></div>
    <div class="col-sm-6 d-flex flex-column justify-content-end gap-2"><label class="d-flex gap-2 align-items-center small"><input class="form-check-input mt-0" type="checkbox" id="pj-m" checked>Projet obligatoire (compte pour la certification)</label><label class="d-flex gap-2 align-items-center small"><input class="form-check-input mt-0" type="checkbox" id="pj-n" checked>Prévenir l'étudiant (notification, e-mail et WhatsApp)</label></div></form>`,
    foot:`<button class="btn btn-ghost" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary" type="submit" form="pj-form">Attribuer le projet</button>`,
    onShow: () => INP['u-pj-c']() });
};
INP['u-pj-f'] = () => { const f = $('#pj-f').value, s = S.students.find(x=>x.id===$('#pj-form').dataset.sid), used = uSProj(s).map(p=>p.t); $('#pj-c').innerHTML = U_PCAT[f].map(c=>`<option ${used.includes(c[0])?'disabled':''}>${esc(c[0])}${used.includes(c[0])?' (déjà attribué)':''}</option>`).join('') + '<option value="__custom">Projet personnalisé…</option>'; const by = $('#pj-by'); if (by) by.value = F[f].trainer; { const o = [...$('#pj-c').options].find(x=>!x.disabled); if (o) $('#pj-c').value = o.value; } INP['u-pj-c'](); };
INP['u-pj-c'] = () => { const f = $('#pj-f').value, v = $('#pj-c').value, c = U_PCAT[f].find(x=>x[0]===v), custom = v==='__custom';
  $('#pj-tw').classList.toggle('d-none', !custom); $('#pj-t').required = custom;
  if (c) { $('#pj-b').value = c[2]; $('#pj-ty').value = c[1]; } else if (custom) { $('#pj-b').value = ''; $('#pj-t').focus(); } };
ACT['u-pj-save'] = form => { const s = S.students.find(x=>x.id===form.dataset.sid);
  const bad = [...form.querySelectorAll('input,select,textarea')].filter(x=>!x.checkValidity()); if (bad.length) { form.classList.add('was-validated'); bad[0].focus(); return; }
  const f = $('#pj-f').value, t = $('#pj-c').value==='__custom' ? $('#pj-t').value.trim() : $('#pj-c').value, due = Math.round((new Date($('#pj-d').value+'T09:00') - TODAY)/864e5);
  if (uSProj(s).some(p=>p.t===t && p.status!=='valide')) { toast('Ce projet est déjà en cours pour cet étudiant.','warn'); return; }
  const P = {id:'pj'+Date.now(), f, t, type:$('#pj-ty').value, brief:$('#pj-b').value.trim(), due, status:'afaire', grade:null, files:[], by:$('#pj-by').value, mandatory:$('#pj-m').checked, admin:true};
  if (s.id===U_DEMO) { const T = {...P, hist:[{w:'Administration EVC', d:0, c:`Projet attribué. Formateur référent : ${P.by}.`}]}; S.st.todos.unshift(T); S.assigned = S.assigned || []; S.assigned.push(clone(T));
    if ($('#pj-n').checked) S.st.notifs.unshift({id:'n'+Date.now(), cat:'Travaux', f, t:`Nouveau projet : « ${t} » — à rendre le ${fmtDM(addDays(due))}.`, d:"À l'instant", read:false}); }
  else uSProj(s).unshift(P);
  audit('Projet attribué', `${t} → ${s.name} (${s.id})`); closeModal(); S.ui.ftb = 'proj'; rerender();
  toast(`Projet attribué à ${esc(s.name)}${$('#pj-n')?.checked!==false?' · étudiant prévenu':''}.`);
};
const uPjFind = el => { const s = S.students.find(x=>x.id===el.dataset.sid); return [s, uSProj(s).find(p=>p.id===el.dataset.id)]; };
const uPjSrc = (s, id) => s.id===U_DEMO ? S.st.todos.find(t=>t.id===id) : uSProj(s).find(p=>p.id===id);
ACT['u-pj-view'] = el => { const [s, p] = uPjFind(el), st = uPStatus(p.status), T = s.id===U_DEMO ? S.st.todos.find(t=>t.id===p.id) : null;
  openModal({title: esc(p.t), body:`<div class="d-flex gap-2 flex-wrap mb-3">${fchip(p.f)}${pill(st[0],st[1])}${p.admin?pill('Attribué par l\'admin','info'):''}</div>
    <div class="kv-col mb-2"><span>Étudiant</span><b>${esc(s.name)} · ${s.id}</b></div><div class="kv-col mb-2"><span>Type</span><b>${esc(p.type)}</b></div><div class="kv-col mb-2"><span>Formateur référent</span><b>${esc(p.by)}</b></div><div class="kv-col mb-3"><span>${p.status==='valide'?'Terminé le':'Échéance'}</span><b>${fmtLong(addDays(p.due))}</b></div>
    <div class="small-caps mb-1">Consignes</div><p>${esc(p.brief||'—')}</p>
    ${p.files&&p.files.length?`<div class="small-caps mb-1">Fichiers rendus</div>${p.files.map(n=>`<div class="file-row"><span class="ftype">${frFile(n)}</span><span class="flex-grow-1">${esc(n)}</span><button class="link-u" data-act="fake-dl" data-n="${esc(n)}">Télécharger</button></div>`).join('')}`:'<p class="small muted">Aucun fichier rendu pour le moment.</p>'}
    ${T&&T.hist&&T.hist.length?`<div class="small-caps mb-1 mt-3">Historique</div><ul class="act-list">${T.hist.map(h=>`<li><span class="ad i9"></span><div><div>${esc(h.c)}</div><div class="small muted">${esc(h.w)} · ${relDay(h.d)}</div></div></li>`).join('')}</ul>`:''}
    ${p.grade!=null?`<div class="dark-card mt-3 d-flex justify-content-between align-items-center"><span>Appréciation</span><b style="font-size:20px">${uApp(p.grade)[0]}</b></div>`:''}`,
    foot: p.status==='soumis' ? `<button class="btn btn-ghost" data-act="u-pj-dec" data-dec="reprendre" data-sid="${s.id}" data-id="${p.id}">Demander une reprise</button><button class="btn btn-primary" data-act="u-pj-dec" data-dec="valide" data-sid="${s.id}" data-id="${p.id}">Valider le projet</button>` : `<button class="btn btn-ghost" data-bs-dismiss="modal">Fermer</button>`});
};
ACT['u-pj-dec'] = el => { const [s, p] = uPjFind(el), src = uPjSrc(s, p.id), ok = el.dataset.dec==='valide';
  src.status = el.dataset.dec; if (ok && src.grade==null) src.grade = 14; if (ok) src.due = 0; if (src.hist) src.hist.push({w:'Administration EVC', d:0, c: ok ? 'Projet validé définitivement.' : 'Reprise demandée par l\'administration.'});
  audit(ok?'Projet validé':'Reprise demandée', `${p.t} — ${s.name}`); closeModal(); rerender(); toast(ok ? 'Projet validé. Il passe dans les projets réalisés.' : 'Reprise demandée. L\'étudiant est prévenu.'); };
ACT['u-pj-remind'] = el => { const [s, p] = uPjFind(el); if (s.id===U_DEMO) S.st.notifs.unshift({id:'n'+Date.now(), cat:'Travaux', f:p.f, t:`Rappel : « ${p.t} » est attendu ${dueTxt(p.due).toLowerCase()}.`, d:"À l'instant", read:false}); audit('Relance projet', `${p.t} — ${s.name}`); toast(`Relance envoyée à ${esc(s.name)} (notification, e-mail, WhatsApp).`); };
ACT['u-pj-due'] = el => { const [s, p] = uPjFind(el), src = uPjSrc(s, p.id); src.due += 7; if (src.hist) src.hist.push({w:'Administration EVC', d:0, c:`Échéance reportée au ${fmtDM(addDays(src.due))}.`}); audit('Échéance reportée', `${p.t} — ${s.name}`); rerender(); toast(`Nouvelle échéance : ${fmtDM(addDays(src.due))}.`); };
ACT['u-pj-del'] = el => { const [s, p] = uPjFind(el);
  if (s.id===U_DEMO) { S.st.todos = S.st.todos.filter(t=>t.id!==p.id); S.assigned = (S.assigned||[]).filter(t=>t.id!==p.id); } else S.sproj[s.id] = S.sproj[s.id].filter(x=>x.id!==p.id);
  audit('Projet retiré', `${p.t} — ${s.name}`); rerender(); toast('Projet retiré de la fiche.'); };

/* ----- Branchement sur la fiche étudiant : onglet « Projets » + résumé dans la synthèse ----- */
(() => { const base = VIEWS['a-fiche'].render;
  VIEWS['a-fiche'].render = () => { const s = S.students.find(x=>x.id===S.ui.sid) || S.students[0], tab = S.ui.ftb || 'syn';
    let html = base();
    const P = uSProj(s), nd = P.filter(p=>p.status==='valide').length, nc = P.length - nd;
    html = html.replace(`data-k="doc" role="tab"`, `data-k="proj" role="tab" aria-selected="${tab==='proj'}">Projets <span class="cnt">${P.length}</span></button></li><li class="nav-item"><button class="nav-link ${tab==='doc'?'active':''}" data-act="u-ftb" data-k="doc" role="tab"`);
    html = html.replace(/<button class="nav-link ([^"]*)" data-act="u-ftb" data-k="proj"/, (m,c)=>`<button class="nav-link ${tab==='proj'?'active':''}" data-act="u-ftb" data-k="proj"`);
    if (tab==='proj') { const i = html.indexOf('</ul>', html.indexOf('nav nav-tabs')); html = html.slice(0, i+5) + uFicheProjects(s); }
    if (tab==='syn') { const card = uCard('Projets', `<div class="d-flex gap-4 mb-3"><div><div class="num-key" style="font-size:28px">${nd}</div><div class="small muted">réalisés</div></div><div><div class="num-key" style="font-size:28px">${nc}</div><div class="small muted">en cours</div></div></div>${P.filter(p=>p.status!=='valide').sort((a,b)=>a.due-b.due).slice(0,3).map(p=>`<div class="d-flex align-items-center gap-2 py-2 border-top-soft"><span class="lr-m small fw-sb text-truncate">${esc(p.t)}</span>${pill(uPStatus(p.status)[0], uPStatus(p.status)[1])}</div>`).join('')}<div class="d-flex gap-2 mt-3 flex-wrap"><button class="btn btn-ghost btn-sm" data-act="u-ftb" data-k="proj">Voir tous les projets</button><button class="btn btn-primary btn-sm" data-act="u-pj-new" data-sid="${s.id}">Attribuer</button></div>`);
      const k = html.indexOf('<section class="panel', html.lastIndexOf('<div class="stack">')); if (k>0) html = html.slice(0,k) + card + html.slice(k); }
    return html; };
})();
