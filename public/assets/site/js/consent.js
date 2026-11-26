/* EVC · consentement aux cookies et mesure d'audience (GA4, Meta Pixel) */
(()=>{
const C=window.EVC_ANALYTICS||{}, KEY='evc-consent', VERSION=1, MAX_AGE=182*864e5;
const tools={analytics:!!C.ga4, marketing:!!C.pixel};
const $=(s,c=document)=>c.querySelector(s), $$=(s,c=document)=>[...c.querySelectorAll(s)];

const read=()=>{try{const c=JSON.parse(localStorage.getItem(KEY));return c&&c.v===VERSION&&Date.now()-c.t<MAX_AGE?c:null}catch(e){return null}};
let consent=read(), ga=false, px=false;

function loadGA(){if(ga||!C.ga4)return;ga=true;
  window.dataLayer=window.dataLayer||[];window.gtag=function(){dataLayer.push(arguments)};
  gtag('consent','default',{ad_storage:'denied',ad_user_data:'denied',ad_personalization:'denied',analytics_storage:'granted'});
  gtag('js',new Date());gtag('config',C.ga4);
  const s=document.createElement('script');s.async=true;s.src='https://www.googletagmanager.com/gtag/js?id='+encodeURIComponent(C.ga4);document.head.appendChild(s)}
function loadPixel(){if(px||!C.pixel)return;px=true;
  const f=window.fbq=function(){f.callMethod?f.callMethod.apply(f,arguments):f.queue.push(arguments)};
  if(!window._fbq)window._fbq=f;f.push=f;f.loaded=true;f.version='2.0';f.queue=[];
  const s=document.createElement('script');s.async=true;s.src='https://connect.facebook.net/fr_FR/fbevents.js';document.head.appendChild(s);
  fbq('init',C.pixel);fbq('track','PageView')}
function apply(){if(consent&&consent.analytics)loadGA();if(consent&&consent.marketing)loadPixel()}

/* Événements de conversion, transmis uniquement aux outils acceptés */
const META={whatsapp_click:['track','Contact'],generate_lead:['track','Lead'],contact_form_submit:['track','Contact'],newsletter_signup:['track','Subscribe']};
window.evcTrack=(name,params={})=>{
  if(ga&&window.gtag)gtag('event',name,params);
  if(px&&window.fbq&&META[name])fbq(META[name][0],META[name][1],params);
};

/* Clics WhatsApp, avec l'emplacement du lien */
document.addEventListener('click',e=>{const a=e.target.closest('a[href*="wa.me/"]');if(!a)return;
  const zone=a.closest('.topbar')?'barre_haute':a.closest('.wa-float')?'bouton_flottant':a.closest('.m-bar')?'barre_mobile':a.closest('.offcanvas')?'menu_mobile':a.closest('.foot')?'pied_de_page':(a.closest('section[id]')||{}).id||'contenu';
  const num=(a.getAttribute('href').match(/wa\.me\/(\d+)/)||[])[1]||'';
  evcTrack('whatsapp_click',{link_location:zone,page:document.body.dataset.page||'',whatsapp_country:num.startsWith('33')?'FR':num.startsWith('225')?'CI':'autre'})},true);

/* Bandeau de consentement */
if(!tools.analytics&&!tools.marketing)return;
const banner=$('#ckBanner');
$$('[data-ck-open]').forEach(l=>{l.hidden=false;l.addEventListener('click',e=>{e.preventDefault();open(true)})});
if(!banner){apply();return}
$$('[data-ck-tool]',banner).forEach(r=>{if(!tools[r.dataset.ckTool])r.remove()});
function open(custom){$$('input[data-ck]',banner).forEach(i=>i.checked=!!(consent&&consent[i.dataset.ck]));
  $('.ck-prefs',banner).hidden=!custom;$('[data-ck-act="save"]',banner).hidden=!custom;$('[data-ck-act="custom"]',banner).hidden=!!custom;
  banner.hidden=false;requestAnimationFrame(()=>banner.classList.add('show'))}
function save(choice){const before=consent;consent={v:VERSION,t:Date.now(),analytics:!!choice.analytics&&tools.analytics,marketing:!!choice.marketing&&tools.marketing};
  try{localStorage.setItem(KEY,JSON.stringify(consent))}catch(e){}
  banner.classList.remove('show');banner.hidden=true;
  if(before&&((before.analytics&&!consent.analytics)||(before.marketing&&!consent.marketing))){
    document.cookie.split(';').map(c=>c.trim().split('=')[0]).filter(n=>/^(_ga|_gid|_gat|_fbp|_fbc)/.test(n)).forEach(n=>{
      const host=location.hostname.replace(/^www\./,'');[host,'.'+host,location.hostname].forEach(d=>document.cookie=`${n}=; Max-Age=0; path=/; domain=${d}`);document.cookie=`${n}=; Max-Age=0; path=/`});
    location.reload();return}
  apply()}
banner.addEventListener('click',e=>{const b=e.target.closest('[data-ck-act]');if(!b)return;
  const act=b.dataset.ckAct;
  if(act==='accept')save({analytics:true,marketing:true});
  else if(act==='refuse')save({analytics:false,marketing:false});
  else if(act==='custom')open(true);
  else if(act==='save'){const v={};$$('input[data-ck]',banner).forEach(i=>v[i.dataset.ck]=i.checked);save(v)}});
if(consent)apply();else open(false);
})();
