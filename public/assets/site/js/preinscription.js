/* EVC · Préinscription en 6 étapes */
(()=>{
const $=(s,c=document)=>c.querySelector(s), $$=(s,c=document)=>[...c.querySelectorAll(s)];
const form=$('#piForm'); if(!form) return;
const steps=$$('.pi-step',form), dots=$$('#piStepper li'), bar=$('#piBar');
const prev=$('#piPrev'), next=$('#piNext'), submit=$('#piSubmit'), saved=$('#piSaved');
const KEY='evc-preinscription'; const files={};
let cur=0;

/* Pré-sélection depuis l'URL (#design, #cm, #gia) */
const h=location.hash.slice(1); const pre=$(`input[data-key="${h}"]`); if(pre) pre.checked=true;

/* Brouillon (sans les fichiers) */
function save(){try{const d={};new FormData(form).forEach((v,k)=>{if(v instanceof File)return;(d[k]=d[k]||[]).push(v)});localStorage.setItem(KEY,JSON.stringify(d));saved.classList.add('on');clearTimeout(save.t);save.t=setTimeout(()=>saved.classList.remove('on'),1400)}catch(e){}}
function load(){try{const d=JSON.parse(localStorage.getItem(KEY)||'null');if(!d)return;
  Object.entries(d).forEach(([k,vals])=>{$$(`[name="${CSS.escape(k)}"]`,form).forEach(el=>{
    if(el.type==='checkbox'||el.type==='radio'){if(vals.includes(el.value)) el.checked=true}
    else if(el.type!=='file') el.value=vals[0]})})}catch(e){}}
load(); if(pre) pre.checked=true;
form.addEventListener('input',()=>{save();sync()}); form.addEventListener('change',()=>{save();sync()});

/* Affichage conditionnel */
function cond(){$$('[data-show-if]',form).forEach(el=>{const [n,vals]=el.dataset.showIf.split('=');const v=(form.querySelector(`[name="${n}"]:checked`)||{}).value;el.hidden=!vals.split('|').includes(v)})}

/* Sélection (encart latéral) */
function sync(){cond();
  const sel=$$('input[name="Formations"]:checked',form);
  $('#piSel').innerHTML=sel.length?sel.map(i=>`<div class="pi-sel-i"><b>${i.value}</b><span>${i.dataset.price} F</span></div>`).join(''):'<span class="text-muted-evc small">Aucune formation choisie.</span>';
  const tot=sel.reduce((a,i)=>a+parseInt(i.dataset.price.replace(/\D/g,'')),0);
  $('#piTotal').hidden=!sel.length; $('#piTotal b').textContent=tot.toLocaleString('fr-FR').replace(/ | /g,' ')+' FCFA';
  $('.pi-err[data-for="Formations"]').classList.remove('on');
}

/* Navigation */
function show(i){cur=i;steps.forEach((s,k)=>s.hidden=k!==i);
  dots.forEach((d,k)=>{d.classList.toggle('on',k===i);d.classList.toggle('done',k<i)});
  bar.style.width=((i)/(steps.length-1)*100)+'%';
  prev.hidden=i===0; next.hidden=i===steps.length-1; submit.hidden=i!==steps.length-1;
  if(i===steps.length-1) recap();
  const ms=$('#piMstep'); if(ms) ms.textContent=`Étape ${i+1} sur ${steps.length} · ${dots[i].querySelector('span').textContent}`;
  const top=$('#piStepper').getBoundingClientRect().top+scrollY-110; if(scrollY>top) scrollTo({top,behavior:'smooth'});
  const f=steps[i].querySelector('input:not([type=file]):not([type=checkbox]):not([type=radio]),select,textarea'); }
function valid(i){const s=steps[i];let ok=true;
  $$('input,select,textarea',s).forEach(el=>{if(el.closest('[hidden]'))return;
    if(el.type==='radio'){const g=$$(`[name="${CSS.escape(el.name)}"]`,s);if(el.required&&!g.some(x=>x.checked)){ok=false;el.closest('.chips-in').classList.add('bad')}else el.closest('.chips-in')?.classList.remove('bad');return}
    if(!el.checkValidity()){ok=false;el.classList.add('is-invalid')}else el.classList.remove('is-invalid')});
  $$('[data-need-one]',s).forEach(g=>{if(!$$('input:checked',g).length){ok=false;$(`.pi-err[data-for="${g.dataset.needOne}"]`,s).classList.add('on')}});
  if(!ok){const b=s.querySelector('.is-invalid,.bad,.pi-err.on');b&&b.scrollIntoView({behavior:'smooth',block:'center'})}
  return ok}
next.addEventListener('click',()=>{if(valid(cur)) show(cur+1)});
prev.addEventListener('click',()=>show(cur-1));
dots.forEach((d,k)=>d.addEventListener('click',()=>{if(k<cur) show(k); else if(k===cur+1&&valid(cur)) show(k)}));
form.addEventListener('input',e=>{if(e.target.classList.contains('is-invalid')&&e.target.checkValidity()) e.target.classList.remove('is-invalid')});
form.addEventListener('change',e=>{if(e.target.type==='radio') e.target.closest('.chips-in')?.classList.remove('bad')});

/* Fichiers : aperçu, glisser-déposer, suppression */
const size=b=>b>1048576?(b/1048576).toFixed(1)+' Mo':Math.round(b/1024)+' Ko';
function render(name){const list=files[name]||[];const inp=form.querySelector(`input[type=file][name="${CSS.escape(name)}"]`);const mode=inp.dataset.preview;
  if(mode==='avatar'){const img=$('#avatarUp img'),ph=$('#avatarUp .av-ph');if(list[0]){img.src=URL.createObjectURL(list[0]);img.hidden=false;ph.hidden=true}else{img.hidden=true;ph.hidden=false}return}
  const box=form.querySelector(mode==='grid'?`[data-thumbs="${CSS.escape(name)}"]`:`[data-files="${CSS.escape(name)}"]`);
  box.innerHTML=list.map((f,i)=>mode==='grid'
    ?`<figure><img src="${URL.createObjectURL(f)}" alt=""><button type="button" data-rm="${name}" data-i="${i}" aria-label="Retirer">×</button></figure>`
    :`<div class="file-i"><span>${f.type.includes('pdf')?'PDF':'IMG'}</span><b>${f.name}</b><small>${size(f.size)}</small><button type="button" data-rm="${name}" data-i="${i}" aria-label="Retirer">×</button></div>`).join('')}
function addFiles(inp,list){const name=inp.name,max=+inp.dataset.max||(inp.multiple?10:1);
  let arr=inp.multiple?(files[name]||[]).concat([...list]):[...list].slice(0,1);
  arr=arr.filter(f=>f.size<=10*1048576).slice(0,max);files[name]=arr;render(name)}
$$('input[type=file]',form).forEach(inp=>inp.addEventListener('change',()=>{addFiles(inp,inp.files);inp.value=''}));
$$('[data-drop]',form).forEach(z=>{const inp=$('input',z);
  ['dragenter','dragover'].forEach(ev=>z.addEventListener(ev,e=>{e.preventDefault();z.classList.add('over')}));
  ['dragleave','drop'].forEach(ev=>z.addEventListener(ev,e=>{e.preventDefault();z.classList.remove('over')}));
  z.addEventListener('drop',e=>addFiles(inp,e.dataTransfer.files))});
form.addEventListener('click',e=>{const b=e.target.closest('[data-rm]');if(!b)return;e.preventDefault();files[b.dataset.rm].splice(+b.dataset.i,1);render(b.dataset.rm)});

/* Récapitulatif */
function data(){const d={};new FormData(form).forEach((v,k)=>{if(v instanceof File||!String(v).trim())return;(d[k]=d[k]||[]).push(v)});return d}
const GROUPS=[["Formations",["Formations","Modalité","Disponibilités"]],["Identité",["Nom","Prénoms","Genre","Date de naissance","Nationalité","Téléphone","E-mail","Pays","Ville","Commune"]],
 ["Profil",["Situation","Niveau d'études","Établissement","Poste","Niveau dans le domaine","Outils","Ordinateur","Connexion","Objectif","Source","Motivation"]],
 ["Portfolio",["Lien portfolio"]],["Personne à contacter",["Contact - Nom","Contact - Lien","Contact - Téléphone","Contact - E-mail","Contact - Fonction","Contact - Résidence","Contact - Prend en charge les frais"]]];
function recap(){const d=data();const fl=Object.entries(files).filter(([k,v])=>v.length);
  $('#piRecap').innerHTML=GROUPS.map(([g,keys],gi)=>{const rows=keys.filter(k=>d[k]).map(k=>`<div><span>${k.replace('Contact - ','')}</span><b>${d[k].join(', ')}</b></div>`).join('');
    const extra=g==='Portfolio'&&fl.length?fl.map(([k,v])=>`<div><span>${k}</span><b>${v.length} fichier${v.length>1?'s':''}</b></div>`).join(''):'';
    const step=[0,1,2,3,4][gi];
    return (rows||extra)?`<section><h6>${g}<button type="button" data-goto="${step}">Modifier</button></h6>${rows}${extra}</section>`:''}).join('')}
$('#piRecap').addEventListener('click',e=>{const b=e.target.closest('[data-goto]');if(b)show(+b.dataset.goto)});

/* Envoi : le dossier est enregistré sur le serveur (référence PRE-AAAA-NNNN), puis proposé sur WhatsApp */
form.addEventListener('submit',async e=>{e.preventDefault();if(!valid(cur))return;
  const btn=form.querySelector('[type=submit]'),label=btn?btn.innerHTML:'';
  const d=data(),fd=new FormData();
  Object.entries(d).forEach(([k,vals])=>vals.forEach(v=>fd.append(`answers[${k}][]`,v)));
  Object.entries(files).forEach(([k,list])=>list.forEach(f=>fd.append(`fichiers[${k}][]`,f,f.name)));
  if(btn){btn.disabled=true;btn.textContent='Envoi en cours…'}
  let res;
  try{const r=await fetch('/preinscription',{method:'POST',body:fd,headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'}});
    res=await r.json();if(!r.ok) throw res}
  catch(err){if(btn){btn.disabled=false;btn.innerHTML=label}
    const msg=err&&err.errors?Object.values(err.errors).flat().join(' '):"L'envoi n'a pas abouti. Vérifiez votre connexion puis réessayez.";
    let box=$('#piErr');if(!box){box=document.createElement('div');box.id='piErr';box.className='alert alert-danger mt-3';form.appendChild(box)}box.textContent=msg;return}
  const lines=[`Bonjour EVC, voici ma PRÉINSCRIPTION (référence ${res.reference}) :`,''];
  GROUPS.forEach(([g,keys])=>{const r=keys.filter(k=>d[k]).map(k=>`• ${k} : ${d[k].join(', ')}`);if(r.length){lines.push(`*${g}*`,...r,'')}});
  const fl=Object.entries(files).filter(([k,v])=>v.length);
  if(fl.length) lines.push('*Documents joints*',...fl.map(([k,v])=>`• ${k} : ${v.map(f=>f.name).join(', ')}`),'(déjà joints à mon dossier en ligne)');
  if(d["Droit à l'image"]) lines.push('',"Droit à l'image : accepté");
  $('#piWa').href='https://wa.me/'+(document.body.dataset.wa||'2250747259507')+'?text='+encodeURIComponent(lines.join('\n'));
  $('#piName').textContent=(d['Prénoms']||[''])[0];
  $('#piRef').textContent=res.reference;$('#piSuivi').href=res.suivi;
  $('#piFilesNote').hidden=!fl.length;
  form.hidden=true;$('#piStepper').hidden=true;$('.pi-bar').hidden=true;$('#piDone').hidden=false;
  try{localStorage.removeItem(KEY)}catch(e){}
  scrollTo({top:$('#piDone').getBoundingClientRect().top+scrollY-140,behavior:'smooth'})});

show(0); sync();
})();
