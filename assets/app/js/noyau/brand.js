/* ============ MY EVC — identité visuelle (charte graphique v1.0, octobre 2026) ============
   Logo (fichiers vectoriels de la charte, non redessinés), étoile, motif « trajectoire », frise de progression
   et icônes au trait (1,8 px, extrémités arrondies) qui remplacent les icônes pleines à l'affichage. */

/* ---------- Logo : LOGO_SVG est défini dans brand-assets.js (généré depuis assets/app/img/*.svg) ---------- */
const BRAND = { name: 'MY EVC', tagline: 'École Virtuelle des Créatifs', slogan: 'De la préinscription à la <em>certification</em>, un seul parcours.' };
/* variante : 'couleur' (fonds clairs), 'negatif' (fond indigo), 'blanc' (fond corail ou photo sombre) */
const logoH = (v='couleur', cls='') => `<span class="logo logo-h ${cls}" role="img" aria-label="myEVC — École Virtuelle des Créatifs">${LOGO_SVG['logo-horizontal-'+v]}</span>`;
const logoSym = (v='couleur', cls='') => `<span class="logo logo-sym ${cls}" role="img" aria-label="myEVC">${LOGO_SVG['symbole-'+v]}</span>`;
const logoType = (v='couleur', cls='') => `<span class="logo logo-type ${cls}" role="img" aria-label="myEVC">${LOGO_SVG['logotype-court-'+v]}</span>`;
const brandMark = () => logoSym('couleur', 'brand-sym');

/* ---------- Éléments graphiques ---------- */
/* L'étoile à 4 branches (carré 38 × 38, creux à 5,5 unités du centre) : repère de réussite, nouveauté, certification */
const STAR_PATH = 'M19 0 L22.89 15.11 L38 19 L22.89 22.89 L19 38 L15.11 22.89 L0 19 L15.11 15.11 Z';
const star = (cls='', title='') => `<svg class="star ${cls}" viewBox="0 0 38 38" aria-hidden="${title?'false':'true'}" ${title?`role="img" aria-label="${title}"`:''}><path d="${STAR_PATH}" fill="currentColor"/></svg>`;
/* Le motif « trajectoire » : la courbe, le point et l'étoile, agrandis, ton sur ton sur les fonds indigo. Jamais en premier plan. */
const motif = (cls='') => `<svg class="motif ${cls}" viewBox="0 0 120 120" aria-hidden="true"><path d="M30 90 C34 62 48 46 66 41" fill="none" stroke="currentColor" stroke-width="8" stroke-linecap="round"/><circle cx="30" cy="90" r="10" fill="currentColor"/><g transform="translate(70 16)"><path d="${STAR_PATH}" fill="currentColor"/></g></svg>`;
/* La frise de progression : étapes franchies en indigo, étape en cours en corail, objectif en étoile */
function frise(steps, cur, {dark=false, compact=false}={}){
  return `<ol class="frise ${dark?'on-dark':''} ${compact?'compact':''}" aria-label="Progression">${steps.map((s,i)=>{ const st = i<cur ? 'done' : i===cur ? 'cur' : 'todo';
    return `<li class="fr-${st}${i===steps.length-1?' fr-goal':''}" ${i===cur?'aria-current="step"':''}><span class="fr-dot">${i<cur ? '<svg viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>' : ''}</span><span class="fr-l">${s.l}${s.s?`<small>${s.s}</small>`:''}</span></li>`; }).join('')}</ol>`;
}

/* ---------- Icônes au trait ---------- */
const _F = '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/>';
const _CAL = '<rect x="3.5" y="5" width="17" height="16" rx="2.5"/><path d="M8 3v4M16 3v4M3.5 10h17"/>';
const _USER = '<circle cx="9.5" cy="8" r="4"/><path d="M3 20.5a6.5 6.5 0 0 1 13 0"/>';
const _COG = '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5v.2a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>';
const _HOUSE = '<path d="M3.5 10.5 12 3.5l8.5 7"/><path d="M5.5 9v10a1.5 1.5 0 0 0 1.5 1.5h10a1.5 1.5 0 0 0 1.5-1.5V9"/>';
const _STAR4 = '<path d="M12 2.5l2.4 7.1 7.1 2.4-7.1 2.4L12 21.5l-2.4-7.1L2.5 12l7.1-2.4z"/>';
const ICONS = {
  'house-chimney': _HOUSE+'<path d="M10 20.5V15h4v5.5"/>', 'house-user': _HOUSE+'<circle cx="12" cy="13" r="2"/><path d="M9 20a3 3 0 0 1 6 0"/>',
  'layer-group': '<path d="m12 3 9 4.5-9 4.5-9-4.5z"/><path d="m3 12 9 4.5 9-4.5"/><path d="m3 16.5 9 4.5 9-4.5"/>',
  'tv': '<rect x="2.5" y="6" width="19" height="13.5" rx="2.5"/><path d="m8 2.5 4 3.5 4-3.5"/>',
  'calendar-days': _CAL+'<path d="M8 14h.01M12 14h.01M16 14h.01M8 17.5h.01M12 17.5h.01"/>', 'calendar': _CAL,
  'calendar-week': _CAL+'<path d="M7.5 15h9"/>', 'calendar-day': _CAL+'<rect x="7.5" y="13.5" width="4" height="4" rx=".8"/>',
  'calendar-plus': _CAL+'<path d="M12 13v5.5M9.25 15.75h5.5"/>', 'calendar-xmark': _CAL+'<path d="m10 13.5 4 4m0-4-4 4"/>', 'calendar-check': _CAL+'<path d="m9 15.5 2 2 4-4"/>',
  'clock': '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.2 2"/>', 'clock-rotate-left': '<path d="M3 12a9 9 0 1 0 2.6-6.4L3 8"/><path d="M3 3.5V8h4.5"/><path d="M12 7.5V12l3 2"/>',
  'list-check': '<path d="M11 6h10M11 12h10M11 18h10"/><path d="m3 6 1.5 1.5 3-3M3 12l1.5 1.5 3-3M3 18l1.5 1.5 3-3"/>',
  'wallet': '<path d="M19 7V5.5A1.5 1.5 0 0 0 17.5 4H5a2 2 0 0 0 0 4h14a1.5 1.5 0 0 1 1.5 1.5V12"/><path d="M3 6v12a2 2 0 0 0 2 2h14a1.5 1.5 0 0 0 1.5-1.5V16"/><path d="M21.5 12v4H17a2 2 0 0 1 0-4z"/>',
  'comments': '<path d="M20.5 11.5a8 8 0 0 1-11.6 7.1L4 20l1.4-4.4A8 8 0 1 1 20.5 11.5z"/><path d="M8.5 11.5h.01M12.5 11.5h.01M16.5 11.5h.01"/>',
  'comment': '<path d="M20.5 11.5a8 8 0 0 1-11.6 7.1L4 20l1.4-4.4A8 8 0 1 1 20.5 11.5z"/>', 'comment-sms': '<path d="M20.5 11.5a8 8 0 0 1-11.6 7.1L4 20l1.4-4.4A8 8 0 1 1 20.5 11.5z"/><path d="M8.5 11.5h.01M12.5 11.5h.01M16.5 11.5h.01"/>',
  'palette': '<path d="M12 21a9 9 0 1 1 9-9c0 2.4-1.9 3.5-3.8 3.5H15a2 2 0 0 0-1.5 3.3c.4.5.5 1.1.1 1.6-.4.4-1 .6-1.6.6z"/><circle cx="7.5" cy="11.5" r="1.1"/><circle cx="10.5" cy="7" r="1.1"/><circle cx="15.5" cy="7.5" r="1.1"/>',
  'clipboard-check': '<rect x="8" y="2.5" width="8" height="4" rx="1"/><path d="M16 4.5h1.5A2.5 2.5 0 0 1 20 7v12.5a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 19.5V7a2.5 2.5 0 0 1 2.5-2.5H8"/><path d="m9 14 2 2 4-4"/>',
  'pen-to-square': '<path d="M12 4H6.5A2.5 2.5 0 0 0 4 6.5v11A2.5 2.5 0 0 0 6.5 20h11a2.5 2.5 0 0 0 2.5-2.5V12"/><path d="M18.4 2.6a2 2 0 0 1 3 3L12 15l-4 1 1-4z"/>',
  'pen': '<path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>', 'pen-nib': '<path d="m12 19 7-7 3 3-7 7z"/><path d="m18 13-1.5-7.5L2 2l3.5 14.5L13 18z"/><path d="m2 2 7.6 7.6"/><circle cx="11" cy="11" r="2"/>',
  'award': '<circle cx="12" cy="9" r="6"/><path d="m8.5 13.8-1.5 7.7 5-3 5 3-1.5-7.7"/>',
  'book-open': '<path d="M2.5 4.5h6A3.5 3.5 0 0 1 12 8v12.5a2.5 2.5 0 0 0-2.5-2.5h-7z"/><path d="M21.5 4.5h-6A3.5 3.5 0 0 0 12 8v12.5a2.5 2.5 0 0 1 2.5-2.5h7z"/>',
  'book': '<path d="M4 19.5V5.5A2.5 2.5 0 0 1 6.5 3H20v15H6.5a2.5 2.5 0 0 0 0 5H20"/>',
  'file-lines': _F+'<path d="M9 13h6M9 17h4"/>', 'file-pdf': _F+'<path d="M9 13h6M9 17h4"/>', 'file-invoice': _F+'<path d="M9 9h2M9 13h6M9 17h6"/>',
  'file-word': _F+'<path d="m8.5 12 1.2 5 1.8-3.5 1.8 3.5 1.2-5"/>', 'file-excel': _F+'<path d="m9.5 12 5 5.5m0-5.5-5 5.5"/>', 'file-powerpoint': _F+'<path d="M10 18v-6h2.5a2 2 0 0 1 0 4H10"/>',
  'file-zipper': _F+'<path d="M10 3v1.5M11 6h-1M10 8h1M11 10h-1M10 12.5h2v3h-2z"/>', 'file-arrow-down': _F+'<path d="M12 11v6m-2.5-2.5L12 17l2.5-2.5"/>',
  'file-export': _F+'<path d="M9 14.5h7m-2.5-2.5 2.5 2.5-2.5 2.5"/>', 'file-import': _F+'<path d="M15 14.5H8m2.5-2.5L8 14.5l2.5 2.5"/>',
  'id-card': '<rect x="2.5" y="5" width="19" height="14" rx="2.5"/><circle cx="8.5" cy="11" r="2"/><path d="M5.5 16a3 3 0 0 1 6 0M14 10h4.5M14 13.5h3"/>',
  'id-badge': '<rect x="5" y="3" width="14" height="18" rx="2.5"/><circle cx="12" cy="10.5" r="2.5"/><path d="M8.5 17a3.5 3.5 0 0 1 7 0M10 6h4"/>',
  'gear': _COG, 'gears': _COG, 'sliders': '<path d="M4 6h9M17 6h3M4 12h3M11 12h9M4 18h11M19 18h1"/><circle cx="15" cy="6" r="2"/><circle cx="9" cy="12" r="2"/><circle cx="17" cy="18" r="2"/>',
  'bell': '<path d="M6 8.5a6 6 0 0 1 12 0c0 6.5 2.5 8.5 2.5 8.5h-17S6 15 6 8.5"/><path d="M10.2 20.5a2 2 0 0 0 3.6 0"/>',
  'bell-slash': '<path d="M6 8.5a6 6 0 0 1 12 0c0 6.5 2.5 8.5 2.5 8.5h-17S6 15 6 8.5"/><path d="M10.2 20.5a2 2 0 0 0 3.6 0M3 3l18 18"/>',
  'headset': '<path d="M4 14v-2a8 8 0 0 1 16 0v2"/><path d="M4 14h2.5a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1zM20 14h-2.5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1H19a1 1 0 0 0 1-1z"/><path d="M19 19a3 3 0 0 1-3 2.5h-2.5"/>',
  'life-ring': '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/><path d="m5.6 5.6 3.6 3.6M14.8 14.8l3.6 3.6M14.8 9.2l3.6-3.6M5.6 18.4l3.6-3.6"/>',
  'briefcase': '<rect x="2.5" y="7" width="19" height="13" rx="2.5"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M2.5 13h19"/>',
  'user-graduate': '<path d="M22 9 12 4 2 9l10 5z"/><path d="M6 11v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5M22 9v5.5"/>', 'graduation-cap': '<path d="M22 9 12 4 2 9l10 5z"/><path d="M6 11v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5M22 9v5.5"/>',
  'user': '<circle cx="12" cy="8" r="4"/><path d="M4.5 21a7.5 7.5 0 0 1 15 0"/>', 'user-plus': _USER+'<path d="M19 8v6M16 11h6"/>', 'user-check': _USER+'<path d="m16 11 2 2 4-4"/>',
  'user-xmark': _USER+'<path d="m17 8.5 4 4m0-4-4 4"/>', 'users': '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 4.6a3.5 3.5 0 0 1 0 6.8M21.5 20a6.5 6.5 0 0 0-4-6"/>',
  'people-group': '<circle cx="12" cy="8" r="3"/><path d="M6.5 20a5.5 5.5 0 0 1 11 0"/><circle cx="5" cy="10" r="2"/><circle cx="19" cy="10" r="2"/><path d="M2 18a3.5 3.5 0 0 1 3-3.5M22 18a3.5 3.5 0 0 0-3-3.5"/>',
  'chalkboard-user': '<path d="M2.5 3.5h19"/><path d="M20.5 3.5v10a2 2 0 0 1-2 2h-13a2 2 0 0 1-2-2v-10"/><path d="m7.5 20.5 4.5-5 4.5 5"/>',
  'tower-broadcast': '<circle cx="12" cy="10" r="2"/><path d="M12 12v9"/><path d="M8.5 6.5a5 5 0 0 0 0 7M15.5 6.5a5 5 0 0 1 0 7M5.6 3.6a9 9 0 0 0 0 12.8M18.4 3.6a9 9 0 0 1 0 12.8"/>',
  'chart-pie': '<path d="M21 12.5A9 9 0 1 1 11.5 3v9.5z"/><path d="M15 2.6A7 7 0 0 1 21.4 9H15z"/>', 'chart-line': '<path d="M3.5 3.5v17h17"/><path d="m7.5 15 4-4 3 3 5-6"/>',
  'arrow-trend-up': '<path d="m3 17 6-6 4 4 8-8"/><path d="M15 7h6v6"/>', 'percent': '<path d="M19 5 5 19"/><circle cx="7" cy="7" r="2.5"/><circle cx="17" cy="17" r="2.5"/>',
  'shield-halved': '<path d="M12 3 4.5 6v6c0 4.5 3.2 7.8 7.5 9 4.3-1.2 7.5-4.5 7.5-9V6z"/><path d="M12 3v18"/>',
  'fingerprint': '<path d="M12 11v3a8 8 0 0 1-1.5 4.8"/><path d="M8.5 7.8A4.5 4.5 0 0 1 16.5 11v2a12 12 0 0 1-.7 4"/><path d="M5.5 15.5A12 12 0 0 0 7.5 11a4.5 4.5 0 0 1 .5-2"/><path d="M4.5 9a8 8 0 0 1 15 2v1.5"/><path d="M19.4 17a16 16 0 0 0 .5-3"/>',
  'folder-open': '<path d="M3 19V6a2 2 0 0 1 2-2h4l2 2.5h7a2 2 0 0 1 2 2V10"/><path d="m3 19 2.6-7a2 2 0 0 1 1.9-1.3H21l-2.8 7.5A2 2 0 0 1 16.3 19z"/>',
  'bullhorn': '<path d="M4 10v4a1 1 0 0 0 1 1h2l9 4.5v-15L7 9H5a1 1 0 0 0-1 1z"/><path d="M19.5 9.5a3.5 3.5 0 0 1 0 5"/><path d="M8 15v3.5a1.5 1.5 0 0 0 3 0V16.5"/>',
  'qrcode': '<rect x="3.5" y="3.5" width="6.5" height="6.5" rx="1.2"/><rect x="14" y="3.5" width="6.5" height="6.5" rx="1.2"/><rect x="3.5" y="14" width="6.5" height="6.5" rx="1.2"/><path d="M14 14h2.5v2.5M20.5 14v.01M14 20.5h.01M17.5 17.5h3v3h-3z"/>',
  'hourglass-half': '<path d="M6 3h12M6 21h12"/><path d="M7.5 3c0 4.5 4.5 6 4.5 9s-4.5 4.5-4.5 9M16.5 3c0 4.5-4.5 6-4.5 9s4.5 4.5 4.5 9"/><path d="M9.5 18.5h5"/>',
  'photo-film': '<rect x="2.5" y="4" width="15" height="12" rx="2"/><path d="M21.5 8v10a2 2 0 0 1-2 2H7"/><path d="m2.5 13 4-4 4 4 2-2 5 5"/>',
  'route': '<circle cx="6" cy="18.5" r="2.5"/><path d="M8.5 18.5H16a3.5 3.5 0 0 0 0-7H8a3.5 3.5 0 0 1 0-7h7"/><path d="m19 2 .8 2.2L22 5l-2.2.8L19 8l-.8-2.2L16 5l2.2-.8z"/>',
  'key': '<circle cx="7.5" cy="15.5" r="4.5"/><path d="m10.7 12.3 9.3-9.3M17 6l3 3M14.5 8.5l2 2"/>', 'lock': '<rect x="4.5" y="10.5" width="15" height="10.5" rx="2.5"/><path d="M8 10.5V7a4 4 0 0 1 8 0v3.5"/>',
  'ban': '<circle cx="12" cy="12" r="9"/><path d="m5.6 5.6 12.8 12.8"/>',
  'circle-check': '<circle cx="12" cy="12" r="9"/><path d="m8.5 12.2 2.4 2.4 4.6-4.9"/>', 'circle-xmark': '<circle cx="12" cy="12" r="9"/><path d="m9 9 6 6m0-6-6 6"/>',
  'circle-info': '<circle cx="12" cy="12" r="9"/><path d="M12 11v5.5M12 7.8h.01"/>', 'circle-question': '<circle cx="12" cy="12" r="9"/><path d="M9.3 9.2a2.8 2.8 0 0 1 5.4 1c0 1.8-2.7 2.5-2.7 2.5M12 16.8h.01"/>',
  'circle-play': '<circle cx="12" cy="12" r="9"/><path d="m10 8.5 5.5 3.5-5.5 3.5z"/>', 'circle': '<circle cx="12" cy="12" r="8"/>',
  'circle-half-stroke': '<circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 0 1 0 18z" fill="currentColor"/>',
  'triangle-exclamation': '<path d="M10.3 3.9 2.4 18a2 2 0 0 0 1.7 3h15.8a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/><path d="M12 9.5v4M12 17h.01"/>',
  'check': '<path d="M20 6 9 17l-5-5"/>', 'check-double': '<path d="m2 12.5 4.5 4.5L15 8.5"/><path d="m11 16 1 1 8.5-8.5"/>', 'xmark': '<path d="M18 6 6 18M6 6l12 12"/>',
  'plus': '<path d="M12 5v14M5 12h14"/>', 'chevron-right': '<path d="m9 6 6 6-6 6"/>', 'chevron-left': '<path d="m15 6-6 6 6 6"/>', 'chevron-down': '<path d="m6 9 6 6 6-6"/>',
  'angles-left': '<path d="m11 17-5-5 5-5M18 17l-5-5 5-5"/>', 'forward': '<path d="m13 6 6 6-6 6M5 6l6 6-6 6"/>',
  'play': '<path d="M7 4.8v14.4a.8.8 0 0 0 1.2.7l11.3-7.2a.8.8 0 0 0 0-1.4L8.2 4.1a.8.8 0 0 0-1.2.7z"/>', 'pause': '<path d="M8 5v14M16 5v14"/>',
  'download': '<path d="M12 4v11m-4.5-4.5L12 15l4.5-4.5"/><path d="M5 19.5h14"/>', 'upload': '<path d="M12 16V4.5M7.5 9 12 4.5 16.5 9"/><path d="M5 19.5h14"/>',
  'cloud-arrow-up': '<path d="M7 18.5a5 5 0 0 1-.6-10A6 6 0 0 1 18 9.5a4.5 4.5 0 0 1-.5 9"/><path d="M12 12.5V20m-3-4.5 3-3 3 3"/>',
  'paperclip': '<path d="m21 11.5-8.6 8.6a5.5 5.5 0 0 1-7.8-7.8l8.6-8.6a3.7 3.7 0 0 1 5.2 5.2l-8.6 8.6a1.8 1.8 0 0 1-2.6-2.6l8-8"/>',
  'paper-plane': '<path d="M21.5 2.5 11 13"/><path d="m21.5 2.5-7 19-3.5-8.5-8.5-3.5z"/>', 'envelope': '<rect x="2.5" y="5" width="19" height="14" rx="2.5"/><path d="m3 7 9 6 9-6"/>',
  'magnifying-glass': '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.8-3.8"/>', 'filter': '<path d="M3 4.5h18l-7 8.5v6l-4 2v-8z"/>',
  'mobile-screen': '<rect x="6.5" y="2.5" width="11" height="19" rx="2.5"/><path d="M11 18h2"/>', 'laptop': '<rect x="4" y="4.5" width="16" height="11" rx="2"/><path d="M2 19.5h20"/>',
  'display': '<rect x="2.5" y="3.5" width="19" height="13" rx="2.5"/><path d="M8 21h8M12 16.5V21"/>', 'video': '<rect x="2.5" y="6" width="13" height="12" rx="2.5"/><path d="m15.5 10.5 6-3.5v10l-6-3.5"/>',
  'image': '<rect x="3" y="3" width="18" height="18" rx="2.5"/><circle cx="9" cy="9" r="2"/><path d="m21 15-4.5-4.5L6 21"/>', 'camera': '<path d="M3 8.5a2 2 0 0 1 2-2h2l1.5-2.5h7L17 6.5h2a2 2 0 0 1 2 2V18a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><circle cx="12" cy="13" r="3.5"/>',
  'receipt': '<path d="M5 3h14v18l-2.3-1.5L14.3 21 12 19.5 9.7 21l-2.4-1.5L5 21z"/><path d="M9 8h6M9 12h6M9 16h3"/>',
  'money-bill-wave': '<rect x="2.5" y="6" width="19" height="12" rx="2.5"/><circle cx="12" cy="12" r="2.5"/><path d="M6 12h.01M18 12h.01"/>',
  'credit-card': '<rect x="2.5" y="5" width="19" height="14" rx="2.5"/><path d="M2.5 10h19M6.5 15h3"/>', 'building-columns': '<path d="M3 21h18M4.5 10h15M12 3 3.5 8h17zM6.5 10v8M10.5 10v8M13.5 10v8M17.5 10v8"/>',
  'calculator': '<rect x="4.5" y="2.5" width="15" height="19" rx="2.5"/><path d="M8 7h8M8 12h.01M12 12h.01M16 12h.01M8 16h.01M12 16h.01M16 16h.01"/>',
  'location-dot': '<path d="M12 21s7-6.2 7-11.5A7 7 0 0 0 5 9.5C5 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>', 'globe': '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
  'flag': '<path d="M4.5 21.5v-18"/><path d="M4.5 4h12l-2.2 4.3 2.2 4.2h-12"/>', 'flag-checkered': '<path d="M4.5 21.5v-18"/><path d="M4.5 4h12l-2.2 4.3 2.2 4.2h-12"/><path d="M10.5 4v8.5"/>',
  'inbox': '<path d="M22 12.5h-6l-2 3h-4l-2-3H2"/><path d="M5.5 5h13l3.5 7.5v5a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-5z"/>', 'box-archive': '<rect x="2.5" y="3.5" width="19" height="5" rx="1.5"/><path d="M4 8.5V19a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8.5M10 13h4"/>',
  'eye': '<path d="M2.5 12S6 5 12 5s9.5 7 9.5 7-3.5 7-9.5 7S2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/>',
  'rotate-left': '<path d="M3 12a9 9 0 1 0 2.6-6.4L3 8"/><path d="M3 3.5V8h4.5"/>', 'rotate': '<path d="M21 12a9 9 0 1 1-2.6-6.4L21 8"/><path d="M21 3.5V8h-4.5"/>',
  'right-to-bracket': '<path d="M15 3h3.5A2.5 2.5 0 0 1 21 5.5v13a2.5 2.5 0 0 1-2.5 2.5H15"/><path d="m10 17 5-5-5-5M15 12H3"/>',
  'right-from-bracket': '<path d="M9 21H5.5A2.5 2.5 0 0 1 3 18.5v-13A2.5 2.5 0 0 1 5.5 3H9"/><path d="m16 17 5-5-5-5M21 12H9"/>',
  'arrow-right-from-bracket': '<path d="M9 21H5.5A2.5 2.5 0 0 1 3 18.5v-13A2.5 2.5 0 0 1 5.5 3H9"/><path d="m16 17 5-5-5-5M21 12H9"/>',
  'arrow-up-right-from-square': '<path d="M14 3h7v7M21 3l-9 9"/><path d="M19 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h5"/>',
  'link': '<path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.7 1.7"/><path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.7-1.7"/>',
  'copy': '<rect x="8" y="8" width="13" height="13" rx="2.5"/><path d="M16 8V5.5A2.5 2.5 0 0 0 13.5 3h-8A2.5 2.5 0 0 0 3 5.5v8A2.5 2.5 0 0 0 5.5 16H8"/>',
  'expand': '<path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/>', 'table-columns': '<rect x="3" y="4" width="18" height="16" rx="2.5"/><path d="M12 4v16"/>',
  'list': '<path d="M8.5 6h12.5M8.5 12h12.5M8.5 18h12.5M3.5 6h.01M3.5 12h.01M3.5 18h.01"/>', 'list-ol': '<path d="M10 6h11M10 12h11M10 18h11M4 4.5h1.5V10M4 10h3M7 19.5H4c0-1.2 3-2.2 3-3.5a1.5 1.5 0 0 0-3-.3"/>',
  'bars': '<path d="M4 6.5h16M4 12h16M4 17.5h16"/>', 'grip': '<circle cx="5.5" cy="5.5" r="1.3" fill="currentColor"/><circle cx="12" cy="5.5" r="1.3" fill="currentColor"/><circle cx="18.5" cy="5.5" r="1.3" fill="currentColor"/><circle cx="5.5" cy="12" r="1.3" fill="currentColor"/><circle cx="12" cy="12" r="1.3" fill="currentColor"/><circle cx="18.5" cy="12" r="1.3" fill="currentColor"/><circle cx="5.5" cy="18.5" r="1.3" fill="currentColor"/><circle cx="12" cy="18.5" r="1.3" fill="currentColor"/><circle cx="18.5" cy="18.5" r="1.3" fill="currentColor"/>',
  'ellipsis-vertical': '<circle cx="12" cy="5" r="1.4" fill="currentColor"/><circle cx="12" cy="12" r="1.4" fill="currentColor"/><circle cx="12" cy="19" r="1.4" fill="currentColor"/>',
  'star': _STAR4, 'wand-magic-sparkles': '<path d="m3 21 11-11"/><path d="m15 3 .8 2.2L18 6l-2.2.8L15 9l-.8-2.2L12 6l2.2-.8zM19.5 10l.5 1.5 1.5.5-1.5.5-.5 1.5-.5-1.5L17.5 12l1.5-.5z"/>',
  'shapes': '<circle cx="7" cy="7" r="4"/><rect x="13" y="13" width="8" height="8" rx="1.5"/><path d="m17 3 4 7h-8z"/><path d="M3 21l4-7 4 7z"/>',
  'wave-square': '<path d="M2 12h3V6.5h5v11h5v-11h5V12h2"/>', 'volume-high': '<path d="M11 5 6 9H3v6h3l5 4z"/><path d="M15.5 8.5a5 5 0 0 1 0 7M19 5a10 10 0 0 1 0 14"/>',
  'trash-can': '<path d="M4 6.5h16M9 6.5v-2A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5v2M6 6.5l1 13a2 2 0 0 0 2 1.5h6a2 2 0 0 0 2-1.5l1-13M10 11v6M14 11v6"/>',
  'spinner': '<path d="M12 3a9 9 0 1 0 9 9"/>', 'robot': '<rect x="4" y="8" width="16" height="12" rx="3"/><path d="M12 4v4M9 13v1.5M15 13v1.5M2 13v3M22 13v3"/><circle cx="12" cy="3" r="1"/>',
  'mug-hot': '<path d="M4 9.5h13v5.5a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5z"/><path d="M17 11.5h1.5a2.5 2.5 0 0 1 0 5H17M8.5 2.5c0 1 1 1.5 1 2.5s-1 1.5-1 2.5M12.5 2.5c0 1 1 1.5 1 2.5s-1 1.5-1 2.5"/>',
  'moon': '<path d="M20.5 14.5A8.5 8.5 0 1 1 9.5 3.5a7 7 0 0 0 11 11z"/>', 'face-smile': '<circle cx="12" cy="12" r="9"/><path d="M8.5 14.5a4.5 4.5 0 0 0 7 0M9 9.5h.01M15 9.5h.01"/>',
  'face-meh-blank': '<circle cx="12" cy="12" r="9"/><path d="M9 10h.01M15 10h.01"/>', 'code-branch': '<circle cx="6" cy="5.5" r="2.5"/><circle cx="6" cy="18.5" r="2.5"/><circle cx="18" cy="7.5" r="2.5"/><path d="M6 8v8M18 10a6 6 0 0 1-6 6H8"/>',
  'handshake': '<path d="m11 17 2 2a1.4 1.4 0 0 0 2-2"/><path d="m14 14 2.5 2.5a1.4 1.4 0 0 0 2-2l-3.9-3.9a2.8 2.8 0 0 0-4 0l-.9.9a1.4 1.4 0 0 1-2-2l2.8-2.8a4 4 0 0 1 5-.5l.5.3a3 3 0 0 0 2 .4H21"/><path d="m21 3 1 11h-2M3 3 2 14l6.5 6.5a1.4 1.4 0 0 0 2-2M3 4h8"/>',
  'toggle-on': '<rect x="2" y="6" width="20" height="12" rx="6"/><circle cx="16" cy="12" r="2.5" fill="currentColor"/>', 'toggle-off': '<rect x="2" y="6" width="20" height="12" rx="6"/><circle cx="8" cy="12" r="2.5"/>'
};
const ICON_MOD = /^fa-(solid|regular|fw|lg|xl|xs|sm|2x|3x|spin|pulse|beat|fade)$/;
const svgIcon = n => `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">${ICONS[n]}</svg>`;
function iconize(el){
  const n = [...el.classList].find(c => c.startsWith('fa-') && !ICON_MOD.test(c)), k = n && n.slice(3);
  if (k && ICONS[k]) { if (el.dataset.ic !== k) { el.dataset.ic = k; el.innerHTML = svgIcon(k); el.classList.add('lic'); } }
  else if (el.dataset.ic) { delete el.dataset.ic; el.innerHTML = ''; el.classList.remove('lic'); }
}
function lineIcons(root){
  if (!root || root.nodeType !== 1) return;
  if (root.matches('i.fa-solid, i.fa-regular')) iconize(root);
  root.querySelectorAll('i.fa-solid, i.fa-regular').forEach(iconize);
}
new MutationObserver(ms => { for (const m of ms) { if (m.type === 'attributes') { if (m.target.matches('i.fa-solid, i.fa-regular, i.lic')) iconize(m.target); continue; }
  m.addedNodes.forEach(n => { if (n.nodeType === 1 && n.tagName !== 'svg' && n.tagName !== 'path') lineIcons(n); }); } })
  .observe(document.documentElement, {childList:true, subtree:true, attributes:true, attributeFilter:['class']});
