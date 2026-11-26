<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AuditLog;
use App\Models\Project;
use App\Models\Student;
use App\Models\Trainer;
use App\Models\User;
use App\Notifications\Avis;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Cycle de vie d'un projet : attribué → démarré → rendu → validé ou à reprendre. */
class ProjectService
{
    /** Réglages par défaut du parcours automatique (modifiables dans Administration › Projets › Attribution automatique). */
    public const AUTO_DEFAULTS = ['enabled' => true, 'count' => 5, 'days' => 5, 'delay' => 60];

    /** Réglages en vigueur : activé, nombre de projets, délai de remise (jours), attente après un rendu (minutes). */
    public static function autoConfig(): array
    {
        $get = fn (string $k) => \App\Models\Setting::get('auto_projects_'.$k, (string) (int) self::AUTO_DEFAULTS[$k]);

        return ['enabled' => $get('enabled') === '1', 'count' => max(1, min(20, (int) $get('count'))),
            'days' => max(1, min(60, (int) $get('days'))), 'delay' => max(0, min(10080, (int) $get('delay')))];
    }

    public static function saveAutoConfig(array $config): void
    {
        foreach (['enabled', 'count', 'days', 'delay'] as $k) {
            \App\Models\Setting::put('auto_projects_'.$k, (string) (int) $config[$k]);
        }
    }

    /** Libellé lisible d'une attente en minutes (« 1 heure », « 30 minutes », « immédiatement »). */
    public static function delayLabel(int $minutes): string
    {
        return match (true) {
            $minutes === 0 => 'immédiatement',
            $minutes % 1440 === 0 => ($d = $minutes / 1440).' jour'.($d > 1 ? 's' : ''),
            $minutes % 60 === 0 => ($h = $minutes / 60).' heure'.($h > 1 ? 's' : ''),
            default => $minutes.' minute'.($minutes > 1 ? 's' : ''),
        };
    }

    /** Projets du parcours automatique d'une formation, dans l'ordre du catalogue (code du titre : AI-01, CM-01…). */
    public static function autoProjects(int $formationId): Collection
    {
        return Project::where('formation_id', $formationId)->where('published', true)->orderBy('title')->orderBy('id')->limit(self::autoConfig()['count'])->get();
    }

    /**
     * Prochain projet automatique d'un étudiant pour une formation, et moment de son envoi :
     * immédiatement s'il n'a encore rien reçu, sinon après l'attente configurée suivant son dernier rendu.
     * Null si le parcours est désactivé, terminé, ou si un projet du parcours est encore ouvert.
     *
     * @return array{project: Project, at: Carbon}|null
     */
    public function nextAuto(Student $student, int $formationId): ?array
    {
        $config = self::autoConfig();
        $student->loadMissing('formations', 'user');
        if (! $config['enabled'] || $student->status !== 'actif' || $student->isReadOnly() || ! $student->formations->contains('id', $formationId)) {
            return null;
        }
        $projects = self::autoProjects($formationId);
        $assigned = $student->assignments()->whereIn('project_id', $projects->pluck('id'))->get();
        $next = $projects->first(fn (Project $p) => ! $assigned->contains('project_id', $p->id));
        if (! $next || $assigned->whereIn('status', Assignment::OPEN)->isNotEmpty()) {
            return null;
        }
        $last = $assigned->map(fn (Assignment $a) => $a->submitted_at ?? $a->updated_at)->max();

        return ['project' => $next, 'at' => $last ? $last->copy()->addMinutes($config['delay']) : now()];
    }

    /** Attribue le projet suivant du parcours s'il est dû (ou immédiatement avec $force). L'étudiant est prévenu par e-mail. */
    public function assignNextAuto(Student $student, int $formationId, bool $force = false): ?Assignment
    {
        $next = $this->nextAuto($student, $formationId);
        if (! $next || (! $force && $next['at']->isFuture())) {
            return null;
        }
        $this->assign($next['project']->loadMissing('formation'), collect([$student]), now()->addDays(self::autoConfig()['days'])->startOfDay(), true, true);

        return $student->assignments()->where('project_id', $next['project']->id)->first();
    }

    /** Envois automatiques prévus pour tous les étudiants actifs : [student, project, at], par date. */
    public function autoPlan(): Collection
    {
        if (! self::autoConfig()['enabled']) {
            return collect();
        }
        $rows = collect();
        Student::where('status', 'actif')->with('formations', 'user')->each(function (Student $s) use ($rows) {
            foreach ($s->formations as $f) {
                if ($n = $this->nextAuto($s, $f->id)) {
                    $rows->push(['student' => $s, 'formation' => $f, 'project' => $n['project'], 'at' => $n['at']]);
                }
            }
        });

        return $rows->sortBy('at')->values();
    }

    /** Attribue un projet à plusieurs étudiants. Renvoie [attribués, ignorés]. */
    public function assign(Project $project, Collection $students, Carbon $due, bool $notify = true, bool $auto = false): array
    {
        $done = 0;
        $skip = 0;
        foreach ($students as $s) {
            /** @var Student $s */
            if (! $s->formations->contains('id', $project->formation_id)) {
                $skip++;

                continue;
            }
            $a = Assignment::firstOrCreate(['project_id' => $project->id, 'student_id' => $s->id],
                ['due_on' => $due, 'status' => 'afaire', 'assigned_by' => $auto ? null : auth()->id()]);
            if (! $a->wasRecentlyCreated) {
                $skip++;

                continue;
            }
            $a->log('attribution', ($auto ? 'Projet attribué automatiquement (parcours des '.self::autoConfig()['count'].' premiers projets)' : 'Projet attribué').', à rendre le '.$due->translatedFormat('j F Y').'.', null, $auto);
            if ($notify) {
                $s->user->notify(new Avis('Nouveau projet', '« '.$project->title.' » — à rendre le '.$due->translatedFormat('j F').'.', route('etudiant.projet', $a), 'fa-pen-nib', 'cor', true, mailData: [
                    'titre' => 'Un nouveau projet vous attend', 'categorie' => 'VOTRE PARCOURS PÉDAGOGIQUE',
                    'introduction' => $auto
                        ? 'Votre prochain projet pratique est disponible : il vous a été attribué automatiquement dans le cadre de votre parcours. Vous disposez de '.self::autoConfig()['days'].' jours pour le réaliser. Consultez sa fiche pour découvrir le brief, les livrables attendus et les critères d’évaluation.'
                        : 'Un nouveau projet pratique vous a été attribué par l’équipe pédagogique. Consultez sa fiche pour découvrir le brief, les livrables attendus et les critères d’évaluation.',
                    'libelleMiseEnAvant' => 'PROJET À RÉALISER', 'miseEnAvant' => $project->title,
                    'details' => ['Formation' => $project->formation->name, 'Type / niveau' => $project->type,
                        'Matricule étudiant' => $s->matricule, 'Date limite de remise' => $due->copy()->locale('fr')->translatedFormat('j F Y')],
                    'actionLabel' => 'Consulter mon projet',
                    'etapes' => ['Lisez l’ensemble des consignes et préparez vos ressources.', 'Démarrez le projet depuis votre fiche MY EVC.', 'Déposez vos fichiers ou liens dans l’espace prévu avant la date limite.'],
                    'conseil' => 'Une question sur le brief ? Échangez avec votre formateur depuis la fiche du projet. Les consignes et les ressources restent accessibles dans votre espace étudiant.',
                ]));
            }
            $done++;
        }
        AuditLog::record($auto ? 'Projet attribué automatiquement' : 'Projet attribué', $project->title.' → '.$done.' étudiant(s)');

        return [$done, $skip];
    }

    public function start(Assignment $a): void
    {
        if ($a->status !== 'afaire') {
            return;
        }
        $a->update(['status' => 'encours', 'started_at' => now()]);
        $a->log('demarrage', 'Projet démarré.');
    }

    /** @param  UploadedFile[]  $files */
    public function submit(Assignment $a, array $files, array $links, ?string $message): void
    {
        if (! in_array($a->status, ['afaire', 'encours', 'reprendre'], true)) {
            throw ValidationException::withMessages(['depot' => 'Ce projet ne peut plus être déposé.']);
        }
        if (! $files && ! $links) {
            throw ValidationException::withMessages(['depot' => 'Ajoutez au moins un fichier ou un lien.']);
        }
        DB::transaction(function () use ($a, $files, $links, $message) {
            $sub = $a->submissions()->create(['message' => $message, 'links' => array_values($links)]);
            foreach ($files as $file) {
                $sub->files()->create(['path' => $file->store('depots/'.$a->id, 'local'), 'original_name' => $file->getClientOriginalName(), 'size' => $file->getSize()]);
            }
            $a->update(['status' => 'soumis', 'submitted_at' => now(), 'started_at' => $a->started_at ?? now()]);
            $a->log('depot', $message ?: 'Travail déposé.');
        });
        if (self::autoConfig()['delay'] === 0) {
            $this->assignNextAuto($a->student, $a->project->formation_id);
        }
        $this->team($a)->each->notify(new Avis('Projet rendu', $a->student->name.' a rendu « '.$a->project->title.' ».',
            route('formateur.corrections'), 'fa-cloud-arrow-up', 'info'));
    }

    /**
     * Modification du rendu par l'étudiant tant qu'il est en cours de validation (statut « soumis ») :
     * retrait de fichiers, ajout de fichiers, liens et message. La date de rendu d'origine est conservée.
     *
     * @param  int[]  $removeIds
     * @param  UploadedFile[]  $files
     */
    public function amend(Assignment $a, array $removeIds, array $files, array $links, ?string $message): void
    {
        $sub = $a->submissions()->with('files')->first();
        if ($a->status !== 'soumis' || ! $sub) {
            throw ValidationException::withMessages(['depot' => 'Ce rendu ne peut plus être modifié : il a déjà été corrigé par votre formateur.']);
        }
        $remove = $sub->files->whereIn('id', $removeIds);
        if ($sub->files->count() - $remove->count() + count($files) === 0 && ! $links) {
            throw ValidationException::withMessages(['depot' => 'Votre rendu doit contenir au moins un fichier ou un lien.']);
        }
        DB::transaction(function () use ($a, $sub, $remove, $files, $links, $message) {
            foreach ($files as $file) {
                $sub->files()->create(['path' => $file->store('depots/'.$a->id, 'local'), 'original_name' => $file->getClientOriginalName(), 'size' => $file->getSize()]);
            }
            $sub->files()->whereIn('id', $remove->pluck('id'))->delete();
            $sub->update(['links' => array_values($links), 'message' => $message]);
            $a->log('depot', 'Rendu modifié par l’étudiant'.($remove->count() || $files ? ' ('.collect([$files ? count($files).' fichier(s) ajouté(s)' : null, $remove->count() ? $remove->count().' retiré(s)' : null])->filter()->implode(', ').')' : '').'.');
        });
        $remove->each(fn ($f) => \Illuminate\Support\Facades\Storage::disk('local')->delete($f->path));
        $this->team($a)->each->notify(new Avis('Rendu modifié', $a->student->name.' a modifié son rendu « '.$a->project->title.' » avant correction.',
            route('formateur.corrections'), 'fa-pen', 'info'));
    }

    public function validate(Assignment $a, string $appreciation, ?string $comment): void
    {
        $a->update(['status' => 'valide', 'appreciation' => $appreciation, 'validated_at' => now()]);
        $a->log('validation', 'Projet validé — appréciation : '.$appreciation.'.'.($comment ? ' '.$comment : ''));
        $a->student->user->notify(new Avis('Projet validé', '« '.$a->project->title.' » est validé : '.$appreciation.'. Bravo !', route('etudiant.projet', $a), 'fa-circle-check', 'ok', true));
        AuditLog::record('Projet validé', $a->project->title.' — '.$a->student->name);
    }

    public function rework(Assignment $a, string $comment, ?Carbon $due = null): void
    {
        $a->update(['status' => 'reprendre', 'due_on' => $due ?? $a->due_on->max(now()->addDays(3))]);
        $a->log('reprise', $comment);
        $a->student->user->notify(new Avis('Projet à reprendre', '« '.$a->project->title.' » : '.$comment, route('etudiant.projet', $a), 'fa-rotate', 'warn', true));
    }

    public function comment(Assignment $a, string $body): void
    {
        $user = auth()->user();
        $mine = $user && $user->id === $a->student->user_id;
        abort_unless($user && ($mine || $a->canDiscuss($user)), 403);
        abort_if($mine && $a->student->isReadOnly(), 403);
        $body = trim($body);
        validator(['message' => $body], ['message' => 'required|string|max:2000'])->validate();
        $a->log('commentaire', $body);
        $targets = $mine
            ? $this->team($a)->merge(User::permission('admin.projets')->get())->unique('id')
            : collect([$a->student->user]);
        foreach ($targets->where('status', 'actif')->where('id', '!=', $user->id) as $target) {
            $url = $mine ? route('projet.echanges', $a) : route('etudiant.projet', $a);
            $target->notify(new Avis('Nouveau message sur un projet', $user->name.' — « '.$a->project->title.' » : '.str($body)->limit(160),
                $url, 'fa-comment-dots', 'lav', true, mailData: [
                    'categorie' => 'ÉCHANGES PÉDAGOGIQUES', 'actionLabel' => 'Lire le message et répondre',
                    'details' => ['Étudiant' => $a->student->name, 'Projet' => $a->project->title],
                ]));
        }
    }

    /** Formateurs de la formation du projet. */
    private function team(Assignment $a): Collection
    {
        return User::whereIn('id', Trainer::where('formation_id', $a->project->formation_id)->pluck('user_id'))->get();
    }
}
