<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\Site\FormController;
use App\Http\Controllers\Site\NewsController;
use App\Http\Controllers\Site\PageController;
use App\Livewire\Admin;
use App\Livewire\Etudiant;
use App\Livewire\Formateur;
use App\Livewire\Gestion;
use App\Livewire\Shared;
use Illuminate\Support\Facades\Route;

/* ==========================================================================
   Site vitrine
   ========================================================================== */
Route::get('/', [PageController::class, 'home'])->name('site.accueil');
Route::get('/actualites', [NewsController::class, 'index'])->name('site.actualites');
Route::get('/actualites/{news}', [NewsController::class, 'show'])->name('site.actualite');
Route::get('/candidature/suivi', [FormController::class, 'suivi'])->name('candidature.suivi');
Route::get('/certificats/verifier', [FormController::class, 'verifier'])->name('certificat.verifier');
Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('site.sitemap');

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/preinscription', [FormController::class, 'preinscription'])->name('preinscription.store');
    Route::post('/contact', [FormController::class, 'contact'])->name('contact.store');
    Route::post('/newsletter', [FormController::class, 'newsletter'])->name('newsletter.store');
});

/* Anciennes adresses de la maquette (…/page.html) */
Route::get('/actualite-{slug}.html', fn (string $slug) => redirect()->route('site.actualite', $slug, 301));
Route::get('/{page}.html', fn (string $page) => redirect($page === 'index' ? '/' : '/'.$page, 301))->where('page', '[a-z0-9-]+');

/* ==========================================================================
   MY EVC — connexion
   ========================================================================== */
Route::prefix('myevc')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/connexion', [AuthController::class, 'login'])->name('login.attempt');
        Route::get('/mot-de-passe-oublie', [AuthController::class, 'showForgot'])->name('password.request');
        Route::post('/mot-de-passe-oublie', [AuthController::class, 'sendReset'])->middleware('throttle:4,1')->name('password.email');
        Route::get('/reinitialiser/{token}', [AuthController::class, 'showReset'])->name('password.reset');
        Route::post('/reinitialiser', [AuthController::class, 'reset'])->name('password.update');
    });
    Route::post('/deconnexion', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

    Route::middleware(['auth', 'active'])->group(function () {
        Route::get('/', fn () => redirect()->route(auth()->user()->homeRoute()))->name('myevc');
        Route::get('/notifications', Shared\Notifications::class)->name('myevc.notifications');
        Route::get('/profil', Shared\Profil::class)->name('myevc.profil');
        Route::get('/fichiers/depot/{file}', [FileController::class, 'submission'])->name('fichier.depot');
        Route::get('/messagerie', Shared\Messagerie::class)->name('myevc.messagerie');
        Route::get('/messagerie/fichier/{message}', [FileController::class, 'message'])->name('fichier.message');
        Route::get('/videos/{video}/fichier', [FileController::class, 'video'])->name('fichier.video');
        Route::get('/reco/fichier/{entry}', [FileController::class, 'recoEntry'])->name('fichier.reco');
        Route::get('/reco/rapport/{project}', [FileController::class, 'recoReport'])->name('fichier.reco.rapport');

        /* ---------- Espace étudiant ---------- */
        Route::prefix('etudiant')->name('etudiant.')->middleware('role:etudiant')->group(function () {
            Route::get('/', Etudiant\Accueil::class)->name('accueil');
            Route::get('/cours', Etudiant\Cours::class)->name('cours');
            Route::get('/projets', Etudiant\Projets::class)->name('projets');
            Route::get('/projets/{assignment}', Etudiant\ProjetDetail::class)->name('projet');
            Route::get('/bibliotheque', Etudiant\Bibliotheque::class)->name('bibliotheque');
            Route::get('/bibliotheque/{resource}/telecharger', [FileController::class, 'resource'])->name('ressource');
            Route::get('/paiements', Etudiant\Paiements::class)->name('paiements');
            Route::get('/certificat', Etudiant\Certificat::class)->name('certificat');
            Route::get('/videos', Etudiant\WebTv::class)->name('webtv');
            Route::get('/videos/{video}', Etudiant\Video::class)->name('video');
            Route::get('/reco-60', Etudiant\Reco::class)->name('reco');
            Route::get('/quiz', Etudiant\Quiz::class)->name('quiz');
            Route::get('/quiz/{quiz}', Etudiant\QuizPassage::class)->name('quiz.passer');
        });

        /* ---------- Espace formateur ---------- */
        Route::prefix('formateur')->name('formateur.')->middleware('role:formateur')->group(function () {
            Route::get('/', Formateur\Accueil::class)->name('accueil');
            Route::get('/seances', Formateur\Seances::class)->name('seances');
            Route::get('/seances/{seance}/appel', Formateur\Appel::class)->name('appel');
            Route::get('/corrections', Formateur\Corrections::class)->name('corrections');
            Route::get('/bibliotheque', Admin\Bibliotheque::class)->name('bibliotheque');
            Route::get('/webtv', Gestion\WebTv::class)->name('webtv');
            Route::get('/reco-60', Gestion\Reco::class)->name('reco');
            Route::get('/reco-60/{project}', Gestion\RecoFiche::class)->name('reco.fiche');
            Route::get('/quiz', Gestion\Quiz::class)->name('quiz');
            Route::get('/quiz/{quiz}', Gestion\QuizEditeur::class)->name('quiz.editeur');
        });

        /* ---------- Administration ---------- */
        Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
            Route::get('/', Admin\Dashboard::class)->middleware('can:admin.dashboard')->name('dashboard');
            Route::get('/candidatures', Admin\Candidatures::class)->middleware('can:admin.candidatures')->name('candidatures');
            Route::get('/candidatures/{candidature}', Admin\CandidatureFiche::class)->middleware('can:admin.candidatures')->name('candidature');
            Route::get('/candidatures/fichier/{file}', [FileController::class, 'candidature'])->middleware('can:admin.candidatures')->name('candidature.fichier');
            Route::get('/etudiants', Admin\Etudiants::class)->middleware('can:admin.etudiants')->name('etudiants');
            Route::get('/etudiants/{student}', Admin\EtudiantFiche::class)->middleware('can:admin.etudiants')->name('etudiant');
            Route::get('/formations', Admin\Formations::class)->middleware('can:admin.formations')->name('formations');
            Route::get('/seances', Admin\Seances::class)->middleware('can:admin.formations')->name('seances');
            Route::get('/projets', Admin\Projets::class)->middleware('can:admin.projets')->name('projets');
            Route::get('/disponibilite', Admin\Disponibilite::class)->middleware('can:admin.projets')->name('disponibilite');
            Route::get('/finances', Admin\Finances::class)->middleware('can:admin.finances')->name('finances');
            Route::get('/bibliotheque', Admin\Bibliotheque::class)->middleware('can:admin.bibliotheque')->name('bibliotheque');
            Route::get('/actualites', Admin\Actualites::class)->middleware('can:admin.actualites')->name('actualites');
            Route::get('/messages', Admin\Messages::class)->middleware('can:admin.messages')->name('messages');
            Route::get('/certificats', Admin\Certificats::class)->middleware('can:admin.certificats')->name('certificats');
            Route::get('/utilisateurs', Admin\Utilisateurs::class)->middleware('can:admin.utilisateurs')->name('utilisateurs');
            Route::get('/audit', Admin\Audit::class)->middleware('can:admin.audit')->name('audit');
            Route::get('/parametres', Admin\Parametres::class)->middleware('can:admin.parametres')->name('parametres');
            Route::get('/webtv', Gestion\WebTv::class)->middleware('can:admin.webtv')->name('webtv');
            Route::get('/reco-60', Gestion\Reco::class)->middleware('can:admin.reco')->name('reco');
            Route::get('/reco-60/{project}', Gestion\RecoFiche::class)->middleware('can:admin.reco')->name('reco.fiche');
            Route::get('/quiz', Gestion\Quiz::class)->middleware('can:admin.qcm')->name('quiz');
            Route::get('/quiz/{quiz}', Gestion\QuizEditeur::class)->middleware('can:admin.qcm')->name('quiz.editeur');
        });

        /* ---------- Documents PDF ---------- */
        Route::get('/recus/{payment}.pdf', [PdfController::class, 'receipt'])->name('pdf.recu');
        Route::get('/certificats/{certificate}.pdf', [PdfController::class, 'certificate'])->name('pdf.certificat');
        Route::get('/attestation/{student}.pdf', [PdfController::class, 'attestation'])->name('pdf.attestation');
    });
});

/* Pages du site (en dernier : adresse /{page}) */
Route::get('/{page}', [PageController::class, 'show'])->whereIn('page', PageController::PAGES)->name('site.page');
