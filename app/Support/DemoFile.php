<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

/** Fichiers de démonstration pour la bibliothèque (remplacés par les vrais fichiers depuis l'administration). */
class DemoFile
{
    public static function make(string $title, string $format, string $dir = 'library'): string
    {
        $slug = Str::slug($title) ?: 'ressource';
        if (str_starts_with(strtoupper($format), 'PDF')) {
            $path = "$dir/$slug.pdf";
            Storage::disk('local')->put($path, self::pdf($title));

            return $path;
        }
        $path = "$dir/$slug.zip";
        $tmp = tempnam(sys_get_temp_dir(), 'evc');
        $zip = new ZipArchive;
        $zip->open($tmp, ZipArchive::OVERWRITE);
        $zip->addFromString('LISEZ-MOI.txt', "$title\n\nFichier de démonstration de la bibliothèque MY EVC ($format).\nLe fichier définitif est déposé par l'école depuis l'administration.\n");
        $zip->close();
        Storage::disk('local')->put($path, file_get_contents($tmp));
        @unlink($tmp);

        return $path;
    }

    /** PDF minimal valide d'une page. */
    public static function pdf(string $title): string
    {
        $t = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], iconv('UTF-8', 'windows-1252//TRANSLIT', $title) ?: 'Ressource');
        $stream = "BT /F1 20 Tf 60 760 Td ($t) Tj ET\nBT /F1 12 Tf 60 730 Td (Bibliotheque MY EVC - Ecole Virtuelle des Creatifs) Tj ET\nBT /F1 11 Tf 60 705 Td (Fichier de demonstration : le document definitif est depose par l'ecole.) Tj ET";
        $objs = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
            '<< /Length '.strlen($stream)." >>\nstream\n$stream\nendstream",
        ];
        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objs as $i => $o) {
            $offsets[] = strlen($pdf);
            $pdf .= ($i + 1)." 0 obj\n$o\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objs) + 1)."\n0000000000 65535 f \n";
        foreach ($offsets as $off) {
            $pdf .= sprintf("%010d 00000 n \n", $off);
        }

        return $pdf."trailer\n<< /Size ".(count($objs) + 1)." /Root 1 0 R >>\nstartxref\n$xref\n%%EOF\n";
    }
}
