/* EVC · scripts communs */
(()=>{
const $=(s,c=document)=>c.querySelector(s), $$=(s,c=document)=>[...c.querySelectorAll(s)];
const WA='2250747259507';
const reduce=matchMedia('(prefers-reduced-motion: reduce)').matches;

/* Navbar, progression, retour en haut */
const nav=$('#mainNav'), prog=$('#progressTop'), top=$('#toTop');
const onScroll=()=>{const y=scrollY;nav&&nav.classList.toggle('shrink',y>40);top&&top.classList.toggle('show',y>700);
  if(prog) prog.style.width=(y/Math.max(1,document.documentElement.scrollHeight-innerHeight)*100)+'%'};
addEventListener('scroll',onScroll,{passive:true});onScroll();
top&&top.addEventListener('click',()=>scrollTo({top:0,behavior:'smooth'}));

/* Lien actif */
const page=document.body.dataset.page;
$$('[data-nav]').forEach(a=>{if(a.dataset.nav===page) a.classList.add('active')});

/* Menus déroulants au survol (desktop) */
const mq=matchMedia('(min-width:992px) and (hover:hover)');
$$('.navbar-evc .dropdown').forEach(dd=>{const t=$('[data-bs-toggle="dropdown"]',dd);if(!t)return;let tm;
  const inst=()=>bootstrap.Dropdown.getOrCreateInstance(t);
  dd.addEventListener('mouseenter',()=>{if(!mq.matches)return;clearTimeout(tm);inst().show()});
  dd.addEventListener('mouseleave',()=>{if(!mq.matches)return;tm=setTimeout(()=>inst().hide(),160)});
  t.addEventListener('click',e=>{if(mq.matches&&t.getAttribute('href')&&t.getAttribute('href')!=='#'){location.href=t.getAttribute('href')}});
});

/* Apparition + compteurs */
const fmt=n=>String(n).replace(/\B(?=(\d{3})+(?!\d))/g,' ');
function count(el){if(el.dataset.done)return;el.dataset.done=1;const to=+el.dataset.count,pre=el.dataset.prefix||'',t0=performance.now(),dur=reduce?1:1800;
  (function f(t){const k=Math.min(1,(t-t0)/dur),e=1-Math.pow(1-k,4);el.textContent=pre+fmt(Math.round(to*e));if(k<1)requestAnimationFrame(f)})(t0)}
const io=new IntersectionObserver(es=>es.forEach(e=>{if(!e.isIntersecting)return;const el=e.target;el.classList.add('in');
  $$('[data-count]',el).forEach(count); if(el.dataset.count) count(el);
  $$('[data-ratio]',el).forEach(b=>setTimeout(()=>b.style.width=b.dataset.ratio,250));
  io.unobserve(el)}),{threshold:.12});
$$('.reveal,[data-count]:not(.reveal [data-count])').forEach(el=>io.observe(el));

/* Hero slider : barres de progression synchronisées */
const hero=$('#heroCarousel');
if(hero){const dots=$$('.hero-dots button');
  hero.addEventListener('slide.bs.carousel',e=>{dots.forEach((d,i)=>{d.classList.toggle('active',i===e.to);const bar=$('.bar i',d);if(bar){bar.style.animation='none';bar.offsetWidth;bar.style.animation=''}})});
  dots.forEach((d,i)=>d.addEventListener('click',()=>bootstrap.Carousel.getOrCreateInstance(hero).to(i)));
}

/* Sliders horizontaux */
$$('[data-slider]').forEach(w=>{const tr=$('.evc-track',w);if(!tr)return;
  const prev=$('[data-prev]',w),next=$('[data-next]',w),bar=$('.slider-progress i',w);
  const step=()=>{const c=tr.children[0];return c?c.getBoundingClientRect().width+parseFloat(getComputedStyle(tr).gap||24):300};
  const upd=()=>{const max=tr.scrollWidth-tr.clientWidth;if(prev)prev.disabled=tr.scrollLeft<4;if(next)next.disabled=tr.scrollLeft>max-4;
    if(bar){const vis=tr.clientWidth/tr.scrollWidth;bar.style.width=(vis*100)+'%';bar.style.marginLeft=(max>0?(tr.scrollLeft/max)*(100-vis*100):0)+'%'}};
  prev&&prev.addEventListener('click',()=>tr.scrollBy({left:-step(),behavior:'smooth'}));
  next&&next.addEventListener('click',()=>tr.scrollBy({left:step(),behavior:'smooth'}));
  tr.addEventListener('scroll',upd,{passive:true});addEventListener('resize',upd);upd();
  if(w.dataset.slider==='auto'&&!reduce){let t=setInterval(()=>{const max=tr.scrollWidth-tr.clientWidth;if(tr.scrollLeft>max-4)tr.scrollTo({left:0,behavior:'smooth'});else tr.scrollBy({left:step(),behavior:'smooth'})},4500);
    w.addEventListener('mouseenter',()=>clearInterval(t));}
});

/* Filtres galerie */
$$('.filter-bar[data-target]').forEach(fb=>{const grid=$(fb.dataset.target);if(!grid)return;
  $$('button',fb).forEach(b=>b.addEventListener('click',()=>{$$('button',fb).forEach(x=>x.classList.remove('active'));b.classList.add('active');
    const f=b.dataset.filter;$$('.g-item',grid).forEach(it=>it.classList.toggle('hide',f!=='all'&&!it.dataset.cat.split(' ').includes(f)))}))});

/* Lightbox */
const lb=$('#lightbox');
if(lb){const img=$('img',lb),cap=$('.lb-cap',lb);let list=[],idx=0;
  const show=i=>{idx=(i+list.length)%list.length;const it=list[idx];img.src=it.dataset.full||$('img',it).src;img.alt=it.dataset.caption||'';cap.textContent=it.dataset.caption||''};
  document.addEventListener('click',e=>{const it=e.target.closest('[data-lb]');if(!it)return;e.preventDefault();
    list=$$(`[data-lb="${it.dataset.lb}"]`).filter(x=>!x.classList.contains('hide')&&x.offsetParent!==null);
    show(list.indexOf(it));bootstrap.Modal.getOrCreateInstance(lb).show()});
  $('.lb-prev',lb).addEventListener('click',()=>show(idx-1));$('.lb-next',lb).addEventListener('click',()=>show(idx+1));
  document.addEventListener('keydown',e=>{if(!lb.classList.contains('show'))return;if(e.key==='ArrowLeft')show(idx-1);if(e.key==='ArrowRight')show(idx+1)});
}

/* Formulaires → message WhatsApp pré-rempli */
$$('form[data-wa]').forEach(f=>f.addEventListener('submit',e=>{e.preventDefault();
  if(!f.checkValidity()){f.classList.add('was-validated');return}
  const fd=new FormData(f);let lines=[f.dataset.wa];
  fd.forEach((v,k)=>{if(v&&String(v).trim()) lines.push(`${k} : ${v}`)});
  const url=`https://wa.me/${WA}?text=`+encodeURIComponent(lines.join('\n'));
  const ok=$(f.dataset.success);if(ok){f.style.display='none';ok.style.display='block';const a=$('a[data-wa-link]',ok);if(a)a.href=url}
}));
$$('.newsletter').forEach(n=>n.addEventListener('submit',e=>{e.preventDefault();const b=$('button',n);$('input',n).value='';b.textContent='Merci !'}));

/* Pré-sélection de formation via #hash sur la page admissions */
const pre=location.hash.replace('#','');
if(pre){const r=$(`input[name="Formation"][data-key="${pre}"]`);if(r) r.checked=true}

/* Accueil : étape active dans la frise du parcours */
const jmap=$$('.j-map a');
if(jmap.length){const io2=new IntersectionObserver(es=>es.forEach(e=>{if(e.isIntersecting){jmap.forEach(a=>a.classList.toggle('on',a.getAttribute('href')==='#'+e.target.id))}}),{rootMargin:'-45% 0px -45% 0px'});
  $$('.j-step').forEach(s=>io2.observe(s));}

/* Partage d'article */
$$('[data-share]').forEach(sh=>{
  const canonical=sh.dataset.url, here=location.href.split('#')[0];
  const u=/^https?:/.test(here)?here:canonical, t=sh.dataset.title, eu=encodeURIComponent(u), et=encodeURIComponent(t);
  const L={whatsapp:`https://wa.me/?text=${et}%20${eu}`,facebook:`https://www.facebook.com/sharer/sharer.php?u=${eu}`,
    linkedin:`https://www.linkedin.com/sharing/share-offsite/?url=${eu}`,x:`https://twitter.com/intent/tweet?text=${et}&url=${eu}`,
    email:`mailto:?subject=${et}&body=${et}%0A${eu}`};
  $$('[data-net]',sh).forEach(a=>a.href=L[a.dataset.net]);
  const c=$('[data-copy]',sh);
  c&&c.addEventListener('click',async()=>{try{await navigator.clipboard.writeText(u)}catch(e){const i=document.createElement('input');i.value=u;document.body.appendChild(i);i.select();try{document.execCommand('copy')}catch(_){}i.remove()}
    c.classList.add('ok');setTimeout(()=>c.classList.remove('ok'),1800)});
});
$$('.nw-filter button').forEach(b=>b.addEventListener('click',()=>{$$('.nw-filter button').forEach(x=>x.classList.remove('active'));b.classList.add('active');
  $$('.nw-col').forEach(c=>c.classList.toggle('hide',b.dataset.nf!=='all'&&c.dataset.cat!==b.dataset.nf))}));

/* Registre de vérification : copie la référence puis ouvre le registre officiel */
const rf=$('#regForm');
if(rf) rf.addEventListener('submit',async e=>{const v=$('#regRef').value.trim();const hint=$('#regHint');
  if(!v){e.preventDefault();$('#regRef').focus();hint.textContent='Saisissez la référence imprimée en bas du document.';hint.classList.add('err');return}
  e.preventDefault();try{await navigator.clipboard.writeText(v);hint.textContent='Référence copiée : collez-la dans le registre officiel qui vient de s’ouvrir.'}catch(_){hint.textContent='Saisissez la référence « '+v+' » dans le registre officiel qui vient de s’ouvrir.'}
  hint.classList.remove('err');window.open(rf.action,'_blank','noopener')||(location.href=rf.action)});

/* Titre du hero : chaque ligne tient sur une ligne, quelle que soit la police */
const ht=$('.h3-title');
if(ht){const fit=()=>{ht.style.fontSize='';let fs=parseFloat(getComputedStyle(ht).fontSize),i=0;
    const w=ht.parentElement.clientWidth;while(ht.scrollWidth>w&&fs>18&&i++<40){fs-=1;ht.style.setProperty('font-size',fs+'px','important')}};
  fit();(document.fonts&&document.fonts.ready||Promise.resolve()).then(fit);addEventListener('resize',fit);}

/* Tarifs : prix total / en 2 fois */
const prt=$('.pr-toggle');
if(prt){const cards=$('.pr-cards');$$('button',prt).forEach(b=>b.addEventListener('click',()=>{$$('button',prt).forEach(x=>x.classList.toggle('on',x===b));
  prt.classList.toggle('split',b.dataset.pay==='split');cards.dataset.mode=b.dataset.pay}));}

/* Sync onglets de page */
$$('.page-tabs .nav-link').forEach(a=>a.addEventListener('click',()=>{$$('.page-tabs .nav-link').forEach(x=>x.classList.remove('active'));a.classList.add('active')}));
})();
