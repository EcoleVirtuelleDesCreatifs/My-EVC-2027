<?php

namespace App\Http\Controllers;

use App\Models\CandidatureFile;
use App\Models\Message;
use App\Models\RecoEntry;
use App\Models\RecoProject;
use App\Models\Video;
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
        $this->authorizeSubmission($request, $file);
        abort_unless(Storage::disk('local')->exists($file->path), 404, 'Ce fichier n’est plus disponible. Contactez l’administration.');

        return Storage::disk('local')->download($file->path, $file->original_name);
    }

    /** Aperçu d'une image rendue (portfolio), redimensionnée et affichée dans le navigateur. */
    public function submissionPreview(Request $request, SubmissionFile $file)
    {
        $this->authorizeSubmission($request, $file);
        $ext = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
        if ($mime = \App\Support\Portfolio::INLINE[$ext] ?? null) {
            abort_unless(Storage::disk('local')->exists($file->path), 404);

            return response()->file(Storage::disk('local')->path($file->path), ['Content-Type' => $mime, 'Cache-Control' => 'private, max-age=86400', 'X-Content-Type-Options' => 'nosniff']);
        }
        $path = \App\Support\Portfolio::thumbnail($file, (int) $request->query('w', 960));
        abort_unless($path, 404);

        return response()->file($path, ['Content-Type' => 'image/jpeg', 'Cache-Control' => 'private, max-age=86400']);
    }

    private function authorizeSubmission(Request $request, SubmissionFile $file): void
    {
        $a = $file->submission->assignment->load('project', 'student');
        $u = $request->user();
        $allowed = $u->id === $a->student->user_id
            || ($u->space() === 'formateur' && in_array($a->project->formation_id, $u->taughtFormationIds()))
            || $u->can('admin.projets') || $u->can('admin.etudiants');
        abort_unless($allowed, 403);
    }

    public function finalReport(Request $request, \App\Models\FinalReport $report)
    {
        $u = $request->user();
        abort_unless($u->id === $report->student->user_id || $u->can('admin.etudiants')
            || ($u->space() === 'formateur' && in_array($report->formation_id, $u->taughtFormationIds())), 403);
        abort_unless(Storage::disk('local')->exists($report->path), 404, 'Ce fichier n’est plus disponible.');

        return Storage::disk('local')->download($report->path, $report->original_name);
    }

    public function expenseReceipt(Request $request, \App\Models\Expense $expense)
    {
        abort_unless($request->user()->can('admin.finances'), 403);
        abort_unless($expense->receipt_path && Storage::disk('local')->exists($expense->receipt_path), 404, 'Justificatif indisponible.');

        return Storage::disk('local')->download($expense->receipt_path, $expense->receipt_name ?: basename($expense->receipt_path));
    }

    /** Outil de démarrage : réservé aux étudiants de la formation (ou de toutes) et à l'équipe. */
    public function starterTool(Request $request, \App\Models\StarterTool $tool)
    {
        $u = $request->user();
        $student = $u->student;
        $allowed = $u->can('admin.bibliotheque') || $u->space() === 'formateur'
            || ($student && ! $student->isReadOnly() && $tool->published && (! $tool->formation_id || $student->formations->contains('id', $tool->formation_id)));
        abort_unless($allowed, 403);
        abort_unless($tool->path && Storage::disk('local')->exists($tool->path), 404, 'Ce fichier n’est plus disponible.');
        if ($student) {
            \Illuminate\Support\Facades\DB::table('starter_tools')->where('id', $tool->id)->increment('downloads');
        }

        return Storage::disk('local')->download($tool->path, $tool->original_name);
    }

    public function candidature(CandidatureFile $file)
    {
        abort_unless(Storage::disk('local')->exists($file->path), 404);

        return Storage::disk('local')->download($file->path, $file->original_name);
    }

    /** Lecture d'une vidéo hébergée (avec reprise : requêtes « Range » gérées). */
    public function video(Request $request, Video $video)
    {
        $u = $request->user();
        $allowed = ($u->student && $video->published && $u->student->formations->contains('id', $video->formation_id))
            || in_array($video->formation_id, $u->taughtFormationIds()) || $u->can('admin.webtv');
        abort_unless($allowed && $video->path && Storage::disk('local')->exists($video->path), 403);

        return response()->file(Storage::disk('local')->path($video->path), ['Content-Type' => 'video/mp4', 'Cache-Control' => 'private, max-age=3600']);
    }

    public function message(Request $request, Message $message)
    {
        abort_unless($message->conversation->participants()->where('users.id', $request->user()->id)->exists() && $message->file_path, 403);
        abort_unless(Storage::disk('local')->exists($message->file_path), 404);

        return Storage::disk('local')->download($message->file_path, $message->file_name);
    }

    public function recoEntry(Request $request, RecoEntry $entry)
    {
        $this->authorizeReco($request, $entry->project);
        abort_unless($entry->file_path && Storage::disk('local')->exists($entry->file_path), 404);

        return Storage::disk('local')->download($entry->file_path, $entry->file_name);
    }

    public function recoReport(Request $request, RecoProject $project)
    {
        $this->authorizeReco($request, $project);
        abort_unless($project->report_path && Storage::disk('local')->exists($project->report_path), 404);

        return Storage::disk('local')->download($project->report_path, $project->report_name);
    }

    private function authorizeReco(Request $request, RecoProject $p): void
    {
        $u = $request->user();
        abort_unless($u->id === $p->student->user_id || in_array($p->formation_id, $u->taughtFormationIds()) || $u->can('admin.reco'), 403);
    }
}
