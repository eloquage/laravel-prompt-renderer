<?php

declare(strict_types=1);

namespace Eloquage\LaravelPromptRenderer\PromptSources;

use Eloquage\LaravelPromptRenderer\Contracts\PromptSource;
use Eloquage\LaravelPromptRenderer\Data\ResolvedPrompt;
use Eloquage\LaravelPromptRenderer\Managers\PromptSourceManager;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;

class CachePromptSource implements PromptSource
{
    public function __construct(
        private readonly PromptSourceManager $manager,
        private readonly ?string $store,
        private readonly int $ttl,
        private readonly string $prefix,
        private readonly string $underlying,
    ) {}

    public function exists(string $template): bool
    {
        if ($this->cache()->has($this->cacheKey($template))) {
            return true;
        }

        return $this->underlying()->exists($template);
    }

    private function cache(): Repository
    {
        return $this->store !== null
            ? Cache::store($this->store)
            : Cache::store();
    }

    private function cacheKey(string $template): string
    {
        return $this->prefix.$template;
    }

    private function underlying(): PromptSource
    {
        return $this->manager->driver($this->underlying);
    }

    public function resolve(string $template): ResolvedPrompt
    {
        $content = $this->cache()->remember(
            $this->cacheKey($template),
            $this->ttl,
            fn (): string => $this->underlying()->contents($template),
        );

        return ResolvedPrompt::string($content);
    }

    public function contents(string $template): string
    {
        return $this->cache()->remember(
            $this->cacheKey($template),
            $this->ttl,
            fn (): string => $this->underlying()->contents($template),
        );
    }
}
