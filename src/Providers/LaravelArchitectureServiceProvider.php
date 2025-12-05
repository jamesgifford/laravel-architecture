<?php

namespace JamesGifford\LaravelArchitecture\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use JamesGifford\LaravelArchitecture\Commands\MakeControllerUnit\MakeControllerUnitCommand;
use JamesGifford\LaravelArchitecture\Commands\TestCommand;
use JamesGifford\LaravelArchitecture\Scaffolds\CreateControllerUnit\CreateControllerUnitScaffold;

class LaravelArchitectureServiceProvider extends ServiceProvider
{
    /**
     * Register bindings, singletons, config merges, etc.
     */
    public function register(): void
    {
        $this->app->singleton(CreateControllerUnitScaffold::class, function ($app) {
            return new CreateControllerUnitScaffold(
                files: new Filesystem(),
                rootNamespace: $app->getNamespace(),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }

        $this->publishes([
            __DIR__ . '/../stubs' => base_path('stubs/jamesgifford/laravel-architecture'),
        ], 'laravel-architecture-stubs');
    }

    /**
     * Register commands added by this package.
     */
    protected function registerCommands(): void
    {
        $this->commands([
            TestCommand::class,
            MakeControllerUnitCommand::class,
        ]);
    }
}
