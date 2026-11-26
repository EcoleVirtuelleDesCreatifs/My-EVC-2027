const assert = require('node:assert/strict');
const { test } = require('node:test');
const fs = require('node:fs');
const vm = require('node:vm');

const source = fs.readFileSync('public/assets/site/js/consent.js', 'utf8');

function el(extra = {}) {
  const listeners = {};
  return Object.assign({
    hidden: false, checked: false, dataset: {}, children: {},
    classList: { set: new Set(), add(c) { this.set.add(c); }, remove(c) { this.set.delete(c); } },
    addEventListener(type, fn) { listeners[type] = fn; },
    fire(type, event) { listeners[type](event); },
    querySelector(s) { return this.children[s] || null; },
    querySelectorAll(s) { return this.children[s + '*'] || []; },
    remove() { this.removed = true; },
  }, extra);
}

function page({ ga4 = 'G-TEST1234', pixel = '123456789', stored = null } = {}) {
  const store = new Map(stored ? [['evc-consent', JSON.stringify(stored)]] : []);
  const inputs = { analytics: el({ dataset: { ck: 'analytics' } }), marketing: el({ dataset: { ck: 'marketing' } }) };
  const banner = el({ hidden: true });
  banner.children = {
    '.ck-prefs': el({ hidden: true }), '[data-ck-act="save"]': el(), '[data-ck-act="custom"]': el(),
    '[data-ck-tool]*': [el({ dataset: { ckTool: 'analytics' } }), el({ dataset: { ckTool: 'marketing' } })],
    'input[data-ck]*': Object.values(inputs),
  };
  const scripts = [];
  const doc = el({ cookie: '', body: { dataset: { page: 'accueil' } }, head: { appendChild: (s) => scripts.push(s.src) }, createElement: () => ({}) });
  doc.children = { '#ckBanner': banner, '[data-ck-open]*': [] };
  const window = { EVC_ANALYTICS: Object.fromEntries(Object.entries({ ga4, pixel }).filter(([, v]) => v)) };
  const context = vm.createContext({
    window, document: doc, Date, JSON, encodeURIComponent,
    localStorage: { getItem: (k) => store.get(k) ?? null, setItem: (k, v) => store.set(k, v) },
    requestAnimationFrame: (fn) => fn(), location: { hostname: 'www.example.ci', reload() { window.reloaded = true; } },
  });
  context.window = context;
  Object.assign(context, window);
  vm.runInContext(source, context);
  const click = (act) => banner.fire('click', { target: { closest: () => ({ dataset: { ckAct: act } }) } });
  return { context, banner, scripts, store, inputs, click };
}

test('aucun outil de mesure n’est chargé avant le choix du visiteur', () => {
  const p = page();
  assert.equal(p.banner.hidden, false);
  assert.deepEqual(p.scripts, []);
  p.context.evcTrack('generate_lead', { formation: 'DG' });
  assert.equal(p.context.dataLayer, undefined);
});

test('le refus est mémorisé sans charger GA4 ni Meta Pixel', () => {
  const p = page();
  p.click('refuse');
  assert.deepEqual(p.scripts, []);
  assert.equal(p.banner.hidden, true);
  assert.equal(JSON.parse(p.store.get('evc-consent')).analytics, false);
});

test('l’acceptation charge les deux outils et transmet les conversions', () => {
  const p = page();
  p.click('accept');
  assert.equal(p.scripts.length, 2);
  assert.match(p.scripts[0], /googletagmanager\.com\/gtag\/js\?id=G-TEST1234/);
  assert.match(p.scripts[1], /connect\.facebook\.net/);
  p.context.evcTrack('generate_lead', { formation: 'DG' });
  const events = p.context.dataLayer.map((args) => Array.from(args));
  assert.ok(events.some(([type, name]) => type === 'event' && name === 'generate_lead'));
  assert.ok(p.context.fbq.queue.some((args) => args[0] === 'track' && args[1] === 'Lead'));
});

test('le choix personnalisé n’active que l’outil coché', () => {
  const p = page();
  p.click('custom');
  assert.equal(p.banner.children['.ck-prefs'].hidden, false);
  p.inputs.analytics.checked = true;
  p.click('save');
  assert.equal(p.scripts.length, 1);
  assert.match(p.scripts[0], /googletagmanager/);
});

test('un choix enregistré est réappliqué sans réafficher le bandeau', () => {
  const p = page({ stored: { v: 1, t: Date.now(), analytics: false, marketing: true } });
  assert.equal(p.banner.hidden, true);
  assert.deepEqual(p.scripts.map((s) => /facebook/.test(s)), [true]);
});

test('sans identifiant configuré, aucun bandeau ni script n’est activé', () => {
  const p = page({ ga4: null, pixel: null });
  assert.equal(p.banner.hidden, true);
  assert.deepEqual(p.scripts, []);
});
