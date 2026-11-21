/* ============ MY EVC — v9 : gabarit et composants de la charte UI de l'application web ============
   Ce fichier redéfinit le gabarit (menu indigo 264 px, barre supérieure 88 px, carte utilisateur, navigation mobile)
   et fournit les composants des écrans (indicateurs, lignes, pastilles, segments, frise, états vides). */

/* ---------- Composants ---------- */
const uIni = (name, cls='') => `<span class="ini ini-${hueOf(name)%5} ${cls}" aria-hidden="true">${esc(initials(name))}</span>`;
const uKpi = (label, value, sub='', icon='fa-circle', tone='lav', subTone='', cls='') => `<div class="ukpi ${cls}"><div class="uk-h"><span>${label}</span>${icon?`<span class="uk-i t-${tone}"><i class="fa-solid ${icon}"></i></span>`:''}</div><div class="uk-v">${value}</div>${sub?`<div class="uk-s ${subTone?'tx-'+subTone:''}">${sub}</div>`:''}</div>`;
const uSeg = (items, cur, act, {soft=false, attr='k', cls=''}={}) => `<div class="seg ${soft?'seg-soft':''} ${cls}" role="tablist">${items.map(([k,l])=>`<button type="button" role="tab" class="${k===cur?'on':''}" aria-selected="${k===cur}" data-act="${act}" data-${attr}="${k}">${l}</button>`).join('')}</div>`;
const uTabs = (items, cur, act) => `<ul class="nav nav-tabs mb-4" role="tablist">${items.map(([k,l])=>`<li class="nav-item"><button class="nav-link ${k===cur?'active':''}" data-act="${act}" data-k="${k}" role="tab" aria-selected="${k===cur}">${l}</button></li>`).join('')}</ul>`;
const uBar = (pct, cls='') => `<div class="u-bar ${cls}" role="progressbar" aria-valuenow="${Math.round(pct)}" aria-valuemin="0" aria-valuemax="100"><span style="width:${Math.max(0,Math.min(100,pct))}%"></span></div>`;
const uDbox = (d, cls='') => `<div class="dbox ${cls}"><span>${d.toLocaleDateString('fr-FR',{month:'short'}).replace('.','')}</span><b>${d.getDate()}</b></div>`;
const uDboxDay = (d, cls='') => `<div class="dbox ${cls}"><span>${d.toLocaleDateString('fr-FR',{weekday:'short'}).replace('.','')}</span><b>${d.getDate()}</b></div>`;
const uEmpty = (title, text='', btn='') => `<div class="u-empty"><div class="ue-i">${logoSym('couleur')}</div><h3>${title}</h3>${text?`<p>${text}</p>`:''}${btn}</div>`;
const uCard = (title, body, o={}) => panel(title, body, o);
const uH = hl => String(hl||'').replace(/(\d{1,2})h(\d{2})/g, (m,h,mn)=>`${h} h ${mn}`);
const fmtLong = d => d.toLocaleDateString('fr-FR',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
const fmtDM = d => d.toLocaleDateString('fr-FR',{day:'numeric',month:'long'});
const dueTxt = n => n<0 ? `En retard de ${-n} j` : n===0 ? "Aujourd'hui" : n===1 ? 'Demain' : `Dans ${n} jours`;
const dueTone = n => n<0 ? 'bad' : n<=3 ? 'warn' : 'mute';
const frFile = n => { const e = (String(n).split('.').pop()||'').toUpperCase(); return /^(PDF|ZIP|JPG|PNG|MP4|AI|PSD|DOCX|XLSX|INDD|PPTX)$/.test(e) ? e.replace('DOCX','DOC').replace('XLSX','XLS').replace('PPTX','PPT').replace('INDD','INDD') : 'LIEN'; };
const uGo = (id, patch={}) => { Object.assign(S.ui, patch); go(id); };
Object.assign(ICONS, {
  'message': '<path d="M4 5.5A1.5 1.5 0 0 1 5.5 4h13A1.5 1.5 0 0 1 20 5.5v10a1.5 1.5 0 0 1-1.5 1.5H9l-5 4z"/>',
  'chart-column': '<path d="M4 20.5h16"/><path d="M7 16.5v-5M12 16.5v-10M17 16.5v-7"/>',
  'exclamation': '<path d="M12 5.5v9M12 18.5h.01"/>', 'info': '<path d="M12 10.5v7M12 6.5h.01"/>',
  'phone': '<path d="M5 3.5h3l1.5 4.5-2 1.5a11 11 0 0 0 7 7l1.5-2 4.5 1.5v3a2 2 0 0 1-2 2A17 17 0 0 1 3 5.5a2 2 0 0 1 2-2z"/>',
  'stopwatch': '<circle cx="12" cy="13.5" r="7.5"/><path d="M12 9.5v4l2.5 2M10 2.5h4M18.5 6l1.5-1.5"/>',
  'arrow-left': '<path d="M19 12H5m6-6-6 6 6 6"/>', 'arrow-right': '<path d="M5 12h14m-6-6 6 6-6 6"/>', 'minus': '<path d="M5 12h14"/>',
  'file-pen': '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h4"/><path d="M14 3v5h5"/><path d="m19.5 13.5 1.9 1.9-5.6 5.6H13.9v-1.9z"/>',
  'tray': '<path d="M3 13.5h5l1.5 2.5h5l1.5-2.5h5"/><path d="M5.5 5h13L21 13.5V18a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4.5z"/>',
  'pen-ruler': '<path d="M4 20 15.5 8.5l-4-4L4 16z"/><path d="m13.5 6.5 4 4M16 3l5 5"/>'
});

/* ---------- Libellés et icônes des écrans (charte) ---------- */
const U_LABELS = {
  'e-dashboard':['Tableau de bord','fa-house-chimney'], 'e-formations':['Ma formation','fa-book-open'], 'e-seances':['Séances','fa-calendar-days'], 'e-todo':['Travaux pratiques','fa-file-lines'],
  'e-evaluations':['Évaluations','fa-chart-column'], 'e-paiements':['Paiements','fa-credit-card'], 'e-certification':['Certification','fa-award'], 'e-messagerie':['Messagerie','fa-message'],
  'e-profil':['Mon profil','fa-user'], 'e-notifications':['Notifications','fa-bell'], 'e-webtv':['Web TV','fa-tv'], 'e-creneaux':['Créneaux','fa-clock'], 'e-projets':['Projets','fa-palette'],
  't-dashboard':['Tableau de bord','fa-house-chimney'], 't-seances':['Séances et présences','fa-clipboard-check'], 't-corrections':['Corrections','fa-file-lines'], 't-planning':['Emploi du temps','fa-calendar-week'],
  't-etudiants':['Mes étudiants','fa-users'], 't-messagerie':['Messagerie','fa-message'], 't-evaluations':['QCM et examens','fa-pen-to-square'],
  'a-dashboard':['Tableau de bord','fa-house-chimney'], 'a-etudiants':['Étudiants','fa-users'], 'a-preinscriptions':['Préinscriptions','fa-tray'], 'a-formations':['Formations et sessions','fa-book-open'],
  'a-finances':['Finances et relances','fa-credit-card'], 'a-certifications':['Certifications','fa-award'], 'a-rapports':['Rapports','fa-file-invoice'], 'a-roles':['Utilisateurs et rôles','fa-sliders'], 'a-sessions':['Sessions et promotions','fa-calendar-days']
};
function uApplyLabels(){ Object.entries(U_LABELS).forEach(([id,[l,i]]) => { if (VIEWS[id]) { VIEWS[id].label = l; VIEWS[id].icon = i; } }); }
uApplyLabels();
MENUS.etu = [['',['e-dashboard','e-formations','e-seances','e-todo','e-evaluations','e-paiements','e-certification','e-messagerie','e-insertion','e-profil']],['Plus',['e-webtv','e-creneaux','e-projets','e-bibliotheque','e-rapports','e-cv','e-notifications','e-assistance'],true]];
MENUS.form = [['',['t-dashboard','t-seances','t-corrections','t-notes']],['Plus',['t-planning','t-etudiants','t-messagerie','t-ressources','t-evaluations','t-rapports','t-notifications'],true]];
MENUS.admin = [['',['a-dashboard','a-etudiants','a-preinscriptions','a-formations','a-finances','a-certifications','a-rapports','a-roles']],['Suivi',['a-live','a-travaux','a-communication','a-seances','a-creneaux','a-formateurs'],true],['Programmes',['a-webtv','a-studio','a-reco'],true],['Gestion',['a-sessions','a-factures','a-documents','a-stages','a-support'],true],['Système',['a-audit','a-parametres'],true]];
[S.perms, PERM_DEFAULT].forEach(P => Object.values(P).forEach(l => { if (l.includes('a-etudiants') && !l.includes('a-fiche')) l.push('a-fiche'); }));
const U_SPACE = {etu:'Espace étudiant', form:'Espace formateur', admin:'Administration'};
const U_HOME_PROFILE = {etu:'e-profil', form:'t-profil', admin:'a-parametres'};

/* ---------- Gabarit ---------- */
function uWho(sp){
  if (sp==='etu') { const s = S.st.student; return {name:`${s.first} ${s.last}`, role:`Étudiante · ${fl().join(' + ')}`, photo:S.st.photo}; }
  if (sp==='form') return {name:'Marc Yao', role:'Formateur principal · DG'};
  return {name:'Didier Koné', role:S.adminRole};
}
function uCrumb(v, sp){
  if (v.crumb) return typeof v.crumb==='function' ? v.crumb() : v.crumb;
  if (/-dashboard$/.test(v.id)) return `${U_SPACE[sp]} · ${fmtLong(TODAY)}`;
  return `${U_SPACE[sp]} <span class="sep">›</span> ${esc(v.crumbTail || v.label)}`;
}
function shell(v, html){
  const sp = S.role, groups = MENUS[sp]; S.navOpen = S.navOpen || {}; S.navCollapsed = false;
  const link = id => { const x = VIEWS[id]; if (!x) return ''; const c = menuCount(id), on = id===v.id || v.parent===id; return `<li><a href="#${id}" data-tip="${esc(x.label)}" class="${on?'active':''}" ${on?'aria-current="page"':''}><i class="fa-solid ${x.icon}"></i><span>${x.label}</span>${c?`<span class="count">${c}</span>`:''}</a></li>`; };
  const items = groups.map(([label, ids, fold]) => { const vis = ids.filter(id => VIEWS[id] && (sp!=='admin' || S.perms[S.adminRole].includes(id))); if (!vis.length) return '';
    if (!fold) return vis.map(link).join('');
    const open = vis.includes(v.id) || vis.includes(v.parent) || S.navOpen[sp+label], n = vis.reduce((a,id)=>a+menuCount(id),0);
    return `<li class="menu-fold"><details ${open?'open':''} data-fold="${sp+label}"><summary class="menu-label"><span>${label}</span>${n&&!open?`<span class="count">${n}</span>`:''}<i class="fa-solid fa-chevron-down chev"></i></summary><ul class="menu sub">${vis.map(link).join('')}</ul></details></li>`; }).join('');
  const w = uWho(sp), unread = sp==='etu' ? S.st.notifs.filter(n=>!n.read).length : sp==='admin' ? S.anotifs.filter(n=>!n.read).length : tNotifs().filter(n=>!n.read).length;
  const title = typeof v.title==='function' ? v.title() : (v.title || v.label), cta = v.cta ? (typeof v.cta==='function' ? v.cta() : v.cta) : '';
  const fsel = sp==='etu' && isCombined() && !v.noFsel && !['e-profil','e-parametres','e-assistance','e-messagerie','e-cv','e-paiements','e-paiement-ok','e-insertion','e-notifications','e-quiz'].includes(v.id) ? `<div class="fsel seg seg-soft d-none d-md-flex" role="group" aria-label="Sélecteur de formation"><button class="${S.fsel==='ALL'?'on':''}" data-act="fsel" data-f="ALL">Toutes</button>${fl().map(f=>`<button class="${S.fsel===f?'on':''}" data-act="fsel" data-f="${f}"><span class="sw" style="background:var(--${f.toLowerCase()})"></span>${f}</button>`).join('')}</div>` : '';
  const prof = U_HOME_PROFILE[sp], canProf = sp!=='admin' || S.perms[S.adminRole].includes(prof);
  return `<div class="app">
  <aside class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" aria-label="Menu principal"><div class="side-inner">
    <div class="d-flex align-items-center"><a class="brand" href="#${homeOf(sp)}" aria-label="MY EVC — accueil">${logoSym('negatif','u-sym')}${logoType('negatif')}</a><button type="button" class="btn-close btn-close-white ms-auto d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Fermer"></button></div>
    <div class="space-tag">${U_SPACE[sp]}</div>
    <ul class="menu"><li class="nav-ind" aria-hidden="true"></li>${items}</ul>
    <div class="u-user"><a class="uu-top text-decoration-none" ${canProf?`href="#${prof}"`:''} title="Mon profil">${w.photo?`<span class="ini"><img src="${w.photo}" alt=""></span>`:`<span class="ini">${esc(initials(w.name))}</span>`}<span style="min-width:0"><b>${esc(w.name)}</b><small>${esc(w.role)}</small></span></a>
      <div class="uu-act"><button class="uu-out" data-act="logout"><i class="fa-solid fa-right-from-bracket"></i>Se déconnecter</button><button class="uu-ic" data-act="theme" title="Thème : ${({auto:'automatique',dark:'sombre',light:'clair'})[S.theme]}" aria-label="Changer de thème"><i class="fa-solid fa-circle-half-stroke"></i></button><button class="uu-ic" data-act="demo" title="Mode démo" aria-label="Mode démo"><i class="fa-solid fa-sliders"></i></button></div></div>
  </div></aside>
  <div class="main">
    <header class="topbar">
      <button class="btn btn-quiet btn-icon d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-label="Ouvrir le menu"><i class="fa-solid fa-bars"></i></button>
      <div class="crumb"><small>${uCrumb(v, sp)}</small><h1>${title}</h1></div>
      <div class="top-actions">
        <label class="search-field t-search d-none d-md-flex mb-0"><i class="fa-solid fa-magnifying-glass"></i><input class="form-control" placeholder="Rechercher..." readonly data-act="cmdk" aria-label="Rechercher (Ctrl+K)"></label>
        <button class="btn btn-quiet btn-icon d-md-none" data-act="cmdk" aria-label="Rechercher"><i class="fa-solid fa-magnifying-glass"></i></button>
        <button class="btn btn-icon bell" data-act="bell" aria-label="Notifications${unread?` (${unread} non lues)`:''}"><i class="fa-solid fa-bell"></i>${unread?'<span class="dot"></span>':''}</button>
        ${cta?`<span class="d-none d-sm-inline-flex">${cta}</span>`:''}
      </div>
    </header>
    <main class="content" id="view">${fsel ? `<div class="fsel-row"><span class="d-none d-sm-inline">Afficher</span>${fsel.replace('d-none d-md-flex','d-flex')}</div>` : ''}${html}</main>
  </div>
  ${bottomNav(sp, v.parent || v.id)}</div>`;
}
function bottomNav(sp, cur){
  const pick = sp==='etu' ? [['e-dashboard','Accueil'],['e-formations','Formation'],['e-seances','Planning'],['e-paiements','Paiements'],['e-profil','Profil']]
    : sp==='form' ? [['t-dashboard','Accueil'],['t-seances','Appel'],['t-corrections','Corrections'],['t-messagerie','Messages']]
    : [['a-dashboard','Accueil'],['a-preinscriptions','Inscriptions'],['a-finances','Relances'],['a-etudiants','Étudiants']].filter(([id]) => S.perms[S.adminRole].includes(id)).slice(0,4);
  return `<nav class="bottom-nav" aria-label="Navigation rapide">${pick.map(([id,l]) => { const x = VIEWS[id], c = menuCount(id); return `<a href="#${id}" class="${id===cur?'on':''}" ${id===cur?'aria-current="page"':''}><i class="fa-solid ${x.icon}"></i><span class="lbl">${l}</span>${c?`<span class="bn-count">${c}</span>`:''}</a>`; }).join('')}</nav>`;
}
function toast(msg, kind='ok'){
  const icon = {ok:'fa-check',warn:'fa-exclamation',bad:'fa-exclamation',info:'fa-info'}[kind] || 'fa-check';
  const el = document.createElement('div'); el.className = 'toast align-items-center border-0'; el.setAttribute('role','status');
  el.innerHTML = `<div class="d-flex align-items-center"><div class="toast-body d-flex align-items-center"><span class="t-ic ${kind}"><i class="fa-solid ${icon}"></i></span><span>${msg}</span></div><button type="button" class="btn-close btn-close-white me-3 ms-auto" data-bs-dismiss="toast" aria-label="Fermer"></button></div>`;
  $('#toasts').appendChild(el); const t = new bootstrap.Toast(el, {delay:3400}); t.show(); el.addEventListener('hidden.bs.toast', () => el.remove());
}
function countUp(root){
  if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  $$('.ukpi .uk-v, .num-key, .kpi .k-v, .stat .s-v', root).forEach(el => {
    const w = document.createTreeWalker(el, NodeFilter.SHOW_TEXT); let n;
    while ((n = w.nextNode())) { const m = n.nodeValue.match(/^(\D*?)(\d{1,3}(?:[  ]\d{3})*|\d+)(?![,.]\d)(.*)$/s); if (!m) continue;
      const target = parseInt(m[2].replace(/\D/g,''),10); if (!target) break; const node = n, t0 = performance.now(), dur = 850;
      const step = t => { const p = Math.min(1,(t-t0)/dur), e = 1-Math.pow(1-p,3); node.nodeValue = m[1] + nfr(Math.round(target*e)) + m[3]; if (p<1) requestAnimationFrame(step); };
      node.nodeValue = m[1] + '0' + m[3]; requestAnimationFrame(step); break; }
  });
}
/* Onglets et segments : uReg('ptab') crée l'action « u-ptab » qui range data-k dans S.ui.ptab puis redessine l'écran */
const uReg = (...keys) => keys.forEach(k => { ACT['u-'+k] = el => { S.ui[k] = el.dataset.k; rerender(); }; });

/* ---------- v10 : niveaux de promotion et appréciations professionnelles (pas de notation chiffrée) ---------- */
const U_LEVELS = ['Débutant','Intermédiaire','Avancé'];
const uLvlPill = l => { const k = Math.max(0, U_LEVELS.indexOf(l)); return `<span class="lvl"><span class="lvb" aria-hidden="true">${[0,1,2].map(i=>`<b class="${i<=k?'on':''}" style="height:${5+i*3}px"></b>`).join('')}</span>${esc(l||'—')}</span>`; };
/* Une valeur interne (0-20) est traduite en appréciation de compétence ; aucune note n'est affichée */
const U_APP = [[16,'Excellent','ok'],[13,'Très bien','ok'],[10,'Acquis','info'],[0,'À renforcer','warn']];
const uApp = v => v==null ? ['En attente','mute'] : U_APP.find(a=>v>=a[0]).slice(1);
const uAppPill = v => { const a = uApp(v); return pill(a[0], a[1]); };
