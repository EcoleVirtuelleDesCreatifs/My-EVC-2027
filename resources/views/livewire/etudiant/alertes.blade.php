<div wire:poll.15s="actualiser" class="student-alerts" aria-live="polite" aria-relevant="additions removals">
  @if ($total)
    <section aria-label="Nouveautés et prochaines actions" class="mb-4">
      <div class="sa-heading">
        <h2><i class="fa-regular fa-bell" aria-hidden="true"></i> Du nouveau pour vous <span>{{ $total }}</span></h2>
        <a href="{{ route('myevc.notifications') }}" wire:navigate>Toutes mes notifications</a>
      </div>
      <div class="sa-grid">
        @foreach ($items as $item)
          <article class="sa-bubble" wire:key="alerte-{{ $item['id'] }}">
            <div class="sa-top"><span class="sa-tag">Non lue</span><time datetime="{{ $item['date']->toAtomString() }}">{{ $item['date']->diffForHumans() }}</time></div>
            <h3>{{ $item['title'] }}</h3>
            <p class="sa-message">{{ \Illuminate\Support\Str::limit($item['body'], 220) }}</p>
            <p class="sa-instruction"><i class="fa-solid fa-arrow-turn-up" aria-hidden="true"></i><span>{{ $item['instruction'] }}</span></p>
            <div class="sa-actions">
              <button type="button" class="btn btn-primary btn-sm" wire:click="ouvrir('{{ $item['id'] }}')" wire:loading.attr="disabled">{{ $item['action'] }}<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
              <button type="button" class="btn btn-quiet btn-sm" wire:click="marquerLue('{{ $item['id'] }}')" wire:loading.attr="disabled" aria-label="Marquer comme lue : {{ $item['title'] }}">Marquer comme lue</button>
            </div>
          </article>
        @endforeach
      </div>
      @if ($total > 3)<p class="small muted mt-3 mb-0">{{ $total - 3 }} autre(s) notification(s) à retrouver dans <a href="{{ route('myevc.notifications') }}" wire:navigate>toutes mes notifications</a>.</p>@endif
    </section>
  @endif
</div>
