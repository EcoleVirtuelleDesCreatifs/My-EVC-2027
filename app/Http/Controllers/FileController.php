<?php

namespace App\Http\Controllers;

use App\Models\CandidatureFile;
use App\Models\Resource;
use App\Models\SubmissionFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Téléchargements protégés : bibliothèque, dépôts de projets, pièces des candidatures. */
class FileController extends Controller
{
    public function resource(Request $request, Resource $resource)
    {
        $student = $request->user()->student;
        abort_unless($student && $resource->published && (! $resource->formation_id || $student->formations->contains('id', $resource->formation_id)), 403);

        if ($student->downloads()->firstOrCreate(['resource_id' => $resource->id])->wasRecentlyCreated) {
            $resource->increment('downloads_count');
        }
        if ($resource->isExternal()) {
            return redirect()->away($resource->url);
        }
        abort_unless($resource->path && Storage::disk('local')->exists($resource->path), 404, 'Fichier indisponible pour le moment.');

        return Storage::disk('local')->download($resource->path, Str::slug($resource->title).'.'.pathinfo($resource->path, PATHINFO_EXTENSION));
    }

    public function submission(Request $request, SubmissionFile $file)
    {
        $a = $file->submission->assignment->load('project', 'student');
        $u = $request->user();
        $allowed = $u->id === $a->student->user_id
            || ($u->space() === 'formateur' && in_array($a->project->formation_id, $u->taughtFormationIds()))
            || $u->can('admin.projets') || $u->can('admin.etudiants');
        abort_unless($allowed, 403);
        abort_unless(Storage::disk('local')->exists($file->path), 404, 'Fichier de démonstration : il n\'existe pas sur le disque.');

        return Storage::disk('local')->download($file->path, $file->original_name);
    }

    public function candidature(CandidatureFile $file)
    {
        abort_unless(Storage::disk('local')->exists($file->path), 404);

        return Storage::disk('local')->download($file->path, $file->original_name);
    }
}
