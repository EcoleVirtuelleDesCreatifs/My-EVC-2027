/* ============ MY EVC — v9 : espace formateur selon la charte UI ============ */
uReg('tcf','tng','tnm');
const uTStudents = () => tStudents();
const uTNow = () => { const P = tPlan(); return P.filter(x=>x.d===0).sort((a,b)=>a.h-b.h); };

/* ----- Tableau de bord ----- */
view('t-dashboard','form','Tableau de bord','fa-house-chimney', () => {
  const P = tPlan(), today = uTNow(), wk = P.filter(x=>x.d>=-3 && x.d<=3), q = tQueue().filter(x=>x.st!=='done'), sts = uTStudents();
  const next = today.find(x=>x.h+durH(x.hl) > TODAY.getHours()) || today[0];
  const byT = {}; q.forEach(x => { (byT[x.t] = byT[x.t] || []).push(x); });
  const follow = [...sts.filter(s=>s.presence<80).map(s=>[s,'absences non justifiées',`${Math.max(2,Math.round((100-s.presence)/8))} absences non justifiées`,'bad']), ...sts.slice(2,3).map(s=>[s,'',`2 travaux en retard`,'warn']), ...sts.slice(5,6).map(s=>[s,'','Moyenne sous 10/20','bad'])].slice(0,3);
  const done = chaptersDone('DG'), tot = chapters('DG');
  return `<div class="grid g-kpi">
    ${uKpi('Séances cette semaine', wk.length, `${today.length} aujourd'hui`, 'fa-calendar-days', 'lav')}
    ${uKpi('Copies à corriger', q.length, `${q.filter(x=>x.d<=-4).length} depuis plus de 4 jours`, 'fa-file-lines', 'warn', 'warn')}
    ${uKpi('Présence moyenne', '91 %', '+2 pts ce mois-ci', 'fa-clipboard-check', 'ok', 'ok')}
    ${uKpi('Étudiants suivis', sts.length, '2 groupes · rattrapages', 'fa-users', 'c')}
  </div>
  <div class="u-grid u-1-1 mt-4">
    ${uCard("Séances d'aujourd'hui", today.length ? today.map(x=>{ const cur = next && x.id===next.id; const [a,b] = uH(x.hl).split(' – '); return `<div class="box-row ${cur?'sel':''}"><div class="tblock"><b>${a}</b><small>${b}</small></div><div class="lr-m"><div class="fw-sb" style="font-size:16px">${esc(x.t)}</div><div class="small muted">Design graphique · ${x.g} · ${x.g==='Rattrapage'?4:sts.length} étudiants · ${x.mode==='Virtuel'?'visio':'présentiel'}</div></div>${cur?`<button class="btn btn-primary btn-sm" data-act="u-appel" data-id="${x.id}">Faire l'appel</button>`:`<button class="btn btn-ghost btn-sm" data-act="u-appel" data-id="${x.id}">Préparer</button>`}</div>`; }).join('') : uEmpty("Pas de séance aujourd'hui",'Profitez-en pour avancer vos corrections.',`<a class="btn btn-ghost btn-sm" href="#t-corrections">Corriger</a>`))}
    ${uCard('Copies à corriger', Object.entries(byT).map(([t,L])=>`<div class="lrow clickable" data-act="u-tq-open" data-id="${L[0].id}"><div class="lr-m"><div class="lr-t">${esc(t)}</div><div class="lr-s">Design graphique · ${x0(L)}</div></div><div class="text-end"><div class="num-key" style="font-size:22px;line-height:26px">${L.length}</div><div class="small muted">copie${L.length>1?'s':''}</div></div></div>`).join('') || uEmpty('Tout est corrigé','Les nouveaux dépôts apparaîtront ici.'), {tools:`<a class="link-u" href="#t-corrections">Tout corriger</a>`})}
  </div>
  <div class="u-grid u-2-1 mt-4">
    ${uCard('Mes niveaux', `<div class="u-grid u-1-1" style="gap:16px">${[['Design graphique · Débutant', S.sessions[0].name, sts.length, done/tot*100, 91, '86 %'],['Design graphique · Avancé', S.sessions[0].name, Math.max(6,sts.length-4), Math.max(0,done-1)/tot*100, 87, '78 %']].map(([t,se,n,p,pr,av])=>`<div class="box-row d-block m-0"><div class="fw-sb" style="font-size:16px">${t}</div><div class="small muted mb-3">${esc(se)} · ${n} étudiants</div><div class="d-flex justify-content-between small fw-sb mb-1"><span>Avancement du programme</span><b>${Math.round(p)} %</b></div>${uBar(p)}<div class="d-flex justify-content-between small muted mt-3"><span>Présence moyenne ${pr} %</span><span>Compétences acquises ${av}</span></div></div>`).join('')}</div>`)}
    ${uCard('Étudiants à suivre', follow.map(([s,,txt,tone])=>`<div class="d-flex align-items-center gap-3 py-2 border-bottom-soft">${uIni(s.name)}<div class="lr-m"><div class="fw-sb">${esc(s.name)}</div><div class="small fw-sb tx-${tone}">${txt}</div></div><button class="link-u" data-act="msg-new">Écrire</button></div>`).join('') || '<p class="muted small mb-0">Aucun étudiant en difficulté.</p>')}
  </div>`;
});
function x0(L){ const d = Math.max(...L.map(x=>x.d)); return d===0 ? "rendu aujourd'hui" : `rendu ${relDay(d).toLowerCase()}`; }
Object.assign(VIEWS['t-dashboard'], {title:'Bonjour Marc', cta:`<button class="btn btn-primary t-cta" data-act="u-plan-new"><i class="fa-solid fa-plus"></i>Planifier une séance</button>`});
ACT['u-appel'] = el => uGo('t-seances', {tsess: el.dataset.id});
ACT['u-tq-open'] = el => uGo('t-corrections', {tqs: el.dataset.id, tcf:'todo'});
ACT['u-plan-new'] = () => openModal({title:'Planifier une séance', body:`<form id="upn" data-submit="u-plan-save" class="row g-3" novalidate><div class="col-12"><label class="form-label" for="upn-t">Thème de la séance</label><input class="form-control" id="upn-t" required placeholder="Ex. Atelier affiche — correction collective"><div class="invalid-feedback">Indiquez un thème.</div></div><div class="col-6"><label class="form-label" for="upn-d">Date</label><input class="form-control" type="date" id="upn-d" value="${iso(addDays(5))}" required></div><div class="col-6"><label class="form-label" for="upn-h">Horaire</label><select class="form-select" id="upn-h"><option>09h00 – 12h00</option><option>14h00 – 17h00</option><option selected>18h00 – 20h00</option></select></div><div class="col-6"><label class="form-label" for="upn-g">Niveau</label><select class="form-select" id="upn-g"><option>Débutant</option><option>Avancé</option><option>Rattrapage</option></select></div><div class="col-6"><label class="form-label" for="upn-m">Type</label><select class="form-select" id="upn-m"><option value="Virtuel">En ligne (visio)</option><option value="Présentiel">Présentiel</option></select></div></form>`, foot:`<button class="btn btn-ghost" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary" type="submit" form="upn">Planifier et notifier</button>`});
ACT['u-plan-save'] = () => { const hl = $('#upn-h').value; tPlan().push({id:'tp-x'+Date.now(), g:$('#upn-g').value, n:null, d:dayOff($('#upn-d').value), hl, t:$('#upn-t').value.trim(), mode:$('#upn-m').value, h:parseInt(hl)}); audit('Séance planifiée', $('#upn-t').value); closeModal(); rerender(); toast('Séance planifiée. Les étudiants du groupe sont notifiés.'); };

/* ----- Séances et présences (appel) ----- */
view('t-seances','form','Séances et présences','fa-clipboard-check', () => {
  const P = tPlan(), up = P.filter(x=>x.d>=0).sort((a,b)=>a.d-b.d||a.h-b.h), x = P.find(p=>p.id===S.ui.tsess) || uTNow().find(p=>p.h+durH(p.hl)>TODAY.getHours()) || up[0];
  S.ui.tsess = x.id; const sts = x.g==='Rattrapage' ? uTStudents().slice(0,4) : uTStudents();
  S.att2 = S.att2 || {}; const A = S.att2[x.id] = S.att2[x.id] || Object.fromEntries(sts.map((s,i)=>[s.id, i<4 ? (i===1?'retard':i===3?'absent':'present') : null]));
  const c = k => sts.filter(s=>A[s.id]===k).length, nm = sts.filter(s=>!A[s.id]).length, virt = x.mode==='Virtuel', r = x.n ? tRes().find(z=>z.n===x.n) : null;
  S.cr = S.cr || {}; S.attOk = S.attOk || {};
  const tg = (s, k, l) => `<button class="att-b ${A[s.id]===k?'on '+k:''}" data-act="u-att" data-s="${s.id}" data-v="${k}" aria-pressed="${A[s.id]===k}">${l}</button>`;
  return `<div class="d-flex justify-content-end mb-3"><label class="d-flex align-items-center gap-2 small fw-sb mb-0">Séance <select class="form-select form-select-sm" style="width:auto;min-width:280px" data-chg="u-tsess" aria-label="Choisir la séance">${up.slice(0,10).map(p=>`<option value="${p.id}" ${p.id===x.id?'selected':''}>${fmtD(addDays(p.d),{weekday:'short',day:'numeric',month:'short'})} · ${uH(p.hl).split(' – ')[0]} · ${esc(p.t)} (${p.g})</option>`).join('')}</select></label></div>
  <section class="panel card-pad d-flex align-items-center gap-4 flex-wrap"><div class="kv-col"><span>Horaire</span><b>${uH(x.hl)}</b></div><div class="kv-col"><span>Niveau</span><b>Design graphique · ${x.g}</b></div><div class="kv-col"><span>Séance</span><b>${x.n?`${x.n} · `:''}${esc(x.t)}</b></div><div class="kv-col"><span>Lieu</span><b>${virt?'Visio':'Salle Bassam'}</b></div>
    <div class="ms-auto d-flex gap-2 flex-wrap"><button class="btn btn-ghost" data-act="qr-full"><i class="fa-solid fa-qrcode"></i>QR code</button>${virt?`<a class="btn btn-indigo" href="https://meet.google.com/evc-dg-${x.g.slice(0,2).toLowerCase()}" target="_blank" rel="noopener"><i class="fa-solid fa-video"></i>Lancer la visio</a>`:`<button class="btn btn-indigo" data-act="qr-full"><i class="fa-solid fa-expand"></i>Projeter le QR code</button>`}</div></section>
  <div class="u-grid u-2-1 mt-4"><section class="panel"><div class="panel-h panel-sep"><h2>Feuille de présence</h2><div class="tools">${pill(`${c('present')} présent${c('present')>1?'s':''}`,'ok')}${pill(`${c('retard')} retard${c('retard')>1?'s':''}`,'warn')}${pill(`${c('absent')} absent${c('absent')>1?'s':''}`,'bad')}${nm?pill(`${nm} non marqué${nm>1?'s':''}`,'grey'):''}</div></div>
    <div class="d-flex justify-content-end px-4 py-3 border-bottom-soft">${S.attOk[x.id]?pill('Appel validé','ok'):''}<button class="btn btn-ghost btn-sm ms-2" data-act="u-att-all">Tous présents</button></div>
    ${sts.map(s=>`<div class="row-item att-row">${uIni(s.name)}<div class="ri-main"><div class="ri-t">${esc(s.name)}</div><div class="ri-s">Assiduité ${s.presence} %</div></div><div class="att-g" role="group" aria-label="Présence de ${esc(s.name)}">${tg(s,'present','Présent')}${tg(s,'retard','Retard')}${tg(s,'absent','Absent')}</div></div>`).join('')}
    <div class="table-foot"><span>${sts.length} étudiants affichés sur ${sts.length}</span><button class="btn btn-primary" data-act="u-att-ok">Valider l'appel</button></div></section>
  <div class="stack">${uCard('Objectifs de la séance', `<ul class="mb-0 ps-3">${[`Comprendre : ${x.t.replace(/^Rattrapage — /,'')}`,'Réaliser un exercice guidé en atelier','Préparer le travail de la séance suivante'].map(o=>`<li class="mb-1">${esc(o)}</li>`).join('')}</ul>`)}
    ${uCard('Ressources partagées', `${r && r.files.length ? r.files.map(f=>`<div class="lrow"><span class="ftype">${frFile(f)}</span><div class="lr-m"><div class="lr-t" style="font-size:14px">${esc(f)}</div><div class="lr-s">${r.st}</div></div></div>`).join('') : '<p class="small muted">Aucune ressource pour cette séance.</p>'}${x.n?`<button class="btn btn-ghost w-100 mt-3 dashed" data-act="t-res-up" data-n="${x.n}"><i class="fa-solid fa-plus"></i>Ajouter une ressource</button>`:''}`)}
    ${uCard('Compte rendu', `<textarea class="form-control" rows="4" placeholder="Points abordés, difficultés, travail demandé…" data-inp="u-cr" data-id="${x.id}" aria-label="Compte rendu">${esc(S.cr[x.id]||'')}</textarea><button class="btn btn-ghost btn-sm mt-3" data-act="u-cr-save">Publier aux étudiants</button>`)}</div></div>`;
});
Object.assign(VIEWS['t-seances'], {title:() => { const x = tPlan().find(p=>p.id===S.ui.tsess); return x ? esc(x.t) : 'Séances et présences'; }, crumb:() => { const x = tPlan().find(p=>p.id===S.ui.tsess); return `Espace formateur <span class="sep">›</span> Séances${x?` <span class="sep">›</span> ${fmtD(addDays(x.d),{weekday:'long',day:'numeric',month:'long'})}`:''}`; }});
INP['u-tsess'] = el => { S.ui.tsess = el.value; rerender(); };
ACT['u-att'] = el => { S.att2[S.ui.tsess][el.dataset.s] = el.dataset.v; rerender(); };
ACT['u-att-all'] = () => { const A = S.att2[S.ui.tsess]; Object.keys(A).forEach(k=>A[k]='present'); rerender(); toast('Tous les étudiants sont marqués présents.','info'); };
ACT['u-att-ok'] = () => { const A = S.att2[S.ui.tsess], nm = Object.values(A).filter(v=>!v).length; if (nm) { toast(`${nm} étudiant${nm>1?'s':''} non marqué${nm>1?'s':''}. Complétez l'appel avant de valider.`,'warn'); return; }
  const ab = Object.values(A).filter(v=>v==='absent').length, x = tPlan().find(p=>p.id===S.ui.tsess); S.attOk[x.id] = true; audit('Appel validé', `${x.t} — ${x.g} — ${ab} absent(s)`); rerender(); toast(`Appel validé. ${ab} alerte${ab>1?'s':''} d'absence envoyée${ab>1?'s':''}.`); };
INP['u-cr'] = el => { S.cr[el.dataset.id] = el.value; };
ACT['u-cr-save'] = () => toast('Compte rendu publié dans la séance des étudiants.');

/* ----- Corrections : copies, aperçu, grille de notation ----- */
const uPoster = (t, f) => { const k = norm(t);
  if (k.includes('affiche')) return `<div class="mock poster"><b>FESTIVAL<br>URBAN ARTS</b><i class="m-circ"></i><i class="m-sq"></i><span>12 · 13 · 14 DÉC.<br>PALAIS DE LA CULTURE<br>ENTRÉE LIBRE</span></div>`;
  if (k.includes('logo')) return `<div class="mock logo-b"><div class="lb-g">${['#C2255C','#1E1B4B','#EE5A36','#127A51'].map((c,i)=>`<span style="background:${i%2?'#fff':c};color:${i%2?c:'#fff'}"><b>TR</b><small>Chez Tantie Rose</small></span>`).join('')}</div></div>`;
  if (k.includes('magazine')) return `<div class="mock mag"><div><b>CRÉER<br>LOCAL</b><p></p><p></p><p></p></div><div><i></i><p></p><p></p></div></div>`;
  if (k.includes('carte')) return `<div class="mock card-b"><div><b>EVC</b><small>Aya Kouassi · Infographiste</small></div><div class="cb-v"></div></div>`;
  return `<div class="mock doc"><b>${esc(t)}</b><p></p><p></p><p></p><p></p></div>`; };
const U_LV3 = [['a','Acquis','ok'],['e','En cours','warn'],['n','Non acquis','bad']];
const uLv3 = k => U_LV3.find(x=>x[0]===k);
const uLv3Val = L => { const v = L.filter(x=>x), n = v.length; return n ? Math.round(v.reduce((a,k)=>a+(k==='a'?2:k==='e'?1:0),0)/(2*n)*20) : null; };
view('t-corrections','form','Corrections','fa-file-lines', () => {
  const q = tQueue(), f = S.ui.tcf || 'todo', list = q.filter(x=> f==='todo' ? x.st!=='done' : x.st==='done'), x = q.find(z=>z.id===S.ui.tqs) || list[0] || q[0];
  if (!x) return uEmpty('Aucune copie','Les dépôts des étudiants apparaîtront ici.');
  S.ui.tqs = x.id; S.tg = S.tg || {}; const crit = U_CRIT.DG, G = S.tg[x.id] = S.tg[x.id] || crit.map(()=>null), nA = G.filter(k=>k==='a').length, done = x.st==='done';
  const stp = z => z.st==='done' ? pill(z.decision==='Validé'?'Validé':'Reprise demandée', z.decision==='Validé'?'ok':'bad') : z.d<=-4 ? pill('En retard','bad') : z.st==='correction' ? pill('En cours','info') : pill('À corriger','warn');
  return `<div class="u-grid tc-grid"><section class="panel tc-list"><div class="p-3 border-bottom-soft"><div class="small muted mb-2">${q.length} copies · ${q.filter(z=>z.st==='done').length} corrigées</div>${uSeg([['todo','À corriger'],['done','Corrigées']], f, 'u-tcf', {soft:true, cls:'block'})}</div>
    ${list.map(z=>`<button class="tc-item ${z.id===x.id?'on':''}" data-act="u-tqs" data-id="${z.id}">${uIni(z.student,'sm')}<span class="lr-m text-start"><b>${esc(z.student)}</b><small class="d-block muted">${esc(z.t.split(' — ')[0])} · déposé ${relDay(z.d).toLowerCase()}</small><span class="d-block mt-1">${stp(z)}</span></span></button>`).join('') || '<p class="small muted p-3 mb-0">Rien ici.</p>'}</section>
  <section class="panel tc-view"><div class="d-flex justify-content-between align-items-start gap-2 p-3 border-bottom-soft"><div><b style="font-size:16px">${esc(x.student)}</b><div class="small muted">${esc(x.file)} · déposé ${relDay(x.d).toLowerCase()} · ${x.d<=-4?'en retard':'à l\'heure'}</div></div><button class="link-u" data-act="fake-dl" data-n="${esc(x.file)}">Télécharger</button></div>
    <div class="tc-prev">${uPoster(x.t)}</div><div class="p-3 small muted border-top-soft">Message de l'étudiant : « J'ai repris la hiérarchie des titres et réduit la palette à trois couleurs. »</div></section>
  <section class="panel card-pad tc-grade"><h2 class="h3 mb-1" style="font-size:20px">Grille de compétences</h2><p class="small muted mb-3">Évaluation professionnelle, sans note chiffrée.</p>${done?`<div class="alert-strip as-ok slim mb-3"><i class="fa-solid fa-circle-check"></i><span>Copie corrigée : ${esc(x.decision)}.</span></div>`:''}
    ${crit.map((c,i)=>`<div class="mb-3"><div class="small fw-sb mb-1">${c[0]}</div><div class="lv3" role="radiogroup" aria-label="${esc(c[0])}">${U_LV3.map(([k,l,t])=>`<button type="button" class="lv3-b t-${t} ${G[i]===k?'on':''}" data-act="u-grc" data-i="${i}" data-k="${k}" ${done?'disabled':''} aria-pressed="${G[i]===k}">${l}</button>`).join('')}</div></div>`).join('')}
    <div class="note-final"><span>Bilan</span><b id="gr-tot" style="font-size:18px">${G.some(k=>!k)?`${G.filter(Boolean).length} / ${crit.length} critères évalués`:nA===crit.length?'Toutes les compétences acquises':`${nA} / ${crit.length} acquises`}</b></div>
    <label class="form-label mt-3" for="gr-com">Appréciation</label><textarea class="form-control" id="gr-com" rows="4" placeholder="Points forts, points à améliorer…" ${done?'disabled':''}>${esc(S.ui['grc'+x.id]||'')}</textarea>
    ${done?'':`<button class="btn btn-primary w-100 mt-3" data-act="u-gr-pub" data-dec="Validé">Valider le travail</button><button class="btn btn-ghost w-100 mt-2" data-act="u-gr-pub" data-dec="À reprendre">Demander une reprise</button>`}</section></div>`;
});
Object.assign(VIEWS['t-corrections'], {title:() => { const x = tQueue().find(z=>z.id===S.ui.tqs); return x ? esc(x.t.split(' — ')[0]) : 'Corrections'; }, crumb:'Espace formateur <span class="sep">›</span> Corrections <span class="sep">›</span> Design graphique'});
ACT['u-tqs'] = el => { S.ui.tqs = el.dataset.id; rerender(); };
INP['u-grcom'] = el => { S.ui['grc'+S.ui.tqs] = el.value; };
ACT['u-grc'] = el => { const c = $('#gr-com'); if (c) S.ui['grc'+S.ui.tqs] = c.value; S.tg[S.ui.tqs][+el.dataset.i] = el.dataset.k; rerender(); };
ACT['u-gr-pub'] = el => { const x = tQueue().find(z=>z.id===S.ui.tqs), G = S.tg[x.id], dec = el.dataset.dec, com = $('#gr-com').value.trim();
  if (dec==='Validé' && G.some(v=>v==null)) { toast('Évaluez chaque critère de la grille.','warn'); return; }
  if (dec==='Validé' && G.includes('n')) { toast('Un critère est « non acquis » : demandez plutôt une reprise.','warn'); return; }
  if (!com) { toast("Ajoutez une appréciation pour l'étudiant.",'warn'); $('#gr-com').focus(); return; }
  const val = uLv3Val(G);
  if (x.todo) { const t = S.st.todos.find(z=>z.id===x.todo); t.tGradedFiles = t.files.length; t.status = dec==='Validé' ? 'soumis' : 'reprendre'; if (dec==='Validé') t.grade = val; t.hist.push({w:'Marc Yao', d:0, c:com + (dec==='Validé' ? ' (avis favorable, validation définitive par l\'administration)' : '')}); S.st.notifs.unshift({id:'n'+Date.now(),cat:'Travaux',f:'DG',t:`Correction disponible : « ${t.t} » — ${dec==='Validé'?'avis favorable':'à reprendre'}.`,d:"À l'instant",read:false}); S.tq.unshift({id:'aya-done-'+t.id, student:x.student, mat:x.mat, t:x.t, d:0, file:x.file, st:'done', note:dec==='Validé'?val:null, decision:dec}); }
  else Object.assign(S.tq.find(z=>z.id===x.id), {st:'done', note:dec==='Validé'?val:null, decision:dec});
  audit('Travail corrigé', `${x.t} — ${x.student} — ${dec}`);
  const nx = tQueue().find(z=>z.st!=='done'); S.ui.tqs = nx ? nx.id : null; rerender(); toast(`${dec==='Validé'?'Travail validé':'Reprise demandée'}. ${esc(x.student)} est notifié(e).${nx?' Copie suivante ouverte.':''}`); };

/* ----- Suivi des compétences : grille du niveau, bilan en direct (aucune note chiffrée) ----- */
const U_TMOD = {m1:{l:'Séances 1 à 4 · Photoshop', c:[['Montage créatif','TP'],['Retouche photo','TP'],['QCM Photoshop','Quiz'],['Participation','']]}, m2:{l:'Séances 5 à 8 · Illustrator et logo', c:[['Logo — maquis','TP'],['Pictogrammes','TP'],['Examen Illustrator','Examen'],['Participation','']]}};
const uGrStudents = g => { const L = uTStudents(); return g==='Avancé' ? L.slice(Math.max(0,L.length-7)) : g==='Intermédiaire' ? L.slice(3, 11) : L.slice(0,9); };
function uGrData(g, m){ S.tgr = S.tgr || {}; const key = g+'|'+m; if (!S.tgr[key]) { const r = rng(hueOf(key)+3); S.tgr[key] = Object.fromEntries(uGrStudents(g).map(s=>[s.id, U_TMOD[m].c.map(()=>{ const x = r(); return x<0.1 ? null : x<0.68 ? 'a' : x<0.9 ? 'e' : 'n'; })])); } return S.tgr[key]; }
const uGrBilan = v => v.some(k=>!k) ? ['Incomplet','mute'] : v.includes('n') ? ['À accompagner','bad'] : v.every(k=>k==='a') ? ['Module validé','ok'] : ['En progression','warn'];
view('t-notes','form','Suivi des compétences','fa-chart-column', () => {
  const g = S.ui.tng || 'Débutant', m = S.ui.tnm || 'm2', M = U_TMOD[m], D = uGrData(g, m), L = uGrStudents(g);
  return `<div class="d-flex align-items-end gap-3 flex-wrap"><div><label class="form-label" for="gr-g">Niveau</label><select class="form-select" id="gr-g" data-chg="u-gr-sel" data-k="tng" style="min-width:220px">${U_LEVELS.map(l=>`<option ${g===l?'selected':''}>${l}</option>`).join('')}</select></div><div><label class="form-label" for="gr-m">Module</label><select class="form-select" id="gr-m" data-chg="u-gr-sel" data-k="tnm" style="min-width:300px">${Object.entries(U_TMOD).map(([k,v])=>`<option value="${k}" ${k===m?'selected':''}>${v.l}</option>`).join('')}</select></div>
    <div class="ms-auto d-flex gap-2 flex-wrap"><button class="btn btn-ghost" data-act="u-gr-save">Enregistrer</button><button class="btn btn-primary" data-act="u-gr-pubask">Publier aux étudiants</button></div></div>
  <div class="grid g-kpi mt-4" id="gr-stats"></div>
  <section class="panel mt-4"><div class="tbl"><table class="table gr-table"><thead><tr><th>Étudiant</th>${M.c.map(c=>`<th class="text-center"><span style="text-transform:none;letter-spacing:0;font-size:13px;color:var(--fg)">${c[0]}</span><div style="text-transform:none;letter-spacing:0;font-weight:500">${c[1]||'Observation'}</div></th>`).join('')}<th class="text-center">Bilan</th></tr></thead>
    <tbody>${L.map(s=>`<tr><td class="tdn">${esc(s.name)}</td>${M.c.map((c,i)=>`<td class="text-center"><select class="form-select form-select-sm gr-lv lv-${D[s.id][i]||'x'}" data-chg="u-gr" data-s="${s.id}" data-i="${i}" aria-label="${esc(s.name)} — ${c[0]}"><option value="" ${!D[s.id][i]?'selected':''}>—</option>${U_LV3.map(([k,l])=>`<option value="${k}" ${D[s.id][i]===k?'selected':''}>${l}</option>`).join('')}</select></td>`).join('')}<td class="text-center" id="ga-${s.id}"></td></tr>`).join('')}</tbody></table></div>
  <div class="table-foot"><span>Acquis : compétence maîtrisée en autonomie · En cours : avec accompagnement · Non acquis : à retravailler</span></div></section>`;
}, () => uGrPaint());
VIEWS['t-notes'].crumb = 'Espace formateur <span class="sep">›</span> Évaluations';
function uGrPaint(){ const g = S.ui.tng || 'Débutant', m = S.ui.tnm || 'm2', D = uGrData(g, m), L = uGrStudents(g);
  const B = L.map(s=>{ const b = uGrBilan(D[s.id]), el = $('#ga-'+s.id); if (el) el.innerHTML = pill(b[0], b[1]); return b[0]; });
  const all = L.flatMap(s=>D[s.id]), filled = all.filter(Boolean), acq = filled.filter(k=>k==='a').length, miss = all.length - filled.length;
  const st = $('#gr-stats'); if (st) st.innerHTML = uKpi('Compétences acquises', `${filled.length?Math.round(acq/filled.length*100):0} %`, `${acq} sur ${filled.length} évaluées`, '', '', '', 'compact') + uKpi('Modules validés', B.filter(b=>b==='Module validé').length, `sur ${L.length} étudiants`, '', '', '', 'compact') + uKpi('À accompagner', `<span class="tx-bad">${B.filter(b=>b==='À accompagner').length}</span>`, 'au moins un critère non acquis', '', '', '', 'compact') + uKpi('Évaluations manquantes', `<span class="tx-warn">${miss}</span>`, '', '', '', '', 'compact'); }
INP['u-gr'] = el => { const g = S.ui.tng || 'Débutant', m = S.ui.tnm || 'm2', D = uGrData(g, m), v = el.value || null; D[el.dataset.s][+el.dataset.i] = v; el.className = `form-select form-select-sm gr-lv lv-${v||'x'}`; uGrPaint(); };
INP['u-gr-sel'] = el => { S.ui[el.dataset.k] = el.value; rerender(); };
ACT['u-gr-save'] = () => toast('Brouillon enregistré. Les étudiants ne voient pas encore ce suivi.','info');
ACT['u-gr-pubask'] = () => { const g = S.ui.tng || 'Débutant', m = S.ui.tnm || 'm2', D = uGrData(g, m), miss = Object.values(D).flat().filter(v=>!v).length;
  openModal({title:'Publier aux étudiants ?', body:`<p class="mb-0">${miss?`<b>${miss} évaluation${miss>1?'s':''} manquante${miss>1?'s':''}</b> : ${miss>1?'elles seront affichées':'elle sera affichée'} « en attente ». `:''}Les étudiants du niveau ${g} recevront une notification avec leur bilan de compétences du module « ${U_TMOD[m].l} ».</p>`, foot:`<button class="btn btn-ghost" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary" data-act="u-gr-pub2">Publier</button>`}); };
ACT['u-gr-pub2'] = () => { const g = S.ui.tng || 'Débutant', m = S.ui.tnm || 'm2'; audit('Bilan de compétences publié', `${U_TMOD[m].l} — ${g}`); if (S.st && g==='Débutant') S.st.notifs.unshift({id:'n'+Date.now(),cat:'Évaluations',f:'DG',t:`Bilan de compétences publié : ${U_TMOD[m].l}.`,d:"À l'instant",read:false}); closeModal(); toast(`Bilan publié. ${uGrStudents(g).length} étudiants notifiés.`); };

/* File des copies : une copie notée par le formateur sort de la file tant que l'étudiant ne redépose pas */
function tQueue(){
  if (!S.st) loadStudent();
  if (!S.tq) { const titles = ['Logo — Maquis "Chez Tantie Rose"','Affiche — Festival Abidjan Urban Arts','Mise en page — magazine 8 pages','Carte de visite — charte EVC'];
    S.tq = tStudents().slice(1,10).map((s,i)=>({id:'q'+i, student:s.name, mat:s.id, t:titles[i%4], d:-(i%5), file:['pistes_logo.pdf','affiche_A2.pdf','magazine.pdf','carte.pdf'][i%4], st:i%3===2?'correction':'soumis', note:null})); }
  const aya = S.st.todos.filter(t=>t.f==='DG' && t.status==='soumis' && t.tGradedFiles!==t.files.length).map(t=>({id:'aya-'+t.id, todo:t.id, student:`${S.st.student.first} ${S.st.student.last}`, mat:S.st.student.matricule, t:t.t, d:0, file:t.files[t.files.length-1]||'—', st:'soumis', note:null}));
  return [...aya, ...S.tq];
}
