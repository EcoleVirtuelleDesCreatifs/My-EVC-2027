<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Student;
use App\Models\Trainer;
use App\Models\User;
use App\Notifications\Avis;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MessagingService
{
    /**
     * Destinataires possibles :
     *   étudiant  → ses formateurs et l'équipe de l'école (relation étudiants, finances, pédagogie…) ;
     *   formateur → ses étudiants, les autres formateurs et l'équipe ;
     *   équipe    → tout le monde.
     */
    public function recipients(User $u): Collection
    {
        $staff = User::where('status', 'actif')->whereHas('roles', fn ($q) => $q->whereNotIn('name', ['etudiant', 'formateur']))->get();
        $list = match ($u->space()) {
            'etudiant' => User::whereIn('id', Trainer::whereIn('formation_id', $u->student->formations->pluck('id'))->pluck('user_id'))->get()->merge($staff),
            'formateur' => User::whereIn('id', Student::whereHas('enrollments', fn ($q) => $q->whereIn('formation_id', $u->taughtFormationIds()))->where('status', 'actif')->pluck('user_id'))->get()
                ->merge(User::role('formateur')->get())->merge($staff),
            default => User::where('status', 'actif')->get(),
        };

        return $list->unique('id')->reject(fn ($x) => $x->id === $u->id)->sortBy('name')->values();
    }

    public function start(User $from, array $toIds, string $subject, string $body, ?UploadedFile $file = null): Conversation
    {
        $allowed = $this->recipients($from)->pluck('id');
        $to = collect($toIds)->map(fn ($id) => (int) $id)->filter(fn ($id) => $allowed->contains($id))->unique();
        abort_if($to->isEmpty(), 422, 'Destinataire non autorisé.');

        $c = DB::transaction(function () use ($from, $to, $subject) {
            $c = Conversation::create(['subject' => $subject, 'created_by' => $from->id]);
            $c->participants()->attach($to->push($from->id)->unique()->all());

            return $c;
        });
        $this->send($c, $from, $body, $file);

        return $c;
    }

    public function send(Conversation $c, User $from, string $body, ?UploadedFile $file = null): Message
    {
        abort_unless($c->participants()->where('users.id', $from->id)->exists(), 403);
        $m = $c->messages()->create(['user_id' => $from->id, 'body' => $body,
            'file_path' => $file?->store('messagerie/'.$c->id, 'local'), 'file_name' => $file?->getClientOriginalName()]);
        $previous = $c->messages()->where('id', '<', $m->id)->max('id');
        $c->update(['last_message_at' => $m->created_at]);
        $c->participants()->updateExistingPivot($from->id, ['last_read_id' => $m->id, 'last_read_at' => $m->created_at]);

        /* Une notification seulement quand la conversation passe de « lue » à « non lue » (pas une par message) */
        foreach ($c->participants()->where('users.id', '!=', $from->id)->get() as $p) {
            $read = $p->pivot->last_read_id;
            if (! $previous || ($read && $read >= $previous)) {
                $p->notify(new Avis('Nouveau message', $from->name.' : « '.str($body)->limit(80).' »', route('myevc.messagerie', ['c' => $c->id]), 'fa-message', 'lav'));
            }
        }

        return $m;
    }

    public function markRead(Conversation $c, User $u): void
    {
        $last = $c->messages()->max('id');
        $read = $c->participants()->where('users.id', $u->id)->first()?->pivot->last_read_id;
        if ($last && $read !== $last) {
            $c->participants()->updateExistingPivot($u->id, ['last_read_id' => $last, 'last_read_at' => now()]);
        }
        $u->unreadNotifications()->where('type', Avis::class)
            ->where('data->url', route('myevc.messagerie', ['c' => $c->id]))->update(['read_at' => now()]);
    }
}
