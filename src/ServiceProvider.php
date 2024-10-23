<?php

namespace Thoughtco\StatamicInstagram;

use Instagram\Api;
use Statamic\Providers\AddonServiceProvider;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class ServiceProvider extends AddonServiceProvider
{
    public function bootAddon()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/statamic-instagram.php', 'statamic-instagram');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/statamic-instagram.php' => config_path('statamic-instagram.php'),
            ], 'statamic-instagram-config');
        }

        $this->app->bind(Api::class, function ($app) {
            $cachePool = new FilesystemAdapter('Instagram', 0, storage_path('app/instagram-cache'));

            $api = new Api($cachePool);
            $api->login(config('statamic-instagram.credentials.username'), config('statamic-instagram.credentials.password'));

            return $api;
        });
    }
}
