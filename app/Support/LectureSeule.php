<?php

namespace App\Support;

use App\Models\User;

/** Compte étudiant en lecture seule après la fin de la formation : rubriques masquées et actions bloquées (messagerie autorisée). */
class LectureSeule
{
    /** Rubriques retirées de l'espace étudiant. */
    public const HIDDEN_ROUTES = ['etudiant.bibliotheque', 'etudiant.ressource', 'etudiant.demarrage', 'etudiant.projets', 'etudiant.projet', 'etudiant.quiz', 'etudiant.quiz.passer'];

    /** Actions Livewire bloquées (dépôts, réponses, modifications). Les consultations restent possibles. */
    public const BLOCKED_ACTIONS = [
        \App\Livewire\Etudiant\Accueil::class => ['rejoindre'],
        \App\Livewire\Etudiant\Cours::class => ['pointer'],
        \App\Livewire\Etudiant\ProjetDetail::class => ['demarrer', 'deposer', 'modifierRendu', 'enregistrerModification', 'commenter'],
        \App\Livewire\Etudiant\QuizPassage::class => ['commencer', 'repondre', 'aller', 'soumettre'],
        \App\Livewire\Etudiant\Rapport::class => ['deposer'],
        \App\Livewire\Etudiant\Reco::class => ['creer', 'basculerEtape', 'ajouterEntree', 'ajouterKpi', 'enregistrerKpi', 'rendreRapport'],
        \App\Livewire\Etudiant\Bibliotheque::class => ['voir'],
        \App\Livewire\Shared\Profil::class => ['enregistrer'],
    ];

    public const MESSAGE = 'Votre formation est terminée : votre compte est en lecture seule. Vous pouvez toujours écrire à l’équipe depuis la messagerie.';

    public static function applies(?User $user): bool
    {
        return $user && $user->space() === 'etudiant' && $user->student?->isReadOnly();
    }

    public static function blocks(object $component, string $method): bool
    {
        return in_array($method, self::BLOCKED_ACTIONS[get_class($component)] ?? [], true);
    }
}
