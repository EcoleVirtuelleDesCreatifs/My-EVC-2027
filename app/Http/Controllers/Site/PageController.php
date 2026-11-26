<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Formation;
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
        return view('site.pages.accueil', ['latestNews' => News::published()->latest('published_at')->limit(3)->get()]);
    }

    public function show(string $page)
    {
        return view('site.pages.'.$page, [
            'registrationFormations' => $page === 'preinscription' ? Formation::active()->orderBy('position')->get() : collect(),
            'uploadLimits' => $page === 'preinscription' ? FormController::uploadLimits() : null,
            'saleFormations' => $page === 'conditions-generales-vente' ? Formation::active()->orderBy('position')->get() : collect(),
            'publicCourse' => isset(\App\Support\SiteSeo::COURSES[$page]) ? Formation::where('code', \App\Support\SiteSeo::COURSES[$page])->firstOrFail() : null,
        ]);
    }

    public function sitemap()
    {
        $news = News::published()->get();
        $sharedModified = max(filemtime(resource_path('views/layouts/site.blade.php')), filemtime(app_path('Support/SiteSeo.php')));
        $formationModified = Formation::max('updated_at');
        $newsModified = $news->map(fn ($n) => ($n->updated_at ?? $n->published_at)->max($n->published_at))->max();
        $urls = collect(['' => 'accueil'])->merge(array_combine(self::PAGES, self::PAGES))->put('actualites', null)->map(function ($view, $path) use ($sharedModified, $formationModified, $newsModified) {
            $modified = \Illuminate\Support\Carbon::createFromTimestampUTC(max($sharedModified, $view ? filemtime(resource_path('views/site/pages/'.$view.'.blade.php')) : filemtime(resource_path('views/site/news/index.blade.php'))));
            if ($formationModified && ($path === '' || in_array($path, ['formations', 'admissions', 'preinscription', 'conditions-generales-vente']) || isset(\App\Support\SiteSeo::COURSES[$path]))) {
                $modified = $modified->max(\Illuminate\Support\Carbon::parse($formationModified));
            }
            if ($newsModified && in_array($path, ['', 'actualites'])) {
                $modified = $modified->max($newsModified);
            }

            return ['loc' => \App\Support\SiteSeo::absolute($path), 'lastmod' => $modified->toAtomString()];
        });

        return response()->view('site.sitemap', compact('urls', 'news'))->header('Content-Type', 'application/xml');
    }
}
