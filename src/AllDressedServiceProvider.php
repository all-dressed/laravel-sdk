<?php

namespace AllDressed\Laravel;

use AllDressed\Laravel\Exceptions\MissingApiKeyException;
use Illuminate\Support\ServiceProvider;

class AllDressedServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('all-dressed.php'),
            ], 'config');
        }

        $this->app->bind(Client::class, static function () {
            $key = config('all-dressed.api.key');

            throw_unless($key, MissingApiKeyException::class);

            return new Client(
                config('all-dressed.api.key'),
                config('all-dressed.account')
            );
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'all-dressed');
    }
}
