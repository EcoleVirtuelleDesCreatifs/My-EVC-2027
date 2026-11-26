<?php

namespace App\Livewire\Etudiant;

use App\Support\LectureSeule;
use Livewire\Attributes\On;
use Livewire\Component;

class Alertes extends Component
{
    #[On('menu-actualiser')]
    public function actualiser(): void {}

    private function user()
    {
        $user = auth()->user();
        abort_unless($user && $user->space() === 'etudiant', 403);

        return $user;
    }

    public function ouvrir(string $id): void
    {
        $notification = $this->user()->notifications()->findOrFail($id);
        $destination = $this->destination($notification->data);
        $notification->markAsRead();
        $this->dispatch('menu-actualiser');
        $this->redirect($destination, navigate: true);
    }

    public function marquerLue(string $id): void
    {
        $this->user()->notifications()->findOrFail($id)->markAsRead();
        $this->dispatch('menu-actualiser');
    }

    private function destination(array $data): string
    {
        $path = parse_url((string) ($data['url'] ?? ''), PHP_URL_PATH);
        if (! is_string($path) || ! preg_match('#^/myevc/(?:etudiant(?:/[a-z0-9_-]+)*|messagerie|profil|notifications)/?$#iD', $path)) {
            return route('myevc.notifications', [], false);
        }
        if (LectureSeule::applies($this->user()) && preg_match('#^/myevc/etudiant/(projets|quiz|bibliotheque|outils-de-demarrage)(/|$)#', $path)) {
            return route('myevc.notifications', [], false);
        }

        return $path;
    }

    private function consigne(array $data): array
    {
        if ($this->destination($data) === route('myevc.notifications', [], false)) {
            return ['Consultez le détail de cette notification.', 'Lire la notification'];
        }

        if (LectureSeule::applies($this->user()) && ($data['title'] ?? '') === 'Rapport à compléter') {
            return ['Consultez le retour de l’équipe. Votre compte étant en lecture seule, contactez l’administration par messagerie pour demander une prolongation avant tout nouveau dépôt.', 'Consulter mon rapport'];
        }

        return match ($data['title'] ?? '') {
            'Nouveau message sur un projet', 'Nouveau commentaire' => ['L’équipe vous a répondu. Ouvrez les échanges du projet pour lire son message et répondre si nécessaire.', 'Lire la réponse'],
            'Nouveau projet' => ['Un projet vous attend. Consultez le brief, les livrables demandés et la date limite avant de commencer.', 'Découvrir mon projet'],
            'Projet à reprendre' => ['Consultez les corrections demandées, puis déposez une nouvelle version de votre travail.', 'Voir les corrections'],
            'Projet validé' => ['Votre travail a été validé. Consultez l’appréciation de l’équipe pédagogique.', 'Voir mon résultat'],
            'Paiement reçu' => ['Votre versement a été enregistré. Vérifiez votre reçu et votre nouveau reste à solder.', 'Consulter mes paiements'],
            'Certificat délivré' => ['Votre certificat est disponible dans votre espace. Vous pouvez le consulter et le télécharger.', 'Voir mon certificat'],
            'Rapport à compléter' => ['Lisez le retour de l’équipe, complétez votre rapport de fin de formation puis déposez la nouvelle version.', 'Consulter mon rapport'],
            'Rapport validé' => ['Votre rapport de fin de formation est validé. Retrouvez la décision dans votre espace.', 'Voir mon rapport'],
            default => ['Une nouvelle information vous concerne. Ouvrez-la pour consulter les détails et les éventuelles consignes.', 'Consulter'],
        };
    }

    public function render()
    {
        $query = $this->user()->unreadNotifications();
        $total = (clone $query)->count();
        $items = $query->limit(3)->get()->map(function ($notification) {
            [$instruction, $action] = $this->consigne($notification->data);

            return ['id' => $notification->id, 'title' => $notification->data['title'] ?? 'Nouvelle notification',
                'body' => $notification->data['body'] ?? '', 'date' => $notification->created_at,
                'instruction' => $instruction, 'action' => $action];
        });

        return view('livewire.etudiant.alertes', compact('items', 'total'));
    }
}
