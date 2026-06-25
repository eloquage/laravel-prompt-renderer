<?php

declare(strict_types=1);

namespace Eloquage\LaravelPromptRenderer\Contracts;

use Eloquage\LaravelPromptRenderer\Data\ResolvedPrompt;

interface PromptSource
{
    public function exists(string $template): bool;

    public function resolve(string $template): ResolvedPrompt;

    public function contents(string $template): string;
}
