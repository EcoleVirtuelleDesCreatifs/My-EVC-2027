/* ============ MY EVC — v10 : dossier de préinscription complet (admin) + simulation du test d'éligibilité ============
   - Toutes les informations saisies par le candidat, son avancement dans l'admission, ses critères d'éligibilité.
   - Fiche du test envoyée (message e-mail / WhatsApp + sujet), résultats question par question, tentatives.
   - Simulation : l'admin peut ouvrir le test « vue candidat », ou générer un passage automatique. */

const U_ADM_STEPS = ['Préinscription','Étude du profil',"Test d'éligibilité",'Premier versement','Affectation à une promotion','Activation du compte'];
const U_TEST_CAT = ['Logique','Calcul','Informatique','Informatique','Informatique','Métier','Métier','Métier','Bureautique','Savoir-être'];
const uSeed = str => { let h = 0; for (const ch of String(str)) h = (h*31 + ch.charCodeAt(0)) >>> 0; return h; };

/* Compléments du dossier : valeurs saisies non présentes dans les données de départ */
function uCandX(c){
  if (!c.x) { const h = uSeed(c.id), [first, ...rest] = c.name.split(' ');
    c.x = { first, last: rest.join(' '), payPref: ['En deux versements (inscription, puis solde)','En une seule fois','Prise en charge par un employeur ou un organisme'][h%3],
      sessionWish: c.session.replace('Session ','Rentrée de ').toLowerCase().replace(/^r/,'R'), cgu: `${c.d} à ${8+h%9} h ${String(h%60).padStart(2,'0')}`,
      device: ['Android · Chrome','iPhone · Safari','Windows · Chrome','Android · Opera Mini'][h%4], docName: c.docs ? `cni_${c.name.split(' ').pop().toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g,'')}.pdf` : '', timeSpent: `${6+h%9} min` }; }
  return c.x;
}
/* Tentatives du test : reconstituées pour les données de départ, réelles après un passage */
function uTests(c){
  if (!c.tests) { c.tests = [];
    if (c.test) { const tries = c.tries || 1;
      for (let t=0; t<tries; t++) { const last = t===tries-1, score = last ? c.test.score : Math.max(4, c.test.score - 4), good = score/2, h = uSeed(c.id+t);
        const order = TEST_Q.map((_,i)=>i).sort((a,b)=>((h>>a)&7)-((h>>b)&7)), okSet = new Set(order.slice(0, good));
        c.tests.push({n:t+1, d: last ? c.test.d : c.testSent || c.d, start:`${9+h%8} h ${String(h%50+5).padStart(2,'0')}`, dur: 11 + h%8, score, ans: TEST_Q.map((q,i)=>okSet.has(i) ? q.a : (q.a+1+(h+i)%3)%4), sim:false}); } } }
  return c.tests;
}
const uTestSent = c => c.testSent || (c.test ? c.d : null);
const uAdmIdx = c => c.stage==='rejete' ? 1 : c.stage==='paye' ? (c.promo ? 5 : 4) : ({recue:1, profil_ok:2, test_ko:2, test_ok:3, actif:6})[c.stage] ?? 1;

/* ----- Vue : dossier de préinscription ----- */
view('a-preinscription','admin','Dossier de préinscription','fa-tray', () => {
  const c = S.preins.find(x=>x.id===S.ui.pre) || S.preins[0]; S.ui.pre = c.id;
  const X = uCandX(c), T = uTests(c), E = eligibility(c), tab = S.ui.pdt || 'info', key = candKey(c), idx = uAdmIdx(c), last = T[T.length-1];
  const okN = E.filter(e=>e.ok).length, pct = Math.round(Math.min(idx, 6)/6*100);
  const dates = [c.d, c.stage==='recue'||c.stage==='rejete' ? null : uTestSent(c), last ? last.d : null, c.pay ? c.pay.d : null, c.promo ? c.promo.d : null, c.stage==='actif' ? (c.pay?.d||'—') : null];
  const acts = {recue:`<button class="btn btn-primary" data-act="cand-review" data-id="${c.id}"><i class="fa-solid fa-user-check me-1"></i>Étudier et valider le profil</button>`,
    profil_ok:`<button class="btn btn-ghost" data-act="u-tfiche" data-id="${c.id}">Voir la fiche envoyée</button><button class="btn btn-primary" data-act="u-tsim" data-id="${c.id}"><i class="fa-solid fa-play me-1"></i>Simuler le test</button>`,
    test_ko:(c.tries||1)<2?`<button class="btn btn-primary" data-act="u-tsim" data-id="${c.id}"><i class="fa-solid fa-play me-1"></i>Simuler la 2ᵉ tentative</button>`:`<button class="btn btn-ghost" data-act="cand-retry" data-id="${c.id}">Accorder une tentative</button>`,
    test_ok:`<button class="btn btn-primary" data-act="cand-cash" data-id="${c.id}"><i class="fa-solid fa-wallet me-1"></i>Encaisser le 1er versement</button>`,
    paye: c.promo ? `<button class="btn btn-ghost" data-act="cand-link" data-id="${c.id}">Renvoyer le lien d'activation</button>` : `<button class="btn btn-primary" data-act="u-promo" data-id="${c.id}"><i class="fa-solid fa-users-rectangle me-1"></i>Affecter à une promotion</button>`, actif:`<button class="btn btn-primary" data-act="u-fiche" data-id="${c.mat||''}">Ouvrir la fiche étudiant</button>`, rejete:''}[c.stage];

  let body = '';
  if (tab==='info') { const sec = (t, rows) => `<section class="panel card-pad"><h2 class="h3 mb-3" style="font-size:18px">${t}</h2><div class="pd-grid">${rows.map(([l,v,w])=>`<div class="pd-f ${w?'pd-w':''}"><span>${l}</span><b>${v}</b></div>`).join('')}</div></section>`;
    body = `<div class="u-grid u-2-1"><div class="stack">
      ${sec('Identité', [['Prénom', esc(X.first)],['Nom', esc(X.last||'—')],['Adresse e-mail', esc(c.email)],['Téléphone (WhatsApp)', esc(c.phone)],['Date de naissance', `${esc(c.birth)} <span class="muted fw-normal">· ${ageOf(c.birth)} ans</span>`],['Pays', esc(c.country)],['Ville / commune', esc(c.city||'—')]])}
      ${sec('Formation demandée', [['Formation(s)', c.f.map(f=>`${fchip(f)} <span class="ms-1">${esc(F[f].name)}</span>`).join('<br>'), true],['Session', esc(X.sessionWish)],['Modalité', esc(c.mode)],['Mode de paiement souhaité', esc(X.payPref), true],['Tarif', `${fcfa(PROFILES[key].price)} <span class="muted fw-normal">· acompte ${fcfa(firstDue(c))}</span>`],['Source', esc(c.src)]])}
      ${sec('Profil', [['Dernier diplôme', esc(c.level)],['Situation actuelle', esc(c.situation)],['Équipement', esc(c.equip)],['Expérience dans le domaine', esc(c.exp||'—')],['Lien vers les réalisations', c.link?`<a class="link-u" href="#a-preinscription" data-act="fake-dl" data-n="${esc(c.link)}">${esc(c.link)}</a>`:'<span class="muted fw-normal">Non renseigné</span>', true]])}
      <section class="panel card-pad"><h2 class="h3 mb-3" style="font-size:18px">Motivation <span class="small muted fw-normal ms-1">${(c.motivation||'').length} caractères</span></h2><blockquote class="pd-quote">${esc(c.motivation)}</blockquote></section></div>
      <div class="stack">${uCard('Documents', `${[["Pièce d'identité", c.docs, X.docName],["Photo d'identité", idx>=4, 'photo.jpg'],['Dernier diplôme', idx>=4, 'diplome.pdf'],['Justificatif de domicile', idx>=4, 'domicile.pdf']].map(([t,ok,n],i)=>`<div class="file-row"><span class="ftype">${ok?(i===1?'JPG':'PDF'):'—'}</span><div class="lr-m"><div class="fw-sb small">${t}</div><div class="small muted">${ok?esc(n):i===0?'Non fournie':'Demandée après le test'}</div></div>${ok?`<button class="link-u small" data-act="fake-dl" data-n="${esc(n)}">Voir</button>`:i===0?`<button class="link-u small" data-act="cand-askdoc" data-id="${c.id}">Relancer</button>`:''}</div>`).join('')}`)}
        ${uCard('Envoi du formulaire', `<div class="kv"><span>Référence</span><b class="mono">${c.id}</b></div><div class="kv"><span>Reçu le</span><b>${esc(c.d)}</b></div><div class="kv"><span>Règlement accepté</span><b>${esc(X.cgu)}</b></div><div class="kv"><span>Appareil</span><b>${esc(X.device)}</b></div><div class="kv"><span>Temps de saisie</span><b>${esc(X.timeSpent)}</b></div><div class="kv"><span>Confirmation</span><b>E-mail + WhatsApp</b></div>`)}</div></div>`; }

  if (tab==='elig') { const sent = uTestSent(c);
    body = `<div class="u-grid u-2-1"><div class="stack">
      ${uCard(`Critères d'éligibilité <span class="cnt">${okN} / ${E.length}</span>`, E.map(e=>`<div class="lrow"><span class="crit-ic ${e.ok?'ok':'bad'}" style="width:30px;height:30px;font-size:13px"><i class="fa-solid ${e.ok?'fa-check':'fa-xmark'}"></i></span><div class="lr-m"><div class="fw-sb">${e.t}</div><div class="small muted">${esc(e.d)}</div></div>${pill(e.ok?'Rempli':'Non rempli', e.ok?'ok':'bad')}</div>`).join('') + (c.stage==='recue'?`<button class="btn btn-primary mt-3" data-act="cand-review" data-id="${c.id}">Valider le profil et envoyer le test</button>`:''))}
      ${T.length ? T.slice().reverse().map(t=>uTestResult(c, t)).join('') : uCard("Résultats du test", uEmpty(sent?'Test envoyé, pas encore passé':'Test pas encore envoyé', sent?`Le lien a été envoyé le ${esc(sent)}. Vous pouvez simuler le passage du candidat.`:"Le test est envoyé automatiquement quand le profil est déclaré éligible.", sent?`<button class="btn btn-primary btn-sm" data-act="u-tsim" data-id="${c.id}">Simuler le test</button>`:''))}</div>
      <div class="stack"><section class="dark-card pd-test"><div class="kc mb-2">Test d'éligibilité</div><div class="d-flex align-items-end gap-2 mb-3"><b class="num" style="font-size:44px;line-height:1">${last?last.score/2:'—'}</b><span class="pb-1">/ 10 bonnes réponses</span><span class="ms-auto">${last?pill(last.score>=12?'Réussi':'Non réussi', last.score>=12?'ok':'bad'):pill(sent?'En attente':'Non envoyé','mute')}</span></div>
        ${uBar(last?last.score/20*100:0,'cor')}<div class="small mt-2 op-8">Seuil de réussite : 6 bonnes réponses sur 10 · ${TEST_Q.length} questions · 20 min · 2 tentatives</div>
        <div class="pd-kv mt-3"><span>Fiche envoyée</span><b>${sent?esc(sent):'—'}</b></div><div class="pd-kv"><span>Tentatives</span><b>${T.length} / 2</b></div><div class="pd-kv"><span>Dernier passage</span><b>${last?`${esc(last.d)} · ${last.dur} min`:'—'}</b></div></section>
        <section class="panel card-pad"><h3 class="h3 mb-3" style="font-size:17px">Actions sur le test</h3><div class="d-grid gap-2">
          ${sent?`<button class="btn btn-ghost" data-act="u-tfiche" data-id="${c.id}"><i class="fa-regular fa-envelope me-1"></i>Voir la fiche envoyée</button>`:`<button class="btn btn-ghost" data-act="u-tfiche" data-id="${c.id}"><i class="fa-regular fa-eye me-1"></i>Aperçu de la fiche du test</button>`}
          ${['profil_ok'].includes(c.stage) || (c.stage==='test_ko' && (c.tries||1)<2) ? `<button class="btn btn-primary" data-act="u-tsim" data-id="${c.id}"><i class="fa-solid fa-play me-1"></i>Passer le test comme le candidat</button><button class="btn btn-quiet" data-act="u-tauto" data-id="${c.id}" data-r="ok">Simuler un résultat réussi</button><button class="btn btn-quiet" data-act="u-tauto" data-id="${c.id}" data-r="ko">Simuler un échec</button><button class="btn btn-quiet" data-act="cand-remind" data-id="${c.id}">Relancer le candidat</button>`:''}
          ${c.stage==='test_ko' && (c.tries||1)>=2 ? `<button class="btn btn-ghost" data-act="cand-retry" data-id="${c.id}">Accorder une nouvelle tentative</button>`:''}
          ${c.stage==='recue' ? `<p class="small muted mb-0">Le test sera envoyé dès que le profil sera déclaré éligible.</p>`:''}</div></section></div></div>`; }

  if (tab==='hist') { const H = [[`Préinscription envoyée depuis ${c.src==='Site web'?'le site web':esc(c.src)}`, c.d, 'Candidat'],["Confirmation envoyée (e-mail + WhatsApp)", c.d, 'Système']];
    if (!c.docs) H.push(["Pièce d'identité manquante", c.d, 'Système']);
    if (c.stage==='rejete') H.push([`Profil déclaré non éligible${c.why?` : ${esc(c.why)}`:''}`, '—', 'Admission']);
    if (uTestSent(c)) H.push(['Profil déclaré éligible', uTestSent(c), 'Awa Bamba'],["Fiche du test d'éligibilité envoyée", uTestSent(c), 'Système']);
    T.forEach(t=>H.push([`Test passé — tentative ${t.n} : ${t.score/2}/10 bonnes réponses (${t.score>=12?'favorable':'défavorable'})${t.sim?' · simulation':''}`, `${t.d} · ${t.start}`, 'Candidat']));
    if (c.pay) H.push([`1er versement : ${fcfa(c.pay.amount)} · ${esc(c.pay.mode)} · ${c.pay.ref}`, c.pay.d, 'Caisse'],[`Compte créé · matricule ${c.mat}`, c.pay.d, 'Système']);
    if (c.stage==='actif') H.push(['Compte activé par le candidat', c.pay?.d||'—', 'Candidat']);
    S.audit.filter(a=>(a.o||'').includes(c.id)).forEach(a=>H.push([`${esc(a.a)}`, a.d, a.u]));
    body = uCard('Historique du dossier', `<ul class="act-list">${H.reverse().map(([t,d,u])=>`<li><span class="ad i9"></span><div><div>${t}</div><div class="small muted">${esc(d)} · ${esc(u)}</div></div></li>`).join('')}</ul>`); }

  return `<section class="panel card-pad pd-head"><div class="d-flex align-items-center gap-4 flex-wrap"><span class="ini" style="width:68px;height:68px;font-size:22px;background:var(--c300);font-family:var(--f-display)">${esc(initials(c.name))}</span>
    <div class="flex-grow-1" style="min-width:220px"><h2 class="h2 mb-0" style="font-size:24px">${esc(c.name)}</h2><div class="muted"><span class="mono">${c.id}</span> · reçue le ${esc(c.d)} · ${c.f.map(f=>esc(uFShort(f))).join(' + ')}</div><div class="d-flex gap-2 mt-2 flex-wrap">${stagePill(c.stage)}${pill(`Éligibilité ${okN}/${E.length}`, okN===E.length?'ok':'warn')}${last?pill(`Test ${last.score/2}/10 bonnes réponses`, last.score>=12?'ok':'bad'):''}</div></div>
    <div class="d-flex gap-2 flex-wrap">${acts}</div></div>
    <div class="pd-prog mt-4"><div class="d-flex justify-content-between small fw-sb mb-2"><span>Avancement de l'admission</span><span>${c.stage==='rejete'?'Dossier clos':`Étape ${Math.min(idx+1,6)} sur 6 · ${pct} %`}</span></div>
    ${frise(U_ADM_STEPS.map((l,i)=>({l, s: dates[i] ? esc(dates[i]) : i===idx && c.stage!=='rejete' ? 'En cours' : '—'})), c.stage==='actif' ? 6 : idx)}</div></section>
    <div class="mt-4">${uTabs([['info','Informations saisies'],['elig',`Éligibilité et test`],['hist','Historique']], tab, 'u-pdt')}</div>${body}`;
});
Object.assign(VIEWS['a-preinscription'], {parent:'a-preinscriptions', title:() => 'Dossier de préinscription', crumb:() => { const c = S.preins.find(x=>x.id===S.ui.pre); return `Administration <span class="sep">›</span> Préinscriptions <span class="sep">›</span> ${esc(c?c.id:'')}`; }});
uReg('pdt');
ACT['u-pre-open'] = el => { S.ui.pre = el.dataset.id; S.ui.pdt = el.dataset.t || 'info'; go('a-preinscription'); };
[S.perms, PERM_DEFAULT].forEach(P => Object.values(P).forEach(l => { if (l.includes('a-preinscriptions') && !l.includes('a-preinscription')) l.push('a-preinscription'); }));

/* Résultat d'une tentative, question par question */
function uTestResult(c, t){
  const good = t.ans.filter((a,i)=>a===TEST_Q[i].a).length, cats = [...new Set(U_TEST_CAT)].map(k=>{ const ids = U_TEST_CAT.map((x,i)=>x===k?i:-1).filter(i=>i>=0); return [k, ids.filter(i=>t.ans[i]===TEST_Q[i].a).length, ids.length]; });
  return `<section class="panel"><div class="panel-h"><h2>Tentative ${t.n}${t.sim?' <span class="pill p-info ms-1">Simulation</span>':''}</h2><span class="sub">${esc(t.d)} à ${t.start} · ${t.dur} min</span><div class="tools"><b class="num" style="font-size:20px">${t.score/2}<small class="muted"> / 10 bonnes réponses</small></b></div></div>
    <div class="panel-b"><div class="tcat mb-3">${cats.map(([k,o,n])=>`<div><div class="d-flex justify-content-between small fw-sb mb-1"><span>${k}</span><span>${o}/${n}</span></div>${uBar(o/n*100, o===n?'ok':o?'':'cor')}</div>`).join('')}</div>
    <div class="tq-list">${TEST_Q.map((q,i)=>{ const ok = t.ans[i]===q.a; return `<details class="tq ${ok?'ok':'ko'}"><summary><span class="tq-n">${String(i+1).padStart(2,'0')}</span><span class="lr-m">${esc(q.q)}</span><span class="crit-ic ${ok?'ok':'bad'}" style="width:24px;height:24px;font-size:11px"><i class="fa-solid ${ok?'fa-check':'fa-xmark'}"></i></span></summary><div class="tq-b"><div><span class="muted">Réponse du candidat :</span> <b class="${ok?'tx-ok':'tx-bad'}">${esc(q.o[t.ans[i]] ?? 'Sans réponse')}</b></div>${ok?'':`<div><span class="muted">Bonne réponse :</span> <b>${esc(q.o[q.a])}</b></div>`}<div class="small muted mt-1">${U_TEST_CAT[i]} · 2 points</div></div></details>`; }).join('')}</div>
    <div class="small muted mt-3">${good} bonne${good>1?'s':''} réponse${good>1?'s':''} sur ${TEST_Q.length} · cliquez sur une question pour voir la réponse donnée.</div></div></section>`;
}

/* Fiche du test envoyée au candidat */
ACT['u-tfiche'] = el => { const c = S.preins.find(x=>x.id===el.dataset.id), X = uCandX(c), sent = uTestSent(c), lim = '48 h';
  openModal({title:"Fiche du test d'éligibilité", size:'lg', body:`<div class="d-flex gap-2 flex-wrap mb-3">${pill(sent?`Envoyée le ${sent}`:'Aperçu — pas encore envoyée', sent?'ok':'mute')}${pill('E-mail','info')}${pill('WhatsApp','ok')}</div>
    <div class="tf-mail"><div class="tf-h"><div><span class="muted">De :</span> admissions@evc.ci</div><div><span class="muted">À :</span> ${esc(c.email)}</div><div><span class="muted">Objet :</span> <b>Votre test d'éligibilité MY EVC — ${esc(c.f.map(uFShort).join(' + '))}</b></div></div>
      <div class="tf-b"><p>Bonjour ${esc(X.first)},</p><p>Bonne nouvelle : votre profil est <b>éligible</b> à la formation ${esc(c.f.map(f=>F[f].name).join(' + '))}. Dernière étape avant votre inscription : un court test en ligne.</p>
      <div class="tf-box"><div><span>Questions</span><b>${TEST_Q.length} QCM</b></div><div><span>Durée</span><b>20 minutes</b></div><div><span>Seuil de réussite</span><b>6 sur 10</b></div><div><span>Tentatives</span><b>2</b></div></div>
      <p class="mb-2"><b>Thèmes :</b> logique, calcul, informatique de base, culture métier et savoir-être.</p><p class="mb-3">Le lien est valable <b>${lim}</b>. Prévoyez une connexion stable ; le test peut se faire sur téléphone ou ordinateur.</p>
      <div class="text-center my-3"><span class="btn btn-primary disabled" style="opacity:1">Commencer mon test</span><div class="small muted mt-2 mono">evc.ci/test/${c.id.toLowerCase()}</div></div><p class="mb-0">Référence de candidature : <b class="mono">${c.id}</b><br>L'équipe Admissions MY EVC</p></div></div>
    <div class="tf-wa mt-3"><div class="small-caps mb-1">Message WhatsApp · ${esc(c.phone)}</div><div class="tf-bubble">MY EVC : Bonjour ${esc(X.first)}, votre profil est éligible 🎉 Passez votre test (10 questions, 20 min) avant 48 h : evc.ci/test/${c.id.toLowerCase()} — Réf. ${c.id}</div></div>
    <details class="mt-3"><summary class="fw-sb">Sujet du test (réservé à l'équipe)</summary><ol class="small mt-2 mb-0">${TEST_Q.map((q,i)=>`<li class="mb-1">${esc(q.q)} <span class="tx-ok fw-sb">→ ${esc(q.o[q.a])}</span> <span class="muted">· ${U_TEST_CAT[i]}</span></li>`).join('')}</ol></details>`,
    foot:`${['profil_ok','test_ko'].includes(c.stage)?`<button class="btn btn-ghost me-auto" data-act="u-tresend" data-id="${c.id}">Renvoyer la fiche</button><button class="btn btn-primary" data-act="u-tsim" data-id="${c.id}">Simuler le test</button>`:`<button class="btn btn-primary" data-act="close-modal">Fermer</button>`}`}); };
ACT['u-tresend'] = el => { const c = S.preins.find(x=>x.id===el.dataset.id); c.testSent = '22/10/2026'; audit("Fiche du test renvoyée", c.id); closeModal(); rerender(); toast(`Fiche du test renvoyée à ${esc(c.name)} (e-mail + WhatsApp).`); };

/* Simulation : passage réel en vue candidat */
ACT['u-tsim'] = el => { const c = S.preins.find(x=>x.id===el.dataset.id); closeModal();
  if (c.stage==='recue') { toast("Validez d'abord le profil : le test n'est envoyé qu'aux profils éligibles.",'warn'); return; }
  if (c.stage==='test_ko' && (c.tries||1)>=2) { toast("Les 2 tentatives ont été utilisées. Accordez une nouvelle tentative.",'warn'); return; }
  S.ui.cand = c.id; S.ui.tsimFrom = c.id; S.ui.tStart = Date.now(); go('test-eligibilite'); };
/* Simulation : résultat généré */
ACT['u-tauto'] = el => { const c = S.preins.find(x=>x.id===el.dataset.id), ok = el.dataset.r==='ok', T = uTests(c), h = uSeed(c.id + T.length + Date.now()%97);
  const good = ok ? 6 + h%5 : 2 + h%4, order = TEST_Q.map((_,i)=>i).sort((a,b)=>((h>>a)&7)-((h>>b)&7)), okSet = new Set(order.slice(0, good));
  uRecordTest(c, TEST_Q.map((q,i)=>okSet.has(i) ? q.a : (q.a+1+(h+i)%3)%4), 9 + h%10, true); rerender();
  toast(`Simulation : ${c.name} obtient ${good} bonnes réponses sur 10 — ${good*2>=12?'test réussi, le 1er versement peut être encaissé.':'note insuffisante.'}`, good*2>=12?'ok':'warn'); };
function uRecordTest(c, ans, dur, sim){ const T = uTests(c), score = ans.filter((a,i)=>a===TEST_Q[i].a).length*2, now = new Date();
  T.push({n:T.length+1, d:'22/10/2026', start:`${now.getHours()} h ${String(now.getMinutes()).padStart(2,'0')}`, dur, score, ans, sim});
  c.tries = T.length; c.test = {score, d:'22/10/2026'}; c.stage = score>=12 ? 'test_ok' : 'test_ko';
  audit("Test d'éligibilité passé", `${c.id} — ${score/2}/10 bonnes réponses${sim?' (simulation)':''}`); }

/* Le test candidat enregistre les réponses ; en simulation admin, retour au dossier */
(() => { const base = VIEWS['test-eligibilite'].render;
  VIEWS['test-eligibilite'].render = () => { const html = base(), c = curCand();
    return S.ui.tsimFrom && c && S.ui.tsimFrom===c.id ? `<div class="sim-bar"><i class="fa-solid fa-eye"></i><span><b>Simulation</b> — vous voyez le test tel que ${esc(c.name)} le reçoit.</span><button class="btn btn-white-line btn-sm ms-auto" data-act="u-tsim-quit">Revenir au dossier</button></div>${html}` : html; };
  ACT['test-done'] = () => { clearInterval(S.timer); const c = curCand(); if (!c) return;
    const ans = TEST_Q.map((q,i)=>{ const r = $(`input[name=t${i}]:checked`); return r ? +r.value : null; }), dur = Math.max(1, Math.round((Date.now() - (S.ui.tStart||Date.now()))/60000));
    uRecordTest(c, ans, dur, !!S.ui.tsimFrom);
    const ok = c.stage==='test_ok', msg = ok ? `Test réussi : ${c.test.score/2}/10 bonnes réponses.` : `Résultat : ${c.test.score/2}/10 bonnes réponses. Seuil : 6 bonnes réponses sur 10.`;
    if (S.ui.tsimFrom) { S.ui.tsimFrom = null; S.ui.pre = c.id; S.ui.pdt = 'elig'; go('a-preinscription'); toast(`Simulation terminée — ${msg}`, ok?'ok':'warn'); }
    else { go('candidature'); toast(ok ? `${msg} Vous pouvez régler le premier versement.` : msg, ok?'ok':'warn'); } };
  ACT['u-tsim-quit'] = () => { clearInterval(S.timer); const id = S.ui.tsimFrom; S.ui.tsimFrom = null; S.ui.pre = id; S.ui.pdt = 'elig'; go('a-preinscription'); };
  const tStartBase = VIEWS['test-eligibilite'].after; VIEWS['test-eligibilite'].after = (...a) => { S.ui.tStart = Date.now(); return tStartBase && tStartBase(...a); };
})();

/* Les nouvelles préinscriptions conservent tout ce qui a été saisi */
(() => { const base = ACT.prein;
  ACT.prein = (...a) => { const t0 = S.ui.pstT0 || Date.now(), X = { first: $('#pi-first').value.trim(), last: $('#pi-last').value.trim(), payPref: $('input[name=ppay]:checked')?.value || '—', sessionWish: $('#pi-session').value,
      cgu: `22/10/2026 à ${new Date().getHours()} h ${String(new Date().getMinutes()).padStart(2,'0')}`, device: /Mobi/.test(navigator.userAgent) ? 'Mobile · navigateur' : 'Ordinateur · navigateur', docName: $('#pi-doc').files[0]?.name || '', timeSpent: `${Math.max(1, Math.round((Date.now()-t0)/60000))} min` };
    const n = S.preins.length; base(...a); if (S.preins.length > n) S.preins[0].x = X; };
  const after = VIEWS['preinscription'].after; VIEWS['preinscription'].after = (...a) => { S.ui.pstT0 = Date.now(); return after && after(...a); };
})();

/* Liste des préinscriptions : accès au dossier complet */
(() => { const base = VIEWS['a-preinscriptions'].render;
  VIEWS['a-preinscriptions'].render = () => { let html = base(); const k = S.ui.prt || 'todo', L = S.preins.filter(U_PRT[k][1]), c = L.find(x=>x.id===S.ui.pre) || L[0];
    if (c) { const T = uTests(c), last = T[T.length-1], E = eligibility(c), okN = E.filter(e=>e.ok).length;
      const block = `<div class="pd-mini mt-3"><div class="d-flex justify-content-between align-items-center mb-2"><b class="small">Éligibilité</b>${pill(`${okN}/${E.length} critères`, okN===E.length?'ok':'warn')}</div><div class="d-flex justify-content-between align-items-center"><b class="small">Test d'éligibilité</b>${last?pill(`${last.score/2}/10 bonnes réponses · ${T.length} tentative${T.length>1?'s':''}`, last.score>=12?'ok':'bad'):pill(uTestSent(c)?'Envoyé · en attente':'Non envoyé','mute')}</div></div>
        <button class="btn btn-indigo w-100 mt-3" data-act="u-pre-open" data-id="${c.id}"><i class="fa-regular fa-folder-open me-1"></i>Ouvrir le dossier complet</button>`;
      html = html.replace('<h3 class="h3 mt-3 mb-2" style="font-size:16px">Pièces justificatives</h3>', block + '<h3 class="h3 mt-3 mb-2" style="font-size:16px">Pièces justificatives</h3>'); }
    return html; };
})();

/* ----- Affectation à une promotion (après le 1er versement) ----- */
S.preins.forEach(c=>{ if (c.stage==='actif' && !c.promo) c.promo = {ses:'S-2027-01', lv:'Débutant', d:c.pay?.d||'16/10/2026'}; });
ACT['u-promo'] = el => { const c = S.preins.find(x=>x.id===el.dataset.id), open = S.sessions.filter(s=>s.status!=='Clôturée'), wish = open.find(s=>s.name===c.session) || open[0];
  openModal({title:`Affecter à une promotion — ${esc(c.name)}`, body:`<form id="pr-f" data-submit="u-promo-ok" data-id="${c.id}" novalidate class="d-flex flex-column gap-3">
    <div class="alert-strip as-info slim"><i class="fa-solid fa-circle-info"></i><span>Session souhaitée par le candidat : <b>${esc(c.session)}</b> · ${esc(c.mode)}. Test d'éligibilité : ${uTests(c).slice(-1)[0]?.score>=16?'très bon niveau':'niveau validé'}.</span></div>
    <div class="row g-3"><div class="col-sm-7"><label class="form-label" for="pr-s">Session (promotion)</label><select class="form-select" id="pr-s">${open.map(s=>`<option value="${s.id}" ${s===wish?'selected':''}>${esc(s.name)} · ${Math.max(0, s.cap - S.students.filter(x=>x.sessionId===s.id).length)} places</option>`).join('')}</select></div>
    <div class="col-sm-5"><label class="form-label" for="pr-l">Niveau</label><select class="form-select" id="pr-l">${U_LEVELS.map((l,i)=>`<option ${i===(uTests(c).slice(-1)[0]?.score>=16?1:0)?'selected':''}>${l}</option>`).join('')}</select></div></div>
    <label class="d-flex gap-2 align-items-center small"><input class="form-check-input mt-0" type="checkbox" checked id="pr-n">Envoyer la notification et le lien d'activation (e-mail + SMS + WhatsApp)</label></form>`,
    foot:`<button class="btn btn-ghost" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary" type="submit" form="pr-f">Affecter</button>`}); };
ACT['u-promo-ok'] = f => { const c = S.preins.find(x=>x.id===f.dataset.id), s = S.sessions.find(x=>x.id===$('#pr-s').value), lv = $('#pr-l').value;
  c.promo = {ses:s.id, lv, d:'22/10/2026'}; const st = S.students.find(x=>x.id===c.mat); if (st) Object.assign(st, {session:s.name, sessionId:s.id, group:lv});
  audit('Affectation à une promotion', `${c.id} (${c.mat}) → ${s.name} · ${lv}`); closeModal(); rerender();
  toast(`${esc(c.name)} affecté(e) à la ${esc(s.name)} · ${lv}. ${$('#pr-n')?.checked!==false?'Notification et lien d\'activation envoyés.':''}`); };
/* Côté candidat : l'activation attend l'affectation */
(() => { const base = VIEWS['candidature'].render;
  VIEWS['candidature'].render = () => { const c = curCand(); let html = base();
    if (c && c.stage==='paye') { const s = c.promo && S.sessions.find(x=>x.id===c.promo.ses);
      const box = c.promo ? `<div class="alert-strip as-ok mb-3"><i class="fa-solid fa-users-rectangle"></i><span>Vous êtes affecté(e) à la <b>${esc(s.name)}</b>, niveau ${esc(c.promo.lv)}. Finalisez la création de votre compte.</span></div>`
        : `<div class="alert-strip as-warn mb-3"><i class="fa-solid fa-hourglass-half"></i><span>Paiement reçu. L'administration vous affecte à une promotion (session et niveau). Vous recevrez une notification avec le lien d'activation.</span></div>`;
      html = html.replace(/(<div class="alert-strip as-ok mb-3"><i class="fa-solid fa-circle-check"><\/i><span>Paiement reçu)/, box + '$1');
      if (!c.promo) html = html.replace('<button class="btn btn-primary" data-go="activation">Finaliser mon compte</button>', '<button class="btn btn-primary" disabled>Activation après affectation</button>'); }
    return html; };
  const act = VIEWS['activation'].render; VIEWS['activation'].render = () => { const c = curCand(); if (c && c.stage==='paye' && !c.promo) return publicShell(`<div class="alert-strip as-warn"><i class="fa-solid fa-hourglass-half"></i><span>Votre compte sera activable dès que l'administration vous aura affecté(e) à une promotion.</span></div><a class="btn btn-primary mt-3" href="#candidature">Mon suivi de candidature</a>`, 760); return act(); };
})();
