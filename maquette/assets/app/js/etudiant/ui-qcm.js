/* ============ MY EVC — v10 : QCM dans les projets ============
   Constructeur (choix unique, choix multiples, vrai / faux, réponse courte), réglages (durée, tentatives, seuil),
   passage côté étudiant avec correction automatique, résultats côté administration. Aucune note chiffrée :
   on affiche le nombre de bonnes réponses et une décision « validé / à reprendre ». */

const U_QT = {single:['Choix unique','fa-circle-dot'], multi:['Choix multiples','fa-square-check'], tf:['Vrai ou faux','fa-toggle-on'], short:['Réponse courte','fa-pen']};
const uQNew = type => ({id:'q'+Date.now()+Math.floor(Math.random()*99), type, q:'', o: type==='tf' ? ['Vrai','Faux'] : type==='short' ? [] : ['',''], a: type==='short' ? [''] : [0]});
const uQcmDefault = () => ({dur:15, tries:2, pass:70, shuffle:true, qs:[]});

/* ----- Données : un QCM de démonstration attaché à un projet DG ----- */
(() => { const base = uProjSeed;
  uProjSeed = function(){ const first = !S.projects, P = base();
    if (first) { P.splice(2, 0, {id:'P-104', f:'DG', t:'QCM — Typographie et mise en page', type:'QCM / Questionnaire', brief:"Questionnaire de fin de module sur la typographie, la grille et la hiérarchie de l'information. 8 questions, 15 minutes, 2 tentatives.", constraints:['15 minutes, en une seule fois','Validation à partir de 70 % de bonnes réponses'], deliverables:[], res:[{k:'pdf',n:'Support_typographie.pdf'},{k:'video',n:'Révision : la grille en 10 minutes',u:'youtube.com/watch?v=evc-grille'}], due:5, lateOk:false, mandatory:true, target:{mode:'formation', f:'DG'}, created:'20/10/2026', by:'Didier Koné', status:'Publié',
      qcm:{dur:15, tries:2, pass:70, shuffle:true, qs:[
        {id:'q1', type:'single', q:"Quelle famille de caractères possède des empattements ?", o:['Les linéales','Les sérifs','Les scriptes','Les monospaces'], a:[1]},
        {id:'q2', type:'multi', q:'Lesquels sont des formats vectoriels ? (plusieurs réponses)', o:['SVG','PNG','AI','JPG'], a:[0,2]},
        {id:'q3', type:'tf', q:"L'interlignage se mesure de ligne de base à ligne de base.", o:['Vrai','Faux'], a:[0]},
        {id:'q4', type:'single', q:'Pour une affiche imprimée, quel mode colorimétrique utiliser ?', o:['RVB','CMJN','Niveaux de gris','Couleurs indexées'], a:[1]},
        {id:'q5', type:'multi', q:"Qu'est-ce qui renforce la hiérarchie de l'information ?", o:['La taille du texte','Le contraste','Utiliser 6 polices','Les espaces blancs'], a:[0,1,3]},
        {id:'q6', type:'short', q:'Comment appelle-t-on la marge de sécurité ajoutée autour d\'un document à imprimer ?', o:[], a:['fonds perdus','fond perdu','bleed']},
        {id:'q7', type:'tf', q:'Une grille de 12 colonnes permet des découpages en 2, 3, 4 et 6.', o:['Vrai','Faux'], a:[0]},
        {id:'q8', type:'single', q:'Combien de polices de caractères maximum conseille-t-on sur un même support ?', o:['1','2 ou 3','5','Autant que nécessaire'], a:[1]}]}}); }
    return P; };
})();
const uQcmOf = p => p && p.qcm && p.qcm.qs && p.qcm.qs.length ? p.qcm : null;
function uQcmCheck(q, ans){ if (ans==null) return false;
  if (q.type==='short') return q.a.some(x=>norm(x).trim() && norm(String(ans)).trim()===norm(x).trim());
  const A = [...q.a].sort().join(','), R = [].concat(ans).map(Number).sort().join(','); return A===R; }
/* Résultat simulé pour les étudiants générés */
function uQcmRes(p, s){ const Q = uQcmOf(p); if (!Q) return null; S.qres = S.qres || {}; const k = p.id+'|'+s.id;
  if (s.id===U_DEMO && S.st) { const t = S.st.todos.find(x=>x.pid===p.id); return t && t.qres ? t.qres : null; }
  if (S.qres[k]===undefined) { const sub = uSub(p, s), h = uSeed(k); if (sub.status==='afaire') S.qres[k] = null;
    else { const good = Math.min(Q.qs.length, Math.round(Q.qs.length*(0.45 + (h%55)/100))); S.qres[k] = {good, total:Q.qs.length, ok: good/Q.qs.length*100 >= Q.pass, tries: 1 + h%Q.tries, d: -(h%5), dur: 6 + h%9}; } }
  return S.qres[k]; }

/* ===================== Éditeur : section « Questionnaire » ===================== */
function uQcmEditor(){ const Q = S.ui.pqset = S.ui.pqset || {...uQcmDefault()}, qs = S.ui.pqcm = S.ui.pqcm || [];
  return `<section class="panel card-pad" id="qcm-ed"><div class="d-flex align-items-center gap-2 mb-1"><span class="step-n">4</span><h2 class="h3 mb-0" style="font-size:19px">Questionnaire (QCM)</h2><span class="small muted ms-auto">${qs.length ? `${qs.length} question${qs.length>1?'s':''}` : 'facultatif'}</span></div>
    <p class="small muted mb-3">Ajoutez des questions corrigées automatiquement. L'étudiant voit le nombre de bonnes réponses, sans note chiffrée.</p>
    ${qs.map((q,i)=>`<div class="qb" data-q="${i}"><div class="qb-h"><span class="qb-n">${i+1}</span><select class="form-select form-select-sm" style="max-width:190px" data-chg="u-qb-type" data-q="${i}" aria-label="Type de question">${Object.entries(U_QT).map(([k,[l]])=>`<option value="${k}" ${q.type===k?'selected':''}>${l}</option>`).join('')}</select><div class="ms-auto d-flex gap-1"><button type="button" class="btn btn-quiet btn-sm btn-icon" data-act="u-qb-mv" data-q="${i}" data-d="-1" ${i===0?'disabled':''} aria-label="Monter"><i class="fa-solid fa-arrow-up"></i></button><button type="button" class="btn btn-quiet btn-sm btn-icon" data-act="u-qb-mv" data-q="${i}" data-d="1" ${i===qs.length-1?'disabled':''} aria-label="Descendre"><i class="fa-solid fa-arrow-down"></i></button><button type="button" class="btn btn-quiet btn-sm btn-icon" data-act="u-qb-dup" data-q="${i}" aria-label="Dupliquer"><i class="fa-regular fa-copy"></i></button><button type="button" class="btn btn-quiet btn-sm btn-icon tx-bad" data-act="u-qb-del" data-q="${i}" aria-label="Supprimer"><i class="fa-solid fa-trash-can"></i></button></div></div>
      <input class="form-control mt-2" value="${esc(q.q)}" placeholder="Intitulé de la question" data-inp="u-qb-q" data-q="${i}" aria-label="Question ${i+1}">
      ${q.type==='short' ? `<label class="form-label small mt-2 mb-1">Réponses acceptées (séparées par une virgule)</label><input class="form-control form-control-sm" value="${esc(q.a.join(', '))}" placeholder="Ex. fonds perdus, bleed" data-inp="u-qb-short" data-q="${i}">`
        : `<div class="qb-opts mt-2">${q.o.map((o,j)=>`<div class="qb-o"><label class="qb-ok" title="Bonne réponse"><input type="${q.type==='multi'?'checkbox':'radio'}" name="qb-${q.id}" ${q.a.includes(j)?'checked':''} data-chg="u-qb-a" data-q="${i}" data-o="${j}"><span>${q.a.includes(j)?'Bonne réponse':'Marquer juste'}</span></label><input class="form-control form-control-sm" value="${esc(o)}" placeholder="Réponse ${j+1}" data-inp="u-qb-o" data-q="${i}" data-o="${j}" ${q.type==='tf'?'readonly':''}>${q.type!=='tf' && q.o.length>2 ? `<button type="button" class="btn btn-quiet btn-sm btn-icon border-0 bg-transparent" data-act="u-qb-odel" data-q="${i}" data-o="${j}" aria-label="Retirer"><i class="fa-solid fa-xmark"></i></button>` : ''}</div>`).join('')}${q.type!=='tf' && q.o.length<6 ? `<button type="button" class="link-u small mt-1" data-act="u-qb-oadd" data-q="${i}">+ Ajouter une réponse</button>` : ''}</div>`}</div>`).join('')}
    <div class="qb-add">${Object.entries(U_QT).map(([k,[l,ic]])=>`<button type="button" class="btn btn-ghost btn-sm" data-act="u-qb-add" data-t="${k}"><i class="fa-solid ${ic} me-1"></i>${l}</button>`).join('')}</div>
    ${qs.length ? `<div class="row g-3 mt-1"><div class="col-6 col-md-3"><label class="form-label small" for="qs-dur">Durée (min)</label><input class="form-control" type="number" min="1" id="qs-dur" value="${Q.dur}" data-inp="u-qs" data-k="dur"></div><div class="col-6 col-md-3"><label class="form-label small" for="qs-tr">Tentatives</label><select class="form-select" id="qs-tr" data-chg="u-qs" data-k="tries">${[1,2,3].map(n=>`<option ${Q.tries==n?'selected':''}>${n}</option>`).join('')}</select></div><div class="col-6 col-md-3"><label class="form-label small" for="qs-pass">Seuil de validation</label><select class="form-select" id="qs-pass" data-chg="u-qs" data-k="pass">${[50,60,70,80,100].map(n=>`<option value="${n}" ${Q.pass==n?'selected':''}>${n} % de bonnes réponses</option>`).join('')}</select></div><div class="col-6 col-md-3 d-flex align-items-end"><label class="d-flex gap-2 align-items-center small mb-2"><input class="form-check-input mt-0" type="checkbox" ${Q.shuffle?'checked':''} data-chg="u-qs" data-k="shuffle">Ordre aléatoire</label></div></div>
      <button type="button" class="btn btn-quiet btn-sm mt-3" data-act="u-qb-preview"><i class="fa-regular fa-eye me-1"></i>Aperçu côté étudiant</button>` : ''}</section>`; }
function uQcmCollect(){ const qs = (S.ui.pqcm||[]).filter(q=>q.q.trim()); return qs.length ? {...(S.ui.pqset||uQcmDefault()), qs: clone(qs)} : null; }
const uQbRe = () => { uPeKeep(); rerenderKeep(); setTimeout(()=>{ const el = $('#qcm-ed'); if (el && S.ui.qbScroll) { el.scrollIntoView({block:'nearest'}); S.ui.qbScroll = false; } }, 30); };
ACT['u-qb-add'] = el => { (S.ui.pqcm = S.ui.pqcm || []).push(uQNew(el.dataset.t)); S.ui.qbScroll = true; uQbRe(); };
ACT['u-qb-del'] = el => { S.ui.pqcm.splice(+el.dataset.q,1); uQbRe(); };
ACT['u-qb-dup'] = el => { const q = clone(S.ui.pqcm[+el.dataset.q]); q.id = 'q'+Date.now(); S.ui.pqcm.splice(+el.dataset.q+1, 0, q); uQbRe(); };
ACT['u-qb-mv'] = el => { const i = +el.dataset.q, j = i + +el.dataset.d, a = S.ui.pqcm; [a[i], a[j]] = [a[j], a[i]]; uQbRe(); };
ACT['u-qb-oadd'] = el => { S.ui.pqcm[+el.dataset.q].o.push(''); uQbRe(); };
ACT['u-qb-odel'] = el => { const q = S.ui.pqcm[+el.dataset.q], j = +el.dataset.o; q.o.splice(j,1); q.a = q.a.filter(x=>x!==j).map(x=>x>j?x-1:x); if (!q.a.length && q.type!=='multi') q.a = [0]; uQbRe(); };
INP['u-qb-type'] = el => { const i = +el.dataset.q, old = S.ui.pqcm[i], n = uQNew(el.value); n.q = old.q; if (el.value!=='tf' && el.value!=='short' && old.o.length && old.type!=='tf' && old.type!=='short') { n.o = old.o; n.a = el.value==='single' ? [old.a[0]??0] : old.a; } S.ui.pqcm[i] = n; uQbRe(); };
INP['u-qb-q'] = el => { S.ui.pqcm[+el.dataset.q].q = el.value; uPeSum(); };
INP['u-qb-o'] = el => { S.ui.pqcm[+el.dataset.q].o[+el.dataset.o] = el.value; };
INP['u-qb-short'] = el => { S.ui.pqcm[+el.dataset.q].a = el.value.split(',').map(x=>x.trim()).filter(Boolean); };
INP['u-qb-a'] = el => { const q = S.ui.pqcm[+el.dataset.q], j = +el.dataset.o; if (q.type==='multi') { q.a = el.checked ? [...new Set([...q.a, j])] : q.a.filter(x=>x!==j); } else q.a = [j]; $$(`[data-q="${el.dataset.q}"] .qb-ok span`).forEach((s,k)=>s.textContent = q.a.includes(k)?'Bonne réponse':'Marquer juste'); };
INP['u-qs'] = el => { const Q = S.ui.pqset; Q[el.dataset.k] = el.type==='checkbox' ? el.checked : +el.value; };
ACT['u-qb-preview'] = () => { const qs = (S.ui.pqcm||[]).filter(q=>q.q.trim()); if (!qs.length) { toast('Rédigez au moins une question.','warn'); return; }
  const bad = qs.find(q=>q.type!=='short' && (!q.a.length || q.o.some(o=>!o.trim()))); if (bad) toast(`Question « ${bad.q.slice(0,40)} » : complétez les réponses et la bonne réponse.`,'warn');
  openModal({title:'Aperçu du questionnaire', size:'lg', body:`<div class="small muted mb-3">${qs.length} questions · ${S.ui.pqset.dur} min · seuil ${S.ui.pqset.pass} %</div>${qs.map((q,i)=>uQItem(q, i, null, true)).join('')}`}); };

/* Branchement dans l'éditeur de projet */
(() => { const base = VIEWS['a-projet-edit'].render;
  VIEWS['a-projet-edit'].render = () => { const ed = uProjSeed().find(p=>p.id===S.ui.pedit);
    if (!S.ui.pqcm) { S.ui.pqcm = ed && ed.qcm ? clone(ed.qcm.qs) : []; S.ui.pqset = ed && ed.qcm ? {dur:ed.qcm.dur, tries:ed.qcm.tries, pass:ed.qcm.pass, shuffle:ed.qcm.shuffle} : uQcmDefault(); }
    const html = base(), k = html.indexOf('</div>\n  <div class="stack pe-side">');
    return k>0 ? html.slice(0,k) + uQcmEditor() + html.slice(k) : html; };
})();

/* ===================== Côté étudiant : passer le QCM ===================== */
function uQItem(q, i, ans, preview, name){ const nm = name || 'pq'+i, type = q.type==='multi' ? 'checkbox' : 'radio';
  return `<fieldset class="panel card-pad mb-3 pq-item"><legend class="d-flex gap-2 align-items-start mb-3" style="font-size:16px;font-weight:600"><span class="qb-n">${i+1}</span><span class="lr-m">${esc(q.q)}<span class="d-block small muted fw-normal mt-1">${U_QT[q.type][0]}${q.type==='multi'?' · plusieurs réponses possibles':''}</span></span></legend>
    ${q.type==='short' ? `<input class="form-control" name="${nm}" placeholder="Votre réponse" ${preview?'disabled':''} data-inp="u-pq-a" data-i="${i}" value="${esc(ans??'')}">`
      : q.o.map((o,j)=>`<label class="opt ${[].concat(ans??[]).includes(j)?'on':''}"><input class="form-check-input" type="${type}" name="${nm}" value="${j}" ${[].concat(ans??[]).includes(j)?'checked':''} ${preview?'disabled':''} data-chg="u-pq-a" data-i="${i}"><span class="opt-l">${'ABCDEF'[j]}</span><span>${esc(o)}</span></label>`).join('')}</fieldset>`; }
view('e-pqcm','etu','Questionnaire','fa-list-check', () => {
  const t = S.st.todos.find(x=>x.id===S.ui.pqt), Q = t && t.qcm; if (!Q) return uEmpty('Questionnaire introuvable','Revenez à vos travaux pratiques.', `<a class="btn btn-primary" href="#e-todo">Mes travaux</a>`);
  const z = S.pq = S.pq && S.pq.t===t.id ? S.pq : {t:t.id, ans:{}, end:Date.now()+Q.dur*60000, order: Q.qs.map((_,i)=>i).sort((a,b)=>Q.shuffle ? (uSeed(t.id+a)%7)-(uSeed(t.id+b)%7) : a-b)};
  const n = Object.keys(z.ans).filter(k=>[].concat(z.ans[k]).length && z.ans[k]!=='').length;
  return `<div class="u-grid u-2-1"><div>${z.order.map((qi,i)=>uQItem(Q.qs[qi], i, z.ans[qi], false, 'pq'+qi).replace(/data-i="\d+"/g, `data-i="${qi}"`)).join('')}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2"><span class="small muted"><span id="pq-n">${n}</span> / ${Q.qs.length} réponses</span><button class="btn btn-primary btn-lg" data-act="u-pq-end">Terminer le questionnaire</button></div></div>
  <div class="stack"><div class="dark-card d-flex align-items-center gap-3"><i class="fa-solid fa-stopwatch" style="font-size:28px;color:var(--c300)"></i><div><div class="small" style="color:#C9C6E8">Temps restant</div><div class="num-key" id="pq-time" style="color:#fff">${String(Q.dur).padStart(2,'0')}:00</div></div></div>
    ${uCard('Règles', `<p class="small mb-2">${esc(t.t)}</p><p class="small muted mb-0">${Q.qs.length} questions · ${Q.dur} minutes · ${Q.tries} tentative${Q.tries>1?'s':''}. Validation à partir de ${Q.pass} % de bonnes réponses. Tentative ${((t.qres&&t.qres.tries)||0)+1} sur ${Q.tries}.</p>`)}</div></div>`;
}, () => { clearInterval(S.pqT); const paint = () => { const el = $('#pq-time'); if (!el || S.view!=='e-pqcm' || !S.pq) { clearInterval(S.pqT); return; } const s = Math.max(0, Math.round((S.pq.end-Date.now())/1000)); el.textContent = `${String(Math.floor(s/60)).padStart(2,'0')}:${String(s%60).padStart(2,'0')}`; if (!s) { clearInterval(S.pqT); ACT['u-pq-finish'](); } }; paint(); S.pqT = setInterval(paint, 1000); });
Object.assign(VIEWS['e-pqcm'], {parent:'e-todo', title:() => { const t = S.st && S.st.todos.find(x=>x.id===S.ui.pqt); return t ? esc(t.t.replace(/^QCM — /,'QCM · ')) : 'Questionnaire'; }, crumb:'Espace étudiant <span class="sep">›</span> Travaux <span class="sep">›</span> Questionnaire'});
INP['u-pq-a'] = el => { const i = +el.dataset.i, t = S.st.todos.find(x=>x.id===S.ui.pqt), q = t.qcm.qs[i];
  S.pq.ans[i] = q.type==='short' ? el.value : q.type==='multi' ? $$(`input[name=pq${i}]:checked`).map(x=>+x.value) : +el.value;
  if (q.type!=='short') $$(`input[name=pq${i}]`).forEach(x=>x.closest('.opt').classList.toggle('on', x.checked));
  const n = Object.values(S.pq.ans).filter(v=>[].concat(v).length && v!=='').length; const c = $('#pq-n'); if (c) c.textContent = n; };
ACT['u-pq'] = el => { const t = S.st.todos.find(x=>x.id===el.dataset.id); if (t.qres && t.qres.tries >= t.qcm.tries && !t.qres.ok) { toast('Toutes les tentatives ont été utilisées. Votre formateur vous recontactera.','warn'); return; } S.pq = null; S.ui.pqt = t.id; go('e-pqcm'); };
ACT['u-pq-end'] = () => { const t = S.st.todos.find(x=>x.id===S.ui.pqt), miss = t.qcm.qs.length - Object.values(S.pq.ans).filter(v=>[].concat(v).length && v!=='').length;
  openModal({title:'Terminer le questionnaire ?', body:`<p class="mb-0">${miss?`<b>${miss} question${miss>1?'s':''} sans réponse.</b> `:''}Vos réponses seront corrigées immédiatement.</p>`, foot:`<button class="btn btn-ghost" data-bs-dismiss="modal">Continuer</button><button class="btn btn-primary" data-act="u-pq-finish">Terminer</button>`}); };
ACT['u-pq-finish'] = () => { clearInterval(S.pqT); const t = S.st.todos.find(x=>x.id===S.ui.pqt), Q = t.qcm, good = Q.qs.filter((q,i)=>uQcmCheck(q, S.pq.ans[i])).length, ok = good/Q.qs.length*100 >= Q.pass;
  t.qres = {good, total:Q.qs.length, ok, tries:((t.qres&&t.qres.tries)||0)+1, d:0, dur: Math.max(1, Q.dur - Math.round((S.pq.end-Date.now())/60000)), ans: clone(S.pq.ans)};
  t.hist.push({w:'Vous', d:0, c:`Questionnaire terminé : ${good} bonne${good>1?'s':''} réponse${good>1?'s':''} sur ${Q.qs.length}.`});
  if (!t.deliverables || !t.deliverables.length) { t.status = ok ? 'soumis' : (t.qres.tries < Q.tries ? 'encours' : 'reprendre'); }
  adminNotify && adminNotify('done', t.t, `${S.st.student.first} ${S.st.student.last} a terminé le questionnaire « ${t.t} » (${good}/${Q.qs.length})`);
  audit('Questionnaire terminé', `${t.t} — ${good}/${Q.qs.length} bonnes réponses`); closeModal(); S.ui.pqres = t.id; go('e-pqres'); };
view('e-pqres','etu','Résultat du questionnaire','fa-list-check', () => { const t = S.st.todos.find(x=>x.id===S.ui.pqres), r = t && t.qres; if (!r) return uEmpty('Aucun résultat');
  return `<div class="u-grid u-2-1"><div class="stack"><section class="panel card-pad text-center"><div class="ok-circle mx-auto mb-3" style="${r.ok?'':'background:var(--warn-soft);color:var(--warn)'}"><i class="fa-solid ${r.ok?'fa-check':'fa-rotate-left'}"></i></div><h2 class="h2">${r.ok?'Questionnaire réussi':'Questionnaire à reprendre'}</h2><div class="my-3">${r.ok?pill('Compétence validée','ok'):pill(r.tries<t.qcm.tries?'Nouvelle tentative possible':'À revoir avec le formateur','warn')}</div><p class="muted mb-4">${r.good} bonne${r.good>1?'s':''} réponse${r.good>1?'s':''} sur ${r.total} · seuil ${t.qcm.pass} % · tentative ${r.tries} sur ${t.qcm.tries}</p><div class="d-flex gap-2 justify-content-center flex-wrap">${!r.ok && r.tries<t.qcm.tries?`<button class="btn btn-primary" data-act="u-pq" data-id="${t.id}">Nouvelle tentative</button>`:''}<a class="btn ${r.ok||r.tries>=t.qcm.tries?'btn-primary':'btn-ghost'}" href="#e-todo">Mes travaux</a></div></section>
    ${uCard('Correction', t.qcm.qs.map((q,i)=>{ const ok = uQcmCheck(q, r.ans?.[i]), a = r.ans?.[i]; const fmt = v => q.type==='short' ? esc(v||'Sans réponse') : [].concat(v??[]).map(j=>esc(q.o[j])).join(', ') || 'Sans réponse'; return `<div class="box-row d-block"><div class="fw-sb small mb-1">${i+1}. ${esc(q.q)}</div><div class="small ${ok?'tx-ok':'tx-bad'}"><i class="fa-solid ${ok?'fa-circle-check':'fa-circle-xmark'} me-1"></i>${fmt(a)}${ok?'':` · bonne réponse : <b>${q.type==='short'?esc(q.a[0]):q.a.map(j=>esc(q.o[j])).join(', ')}</b>`}</div></div>`; }).join(''))}</div><div></div></div>`; });
Object.assign(VIEWS['e-pqres'], {parent:'e-todo', crumb:'Espace étudiant <span class="sep">›</span> Travaux <span class="sep">›</span> Résultat'});

/* ===================== Côté étudiant : la fiche projet affiche tout ===================== */
(() => { const base = VIEWS['e-todo'].render;
  VIEWS['e-todo'].render = () => { uProjSeed().filter(p=>p.status==='Publié').forEach(p=>uPjSync(p, false, true));
    let html = base(); const fs = vf(), all = S.st.todos.filter(t=>fs.includes(t.f)), g = S.ui.tgrp || 'open', list = all.filter(U_TGRP[g]), t = list.find(x=>x.id===S.ui.tsel) || list[0]; if (!t) return html;
    html = html.replace(/<b class="small">(\d+) pts<\/b>/g, '').replace(/pill\(`Corrigé · \d+\/20`/g,'');
    html = html.replace(/Corrigé · \d+(,\d)?\/20/g, 'Validé');
    const extra = `${t.updated?`<div class="alert-strip as-info slim mt-3"><i class="fa-solid fa-pen-to-square"></i><span>Ce projet a été mis à jour par l'administration. Relisez les consignes.</span></div>`:''}
      ${t.constraints&&t.constraints.length?`<h3 class="h3 mt-4 mb-2">Contraintes</h3><ul class="cons-ul">${t.constraints.map(c=>`<li><i class="fa-solid fa-check"></i>${esc(c)}</li>`).join('')}</ul>`:''}
      ${t.deliverables&&t.deliverables.length?`<h3 class="h3 mt-4 mb-2">Livrables attendus</h3><div class="d-flex flex-wrap gap-2">${t.deliverables.map(d=>pill(d,'grey')).join('')}</div>`:''}
      ${t.res&&t.res.length?`<h3 class="h3 mt-4 mb-2">Ressources</h3>${t.res.map((r,i)=>uResRow(r,i,false)).join('')}`:''}
      ${t.qcm?`<div class="qcm-card mt-4"><span class="res-ic" style="background:var(--c100);color:var(--c500)"><i class="fa-solid fa-list-check"></i></span><div class="lr-m"><b>Questionnaire · ${t.qcm.qs.length} questions</b><div class="small muted">${t.qcm.dur} min · seuil ${t.qcm.pass} % · ${t.qcm.tries} tentative${t.qcm.tries>1?'s':''}${t.qres?` · dernier résultat : ${t.qres.good}/${t.qres.total} bonnes réponses`:''}</div></div>${t.qres&&t.qres.ok?`<button class="btn btn-ghost btn-sm" data-act="u-pqres" data-id="${t.id}">Voir le résultat</button>`:`<button class="btn btn-primary btn-sm" data-act="u-pq" data-id="${t.id}">${t.qres?'Nouvelle tentative':'Commencer'}</button>`}</div>`:''}`;
    html = html.replace(/(<h3 class="h3 mb-2">Consigne<\/h3><p class="mb-0" style="white-space:pre-line">[\s\S]*?<\/p>)/, `$1${extra}`);
    html = html.replace('<input class="form-control" id="td-link" type="url" placeholder="Ou un lien (Drive, Behance, Canva…)" aria-label="Lien vers votre travail">', `<div class="row g-2"><div class="col-md-6"><label class="form-label small" for="td-link">Lien vers le travail (facultatif)</label><input class="form-control" id="td-link" placeholder="Drive, Behance, Canva, page Facebook…"></div><div class="col-md-6"><label class="form-label small" for="td-video">Vidéo de présentation (facultatif)</label><input class="form-control" id="td-video" placeholder="YouTube, Drive ou Vimeo"></div></div>`);
    if (t.qcm && (!t.deliverables || !t.deliverables.length)) html = html.replace(/<h3 class="h3 mt-4 mb-2">Mon dépôt<\/h3>\s*<form id="td-form"[\s\S]*?<\/form>/, '');
    return html; };
  ACT['u-pqres'] = el => { S.ui.pqres = el.dataset.id; go('e-pqres'); };
  const sub = ACT['todo-submit']; ACT['todo-submit'] = f => { const t = S.st.todos.find(x=>x.id===f.dataset.id), v = $('#td-video')?.value.trim(), m = $('#td-msg')?.value.trim(); sub(f); if (t && t.status==='soumis') { if (v) t.video = v; if (m) t.msg = m; } };
})();

/* ===================== Côté administration : résultats ===================== */
(() => { const base = VIEWS['a-projet'].render;
  VIEWS['a-projet'].render = () => { const p = uProjSeed().find(x=>x.id===S.ui.proj) || uProjSeed()[0], Q = uQcmOf(p); let html = base(); if (!Q) return html;
    const X = uSubsOf(p).map(x=>({...x, r:uQcmRes(p, x.s)})), done = X.filter(x=>x.r), ok = done.filter(x=>x.r.ok).length;
    const card = uCard('Questionnaire', `<div class="d-flex gap-4 mb-3"><div><div class="num-key" style="font-size:26px">${Q.qs.length}</div><div class="small muted">questions</div></div><div><div class="num-key" style="font-size:26px">${done.length}</div><div class="small muted">passés</div></div><div><div class="num-key" style="font-size:26px">${ok}</div><div class="small muted">réussis</div></div></div><div class="small muted mb-3">${Q.dur} min · seuil ${Q.pass} % · ${Q.tries} tentative${Q.tries>1?'s':''}${Q.shuffle?' · ordre aléatoire':''}</div><div class="d-grid gap-2"><button class="btn btn-ghost btn-sm" data-act="u-qv" data-id="${p.id}">Voir les questions</button><button class="btn btn-ghost btn-sm" data-act="u-qr" data-id="${p.id}">Résultats détaillés</button></div>`);
    html = html.replace('<div class="stack"><section class="panel card-pad"><h3 class="h3 mb-3" style="font-size:17px">Avancement</h3>', `<div class="stack">${card}<section class="panel card-pad"><h3 class="h3 mb-3" style="font-size:17px">Avancement</h3>`);
    html = html.replace('<th>Dépôt</th>', '<th>Questionnaire</th>');
    X.forEach(({s, sub, r})=>{ const cell = r ? `${r.good}/${r.total} ${pill(r.ok?'Réussi':'À reprendre', r.ok?'ok':'bad')}` : '<span class="muted">Non passé</span>'; html = html.replace(new RegExp(`(<div class="tds mono">${s.id}</div></div></div></td><td class="small">[^<]*</td><td class="small">)[\\s\\S]*?(</td>)`), `$1${cell}$2`); });
    return html; };
})();
ACT['u-qv'] = el => { const p = uProjSeed().find(x=>x.id===el.dataset.id), Q = p.qcm;
  openModal({title:`Questions — ${esc(p.t)}`, size:'lg', body:Q.qs.map((q,i)=>`<div class="box-row d-block"><div class="d-flex gap-2 mb-1"><span class="qb-n">${i+1}</span><b class="lr-m small">${esc(q.q)}</b><span class="small muted nowrap">${U_QT[q.type][0]}</span></div><div class="small ps-4">${q.type==='short' ? `Réponses acceptées : <b class="tx-ok">${q.a.map(esc).join(', ')}</b>` : q.o.map((o,j)=>`<div class="${q.a.includes(j)?'tx-ok fw-sb':'muted'}"><i class="fa-solid ${q.a.includes(j)?'fa-circle-check':'fa-circle'} me-1" style="${q.a.includes(j)?'':'opacity:.25'}"></i>${esc(o)}</div>`).join('')}</div></div>`).join(''), foot:`<button class="btn btn-ghost" data-act="u-pj-edit" data-id="${p.id}">Modifier le questionnaire</button><button class="btn btn-primary" data-act="close-modal">Fermer</button>`}); };
ACT['u-qr'] = el => { const p = uProjSeed().find(x=>x.id===el.dataset.id), Q = p.qcm, X = uSubsOf(p).map(x=>({...x, r:uQcmRes(p, x.s)}));
  openModal({title:`Résultats — ${esc(p.t)}`, size:'lg', body:`<div class="tbl"><table class="table"><thead><tr><th>Étudiant</th><th>Bonnes réponses</th><th>Tentatives</th><th>Durée</th><th>Décision</th></tr></thead><tbody>${X.map(({s,r})=>`<tr><td class="tdn">${esc(s.name)}</td><td>${r?`<div class="d-flex align-items-center gap-2"><div style="width:90px">${uBar(r.good/r.total*100, r.ok?'ok':'cor')}</div><b class="small">${r.good}/${r.total}</b></div>`:'<span class="muted small">Non passé</span>'}</td><td class="small">${r?`${r.tries} / ${Q.tries}`:'—'}</td><td class="small">${r?`${r.dur} min`:'—'}</td><td>${r?pill(r.ok?'Réussi':'À reprendre', r.ok?'ok':'bad'):pill('En attente','mute')}</td></tr>`).join('')}</tbody></table></div><p class="small muted mt-2 mb-0">Seuil de validation : ${Q.pass} % de bonnes réponses. Aucune note chiffrée n'est communiquée aux étudiants.</p>`}); };
