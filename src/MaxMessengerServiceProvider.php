<?php

namespace MaxMessenger;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class MaxMessengerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/max-messenger.php', 'max-messenger'
        );

        $this->app->singleton(MaxMessenger::class, function ($app) {
            return new MaxMessenger([
                'bot_token' => Config::get('max-messenger.bot_token'),
                'base_url' => Config::get('max-messenger.base_url'),
                'timeout' => Config::get('max-messenger.timeout'),
                'verify' => Config::get('max-messenger.verify'),
            ]);
        });

        $this->app->alias(MaxMessenger::class, 'max-messenger');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/max-messenger.php' => config_path('max-messenger.php'),
            ], 'max-messenger-config');
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [MaxMessenger::class, 'max-messenger'];
    }
}
