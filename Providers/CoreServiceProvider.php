<?php

namespace Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Helpers\BladeDirective;
use Modules\Core\Helpers\ValidatorExtend;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();

        ValidatorExtend::boot();
        BladeDirective::boot();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Admin menu bind
        $this->app->singleton('adminMenu', function () {
            return $this->app->make('Modules\Core\Helpers\Menu\AdminMenu');
        });

        // Config menu bind
        $this->app->singleton('configMenu', function () {
            return $this->app->make('Modules\Core\Helpers\Menu\ConfigMenu');
        });
        
        // Route helper bind
        $this->app->singleton('routeHelper', function () {
            return $this->app->make('Modules\Core\Helpers\RouteHelper');
        });

        // Register Middleware
        $this->app['router']->middleware('ajax', '\Modules\Core\Http\Middleware\Ajax');
        $this->app['router']->middleware('revalidate', '\Modules\Core\Http\Middleware\RevalidateBackHistory');
        
        $this->registerAlias();
    }

    /**
     * Register Alias
     * 
     * @return void
     */
    public function registerAlias() 
    {
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('AdminMenu', 'Modules\Core\Facades\AdminMenu');
        $loader->alias('ConfigMenu', 'Modules\Core\Facades\ConfigMenu');
        $loader->alias('RouteHelper', 'Modules\Core\Facades\RouteHelper');
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        // $this->publishes([
        //     __DIR__.'/../config/config.php' => config_path('core.php'),
        // ], 'config');

        if (! file_exists($this->app->getCachedConfigPath())) 
        {
            $this->mergeConfigFrom(
                __DIR__.'/../config/config.php', 'core'
            );

            $config = $this->app['config']->get('menu', []);
            $this->app['config']->set('menu', array_replace_recursive(
                require __DIR__.'/../config/menu.php', $config));
        }
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = base_path('resources/views/modules/core');

        $sourcePath = __DIR__.'/../resources/views';

        // $this->publishes([
        //     $sourcePath => $viewPath
        // ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/core';
        }, \Config::get('view.paths')), [$sourcePath]), 'core');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = base_path('resources/lang/modules/core');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'core');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../resources/lang', 'core');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
