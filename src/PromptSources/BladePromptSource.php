<?php

declare(strict_types=1);

namespace Eloquage\LaravelPromptRenderer\PromptSources;

use Eloquage\LaravelPromptRenderer\Contracts\PromptSource;
use Eloquage\LaravelPromptRenderer\Data\ResolvedPrompt;
use Illuminate\Support\Facades\View;
use InvalidArgumentException;

class BladePromptSource implements PromptSource
{
    public function resolve(string $template): ResolvedPrompt
    {
        return ResolvedPrompt::view($template);
    }

    public function contents(string $template): string
    {
        if (! $this->exists($template)) {
            throw new InvalidArgumentException("Prompt template not found: {$template}");
        }

        $path = View::getFinder()->find($template);

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new InvalidArgumentException("Unable to read prompt template: {$template}");
        }

        return $contents;
    }

    public function exists(string $template): bool
    {
        return View::exists($template);
    }
}
