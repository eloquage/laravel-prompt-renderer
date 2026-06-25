<?php

declare(strict_types=1);

namespace Eloquage\LaravelPromptRenderer\PromptSources;

use Eloquage\LaravelPromptRenderer\Contracts\PromptSource;
use Eloquage\LaravelPromptRenderer\Data\ResolvedPrompt;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DatabasePromptSource implements PromptSource
{
    public function exists(string $template): bool
    {
        $config = $this->config();

        return DB::connection($config['connection'])
            ->table($config['table'])
            ->where($config['key_column'], $template)
            ->exists();
    }

    /**
     * @return array{
     *     connection: string|null,
     *     table: string,
     *     key_column: string,
     *     content_column: string,
     * }
     */
    private function config(): array
    {
        /** @var array{
         *     connection: string|null,
         *     table: string,
         *     key_column: string,
         *     content_column: string,
         * } $config
         */
        $config = config('prompt-renderer.sources.database');

        return $config;
    }

    public function resolve(string $template): ResolvedPrompt
    {
        return ResolvedPrompt::string($this->contents($template));
    }

    public function contents(string $template): string
    {
        $config = $this->config();

        $content = DB::connection($config['connection'])
            ->table($config['table'])
            ->where($config['key_column'], $template)
            ->value($config['content_column']);

        if (! is_string($content) || $content === '') {
            throw new InvalidArgumentException("Prompt template not found: {$template}");
        }

        return $content;
    }
}
