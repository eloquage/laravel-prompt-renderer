<?php

declare(strict_types=1);

namespace Eloquage\LaravelPromptRenderer;

use Eloquage\LaravelPromptRenderer\Managers\PromptSourceManager;
use Illuminate\Support\Facades\View;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelPromptRendererServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-prompt-renderer')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_prompt_templates_table');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(PromptSourceManager::class);
        $this->app->singleton(PromptRenderer::class);
    }

    public function packageBooted(): void
    {
        $publishedPath = resource_path('views/vendor/prompt-renderer');

        if (is_dir($publishedPath)) {
            View::prependLocation($publishedPath);
        }

        View::addLocation($this->package->basePath('/resources/views'));

        $this->publishes([
            $this->package->basePath('/resources/views') => $publishedPath,
        ], 'prompt-renderer-views');
    }
}
