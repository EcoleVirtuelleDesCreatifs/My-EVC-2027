/* ============ MY EVC — données de démonstration ============ */
const TODAY = new Date(2026, 9, 22, 9, 0); // 22 octobre 2026
const iso = d => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
const addDays = (n, h) => { const d = new Date(TODAY); d.setDate(d.getDate()+n); if (h!=null) d.setHours(h,0,0,0); return d; };
function rng(seed){ return function(){ seed |= 0; seed = seed + 0x6D2B79F5 | 0; let t = Math.imul(seed ^ seed >>> 15, 1 | seed); t = t + Math.imul(t ^ t >>> 7, 61 | t) ^ t; return ((t ^ t >>> 14) >>> 0) / 4294967296; }; }

const F = {
  DG: { code:'DG', name:'Infographie & Design Graphique', icon:'fa-palette', price:185000, deposit:95000, months:4, trancheDue:'20/12/2026', trancheDays:59, payRule:'95 000 FCFA à l\'inscription, 90 000 FCFA à solder après 2 mois (le 20 du mois)', format:'Hybride : en ligne + présentiel (1 séance par mois)', place:'Immeuble Bloom Square Akwaba (Palmeraie – Carrefour Guiraud)', trainer:'Marc Yao', trainer2:'Inès Gbagbo', hours:'En ligne + 1 séance présentielle par mois',
    pitch:"Maîtriser Photoshop, Illustrator et InDesign pour créer identités visuelles, logotypes, chartes graphiques, étiquettes et affiches professionnelles.",
    goals:["Maîtriser Photoshop, Illustrator et InDesign","Concevoir une identité visuelle complète","Produire des supports print conformes à l'impression","Présenter et défendre un projet devant un client"],
    modules:[
      {t:'Photoshop', ch:['Interface et calques','Retouche photo','Montages créatifs','Export web et print'], done:4},
      {t:'Illustrator', ch:['Outils vectoriels','Plume et tracés','Typographie vectorielle','Pictogrammes'], done:4},
      {t:'InDesign', ch:['Gabarits et pages types','Styles de paragraphe','Mise en page magazine'], done:2},
      {t:'Identité visuelle', ch:['Plateforme de marque','Charte graphique','Déclinaisons'], done:2},
      {t:'Création de logos', ch:['Recherche et croquis','Vectorisation','Présentation client'], done:1},
      {t:'Affiches et supports publicitaires', ch:['Composition','Hiérarchie visuelle','Formats affichage'], done:0},
      {t:'Packaging', ch:['Gabarits de découpe','Maquette 3D'], done:0},
      {t:'Catalogues et magazines', ch:['Chemin de fer','Grilles de mise en page'], done:0},
      {t:'Mise en page professionnelle', ch:['Prépresse','BAT et impression'], done:0}],
    sessions:{total:32, done:6}, presence:94, late:2, abs:2 },
  CM: { code:'CM', name:'Community & Social Media Management', icon:'fa-users', price:165000, deposit:85000, months:2, trancheDue:'15/11/2026', trancheDays:24, payRule:'85 000 FCFA à l\'inscription, 80 000 FCFA à solder après 1 mois (le 15 du mois)', format:'8 séances : 4 en présentiel (samedi 9h-12h) et 4 en ligne (samedi et dimanche 14h-15h30)', place:'Immeuble Bloom Square Akwaba (Palmeraie – Carrefour Guiraud)', trainer:'Fatim Diabaté', trainer2:'Hervé Kassi', hours:'Samedi 9h-12h (présentiel) · Sam. et dim. 14h-15h30 (en ligne)',
    pitch:"Devenir expert des réseaux sociaux : créer du contenu viral, gérer des communautés et développer la présence digitale des marques.",
    goals:["Définir positionnement, cibles et personas","Construire une ligne et un calendrier éditorial","Lancer et optimiser des campagnes publicitaires","Mesurer la performance et produire un reporting"],
    modules:[
      {t:'Stratégie digitale', ch:['Positionnement','Analyse de cible','Personas','Objectifs SMART','Choix des plateformes','Stratégie éditoriale'], done:6},
      {t:'Production de contenus', ch:['Publications','Visuels','Vidéos courtes','Scripts','Calendrier éditorial','Campagnes publicitaires'], done:3},
      {t:'Reporting', ch:['Indicateurs de performance','Suivi des objectifs','Tableaux de bord','Bilans de campagnes'], done:1},
      {t:'RECO 60', ch:["Identité du projet","Configuration des canaux","Plan d'action et exécution"], done:1}],
    sessions:{total:8, done:5}, presence:88, late:3, abs:3 },
  GIA:{ code:'GIA', name:'Bureautique et informatique', icon:'fa-gears', price:265000, deposit:140000, months:2, trancheDue:'06/11/2026', trancheDays:15, payRule:'140 000 FCFA à l\'inscription, puis 125 000 FCFA', format:'En ligne et en présentiel', place:'Immeuble Bloom Square Akwaba (Palmeraie – Carrefour Guiraud)', trainer:'Koffi Brou', trainer2:'Mariette Aka', hours:'Lun · Jeu — 17h30-20h',
    pitch:"Maîtriser l'outil informatique et bureautique (Word, Excel, PowerPoint, Canva, services Google) et booster son employabilité en entreprise.",
    goals:["Organiser fichiers, dossiers et périphériques","Produire des documents Word, Excel et PowerPoint professionnels","Travailler en équipe avec Google Workspace","Mener un projet professionnel de 60 jours"],
    modules:[
      {t:'Informatique générale', ch:['Environnement informatique','Gestion des fichiers','Gestion des dossiers','Utilisation des périphériques'], done:4},
      {t:'Microsoft Office', ch:['Word','Excel','PowerPoint'], done:3},
      {t:'Outils numériques', ch:['Canva','Gmail','Google Drive','Google Docs','Google Sheets','Google Slides','Google Forms','Google Calendar','Google Meet'], done:6},
      {t:'Travaux pratiques', ch:['Documents professionnels','Tableaux de gestion','Présentations','Formulaires','Rapports','Exercices bureautiques'], done:4},
      {t:'RECO 60', ch:['Création du projet professionnel','Dépôt des livrables','Suivi des activités','Évaluation','Rapport final'], done:2}],
    sessions:{total:16, done:9}, presence:97, late:1, abs:0 }
};
const FORDER = ['GIA','DG','CM'];
const PROFILES = {
  'DG':        { label:'Infographie & Design Graphique', f:['DG'], price:185000 },
  'CM':        { label:'Community & Social Media Management', f:['CM'], price:165000 },
  'DG+CM':     { label:'Infographie + Community Management', f:['DG','CM'], price:350000 },
  'GIA':       { label:'Bureautique et informatique', f:['GIA'], price:265000 },
  'GIA+CM':    { label:'Bureautique + Community Management', f:['GIA','CM'], price:430000 },
  'GIA+DG':    { label:'Bureautique + Infographie', f:['GIA','DG'], price:450000 },
  'GIA+DG+CM': { label:'Bureautique + Infographie + Community Management', f:['GIA','DG','CM'], price:615000 }
};
const PROFILE_KEYS = ['DG','CM','DG+CM','GIA','GIA+CM','GIA+DG','GIA+DG+CM'];



/* ---------- Programme : chaque formation est organisée en séances pratiques (format atelier / webinaire) ----------
   [date AAAA-MM-JJ, 'P' présentiel | 'L' en ligne, horaire, thématique] */
const PLACE_EVC = 'Bloom Square Akwaba, Palmeraie';
const PROGRAM = {
  DG:[['2026-10-03','P','09h00 – 12h00',"Découverte de l'infographie et de Photoshop"],['2026-10-07','L','18h00 – 20h00','Calques, sélections et masques'],['2026-10-14','L','18h00 – 20h00','Retouche photo professionnelle'],['2026-10-21','L','18h00 – 20h00','Montages créatifs et effets'],
      ['2026-10-28','L','18h00 – 20h00','Illustrator : outils vectoriels'],['2026-11-07','P','09h00 – 12h00','Plume, tracés et pictogrammes'],['2026-11-11','L','18h00 – 20h00','Création de logotypes'],['2026-11-18','L','18h00 – 20h00','Typographie et identité visuelle'],
      ['2026-11-25','L','18h00 – 20h00','Charte graphique complète'],['2026-12-05','P','09h00 – 12h00','Étiquettes et packaging'],['2026-12-09','L','18h00 – 20h00','InDesign : mise en page'],['2026-12-16','L','18h00 – 20h00','Affiches professionnelles'],
      ['2027-01-02','P','09h00 – 12h00',"Préparation à l'impression (BAT)"],['2027-01-06','L','18h00 – 20h00','Portfolio et pressbook'],['2027-01-13','L','18h00 – 20h00','Module Pitch pour Convaincre (PPC)'],['2027-01-20','L','18h00 – 20h00','Présentation du projet final']],
  CM:[['2026-10-03','P','09h00 – 12h00','Stratégie digitale et positionnement'],['2026-10-11','L','14h00 – 15h30','Personas et choix des plateformes'],['2026-10-17','P','09h00 – 12h00','Ligne éditoriale et calendrier'],['2026-10-25','L','14h00 – 15h30','Visuels avec Photoshop et Canva'],
      ['2026-10-31','P','09h00 – 12h00','Vidéos courtes et contenus viraux'],['2026-11-08','L','14h00 – 15h30','Animation et gestion de communauté'],['2026-11-14','P','09h00 – 12h00','Campagnes publicitaires Meta'],['2026-11-22','L','14h00 – 15h30','Reporting et Module Pitch pour Convaincre']],
  GIA:[['2026-10-05','P','17h30 – 20h00',"Prise en main de l'ordinateur et de Windows"],['2026-10-08','L','17h30 – 20h00','Fichiers, dossiers et périphériques'],['2026-10-12','P','17h30 – 20h00','Word : mise en forme'],['2026-10-15','L','17h30 – 20h00','Word : CV et lettre professionnelle'],
      ['2026-10-19','P','17h30 – 20h00','Excel : premiers tableaux'],['2026-10-22','L','17h30 – 20h00','Excel : formules et fonctions'],['2026-10-26','P','17h30 – 20h00','Excel : tableaux de gestion'],['2026-10-29','L','17h30 – 20h00','Excel : graphiques et tableaux croisés'],
      ['2026-11-02','P','17h30 – 20h00','PowerPoint : présentation efficace'],['2026-11-05','L','17h30 – 20h00','Canva : visuels et présentations'],['2026-11-09','P','17h30 – 20h00','Gmail et Google Drive'],['2026-11-12','L','17h30 – 20h00','Google Docs, Sheets et Slides'],
      ['2026-11-16','P','17h30 – 20h00','Google Forms et Agenda'],['2026-11-19','L','17h30 – 20h00','Google Meet et travail collaboratif'],['2026-11-23','P','17h30 – 20h00','Stratégie business et outils en entreprise'],['2026-11-26','L','17h30 – 20h00','Projet pratique et évaluation finale']]};
const dayOff = iso => { const [y,m,d] = iso.split('-').map(Number), t0 = new Date(TODAY.getFullYear(), TODAY.getMonth(), TODAY.getDate()); return Math.round((new Date(y,m-1,d) - t0)/86400000); };
const ATTEND = {DG:['present','present','retard','absent'], CM:['present','absent','present'], GIA:['present','present','present','retard','present']};
function buildSeances(){
  const out = [];
  FORDER.forEach(f => PROGRAM[f].forEach(([iso, m, hl, t], i) => {
    const n = i+1, d = dayOff(iso), [hs, he] = hl.split(' – '), h = parseInt(hs), e = parseInt(he) + (he.includes('30')?.5:0), online = m==='L';
    const past = d < 0 || (d===0 && e <= TODAY.getHours()), st = past ? (ATTEND[f][i] || 'present') : undefined;
    out.push({id:`${f}-${n}`, f, n, d, h, e, hl, t, mode: online ? 'Virtuel' : 'Présentiel', place: online ? `meet.google.com/evc-${f.toLowerCase()}-s${n}` : PLACE_EVC, st, just: st==='absent' ? (f==='CM') : undefined, replay: past && online, done: past});
  }));
  return out;
}
const SEANCES = buildSeances();
FORDER.forEach(f => { const L = SEANCES.filter(x=>x.f===f), past = L.filter(x=>x.done), att = past.filter(x=>x.st!=='absent');
  F[f].prog = L.map(x=>({n:x.n, t:x.t, d:x.d, hl:x.hl, mode:x.mode, done:x.done}));
  F[f].sessions = {total:L.length, done:past.length}; F[f].presence = past.length ? Math.round(att.length/past.length*100) : 100;
  F[f].late = past.filter(x=>x.st==='retard').length; F[f].abs = past.filter(x=>x.st==='absent').length; });

/* ---------- Bien démarrer : logiciels, supports, classe virtuelle ---------- */
const SOFTWARE = {
  DG:[['Adobe Photoshop','fa-image','Retouche photo et montages'],['Adobe Illustrator','fa-pen-nib','Logos et dessin vectoriel'],['Adobe InDesign','fa-book-open','Mise en page et documents']],
  CM:[['Adobe Photoshop','fa-image','Visuels pour les réseaux sociaux'],['Canva','fa-shapes','Création rapide de contenus'],['Meta Business Suite','fa-users','Gestion des pages Facebook et Instagram']],
  GIA:[['Microsoft Office','fa-file-word','Word, Excel et PowerPoint'],['Google Chrome','fa-globe','Accès aux services Google'],['Canva','fa-shapes','Présentations et visuels']]};
const STARTER = {
  DG:{t:"La carte de l'infographiste", d:"Le livret de démarrage : outils, formats, couleurs, typographie et raccourcis essentiels.", file:'carte-de-l-infographiste.pdf'},
  CM:{t:'Premier support — Community Management', d:'Les bases : plateformes, ligne éditoriale, calendrier et indicateurs.', file:'support-cm-module-1.pdf'},
  GIA:{t:'Premier support — Bureautique et informatique', d:"Prise en main de l'ordinateur, gestion des fichiers et premiers pas sur Word.", file:'support-bureautique-module-1.pdf'}};
const CLASSE = {DG:'Classe virtuelle DG — Niveau débutant', CM:'Classe virtuelle CM — Niveau débutant', GIA:'Classe virtuelle Bureautique — Niveau intermédiaire'};

/* ---------- Web TV : cours diffusés en vidéo ---------- */
function buildVideos(){
  const r = rng(7), out = [];
  FORDER.forEach(f => {
    SOFTWARE[f].forEach((sw,i) => out.push({id:`v-${f}-start-${i}`, f, mod:'Bien démarrer', mi:-1, t:`Installer ${sw[0]}`, dur:6+i*2, p0:0, views:120+i*40, pub:true}));
    const L = SEANCES.filter(x=>x.f===f), lastDone = L.filter(x=>x.done).length;
    L.forEach(x => out.push({id:`v-${f}-s${x.n}`, f, mod:'Séances', mi:x.n, n:x.n, t:`Séance ${x.n} — ${x.t}`, dur:x.mode==='Virtuel'?(x.e-x.h>=2?110:85):150, p0: x.done ? (x.n===lastDone ? .35 : 1) : 0, views: x.done ? 20+Math.floor(r()*180) : 0, pub: x.done}));
  });
  return out;
}
const VIDEOS = buildVideos();

/* ---------- Créneaux : l'étudiant choisit sa disponibilité ---------- */
const SLOTS = [
  {id:'dg-1',f:'DG',day:'1er samedi du mois',h:'09h00 – 12h00',mode:'Présentiel',cap:15,taken:11,note:'Séance présentielle mensuelle'},
  {id:'dg-2',f:'DG',day:'1er samedi du mois',h:'14h00 – 17h00',mode:'Présentiel',cap:15,taken:15,note:'Séance présentielle mensuelle'},
  {id:'dg-3',f:'DG',day:'2e samedi du mois',h:'09h00 – 12h00',mode:'Présentiel',cap:15,taken:6,note:'Séance présentielle mensuelle'},
  {id:'cm-1',f:'CM',day:'Samedi',h:'09h00 – 12h00 + samedi 14h00 – 15h30',mode:'Hybride',cap:20,taken:14,note:'Présentiel le matin, en ligne l\'après-midi'},
  {id:'cm-2',f:'CM',day:'Samedi',h:'09h00 – 12h00 + dimanche 14h00 – 15h30',mode:'Hybride',cap:20,taken:9,note:'Présentiel le samedi, en ligne le dimanche'},
  {id:'gia-1',f:'GIA',day:'Lundi et jeudi',h:'17h30 – 20h00',mode:'Présentiel',cap:16,taken:13,note:'Créneau du soir'},
  {id:'gia-2',f:'GIA',day:'Mardi et vendredi',h:'09h00 – 11h30',mode:'Présentiel',cap:16,taken:7,note:'Créneau du matin'},
  {id:'gia-3',f:'GIA',day:'Samedi',h:'09h00 – 13h00',mode:'En ligne',cap:25,taken:10,note:'Groupe Week-end'}];
const RDV_TIMES = ['09h00','10h00','11h00','15h00','16h00','17h00'];
const RDV_REQ = [
  {id:'r-1',student:'Kevin Ahoua',f:'CM',d:2,t:'10h00',motif:'Aide sur un travail',mode:'En ligne',st:'En attente'},
  {id:'r-2',student:'Nadia Touré',f:'GIA',d:3,t:'15h00',motif:'Révision',mode:'Présentiel',st:'En attente'},
  {id:'r-3',student:'Grace Mensah',f:'DG',d:1,t:'11h00',motif:'Orientation et projet',mode:'En ligne',st:'Confirmé'}];

/* ---------- Seed pour l'étudiant connecté ---------- */
const SEED = {
  student:{ first:'Aya', last:'Kouassi', matricule:'EVC-26-0142', email:'aya.kouassi@evc.ci', phone:'+225 07 48 21 63 90', city:'Abidjan, Cocody', country:"Côte d'Ivoire", birth:'14/03/2002', promo:'Promotion 2026', session:'Session Octobre 2026 — Abidjan', status:'Actif', since:'06/10/2026',
    skills:['Photoshop','Illustrator','Canva','Excel','Rédaction web'], exp:[{t:'Assistante communication',o:'Agence Sankofa',d:'2025 — 2026'},{t:'Stagiaire infographie',o:'Imprimerie Nouvelle',d:'2024'}],
    links:{linkedin:'linkedin.com/in/aya-kouassi',behance:'behance.net/ayakouassi',site:''} },
  seances: SEANCES,
  rattrapages:[{f:'DG',t:'Rattrapage séance 4 — Montages créatifs et effets',d:3,h:14,place:PLACE_EVC,ok:true},{f:'CM',t:'Rattrapage séance 2 — Personas et choix des plateformes',d:7,h:10,place:'meet.google.com/evc-cm-rat',ok:true}],
  todos:[
    {id:'t1',f:'DG',t:'Logo — Maquis "Chez Tantie Rose"',type:'Projet graphique',due:3,status:'encours',mandatory:true,
      brief:"Concevoir le logo d'un maquis familial de Treichville. Livrer 3 pistes en noir et blanc, puis la piste retenue en couleur. Fichiers sources .ai et rendus PNG 2000 px.",files:['croquis_pistes.pdf'],hist:[{w:'Marc Yao',d:-4,c:'Les pistes 1 et 3 ont du potentiel. Travaille la lisibilité à petite taille.'}]},
    {id:'t2',f:'DG',t:'Affiche — Festival Abidjan Urban Arts',type:'Travail pratique',due:8,status:'afaire',mandatory:true,brief:"Affiche A2 portrait, CMJN, fonds perdus 3 mm. Intégrer les logos partenaires fournis.",files:[],hist:[]},
    {id:'t3',f:'DG',t:'Carte de visite — charte EVC',type:'Exercice',due:-2,status:'valide',mandatory:true,grade:16,brief:"Recto-verso 85×55 mm selon la charte EVC.",files:['carte_evc_v2.pdf'],hist:[{w:'Marc Yao',d:-6,c:'Revoir les marges intérieures.'},{w:'Marc Yao',d:-1,c:'Validé. Bon travail sur la hiérarchie.'}]},
    {id:'t4',f:'DG',t:'Mise en page — magazine 8 pages',type:'Travail pratique',due:-1,status:'reprendre',mandatory:true,brief:"Chemin de fer + maquette InDesign, grille 12 colonnes.",files:['magazine_v1.indd'],hist:[{w:'Inès Gbagbo',d:-1,c:'Les styles de paragraphe ne sont pas appliqués partout. Reprendre les pages 4 à 6.'}]},
    {id:'t5',f:'CM',t:'Calendrier éditorial — novembre',type:'Livrable',due:2,status:'encours',mandatory:true,brief:"Planifier 20 publications sur 3 plateformes, avec objectif et format pour chacune.",files:[],hist:[]},
    {id:'t6',f:'CM',t:'Personas — marque de jus locale',type:'Exercice',due:-5,status:'encours',mandatory:true,brief:"Créer 3 personas documentés à partir du questionnaire fourni.",files:['personas_jus_bissap.pdf'],hist:[]},
    {id:'t7',f:'CM',t:'Script vidéo 30 s — lancement produit',type:'Travail pratique',due:6,status:'afaire',mandatory:false,brief:"Script découpé en plans avec indications sonores.",files:[],hist:[]},
    {id:'t8',f:'GIA',t:'Tableau de gestion des stocks — Excel',type:'Travail pratique',due:1,status:'soumis',mandatory:true,brief:"Tableau avec entrées, sorties, stock d'alerte en mise en forme conditionnelle et graphique mensuel.",files:['stocks_boutique.xlsx'],hist:[]},
    {id:'t9',f:'GIA',t:'Lettre de motivation — Word',type:'Exercice',due:-7,status:'valide',mandatory:true,grade:15,brief:"Respecter la norme AFNOR.",files:['lettre_motivation.docx'],hist:[{w:'Koffi Brou',d:-5,c:'Validé.'}]},
    {id:'t10',f:'GIA',t:'Formulaire d\'inscription — Google Forms',type:'Exercice',due:5,status:'afaire',mandatory:true,brief:"Formulaire avec logique conditionnelle et réponses vers Google Sheets.",files:[],hist:[]}],
  evals:[
    {id:'e1',f:'DG',t:'QCM — Photoshop',type:'QCM',d:-20,note:17,coef:1},{id:'e2',f:'DG',t:'Examen — Illustrator',type:'Examen',d:-12,note:14.5,coef:2},
    {id:'e3',f:'DG',t:'QCM — InDesign',type:'QCM',d:2,note:null,coef:1,open:true},{id:'e4',f:'DG',t:'Test final Design Graphic',type:'Examen final',d:40,note:null,coef:4},
    {id:'e5',f:'CM',t:'QCM — Stratégie digitale',type:'QCM',d:-15,note:13,coef:1},{id:'e6',f:'CM',t:'Étude de cas — Personas',type:'Examen',d:-6,note:null,coef:2,pending:true},
    {id:'e7',f:'GIA',t:'QCM — Environnement informatique',type:'QCM',d:-25,note:18,coef:1},{id:'e8',f:'GIA',t:'Examen pratique — Excel',type:'Examen',d:-9,note:15,coef:2},{id:'e9',f:'GIA',t:'QCM — Google Workspace',type:'QCM',d:0,note:null,coef:1,open:true}],
  qcm:[
    {q:"Quel format conserve les calques d'un document Photoshop ?",o:['.JPG','.PSD','.PNG','.GIF'],a:1},
    {q:"Dans InDesign, à quoi sert une page type (gabarit) ?",o:["Exporter en PDF","Appliquer des éléments communs à plusieurs pages","Changer la langue du document","Compresser les images"],a:1},
    {q:"Quel mode colorimétrique utiliser pour un document imprimé ?",o:['RVB','CMJN','Niveaux de gris uniquement','HSL'],a:1},
    {q:"Que sont les fonds perdus ?",o:["Une marge de sécurité intérieure","Une extension de l'image au-delà du format de coupe","Un type de papier","Un calque masqué"],a:1}],
  notifs:[{id:'n-c1',cat:'Communiqués',f:null,t:'Bienvenue à la session Octobre 2026',body:"Bonjour Aya, toute l'équipe de l'École Virtuelle des Créatifs vous souhaite la bienvenue. Pensez à installer vos logiciels et à choisir votre créneau dès cette semaine.",ch:['Espace étudiant','Email','WhatsApp'],comm:true,d:'06/10/2026',read:true},
    {id:'n1',cat:'Travaux',f:'DG',t:'Correction disponible : « Mise en page — magazine 8 pages » est à reprendre.',d:'Il y a 2 h',read:false},
    {id:'n2',cat:'Séances',f:'GIA',t:'Rappel : Google Forms — questionnaires aujourd\'hui à 17h00.',d:'Il y a 3 h',read:false},
    {id:'n3',cat:'Paiements',f:null,t:'Échéance du 31 octobre : 150 000 FCFA à régler.',d:'Hier',read:false},
    {id:'n4',cat:'Projets',f:'CM',t:'RECO 60 : jour 37 sur 60. Pensez à enregistrer vos activités de la semaine.',d:'Hier',read:true},
    {id:'n5',cat:'Évaluations',f:'GIA',t:'Résultat publié : Examen pratique — Excel · compétence validée.',d:'il y a 3 j',read:true},
    {id:'n6',cat:'Annonces',f:null,t:'Studio Creative édition 7 : inscriptions ouvertes jusqu\'au 30 octobre.',d:'il y a 4 j',read:true},
    {id:'n7',cat:'Messages',f:'DG',t:'Marc Yao vous a envoyé un message.',d:'il y a 5 j',read:true},
    {id:'n8',cat:'Certifications',f:'GIA',t:'Votre dossier de certification GIA est complet à 75 %.',d:'il y a 6 j',read:true}],
  threads:[
    {id:'m1',who:'Marc Yao',role:'Formateur principal — DG',f:'DG',msgs:[{me:false,t:'Bonjour Aya, tes pistes de logo sont intéressantes. Tu peux passer jeudi après la séance ?',d:'Lun. 10:12'},{me:true,t:'Bonjour M. Yao, oui je serai disponible. Merci !',d:'Lun. 10:40'},{me:false,t:'Parfait. Apporte tes croquis papier.',d:'Lun. 11:02'}]},
    {id:'m2',who:'Service financier',role:'Responsable financier',f:null,msgs:[{me:false,t:"Bonjour, nous vous rappelons l'échéance du 31 octobre. Le paiement Mobile Money est disponible depuis votre espace.",d:'Hier 09:00'}]},
    {id:'m3',who:'Fatim Diabaté',role:'Formatrice — CM',f:'CM',msgs:[{me:true,t:"Bonjour Madame, je n'arrive pas à connecter la page Facebook à Meta Business Suite pour le RECO 60.",d:'Mar. 15:20'},{me:false,t:"Vérifie que tu es bien administratrice de la page. On regarde ensemble jeudi.",d:'Mar. 16:05'}]}],
  tickets:[{id:'TCK-2041',cat:'Accès plateforme',prio:'Moyenne',t:'Replay du 12/10 introuvable',st:'Résolu',d:'14/10/2026'},{id:'TCK-2077',cat:'Paiement',prio:'Haute',t:'Reçu Orange Money non reçu',st:'En cours',d:'20/10/2026'}],
  reco:{
    CM:{start:-37, name:'Bissap Délice — lancement digital', steps:[{t:"Création de l'identité du projet",d:'Nom, logo, positionnement, cible',st:'done'},{t:'Configuration des canaux',d:'Facebook, Instagram, TikTok, WhatsApp Business',st:'done'},{t:"Élaboration et exécution du plan d'action",d:'Calendrier, campagnes, suivi des KPI',st:'cur'}],
      kpi:[{l:'Abonnés',v:'1 284',s:'+312 cette semaine'},{l:'Portée',v:'18,6 k',s:'30 derniers jours'},{l:'Engagement',v:'6,4 %',s:'objectif 5 %'},{l:'Commandes WhatsApp',v:'47',s:'depuis le jour 20'}],
      log:[{d:-1,a:'Live Instagram dégustation',r:'312 vues, 41 commentaires',p:'capture_live.png'},{d:-3,a:'Campagne Meta Ads 10 000 FCFA',r:'CPC 38 FCFA, 260 clics',p:'rapport_ads.pdf'},{d:-6,a:'Partenariat micro-influenceuse',r:'+180 abonnés',p:'lien_publication'}]},
    GIA:{start:-30, name:'Gestion numérique d\'une boutique de quartier', steps:[{t:'Création du projet professionnel',d:'Fiche projet et objectifs',st:'done'},{t:'Dépôt des livrables',d:'Fichier stocks, factures, présentation',st:'cur'},{t:'Suivi des activités',d:'Journal hebdomadaire',st:'todo'},{t:'Évaluation',d:'Soutenance devant le formateur',st:'todo'},{t:'Rapport final',d:'Rapport écrit de 10 pages',st:'todo'}],
      kpi:[{l:'Livrables déposés',v:'3 / 6',s:'2 validés'},{l:'Heures déclarées',v:'42 h',s:'objectif 80 h'}],
      log:[{d:-2,a:'Création du fichier clients Google Sheets',r:'126 clients saisis',p:'clients.xlsx'},{d:-8,a:'Modèle de facture Word',r:'Validé par le commerçant',p:'facture_modele.docx'}]}
  },
  studio:{ edition:7, title:'Festival Abidjan Urban Arts', theme:"Créer l'identité visuelle de la 3e édition du festival : logo, affiche et déclinaison réseaux sociaux.", open:-6, close:8, jury:24, registered:false, submitted:false,
    past:[{ed:6,t:'Campagne Mangez local',rank:'4e / 22',score:15.5,st:'Classée'},{ed:5,t:'Packaging cacao premium',rank:'—',score:null,st:'Non inscrite'}] },
  pressbook:[{t:'Identité Kôrô Café',cat:'Identité visuelle',c:'#C2255C',desc:'Logo, charte et menus pour un café de Grand-Bassam.'},{t:'Affiche Nuit du Zouglou',cat:'Affiche',c:'#E8620C',desc:'Affiche concert A2, typographie expressive.'},{t:'Packaging Attiéké Gold',cat:'Packaging',c:'#2B8A3E',desc:'Sachet 1 kg et étiquette prix.'},{t:'Magazine Créa+ n°1',cat:'Mise en page',c:'#1B6EC2',desc:'Magazine étudiant de 8 pages.'}],
  pressbookStatus:'En construction',
  reports:{ DG:{st:'Brouillon',versions:[{v:1,d:'10/10/2026',file:'rapport_DG_v1.docx',note:'Brouillon initial'}],comments:[]},
            CM:{st:'En correction',versions:[{v:1,d:'02/10/2026',file:'rapport_CM_v1.docx',note:''},{v:2,d:'18/10/2026',file:'rapport_CM_v2.docx',note:'Ajout du bilan RECO 60 (partiel)'}],comments:[{w:'Fatim Diabaté',c:'Structure claire. Ajouter les chiffres de la semaine 6.'}]},
            GIA:{st:'Non commencé',versions:[],comments:[]} },
  pay:{ acompte:{DG:95000,CM:85000,GIA:140000},
    tx:[{d:'06/10/2026',ref:'REC-2026-1187',mode:'Orange Money',amount:200000,by:'Aya Kouassi'},{d:'06/10/2026',ref:'REC-2026-1188',mode:'Espèces',amount:130000,by:'Aya Kouassi'},{d:'15/10/2026',ref:'REC-2026-1302',mode:'Wave',amount:120000,by:'Jean Kouassi (parent)'}] },
  onb:{soft:false, book:false, hidden:false, tourSeen:false}, slot:{}, rdv:[],
  cv:{ title:'Graphiste & community manager junior', about:"Créative et rigoureuse, je conçois des identités visuelles et des contenus qui font grandir les marques locales.", avail:'Stage — dès janvier 2027', visible:true }
};

/* ---------- Données back-office ---------- */
const NAMES = ['Aya Kouassi','Moussa Diallo','Fatou Ndiaye','Jean-Marc Kouadio','Awa Traoré','Ibrahim Sanogo','Mariam Coulibaly','Yann Konan','Aminata Bamba','Serge Akissi',"Christelle N'Guessan",'Ousmane Cissé','Grace Mensah','Kevin Ahoua','Salimata Ouattara','Rodrigue Mbarga','Nadia Touré','Cédric Gnahoré','Esther Agbo','Hamed Fofana','Prisca Lath','Abdoulaye Keïta','Divine Essoh','Loïc Tanoh','Ruth Yapi','Boris Zadi','Mireille Ehui','Idrissa Kaboré'];
const COUNTRIES = ["Côte d'Ivoire","Côte d'Ivoire","Côte d'Ivoire","Sénégal","Cameroun","Burkina Faso","Togo","Bénin","Mali","Gabon","France"];
const SESSIONS = [
  {id:'S-2026-10',name:'Session Octobre 2026',start:'06/10/2026',end:'27/03/2027',cap:120,enrolled:96,groups:['Débutant','Intermédiaire','Avancé'],place:'EVC Cocody + en ligne',status:'Active'},
  {id:'S-2026-06',name:'Session Juin 2026',start:'08/06/2026',end:'27/11/2026',cap:100,enrolled:88,groups:['Débutant','Intermédiaire','Avancé'],place:'EVC Cocody + en ligne',status:'Active'},
  {id:'S-2026-02',name:'Session Février 2026',start:'09/02/2026',end:'31/07/2026',cap:100,enrolled:91,groups:['Débutant','Intermédiaire','Avancé'],place:'EVC Cocody',status:'Clôturée'},
  {id:'S-2027-01',name:'Session Janvier 2027',start:'11/01/2027',end:'25/06/2027',cap:120,enrolled:14,groups:['Débutant','Intermédiaire','Avancé'],place:'EVC Cocody + Dakar (partenaire)',status:'Planifiée'}];
const TRAINERS = [
  {id:'FR-01',name:'Marc Yao',role:'Formateur principal',f:['DG'],groups:3,sessions:18,pending:9,avg:14.2,delay:'1,8 j',rating:4.7,email:'m.yao@evc.ci'},
  {id:'FR-02',name:'Inès Gbagbo',role:'Formateur adjoint',f:['DG'],groups:2,sessions:11,pending:4,avg:13.6,delay:'2,4 j',rating:4.5,email:'i.gbagbo@evc.ci'},
  {id:'FR-03',name:'Fatim Diabaté',role:'Formateur principal',f:['CM'],groups:3,sessions:16,pending:12,avg:13.1,delay:'3,1 j',rating:4.6,email:'f.diabate@evc.ci'},
  {id:'FR-04',name:'Hervé Kassi',role:'Formateur adjoint',f:['CM'],groups:1,sessions:6,pending:2,avg:12.8,delay:'1,2 j',rating:4.2,email:'h.kassi@evc.ci'},
  {id:'FR-05',name:'Koffi Brou',role:'Formateur principal',f:['GIA'],groups:3,sessions:20,pending:5,avg:14.9,delay:'0,9 j',rating:4.8,email:'k.brou@evc.ci'},
  {id:'FR-06',name:'Mariette Aka',role:'Formateur adjoint',f:['GIA'],groups:2,sessions:9,pending:3,avg:14.0,delay:'1,5 j',rating:4.4,email:'m.aka@evc.ci'}];
function buildStudents(){
  const r = rng(42), list = [];
  NAMES.forEach((n,i)=>{
    const p = PROFILE_KEYS[i % 7]; const prof = PROFILES[p];
    const ses = SESSIONS[i%5===4 ? 1 : (i%9===8 ? 2 : 0)];
    let status = 'Actif'; if (i%11===5) status='Suspendu'; if (ses.id==='S-2026-02') status='Certifié'; if (i===26) status='Archivé';
    const dep = prof.f.reduce((a,f)=>a+F[f].deposit,0), x = r(); const paid = status==='Certifié' ? prof.price : x<0.45 ? dep : Math.min(prof.price, dep + Math.round((prof.price-dep)*x/5000)*5000);
    list.push({ id:'EVC-26-'+String(101+i*3).padStart(4,'0'), name:n, profile:p, f:prof.f, session:ses.name, sessionId:ses.id, country: i===0 ? "Côte d'Ivoire" : COUNTRIES[Math.floor(r()*COUNTRIES.length)],
      status, progress: status==='Certifié'?100:Math.round(20+r()*70), presence: Math.round(70+r()*29), total:prof.price, paid,
      email:n.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g,'').replace(/[^a-z ]/g,'').replace(' ','.')+'@mail.com', phone:'+225 0'+(1+i%9)+' '+String(10+i).padStart(2,'0')+' 4'+(i%10)+' 2'+(i%7)+' 1'+(i%5), group:ses.groups[i%ses.groups.length], trainer: TRAINERS[(i*2)%6].name });
  });
  list[0].id='EVC-26-0142'; return list;
}
/* Candidats : dossier reçu → profil éligible (test envoyé) → test réussi → 1er versement → compte validé → mot de passe choisi */
const PREINS = [
  {id:'PRE-0311',name:'Daniel Atta',email:'daniel.atta@mail.com',phone:'+225 05 11 22 33 44',country:"Côte d'Ivoire",city:'Yopougon',birth:'02/05/2004',f:['DG'],session:'Session Janvier 2027',d:'21/10/2026',src:'Site web',docs:true,
    level:'Baccalauréat (série G2)',situation:'Sans emploi',equip:'Ordinateur portable + connexion internet',mode:'Présentiel (Abidjan)',
    motivation:"Je dessine depuis le lycée et je réalise déjà des affiches pour l'église de mon quartier sur mon téléphone. Je veux maîtriser les logiciels professionnels pour travailler en agence.",exp:'Affiches et flyers bénévoles (2 ans)',link:'instagram.com/daniel.creations',stage:'recue'},
  {id:'PRE-0312',name:'Khadija Sow',email:'khadija.sow@mail.com',phone:'+221 77 845 12 03',country:'Sénégal',city:'Dakar',birth:'19/11/1998',f:['GIA','CM'],session:'Session Janvier 2027',d:'21/10/2026',src:'Facebook',docs:true,
    level:'Licence en gestion',situation:'Assistante commerciale',equip:'Ordinateur portable + connexion internet',mode:'En ligne',
    motivation:"Je gère déjà la page Facebook de l'entreprise où je travaille. Je veux structurer ma pratique et maîtriser les outils bureautiques avancés.",exp:'Assistante commerciale depuis 3 ans',link:'',stage:'profil_ok',testSent:'21/10/2026'},
  {id:'PRE-0313',name:'Arnaud Bédié',email:'a.bedie@mail.com',phone:'+225 07 90 55 21 18',country:"Côte d'Ivoire",city:'Cocody',birth:'30/08/2003',f:['GIA','DG','CM'],session:'Session Janvier 2027',d:'20/10/2026',src:'Recommandation',docs:true,
    level:'BTS Communication (en cours)',situation:'Étudiant',equip:'Ordinateur fixe + connexion internet',mode:'Hybride',
    motivation:"Je veux lancer ma propre agence de communication digitale et avoir toutes les compétences : bureautique, design et réseaux sociaux.",exp:'Community manager bénévole pour une association',link:'behance.net/arnaudb',stage:'test_ok',test:{score:16,d:'21/10/2026'}},
  {id:'PRE-0314',name:'Sandrine Mbappé',email:'s.mbappe@mail.com',phone:'+237 6 77 12 45 90',country:'Cameroun',city:'Douala',birth:'12/01/2000',f:['CM'],session:'Session Janvier 2027',d:'19/10/2026',src:'Instagram',docs:true,
    level:'Licence en communication',situation:'En recherche d\'emploi',equip:'Smartphone + ordinateur portable',mode:'En ligne',
    motivation:"Je souhaite devenir community manager freelance pour les marques de mode africaines.",exp:'Stage de 6 mois en agence événementielle',link:'',stage:'paye',test:{score:14,d:'19/10/2026'},pay:{amount:100000,mode:'MTN MoMo',ref:'REC-2026-1499',d:'20/10/2026'},mat:'EVC-26-0190'},
  {id:'PRE-0315',name:'Paul-Henri Gnamien',email:'ph.gnamien@mail.com',phone:'+225 01 02 33 87 65',country:"Côte d'Ivoire",city:'Marcory',birth:'07/07/2001',f:['DG','CM'],session:'Session Janvier 2027',d:'18/10/2026',src:"Salon de l'étudiant",docs:true,
    level:'Baccalauréat (série A)',situation:'Étudiant',equip:'Smartphone uniquement',mode:'Présentiel (Abidjan)',
    motivation:"J'aime la mode et les réseaux sociaux.",exp:'Aucune',link:'',stage:'test_ko',test:{score:9,d:'19/10/2026'},tries:1},
  {id:'PRE-0316',name:'Aïcha Diarra',email:'aicha.diarra@mail.com',phone:'+223 76 55 44 21',country:'Mali',city:'Bamako',birth:'23/04/1995',f:['GIA'],session:'Session Janvier 2027',d:'17/10/2026',src:'Site web',docs:false,
    level:'BEPC',situation:'Commerçante',equip:'Ordinateur portable partagé',mode:'En ligne',
    motivation:"Je veux apprendre à gérer les comptes et les stocks de ma boutique sur ordinateur.",exp:'Gérante de boutique depuis 8 ans',link:'',stage:'recue'},
  {id:'PRE-0309',name:'Fanta Koné',email:'fanta.kone@mail.com',phone:'+225 07 11 98 45 30',country:"Côte d'Ivoire",city:'Abobo',birth:'15/09/2002',f:['DG'],session:'Session Janvier 2027',d:'14/10/2026',src:'TikTok',docs:true,
    level:'BTS Infographie',situation:'Étudiante',equip:'Ordinateur portable + connexion internet',mode:'Présentiel (Abidjan)',
    motivation:"Compléter mon BTS par la pratique professionnelle, participer au Studio Creative et constituer un pressbook solide pour intégrer une agence.",exp:'Stage en imprimerie (3 mois)',link:'behance.net/fantak',stage:'actif',test:{score:18,d:'15/10/2026'},pay:{amount:150000,mode:'Espèces',ref:'REC-2026-1488',d:'16/10/2026'},mat:'EVC-26-0188'}];
/* ---------- Bibliothèque : ressources à télécharger ----------
   Partagée par tous les espaces : l'administration et les formateurs publient, les étudiants
   voient les ressources de LEURS formations (f) et celles destinées à tous (f:'ALL').
   cat : logiciels · ebooks · polices · graphiques · modeles · supports · tutos
   url : lien vers le site officiel (logiciels, polices) ; sinon fichier hébergé par l'EVC.
   d   : date d'ajout en jours par rapport à aujourd'hui (négatif = passé) ; dl : téléchargements. */
const LIB = [
  /* Design graphique */
  {id:'lb-01',f:'DG',cat:'logiciels',t:'Adobe Creative Cloud (Photoshop, Illustrator, InDesign)',desc:"Installateur officiel des logiciels Adobe utilisés en cours. Licence étudiante à tarif réduit : demandez la procédure à la relation étudiants.",fmt:'Site officiel',size:'—',lic:'Licence payante',os:'Windows · macOS',url:'https://www.adobe.com/fr/creativecloud.html',key:true,d:-40,dl:212},
  {id:'lb-02',f:'DG',cat:'logiciels',t:'GIMP',desc:"Alternative libre à Photoshop pour la retouche photo, si vous n'avez pas encore de licence Adobe.",fmt:'Site officiel',size:'≈ 300 Mo',lic:'Libre et gratuit',os:'Windows · macOS · Linux',url:'https://www.gimp.org/downloads/',d:-40,dl:64},
  {id:'lb-03',f:'DG',cat:'logiciels',t:'Inkscape',desc:'Alternative libre à Illustrator pour le dessin vectoriel et les logos.',fmt:'Site officiel',size:'≈ 120 Mo',lic:'Libre et gratuit',os:'Windows · macOS · Linux',url:'https://inkscape.org/fr/release/',d:-40,dl:51},
  {id:'lb-04',f:'DG',cat:'ebooks',t:"La carte de l'infographiste",desc:'Le livret de démarrage EVC : outils, formats, couleurs, typographie et raccourcis essentiels.',fmt:'PDF',size:'3,6 Mo',lic:'Réservé EVC',key:true,d:-38,dl:188},
  {id:'lb-05',f:'DG',cat:'ebooks',t:'Les bases de la typographie',desc:'Familles, graisses, interlignage, hiérarchie : choisir et associer les polices dans une mise en page.',fmt:'PDF',size:'5,1 Mo',lic:'Réservé EVC',d:-20,dl:97},
  {id:'lb-06',f:'DG',cat:'ebooks',t:'Préparer un fichier pour l’imprimeur',desc:'CMJN, fonds perdus, résolution, profils ICC : la check-list avant d’envoyer un BAT.',fmt:'PDF',size:'2,2 Mo',lic:'Réservé EVC',d:-6,dl:41},
  {id:'lb-07',f:'DG',cat:'polices',t:'Pack de 30 polices libres pour le design',desc:'Sélection de polices Google Fonts utilisables gratuitement en commercial : Montserrat, Poppins, Playfair Display, Bebas Neue…',fmt:'ZIP · TTF',size:'24 Mo',lic:'Libre (OFL)',os:'Windows · macOS',d:-30,dl:143},
  {id:'lb-08',f:'DG',cat:'graphiques',t:'Mockups : affiches, flyers et cartes de visite',desc:'15 maquettes Photoshop à objets dynamiques pour présenter vos créations à un client.',fmt:'ZIP · PSD',size:'420 Mo',lic:'Réservé EVC',d:-14,dl:76},
  {id:'lb-09',f:'DG',cat:'graphiques',t:'Mockups packaging et T-shirts',desc:'Boîtes, sachets, bouteilles et textiles en haute définition.',fmt:'ZIP · PSD',size:'610 Mo',lic:'Réservé EVC',d:-3,dl:18},
  {id:'lb-10',f:'DG',cat:'modeles',t:'Gabarit magazine 8 pages',desc:'Fichier InDesign avec grille, styles de paragraphe et pages types, utilisé en séance.',fmt:'ZIP · INDD',size:'18 Mo',lic:'Réservé EVC',d:-25,dl:88},
  {id:'lb-11',f:'DG',cat:'supports',t:'Raccourcis Illustrator',desc:'Fiche à imprimer des raccourcis clavier indispensables.',fmt:'PDF',size:'640 Ko',lic:'Réservé EVC',d:-33,dl:120},
  {id:'lb-12',f:'DG',cat:'tutos',t:'La plume en 10 minutes',desc:'Tutoriel vidéo pour maîtriser l’outil Plume dans Illustrator.',fmt:'Vidéo',size:'12 min',lic:'Réservé EVC',d:-28,dl:134},

  /* Community management */
  {id:'lb-20',f:'CM',cat:'logiciels',t:'Canva',desc:'Création rapide de visuels, stories et carrousels pour les réseaux sociaux.',fmt:'Site officiel',size:'—',lic:'Gratuit (version Pro payante)',os:'Web · Windows · macOS · mobile',url:'https://www.canva.com/fr_fr/telecharger/',key:true,d:-40,dl:176},
  {id:'lb-21',f:'CM',cat:'logiciels',t:'CapCut',desc:'Montage de vidéos courtes (Reels, TikTok, Shorts) avec sous-titres automatiques.',fmt:'Site officiel',size:'≈ 500 Mo',lic:'Gratuit',os:'Windows · macOS · mobile',url:'https://www.capcut.com/fr-fr/',key:true,d:-40,dl:154},
  {id:'lb-22',f:'CM',cat:'logiciels',t:'Meta Business Suite',desc:'Gérer et programmer les publications Facebook et Instagram, lire les statistiques.',fmt:'Site officiel',size:'—',lic:'Gratuit',os:'Web · mobile',url:'https://business.facebook.com/',d:-40,dl:98},
  {id:'lb-23',f:'CM',cat:'ebooks',t:'Construire sa stratégie social media',desc:'De l’analyse de la cible au calendrier éditorial : la méthode enseignée à l’EVC, avec exemples ivoiriens.',fmt:'PDF',size:'6,4 Mo',lic:'Réservé EVC',key:true,d:-35,dl:131},
  {id:'lb-24',f:'CM',cat:'ebooks',t:'100 idées de publications',desc:'Idées de contenus classées par objectif : notoriété, engagement, vente.',fmt:'PDF',size:'1,9 Mo',lic:'Réservé EVC',d:-5,dl:47},
  {id:'lb-25',f:'CM',cat:'modeles',t:'Calendrier éditorial',desc:'Tableau prêt à remplir : dates, réseaux, formats, statut et indicateurs.',fmt:'XLSX',size:'96 Ko',lic:'Réservé EVC',d:-30,dl:162},
  {id:'lb-26',f:'CM',cat:'modeles',t:'Plan de communication',desc:'Trame Word pour présenter une stratégie à un client.',fmt:'DOCX',size:'220 Ko',lic:'Réservé EVC',d:-30,dl:84},
  {id:'lb-27',f:'CM',cat:'modeles',t:'Reporting mensuel',desc:'Présentation PowerPoint pour restituer les résultats d’une campagne.',fmt:'PPTX',size:'2,1 Mo',lic:'Réservé EVC',d:-18,dl:59},
  {id:'lb-28',f:'CM',cat:'graphiques',t:'Templates de stories et carrousels',desc:'60 modèles Canva et Photoshop aux formats 1080 × 1920 et 1080 × 1350.',fmt:'ZIP · PSD + liens Canva',size:'180 Mo',lic:'Réservé EVC',d:-10,dl:72},
  {id:'lb-29',f:'CM',cat:'tutos',t:'Meta Ads Manager pas à pas',desc:'Créer, cibler et suivre une campagne publicitaire en 14 étapes.',fmt:'Vidéo',size:'22 min',lic:'Réservé EVC',d:-22,dl:90},

  /* Bureautique et informatique */
  {id:'lb-40',f:'GIA',cat:'logiciels',t:'Microsoft 365 (Word, Excel, PowerPoint)',desc:'Installateur officiel de la suite Office utilisée en cours.',fmt:'Site officiel',size:'—',lic:'Licence payante',os:'Windows · macOS',url:'https://www.microsoft.com/fr-fr/microsoft-365',key:true,d:-40,dl:141},
  {id:'lb-41',f:'GIA',cat:'logiciels',t:'LibreOffice',desc:'Suite bureautique libre et compatible avec les fichiers Word, Excel et PowerPoint.',fmt:'Site officiel',size:'≈ 350 Mo',lic:'Libre et gratuit',os:'Windows · macOS · Linux',url:'https://fr.libreoffice.org/download/telecharger-libreoffice/',d:-40,dl:66},
  {id:'lb-42',f:'GIA',cat:'logiciels',t:'Google Chrome',desc:'Navigateur recommandé pour Gmail, Drive, Docs, Sheets et Meet.',fmt:'Site officiel',size:'≈ 100 Mo',lic:'Gratuit',os:'Windows · macOS · mobile',url:'https://www.google.com/intl/fr/chrome/',key:true,d:-40,dl:118},
  {id:'lb-43',f:'GIA',cat:'ebooks',t:'Excel : les 50 formules essentielles',desc:'SOMME.SI, RECHERCHEV, SI imbriqués, tableaux croisés dynamiques… avec exemples.',fmt:'PDF',size:'4,3 Mo',lic:'Réservé EVC',key:true,d:-26,dl:157},
  {id:'lb-44',f:'GIA',cat:'ebooks',t:'Word : rédiger des documents professionnels',desc:'Styles, sommaires automatiques, publipostage et mise en page.',fmt:'PDF',size:'1,8 Mo',lic:'Réservé EVC',d:-26,dl:93},
  {id:'lb-45',f:'GIA',cat:'modeles',t:'Pack CV et lettres de motivation',desc:'8 modèles Word sobres et modernes, prêts à personnaliser.',fmt:'ZIP · DOCX',size:'3,4 Mo',lic:'Réservé EVC',d:-4,dl:39},
  {id:'lb-46',f:'GIA',cat:'modeles',t:'Factures, devis et bons de commande',desc:'Modèles Excel avec calculs automatiques (TVA, totaux, remises).',fmt:'XLSX',size:'310 Ko',lic:'Réservé EVC',d:-15,dl:61},
  {id:'lb-47',f:'GIA',cat:'supports',t:'Exercices Excel — niveau 1 à 3',desc:'Énoncés et corrigés des exercices faits en séance.',fmt:'XLSX',size:'410 Ko',lic:'Réservé EVC',d:-21,dl:102},
  {id:'lb-48',f:'GIA',cat:'tutos',t:'Google Workspace — prise en main',desc:'Gmail, Drive, Docs, Sheets et Meet en 25 minutes.',fmt:'Vidéo',size:'25 min',lic:'Réservé EVC',d:-32,dl:87},

  /* Toutes les formations */
  {id:'lb-60',f:'ALL',cat:'supports',t:"Guide de l'étudiant EVC 2026",desc:'Fonctionnement de l’école, MY EVC, assiduité, projets et certification.',fmt:'PDF',size:'2,4 Mo',lic:'Réservé EVC',key:true,d:-40,dl:305},
  {id:'lb-61',f:'ALL',cat:'supports',t:'Règlement intérieur',desc:'Règles de vie, assiduité et conditions de certification.',fmt:'PDF',size:'380 Ko',lic:'Réservé EVC',d:-40,dl:190},
  {id:'lb-62',f:'ALL',cat:'modeles',t:'Modèle de rapport de fin de formation',desc:'Trame officielle du rapport à rendre avant la certification.',fmt:'DOCX',size:'190 Ko',lic:'Réservé EVC',d:-30,dl:121},
  {id:'lb-63',f:'ALL',cat:'polices',t:'Montserrat et Inter',desc:'Deux familles polyvalentes pour vos rapports, CV et présentations.',fmt:'Site officiel',size:'—',lic:'Libre (OFL)',os:'Windows · macOS',url:'https://fonts.google.com/specimen/Montserrat',d:-12,dl:58},
  {id:'lb-64',f:'ALL',cat:'ebooks',t:'Trouver ses premiers clients en freelance',desc:'Se présenter, fixer ses prix, rédiger un devis et fidéliser.',fmt:'PDF',size:'3,0 Mo',lic:'Réservé EVC',d:-2,dl:22}];

const ADMIN_ROLES = ['Super Administrateur','Direction Générale','Responsable pédagogique','Responsable administratif','Responsable financier','Formateur principal','Formateur adjoint','Responsable certification','Responsable communication','Support technique'];
const DOCS = [
  {t:"Guide de l'étudiant EVC 2026",cat:'Guides',f:'Toutes',v:'3.1',d:'02/10/2026',acc:'Étudiants, formateurs'},{t:'Règlement intérieur',cat:'Règlements',f:'Toutes',v:'2.0',d:'01/09/2026',acc:'Tous'},
  {t:'Charte graphique — guide complet',cat:'Bibliothèque',f:'DG',v:'1.4',d:'15/09/2026',acc:'Étudiants DG'},{t:'Modèle de rapport de fin de formation',cat:'Modèles de documents',f:'Toutes',v:'1.2',d:'20/09/2026',acc:'Étudiants'},
  {t:'Calendrier éditorial',cat:'Bibliothèque',f:'CM',v:'1.0',d:'10/10/2026',acc:'Étudiants CM'},{t:'Rapport final — Moussa Diallo (CM)',cat:'Rapports',f:'CM',v:'final',d:'28/07/2026',acc:'Pédagogie'},
  {t:'Pressbook — Grace Mensah',cat:'Pressbooks',f:'DG',v:'final',d:'25/07/2026',acc:'Pédagogie, recruteurs'},{t:'Modèle de certificat 2026',cat:'Certificats',f:'Toutes',v:'2.2',d:'05/07/2026',acc:'Certification'},
  {t:'Archives Session Février 2026',cat:'Archives',f:'Toutes',v:'—',d:'31/07/2026',acc:'Direction'}];
const COMPANIES = [{n:'Agence Sankofa',s:'Communication',c:'Abidjan',o:3,h:4},{n:'Orange Côte d\'Ivoire',s:'Télécoms',c:'Abidjan',o:2,h:2},{n:'Imprimerie Nouvelle',s:'Impression',c:'Abidjan',o:1,h:3},{n:'Jumia CI',s:'E-commerce',c:'Abidjan',o:2,h:1},{n:'Studio Wôrô',s:'Design',c:'Dakar',o:1,h:1},{n:'Cabinet Konan & Associés',s:'Conseil',c:'Abidjan',o:1,h:0}];
const OFFERS = [{t:'Stagiaire graphiste',co:'Agence Sankofa',type:'Stage',f:'DG',d:'6 mois',apps:8,st:'Ouverte'},{t:'Community manager junior',co:'Jumia CI',type:'Emploi',f:'CM',d:'CDD 12 mois',apps:14,st:'Ouverte'},{t:'Assistant(e) bureautique',co:'Cabinet Konan & Associés',type:'Stage',f:'GIA',d:'3 mois',apps:6,st:'Ouverte'},{t:'Infographiste print',co:'Imprimerie Nouvelle',type:'Emploi',f:'DG',d:'CDI',apps:5,st:'Pourvue'},{t:'Chargé(e) de contenus',co:"Orange Côte d'Ivoire",type:'Stage',f:'CM',d:'6 mois',apps:21,st:'Ouverte'}];
const ADMIN_TICKETS = [
  {id:'TCK-2077',from:'Aya Kouassi',cat:'Paiement',prio:'Haute',t:'Reçu Orange Money non reçu',st:'En cours',to:'Service financier',d:'20/10/2026'},
  {id:'TCK-2079',from:'Kevin Ahoua',cat:'Accès plateforme',prio:'Critique',t:'Impossible de me connecter depuis hier',st:'Ouvert',to:'—',d:'22/10/2026'},
  {id:'TCK-2076',from:'Grace Mensah',cat:'Pédagogie',prio:'Moyenne',t:'Erreur sur mon résultat de QCM Illustrator',st:'En cours',to:'Responsable pédagogique',d:'19/10/2026'},
  {id:'TCK-2074',from:'Ousmane Cissé',cat:'Certification',prio:'Basse',t:'Date de remise des certificats ?',st:'Résolu',to:'Responsable certification',d:'17/10/2026'},
  {id:'TCK-2072',from:'Nadia Touré',cat:'Technique',prio:'Moyenne',t:'Téléversement bloqué au-delà de 50 Mo',st:'Ouvert',to:'Support technique',d:'16/10/2026'}];
const AUDIT = [
  {d:'22/10/2026 08:57',u:'Awa Bamba',r:'Responsable financier',a:'Paiement enregistré',o:'REC-2026-1344 — Hamed Fofana — 75 000 FCFA',ip:'102.67.14.22'},
  {d:'22/10/2026 08:41',u:'Système',r:'Automatisation',a:'Relance financière envoyée',o:'12 étudiants — WhatsApp + email',ip:'—'},
  {d:'21/10/2026 18:12',u:'Marc Yao',r:'Formateur principal',a:'Travail corrigé',o:'Mise en page — magazine 8 pages — Aya Kouassi',ip:'41.202.88.10'},
  {d:'21/10/2026 16:30',u:'Didier Koné',r:'Super Administrateur',a:'Permission modifiée',o:'Formateur adjoint : Finances (lecture) retirée',ip:'102.67.14.10'},
  {d:'21/10/2026 11:05',u:'Sophie Amon',r:'Responsable administratif',a:'Préinscription validée',o:'PRE-0309 → EVC-26-0188',ip:'102.67.14.31'},
  {d:'20/10/2026 09:15',u:'Système',r:'Automatisation',a:'Compte suspendu',o:'Ibrahim Sanogo — solde impayé > 30 jours',ip:'—'},
  {d:'19/10/2026 14:48',u:'Didier Koné',r:'Super Administrateur',a:'Connexion MFA',o:'Code TOTP vérifié',ip:'102.67.14.10'},
  {d:'18/10/2026 10:20',u:'Jules Ané',r:'Responsable certification',a:'Certificat généré',o:'EVC-CERT-2026-0451 — Grace Mensah (DG)',ip:'102.67.14.40'}];
