# Laravel Prompt Renderer

- `PromptRenderer::render($template, $data)` qualifies templates with `default_namespace` (default: `prompts`).
- Prompt sources: `blade`, `database`, `cache`, `stack` via `PromptSourceManager`.
- Publish tags: `prompt-renderer-views`, `laravel-prompt-renderer-migrations`, `laravel-prompt-renderer-config`.
- No `laravel/ai` dependency; consumers wire prompts into agent `instructions()`.
