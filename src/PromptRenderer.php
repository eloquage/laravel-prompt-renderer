<?php

declare(strict_types=1);

namespace Eloquage\LaravelPromptRenderer;

use Eloquage\LaravelPromptRenderer\Managers\PromptSourceManager;
use Illuminate\Support\Facades\Blade;
use InvalidArgumentException;

class PromptRenderer
{
    public function __construct(
        private readonly PromptSourceManager $promptSources,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function render(string $template, array $data = [], ?string $source = null): string
    {
        $qualified = $this->qualifyTemplate($template);

        $driver = $source !== null
            ? $this->promptSources->driver($source)
            : $this->promptSources->driver();

        if (! $driver->exists($qualified)) {
            throw new InvalidArgumentException("Prompt template not found: {$qualified}");
        }

        $resolved = $driver->resolve($qualified);

        $output = $resolved->isView()
            ? view($resolved->view, $data)->render()
            : Blade::render($resolved->content, $data);

        return trim($output);
    }

    public function qualifyTemplate(string $template): string
    {
        $namespace = (string) config('prompt-renderer.default_namespace', 'prompts');
        $prefix = $namespace.'.';

        if (str_starts_with($template, $prefix)) {
            return $template;
        }

        return $prefix.$template;
    }
}
