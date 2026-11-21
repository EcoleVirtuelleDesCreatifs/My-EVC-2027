/* ============ MY EVC — v10 : promotions par session, puis par niveau ============
   Une promotion = une session. Dans chaque session, les étudiants sont répartis par niveau (Débutant, Intermédiaire, Avancé).
   L'administration affecte un étudiant à une session et à un niveau, depuis la session ou depuis la fiche étudiant. */

const uSesStudents = s => S.students.filter(x=>x.sessionId===s.id && x.status!=='Archivé');

view('a-session','admin','Session','fa-book-open', () => {
  const s = S.sessions.find(x=>x.id===S.ui.ses) || S.sessions[0]; S.ui.ses = s.id; S.ui.sesF = S.ui.sesF || '';
  const L = uSesStudents(s).filter(x=>!S.ui.sesF || x.f.includes(S.ui.sesF)), all = uSesStudents(s), lv = s.groups.filter(g=>U_LEVELS.includes(g)).length ? U_LEVELS : U_LEVELS;
  const col = l => { const R = L.filter(x=>x.group===l);
    return `<section class="panel lv-col" data-lv="${l}"><div class="panel-h"><h2>${uLvlPill(l)}</h2><span class="sub">${R.length} étudiant${R.length>1?'s':''}</span><div class="tools"><button class="btn btn-quiet btn-sm" data-act="u-ses-add" data-lv="${l}" aria-label="Affecter au niveau ${l}"><i class="fa-solid fa-plus"></i></button></div></div>
      <div class="panel-b">${R.map(x=>`<div class="lv-st"><span class="ini ini-${hueOf(x.name)%5} sm">${esc(initials(x.name))}</span><div class="lr-m"><button class="link-plain fw-sb small text-start" data-act="u-fiche" data-id="${x.id}">${esc(x.name)}</button><div class="small muted">${x.f.map(fchip).join(' ')} <span class="mono ms-1">${x.id}</span></div></div>
        <select class="form-select form-select-sm lv-sel" data-chg="u-ses-lv" data-id="${x.id}" aria-label="Niveau de ${esc(x.name)}">${U_LEVELS.map(o=>`<option ${o===x.group?'selected':''}>${o}</option>`).join('')}</select></div>`).join('') || `<p class="small muted mb-0 py-3 text-center">Aucun étudiant à ce niveau.</p>`}</div></section>`; };
  return `<section class="panel card-pad"><div class="d-flex align-items-center gap-4 flex-wrap"><div class="flex-grow-1" style="min-width:240px"><div class="small-caps mb-1">Promotion</div><h2 class="h2 mb-1" style="font-size:26px">${esc(s.name)}</h2><div class="muted">${s.start} → ${s.end} · ${esc(s.place)} · formateur référent : ${esc(s.trainer||'non affecté')}</div></div>
      <div class="d-flex gap-2 flex-wrap"><button class="btn btn-ghost" data-act="u-assign" data-id="${s.id}">Formateur</button><button class="btn btn-primary" data-act="u-ses-add" data-lv="Débutant"><i class="fa-solid fa-user-plus me-1"></i>Affecter un étudiant</button></div></div>
    <div class="grid g-kpi mt-4">${uKpi('Inscrits', all.length, `${s.cap} places · ${Math.max(0, s.cap - all.length)} libres`, 'fa-users', 'lav', '', 'compact')}${U_LEVELS.map((l,i)=>uKpi(l, all.filter(x=>x.group===l).length, `${all.length?Math.round(all.filter(x=>x.group===l).length/all.length*100):0} % de la promotion`, '', '', '', 'compact')).join('')}</div></section>
  <div class="d-flex align-items-center gap-2 flex-wrap mt-4 mb-3"><h2 class="h3 mb-0 me-auto" style="font-size:20px">Répartition par niveau</h2>${uSeg([['','Toutes'],...FORDER.map(f=>[f,uFShort(f)])], S.ui.sesF, 'u-sesf')}</div>
  <div class="u-grid lv-grid">${lv.map(col).join('')}</div>`;
});
Object.assign(VIEWS['a-session'], {parent:'a-formations', title:() => { const s = S.sessions.find(x=>x.id===S.ui.ses); return s ? esc(s.name) : 'Session'; }, crumb:'Administration <span class="sep">›</span> Formations et sessions <span class="sep">›</span> Promotion'});
[S.perms, PERM_DEFAULT].forEach(P => Object.values(P).forEach(l => { if (l.includes('a-formations') && !l.includes('a-session')) l.push('a-session'); }));
ACT['u-sesf'] = el => { S.ui.sesF = el.dataset.k; rerender(); };
ACT['u-ses-open'] = el => { S.ui.ses = el.dataset.id; S.ui.sesF = ''; go('a-session'); };
INP['u-ses-lv'] = el => { const x = S.students.find(s=>s.id===el.dataset.id), old = x.group; x.group = el.value; audit('Changement de niveau', `${x.name} : ${old} → ${x.group}`); rerender(); toast(`${esc(x.name)} passe au niveau ${x.group}.`); };

/* Affecter un étudiant : à une session et un niveau */
function uAffModal({sid='', ses='', lv='Débutant'}={}){
  const st = sid ? S.students.find(x=>x.id===sid) : null, open = S.sessions.filter(s=>s.status!=='Clôturée');
  openModal({title: st ? `Session et niveau — ${esc(st.name)}` : 'Affecter un étudiant', body:`<form id="aff-f" data-submit="u-aff-save" novalidate class="d-flex flex-column gap-3">
    ${st ? `<div class="d-flex align-items-center gap-3">${uIni(st.name)}<div><b>${esc(st.name)}</b><div class="small muted">Actuellement : ${esc(st.session)} · ${esc(st.group)}</div></div></div><input type="hidden" id="aff-s" value="${st.id}">`
      : `<div><label class="form-label" for="aff-s">Étudiant</label><input class="form-control" id="aff-s" list="aff-dl" required placeholder="Nom ou matricule"><datalist id="aff-dl">${S.students.filter(x=>x.status!=='Archivé').map(x=>`<option value="${x.id}">${esc(x.name)} · ${esc(x.session.replace('Session ',''))}</option>`).join('')}</datalist><div class="invalid-feedback">Choisissez un étudiant de la liste.</div></div>`}
    <div class="row g-3"><div class="col-sm-7"><label class="form-label" for="aff-ses">Session (promotion)</label><select class="form-select" id="aff-ses">${open.map(s=>`<option value="${s.id}" ${(ses||st?.sessionId)===s.id?'selected':''}>${esc(s.name)} · ${Math.max(0, s.cap - uSesStudents(s).length)} places</option>`).join('')}</select></div>
    <div class="col-sm-5"><label class="form-label" for="aff-lv">Niveau</label><select class="form-select" id="aff-lv">${U_LEVELS.map(l=>`<option ${(st?st.group:lv)===l?'selected':''}>${l}</option>`).join('')}</select></div></div>
    <div><label class="form-label" for="aff-why">Motif (facultatif)</label><input class="form-control" id="aff-why" placeholder="Ex. report de session, test de positionnement"></div>
    <label class="d-flex gap-2 align-items-center small"><input class="form-check-input mt-0" type="checkbox" id="aff-n" checked>Prévenir l'étudiant et le formateur (notification et e-mail)</label></form>`,
    foot:`<button class="btn btn-ghost" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary" type="submit" form="aff-f">Affecter</button>`});
}
ACT['u-ses-add'] = el => uAffModal({ses: S.ui.ses, lv: el.dataset.lv});
ACT['u-aff'] = el => uAffModal({sid: el.dataset.id});
ACT['u-aff-save'] = () => { const v = $('#aff-s').value.trim(), x = S.students.find(s=>s.id===v || norm(s.name)===norm(v));
  if (!x) { $('#aff-s').classList.add('is-invalid'); toast('Choisissez un étudiant de la liste.','warn'); return; }
  const s = S.sessions.find(z=>z.id===$('#aff-ses').value), lv = $('#aff-lv').value, before = `${x.session} · ${x.group}`;
  if (x.sessionId===s.id && x.group===lv) { toast('Cet étudiant est déjà dans cette session et ce niveau.','info'); return; }
  Object.assign(x, {session:s.name, sessionId:s.id, group:lv}); audit('Affectation de session', `${x.name} (${x.id}) : ${before} → ${s.name} · ${lv}${$('#aff-why').value?` — ${$('#aff-why').value}`:''}`);
  if (x.id==='EVC-26-0142' && S.st) { S.st.student.session = `${s.name} — Abidjan`; S.st.notifs.unshift({id:'n'+Date.now(), cat:'Annonces', f:'', t:`Vous êtes affecté(e) à la ${s.name}, niveau ${lv}.`, d:"À l'instant", read:false}); }
  closeModal(); rerender(); toast(`${esc(x.name)} affecté(e) à la ${esc(s.name)} · niveau ${lv}.`); };

/* La modification du dossier met aussi à jour l'identifiant de session */
(() => { const base = ACT['as-save']; ACT['as-save'] = f => { base(f); const x = S.students.find(s=>s.id===f.dataset.id), s = S.sessions.find(z=>z.name===x.session); if (s) x.sessionId = s.id; }; })();

/* Branchements : liste des sessions → page promotion ; fiche étudiant → bouton d'affectation */
(() => { const base = VIEWS['a-formations'].render;
  VIEWS['a-formations'].render = () => base()
    .replace(/data-act="se-assign" data-id="([^"]+)">Gérer/g, 'data-act="u-ses-open" data-id="$1">Voir la promotion')
    .replace(/<td><div class="tdn">([^<]+)<\/div><div class="tds">/g, (m, n) => { const s = S.sessions.find(z=>z.name===n.replace(/&#39;/g,"'")); if (!s) return m; const all = uSesStudents(s); return `<td><button class="link-plain tdn text-start" data-act="u-ses-open" data-id="${s.id}">${n}</button><div class="lv-mini">${U_LEVELS.map(l=>`<span>${l.slice(0,3)}. ${all.filter(x=>x.group===l).length}</span>`).join('')}</div><div class="tds">`; });
})();
(() => { const base = VIEWS['a-fiche'].render;
  VIEWS['a-fiche'].render = () => { const s = S.students.find(x=>x.id===S.ui.sid) || S.students[0];
    return base().replace('<button class="btn btn-ghost" data-act="msg-new">Envoyer un message</button>', `<button class="btn btn-ghost" data-act="u-aff" data-id="${s.id}">Session et niveau</button><button class="btn btn-ghost" data-act="msg-new">Envoyer un message</button>`)
      .replace(`${s.id} · ${s.f.map(f=>esc(uFShort(f))).join(' + ')} · ${esc(s.session)}</div>`, `${s.id} · ${s.f.map(f=>esc(uFShort(f))).join(' + ')} · ${esc(s.session)} · niveau ${esc(s.group)}</div>`); };
})();
