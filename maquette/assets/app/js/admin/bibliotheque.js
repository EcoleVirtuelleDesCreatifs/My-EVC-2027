/* ============ MY EVC — Bibliothèque : gestion (administration et formateurs) ============
   Publier les ressources que les étudiants téléchargent dans leur Bibliothèque (#e-bibliotheque).

   - #a-bibliotheque  administration : toutes les formations (et les ressources communes) ;
   - #t-bibliotheque  formateur : uniquement sa formation (Marc Yao → DG dans la démonstration).
   Ajouter, modifier, publier ou repasser en brouillon, supprimer. Une ressource publiée apparaît
   aussitôt chez les étudiants concernés ; « Prévenir les étudiants » leur envoie une notification.

   Catégories et outils d'affichage : etudiant/bibliotheque.js (LIB_CATS, libIcon…).
   En production : table ressources(id, formation_code|NULL, categorie, titre, description, format,
   taille, licence, compatible, url|fichier, indispensable, publie, cree_le, cree_par) et
   telechargements(ressource_id, etudiant_id, le). */

const LIB_LICENCES = ['Réservé EVC', 'Libre et gratuit', 'Gratuit', 'Gratuit (version Pro payante)', 'Libre (OFL)', 'Licence payante'];
const libScope = () => S.role === 'form' ? ['DG'] : FORDER;          // formations gérées par l'utilisateur
const libManaged = () => S.lib.filter(r => S.role === 'form' ? libScope().includes(r.f) : true);
const libAuteur = () => S.role === 'form' ? 'Marc Yao' : 'Didier Koné';

/* ---------- 1. Écran de gestion (commun aux deux espaces) ---------- */
function libAdmRows(){
  const U = S.ui.libA = S.ui.libA || {}, q = (U.q || '').trim().toLowerCase();
  const L = libManaged().filter(r => (!U.f || r.f === U.f) && (!U.cat || r.cat === U.cat) && (!U.st || (U.st === 'pub') === (r.pub !== false)) && (!q || r.t.toLowerCase().includes(q)))
    .sort((a, b) => b.d - a.d);
  if (!L.length) return `<div class="panel">${empty('Aucune ressource ne correspond à ces filtres.', 'fa-book-bookmark')}</div>`;
  return `<div class="panel tbl"><table class="table align-middle"><thead><tr><th>Ressource</th><th>Catégorie</th><th>Formation</th><th>Licence</th><th class="text-end">Téléch.</th><th>Ajoutée</th><th>Statut</th><th></th></tr></thead><tbody>
    ${L.map(r => `<tr>
      <td><div class="lib-adm-t">${libBook(r, 'xs')}<div class="lr-m"><b title="${esc(r.t)}">${esc(r.t)}</b><small>${esc(r.fmt)}${r.size !== '—' ? ` · ${esc(r.size)}` : ''}${r.key ? ' · <i class="fa-solid fa-star" title="Indispensable"></i> Indispensable' : ''}</small></div></div></td>
      <td class="small">${esc(libCat(r).l)}</td>
      <td>${libFChip(r)}</td>
      <td class="small">${esc(r.lic)}</td>
      <td class="text-end">${nfr(r.dl)}</td>
      <td class="small text-nowrap">${r.d === 0 ? "Aujourd'hui" : fmtD(addDays(r.d), {day:'numeric', month:'short'})}</td>
      <td>${r.pub === false ? pill('Brouillon', 'mute') : pill('Publiée', 'ok')}</td>
      <td class="text-end text-nowrap">
        <button class="btn btn-quiet btn-sm btn-icon" data-act="lib-edit" data-id="${r.id}" aria-label="Modifier ${esc(r.t)}" title="Modifier"><i class="fa-solid fa-pen"></i></button>
        <button class="btn btn-quiet btn-sm btn-icon" data-act="lib-pub" data-id="${r.id}" aria-label="${r.pub === false ? 'Publier' : 'Repasser en brouillon'}" title="${r.pub === false ? 'Publier' : 'Repasser en brouillon'}"><i class="fa-solid ${r.pub === false ? 'fa-eye' : 'fa-eye-slash'}"></i></button>
        <button class="btn btn-quiet btn-sm btn-icon" data-act="lib-del" data-id="${r.id}" aria-label="Supprimer ${esc(r.t)}" title="Supprimer"><i class="fa-solid fa-trash-can"></i></button></td></tr>`).join('')}
  </tbody></table></div>`;
}

function libManager(){
  const U = S.ui.libA = S.ui.libA || {}, M = libManaged(), pub = M.filter(r => r.pub !== false);
  const total = M.reduce((a, r) => a + r.dl, 0), top = M.slice().sort((a, b) => b.dl - a.dl)[0], recents = M.filter(r => r.d >= -30).length;
  const scope = libScope();
  return `
  <div class="lib-kpis">
    ${uKpi('Ressources publiées', nfr(pub.length), `${M.length - pub.length} brouillon${M.length - pub.length > 1 ? 's' : ''}`, 'fa-book-bookmark', 'lav')}
    ${uKpi('Téléchargements', nfr(total), 'Depuis la rentrée', 'fa-download', 'cor')}
    ${uKpi('Ajoutées ce mois', nfr(recents), '30 derniers jours', 'fa-plus', 'ok')}
    ${uKpi('La plus téléchargée', top ? nfr(top.dl) : '—', top ? esc(top.t) : '', 'fa-trophy', 'warn')}
  </div>
  <div class="filters-row mb-3">
    <label class="search-field flex-grow-1 mb-0" style="min-width:220px"><i class="fa-solid fa-magnifying-glass"></i><input class="form-control" style="background:var(--surface)" placeholder="Rechercher une ressource…" value="${esc(U.q || '')}" data-inp="liba-q" aria-label="Rechercher une ressource"></label>
    ${scope.length > 1 ? `<select class="form-select" data-chg="liba-f" data-k="f" aria-label="Formation" style="max-width:240px"><option value="">Toutes les formations</option>${scope.map(f => `<option value="${f}" ${U.f === f ? 'selected' : ''}>${esc(F[f].name)}</option>`).join('')}<option value="ALL" ${U.f === 'ALL' ? 'selected' : ''}>Communes à toutes</option></select>` : ''}
    <select class="form-select" data-chg="liba-f" data-k="cat" aria-label="Catégorie" style="max-width:220px"><option value="">Toutes les catégories</option>${Object.entries(LIB_CATS).map(([k, c]) => `<option value="${k}" ${U.cat === k ? 'selected' : ''}>${c.l}</option>`).join('')}</select>
    <select class="form-select" data-chg="liba-f" data-k="st" aria-label="Statut" style="max-width:170px"><option value="">Tous les statuts</option><option value="pub" ${U.st === 'pub' ? 'selected' : ''}>Publiées</option><option value="brouillon" ${U.st === 'brouillon' ? 'selected' : ''}>Brouillons</option></select>
  </div>
  <div id="liba-region">${libAdmRows()}</div>`;
}

const libCta = () => `<button class="btn btn-primary t-cta" data-act="lib-new"><i class="fa-solid fa-plus"></i>Ajouter une ressource</button>`;
view('a-bibliotheque', 'admin', 'Bibliothèque', 'fa-book-bookmark', libManager);
Object.assign(VIEWS['a-bibliotheque'], { title: 'Bibliothèque des étudiants', cta: libCta,
  crumb: () => `Administration <span class="sep">›</span> Programmes <span class="sep">›</span> Bibliothèque` });
view('t-bibliotheque', 'form', 'Bibliothèque', 'fa-book-bookmark', libManager);
Object.assign(VIEWS['t-bibliotheque'], { title: 'Bibliothèque de ma formation', cta: libCta,
  crumb: () => `Espace formateur <span class="sep">›</span> Bibliothèque` });

/* ---------- 2. Ajouter ou modifier une ressource ---------- */
function libForm(r){
  const nouveau = !r, x = r || {cat:'ebooks', f:libScope()[0], lic:'Réservé EVC', pub:true}, ext = !!x.url;
  const opts = (list, cur) => list.map(([v, l]) => `<option value="${v}" ${cur === v ? 'selected' : ''}>${esc(l)}</option>`).join('');
  const formations = [...libScope().map(f => [f, F[f].name]), ...(S.role === 'form' ? [] : [['ALL', 'Toutes les formations']])];
  openModal({ title: nouveau ? 'Ajouter une ressource' : 'Modifier la ressource', size: 'modal-lg',
    body: `<form id="lib-form" data-submit="lib-save" data-id="${x.id || ''}" class="row g-3" novalidate>
      <div class="col-12"><label class="form-label" for="lf-t">Titre</label><input class="form-control" id="lf-t" required maxlength="90" value="${esc(x.t || '')}" placeholder="Ex. : Pack de polices pour l'affiche"><div class="invalid-feedback">Indiquez un titre.</div></div>
      <div class="col-sm-6"><label class="form-label" for="lf-c">Catégorie</label><select class="form-select" id="lf-c">${opts(Object.entries(LIB_CATS).map(([k, c]) => [k, c.l]), x.cat)}</select></div>
      <div class="col-sm-6"><label class="form-label" for="lf-f">Formation</label><select class="form-select" id="lf-f">${opts(formations, x.f)}</select></div>
      <div class="col-12"><label class="form-label" for="lf-d">Description</label><textarea class="form-control" id="lf-d" rows="2" required maxlength="240" placeholder="À quoi sert cette ressource, en une ou deux phrases.">${esc(x.desc || '')}</textarea><div class="invalid-feedback">Ajoutez une courte description.</div></div>
      <div class="col-12"><span class="form-label d-block">Source</span><div class="lib-src" role="radiogroup">
        <label class="form-check-card"><input class="form-check-input me-2" type="radio" name="lf-src" value="fichier" ${ext ? '' : 'checked'} data-chg="lf-src">Fichier hébergé par l'EVC</label>
        <label class="form-check-card"><input class="form-check-input me-2" type="radio" name="lf-src" value="lien" ${ext ? 'checked' : ''} data-chg="lf-src">Lien vers le site officiel</label></div></div>
      <div class="col-12 ${ext ? 'd-none' : ''}" id="lf-fw"><label class="form-label" for="lf-file">Fichier ${nouveau ? '' : '<span class="small muted">(laisser vide pour garder le fichier actuel)</span>'}</label><input class="form-control" type="file" id="lf-file" data-chg="lf-file"></div>
      <div class="col-12 ${ext ? '' : 'd-none'}" id="lf-uw"><label class="form-label" for="lf-u">Adresse du site officiel</label><input class="form-control" type="url" id="lf-u" value="${esc(x.url || '')}" placeholder="https://…" ${ext ? 'required' : ''}><div class="invalid-feedback">Indiquez une adresse commençant par https://</div></div>
      <div class="col-sm-4"><label class="form-label" for="lf-fmt">Format</label><input class="form-control" id="lf-fmt" value="${esc(x.fmt || '')}" placeholder="PDF, ZIP · TTF…"></div>
      <div class="col-sm-4"><label class="form-label" for="lf-s">Taille</label><input class="form-control" id="lf-s" value="${esc(x.size && x.size !== '—' ? x.size : '')}" placeholder="Ex. : 4,2 Mo"></div>
      <div class="col-sm-4"><label class="form-label" for="lf-l">Licence</label><select class="form-select" id="lf-l">${opts(LIB_LICENCES.map(l => [l, l]), x.lic)}</select></div>
      <div class="col-12"><label class="form-label" for="lf-os">Compatible <span class="small muted">(facultatif)</span></label><input class="form-control" id="lf-os" value="${esc(x.os || '')}" placeholder="Windows · macOS · mobile"></div>
      <div class="col-12 d-flex flex-column gap-2">
        <div class="form-check"><input class="form-check-input" type="checkbox" id="lf-key" ${x.key ? 'checked' : ''}><label class="form-check-label" for="lf-key">Indispensable : afficher dans « Pour bien démarrer »</label></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" id="lf-pub" ${x.pub !== false ? 'checked' : ''}><label class="form-check-label" for="lf-pub">Publier : visible tout de suite par les étudiants</label></div>
        ${nouveau ? `<div class="form-check"><input class="form-check-input" type="checkbox" id="lf-n" checked><label class="form-check-label" for="lf-n">Prévenir les étudiants concernés (notification)</label></div>` : ''}
      </div></form>`,
    foot: `<button class="btn btn-ghost" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary" type="submit" form="lib-form"><i class="fa-solid fa-check"></i>${nouveau ? 'Ajouter' : 'Enregistrer'}</button>` });
}
ACT['lib-new'] = () => libForm(null);
ACT['lib-edit'] = el => libForm(S.lib.find(r => r.id === el.dataset.id));

INP['lf-src'] = () => { const ext = $('input[name=lf-src]:checked').value === 'lien';
  $('#lf-fw').classList.toggle('d-none', ext); $('#lf-uw').classList.toggle('d-none', !ext); $('#lf-u').required = ext;
  if (ext && !$('#lf-fmt').value) $('#lf-fmt').value = 'Site officiel'; };
INP['lf-file'] = el => { const f = el.files && el.files[0]; if (!f) return;
  const ext = (f.name.split('.').pop() || '').toUpperCase(), mo = f.size / 1048576;
  $('#lf-fmt').value = ext; $('#lf-s').value = mo >= 1 ? `${mo.toFixed(1).replace('.', ',')} Mo` : `${Math.max(1, Math.round(f.size / 1024))} Ko`;
  if (!$('#lf-t').value) $('#lf-t').value = f.name.replace(/\.[^.]+$/, '').replace(/[-_]+/g, ' '); };

ACT['lib-save'] = form => {
  const ext = $('input[name=lf-src]:checked').value === 'lien', id = form.dataset.id, old = id ? S.lib.find(r => r.id === id) : null;
  if (!ext && !old?.size && !$('#lf-file').files.length && !$('#lf-s').value.trim()) { toast('Ajoutez le fichier à mettre en ligne.', 'warn'); return; }
  const data = { t: $('#lf-t').value.trim(), cat: $('#lf-c').value, f: $('#lf-f').value, desc: $('#lf-d').value.trim(),
    fmt: $('#lf-fmt').value.trim() || (ext ? 'Site officiel' : 'Fichier'), size: $('#lf-s').value.trim() || '—', lic: $('#lf-l').value,
    os: $('#lf-os').value.trim(), url: ext ? $('#lf-u').value.trim() : '', key: $('#lf-key').checked, pub: $('#lf-pub').checked };
  if (old) { Object.assign(old, data); audit('Ressource modifiée', `Bibliothèque — ${data.t}`); closeModal(); rerender(); toast('Ressource mise à jour.'); return; }
  const r = { id: 'lb-' + Date.now(), d: 0, dl: 0, by: libAuteur(), ...data }; S.lib.unshift(r);
  const vise = S.st && (r.f === 'ALL' || PROFILES[S.profile].f.includes(r.f));
  if (r.pub && $('#lf-n')?.checked && vise) S.st.notifs.unshift({ id: 'n' + Date.now(), cat: 'Bibliothèque', f: r.f === 'ALL' ? null : r.f, t: `Nouvelle ressource : « ${r.t} » est disponible dans votre bibliothèque.`, d: "À l'instant", read: false });
  audit('Ressource ajoutée', `Bibliothèque ${r.f === 'ALL' ? 'toutes formations' : r.f} — ${r.t}`); closeModal(); rerender();
  toast(r.pub ? `« ${esc(r.t)} » est publiée${$('#lf-n')?.checked ? ' et les étudiants sont prévenus' : ''}.` : `« ${esc(r.t)} » est enregistrée en brouillon.`);
};

/* ---------- 3. Publier, supprimer, filtrer ---------- */
ACT['lib-pub'] = el => { const r = S.lib.find(x => x.id === el.dataset.id); r.pub = r.pub === false;
  audit(r.pub ? 'Ressource publiée' : 'Ressource masquée', `Bibliothèque — ${r.t}`); rerender();
  toast(r.pub ? 'Ressource publiée : les étudiants la voient.' : 'Ressource repassée en brouillon : elle est masquée pour les étudiants.', r.pub ? 'ok' : 'info'); };
ACT['lib-del'] = el => { const r = S.lib.find(x => x.id === el.dataset.id);
  confirmBox('Supprimer la ressource', `« ${esc(r.t)} » sera retirée de la bibliothèque des étudiants. Cette action est définitive.`, 'lib-del-ok', `data-id="${r.id}"`, true); };
ACT['lib-del-ok'] = el => { const i = S.lib.findIndex(x => x.id === el.dataset.id); if (i < 0) return;
  const [r] = S.lib.splice(i, 1); audit('Ressource supprimée', `Bibliothèque — ${r.t}`); closeModal(); rerender(); toast('Ressource supprimée.'); };

const libAPaint = () => { const z = $('#liba-region'); if (z) z.innerHTML = libAdmRows(); };
INP['liba-q'] = el => { S.ui.libA.q = el.value; libAPaint(); };
INP['liba-f'] = el => { S.ui.libA[el.dataset.k] = el.value; libAPaint(); };

/* ---------- 4. Raccordements : menus et droits ---------- */
(() => {
  ADMIN_MODULES.push(['a-bibliotheque', 'Bibliothèque', 'fa-book-bookmark']); ALL_A.push('a-bibliotheque');
  /* Les rôles qui gèrent les documents gèrent aussi la bibliothèque */
  [S.perms, PERM_DEFAULT].forEach(P => Object.values(P).forEach(l => { if (l.includes('a-documents') && !l.includes('a-bibliotheque')) l.push('a-bibliotheque'); }));
  const prog = MENUS.admin.find(g => g[0] === 'Programmes'); if (prog && !prog[1].includes('a-bibliotheque')) prog[1].push('a-bibliotheque');
  const plus = MENUS.form.find(g => g[0] === 'Plus'); if (plus && !plus[1].includes('t-bibliotheque')) plus[1].splice(plus[1].indexOf('t-ressources') + 1, 0, 't-bibliotheque');
})();
