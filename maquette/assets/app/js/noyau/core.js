/* ============ MY EVC — noyau : état, routeur, gabarit, composants ============ */
const $ = (s, r=document) => r.querySelector(s);
const $$ = (s, r=document) => [...r.querySelectorAll(s)];
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const fcfa = n => Math.round(n).toLocaleString('fr-FR').replace(/\s/g,' ') + ' FCFA';
const nfr = n => Number(n).toLocaleString('fr-FR').replace(/\s/g,' ');
const fmtD = (d, o={weekday:'short',day:'numeric',month:'short'}) => d.toLocaleDateString('fr-FR', o);
const relDay = n => n===0 ? "Aujourd'hui" : n===1 ? 'Demain' : n===-1 ? 'Hier' : n>0 ? `Dans ${n} j` : `Il y a ${-n} j`;
const clone = o => JSON.parse(JSON.stringify(o));
const initials = n => n.split(/[\s-]+/).filter(Boolean).slice(0,2).map(x=>x[0]).join('').toUpperCase();
const hueOf = s => { let h=0; for (const c of s) h=(h*31+c.charCodeAt(0))%360; return h; };
const avatar = (name, cls='', img=null) => img ? `<span class="avatar ${cls}"><img src="${img}" alt=""></span>` : `<span class="avatar ${cls}" style="background:hsl(${hueOf(name)} 45% 42%)">${esc(initials(name))}</span>`;
const fchip = f => `<span class="fchip f-${f}">${f}</span>`;
const pill = (txt, kind='mute', nodot) => `<span class="pill p-${kind}${nodot?' nodot':''}">${esc(txt)}</span>`;
const bar = (pct, f) => `<div class="bar ${f?'c-'+f:''}" role="progressbar" aria-valuenow="${Math.round(pct)}" aria-valuemin="0" aria-valuemax="100"><span style="width:${Math.max(0,Math.min(100,pct))}%"></span></div>`;
const kpi = (label, value, sub='', icon='fa-circle') => `<div class="kpi"><div class="k-l"><i class="fa-solid ${icon}"></i>${esc(label)}</div><div class="k-v">${value}</div><div class="k-s">${sub}</div></div>`;
const panel = (title, body, {tools='', sub='', p0=false, cls='', id=''}={}) => `<section class="panel ${cls}" ${id?`id="${id}"`:''}><div class="panel-h"><h2>${title}</h2>${sub?`<span class="sub">${sub}</span>`:''}${tools?`<div class="tools">${tools}</div>`:''}</div><div class="panel-b ${p0?'p0':''}">${body}</div></section>`;
const empty = (txt, icon='fa-inbox') => `<div class="empty"><i class="fa-solid ${icon}"></i>${esc(txt)}</div>`;
const fmtPct = v => `${Math.round(v)} %`;

/* ---------- État ---------- */
const STATUS = { afaire:['À faire','mute'], encours:['En cours','info'], soumis:['Publié · à valider','warn'], reprendre:['À reprendre','bad'], valide:['Validé','ok'] };
const ADMIN_MODULES = [
  ['a-dashboard','Dashboard général','fa-chart-pie'],['a-live','En direct','fa-tower-broadcast'],['a-etudiants','Étudiants','fa-user-graduate'],['a-preinscriptions','Préinscriptions','fa-user-plus'],['a-formations','Formations','fa-layer-group'],
  ['a-sessions','Sessions','fa-calendar-days'],['a-creneaux','Créneaux et rendez-vous','fa-clock'],['a-webtv','Web TV','fa-tv'],['a-formateurs','Formateurs','fa-chalkboard-user'],['a-seances','Séances et présences','fa-qrcode'],['a-travaux','Travaux et évaluations','fa-clipboard-check'],
  ['a-studio','Studio Creative','fa-palette'],['a-reco','RECO 60','fa-hourglass-half'],['a-finances','Finances','fa-wallet'],['a-factures','Factures et reçus','fa-file-invoice'],['a-certifications','Certifications','fa-award'],
  ['a-documents','Documents','fa-folder-open'],['a-communication','Communiqués','fa-bullhorn'],['a-stages','Stages et alumni','fa-briefcase'],['a-support','Support','fa-headset'],
  ['a-rapports','Rapports et statistiques','fa-chart-line'],['a-roles','Rôles et permissions','fa-shield-halved'],['a-audit',"Journal d'audit",'fa-fingerprint'],['a-parametres','Paramètres généraux','fa-gear']];
const ALL_A = ADMIN_MODULES.map(m=>m[0]);
const PERM_DEFAULT = {
  'Super Administrateur': ALL_A,
  'Direction Générale': ALL_A.filter(x=>!['a-roles','a-parametres'].includes(x)),
  'Responsable pédagogique': ['a-dashboard','a-live','a-creneaux','a-webtv','a-etudiants','a-formations','a-sessions','a-formateurs','a-seances','a-travaux','a-studio','a-reco','a-certifications','a-documents','a-communication','a-rapports'],
  'Responsable administratif': ['a-dashboard','a-live','a-communication','a-creneaux','a-etudiants','a-preinscriptions','a-factures','a-sessions','a-documents','a-communication','a-stages','a-support','a-rapports'],
  'Responsable financier': ['a-dashboard','a-etudiants','a-preinscriptions','a-finances','a-factures','a-communication','a-rapports'],
  'Formateur principal': ['a-dashboard','a-creneaux','a-webtv','a-etudiants','a-seances','a-travaux','a-studio','a-reco','a-documents'],
  'Formateur adjoint': ['a-seances','a-travaux','a-documents'],
  'Responsable certification': ['a-dashboard','a-etudiants','a-studio','a-reco','a-certifications','a-documents','a-rapports'],
  'Responsable communication': ['a-dashboard','a-webtv','a-communication','a-stages','a-documents'],
  'Support technique': ['a-live','a-etudiants','a-support','a-audit','a-parametres']};
const MFA_ROLES = ['Super Administrateur','Direction Générale','Responsable financier','Responsable certification'];

const S = {
  role:null, profile:'GIA+DG+CM', fsel:'ALL', adminRole:'Super Administrateur', theme:'auto', ui:{},
  students: buildStudents(), preins: clone(PREINS), perms: clone(PERM_DEFAULT), mfa:[...MFA_ROLES],
  sessions: clone(SESSIONS), trainers: clone(TRAINERS), docs: clone(DOCS), lib: clone(LIB), tickets: clone(ADMIN_TICKETS), audit: clone(AUDIT), offers: clone(OFFERS),
  comms:[{d:'21/10/2026 10:00',t:'Rappel échéance du 31 octobre',target:'Étudiants avec solde',ch:['WhatsApp','Email'],n:38,read:'71 %'},{d:'18/10/2026 09:00',t:'Studio Creative édition 7 : inscriptions ouvertes',target:'Étudiants DG',ch:['Notification','Email'],n:54,read:'83 %'},{d:'15/10/2026 16:00',t:'Fermeture exceptionnelle le 1er novembre',target:'Tous',ch:['Notification','SMS'],n:212,read:'92 %'}],
  anotifs:[{id:'an1',kind:'done',t:'Logo — Maquis "Chez Tantie Rose"',txt:'Kevin Ahoua a terminé et publié son projet « Logo — Maquis "Chez Tantie Rose" »',d:'Il y a 25 min',read:false},{id:'an2',kind:'start',t:'Affiche — Festival Abidjan Urban Arts',txt:'Ibrahim Sanogo a commencé son projet « Affiche — Festival Abidjan Urban Arts »',d:'Il y a 1 h',read:false},{id:'an3',kind:'done',t:'Tableau de gestion des stocks — Excel',txt:'Nadia Touré a terminé et publié son projet « Tableau de gestion des stocks — Excel »',d:'Hier',read:true}],
  formations: clone(F), st:null, videos: clone(VIDEOS), slots: clone(SLOTS), ardv: clone(RDV_REQ),
  av: [{id:'av-1',student:'Kevin Ahoua',mat:'EVC-26-0104',f:'DG',t:'Logo — Maquis "Chez Tantie Rose"',d:'25/10/2026',files:['logo_final.ai','logo_couleur.png'],st:'soumis'},{id:'av-2',student:'Nadia Touré',mat:'EVC-26-0110',f:'GIA',t:'Tableau de gestion des stocks — Excel',d:'25/10/2026',files:['stocks_v2.xlsx'],st:'soumis'},{id:'av-3',student:'Grace Mensah',mat:'EVC-26-0113',f:'CM',t:'Calendrier éditorial — novembre',d:'24/10/2026',files:['calendrier_nov.pdf'],st:'soumis'},{id:'av-4',student:'Ibrahim Sanogo',mat:'EVC-26-0119',f:'DG',t:'Affiche — Festival Abidjan Urban Arts',d:'24/10/2026',files:['affiche_A2.pdf'],st:'soumis'}]
};
function loadStudent(){ S.st = clone(SEED); S.st.photo = null; S.fsel = 'ALL'; S.ui = {}; S.st.tv = {cur:null, playing:false, speed:1, prog:Object.fromEntries(S.videos.map(v=>[v.id, v.p0]))}; buildPayments(); if (typeof uMergeAssigned==="function") uMergeAssigned(); }
function audit(a, o){ S.audit.unshift({d: new Date().toLocaleString('fr-FR',{day:'2-digit',month:'2-digit',year:'numeric',hour:'2-digit',minute:'2-digit'}).replace(',', ''), u: S.role==='admin' ? 'Didier Koné' : S.role==='form' ? 'Marc Yao' : 'Aya Kouassi', r: S.role==='admin' ? S.adminRole : S.role==='form' ? 'Formateur principal' : 'Étudiant', a, o, ip:'102.67.14.10'}); }

/* ---------- Étudiant : calculs ---------- */
const fl = () => FORDER.filter(f => PROFILES[S.profile].f.includes(f));
const vf = () => S.fsel==='ALL' ? fl() : [S.fsel];
const isCombined = () => fl().length > 1;
/* Progression = séances réalisées / séances du programme */
const chapters = f => S.formations[f].prog.length;
const chaptersDone = f => S.formations[f].prog.filter(x=>x.done).length;
const prog = f => Math.round(chaptersDone(f)/chapters(f)*100);
const hrs = x => x.hl || `${x.h}h00 – ${x.e}h00`;
const modeLbl = m => m==='Virtuel' ? 'En ligne' : 'Présentiel';
const modePill = m => pill(modeLbl(m), m==='Virtuel'?'info':'acc', 1);
/* Paiements : par formation, un acompte à l'inscription puis une tranche à solder à date fixe (voir F[x].payRule) */
function buildPayments(){
  const fs = PROFILES[S.profile].f, dep = fs.reduce((a,f)=>a+F[f].deposit,0), total = fs.reduce((a,f)=>a+F[f].price,0);
  const t1 = Math.round(dep*0.6/5000)*5000, extra = Math.min(40000, total-dep);
  S.st.pay.tx = [{d:'06/10/2026',ref:'REC-2026-1187',mode:'Orange Money',amount:t1,by:'Aya Kouassi'},{d:'06/10/2026',ref:'REC-2026-1188',mode:'Espèces',amount:dep-t1,by:'Aya Kouassi'}];
  if (extra>0) S.st.pay.tx.push({d:'15/10/2026',ref:'REC-2026-1302',mode:'Wave',amount:extra,by:'Jean Kouassi (parent)'});
  S.st.pay.perF = Object.fromEntries(fs.map(f=>[f,F[f].price])); S.st.pay.total = total; S.st.pay.base = total;
}
function payState(){
  const P = S.st.pay, paid = P.tx.reduce((a,t)=>a+t.amount,0), fs = fl(), total = fs.reduce((a,f)=>a+F[f].price,0);
  const acompte = fs.reduce((a,f)=>a+F[f].deposit,0);
  const sched = [];
  fs.forEach(f => { sched.push({f, kind:'dep', label:`Inscription — ${F[f].name}`, due:'06/10/2026', days:-16, amount:F[f].deposit}); sched.push({f, kind:'tr', label:`Solde — ${F[f].name}`, due:F[f].trancheDue, days:F[f].trancheDays, amount:F[f].price-F[f].deposit}); });
  sched.sort((a,b)=>a.days-b.days);
  let cover = paid;
  sched.forEach(s => { const c = Math.min(cover, s.amount); s.paid = c; cover -= c; s.status = c>=s.amount ? 'Payé' : s.days<0 ? 'En retard' : c>0 ? 'Partiel' : 'À venir'; s.penalty = 0; });
  const balance = Math.max(0,total-paid), late = sched.some(s=>s.status==='En retard');
  const perF = fs.map(f => { const tot = F[f].price, p = sched.filter(s=>s.f===f).reduce((a,s)=>a+s.paid,0); return {f, total:tot, acompte:F[f].deposit, paid:p, balance:tot-p, rule:F[f].payRule}; });
  return {paid,total,acompte,balance,sched,late,perF,base:total,remise:0,next:sched.find(s=>s.status!=='Payé'),status: balance===0?'Soldé': late?'En retard':'À jour'};
}
function recoDay(f){ return Math.min(60, -S.st.reco[f].start); }
function certCriteria(f){
  const st = S.st, ps = payState(), todos = st.todos.filter(t=>t.f===f && t.mandatory), val = todos.filter(t=>t.status==='valide').length;
  const ev = st.evals.filter(e=>e.f===f), evDone = ev.filter(e=>e.note!=null), evOk = evDone.every(e=>e.note>=10);
  const solde = { t:'Situation financière', d: ps.balance===0 ? 'Solde réglé' : `Solde restant : ${fcfa(ps.perF.find(x=>x.f===f).balance)}`, s: ps.balance===0 ? 'ok' : ps.late ? 'ko' : 'pending', link:'e-paiements'};
  const travaux = { t:'Travaux obligatoires', d:`${val} / ${todos.length} validés`, s: val===todos.length ? 'ok' : todos.some(t=>t.status==='reprendre') ? 'ko' : 'pending', link:'e-todo'};
  const rep = st.reports[f], rapport = { t:'Rapport de fin de formation', d:`Statut : ${rep.st}`, s: rep.st==='Validé' ? 'ok' : 'pending', link:'e-rapports'};
  const evals = { t:'Évaluations', d:`${evDone.length} / ${ev.length} notées · moyenne ${avgF(f) ?? '—'}/20`, s: evDone.length===ev.length && evOk ? 'ok' : evOk ? 'pending' : 'ko', link:'e-evaluations'};
  if (f==='DG') return [travaux,
    { t:'Studio Creative', d: st.studio.submitted ? 'Projet soumis, en attente du jury' : st.studio.registered ? 'Inscrite, projet non soumis' : 'Non inscrite à l\'édition 7', s: 'pending', link:'e-projets'},
    { t:'Pressbook', d:`${st.pressbook.length} réalisations · ${st.pressbookStatus}`, s: st.pressbookStatus==='Validé' ? 'ok':'pending', link:'e-projets'},
    rapport, { t:'Test final', d:`Prévu le ${fmtD(addDays(40),{day:'numeric',month:'long'})}`, s:'pending', link:'e-evaluations'}, solde];
  const reco = { t:'RECO 60', d:`Jour ${recoDay(f)} / 60`, s: recoDay(f)>=60 ? 'ok':'pending', link:'e-projets'};
  if (f==='CM') return [travaux, evals, reco, rapport, solde];
  return [travaux, evals, reco, solde];
}
function avgF(f){ const e = S.st.evals.filter(x=>x.f===f && x.note!=null); if(!e.length) return null; const s=e.reduce((a,x)=>a+x.note*x.coef,0), c=e.reduce((a,x)=>a+x.coef,0); return (s/c).toFixed(1).replace('.',','); }
function certPct(f){ const c = certCriteria(f); return Math.round(c.filter(x=>x.s==='ok').length/c.length*100); }

/* ---------- SVG : graphiques et QR ---------- */
function svgBars(cats, series, {h=220, money=false}={}){
  const W = Math.max(320, cats.length*90), pad = {l:56,r:12,t:12,b:34}, ih = h-pad.t-pad.b, iw = W-pad.l-pad.r;
  const max = Math.max(1,...series.flatMap(s=>s.values)); const step = niceStep(max); const top = Math.ceil(max/step)*step;
  const gw = iw/cats.length, bw = Math.min(28,(gw-16)/series.length);
  let g = '';
  for (let v=0; v<=top; v+=step){ const y = pad.t+ih-(v/top)*ih; g += `<line class="gl" x1="${pad.l}" x2="${W-pad.r}" y1="${y}" y2="${y}" stroke-dasharray="${v?3:0}"/><text x="${pad.l-8}" y="${y+4}" text-anchor="end">${money?shortMoney(v):nfr(v)}</text>`; }
  cats.forEach((c,i)=>{ const x0 = pad.l+gw*i+(gw-bw*series.length)/2;
    series.forEach((s,j)=>{ const v=s.values[i], bh=(v/top)*ih; g += `<rect x="${x0+j*bw+1}" y="${pad.t+ih-bh}" width="${bw-2}" height="${bh}" rx="3" fill="${s.color}"><title>${esc(s.name)} — ${esc(c)} : ${money?fcfa(v):nfr(v)}</title></rect>`; });
    g += `<text x="${pad.l+gw*i+gw/2}" y="${h-12}" text-anchor="middle">${esc(c)}</text>`; });
  return `<div class="tbl"><svg class="chart-svg" viewBox="0 0 ${W} ${h}" width="100%" style="min-width:${Math.min(W,520)}px" role="img">${g}</svg></div>` + legend(series);
}
function svgLine(cats, series, {h=220}={}){
  const W = 520, pad={l:44,r:26,t:14,b:34}, ih=h-pad.t-pad.b, iw=W-pad.l-pad.r;
  const max = Math.max(1,...series.flatMap(s=>s.values)), step=niceStep(max), top=Math.ceil(max/step)*step;
  const X = i => pad.l + (cats.length===1?iw/2: iw*i/(cats.length-1)), Y = v => pad.t+ih-(v/top)*ih;
  let g='';
  for (let v=0; v<=top; v+=step) g += `<line class="gl" x1="${pad.l}" x2="${W-pad.r}" y1="${Y(v)}" y2="${Y(v)}" stroke-dasharray="${v?3:0}"/><text x="${pad.l-8}" y="${Y(v)+4}" text-anchor="end">${nfr(v)}</text>`;
  cats.forEach((c,i)=> g += `<text x="${X(i)}" y="${h-12}" text-anchor="middle">${esc(c)}</text>`);
  series.forEach(s=>{ const pts = s.values.map((v,i)=>`${X(i)},${Y(v)}`).join(' ');
    if (s.area) g += `<polygon points="${X(0)},${Y(0)} ${pts} ${X(s.values.length-1)},${Y(0)}" fill="${s.color}" opacity=".12"/>`;
    g += `<polyline points="${pts}" fill="none" stroke="${s.color}" stroke-width="2.5" stroke-linejoin="round"/>`;
    s.values.forEach((v,i)=> g += `<circle cx="${X(i)}" cy="${Y(v)}" r="${i===s.values.length-1?5:3}" fill="${i===s.values.length-1?s.color:'var(--surface)'}" stroke="${s.color}" stroke-width="2"><title>${esc(s.name)} — ${esc(cats[i])} : ${nfr(v)}</title></circle>`); });
  return `<div class="tbl"><svg class="chart-svg" viewBox="0 0 ${W} ${h}" width="100%" style="min-width:420px" role="img">${g}</svg></div>` + legend(series);
}
function svgDonut(parts, {size=170, label='', sub=''}={}){
  const tot = parts.reduce((a,p)=>a+p.value,0)||1, r=size/2-14, c=2*Math.PI*r; let off=0, g='';
  parts.forEach(p=>{ const len = p.value/tot*c; g += `<circle r="${r}" cx="${size/2}" cy="${size/2}" fill="none" stroke="${p.color}" stroke-width="22" stroke-dasharray="${len} ${c-len}" stroke-dashoffset="${-off}" transform="rotate(-90 ${size/2} ${size/2})"><title>${esc(p.name)} : ${nfr(p.value)}</title></circle>`; off+=len; });
  return `<div class="d-flex align-items-center gap-4 flex-wrap"><svg width="${size}" height="${size}" viewBox="0 0 ${size} ${size}" role="img" style="max-width:100%">${g}<text x="50%" y="48%" text-anchor="middle" style="font:800 24px var(--f-display);fill:var(--fg)">${label}</text><text x="50%" y="62%" text-anchor="middle" style="font:600 11px var(--f-body);fill:var(--muted)">${sub}</text></svg><div class="d-flex flex-column gap-2" style="min-width:0">${parts.map(p=>`<div class="d-flex align-items-center gap-2 small"><i style="width:10px;height:10px;border-radius:3px;background:${p.color}"></i><span class="muted">${esc(p.name)}</span><b class="ms-auto num ps-3">${nfr(p.value)}</b><span class="muted num" style="width:44px;text-align:right">${Math.round(p.value/tot*100)} %</span></div>`).join('')}</div></div>`;
}
const legend = series => series.length>1 ? `<div class="legend mt-2">${series.map(s=>`<span><i style="background:${s.color}"></i>${esc(s.name)}</span>`).join('')}</div>` : '';
function niceStep(max){ const raw = max/4, mag = Math.pow(10, Math.floor(Math.log10(raw))), n = raw/mag; return (n<=1?1:n<=2?2:n<=5?5:10)*mag; }
const shortMoney = v => v>=1e6 ? (v/1e6).toFixed(v%1e6?1:0).replace('.',',')+' M' : v>=1e3 ? Math.round(v/1e3)+' k' : String(v);
function qrSVG(text, size=164){
  const n = 29, r = rng([...text].reduce((a,c)=>a*31+c.charCodeAt(0)|0, 7)), cell = size/n; let rects='';
  const finder = (x,y) => { for(let i=0;i<7;i++) for(let j=0;j<7;j++){ const on = i===0||i===6||j===0||j===6||(i>=2&&i<=4&&j>=2&&j<=4); if(on) rects += `<rect x="${(x+i)*cell}" y="${(y+j)*cell}" width="${cell}" height="${cell}"/>`; } };
  const inF = (i,j) => (i<8&&j<8)||(i>=n-8&&j<8)||(i<8&&j>=n-8);
  for(let i=0;i<n;i++) for(let j=0;j<n;j++){ if(inF(i,j)) continue; if(r()>0.52) rects += `<rect x="${i*cell}" y="${j*cell}" width="${cell+.3}" height="${cell+.3}"/>`; }
  finder(0,0); finder(n-7,0); finder(0,n-7);
  return `<svg class="qr" width="${size+16}" height="${size+16}" viewBox="-8 -8 ${size+16} ${size+16}" role="img" aria-label="QR code ${esc(text)}"><rect x="-8" y="-8" width="${size+16}" height="${size+16}" fill="#fff"/><g fill="#14182B">${rects}</g></svg>`;
}

/* ---------- Modale, toast ---------- */
let MODAL;
function openModal({title, body, foot='', size='', onShow}){
  const m = $('#modal'); m.querySelector('.modal-dialog').className = `modal-dialog modal-dialog-centered modal-dialog-scrollable ${size}`;
  m.querySelector('.modal-title').innerHTML = title; m.querySelector('.modal-body').innerHTML = body;
  const f = m.querySelector('.modal-footer'); f.innerHTML = foot; f.hidden = !foot;
  MODAL = MODAL || new bootstrap.Modal(m); MODAL.show(); if (onShow) setTimeout(onShow, 50);
}
const closeModal = () => { if (!MODAL) return; MODAL.hide(); setTimeout(()=>{ if ($('#modal').classList.contains('show')) MODAL.hide(); }, 400); };
function toast(msg, kind='ok'){
  const icon = {ok:'fa-circle-check',warn:'fa-triangle-exclamation',bad:'fa-circle-xmark',info:'fa-circle-info'}[kind];
  const el = document.createElement('div'); el.className='toast align-items-center border-0'; el.setAttribute('role','status');
  el.innerHTML = `<div class="d-flex"><div class="toast-body d-flex gap-2 align-items-center"><i class="fa-solid ${icon}" style="color:var(--accent)"></i>${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button></div>`;
  $('#toasts').appendChild(el); const t = new bootstrap.Toast(el,{delay:3200}); t.show(); el.addEventListener('hidden.bs.toast',()=>el.remove());
}
function confirmBox(title, text, actName, data='', danger=false){
  openModal({title, body:`<p class="mb-0">${text}</p>`, foot:`<button class="btn btn-ghost" data-bs-dismiss="modal">Annuler</button><button class="btn ${danger?'btn-danger':'btn-primary'}" data-act="${actName}" ${data}>Confirmer</button>`});
}
function showCSV(title, rows){
  const csv = rows.map(r=>r.map(c=>`"${String(c).replace(/"/g,'""')}"`).join(';')).join('\n');
  openModal({title, size:'modal-lg', body:`<p class="muted small">Export CSV (séparateur point-virgule, compatible Excel). Copiez le contenu ou utilisez « Télécharger » dans la version installée.</p><textarea id="csv-out" class="form-control mono" rows="12" readonly>${esc(csv)}</textarea>`,
    foot:`<button class="btn btn-ghost" data-act="dl-csv">Télécharger</button><button class="btn btn-primary" data-act="copy" data-target="#csv-out"><i class="fa-solid fa-copy me-1"></i>Copier</button>`});
}

/* ---------- Routeur ---------- */
const VIEWS = {}, ACT = {}, INP = {};
function view(id, space, label, icon, render, after){ VIEWS[id] = {id, space, label, icon, render, after}; }
function go(id){ if (location.hash.slice(1)===id) route(); else location.hash = id; }
function homeOf(role){ return role==='etu' ? 'e-dashboard' : role==='form' ? 't-dashboard' : role==='admin' ? firstAdmin() : 'login'; }
function firstAdmin(){ return ALL_A.find(a => S.perms[S.adminRole].includes(a)) || 'a-dashboard'; }
function route(){
  let id = location.hash.slice(1); if (!VIEWS[id]) id = S.role ? homeOf(S.role) : 'login';
  const v = VIEWS[id];
  if (v.space !== 'public') { if (!S.role || S.role !== v.space){ if (!S.role && v.space==='etu') loadStudent(); S.role = v.space; if (S.role==='etu' && !S.st) loadStudent(); } }
  if (v.space==='admin' && !S.perms[S.adminRole].includes(id)) { render(VIEWS['denied']); return; }
  render(v);
}
function render(v){
  $$('.offcanvas-backdrop').forEach(b=>b.remove()); document.body.style.overflow=''; document.body.classList.remove('modal-open');
  const fresh = !S.keepScroll || S.view !== v.id, sideTop = S.lastSpace===v.space ? ($('.side-inner')?.scrollTop || 0) : 0; S.lastSpace = v.space;
  if (typeof tourEnd==='function') tourEnd(true); S.view = v.id; const html = v.render();
  $('#app').innerHTML = v.space==='public' ? html : shell(v, html);
  const si = $('.side-inner'); if (si) { si.scrollTop = sideTop; const a = si.querySelector('a.active'); if (a && fresh) { const r = a.getBoundingClientRect(), sr = si.getBoundingClientRect(); if (r.top < sr.top || r.bottom > sr.bottom) a.scrollIntoView({block:'nearest'}); } }
  const main = $('#view'); if (main && fresh) { main.classList.add('anim'); countUp(main); setTimeout(()=>main.classList.remove('anim'), 1200); }
  if (v.space!=='public') { sideHue(v); sideInd(); }
  v.after && v.after(); if (v.space==='etu' && typeof commPopup==='function') commPopup(); if (typeof liveTrack==='function') liveTrack(v); if (!S.keepScroll) window.scrollTo({top:0, behavior:'instant'}); S.keepScroll = false;
  onScrollTop();
  document.title = (v.space==='public' ? '' : v.label + ' · ') + 'MY EVC';
}
/* Compteurs animés sur les indicateurs (KPI) */
function countUp(root){
  if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  $$('.kpi .k-v, .big-pct, .stat .s-v, .paybox .big', root).forEach(el => {
    const w = document.createTreeWalker(el, NodeFilter.SHOW_TEXT); let n;
    while ((n = w.nextNode())) { const m = n.nodeValue.match(/^(\D*?)(\d{1,3}(?:[  ]\d{3})*|\d+)(?![,.]\d)(.*)$/s); if (!m) continue;
      const target = parseInt(m[2].replace(/\D/g,''),10); if (!target) break; const node = n, t0 = performance.now(), dur = 900;
      const step = t => { const p = Math.min(1,(t-t0)/dur), e = 1-Math.pow(1-p,3); node.nodeValue = m[1] + nfr(Math.round(target*e)) + m[3]; if (p<1) requestAnimationFrame(step); };
      node.nodeValue = m[1] + '0' + m[3]; requestAnimationFrame(step); break; }
  });
}
function onScrollTop(){ const t = $('.topbar'); if (t) t.classList.toggle('scrolled', window.scrollY > 4); }
window.addEventListener('scroll', onScrollTop, {passive:true});
function rerender(){ S.keepScroll = true; route(); }

/* ---------- Gabarit ---------- */
const MENUS = {
  etu:[['',['e-dashboard','e-formations','e-webtv','e-seances','e-creneaux','e-todo','e-paiements']],['Plus',['e-messagerie','e-projets','e-evaluations','e-certification','e-bibliotheque','e-rapports','e-cv','e-notifications','e-assistance'],true]],
  form:[['',['t-dashboard','t-planning','t-seances','t-corrections','t-etudiants','t-messagerie']],['Plus',['t-ressources','t-evaluations','t-rapports','t-notifications'],true]],
  admin:[['',['a-dashboard','a-live','a-etudiants','a-preinscriptions','a-travaux','a-communication','a-finances']],['Scolarité',['a-formations','a-sessions','a-creneaux','a-formateurs','a-seances'],true],['Programmes',['a-webtv','a-studio','a-reco','a-certifications'],true],['Gestion',['a-factures','a-documents','a-stages','a-support'],true],['Analyse et système',['a-rapports','a-roles','a-audit','a-parametres'],true]]
};
/* Travaux déposés par les étudiants, en attente de validation par l'administration */
function toValidate(){ const aya = S.st ? S.st.todos.filter(t=>t.status==='soumis').map(t=>({id:'aya-'+t.id, todo:t.id, student:`${S.st.student.first} ${S.st.student.last}`, mat:S.st.student.matricule, f:t.f, t:t.t, d:"Aujourd'hui", files:t.files.slice(-2), st:'soumis'})) : []; return [...aya, ...S.av.filter(x=>x.st==='soumis')]; }
function menuCount(id){
  if (S.role==='form') { if (id==='t-corrections') return tQueue().filter(x=>x.st!=='done').length; if (id==='t-notifications') return tNotifs().filter(n=>!n.read).length; return 0; }
  if (S.role!=='etu' || !S.st) { if (id==='a-preinscriptions') return S.preins.filter(c=>['recue','test_ok'].includes(c.stage)).length; if (id==='a-support') return S.tickets.filter(t=>t.st==='Ouvert').length; if (id==='a-travaux') return toValidate().length; if (id==='a-creneaux') return S.ardv.filter(r=>r.st==='En attente').length + (S.st?S.st.rdv.filter(r=>r.st==='En attente').length:0); return 0; }
  if (id==='e-todo') return S.st.todos.filter(t=>fl().includes(t.f) && ['afaire','encours','reprendre'].includes(t.status)).length;
  if (id==='e-notifications') return S.st.notifs.filter(n=>!n.read).length;
  return 0;
}
function shell(v, html){
  const sp = S.role, groups = MENUS[sp]; S.navOpen = S.navOpen || {};
  const link = id => { const x = VIEWS[id], c = menuCount(id); return `<li><a href="#${id}" data-tip="${esc(x.label)}" class="${id===v.id?'active':''}" ${id===v.id?'aria-current="page"':''}><i class="fa-solid ${x.icon}"></i><span>${x.label}</span>${c?`<span class="count">${c}</span>`:''}</a></li>`; };
  const items = groups.map(([label, ids, fold]) => { const vis = ids.filter(id => sp!=='admin' || S.perms[S.adminRole].includes(id)); if(!vis.length) return '';
    if (!fold) return vis.map(link).join('');
    const open = S.navCollapsed || vis.includes(v.id) || S.navOpen[sp+label], n = vis.reduce((a,id)=>a+menuCount(id),0);
    return `<li class="menu-fold"><details ${open?'open':''} data-fold="${sp+label}"><summary class="menu-label"><span>${label}</span>${n&&!open?`<span class="count">${n}</span>`:''}<i class="fa-solid fa-chevron-down chev"></i></summary><ul class="menu sub">${vis.map(link).join('')}</ul></details></li>`; }).join('');
  const spaceName = sp==='etu' ? 'Espace étudiant' : sp==='form' ? 'Espace formateur' : 'Back-office';
  const unread = sp==='etu' ? S.st.notifs.filter(n=>!n.read).length : sp==='admin' ? S.anotifs.filter(n=>!n.read).length : tNotifs().filter(n=>!n.read).length;
  const who = sp==='etu' ? `${S.st.student.first} ${S.st.student.last}` : sp==='form' ? 'Marc Yao' : 'Didier Koné';
  const roleLine = sp==='etu' ? `${S.st.student.matricule} · ${fl().join(' + ')}` : sp==='form' ? 'Formateur principal DG' : esc(S.adminRole);
  const fsel = sp==='etu' && isCombined() && !['e-profil','e-parametres','e-assistance','e-messagerie','e-cv','e-paiements'].includes(v.id) ? `<div class="fsel d-none d-md-flex" role="group" aria-label="Sélecteur de formation"><button class="${S.fsel==='ALL'?'on':''}" data-act="fsel" data-f="ALL">Toutes</button>${fl().map(f=>`<button class="${S.fsel===f?'on':''}" data-act="fsel" data-f="${f}"><span class="sw" style="background:var(--${f.toLowerCase()})"></span>${f}</button>`).join('')}</div>` : '';
  const themeLbl = {auto:'automatique',dark:'sombre',light:'clair'}[S.theme];
  const grp = ((groups.find(g=>g[1].includes(v.id))||[''])[0] || '').replace(/^Plus$/,'');
  return `<div class="app ${S.navCollapsed?'collapsed':''}">
  <aside class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" aria-label="Menu principal" style="--aoff:${(-performance.now()/1000).toFixed(2)}"><div class="side-bg" aria-hidden="true"><i></i><i></i><i></i>${motif('side-motif')}</div><div class="side-inner">
    <a class="brand" href="#${homeOf(sp)}" aria-label="MY EVC — ${spaceName}">${logoH('negatif','brand-full')}${logoSym('negatif','brand-sym')}<button type="button" class="btn-close ms-auto d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Fermer"></button></a>
    <div class="space-tag"><span class="sp-dot"></span><span>${spaceName}</span></div>
    <ul class="menu"><li class="nav-ind" aria-hidden="true"></li>${items}</ul>
    <div class="side-foot"><button class="demo-badge border-0" data-act="demo" title="Changer d'espace, de profil ou de rôle">Mode démo</button><button class="collapse-btn d-none d-lg-grid" data-act="nav-collapse" aria-label="${S.navCollapsed?'Déplier':'Replier'} le menu" title="${S.navCollapsed?'Déplier':'Replier'} le menu"><i class="fa-solid fa-angles-left" style="font-size:.75rem"></i></button></div>
  </div></aside>
  <div class="main">
    <header class="topbar">
      <button class="btn btn-ghost btn-icon d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-label="Ouvrir le menu"><i class="fa-solid fa-bars"></i></button>
      <div class="crumb"><small>${spaceName}${grp?`<i class="fa-solid fa-chevron-right" style="font-size:.5rem;opacity:.6"></i>${esc(grp)}`:''}</small><h1>${v.label}</h1></div>
      <div class="top-actions">
        ${fsel}
        <button class="search-trigger d-none d-xl-flex" data-act="cmdk" aria-label="Rechercher (Ctrl+K)"><i class="fa-solid fa-magnifying-glass"></i><span>Rechercher</span><kbd>Ctrl K</kbd></button>
        <button class="btn btn-ghost btn-icon d-xl-none" data-act="cmdk" aria-label="Rechercher"><i class="fa-solid fa-magnifying-glass"></i></button>
        <button class="btn btn-ghost btn-icon bell" data-act="bell" aria-label="Notifications"><i class="fa-regular fa-bell"></i>${unread?'<span class="dot"></span>':''}</button>
        <div class="dropdown">
          <button class="btn p-0 border-0 user-btn ms-1" data-bs-toggle="dropdown" aria-label="Mon compte">${avatar(who,'',sp==='etu'?S.st.photo:null)}</button>
          <div class="dropdown-menu dropdown-menu-end" style="min-width:250px">
            <div class="px-3 py-2"><b>${esc(who)}</b><div class="small muted">${roleLine}</div></div><div class="dropdown-divider"></div>
            ${sp==='etu'?`<a class="dropdown-item" href="#e-profil"><i class="fa-regular fa-user me-2"></i>Mon profil</a><a class="dropdown-item" href="#e-parametres"><i class="fa-solid fa-gear me-2"></i>Paramètres</a>`:''}
            ${sp==='form'?`<a class="dropdown-item" href="#t-profil"><i class="fa-regular fa-user me-2"></i>Mon profil et disponibilités</a>`:''}
            ${sp==='admin'&&S.perms[S.adminRole].includes('a-parametres')?`<a class="dropdown-item" href="#a-parametres"><i class="fa-solid fa-gear me-2"></i>Paramètres</a>`:''}
            <button class="dropdown-item" data-act="theme"><i class="fa-solid fa-circle-half-stroke me-2"></i>Thème : ${themeLbl}</button>
            <button class="dropdown-item" data-act="demo"><i class="fa-solid fa-sliders me-2"></i>Mode démo</button>
            <div class="dropdown-divider"></div>
            <button class="dropdown-item" data-act="logout"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Se déconnecter</button>
          </div>
        </div>
      </div>
    </header>
    ${fsel ? `<div class="d-md-none px-3 pt-2" style="padding-left:max(16px,env(safe-area-inset-left,0px));padding-right:max(16px,env(safe-area-inset-right,0px))">${fsel.replace('d-none d-md-flex','d-flex')}</div>` : ''}
    <main class="content" id="view">${html}</main>
  </div>
  ${bottomNav(sp, v.id)}</div>`;
}
/* ---------- Barre latérale : teinte dynamique et indicateur glissant ----------
   La teinte (--sh, propriété CSS animée) suit l'espace, la formation sélectionnée et dérive légèrement d'un écran à l'autre. */
/* [teinte du halo, réservé] — la barre reste indigo (charte) ; seuls les halos prennent la teinte de l'espace ou de la formation */
const SIDE_HUE = {DG:[350,0], CM:[238,0], GIA:[165,0], etu:[285,0], form:[305,0], admin:[262,0]};
function sideHue(v){
  const sp = S.role; let [h, o] = SIDE_HUE[sp] || SIDE_HUE.etu;
  if (sp==='etu' && S.st) { const fs = vf(); if (fs.length===1) [h, o] = SIDE_HUE[fs[0]]; }
  document.documentElement.style.setProperty('--sho', o);
  const ids = (MENUS[sp]||[]).flatMap(g=>g[1]), i = ids.indexOf(v.id);
  if (i>=0) h += (i % 6) * 4 - 10;
  const cur = S.sh ?? h; h = h + 360*Math.round((cur-h)/360); S.sh = h;
  document.documentElement.style.setProperty('--sh', h.toFixed(1));
}
function sideInd(){
  const m = $('.side-inner > .menu'), ind = m && m.querySelector(':scope > .nav-ind'); if (!ind) return;
  const a = m.querySelector('a.active'); if (!a || !a.offsetHeight) { ind.style.opacity = 0; m.classList.remove('ind-ready'); return; }
  let y = 0, el = a; while (el && el !== m) { y += el.offsetTop; el = el.offsetParent; } if (!el) y = a.offsetTop;
  const h = a.offsetHeight, p = S.ind, same = p && p.sp===S.role && p.c===S.navCollapsed && !matchMedia('(prefers-reduced-motion: reduce)').matches;
  const place = (yy, hh) => { ind.style.transform = `translate3d(0,${yy}px,0)`; ind.style.height = hh+'px'; };
  ind.style.transition = 'none'; place(same ? p.y : y, same ? p.h : h); ind.style.opacity = 1; void ind.offsetHeight; ind.style.transition = '';
  m.classList.add('ind-ready'); if (same && (p.y!==y || p.h!==h)) requestAnimationFrame(() => place(y, h));
  S.ind = {y, h, sp:S.role, c:S.navCollapsed};
}
document.addEventListener('shown.bs.offcanvas', () => { S.ind = null; sideInd(); });
window.addEventListener('resize', () => { if (S.ind) { S.ind = null; sideInd(); } }, {passive:true});
document.addEventListener('pointermove', e => { const sb = e.target.closest && e.target.closest('.sidebar'); if (!sb) return; const r = sb.getBoundingClientRect(); sb.style.setProperty('--mx', (e.clientX-r.left)+'px'); sb.style.setProperty('--my', (e.clientY-r.top)+'px'); }, {passive:true});

/* Mode démo : changer d'espace, de profil étudiant ou de rôle, dans une fenêtre dédiée */
ACT.demo = () => { const sp = S.role; const b = (act, attr, on, html) => `<button class="demo-opt ${on?'on':''}" data-act="${act}" ${attr}>${html}</button>`;
  openModal({title:'Mode démo', body:`<p class="muted small mb-3">Changez de point de vue pour parcourir la maquette.</p>
  <div class="small-caps mb-2">Espace</div><div class="demo-grid mb-3">${b('space','data-s="etu"',sp==='etu','<i class="fa-solid fa-user-graduate"></i>Étudiant')}${b('space','data-s="form"',sp==='form','<i class="fa-solid fa-chalkboard-user"></i>Formateur')}${b('space','data-s="admin"',sp==='admin','<i class="fa-solid fa-shield-halved"></i>Administration')}</div>
  <div class="small-caps mb-2">Profil étudiant</div><div class="demo-grid mb-3">${PROFILE_KEYS.map(p=>b('profile',`data-p="${p}"`,S.profile===p&&sp==='etu',PROFILES[p].f.map(fchip).join(''))).join('')}</div>
  <div class="small-caps mb-2">Rôle administratif</div><select class="form-select" data-chg="demo-role" aria-label="Rôle administratif">${ADMIN_ROLES.map(r=>`<option ${S.adminRole===r?'selected':''}>${esc(r)}</option>`).join('')}</select>`}); };
INP['demo-role'] = el => ACT.arole({dataset:{r:el.value}});
document.addEventListener('toggle', e => { const d = e.target; if (d.matches?.('details[data-fold]')) { S.navOpen = S.navOpen || {}; S.navOpen[d.dataset.fold] = d.open; sideInd(); } }, true);
/* Barre de navigation mobile : 4 raccourcis + « Menu » */
function bottomNav(sp, cur){
  const pick = sp==='etu' ? ['e-dashboard','e-webtv','e-todo','e-seances'] : sp==='form' ? ['t-dashboard','t-seances','t-corrections','t-messagerie']
    : ['a-dashboard','a-live','a-travaux','a-communication','a-preinscriptions','a-etudiants','a-finances'].filter(id => S.perms[S.adminRole].includes(id)).slice(0,4);
  const short = {'e-dashboard':'Accueil','t-dashboard':'Accueil','a-dashboard':'Accueil','e-seances':'Séances','t-seances':'Séances','a-seances':'Séances','e-todo':'To-Do','e-notifications':'Alertes','e-webtv':'Web TV','a-travaux':'Validations','a-live':'Direct','a-communication':'Communiqués','t-corrections':'Corrections','t-messagerie':'Messages','a-etudiants':'Étudiants','a-preinscriptions':'Candidats','a-finances':'Finances','a-support':'Support','a-documents':'Documents'};
  return `<nav class="bottom-nav" aria-label="Navigation rapide">${pick.map(id => { const x = VIEWS[id], c = menuCount(id); return `<a href="#${id}" class="${id===cur?'on':''}" ${id===cur?'aria-current="page"':''}><i class="fa-solid ${x.icon}"></i><span class="lbl">${short[id]||x.label}</span>${c?`<span class="bn-count">${c}</span>`:''}</a>`; }).join('')}<button data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-label="Tout le menu"><i class="fa-solid fa-grip"></i><span class="lbl">Menu</span></button></nav>`;
}

/* ---------- Recherche rapide (Ctrl+K) ---------- */
const norm = s => String(s).normalize('NFD').replace(/[̀-ͯ]/g,'').toLowerCase();
function cmdkItems(){
  const sp = S.role, out = [];
  if (sp) MENUS[sp].forEach(([g, ids]) => ids.filter(id => sp!=='admin' || S.perms[S.adminRole].includes(id)).forEach(id => out.push({sec:'Écrans', label:VIEWS[id].label, hint:g, icon:VIEWS[id].icon, run:()=>go(id)})));
  if (sp==='form') out.push({sec:'Écrans', label:VIEWS['t-profil'].label, hint:'Compte', icon:VIEWS['t-profil'].icon, run:()=>go('t-profil')});
  if (sp==='etu') [['e-profil','Compte'],['e-parametres','Compte']].forEach(([id,g]) => out.push({sec:'Écrans', label:VIEWS[id].label, hint:g, icon:VIEWS[id].icon, run:()=>go(id)}));
  [['etu','Espace étudiant','fa-user-graduate'],['form','Espace formateur','fa-chalkboard-user'],['admin','Back-office','fa-shield-halved']].filter(x=>x[0]!==sp).forEach(([s,l,i]) => out.push({sec:'Actions', label:`Aller à : ${l}`, hint:'Démo', icon:i, run:()=>ACT.space({dataset:{s}})}));
  out.push({sec:'Actions', label:'Changer de thème (auto / sombre / clair)', hint:'Affichage', icon:'fa-circle-half-stroke', run:()=>ACT.theme()});
  if (sp) out.push({sec:'Actions', label:S.navCollapsed?'Déplier le menu latéral':'Replier le menu latéral', hint:'Affichage', icon:'fa-table-columns', run:()=>ACT['nav-collapse']()});
  out.push({sec:'Pages publiques', label:'Faire une préinscription', hint:'Admission', icon:'fa-user-plus', run:()=>go('preinscription')});
  out.push({sec:'Pages publiques', label:"Vérifier l'authenticité d'un certificat", hint:'Public', icon:'fa-qrcode', run:()=>go('verifier')});
  if (sp) out.push({sec:'Actions', label:'Se déconnecter', hint:'Compte', icon:'fa-arrow-right-from-bracket', run:()=>ACT.logout()});
  return out.filter(x => x.run);
}
function cmdkOpen(){
  let el = $('#cmdk');
  if (!el) { el = document.createElement('div'); el.id = 'cmdk'; el.className = 'cmdk'; el.setAttribute('role','dialog'); el.setAttribute('aria-modal','true'); el.setAttribute('aria-label','Recherche rapide');
    el.innerHTML = `<div class="cmdk-box"><div class="cmdk-in"><i class="fa-solid fa-magnifying-glass"></i><input id="cmdk-q" placeholder="Rechercher un écran ou une action…" autocomplete="off" aria-controls="cmdk-list"><span class="kbd">Échap</span></div><div class="cmdk-list" id="cmdk-list" role="listbox"></div><div class="cmdk-foot"><span><span class="kbd">↑</span> <span class="kbd">↓</span> naviguer</span><span><span class="kbd">Entrée</span> ouvrir</span><span class="ms-auto"><span class="kbd">Ctrl K</span> à tout moment</span></div></div>`;
    document.body.appendChild(el);
    el.addEventListener('mousedown', e => { if (e.target === el) cmdkClose(); });
    const q = $('#cmdk-q', el);
    q.addEventListener('input', () => { CMDK.sel = 0; cmdkDraw(); });
    q.addEventListener('keydown', e => { const n = CMDK.list.length;
      if (e.key==='ArrowDown'){ e.preventDefault(); CMDK.sel = (CMDK.sel+1)%Math.max(1,n); cmdkDraw(true); }
      else if (e.key==='ArrowUp'){ e.preventDefault(); CMDK.sel = (CMDK.sel-1+n)%Math.max(1,n); cmdkDraw(true); }
      else if (e.key==='Enter'){ e.preventDefault(); const it = CMDK.list[CMDK.sel]; if (it){ cmdkClose(); it.run(); } }
      else if (e.key==='Escape'){ e.preventDefault(); cmdkClose(); } });
    $('#cmdk-list', el).addEventListener('click', e => { const b = e.target.closest('[data-i]'); if (b){ const it = CMDK.list[+b.dataset.i]; cmdkClose(); it.run(); } });
    $('#cmdk-list', el).addEventListener('mousemove', e => { const b = e.target.closest('[data-i]'); if (b && +b.dataset.i !== CMDK.sel){ CMDK.sel = +b.dataset.i; $$('.cmdk-item', el).forEach(x=>x.classList.toggle('sel', +x.dataset.i===CMDK.sel)); } });
  }
  CMDK.all = cmdkItems(); CMDK.sel = 0; CMDK.prev = document.activeElement;
  el.classList.add('open'); const q = $('#cmdk-q'); q.value = ''; cmdkDraw(); setTimeout(()=>q.focus(), 10);
}
const CMDK = {all:[], list:[], sel:0};
function cmdkDraw(keepScroll){
  const q = norm($('#cmdk-q').value.trim()), words = q.split(/\s+/).filter(Boolean);
  CMDK.list = CMDK.all.filter(x => words.every(w => norm(x.label+' '+x.hint).includes(w)));
  let html = '', sec = '';
  CMDK.list.forEach((x,i) => { if (x.sec!==sec){ sec = x.sec; html += `<div class="cmdk-sec">${esc(sec)}</div>`; }
    html += `<button class="cmdk-item ${i===CMDK.sel?'sel':''}" data-i="${i}" role="option" aria-selected="${i===CMDK.sel}"><i class="fa-solid ${x.icon} ic"></i><span>${esc(x.label)}</span><small>${esc(x.hint)}</small></button>`; });
  const list = $('#cmdk-list'); list.innerHTML = html || `<div class="cmdk-empty"><i class="fa-regular fa-face-meh-blank fa-lg d-block mb-2"></i>Aucun résultat pour « ${esc($('#cmdk-q').value)} »</div>`;
  if (keepScroll) list.querySelector('.sel')?.scrollIntoView({block:'nearest'});
}
function cmdkClose(){ const el = $('#cmdk'); if (el) el.classList.remove('open'); CMDK.prev?.focus?.(); }
document.addEventListener('keydown', e => {
  const open = $('#cmdk')?.classList.contains('open');
  if ((e.ctrlKey||e.metaKey) && e.key.toLowerCase()==='k') { e.preventDefault(); open ? cmdkClose() : cmdkOpen(); return; }
  if (!open && e.key==='/' && !e.target.closest('input,textarea,select,[contenteditable]') && !document.body.classList.contains('modal-open')) { e.preventDefault(); cmdkOpen(); }
});

/* ---------- Événements délégués ---------- */
document.addEventListener('click', e => {
  const a = e.target.closest('[data-act]'); if (a && ACT[a.dataset.act]) { e.preventDefault(); ACT[a.dataset.act](a, e); return; }
  const g = e.target.closest('[data-go]'); if (g) { e.preventDefault(); go(g.dataset.go); }
});
document.addEventListener('input', e => { const t = e.target.closest('[data-inp]'); if (t && INP[t.dataset.inp]) INP[t.dataset.inp](t, e); });
document.addEventListener('change', e => { const t = e.target.closest('[data-chg]'); if (t && INP[t.dataset.chg]) INP[t.dataset.chg](t, e); });
document.addEventListener('submit', e => { const f = e.target.closest('form[data-submit]'); if (f) { e.preventDefault(); if (!f.checkValidity()) { f.classList.add('was-validated'); return; } ACT[f.dataset.submit] && ACT[f.dataset.submit](f, e); } });
window.addEventListener('hashchange', route);

Object.assign(ACT, {
  fsel: el => { S.fsel = el.dataset.f; rerender(); },
  space: el => { closeModal(); const s = el.dataset.s; if (s==='etu' && !S.st) loadStudent(); S.role = s; go(homeOf(s)); },
  profile: el => { closeModal(); S.profile = el.dataset.p; loadStudent(); S.role='etu'; toast(`Profil ${PROFILES[S.profile].label} chargé. Un seul compte, ${fl().length} formation${fl().length>1?'s':''}.`,'info'); go('e-dashboard'); },
  arole: el => { closeModal(); S.adminRole = el.dataset.r; S.role='admin'; toast(`Connecté en tant que ${S.adminRole}. Le menu suit ses permissions.`,'info'); go(firstAdmin()); },
  theme: () => { S.theme = S.theme==='auto' ? 'dark' : S.theme==='dark' ? 'light' : 'auto'; applyTheme(); rerender(); toast('Thème : ' + ({auto:'automatique',dark:'sombre',light:'clair'})[S.theme],'info'); },
  bell: () => S.role==='admin' ? ACT.anotifs() : go(S.role==='etu' ? 'e-notifications' : 't-notifications'),
  logout: () => { S.role = null; go('login'); },
  copy: el => { const t = $(el.dataset.target); const v = t.value ?? t.textContent; const fb = () => { if (t.select) { t.select(); } toast('Texte sélectionné : utilisez Ctrl+C pour copier.','info'); };
    try { navigator.clipboard.writeText(v).then(()=>toast('Copié dans le presse-papiers.'), fb); } catch(_) { fb(); } },
  'dl-csv': () => { try { const b = new Blob(['﻿'+$('#csv-out').value],{type:'text/csv'}); const a = document.createElement('a'); a.href = URL.createObjectURL(b); a.download = 'export-myevc.csv'; a.click(); toast('Téléchargement lancé (désactivé dans l\'aperçu en ligne).','info'); } catch(_){ toast('Téléchargement indisponible ici : utilisez Copier.','warn'); } },
  'fake-dl': el => toast(`Téléchargement simulé : <b>${esc(el.dataset.n||'document')}</b>`,'info'),
  'close-modal': () => closeModal(),
  cmdk: () => cmdkOpen(),
  'nav-collapse': () => { S.navCollapsed = !S.navCollapsed; try { localStorage.setItem('myevc-nav', S.navCollapsed?'1':'0'); } catch(_){} rerender(); }
});
function applyTheme(){ const r = document.documentElement; if (S.theme==='auto') r.removeAttribute('data-theme'); else r.setAttribute('data-theme', S.theme); try { localStorage.setItem('myevc-theme', S.theme); } catch(_){} }
function fileNames(input){ return [...(input?.files||[])].map(f=>f.name); }

/* ---------- Pages publiques ---------- */
view('login','public','Connexion','fa-key', () => `
<div class="login-wrap">
  <div class="login-art">
    ${motif('login-motif')}
    <a class="login-logo" href="#login">${logoH('negatif')}</a>
    <div style="max-width:540px;position:relative">
      <p class="small-caps kicker">Un étudiant, un compte unique</p>
      <h1>${BRAND.slogan}</h1>
      <p class="mt-3" style="max-width:440px">Formations, séances, travaux, évaluations, paiements et certificats : tout votre parcours EVC au même endroit.</p>
    </div>
    <div class="ftiles">${FORDER.map(f=>`<div class="ft"><i class="fa-solid ${F[f].icon}" style="background:var(--${f.toLowerCase()})"></i>${F[f].name}</div>`).join('')}</div>
  </div>
  <div class="login-form"><div class="box">
    <div class="d-flex justify-content-between align-items-center mb-4"><span class="demo-badge">Maquette interactive</span><button class="btn btn-ghost btn-sm" data-act="theme"><i class="fa-solid fa-circle-half-stroke me-1"></i>Thème</button></div>
    <div class="login-mob-logo">${logoH('couleur')}</div><h2 class="h3 mb-1">Connexion à votre espace</h2><p class="muted mb-4">Étudiant, formateur ou administration. Les identifiants de démonstration sont préremplis.</p>
    <form data-submit="login" novalidate class="d-flex flex-column gap-3">
      <div class="role-pick">
        <input type="radio" name="role" id="r-etu" value="etu" checked><label for="r-etu"><i class="fa-solid fa-user-graduate"></i>Étudiant</label>
        <input type="radio" name="role" id="r-form" value="form"><label for="r-form"><i class="fa-solid fa-chalkboard-user"></i>Formateur</label>
        <input type="radio" name="role" id="r-admin" value="admin"><label for="r-admin"><i class="fa-solid fa-shield-halved"></i>Administration</label>
      </div>
      <div id="login-profile"><label class="form-label">Profil étudiant à simuler</label><div class="profile-pick">${PROFILE_KEYS.map((p,i)=>`<input type="radio" name="profile" id="lp-${i}" value="${p}" ${p===S.profile?'checked':''}><label for="lp-${i}">${PROFILES[p].f.map(fchip).join('')}</label>`).join('')}</div></div>
      <div id="login-arole" hidden><label class="form-label" for="l-arole">Rôle administratif</label><select class="form-select" id="l-arole">${ADMIN_ROLES.map(r=>`<option ${r===S.adminRole?'selected':''}>${esc(r)}</option>`).join('')}</select></div>
      <div><label class="form-label" for="l-email">Email ou matricule</label><input class="form-control" id="l-email" value="EVC-26-0142" required></div>
      <div><label class="form-label" for="l-pass">Mot de passe</label><input class="form-control" type="password" id="l-pass" value="demo-evc-2026" required><div class="invalid-feedback">Saisissez votre mot de passe.</div></div>
      <div id="login-mfa" hidden><label class="form-label" for="l-otp">Code de vérification (MFA)</label><input class="form-control mono" id="l-otp" inputmode="numeric" maxlength="6" value="482913"><div class="form-text muted">Compte sensible : code à 6 chiffres envoyé par l'application d'authentification.</div></div>
      <div class="d-flex justify-content-between small"><label class="d-flex gap-2"><input class="form-check-input" type="checkbox" checked id="l-rem">Rester connecté</label><a href="#login" data-act="forgot">Mot de passe oublié ?</a></div>
      <button class="btn btn-primary btn-lg" type="submit">Se connecter</button>
    </form>
    <hr class="my-4"><div class="d-flex flex-column gap-2 small">
      <a href="#preinscription" class="d-flex align-items-center gap-2"><i class="fa-solid fa-user-plus"></i>Pas encore inscrit ? Faire une préinscription</a>
      <a href="#candidature" class="d-flex align-items-center gap-2"><i class="fa-solid fa-route"></i>Suivre ma candidature (test, paiement, activation)</a>
      <a href="#verifier" class="d-flex align-items-center gap-2"><i class="fa-solid fa-qrcode"></i>Vérifier l'authenticité d'un certificat</a>
    </div>
  </div></div>
</div>`, () => { $$('input[name=role]').forEach(r => r.addEventListener('change', () => { const v = $('input[name=role]:checked').value; $('#login-profile').hidden = v!=='etu'; $('#login-arole').hidden = v!=='admin'; $('#login-mfa').hidden = v!=='admin' || !S.mfa.includes($('#l-arole').value); $('#l-email').value = v==='etu' ? 'EVC-26-0142' : v==='form' ? 'm.yao@evc.ci' : 'd.kone@evc.ci'; })); $('#l-arole').addEventListener('change', e => $('#login-mfa').hidden = !S.mfa.includes(e.target.value)); });
ACT.login = f => { const role = $('input[name=role]:checked').value; if (role==='etu') { S.profile = $('input[name=profile]:checked').value; loadStudent(); } if (role==='admin') S.adminRole = $('#l-arole').value; S.role = role; go(homeOf(role)); toast('Connexion réussie. Bienvenue sur MY EVC.'); };
ACT.forgot = () => openModal({title:'Réinitialiser le mot de passe', body:`<form id="fg" data-submit="forgot-send" novalidate><label class="form-label" for="fg-mail">Email du compte</label><input class="form-control" type="email" id="fg-mail" required placeholder="prenom.nom@mail.com"><div class="invalid-feedback">Saisissez un email valide.</div></form>`, foot:`<button class="btn btn-primary" type="submit" form="fg">Envoyer le lien</button>`});
ACT['forgot-send'] = () => { closeModal(); toast('Un lien de réinitialisation valable 30 minutes a été envoyé.'); };

view('verifier','public','Vérifier un certificat','fa-qrcode', () => `
<div class="content mx-auto" style="max-width:640px">
  <a class="pub-logo mb-4" href="#login">${logoH('couleur')}</a><div class="small-caps mb-1">Vérification des certificats</div>
  <h2 class="h2">Vérifier un certificat EVC</h2><p class="muted">Saisissez le code imprimé sous le QR code du certificat. Exemple : <span class="mono">EVC-CERT-2026-0451</span></p>
  <form data-submit="verify" class="d-flex gap-2 mt-3"><input class="form-control" id="v-code" value="EVC-CERT-2026-0451" aria-label="Code du certificat" required><button class="btn btn-primary" type="submit">Vérifier</button></form>
  <div id="v-res" class="mt-4"></div>
</div>`);
ACT.verify = () => { const c = $('#v-code').value.trim().toUpperCase(); const ok = /^EVC-CERT-20\d\d-\d{4}$/.test(c);
  $('#v-res').innerHTML = ok ? `<div class="panel panel-b d-flex gap-3 align-items-center flex-wrap">${qrSVG(c,110)}<div><span class="pill p-ok mb-2">Certificat authentique</span><h3 class="h5 mb-1">Grace Mensah</h3><div class="muted small">Design Graphic · Session Février 2026 · Mention Bien</div><div class="small mt-1">Délivré le 18/10/2026 · <span class="mono">${esc(c)}</span></div></div></div>`
  : `<div class="alert-strip as-bad"><i class="fa-solid fa-circle-xmark"></i>Aucun certificat ne correspond au code « ${esc(c)} ». Vérifiez la saisie (format EVC-CERT-AAAA-0000).</div>`; };

view('denied','admin','Accès restreint','fa-ban', () => `<div class="panel panel-b text-center py-5"><i class="fa-solid fa-lock fa-2x muted mb-3"></i><h2 class="h4">Ce module n'est pas autorisé pour le rôle « ${esc(S.adminRole)} »</h2><p class="muted">Un Super Administrateur peut modifier les permissions dans Rôles et permissions.</p><button class="btn btn-primary" data-go="${firstAdmin()}">Retour à mon accueil</button></div>`);
