<?php

namespace BitfactoryNL\PostcodeNl;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use BitfactoryNL\PostcodeNl\Http\PostcodeNlClient;
use BitfactoryNL\PostcodeNl\Services\AddressLookup;
use BitfactoryNL\PostcodeNl\Validators\AddressLookupValidator;

/**
 * Class PostcodeNlServiceProvider
 *
 * @property Application $app
 */
class PostcodeNlServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            $this->getPathToConfigFile() => config_path('postcode-nl.php')
        ], 'config');

        if (array_get($this->app['config'], 'postcode-nl.enableRoutes', false) and ! $this->app->routesAreCached()) {
            require __DIR__ . '/Http/routes.php';
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom($this->getPathToConfigFile(), 'postcode-nl');

        $this->app->singleton(AddressLookup::class, function ($app) {
            return new AddressLookup($app[AddressLookupValidator::class], $app[PostcodeNlClient::class]);
        });
    }

    /**
     * Composes the path to the config file.
     *
     * @return string
     */
    protected function getPathToConfigFile(): string
    {
        return __DIR__ . '/../config/postcode-nl.php';
    }
}
