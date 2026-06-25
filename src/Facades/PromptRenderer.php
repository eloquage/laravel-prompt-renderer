<?php

declare(strict_types=1);

namespace Eloquage\LaravelPromptRenderer\Facades;

use Eloquage\LaravelPromptRenderer\PromptRenderer as PromptRendererService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string render(string $template, array $data = [], ?string $source = null)
 * @method static string qualifyTemplate(string $template)
 *
 * @see PromptRendererService
 */
class PromptRenderer extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PromptRendererService::class;
    }
}
