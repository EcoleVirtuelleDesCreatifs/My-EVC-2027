<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /* Formation terminée : les actions de dépôt, de réponse et de modification sont refusées (messagerie autorisée) */
        \Livewire\Livewire::listen('call', function ($component, $method, $params, $context, $returnEarly) {
            if (\App\Support\LectureSeule::blocks($component, $method) && \App\Support\LectureSeule::applies(auth()->user())) {
                $component->dispatch('toast', message: \App\Support\LectureSeule::MESSAGE, kind: 'warn');
                $returnEarly();
            }
        });

        if ($this->app->isProduction() && str_starts_with((string) config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
