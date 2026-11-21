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

    public function show(News $news)
    {
        abort_unless($news->published_at && $news->published_at->isPast(), 404);
        $others = News::published()->where('id', '!=', $news->id)->take(4)->get();

        return view('site.news.show', compact('news', 'others'));
    }
}
