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
    /** Attribue un projet à plusieurs étudiants. Renvoie [attribués, ignorés]. */
    public function assign(Project $project, Collection $students, Carbon $due, bool $notify = true): array
    {
        $done = 0;
        $skip = 0;
        foreach ($students as $s) {
            /** @var Student $s */
            if (! $s->formations->contains('id', $project->formation_id)) {
                $skip++;

                continue;
            }
            $a = Assignment::firstOrNew(['project_id' => $project->id, 'student_id' => $s->id]);
            if ($a->exists && $a->status !== 'valide') {
                $skip++;

                continue;
            }
            $a->fill(['due_on' => $due, 'status' => 'afaire', 'assigned_by' => auth()->id(), 'appreciation' => null,
                'started_at' => null, 'submitted_at' => null, 'validated_at' => null])->save();
            $a->log('attribution', 'Projet attribué, à rendre le '.$due->translatedFormat('j F Y').'.');
            if ($notify) {
                $s->user->notify(new Avis('Nouveau projet', '« '.$project->title.' » — à rendre le '.$due->translatedFormat('j F').'.', route('etudiant.projet', $a), 'fa-pen-nib', 'cor', true));
            }
            $done++;
        }
        AuditLog::record('Projet attribué', $project->title.' → '.$done.' étudiant(s)');

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
        $this->team($a)->each->notify(new Avis('Projet rendu', $a->student->name.' a rendu « '.$a->project->title.' ».',
            route('formateur.corrections'), 'fa-cloud-arrow-up', 'info'));
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
        $a->log('commentaire', $body);
        $mine = auth()->id() === $a->student->user_id;
        $targets = $mine ? $this->team($a) : collect([$a->student->user]);
        $targets->each->notify(new Avis('Nouveau commentaire', '« '.$a->project->title.' » : '.str($body)->limit(90),
            $mine ? route('formateur.corrections') : route('etudiant.projet', $a), 'fa-comment', 'lav'));
    }

    /** Formateurs de la formation du projet. */
    private function team(Assignment $a): Collection
    {
        return User::whereIn('id', Trainer::where('formation_id', $a->project->formation_id)->pluck('user_id'))->get();
    }
}
