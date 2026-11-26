const assert = require('node:assert/strict');
const { test } = require('node:test');
const fs = require('node:fs');
const vm = require('node:vm');

const source = fs.readFileSync('public/assets/site/js/preinscription.js', 'utf8');
const helpers = source.slice(source.indexOf('const escapeHtml'), source.indexOf('const $='));
const { escapeHtml, formationKey } = vm.runInNewContext(`(() => { ${helpers} return { escapeHtml, formationKey }; })()`);

test('les saisies et noms de fichiers du recapitulatif ne deviennent pas du HTML', () => {
  const attack = '<img src=x onerror="alert(1)"> & \'document\'';
  assert.equal(escapeHtml(attack), '&lt;img src=x onerror=&quot;alert(1)&quot;&gt; &amp; &#39;document&#39;');
  assert.equal(escapeHtml('Awa Kouassi — Design graphique'), 'Awa Kouassi — Design graphique');
  assert.match(source, /escapeHtml\(f\.name\)/);
  assert.match(source, /escapeHtml\(d\[k\]\.join/);
});

test('seuls les fragments de formations connus peuvent alimenter un selecteur CSS', () => {
  for (const value of ['design', 'cm', 'gia']) assert.equal(formationKey(value), value);
  for (const value of ['inconnue', '"]', '__proto__', '<script>', '']) assert.equal(formationKey(value), null);
});
