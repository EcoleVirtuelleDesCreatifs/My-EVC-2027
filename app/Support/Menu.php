<?php

namespace App\Support;

use App\Models\Assignment;
use App\Models\Candidature;
use App\Models\ContactMessage;
use App\Models\Conversation;
use App\Models\Quiz;
use App\Models\RecoProject;
use App\Models\Resource;
use App\Models\User;

/** Menus de MY EVC par espace : groupes [libellé, [[route, libellé, icône, compteur]], replié ?]. */
class Menu
{
    public static function groups(User $u): array
    {
        return match ($u->space()) {
            'etudiant' => self::etudiant($u),
            'formateur' => self::formateur($u),
            default => self::admin($u),
        };
    }

    public static function bottom(User $u): array
    {
        return match ($u->space()) {
            'etudiant' => [['etudiant.accueil', 'Accueil', 'fa-house-chimney'], ['etudiant.cours', 'Cours', 'fa-calendar-days'], ['etudiant.projets', 'Projets', 'fa-pen-nib'], ['myevc.messagerie', 'Messages', 'fa-message'], ['etudiant.bibliotheque', 'Biblio', 'fa-book-bookmark']],
            'formateur' => [['formateur.accueil', 'Accueil', 'fa-house-chimney'], ['formateur.seances', 'Séances', 'fa-calendar-days'], ['formateur.corrections', 'Corrections', 'fa-pen-to-square'], ['myevc.messagerie', 'Messages', 'fa-message']],
            default => collect([['admin.dashboard', 'Accueil', 'fa-chart-pie', 'admin.dashboard'], ['admin.candidatures', 'Candidats', 'fa-user-plus', 'admin.candidatures'], ['admin.etudiants', 'Étudiants', 'fa-user-graduate', 'admin.etudiants'], ['admin.finances', 'Finances', 'fa-wallet', 'admin.finances']])
                ->filter(fn ($i) => $u->can($i[3]))->values()->all(),
        };
    }

    private static function etudiant(User $u): array
    {
        $s = $u->student;
        $fids = $s?->formations->pluck('id')->all() ?? [];
        $open = $s ? $s->assignments()->whereIn('status', Assignment::OPEN)->count() : 0;
        $got = $s ? $s->downloads()->pluck('resource_id') : collect();
        $new = Resource::visibleFor($fids)->where('created_at', '>=', now()->subDays(Resource::NEW_DAYS))->whereNotIn('id', $got)->count();
        $quiz = $s ? Quiz::visibleFor($fids)->with(['attempts' => fn ($q) => $q->where('student_id', $s->id)])->get()->filter(fn ($q) => $q->stateFor($s)[2])->count() : 0;
        $reco = $s && $s->formations->contains('has_reco', true);

        return [
            ['', array_values(array_filter([['etudiant.accueil', 'Accueil', 'fa-house-chimney'], ['etudiant.cours', 'Mes cours', 'fa-calendar-days'], ['etudiant.projets', 'Mes projets', 'fa-pen-nib', $open],
                $reco ? ['etudiant.reco', 'RECO 60', 'fa-hourglass-half'] : null, ['etudiant.quiz', 'Quiz', 'fa-list-check', $quiz], ['etudiant.webtv', 'Vidéos des cours', 'fa-tv'],
                ['etudiant.bibliotheque', 'Bibliothèque', 'fa-book-bookmark', $new], ['myevc.messagerie', 'Messages', 'fa-message', Conversation::unreadCount($u)],
                ['etudiant.paiements', 'Paiements', 'fa-credit-card'], ['etudiant.certificat', 'Certificat', 'fa-award']])), false],
            ['Plus', [['myevc.notifications', 'Notifications', 'fa-bell'], ['myevc.profil', 'Mon profil', 'fa-user']], true],
        ];
    }

    private static function formateur(User $u): array
    {
        $fids = $u->taughtFormationIds();
        $pending = Assignment::where('status', 'soumis')->whereHas('project', fn ($q) => $q->whereIn('formation_id', $fids))->count();
        $reco = RecoProject::where('status', 'rendu')->whereIn('formation_id', $fids)->count();
        $hasReco = \App\Models\Formation::whereIn('id', $fids)->where('has_reco', true)->exists();

        return [
            ['', array_values(array_filter([['formateur.accueil', 'Accueil', 'fa-house-chimney'], ['formateur.seances', 'Séances et appel', 'fa-calendar-days'], ['formateur.corrections', 'Corrections', 'fa-pen-to-square', $pending],
                $hasReco ? ['formateur.reco', 'RECO 60', 'fa-hourglass-half', $reco] : null, ['formateur.quiz', 'Quiz', 'fa-list-check'], ['formateur.webtv', 'Web TV', 'fa-tv'],
                ['formateur.bibliotheque', 'Bibliothèque', 'fa-book-bookmark'], ['myevc.messagerie', 'Messages', 'fa-message', Conversation::unreadCount($u)]])), false],
            ['Plus', [['myevc.notifications', 'Notifications', 'fa-bell'], ['myevc.profil', 'Mon profil', 'fa-user']], true],
        ];
    }

    private static function admin(User $u): array
    {
        $groups = [
            ['', [
                ['admin.dashboard', 'Tableau de bord', 'fa-chart-pie', null, 'dashboard'],
                ['admin.candidatures', 'Préinscriptions', 'fa-user-plus', fn () => Candidature::whereIn('stage', ['recue', 'paiement'])->count(), 'candidatures'],
                ['admin.etudiants', 'Étudiants', 'fa-user-graduate', null, 'etudiants'],
                ['admin.projets', 'Projets', 'fa-palette', fn () => Assignment::where('status', 'soumis')->count(), 'projets'],
                ['admin.disponibilite', 'Disponibilité', 'fa-user-clock', null, 'projets'],
                ['admin.finances', 'Finances', 'fa-wallet', null, 'finances'],
                ['myevc.messagerie', 'Messagerie', 'fa-message', fn () => Conversation::unreadCount($u), null],
            ], false],
            ['Programmes', [['admin.reco', 'RECO 60', 'fa-hourglass-half', fn () => RecoProject::where('status', 'rendu')->count(), 'reco'], ['admin.quiz', 'Quiz', 'fa-list-check', null, 'qcm'], ['admin.webtv', 'Web TV', 'fa-tv', null, 'webtv']], true],
            ['Scolarité', [['admin.formations', 'Formations et sessions', 'fa-book-open', null, 'formations'], ['admin.seances', 'Séances', 'fa-calendar-days', null, 'formations'], ['admin.certificats', 'Certificats', 'fa-award', null, 'certificats']], true],
            ['Contenus', [['admin.bibliotheque', 'Bibliothèque', 'fa-book-bookmark', null, 'bibliotheque'], ['admin.actualites', 'Actualités', 'fa-newspaper', null, 'actualites'], ['admin.messages', 'Messages du site', 'fa-envelope', fn () => ContactMessage::whereNull('handled_at')->count(), 'messages']], true],
            ['Système', [['admin.utilisateurs', 'Utilisateurs et rôles', 'fa-shield-halved', null, 'utilisateurs'], ['admin.audit', 'Journal d\'audit', 'fa-fingerprint', null, 'audit'], ['admin.parametres', 'Paramètres', 'fa-gear', null, 'parametres']], true],
        ];

        return collect($groups)->map(function ($g) use ($u) {
            $g[1] = collect($g[1])->filter(fn ($i) => $i[4] === null || $u->can('admin.'.$i[4]))
                ->map(fn ($i) => [$i[0], $i[1], $i[2], $i[3] ? ($i[3])() : 0])->values()->all();

            return $g;
        })->filter(fn ($g) => $g[1])->values()->all();
    }
}
