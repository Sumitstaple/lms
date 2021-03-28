<?php
namespace App\Repositories;

use Illuminate\Support\ServiceProvider;

class BackendServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'App\Repositories\SportRepositoryInterface',
            'App\Repositories\SportRepository'
        );

        $this->app->bind(
            'App\Repositories\NotificationRepositoryInterface',
            'App\Repositories\NotificationRepository'
        );

        $this->app->bind(
            'App\Repositories\LeagueRepositoryInterface',
            'App\Repositories\LeagueRepository'
        );

         $this->app->bind(
            'App\Repositories\TeamSelectionInterface',
            'App\Repositories\TeamSelection'
        );
    }
}
?>
