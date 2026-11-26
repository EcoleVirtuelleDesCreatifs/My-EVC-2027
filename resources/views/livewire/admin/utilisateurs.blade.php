<div>
  <ul class="nav nav-tabs mb-3">
    <li class="nav-item"><button class="nav-link {{ $onglet === 'equipe' ? 'active' : '' }}" wire:click="$set('onglet','equipe')">Équipe et formateurs</button></li>
    <li class="nav-item"><button class="nav-link {{ $onglet === 'roles' ? 'active' : '' }}" wire:click="$set('onglet','roles')">Rôles et droits</button></li>
  </ul>
  @if ($onglet === 'equipe')
    <button class="btn btn-primary d-sm-none mb-3" wire:click="creer"><i class="fa-solid fa-user-plus"></i>Nouvel utilisateur</button>
    <div class="panel tbl"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Utilisateur</th><th>Rôle</th><th>Dernière connexion</th><th>Statut</th><th></th></tr></thead><tbody>
      @foreach ($team as $m)
        <tr><td><div class="d-flex align-items-center gap-2"><span class="ini">{{ $m->initials() }}</span><div><b>{{ $m->name }}</b><div class="small muted">{{ $m->email }}</div></div></div></td>
          <td class="small">{{ $m->getRoleNames()->first() === 'formateur' ? 'Formateur · '.$m->trainerProfiles->map(fn ($t) => $t->formation->code)->implode(', ') : $m->getRoleNames()->first() }}</td>
          <td class="small">{{ $m->last_login_at?->diffForHumans() ?? 'Jamais' }}</td>
          <td><x-pill :tone="$m->status === 'suspendu' ? 'bad' : 'ok'">{{ $m->status === 'suspendu' ? 'Suspendu' : 'Actif' }}</x-pill></td>
          <td class="table-actions">@if ($m->id !== auth()->id())<button class="btn btn-ghost btn-sm" wire:click="basculer({{ $m->id }})" @if ($m->status !== 'suspendu') wire:confirm="Suspendre le compte de {{ $m->name }} ?" @endif>{{ $m->status === 'suspendu' ? 'Réactiver' : 'Suspendre' }}</button>
            <button class="btn btn-quiet btn-sm btn-icon text-danger" wire:click="supprimer({{ $m->id }})" wire:confirm="Supprimer définitivement le compte de {{ $m->name }} ? Il ne pourra plus se connecter. L’historique de ses actions (versements, corrections, journaux) est conservé." aria-label="Supprimer {{ $m->name }}" title="Supprimer"><i class="fa-solid fa-trash-can"></i></button>@endif</td></tr>
      @endforeach
    </tbody></table></div></div>
  @else
    <x-panel title="Droits d'accès par rôle" sub="Le Super Administrateur a toujours tous les droits." :p0="true">
      <div class="table-responsive"><table class="table matrix align-middle mb-0"><thead><tr><th>Module</th>@foreach (\App\Models\User::ADMIN_ROLES as $r)<th class="small" style="min-width:96px">{{ $r }}</th>@endforeach</tr></thead><tbody>
        @foreach ($modules as $k => $l)
          <tr><td class="fw-sb small">{{ $l }}</td>@foreach (\App\Models\User::ADMIN_ROLES as $r)<td><input type="checkbox" class="form-check-input" wire:model="matrice.{{ $r }}.{{ $k }}" @disabled($r === 'Super Administrateur') aria-label="{{ $r }} : {{ $l }}"></td>@endforeach</tr>
        @endforeach
      </tbody></table></div>
    </x-panel>
    <button class="btn btn-primary mt-3" wire:click="enregistrerRoles"><i class="fa-solid fa-floppy-disk"></i>Enregistrer les droits</button>
  @endif
  @if ($form)
    <x-modal title="Nouvel utilisateur" icon="fa-user-plus">
      <div class="d-flex flex-column gap-3">
        <div><label class="form-label">Nom complet</label><input class="form-control @error('u.name') is-invalid @enderror" wire:model="u.name">@error('u.name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div><label class="form-label">E-mail</label><input class="form-control @error('u.email') is-invalid @enderror" type="email" wire:model="u.email">@error('u.email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div><label class="form-label">Téléphone</label><input class="form-control" wire:model="u.phone"></div>
        <div><label class="form-label">Rôle</label><select class="form-select" wire:model.live="u.role"><option value="formateur">Formateur</option>@foreach (\App\Models\User::ADMIN_ROLES as $r)<option>{{ $r }}</option>@endforeach</select></div>
        @if (($u['role'] ?? '') === 'formateur')<div><label class="form-label">Formation enseignée</label><select class="form-select" wire:model="u.formation_id">@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->short_name }}</option>@endforeach</select></div>@endif
      </div>
      <p class="small muted mt-3 mb-0">Un e-mail est envoyé pour choisir le mot de passe.</p>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="enregistrer">Créer le compte</button></x-slot:footer>
    </x-modal>
  @endif
</div>
