<?php

namespace App\Support;

use App\Models\Assignment;
use App\Models\Student;
use App\Models\SubmissionFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/** Catalogue des projets validés d'un étudiant : œuvre retenue, visuel de couverture et avis du formateur. */
class Portfolio
{
    public const IMAGE = '/\.(jpe?g|png|webp|gif)$/i';

    /** Formats lisibles directement dans le navigateur (aperçu sans téléchargement). */
    public const INLINE = ['pdf' => 'application/pdf', 'mp4' => 'video/mp4', 'mov' => 'video/quicktime'];

    /** Libellé et icône par format de livrable. */
    public const FORMATS = [
        'pdf' => ['PDF', 'fa-file-pdf'], 'zip' => ['Archive ZIP', 'fa-file-zipper'], 'rar' => ['Archive RAR', 'fa-file-zipper'],
        'ai' => ['Illustrator', 'fa-bezier-curve'], 'psd' => ['Photoshop', 'fa-layer-group'], 'indd' => ['InDesign', 'fa-book-open'],
        'eps' => ['EPS vectoriel', 'fa-bezier-curve'], 'svg' => ['SVG vectoriel', 'fa-bezier-curve'],
        'doc' => ['Word', 'fa-file-word'], 'docx' => ['Word', 'fa-file-word'], 'xls' => ['Excel', 'fa-file-excel'], 'xlsx' => ['Excel', 'fa-file-excel'],
        'ppt' => ['PowerPoint', 'fa-file-powerpoint'], 'pptx' => ['PowerPoint', 'fa-file-powerpoint'],
        'mp4' => ['Vidéo MP4', 'fa-file-video'], 'mov' => ['Vidéo MOV', 'fa-file-video'],
    ];

    /** Projets validés, du plus récent au plus ancien, chacun enrichi de `work` (rendu validé), `cover`, `review`. */
    public static function items(Student $student, ?array $formationIds = null): Collection
    {
        return $student->assignments()->where('status', 'valide')
            ->when($formationIds !== null, fn ($q) => $q->whereHas('project', fn ($p) => $p->whereIn('formation_id', $formationIds)))
            ->with(['project.formation', 'submissions.files', 'comments' => fn ($q) => $q->where('kind', 'validation')->with('user')])
            ->orderByDesc('validated_at')->orderByDesc('id')->get()
            ->each(function (Assignment $a) {
                $work = $a->submissions->first(fn ($s) => ! $a->validated_at || $s->created_at->lte($a->validated_at)) ?? $a->submissions->first();
                $deliverables = collect($work?->files ?? [])->map(fn (SubmissionFile $f) => self::deliverable($f))->filter()->values()
                    ->concat(collect($work?->links ?? [])->map(fn (string $url) => ['kind' => 'link', 'url' => $url, 'name' => (string) (parse_url($url, PHP_URL_HOST) ?: $url), 'label' => 'Lien en ligne', 'icon' => 'fa-link']));
                $a->setAttribute('work', $work);
                $a->setAttribute('deliverables', $deliverables);
                $a->setAttribute('visuals', $deliverables->where('kind', 'image')->values());
                $a->setAttribute('cover', $deliverables->firstWhere('kind', 'image')['file'] ?? null);
                $validation = $a->comments->last();
                $review = $validation ? trim((string) preg_replace('/^Projet validé — appréciation : [^.]*\.\s*/u', '', $validation->body)) : '';
                $a->setAttribute('review', $review !== '' ? $review : null);
                $a->setAttribute('reviewer', $validation?->user?->name);
            });
    }

    /**
     * Livrable d'un rendu : image (avec ses dimensions réelles, pour respecter son format), vidéo, document ou fichier source.
     * Les fichiers absents du stockage sont ignorés.
     */
    public static function deliverable(SubmissionFile $file): ?array
    {
        if (! Storage::disk('local')->exists($file->path)) {
            return null;
        }
        $ext = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION) ?: pathinfo($file->path, PATHINFO_EXTENSION));
        $base = ['file' => $file, 'name' => $file->original_name, 'ext' => strtoupper($ext), 'size' => (int) $file->size];
        if (self::isImage($file) && self::thumbnail($file, 960) && ([$w, $h] = @getimagesize(Storage::disk('local')->path($file->path)) ?: [0, 0]) && $w && $h) {
            return $base + ['kind' => 'image', 'width' => $w, 'height' => $h, 'orientation' => $w > $h * 1.15 ? 'paysage' : ($h > $w * 1.15 ? 'portrait' : 'carré'),
                'label' => 'Image '.strtoupper($ext === 'jpeg' ? 'jpg' : $ext), 'icon' => 'fa-image'];
        }
        [$label, $icon] = self::FORMATS[$ext] ?? [strtoupper($ext ?: 'Fichier'), 'fa-file'];

        return $base + ['kind' => in_array($ext, ['mp4', 'mov'], true) ? 'video' : 'file', 'label' => $label, 'icon' => $icon, 'inline' => isset(self::INLINE[$ext])];
    }

    /** Synthèse pour la couverture. */
    public static function stats(Collection $items): array
    {
        return [
            'total' => $items->count(),
            'deliverables' => $items->sum(fn ($a) => $a->deliverables->count()),
            'distinctions' => $items->whereIn('appreciation', ['Excellent', 'Très bien'])->count(),
            'formations' => $items->pluck('project.formation')->unique('id')->values(),
            'types' => $items->pluck('project.type')->filter()->unique()->values(),
            'first' => $items->min('validated_at'),
            'last' => $items->max('validated_at'),
        ];
    }

    public static function isImage(SubmissionFile $file): bool
    {
        return (bool) (preg_match(self::IMAGE, $file->original_name) || preg_match(self::IMAGE, $file->path));
    }

    /** Miniature JPEG mise en cache (stockage privé), générée à la demande. Retourne le chemin absolu, ou null. */
    public static function thumbnail(SubmissionFile $file, int $width = 960): ?string
    {
        $width = in_array($width, [480, 960, 1600], true) ? $width : 960;
        $disk = Storage::disk('local');
        if (! self::isImage($file) || ! $disk->exists($file->path)) {
            return null;
        }
        $target = 'portfolio-apercus/'.$file->id.'-'.$width.'.jpg';
        $source = $disk->path($file->path);
        if ($disk->exists($target) && $disk->lastModified($target) >= filemtime($source)) {
            return $disk->path($target);
        }
        [$w, $h, $type] = @getimagesize($source) ?: [0, 0, 0];
        $image = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($source), IMAGETYPE_PNG => @imagecreatefrompng($source),
            IMAGETYPE_WEBP => @imagecreatefromwebp($source), IMAGETYPE_GIF => @imagecreatefromgif($source), default => false,
        };
        if (! $image || ! $w || ! $h) {
            return null;
        }
        $nw = min($width, $w);
        $nh = (int) round($h * $nw / $w);
        $canvas = imagecreatetruecolor($nw, $nh);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $nw, $nh, $w, $h);
        $disk->makeDirectory('portfolio-apercus');
        imagejpeg($canvas, $disk->path($target), 82);
        imagedestroy($image);
        imagedestroy($canvas);

        return $disk->path($target);
    }

    /** Visuel entier, jamais recadré (PDF) : data URI et dimensions ajustées à la zone donnée, en points, en conservant ses proportions. */
    public static function fittedImage(SubmissionFile $file, float $maxWidth, float $maxHeight): ?array
    {
        $path = self::thumbnail($file, 1600);
        [$w, $h] = ($path ? @getimagesize($path) : false) ?: [0, 0];
        if (! $w || ! $h) {
            return null;
        }
        $ratio = min($maxWidth / $w, $maxHeight / $h, 1.5);

        return ['src' => 'data:image/jpeg;base64,'.base64_encode((string) file_get_contents($path)), 'width' => round($w * $ratio, 1), 'height' => round($h * $ratio, 1)];
    }
}
