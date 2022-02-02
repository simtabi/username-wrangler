<?php

namespace Simtabi\UsernameWrangler\Providers;

use Illuminate\Support\ServiceProvider;
use Simtabi\UsernameWrangler\UsernameWrangler;

class UsernameWranglerServiceProvider extends ServiceProvider
{

    public const PATH = __DIR__ . '/../../';

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('username', UsernameWrangler::class);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(self::PATH.'config/username-wrangler.php', 'username-wrangler');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                self::PATH . 'config/username-wrangler.php' => config_path('username-wrangler.php'),
            ], 'username-wrangler:config');

            $this->publishes([
                self::PATH . 'resources/assets/media'       => public_path('vendor/username-wrangler'),
            ], 'username-wrangler:assets');

            $this->publishes([
                self::PATH . 'resources/views'     => resource_path('views/vendor/username-wrangler'),
            ], 'username-wrangler:views');
        }
    }

}
