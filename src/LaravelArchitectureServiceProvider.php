<?php

namespace JamesGifford\LaravelArchitecture;

use Illuminate\Support\ServiceProvider;
use JamesGifford\LaravelArchitecture\Commands\TestCommand;

class LaravelArchitectureServiceProvider extends ServiceProvider
{
    /**
     * Register bindings, singletons, config merges, etc.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }
    }

    /**
     * Register commands added by this package.
     */
    protected function registerCommands(): void
    {
        $this->commands([
            TestCommand::class,
        ]);
    }
}
