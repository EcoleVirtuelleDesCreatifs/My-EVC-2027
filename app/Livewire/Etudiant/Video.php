<?php

namespace App\Livewire\Etudiant;

use App\Livewire\Page;
use App\Models\Video as VideoModel;
use App\Models\VideoProgress;

class Video extends Page
{
    public VideoModel $video;

    public function mount(VideoModel $video): void
    {
        abort_unless($video->published && $this->student()->formations->contains('id', $video->formation_id), 403);
        $p = VideoProgress::firstOrCreate(['video_id' => $video->id, 'student_id' => $this->student()->id]);
        if ($p->wasRecentlyCreated) {
            $video->increment('views_count');
        }
    }

    /** Appelé par le lecteur toutes les 10 secondes et à la fin de la vidéo. */
    public function progression(int $seconds, int $duration): void
    {
        if ($duration <= 0) {
            return;
        }
        $p = VideoProgress::firstOrCreate(['video_id' => $this->video->id, 'student_id' => $this->student()->id]);
        $pct = (int) min(100, round($seconds / $duration * 100));
        $p->fill(['seconds' => $seconds, 'percent' => max($p->percent, $pct)]);
        if ($pct >= 90 && ! $p->completed_at) {
            $p->completed_at = now();
        }
        $p->save();
        if ($duration && ! $this->video->duration_min) {
            $this->video->update(['duration_min' => max(1, (int) round($duration / 60))]);
        }
        $this->skipRender();
    }

    public function marquerVue(): void
    {
        VideoProgress::updateOrCreate(['video_id' => $this->video->id, 'student_id' => $this->student()->id], ['percent' => 100, 'completed_at' => now()]);
        $this->toast('Vidéo marquée comme vue.');
    }

    public function render()
    {
        $v = $this->video->load('formation', 'seance');
        $p = VideoProgress::where('video_id', $v->id)->where('student_id', $this->student()->id)->first();
        $suite = VideoModel::visibleFor([$v->formation_id])->where('id', '!=', $v->id)->where('module', $v->module)->latest()->take(6)->get();
        $prog = $this->student()->videoProgress()->whereIn('video_id', $suite->pluck('id'))->get()->keyBy('video_id');

        return $this->page('livewire.etudiant.video', compact('v', 'p', 'suite', 'prog'), $v->title,
            'Espace étudiant <span class="sep">›</span> <a href="'.route('etudiant.webtv').'" wire:navigate>Web TV</a>');
    }
}
