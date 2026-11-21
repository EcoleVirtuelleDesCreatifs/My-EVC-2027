/* ============ MY EVC — Activité en direct : qui est connecté, sur quelle page, quelles actions ============
   Dans la maquette, les autres étudiants sont simulés ; les actions faites dans l'espace étudiant de démonstration
   (Aya Kouassi) sont réellement suivies. En production : WebSocket / Laravel Echo + journal d'activité. */
const LIVE_PAGES = [['e-dashboard','Accueil','fa-house-chimney'],['e-webtv','Web TV','fa-tv'],['e-todo','My To-Do','fa-list-check'],['e-seances','Mes séances','fa-calendar-days'],['e-paiements','Mes paiements','fa-wallet'],['e-creneaux','Mes créneaux','fa-clock'],['e-formations','Mes formations','fa-layer-group'],['seance','Séance en ligne','fa-video']];
const LIVE_CITIES = ['Abidjan — Cocody','Abidjan — Yopougon','Bouaké','Abidjan — Plateau','San-Pédro','Abidjan — Marcory','Yamoussoukro','Abidjan — Abobo','Dakar'];
const LIVE_WORKS = {DG:['Logo — maquis','Affiche A2 festival','Charte graphique'], CM:['Calendrier éditorial','Personas','Script vidéo 30 s'], GIA:['Tableau des stocks Excel','CV sous Word','Présentation PowerPoint']};
S.live = {events:[], users:[], paused:false, pool:[]};
const pageInfo = id => LIVE_PAGES.find(p=>p[0]===id) || [id, VIEWS[id]?.label || id, VIEWS[id]?.icon || 'fa-circle'];
const pick = a => a[Math.floor(Math.random()*a.length)];
function liveInit(){
  if (S.live.users.length || S.live.init) return; S.live.init = true;
  S.live.pool = S.students.filter(s=>s.status==='Actif' && s.id!=='EVC-26-0142');
  S.live.pool.slice(0,8).forEach((s,i) => liveJoin(s, 3+i*6, true));
  for (let i=0; i<12; i++) liveRandom(Date.now() - (40-i*3)*60000);
}
function liveJoin(s, minAgo=0, silent){ const u = {id:s.id, name:s.name, f:s.f, page:pick(LIVE_PAGES.slice(0,7))[0], since:Date.now()-minAgo*60000, device:Math.random()<.55?'Mobile':'Ordinateur', city:LIVE_CITIES[S.live.users.length % LIVE_CITIES.length]};
  S.live.users.push(u); if (!silent) livePush({uid:u.id, txt:'Se connecte à son espace', ic:'fa-solid fa-right-to-bracket', kind:'login'}); return u; }
function livePush(ev){ ev.t = ev.t || Date.now(); const u = S.live.users.find(x=>x.id===ev.uid); ev.name = ev.name || u?.name; ev.f = ev.f || u?.f;
  S.live.events.unshift(ev); if (S.live.events.length > 120) S.live.events.length = 120; if (S.view==='a-live') livePaint(ev); }
function liveRandom(t){
  const U = S.live.users; if (!U.length) return; const r = Math.random();
  if (!t && r < .07 && U.length > 5) { const u = pick(U.filter(x=>!x.you)); if (u) { S.live.users = U.filter(x=>x!==u); livePush({uid:u.id, name:u.name, f:u.f, txt:'Se déconnecte', ic:'fa-solid fa-right-from-bracket', kind:'login'}); } return; }
  if (!t && r < .14) { const s = S.live.pool.find(p=>!U.some(u=>u.id===p.id)); if (s) { liveJoin(s); return; } }
  const u = pick(U.filter(x=>!x.you)); if (!u) return; const f = u.f[0], k = Math.random();
  let ev;
  if (k < .4) { const p = pick(LIVE_PAGES); u.page = p[0]; ev = {txt: p[0]==='seance' ? 'Rejoint la séance en ligne' : `Ouvre ${p[1]}`, ic:`fa-solid ${p[2]}`, kind: p[0]==='seance'?'seance':'page'}; }
  else if (k < .6) { const v = pick(S.videos.filter(x=>x.f===f && x.pub)); u.page = 'e-webtv'; ev = {txt:`Regarde « ${v.t} »`, ic:'fa-solid fa-play', kind:'tv'}; }
  else if (k < .72) { u.page = 'e-todo'; ev = Math.random()<.5 ? {txt:`Démarre le projet « ${pick(LIVE_WORKS[f])} »`, ic:'fa-solid fa-play', kind:'todo'} : {txt:`Termine et publie « ${pick(LIVE_WORKS[f])} »`, ic:'fa-solid fa-cloud-arrow-up', kind:'todo'}; }
  else if (k < .8) { u.page = 'e-paiements'; ev = {txt:`Paie ${fcfa(pick([25000,40000,F[f].price-F[f].deposit]))} via ${pick(['Orange Money','Wave','MTN MoMo','Moov Money'])}`, ic:'fa-solid fa-wallet', kind:'pay'}; }
  else if (k < .88) { u.page = 'e-creneaux'; const sl = pick(S.slots.filter(x=>x.f===f)); ev = {txt:`Choisit le créneau ${sl.day}, ${sl.h}`, ic:'fa-solid fa-clock', kind:'slot'}; }
  else { ev = {txt:`Télécharge « ${STARTER[f].t} »`, ic:'fa-solid fa-download', kind:'dl'}; }
  livePush({...ev, uid:u.id, t});
}
/* Étudiant de démonstration : chaque page ouverte et chaque action est réellement suivie */
function liveMe(){ if (!S.st) return null; liveInit(); let u = S.live.users.find(x=>x.you);
  if (!u) { u = {id:'EVC-26-0142', you:true, name:`${S.st.student.first} ${S.st.student.last}`, f:fl(), page:'e-dashboard', since:Date.now(), device:'Ordinateur', city:'Abidjan — Cocody'}; S.live.users.unshift(u); livePush({uid:u.id, txt:'Se connecte à son espace', ic:'fa-solid fa-right-to-bracket', kind:'login'}); }
  u.f = fl(); return u; }
function liveTrack(v){ if (v.space!=='etu' || !S.st) return; const u = liveMe(); if (u.page===v.id && S.live.events[0]?.uid===u.id) return; u.page = v.id; livePush({uid:u.id, txt:`Ouvre ${v.label}`, ic:`fa-solid ${v.icon}`, kind:'page'}); }
const LIVE_WRAP = {'todo-start':['Démarre un projet','fa-solid fa-play','todo'], 'todo-submit':['Termine et publie un projet','fa-solid fa-cloud-arrow-up','todo'], 'tv-pick':['Regarde','fa-solid fa-play','tv'], 'tv-resume':['Reprend','fa-solid fa-play','tv'], 'tv-toggle':['Lecture / pause sur la Web TV','fa-solid fa-circle-play','tv'],
  'slot-pick':['Choisit son créneau','fa-solid fa-clock','slot'], 'rdv-book':['Réserve une séance individuelle','fa-solid fa-calendar-plus','slot'], 'pay-go':['Effectue un paiement','fa-solid fa-wallet','pay'], 'onb-soft-ok':["Termine l'étape « logiciels »",'fa-solid fa-check','dl'], 'onb-dl':['Télécharge','fa-solid fa-download','dl'], 'fake-dl':['Télécharge','fa-solid fa-download','dl'], 'msg-send':['Envoie un message','fa-solid fa-comments','page'], 'n-read':['Lit une notification','fa-solid fa-bell','page']};
Object.entries(LIVE_WRAP).forEach(([k,[txt,ic,kind]]) => { const o = ACT[k]; if (!o) return; ACT[k] = (el, e) => {
  if (S.role==='etu' && S.st) { const u = liveMe(); let d = '';
    if (k==='tv-pick' || k==='tv-resume') { const v = S.videos.find(x=>x.id===el.dataset.id); d = v ? ` « ${v.t} »` : ''; u.page = 'e-webtv'; }
    if (k==='fake-dl' || k==='onb-dl') d = el.dataset.n ? ` « ${el.dataset.n} »` : ' un document';
    if (k==='pay-go') { const a = +($('#pay-amt')?.value||0), m = $('input[name=pmode]:checked')?.value; d = a ? ` de ${fcfa(a)}${m?' via '+m:''}` : ''; }
    livePush({uid:u.id, txt:txt+d, ic, kind}); }
  return o(el, e); }; });
setInterval(() => { if (!S.live.paused && S.live.users.length) liveRandom(); }, 3500);

/* ----- Écran administrateur « En direct » ----- */
const ago = t => { const m = Math.floor((Date.now()-t)/60000); return m < 1 ? "à l'instant" : m < 60 ? `il y a ${m} min` : `il y a ${Math.floor(m/60)} h`; };
const hms = t => new Date(t).toLocaleTimeString('fr-FR',{hour:'2-digit',minute:'2-digit',second:'2-digit'});
const LIVE_F = [['','Tout'],['page','Navigation'],['tv','Web TV'],['todo','Travaux'],['pay','Paiements'],['slot','Créneaux'],['login','Connexions']];
function liveStats(){ const U = S.live.users, hr = S.live.events.filter(e=>Date.now()-e.t < 3600000).length;
  return `<div class="stats">${statBox('<span class="live-dot me-1"></span>Connectés maintenant', U.length, `${U.filter(u=>u.device==='Mobile').length} sur mobile`)}${statBox('Sur la Web TV', U.filter(u=>u.page==='e-webtv').length, 'regardent un cours')}${statBox('En séance en ligne', U.filter(u=>u.page==='seance').length, 'classe virtuelle')}${statBox('Actions', hr, 'dernière heure')}</div>`; }
function liveUsers(){ const U = S.live.users; if (!U.length) return empty('Personne n\'est connecté pour le moment','fa-moon');
  return U.map(u => { const p = pageInfo(u.page), last = S.live.events.find(e=>e.uid===u.id);
    return `<div class="row-item clickable live-u" data-act="live-user" data-id="${u.id}"><span class="live-av">${avatar(u.name,'sm')}<i class="live-dot"></i></span><div class="ri-main"><div class="ri-t">${esc(u.name)}${u.you?' <span class="pill p-acc nodot ms-1">démo</span>':''}</div><div class="ri-s">${u.f.map(fchip).join(' ')} <i class="fa-solid ${p[2]} ms-1"></i> ${esc(p[1])}${last?` · <span class="muted">${esc(last.txt)}</span>`:''}</div></div><div class="text-end small muted d-none d-sm-block" style="min-width:120px"><div><i class="fa-solid ${u.device==='Mobile'?'fa-mobile-screen':'fa-laptop'} me-1"></i>${esc(u.city)}</div><div>connecté ${ago(u.since)}</div></div></div>`; }).join(''); }
const liveRow = e => `<div class="live-ev ${e.fresh?'fresh':''}"><span class="live-ic"><i class="${e.ic}"></i></span><div class="ri-main"><div class="small"><b>${esc(e.name||'')}</b> ${esc(e.txt)}</div><div class="ri-s">${hms(e.t)}${e.f?' · '+e.f.map(fchip).join(' '):''}</div></div></div>`;
function liveFeed(){ const k = S.ui.lf || '', L = S.live.events.filter(e=>!k || e.kind===k).slice(0,40); return L.map(liveRow).join('') || empty('Aucune activité','fa-wave-square'); }
function livePaint(ev){ const fd = $('#live-feed'); if (!fd) return;
  if (!S.ui.lf || ev.kind===S.ui.lf) { fd.querySelector('.empty')?.remove(); fd.insertAdjacentHTML('afterbegin', liveRow({...ev, fresh:true})); while (fd.children.length > 40) fd.lastElementChild.remove(); }
  const us = $('#live-users'); if (us) us.innerHTML = liveUsers(); const st = $('#live-stats'); if (st) st.innerHTML = liveStats(); }
view('a-live','admin','En direct','fa-tower-broadcast', () => { liveInit(); if (S.st && S.live.users.some(u=>u.you)) liveMe();
  return `<div class="d-flex align-items-center gap-2 mb-3 flex-wrap"><span class="live-badge"><span class="live-dot"></span>EN DIRECT</span><span class="small muted">Connexions et actions des étudiants, mises à jour instantanément</span><button class="btn btn-ghost btn-sm ms-auto" data-act="live-pause">${S.live.paused?'<i class="fa-solid fa-play me-1"></i>Reprendre':'<i class="fa-solid fa-pause me-1"></i>Mettre en pause'}</button></div>
  <div id="live-stats">${liveStats()}</div>
  <div class="row g-3"><div class="col-xl-7">${panel('Étudiants connectés', `<div id="live-users">${liveUsers()}</div>`, {p0:true, sub:'Cliquez pour voir le détail de la session'})}</div>
  <div class="col-xl-5"><section class="panel"><div class="panel-h"><h2>Fil d'activité</h2></div><div class="chips p-2 border-bottom">${LIVE_F.map(([k,l])=>`<button class="chip-filter ${(S.ui.lf||'')===k?'on':''}" data-act="live-f" data-k="${k}">${l}</button>`).join('')}</div><div id="live-feed" class="live-feed">${liveFeed()}</div></section></div></div>
  <p class="small muted mt-3 mb-0"><i class="fa-solid fa-shield-halved me-1"></i>Les étudiants sont informés, dans les conditions d'utilisation, de la journalisation de leurs connexions et de leurs actions sur la plateforme.</p>`; });
Object.assign(ACT, {
  'live-pause': () => { S.live.paused = !S.live.paused; rerender(); },
  'live-f': el => { S.ui.lf = el.dataset.k; rerender(); },
  'live-user': el => { const u = S.live.users.find(x=>x.id===el.dataset.id); if (!u) return; const p = pageInfo(u.page), evs = S.live.events.filter(e=>e.uid===u.id).slice(0,15);
    openModal({title:`${avatar(u.name,'sm')}<span class="ms-2">${esc(u.name)}</span>`, size:'modal-lg', body:`<div class="stats mb-3">${statBox('Page actuelle', `<span style="font-size:1rem">${esc(p[1])}</span>`)}${statBox('Connecté', `<span style="font-size:1rem">${ago(u.since)}</span>`)}${statBox('Appareil', `<span style="font-size:1rem">${u.device}</span>`, esc(u.city))}</div><div class="small-caps mb-2">Actions de la session</div><div class="panel">${evs.map(liveRow).join('') || empty('Aucune action')}</div>`,
      foot:`<button class="btn btn-ghost" data-act="live-msg" data-id="${u.id}"><i class="fa-solid fa-bullhorn me-1"></i>Lui envoyer un communiqué</button><button class="btn btn-primary" data-act="close-modal">Fermer</button>`}); },
  'live-msg': el => { S.ui.cmMode = 'sel'; S.ui.cmSel = [el.dataset.id]; closeModal(); go('a-communication'); }
});
