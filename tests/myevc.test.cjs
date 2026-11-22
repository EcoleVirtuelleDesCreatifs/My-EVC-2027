const test = require('node:test');
const assert = require('node:assert/strict');
const { readFileSync } = require('node:fs');
const { resolve } = require('node:path');
const vm = require('node:vm');

const source = readFileSync(resolve(__dirname, '../public/assets/app/js/myevc.js'), 'utf8');
const settle = () => new Promise(resolve => setImmediate(resolve));

function browser({ blocked = false, soundSetting = null, theme = null } = {}) {
  const documentEvents = {}, windowEvents = {}, contexts = [], warnings = [];
  const stored = new Map(soundSetting === null ? [] : [['myevc-message-sounds', soundSetting]]);
  if (theme) stored.set('myevc-theme', theme);
  const attributes = new Map();
  const html = { setAttribute: (key, value) => attributes.set(key, value), removeAttribute: key => attributes.delete(key) };
  const on = events => (name, callback) => (events[name] ||= []).push(callback);
  class AudioContext {
    constructor() {
      this.state = 'suspended';
      this.currentTime = 0;
      this.sampleRate = 44100;
      this.destination = {};
      this.tones = [];
      this.unlocks = 0;
      contexts.push(this);
    }
    async resume() {
      if (blocked) throw new Error('NotAllowedError');
      this.state = 'running';
    }
    createBuffer() { return {}; }
    createBufferSource() {
      return { connect() {}, disconnect() {}, start: () => this.unlocks++ };
    }
    createGain() {
      return { connect() {}, disconnect() {}, gain: { setValueAtTime() {}, linearRampToValueAtTime() {}, exponentialRampToValueAtTime() {} } };
    }
    createOscillator() {
      const tone = { connect() {}, disconnect() {}, frequency: { setValueAtTime() {} }, start() { this.started = true; }, stop() { this.stopped = true; } };
      this.tones.push(tone);
      return tone;
    }
  }
  const document = {
    documentElement: html, body: { style: {} }, addEventListener: on(documentEvents), getElementById: () => null,
    querySelector: () => null, querySelectorAll: () => [],
  };
  const window = { AudioContext, addEventListener: on(windowEvents), dispatchEvent: event =>
    (windowEvents[event.type] || []).forEach(callback => callback(event)) };
  const localStorage = { getItem: key => stored.get(key) ?? null, setItem: (key, value) => stored.set(key, value) };
  class CustomEvent { constructor(type, options) { this.type = type; this.detail = options.detail; } }
  vm.runInNewContext(source, { document, window, localStorage, CustomEvent });
  window.evcToast = (...args) => warnings.push(args);
  return { contexts, warnings, stored, attributes,
    navigate: () => { html.removeAttribute('data-theme'); documentEvents['livewire:navigated'].forEach(callback => callback()); },
    toggle: window.evcToggleMessageSounds,
    received: () => window.dispatchEvent({type: 'message-recu'}),
    sent: () => windowEvents['message-envoye'].forEach(callback => callback()),
    click: testButton => documentEvents.click.forEach(callback => callback({ target: { closest: selector =>
      selector === '.msg-app' || (testButton && selector === '[data-message-sound-test]') ? {} : null } })),
  };
}

test('le mode sombre est restaure apres chaque navigation Livewire', () => {
  const b = browser({theme: 'dark'});
  assert.equal(b.attributes.get('data-theme'), 'dark');
  b.navigate();
  b.navigate();
  assert.equal(b.attributes.get('data-theme'), 'dark');
  assert.equal(b.stored.get('myevc-theme'), 'dark');
});

test('le mode clair reste egalement stable pendant la navigation', () => {
  const b = browser({theme: 'light'});
  b.navigate();
  assert.equal(b.attributes.get('data-theme'), 'light');
});

test('un envoi initialise et reprend un contexte audio suspendu avant de jouer', async () => {
  const b = browser();
  b.sent();
  await settle();
  assert.equal(b.contexts.length, 1);
  assert.equal(b.contexts[0].state, 'running');
  assert.equal(b.contexts[0].tones.length, 2);
  assert.ok(b.contexts[0].tones.every(tone => tone.started && tone.stopped));
  assert.equal(b.warnings.length, 0);
});

test('une interaction debloque le contexte et les envois le reutilisent', async () => {
  const b = browser();
  b.click(false);
  await settle();
  assert.equal(b.contexts[0].tones.length, 0);
  assert.ok(b.contexts[0].unlocks > 0);
  b.sent();
  b.sent();
  await settle();
  assert.equal(b.contexts.length, 1);
  assert.equal(b.contexts[0].tones.length, 4);
});

test('le bouton de test joue sans envoyer de message', async () => {
  const b = browser();
  b.click(true);
  await settle();
  assert.equal(b.contexts[0].tones.length, 2);
});

test('une reception joue une sonnerie distincte', async () => {
  const b = browser();
  b.received();
  await settle();
  assert.equal(b.contexts[0].tones.length, 3);
});

test('le reglage coupe envoi et reception et memorise la reactivation', async () => {
  const b = browser({ soundSetting: 'off' });
  b.sent();
  b.received();
  await settle();
  assert.equal(b.contexts.length, 0);
  b.toggle();
  await settle();
  assert.equal(b.stored.get('myevc-message-sounds'), 'on');
  assert.equal(b.contexts[0].tones.length, 2);
  b.received();
  await settle();
  assert.equal(b.contexts[0].tones.length, 5);
  b.toggle();
  b.sent();
  b.received();
  await settle();
  assert.equal(b.stored.get('myevc-message-sounds'), 'off');
  assert.equal(b.contexts[0].tones.length, 5);
});

test('un navigateur qui refuse le son declenche une alerte', async () => {
  const b = browser({ blocked: true });
  b.sent();
  await settle();
  assert.equal(b.contexts[0].tones.length, 0);
  assert.equal(b.warnings.length, 1);
  assert.equal(b.warnings[0][1], 'warn');
});

function projectEditor(field = 'p.brief') {
  const commands = [], changes = [], alerts = [];
  const body = { innerHTML: '', textContent: 'Consignes', focus() {}, contains: () => true };
  const wire = { ...(field === 'n.body' ? { n: { body: '<p>Actualité</p>' } } : { p: { brief: '<p>Consignes</p>' } }), $set: (...args) => changes.push(args) };
  const document = { execCommand: (...args) => commands.push(args) };
  const window = { getSelection: () => ({ anchorNode: body }), alert: text => alerts.push(text) };
  vm.runInNewContext(source.slice(0, source.indexOf('\n(() => {')), { window, document, URL });
  const editor = window.evcProjectEditor(wire, field);
  editor.$refs = { body };
  editor.$nextTick = callback => callback();
  editor.init();
  return { editor, body, wire, window, commands, changes, alerts };
}

test('l’editeur synchronise le texte des actualites sans dependre des champs projet', () => {
  const b = projectEditor('n.body');
  assert.equal(b.body.innerHTML, '<p>Actualité</p>');
  assert.doesNotThrow(() => Object.entries(b.editor));
  b.body.innerHTML = '<h2>Notre école</h2><p>Bienvenue</p>';
  b.editor.sync();
  assert.deepEqual(b.changes[0], ['n.body', b.body.innerHTML, false]);
});

test('l’editeur initialise les consignes existantes et les synchronise sans requete immediate', () => {
  const b = projectEditor();
  assert.equal(b.body.innerHTML, '<p>Consignes</p>');
  b.body.innerHTML = '<h2>Livrables</h2><p>Deux visuels</p>';
  b.editor.sync();
  assert.deepEqual(b.changes[0], ['p.brief', b.body.innerHTML, false]);
  b.editor.format('bold');
  assert.deepEqual(b.commands[0], ['bold', false, null]);
});

test('le collage dans les consignes utilise uniquement le texte simple', () => {
  const b = projectEditor();
  let prevented = false;
  b.editor.paste({ preventDefault() { prevented = true; }, clipboardData: { getData: type => type === 'text/plain' ? 'Texte colle' : '<img src=x onerror=alert(1)>' } });
  assert.ok(prevented);
  assert.deepEqual(b.commands[0], ['insertText', false, 'Texte colle']);
  b.editor.editing = false;
  b.editor.paste({ preventDefault() {}, clipboardData: { getData: () => 'ignore' } });
  assert.equal(b.commands.length, 1);
});

test('les liens de l’editeur refusent javascript et acceptent https', () => {
  const b = projectEditor();
  b.window.prompt = () => 'javascript:alert(1)';
  b.editor.link();
  assert.equal(b.commands.length, 0);
  assert.equal(b.alerts.length, 1);
  b.window.prompt = () => 'https://example.test/brief';
  b.editor.link();
  assert.deepEqual(b.commands[0], ['createLink', false, 'https://example.test/brief']);
});

test('l’en-tete de la fiche suit le code et le titre saisis dans l’editeur', () => {
  const b = projectEditor();
  b.wire.p.title = 'CM-01 — Fiche marque du restaurant';
  assert.equal(b.editor.sheetCode, 'CM-01');
  assert.equal(b.editor.sheetTitle, 'Fiche marque du restaurant');
  b.wire.p.title = 'PM-A — Lancement de marque';
  assert.equal(b.editor.sheetCode, 'PM-A');
  assert.equal(b.editor.sheetTitle, 'Lancement de marque');
  b.wire.p.title = '';
  assert.equal(b.editor.sheetCode, 'PROJET');
  assert.equal(b.editor.sheetTitle, 'Nouveau projet');
});
