/* MY EVC — comportements communs : thème, notifications « toast », retour en haut de la barre. */
window.evcProjectEditor = (wire, field = 'p.brief') => ({
  editing: true,
  count: 0,
  get sheetCode() {
    return (wire.p?.title || '').match(/^((?:[A-Z]{2,5}-[0-9]{2,})|(?:PM-[A-C]))/)?.[1] || 'PROJET';
  },
  get sheetTitle() {
    const title = (wire.p?.title || '').replace(/^((?:[A-Z]{2,5}-[0-9]{2,})|(?:PM-[A-C]))\s*(?:[—–-]\s*)?/, '').trim();
    return title || 'Nouveau projet';
  },
  init() {
    this.$nextTick(() => {
      this.$refs.body.innerHTML = field.split('.').reduce((value, key) => value?.[key], wire) || '';
      this.count = (this.$refs.body.textContent || '').trim().length;
    });
  },
  sync() {
    wire.$set(field, this.$refs.body.innerHTML, false);
    this.count = (this.$refs.body.textContent || '').trim().length;
  },
  format(command, value = null) {
    this.$refs.body.focus();
    const selection = window.getSelection();
    if (!selection.anchorNode || !this.$refs.body.contains(selection.anchorNode)) return;
    document.execCommand(command, false, value);
    this.sync();
  },
  paste(event) {
    event.preventDefault();
    if (!this.editing) return;
    document.execCommand('insertText', false, event.clipboardData?.getData('text/plain') || '');
    this.sync();
  },
  link() {
    const selection = window.getSelection();
    const range = selection.rangeCount ? selection.getRangeAt(0).cloneRange() : null;
    const value = window.prompt('Adresse du lien (https://… ou mailto:…)');
    if (!value) return;
    let url;
    try { url = new URL(value.trim()); } catch (_) { window.alert('Saisissez une adresse complète valide.'); return; }
    if (!['https:', 'http:', 'mailto:'].includes(url.protocol)) { window.alert('Ce type de lien n’est pas autorisé.'); return; }
    if (range) {
      this.$refs.body.focus();
      selection.removeAllRanges();
      selection.addRange(range);
    }
    this.format('createLink', url.href);
  },
});

(() => {
  let messageAudio;
  let messageSounds = true;
  try { messageSounds = localStorage.getItem('myevc-message-sounds') !== 'off'; } catch (_) {}
  window.evcMessageSoundsEnabled = () => messageSounds;
  const prepareMessageAudio = () => {
    const Audio = window.AudioContext || window.webkitAudioContext;
    if (!Audio) return Promise.reject(new Error('Audio non disponible'));
    try {
      if (!messageAudio || messageAudio.state === 'closed') messageAudio = new Audio();
      const ready = messageAudio.state !== 'running' ? messageAudio.resume() : Promise.resolve();
      const unlock = messageAudio.createBufferSource();
      unlock.buffer = messageAudio.createBuffer(1, 1, messageAudio.sampleRate);
      unlock.connect(messageAudio.destination);
      unlock.onended = () => unlock.disconnect();
      unlock.start();
      return ready;
    } catch (error) { return Promise.reject(error); }
  };
  ['click', 'keydown', 'touchend'].forEach(event => document.addEventListener(event, e => {
    if (e.target.closest('.msg-app')) prepareMessageAudio().catch(() => {});
  }, {capture: true, passive: true}));
  const playMessageSound = async (received = false) => {
    await prepareMessageAudio();
    if (messageAudio.state !== 'running') throw new Error('Audio bloqué');
    const start = messageAudio.currentTime + .02;
    (received ? [880, 660, 880] : [660, 880]).forEach((frequency, i) => {
      const tone = messageAudio.createOscillator();
      const volume = messageAudio.createGain();
      const at = start + i * .12;
      tone.type = 'sine';
      tone.frequency.setValueAtTime(frequency, at);
      volume.gain.setValueAtTime(0, at);
      volume.gain.linearRampToValueAtTime(.18, at + .01);
      volume.gain.exponentialRampToValueAtTime(.001, at + .12);
      tone.connect(volume);
      volume.connect(messageAudio.destination);
      tone.start(at);
      tone.stop(at + .13);
      tone.onended = () => { tone.disconnect(); volume.disconnect(); };
    });
  };
  const soundError = () => window.evcToast('Son indisponible : autorisez l’audio pour ce site et vérifiez le volume de votre appareil.', 'warn');
  window.addEventListener('message-envoye', () => { if (messageSounds) playMessageSound().catch(soundError); });
  window.addEventListener('message-recu', () => { if (messageSounds) playMessageSound(true).catch(soundError); });
  window.evcToggleMessageSounds = () => {
    messageSounds = !messageSounds;
    try { localStorage.setItem('myevc-message-sounds', messageSounds ? 'on' : 'off'); } catch (_) {}
    window.dispatchEvent(new CustomEvent('message-sound-setting', {detail: {enabled: messageSounds}}));
    if (messageSounds) playMessageSound().catch(soundError);
  };
  document.addEventListener('click', e => {
    if (e.target.closest('[data-message-sound-test]')) playMessageSound().catch(soundError);
  });
  const THEMES = ['auto', 'dark', 'light'];
  let selectedTheme = 'auto';
  const applyTheme = t => {
    const html = document.documentElement;
    if (t === 'auto') html.removeAttribute('data-theme'); else html.setAttribute('data-theme', t);
    document.querySelectorAll('[data-theme-toggle]').forEach(button => {
      button.classList.toggle('active', t === 'dark');
      button.setAttribute('aria-pressed', String(t === 'dark'));
      button.setAttribute('title', `Thème : ${({auto: 'automatique', dark: 'sombre', light: 'clair'})[t]}`);
    });
  };
  const restoreTheme = () => {
    try { const saved = localStorage.getItem('myevc-theme'); if (THEMES.includes(saved)) selectedTheme = saved; } catch (_) {}
    applyTheme(selectedTheme);
  };
  restoreTheme();
  document.addEventListener('DOMContentLoaded', restoreTheme);

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
    const next = THEMES[(THEMES.indexOf(selectedTheme) + 1) % THEMES.length];
    selectedTheme = next;
    try { localStorage.setItem('myevc-theme', next); } catch (_) {}
    applyTheme(next);
    evcToast(`Thème : ${({auto: 'automatique', dark: 'sombre', light: 'clair'})[next]}`, 'info');
  });

  /* Messages flash du serveur et événements Livewire */
  const flash = () => document.querySelectorAll('[data-flash-toast]').forEach(el => { evcToast(el.dataset.flashToast); el.remove(); });
  document.addEventListener('DOMContentLoaded', flash);
  document.addEventListener('livewire:navigated', () => {
    restoreTheme();
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
