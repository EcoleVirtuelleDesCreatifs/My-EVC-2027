<?php

namespace App\Notifications;

use App\Models\News;
use App\Models\NewsletterSubscriber;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/* E-mail envoyé aux abonnés de la newsletter lorsqu'une actualité est publiée. */
class ActualiteNewsletter extends Notification
{
    public function __construct(public News $news, public NewsletterSubscriber $abonne) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $n = $this->news;

        return (new MailMessage)->subject($n->title.' · EVC')
            ->view(['html' => 'mail-actualite', 'text' => 'mail-actualite-texte'], [
                'titre' => $n->title,
                'categorie' => $n->label ?: $n->category,
                'chapeau' => $n->excerpt,
                'couverture' => $n->cover ? url($n->cover) : null,
                'articleUrl' => route('site.actualite', $n),
                'desinscriptionUrl' => $this->abonne->desinscriptionUrl(),
            ]);
    }
}
