<?php

namespace App\Livewire\Etudiant;

use App\Livewire\Page;
use App\Models\Video;
use Livewire\Attributes\Url;

/** Web TV : replays des séances, tutoriels d'installation, masterclass. */
class WebTv extends Page
{
    use ChoixFormation;

    #[Url(except: '')]
    public string $module = '';

    #[Url(except: '')]
    public string $q = '';

    public function render()
    {
        $s = $this->student();
        $all = Video::visibleFor($this->formationIds())->with('formation', 'seance')->latest()->get();
        $prog = $s->videoProgress()->whereIn('video_id', $all->pluck('id'))->get()->keyBy('video_id');
        $q = mb_strtolower(trim($this->q));
        $videos = $all->when($this->module, fn ($c) => $c->where('module', $this->module))
            ->when($q !== '', fn ($c) => $c->filter(fn ($v) => str_contains(mb_strtolower($v->title.' '.$v->description), $q)));
        $resume = $all->filter(fn ($v) => ($p = $prog->get($v->id)) && ! $p->completed_at && $p->percent > 0)->sortByDesc(fn ($v) => $prog[$v->id]->updated_at)->first();
        $modules = $all->pluck('module')->unique()->sort()->values();
        $vues = $prog->whereNotNull('completed_at')->count();

        return $this->page('livewire.etudiant.webtv', compact('s', 'videos', 'prog', 'resume', 'modules', 'all', 'vues'), 'Vidéos des cours', 'Espace étudiant <span class="sep">›</span> Web TV');
    }
}
