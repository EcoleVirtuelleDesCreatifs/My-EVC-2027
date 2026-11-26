<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $all = News::published()->get();
        $featured = $all->firstWhere('featured', true) ?? $all->first();

        return view('site.news.index', [
            'featured' => $featured,
            'news' => $all->reject(fn ($n) => $n->is($featured)),
            'categories' => $all->pluck('category')->unique()->values(),
        ]);
    }

    public function show(\Illuminate\Http\Request $request, News $news)
    {
        abort_unless($news->published_at && $news->published_at->isPast(), 404);
        $this->countReader($request, $news);
        $others = News::published()->where('id', '!=', $news->id)->take(4)->get();

        return view('site.news.show', compact('news', 'others'));
    }

    /**
     * Compte un lecteur : une fois par session et par article, hors robots et hors équipe connectée
     * (sans modifier la date de mise à jour de l'article, utilisée pour le référencement).
     */
    private function countReader(\Illuminate\Http\Request $request, News $news): void
    {
        $agent = (string) $request->userAgent();
        $key = 'news_read.'.$news->id;
        if ($agent === '' || preg_match('/bot|crawl|spider|slurp|preview|facebookexternalhit|whatsapp|telegram|curl|wget|python|headless|lighthouse/i', $agent)
            || ($request->user() && $request->user()->space() !== 'etudiant') || $request->session()->has($key)) {
            return;
        }
        $request->session()->put($key, true);
        \Illuminate\Support\Facades\DB::table('news')->where('id', $news->id)->increment('views');
    }
}
