<?php

namespace Arete\LadiesHub\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Arete\LadiesHub\Commands\GenerateProducts;

class LadiesHubServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadPublishableAssets();

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateProducts::class
            ]);
        }

        include dirname(__DIR__) . '/Helpers/functions.php';

    }


    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        
        $this->registerConfig();
    }

    /**
     * This method will load all publishables.
     *
     * @return void
     */
    private function loadPublishableAssets()
    {
        $this->publishes([
            __DIR__ . '/../../publishable/assets' => public_path('themes/lhub/assets'),
        ], 'public');

        $this->publishes([
            __DIR__ . '/../Resources/views/shop' => resource_path('themes/lhub/views'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/components' => resource_path('/views/components'),
        ]);
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/Config/themes.php', 'themes.themes');
    }
}