<?php

namespace App\Http\Middleware;

use App\Support\ResponsiveImages;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Site public : sert les images en WebP avec srcset et dimensions déclarées (voir `php artisan evc:images`). */
class OptimiseSiteImages
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        if ($request->isMethod('GET') && ! $request->is('myevc', 'myevc/*', 'livewire/*') && $response->getStatusCode() === 200
            && str_contains((string) $response->headers->get('Content-Type'), 'text/html') && is_string($content = $response->getContent())) {
            $response->setContent(ResponsiveImages::rewrite($content));
        }

        return $response;
    }
}
