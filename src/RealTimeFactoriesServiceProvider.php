<?php

namespace Shift;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class RealTimeFactoriesServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot(): void
    {
        if (! defined('STUBS_PATH')) {
            define('STUBS_PATH', dirname(__DIR__).'/stubs');
        }

        if (! defined('CUSTOM_STUBS_PATH')) {
            define('CUSTOM_STUBS_PATH', base_path('stubs/real-time-factories'));
        }

        $this->publishes([
            __DIR__.'/../config/real-time-factories.php' => config_path('real-time-factories.php'),
        ], 'real-time-factories-config');

        $this->publishes([
            dirname(__DIR__).'/stubs' => CUSTOM_STUBS_PATH,
        ], 'real-time-factories-stubs');
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/real-time-factories.php',
            'real-time-factories'
        );
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}
