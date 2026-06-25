<?php

use Eloquage\LaravelPromptRenderer\PromptRenderer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

it('renders the example stub with variables', function () {
    $output = app(PromptRenderer::class)->render('example', [
        'context' => 'integration-test',
    ]);

    expect($output)
        ->toContain('integration-test')
        ->toContain('example agent');
});

it('qualifies templates with the default namespace', function () {
    $renderer = app(PromptRenderer::class);

    expect($renderer->qualifyTemplate('example'))->toBe('prompts.example')
        ->and($renderer->qualifyTemplate('prompts.example'))->toBe('prompts.example');
});

it('renders a fully qualified template name', function () {
    $output = app(PromptRenderer::class)->render('prompts.example', [
        'context' => 'qualified',
    ]);

    expect($output)->toContain('qualified');
});

it('throws when a template is missing', function () {
    app(PromptRenderer::class)->render('missing');
})->throws(InvalidArgumentException::class, 'Prompt template not found: prompts.missing');

it('trims rendered output', function () {
    $this->runPromptTemplatesMigration();

    DB::table('prompt_templates')->insert([
        'key' => 'prompts.trim-test',
        'content' => '  Trimmed {{ $context }}  ',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    config()->set('prompt-renderer.default_source', 'database');

    $output = app(PromptRenderer::class)->render('trim-test', [
        'context' => 'value',
    ]);

    expect($output)->toBe('Trimmed value');
});

it('allows apps to override views via published paths', function () {
    $overrideDir = sys_get_temp_dir().'/prompt-renderer-override-'.uniqid();
    $promptsDir = $overrideDir.'/prompts';

    mkdir($promptsDir, 0777, true);
    file_put_contents($promptsDir.'/example.blade.php', 'Override: {{ $context }}');

    View::prependLocation($overrideDir);

    $output = app(PromptRenderer::class)->render('example', [
        'context' => 'published',
    ]);

    expect($output)->toBe('Override: published');
});

it('renders templates from the database driver', function () {
    $this->runPromptTemplatesMigration();

    DB::table('prompt_templates')->insert([
        'key' => 'prompts.db-test',
        'content' => 'Database prompt for {{ $context }}.',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    config()->set('prompt-renderer.default_source', 'database');

    $output = app(PromptRenderer::class)->render('db-test', [
        'context' => 'agents',
    ]);

    expect($output)->toBe('Database prompt for agents.');
});

it('falls back through the stack driver to blade', function () {
    $this->runPromptTemplatesMigration();

    config()->set('prompt-renderer.default_source', 'stack');
    config()->set('prompt-renderer.stack', ['database', 'blade']);

    $output = app(PromptRenderer::class)->render('example', [
        'context' => 'stack',
    ]);

    expect($output)->toContain('stack');
});

it('caches database template contents', function () {
    $this->runPromptTemplatesMigration();

    DB::table('prompt_templates')->insert([
        'key' => 'prompts.cache-test',
        'content' => 'Cached {{ $context }}.',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    config()->set('prompt-renderer.default_source', 'cache');
    config()->set('prompt-renderer.sources.cache', [
        'store' => 'array',
        'ttl' => 3600,
        'prefix' => 'prompt-renderer:',
        'underlying' => 'database',
    ]);

    $renderer = app(PromptRenderer::class);

    DB::enableQueryLog();

    $renderer->render('cache-test', ['context' => 'first']);
    $firstQueryCount = count(DB::getQueryLog());

    DB::flushQueryLog();

    $renderer->render('cache-test', ['context' => 'second']);
    $secondQueryCount = count(DB::getQueryLog());

    expect($firstQueryCount)->toBeGreaterThan(0)
        ->and($secondQueryCount)->toBe(0)
        ->and($renderer->render('cache-test', ['context' => 'second']))->toBe('Cached second.');
});
