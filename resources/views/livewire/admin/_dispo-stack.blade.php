<div class="dispo-stack" role="img" aria-label="{{ $e['libre'] }} libres, {{ $e['recu'] }} projet reçu, {{ $e['encours'] }} en cours">
  @foreach (['encours', 'recu', 'libre'] as $k)<span class="ds-{{ $k }}" style="flex:{{ $e[$k] }}"></span>@endforeach
</div>
