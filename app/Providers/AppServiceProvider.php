<?php

namespace App\Providers;

use App\Http\Services\GCloudPubSubService;
use App\Oauth\CustomKeycloakProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $this->bootKeycloakSocialite();
        JsonResource::withoutWrapping();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind(GCloudPubSubService::class, function () {
            return new GCloudPubSubService();
        });
    }

    /**
     * bootKeycloakSocialite func
     *
     */
    private function bootKeycloakSocialite()
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend(
            'keycloak',
            function ($app) use ($socialite) {
                $config = $app['config']['services.keycloak'];
                return new CustomKeycloakProvider($config);
            }
        );
    }
}
