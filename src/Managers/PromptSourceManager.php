<?php

declare(strict_types=1);

namespace Eloquage\LaravelPromptRenderer\Managers;

use Eloquage\LaravelPromptRenderer\Contracts\PromptSource;
use Eloquage\LaravelPromptRenderer\PromptSources\BladePromptSource;
use Eloquage\LaravelPromptRenderer\PromptSources\CachePromptSource;
use Eloquage\LaravelPromptRenderer\PromptSources\DatabasePromptSource;
use Eloquage\LaravelPromptRenderer\PromptSources\StackPromptSource;
use Illuminate\Support\Manager;

class PromptSourceManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return (string) config('prompt-renderer.default_source', 'blade');
    }

    public function createBladeDriver(): PromptSource
    {
        return new BladePromptSource;
    }

    public function createDatabaseDriver(): PromptSource
    {
        return new DatabasePromptSource;
    }

    public function createCacheDriver(): PromptSource
    {
        /** @var array{
         *     store: string|null,
         *     ttl: int,
         *     prefix: string,
         *     underlying: string,
         * } $config
         */
        $config = config('prompt-renderer.sources.cache');

        return new CachePromptSource(
            manager: $this,
            store: $config['store'],
            ttl: $config['ttl'],
            prefix: $config['prefix'],
            underlying: $config['underlying'],
        );
    }

    public function createStackDriver(): PromptSource
    {
        return new StackPromptSource($this);
    }

    protected function createDriver($driver): PromptSource
    {
        /** @var PromptSource $instance */
        $instance = parent::createDriver($driver);

        return $instance;
    }
}
