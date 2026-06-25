<?php

declare(strict_types=1);

namespace Eloquage\LaravelPromptRenderer\PromptSources;

use Eloquage\LaravelPromptRenderer\Contracts\PromptSource;
use Eloquage\LaravelPromptRenderer\Data\ResolvedPrompt;
use Eloquage\LaravelPromptRenderer\Managers\PromptSourceManager;
use InvalidArgumentException;

class StackPromptSource implements PromptSource
{
    public function __construct(
        private readonly PromptSourceManager $manager,
    ) {}

    public function exists(string $template): bool
    {
        return $this->matchingDriver($template) !== null;
    }

    private function matchingDriver(string $template): ?PromptSource
    {
        /** @var list<string> $stack */
        $stack = config('prompt-renderer.stack', []);

        foreach ($stack as $driverName) {
            $driver = $this->manager->driver($driverName);

            if ($driver->exists($template)) {
                return $driver;
            }
        }

        return null;
    }

    public function resolve(string $template): ResolvedPrompt
    {
        $driver = $this->matchingDriver($template);

        if ($driver === null) {
            throw new InvalidArgumentException("Prompt template not found: {$template}");
        }

        return $driver->resolve($template);
    }

    public function contents(string $template): string
    {
        $driver = $this->matchingDriver($template);

        if ($driver === null) {
            throw new InvalidArgumentException("Prompt template not found: {$template}");
        }

        return $driver->contents($template);
    }
}
