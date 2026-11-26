<?php

namespace App\Livewire\Shared;

use App\Livewire\Page;
use App\Models\Assignment;
use Livewire\WithPagination;

class EchangesProjets extends Page
{
    use WithPagination;

    public function render()
    {
        $user = auth()->user();
        $formations = $user->taughtFormationIds();
        abort_unless($user->can('admin.projets') || $formations, 403);
        $items = Assignment::query()
            ->when(! $user->can('admin.projets'), fn ($q) => $q->whereHas('project', fn ($p) => $p->whereIn('formation_id', $formations)))
            ->whereHas('comments', fn ($q) => $q->where('kind', 'commentaire'))
            ->with('project.formation', 'student.user')
            ->withMax(['comments as last_message_at' => fn ($q) => $q->where('kind', 'commentaire')], 'created_at')
            ->withCount(['comments as messages_count' => fn ($q) => $q->where('kind', 'commentaire')])
            ->orderByDesc('last_message_at')->orderByDesc('id')->paginate(20);

        return $this->page('livewire.shared.echanges-projets', compact('items'), 'Échanges projets', 'Suivi pédagogique');
    }
}
