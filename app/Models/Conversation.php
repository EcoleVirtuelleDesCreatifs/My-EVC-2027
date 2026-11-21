<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['last_message_at' => 'datetime'];
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('last_read_id', 'last_read_at');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->oldest();
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /** Conversations d'un utilisateur, la plus récente d'abord. */
    public static function forUser(User $u)
    {
        return static::whereHas('participants', fn ($q) => $q->where('users.id', $u->id))->orderByDesc('last_message_at');
    }

    public function isUnreadFor(User $u): bool
    {
        $read = $this->participants->firstWhere('id', $u->id)?->pivot->last_read_id;
        $last = $this->lastMessage;

        return $last && $last->user_id !== $u->id && (! $read || $read < $last->id);
    }

    /** Nombre de conversations non lues (menus). */
    public static function unreadCount(User $u): int
    {
        return static::forUser($u)->whereNotNull('last_message_at')
            ->whereHas('participants', fn ($q) => $q->where('users.id', $u->id)->where(fn ($w) => $w->whereNull('conversation_user.last_read_id')
                ->orWhereRaw('conversation_user.last_read_id < (select max(m.id) from messages m where m.conversation_id = conversations.id)')))
            ->whereHas('lastMessage', fn ($q) => $q->where('user_id', '!=', $u->id))->count();
    }
}
