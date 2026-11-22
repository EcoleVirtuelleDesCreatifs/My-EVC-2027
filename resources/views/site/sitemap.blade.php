{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($urls as $u)
  <url><loc>{{ $u }}</loc><changefreq>monthly</changefreq></url>
@endforeach
@foreach ($news as $n)
  <url><loc>{{ route('site.actualite', $n) }}</loc><lastmod>{{ $n->updated_at->toAtomString() }}</lastmod></url>
@endforeach
</urlset>
