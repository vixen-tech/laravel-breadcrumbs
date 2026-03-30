<?php

namespace Vixen\Breadcrumbs;

use Vixen\Breadcrumbs\Facades\Crumbs;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BreadcrumbsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->registerViews();
        $this->registerBladeDirective();
        $this->registerMacros();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerSingleton();
    }

    private function registerSingleton(): void
    {
        $this->app->singleton(Breadcrumbs::class, function () {
            return new Breadcrumbs();
        });
    }

    private function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/breadcrumbs.php', 'breadcrumbs');
        $this->publishes([
            __DIR__.'/../config/breadcrumbs.php' => config_path('breadcrumbs.php'),
        ], 'breadcrumbs-config');
    }

    private function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'breadcrumbs');
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/breadcrumbs'),
        ], 'breadcrumbs-views');
    }

    private function registerMacros(): void
    {
        Route::macro('crumbs', function (callable $closure) {
            crumbs($closure);

            return $this;
        });
    }

    private function registerBladeDirective(): void
    {
        Blade::directive('crumbs', function (?string $view = null) {
            return Crumbs::render($view);
        });
    }
}
