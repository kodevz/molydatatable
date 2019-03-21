<?php

namespace Kodevz\MolyDatatable\Providers;
use Illuminate\Support\ServiceProvider;
use \Kodevz\MolyDatatable\MolyDataTableFactory;
class MolyDataTableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        $this->mergeConfigFrom(__DIR__.'/../../config/molydatatable.php', 'molydatatable');


        // Register the service the package provides.
        $this->app->singleton('molydatatable', function ($app) {
            return new MolyDataTableFactory;
        });
    }


     /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['molydatatable'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../../config/molydatatable.php' => config_path('molydatatable.php'),
        ], 'molydatatable.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/moly'),
        ], 'calci.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/moly'),
        ], 'calci.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/moly'),
        ], 'calci.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
