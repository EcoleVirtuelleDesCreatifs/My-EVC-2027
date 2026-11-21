/* ============ MY EVC — Espace étudiant épuré ============
   Objectif : qu'un étudiant comprenne son espace en 5 secondes.
     - 5 entrées de menu seulement : Accueil, Mes cours, Mes projets, Paiements, Certificat
       (+ Bibliothèque, ajoutée par etudiant/bibliotheque.js) ;
       le reste est rangé dans « Plus » (replié) ;
     - un accueil qui répond à trois questions : quel est mon prochain cours ? quel projet dois-je rendre ?
       combien me reste-t-il à payer ? avec UNE action principale mise en avant ;
     - pas de visite guidée qui s'ouvre toute seule (elle reste accessible : bouton « Guide »).
   Les écrans détaillés (séances, projets, paiements…) sont ceux des fichiers student.js et ui-student.js. */

/* ---------- 1. Libellés courts et menu réduit ---------- */
Object.entries({
  'e-dashboard':['Accueil','fa-house-chimney'], 'e-seances':['Mes cours','fa-calendar-days'], 'e-todo':['Mes projets','fa-pen-nib'],
  'e-paiements':['Paiements','fa-credit-card'], 'e-certification':['Certificat','fa-award'], 'e-formations':['Programme','fa-book-open'],
  'e-webtv':['Vidéos des cours','fa-tv'], 'e-evaluations':['Quiz et évaluations','fa-list-check'], 'e-messagerie':['Messages','fa-message'],
  'e-creneaux':['Mon créneau','fa-clock'], 'e-insertion':['Stages et emploi','fa-briefcase'], 'e-assistance':['Aide','fa-circle-question'], 'e-profil':['Mon profil','fa-user']
}).forEach(([id,[l,i]]) => { if (VIEWS[id]) { VIEWS[id].label = l; VIEWS[id].icon = i; } });

MENUS.etu = [
  ['', ['e-dashboard','e-seances','e-todo','e-paiements','e-certification']],
  ['Plus', ['e-formations','e-webtv','e-evaluations','e-messagerie','e-creneaux','e-insertion','e-assistance'], true]
];

/* Barre du bas (mobile) : les mêmes 4 essentiels + profil */
(() => { const base = bottomNav; bottomNav = function(sp, cur){
  if (sp!=='etu') return base(sp, cur);
  const pick = [['e-dashboard','Accueil'],['e-seances','Cours'],['e-todo','Projets'],['e-paiements','Paiements'],['e-profil','Profil']];
  return `<nav class="bottom-nav" aria-label="Navigation rapide">${pick.map(([id,l]) => { const x = VIEWS[id], c = menuCount(id); return `<a href="#${id}" class="${id===cur?'on':''}" ${id===cur?'aria-current="page"':''}><i class="fa-solid ${x.icon}"></i><span class="lbl">${l}</span>${c?`<span class="bn-count">${c}</span>`:''}</a>`; }).join('')}</nav>`; }; })();

/* ---------- 2. Accueil ---------- */
const eFirst = () => S.st.student.first;
/* L'action la plus utile maintenant, par ordre de priorité */
function eAction(fs){
  const next = uNextSeances(fs), now = next.find(x=>x.d===0), open = uOpenTodos(fs), late = open.find(t=>t.due<0), ps = payState(), payLate = ps.sched.find(s=>s.status==='En retard'), paySoon = ps.next && ps.next.days<=7 ? ps.next : null;
  if (now) return {tone:'live', k:'Aujourd\'hui', t:`Cours en direct : ${now.t}`, s:`${uH(hrs(now))} · ${now.mode==='Virtuel'?'en ligne':'en salle'} · ${uTrainerOf(now.f)}`,
    btn: now.mode==='Virtuel' ? `<button class="btn btn-primary btn-lg" data-act="u-checkin" data-id="${now.id}"><i class="fa-solid fa-video"></i>Rejoindre le cours</button>` : `<button class="btn btn-primary btn-lg" data-act="seance" data-id="${now.id}"><i class="fa-solid fa-qrcode"></i>Pointer en salle</button>`};
  if (late) return {tone:'bad', k:'En retard', t:late.t, s:`Ce projet était à rendre ${dueTxt(late.due).toLowerCase()}. Rendez-le dès que possible.`, btn:`<button class="btn btn-primary btn-lg" data-act="todo" data-id="${late.id}"><i class="fa-solid fa-upload"></i>Rendre mon projet</button>`};
  if (payLate) return {tone:'bad', k:'Paiement en retard', t:`${fcfa(payLate.amount - payLate.paid)} à régler`, s:`Échéance du ${fmtDM(uPayDate(payLate.due))} dépassée.`, btn:`<a class="btn btn-primary btn-lg" href="#e-paiements"><i class="fa-solid fa-mobile-screen"></i>Payer maintenant</a>`};
  const fresh = open.find(t=>t.status==='afaire');
  if (fresh) return {tone:'new', k:'Nouveau projet', t:fresh.t, s:`À rendre ${dueTxt(fresh.due).toLowerCase()}. Lisez les consignes puis démarrez.`, btn:`<button class="btn btn-primary btn-lg" data-act="todo" data-id="${fresh.id}"><i class="fa-solid fa-play"></i>Voir et démarrer</button>`};
  if (paySoon) return {tone:'warn', k:'Paiement à venir', t:`${fcfa(paySoon.amount - paySoon.paid)} le ${fmtDM(uPayDate(paySoon.due))}`, s:'Payez par Mobile Money en une minute, le reçu arrive tout de suite.', btn:`<a class="btn btn-primary btn-lg" href="#e-paiements">Payer</a>`};
  if (next[0]) return {tone:'calm', k:'Prochain cours', t:next[0].t, s:`${fmtD(dateOf(next[0]),{weekday:'long',day:'numeric',month:'long'})} · ${uH(hrs(next[0]))}`, btn:`<a class="btn btn-primary btn-lg" href="#e-seances">Voir mes cours</a>`};
  return {tone:'calm', k:'Tout est à jour', t:'Rien à faire pour le moment', s:'Profitez-en pour revoir les vidéos des cours.', btn:`<a class="btn btn-primary btn-lg" href="#e-webtv">Vidéos des cours</a>`};
}
function eDashboard(){
  const fs = vf(), a = eAction(fs), next = uNextSeances(fs), open = uOpenTodos(fs), ps = payState();
  const pct = Math.round(fs.reduce((x,f)=>x+prog(f),0)/fs.length), jours = typeof uDaysLeft==='function' ? uDaysLeft(uAccEnd()) : null;
  const done = S.st.todos.filter(t=>fs.includes(t.f) && t.status==='valide').length, totalP = S.st.todos.filter(t=>fs.includes(t.f)).length;
  const onb = !S.st.onb.hidden && typeof onbSteps==='function' ? onbSteps() : null, onbN = onb ? onb.filter(x=>x.done).length : 3;
  const s0 = next[0], p0 = open[0];
  const carte = (href, icon, k, t, s, extra='', act='') => `<a class="e-card" ${act?`role="button" ${act}`:`href="${href}"`}><span class="ec-i"><i class="fa-solid ${icon}"></i></span><span class="ec-k">${k}</span><span class="ec-t">${t}</span><span class="ec-s">${s}</span>${extra}<span class="ec-go" aria-hidden="true"><i class="fa-solid fa-arrow-right"></i></span></a>`;
  return `${commBanner()}
  <div class="e-wrap">
    <p class="e-hello">Bonjour ${esc(eFirst())}</p>
    <section class="e-now t-${a.tone}">${star('en-star')}
      <div class="en-k">${a.tone==='live'?'<span class="live-dot"></span>':''}${esc(a.k)}</div>
      <h2 class="en-t">${esc(a.t)}</h2><p class="en-s">${esc(a.s)}</p>
      <div class="en-b">${a.btn}</div></section>

    <div class="e-cards">
      ${carte('#e-seances','fa-calendar-days','Prochain cours', s0 ? esc(s0.t) : 'Aucun cours prévu', s0 ? `${relDay(s0.d)} · ${uH(hrs(s0))} · ${s0.mode==='Virtuel'?'en ligne':'en salle'}` : 'Le planning sera publié bientôt')}
      ${carte('#e-todo','fa-pen-nib','Mon projet', p0 ? esc(p0.t) : 'Aucun projet à rendre', p0 ? `${uPStatusEtu(p0.status)} · ${dueTxt(p0.due).toLowerCase()}` : `${done} projet${done>1?'s':''} validé${done>1?'s':''}`, p0 && open.length>1 ? `<span class="ec-more">+ ${open.length-1} autre${open.length>2?'s':''}</span>` : '', p0 ? `data-act="todo" data-id="${p0.id}"` : '')}
      ${carte('#e-paiements','fa-credit-card','Paiements', ps.balance ? `${fcfa(ps.balance)} restant` : 'Formation soldée', ps.next ? `Prochaine échéance : ${fmtDM(uPayDate(ps.next.due))}` : 'Merci, tout est réglé', `<span class="ec-bar">${uBar(ps.total?ps.paid/ps.total*100:100)}</span>`)}
    </div>

    <section class="e-progress">
      <div class="ep-h"><span>Ma progression</span><b>${pct} %</b></div>
      ${fs.map(f=>`<div class="ep-row"><span class="ep-f">${fchip(f)}<span class="d-none d-sm-inline">${esc(uFShort(f))}</span></span><span class="ep-bar">${uBar(prog(f), 'c-'+f)}</span><span class="ep-n">Séance ${Math.min(chaptersDone(f)+1, chapters(f))} / ${chapters(f)}</span></div>`).join('')}
      <div class="ep-foot"><span><i class="fa-solid fa-check"></i>${done} / ${totalP} projets validés</span>${jours!=null?`<span><i class="fa-regular fa-hourglass-half"></i>Fin de formation dans ${jours} jours</span>`:''}</div>
    </section>

    ${onb && onbN<3 ? `<section class="e-start"><div class="es-h"><b>Bien démarrer</b><span>${onbN} / 3</span><button class="btn btn-quiet btn-sm ms-auto" data-act="tour-start"><i class="fa-regular fa-circle-question"></i>Guide</button><button class="btn-close" data-act="onb-hide" aria-label="Masquer"></button></div>
      <ol class="es-list">${onb.map(x=>`<li class="${x.done?'done':''}"><span class="es-n">${x.done?'<i class="fa-solid fa-check"></i>':x.n}</span><span class="lr-m">${x.t}</span>${x.done?'':x.btn.replace(/btn-ghost|btn-primary/,'btn-ghost')}</li>`).join('')}</ol></section>` : ''}
  </div>`;
}
const uPStatusEtu = st => ({afaire:'À démarrer', encours:'En cours', reprendre:'À reprendre', soumis:'Rendu, en validation', valide:'Validé'})[st] || st;

(() => { const D = VIEWS['e-dashboard'];
  D.render = () => S.st && S.st.expired && typeof uExpiredView==='function' ? uExpiredView() : eDashboard();
  D.after = null;                       // plus de décompte animé ni de visite guidée automatique
  D.title = () => 'Accueil'; D.crumb = () => `Espace étudiant · ${fmtLong(TODAY)}`; D.noFsel = false;
})();

/* ---------- 3. Barre du haut plus calme côté étudiant ---------- */
(() => { const base = shell; shell = function(v, html){ let out = base(v, html); if (S.role!=='etu') return out;
  out = out.replace(/<label class="search-field t-search[\s\S]*?<\/label>/, '');                    // recherche : icône seule
  out = out.replace('class="btn btn-quiet btn-icon d-md-none" data-act="cmdk"', 'class="btn btn-quiet btn-icon" data-act="cmdk"');
  return out; }; })();
