<?php

namespace App\Support;

/**
 * Images du site public : variantes WebP (srcset) générées par `php artisan site:images`,
 * et réécriture des balises <img> pour déclarer dimensions, WebP et versions mobiles.
 */
class ResponsiveImages
{
    public const SOURCE = 'assets/site/img';

    public const OUTPUT = 'assets/site/img/opt';

    public const WIDTHS = [480, 960, 1440];

    private static ?array $manifest = null;

    public static function manifestPath(): string
    {
        return public_path(self::OUTPUT.'/manifest.json');
    }

    public static function manifest(): array
    {
        if (self::$manifest === null) {
            $file = self::manifestPath();
            self::$manifest = is_file($file) ? (json_decode((string) file_get_contents($file), true) ?: []) : [];
        }

        return self::$manifest;
    }

    public static function flush(): void
    {
        self::$manifest = null;
    }

    /** Entrée du manifeste pour un chemin public (« /assets/site/img/x.jpg »), ou null. */
    public static function find(string $src): ?array
    {
        $path = '/'.ltrim((string) parse_url(html_entity_decode($src), PHP_URL_PATH), '/');

        return self::manifest()[$path] ?? null;
    }

    public static function rewrite(string $html): string
    {
        if (! self::manifest()) {
            return $html;
        }

        return preg_replace_callback('/<img\b[^>]*>/i', function (array $m) {
            $tag = $m[0];
            if (! preg_match('/\ssrc="([^"]+)"/i', $tag, $src) || ! ($image = self::find($src[1]))) {
                return $tag;
            }
            $add = [];
            if (! empty($image['variants'])) {
                $variants = $image['variants'];
                $tag = str_replace($src[0], ' src="'.end($variants).'"', $tag);
                if (! preg_match('/\ssrcset=/i', $tag)) {
                    $add[] = 'srcset="'.collect($variants)->map(fn ($url, $w) => $url.' '.$w.'w')->implode(', ').'"';
                    if (! preg_match('/\ssizes=/i', $tag)) {
                        $add[] = 'sizes="'.(str_contains($src[1], '/logo') ? '240px' : '100vw').'"';
                    }
                }
            }
            if (! preg_match('/\swidth=/i', $tag) && ! preg_match('/\sheight=/i', $tag)) {
                $add[] = 'width="'.$image['width'].'" height="'.$image['height'].'"';
            }

            return $add ? preg_replace('/\s*\/?>$/', ' '.implode(' ', $add).'>', $tag) : $tag;
        }, $html) ?? $html;
    }

    /**
     * Génère les variantes WebP de chaque image JPEG/PNG et enregistre les dimensions (SVG compris).
     *
     * @return array{images:int, generated:int, before:int, after:int}
     */
    public static function build(int $quality = 78, bool $force = false): array
    {
        $sourceDir = public_path(self::SOURCE);
        $outputDir = public_path(self::OUTPUT);
        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0775, true);
        }
        $manifest = [];
        $stats = ['images' => 0, 'generated' => 0, 'before' => 0, 'after' => 0];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($sourceDir, \FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            $path = $file->getPathname();
            if (str_starts_with($path, $outputDir) || ! preg_match('/\.(jpe?g|png|svg)$/i', $path)) {
                continue;
            }
            $public = '/'.self::SOURCE.'/'.str_replace(DIRECTORY_SEPARATOR, '/', substr($path, strlen($sourceDir) + 1));
            if (strtolower($file->getExtension()) === 'svg') {
                if ($size = self::svgSize($path)) {
                    $manifest[$public] = $size;
                }

                continue;
            }
            [$width, $height] = getimagesize($path) ?: [0, 0];
            if (! $width || ! $height) {
                continue;
            }
            if (in_array(self::orientation($path), [6, 8], true)) {
                [$width, $height] = [$height, $width];
            }
            $stats['images']++;
            $stats['before'] += filesize($path);
            $base = preg_replace('/\.[^.]+$/', '', str_replace('/', '--', substr($public, strlen('/'.self::SOURCE.'/'))));
            $widths = array_values(array_unique(array_merge(array_filter(self::WIDTHS, fn ($w) => $w < $width * 0.9), [min($width, max(self::WIDTHS))])));
            sort($widths);
            $variants = [];
            $image = null;
            foreach ($widths as $w) {
                $name = $base.'-'.$w.'.webp';
                $target = $outputDir.'/'.$name;
                if ($force || ! is_file($target) || filemtime($target) < filemtime($path)) {
                    $image ??= self::load($path);
                    $scaled = $w === $width ? $image : imagescale($image, $w, (int) round($height * $w / $width), IMG_BICUBIC);
                    imagepalettetotruecolor($scaled);
                    imagealphablending($scaled, false);
                    imagesavealpha($scaled, true);
                    imagewebp($scaled, $target, $quality);
                    if ($scaled !== $image) {
                        imagedestroy($scaled);
                    }
                    $stats['generated']++;
                }
                $variants[$w] = '/'.self::OUTPUT.'/'.$name.'?v='.filemtime($target);
            }
            $stats['after'] += filesize($outputDir.'/'.$base.'-'.end($widths).'.webp');
            if ($image) {
                imagedestroy($image);
            }
            $manifest[$public] = ['width' => $width, 'height' => $height, 'variants' => $variants];
        }
        ksort($manifest);
        file_put_contents(self::manifestPath(), json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");
        self::flush();

        return $stats;
    }

    private static function load(string $path): \GdImage
    {
        $image = preg_match('/\.png$/i', $path) ? imagecreatefrompng($path) : imagecreatefromjpeg($path);

        return match (self::orientation($path)) {
            3 => imagerotate($image, 180, 0), 6 => imagerotate($image, -90, 0), 8 => imagerotate($image, 90, 0), default => $image,
        };
    }

    private static function orientation(string $path): int
    {
        return preg_match('/\.jpe?g$/i', $path) && function_exists('exif_read_data') && ($exif = @exif_read_data($path)) ? (int) ($exif['Orientation'] ?? 1) : 1;
    }

    private static function svgSize(string $path): ?array
    {
        $svg = (string) file_get_contents($path, false, null, 0, 4096);
        if (preg_match('/<svg\b[^>]*>/i', $svg, $root) && preg_match('/\swidth="([\d.]+)(?:px)?"/i', $root[0], $w) && preg_match('/\sheight="([\d.]+)(?:px)?"/i', $root[0], $h)) {
            return ['width' => (int) round($w[1]), 'height' => (int) round($h[1])];
        }
        if (preg_match('/\sviewBox="[\d.\-]+[\s,]+[\d.\-]+[\s,]+([\d.]+)[\s,]+([\d.]+)"/i', $svg, $m)) {
            return ['width' => (int) round($m[1]), 'height' => (int) round($m[2])];
        }

        return null;
    }
}
