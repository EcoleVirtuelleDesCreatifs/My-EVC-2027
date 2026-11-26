{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($urls as $u)
  <url><loc>{{ $u['loc'] }}</loc><lastmod>{{ $u['lastmod'] }}</lastmod></url>
@endforeach
@foreach ($news as $n)
  <url><loc>{{ \App\Support\SiteSeo::absolute(route('site.actualite', $n, false)) }}</loc><lastmod>{{ ($n->updated_at ?? $n->published_at)->max($n->published_at)->toAtomString() }}</lastmod></url>
@endforeach
</urlset>
