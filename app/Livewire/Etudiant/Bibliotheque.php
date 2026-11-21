<?php

namespace App\Livewire\Etudiant;

use App\Livewire\Page;
use App\Models\Resource;
use Livewire\Attributes\Url;

/** Bibliothèque numérique : livres rangés par rayon, recherche, fiche du livre, « Mon étagère ». */
class Bibliotheque extends Page
{
    use ChoixFormation;

    #[Url(except: '')]
    public string $rayon = '';

    #[Url(except: '')]
    public string $q = '';

    #[Url(except: 'recent')]
    public string $tri = 'recent';

    public ?int $ficheId = null;

    public function rayon(string $k): void
    {
        $this->rayon = $k;
        $this->q = '';
    }

    public function voir(int $id): void
    {
        $this->ficheId = $id;
    }

    public function closeModal(): void
    {
        $this->ficheId = null;
    }

    public function render()
    {
        $s = $this->student();
        $all = Resource::visibleFor($this->formationIds())->with('formation')->get();
        $got = $s->downloads()->pluck('resource_id')->all();
        $sort = fn ($c) => match ($this->tri) {
            'populaire' => $c->sortByDesc('downloads_count'), 'az' => $c->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE), default => $c->sortByDesc('created_at'),
        };
        $q = mb_strtolower(trim($this->q));
        $view = match (true) {
            $q !== '' => ['mode' => 'grid', 'title' => 'Résultats pour « '.$this->q.' »', 'items' => $sort($all->filter(fn ($r) => str_contains(mb_strtolower($r->title.' '.$r->description.' '.$r->format.' '.$r->categoryLabel()), $q)))],
            $this->rayon === 'mes' => ['mode' => 'grid', 'title' => 'Mon étagère', 'plate' => '<i class="fa-solid fa-bookmark"></i>', 'items' => $sort($all->whereIn('id', $got))],
            $this->rayon !== '' => ['mode' => 'grid', 'title' => Resource::CATEGORIES[$this->rayon][0] ?? '', 'plate' => 'Rayon '.(array_search($this->rayon, array_keys(Resource::CATEGORIES)) + 1), 'items' => $sort($all->where('category', $this->rayon))],
            default => ['mode' => 'shelves'],
        };
        $shelves = [];
        if ($view['mode'] === 'shelves') {
            $shelves[] = ['<i class="fa-solid fa-star"></i>', 'Indispensables de votre formation', $all->where('essential', true), null];
            $shelves[] = ['<i class="fa-solid fa-wand-magic-sparkles"></i>', 'Nouveautés', $sort($all->filter->isNew()), null];
            $i = 0;
            foreach (Resource::CATEGORIES as $k => $c) {
                $shelves[] = ['Rayon '.++$i, $c[0], $sort($all->where('category', $k)), $k];
            }
        }
        $counts = $all->countBy('category');
        $fiche = $this->ficheId ? $all->firstWhere('id', $this->ficheId) : null;
        $vitrine = $sort($all->where('category', 'ebooks'))->take(4);
        $lastUpdate = $all->max('created_at');

        return $this->page('livewire.etudiant.bibliotheque', compact('s', 'all', 'got', 'view', 'shelves', 'counts', 'fiche', 'vitrine', 'lastUpdate'),
            'Bibliothèque', 'Espace étudiant <span class="sep">›</span> Bibliothèque');
    }
}
