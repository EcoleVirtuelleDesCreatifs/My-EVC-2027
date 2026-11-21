<div>
  @include('livewire.etudiant._fsel')
  <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
    <div class="seg seg-soft" role="tablist">
      <button type="button" class="{{ $vue === 'avenir' ? 'on' : '' }}" wire:click="$set('vue','avenir')">À venir</button>
      <button type="button" class="{{ $vue === 'passees' ? 'on' : '' }}" wire:click="$set('vue','passees')">Passées</button>
    </div>
    <span class="small muted ms-auto"><i class="fa-solid fa-user-check me-1"></i>Assiduité : {{ $s->attendanceRate() }} %</span>
  </div>

  @forelse ($days as $day => $list)
    <h3 class="small-caps mt-4 mb-2">{{ ucfirst(\Illuminate\Support\Carbon::parse($day)->translatedFormat('l j F')) }} @if (\Illuminate\Support\Carbon::parse($day)->isToday())<x-pill tone="acc" nodot>Aujourd'hui</x-pill>@endif</h3>
    <div class="panel"><div class="panel-b p0">
      @foreach ($list as $x)
        @php($pr = $presences->get($x->id))
        <div class="row-item c-{{ $x->formation->code }}">
          <div class="dbox"><span>{{ $x->starts_at->format('H\hi') }}</span><b><x-fchip :code="$x->formation->code" /></b></div>
          <div class="ri-main">
            <div class="ri-t">{{ $x->title }}</div>
            <div class="ri-s">{{ $x->module }} · {{ $x->hours() }} · {{ $x->trainer?->name }} @if ($x->room)· {{ $x->room }}@endif</div>
          </div>
          <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
            <x-pill :tone="$x->mode === 'virtuel' ? 'info' : 'acc'" nodot>{{ $x->modeLabel() }}</x-pill>
            @if ($pr)
              <x-pill :tone="\App\Models\Presence::STATUSES[$pr->status][1]">{{ \App\Models\Presence::STATUSES[$pr->status][0] }}</x-pill>
              @if ($x->checkInOpen() && $x->meet_url)<a class="btn btn-ghost btn-sm" href="{{ $x->meet_url }}" target="_blank" rel="noopener"><i class="fa-solid fa-video"></i>Rejoindre</a>@endif
            @elseif ($x->checkInOpen())
              <button class="btn btn-primary btn-sm" wire:click="pointer({{ $x->id }})" wire:loading.attr="disabled"><i class="fa-solid {{ $x->mode === 'virtuel' ? 'fa-video' : 'fa-check' }}"></i>{{ $x->mode === 'virtuel' ? 'Rejoindre le cours' : 'Je suis présent' }}</button>
            @elseif ($x->ends_at->isPast())
              <x-pill tone="bad">Absent</x-pill>
            @endif
          </div>
        </div>
      @endforeach
    </div></div>
  @empty
    <x-empty title="{{ $vue === 'avenir' ? 'Aucun cours à venir' : 'Aucun cours passé' }}" text="Le planning est publié par votre formateur." />
  @endforelse
</div>
