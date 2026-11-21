<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\News;

class PageController extends Controller
{
    /** Pages statiques du site (resources/views/site/pages). */
    public const PAGES = [
        'ecole', 'formations', 'formation-design', 'formation-community-management', 'formation-bureautique', 'pedagogie',
        'studio-creative', 'admissions', 'preinscription', 'jury-certification', 'realisations', 'phototheque', 'entreprises',
        'contact', 'mentions-legales', 'confidentialite', 'conditions-utilisation', 'conditions-generales-vente', 'reglement-interieur',
    ];

    public function home()
    {
        return view('site.pages.accueil');
    }

    public function show(string $page)
    {
        return view('site.pages.'.$page);
    }

    public function sitemap()
    {
        $urls = collect([url('/')])->merge(collect(self::PAGES)->map(fn ($p) => url($p)))->push(route('site.actualites'));
        $news = News::published()->get();

        return response()->view('site.sitemap', compact('urls', 'news'))->header('Content-Type', 'application/xml');
    }
}
