/* MY EVC — comportements communs : thème, notifications « toast », retour en haut de la barre. */
(() => {
  const html = document.documentElement;
  const THEMES = ['auto', 'dark', 'light'];
  const applyTheme = t => { if (t === 'auto') html.removeAttribute('data-theme'); else html.setAttribute('data-theme', t); };

  /* Toast Bootstrap. kind : ok | warn | bad | info */
  window.evcToast = (msg, kind = 'ok') => {
    const icon = {ok: 'fa-circle-check', warn: 'fa-triangle-exclamation', bad: 'fa-circle-xmark', info: 'fa-circle-info'}[kind] || 'fa-circle-check';
    const box = document.getElementById('toasts'); if (!box || !window.bootstrap) return;
    const el = document.createElement('div');
    el.className = 'toast align-items-center border-0'; el.setAttribute('role', 'status');
    el.innerHTML = `<div class="d-flex"><div class="toast-body d-flex gap-2 align-items-center"><i class="fa-solid ${icon}" style="color:var(--accent)"></i><span></span></div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button></div>`;
    el.querySelector('span').innerHTML = msg;
    box.appendChild(el); const t = new bootstrap.Toast(el, {delay: 3600}); t.show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
  };

  document.addEventListener('click', e => {
    const b = e.target.closest('[data-theme-toggle]'); if (!b) return;
    let cur = 'auto'; try { cur = localStorage.getItem('myevc-theme') || 'auto'; } catch (_) {}
    const next = THEMES[(THEMES.indexOf(cur) + 1) % THEMES.length];
    try { localStorage.setItem('myevc-theme', next); } catch (_) {}
    applyTheme(next);
    evcToast(`Thème : ${({auto: 'automatique', dark: 'sombre', light: 'clair'})[next]}`, 'info');
  });

  /* Messages flash du serveur et événements Livewire */
  const flash = () => document.querySelectorAll('[data-flash-toast]').forEach(el => { evcToast(el.dataset.flashToast); el.remove(); });
  document.addEventListener('DOMContentLoaded', flash);
  document.addEventListener('livewire:navigated', () => {
    flash();
    document.querySelectorAll('.offcanvas-backdrop').forEach(b => b.remove());
    document.body.style.overflow = '';
    const t = document.querySelector('.topbar'); if (t) t.classList.toggle('scrolled', window.scrollY > 4);
  });
  document.addEventListener('livewire:init', () => {
    Livewire.on('toast', ({message, kind}) => evcToast(message, kind || 'ok'));
    Livewire.on('open-url', ({url}) => { const w = window.open(url, '_blank', 'noopener'); if (!w) location.href = url; });
  });
  window.addEventListener('scroll', () => { const t = document.querySelector('.topbar'); if (t) t.classList.toggle('scrolled', window.scrollY > 4); }, {passive: true});
})();

/* Boutons d'action de la barre du haut : ils sont rendus hors du composant Livewire de la page,
   on relaie donc le clic (wire:click="methode" ou "methode(args)") vers ce composant. */
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.topbar [wire\\:click], .t-cta[wire\\:click]');
  if (!btn || btn.closest('#view') || !window.Livewire) return;
  const root = document.querySelector('#view [wire\\:id]');
  if (!root) return;
  e.preventDefault();
  const m = btn.getAttribute('wire:click').trim().match(/^([\w$]+)\s*(?:\((.*)\))?$/s);
  if (!m) return;
  let args = [];
  try { args = m[2] ? JSON.parse('[' + m[2].replace(/'/g, '"') + ']') : []; } catch (_) { args = []; }
  const wire = Livewire.find(root.getAttribute('wire:id'));
  if (m[1] === '$set') wire.$set(...args); else wire.$call(m[1], ...args);
});
