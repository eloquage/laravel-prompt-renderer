<?php

// config for Eloquage/LaravelPromptRenderer
return [
    'default_namespace' => 'prompts',

    'default_source' => env('PROMPT_RENDERER_SOURCE', 'blade'),

    'stack' => [
        'cache',
        'database',
        'blade',
    ],

    'sources' => [
        'blade' => [],

        'cache' => [
            'store' => env('PROMPT_RENDERER_CACHE_STORE'),
            'ttl' => (int) env('PROMPT_RENDERER_CACHE_TTL', 3600),
            'prefix' => 'prompt-renderer:',
            'underlying' => 'database',
        ],

        'database' => [
            'connection' => env('PROMPT_RENDERER_DB_CONNECTION'),
            'table' => 'prompt_templates',
            'key_column' => 'key',
            'content_column' => 'content',
        ],
    ],
];
