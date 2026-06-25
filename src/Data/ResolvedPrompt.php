<?php

declare(strict_types=1);

namespace Eloquage\LaravelPromptRenderer\Data;

final class ResolvedPrompt
{
    private function __construct(
        public readonly ?string $view,
        public readonly ?string $content,
    ) {}

    public static function view(string $view): self
    {
        return new self(view: $view, content: null);
    }

    public static function string(string $content): self
    {
        return new self(view: null, content: $content);
    }

    public function isView(): bool
    {
        return $this->view !== null;
    }

    public function isString(): bool
    {
        return $this->content !== null;
    }
}
