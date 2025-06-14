<?php

declare(strict_types=1);

namespace Codeldev\LivewireMarkdownEditor;

use Codeldev\LivewireMarkdownEditor\Livewire\MarkdownEditor;
use Livewire\Livewire;
use Override;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class LivewireMarkdownEditorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('livewire-markdown-editor')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews()
            ->hasViewComponents('markdown-editor');
    }

    #[Override]
    public function bootingPackage(): void
    {
        Livewire::component('markdown-editor', MarkdownEditor::class);
    }

    #[Override]
    public function packageBooted(): void
    {
        $this->extendMarkdownConfig();
    }

    private function extendMarkdownConfig(): void
    {
        if (! config()->has('markdown'))
        {
            return;
        }

        $baseConfig   = config('markdown');
        $editorConfig = config('livewire-markdown-editor');

        if (! is_array($baseConfig) || ! is_array($editorConfig))
        {
            return;
        }

        /** @var array<string, mixed> $baseConfig */
        /** @var array<string, mixed> $editorConfig */
        $this->mergeExtensions($baseConfig, $editorConfig);
        $this->mergeOptions($baseConfig, $editorConfig);
    }

    /**
     * @param  array<string, mixed>  $baseConfig
     * @param  array<string, mixed>  $editorConfig
     */
    private function mergeExtensions(array $baseConfig, array $editorConfig): void
    {
        if (! isset($editorConfig['commonmark']) || ! is_array($editorConfig['commonmark']))
        {
            return;
        }

        $commonmarkConfig = $editorConfig['commonmark'];
        $editorExtensions = $commonmarkConfig['extensions'] ?? null;

        if (! is_array($editorExtensions))
        {
            return;
        }

        $baseExtensions = is_array($baseConfig['extensions'] ?? null)
            ? $baseConfig['extensions']
            : [];

        $mergedExtensions = array_unique(
            array_merge($baseExtensions, $editorExtensions)
        );

        config(['markdown.extensions' => $mergedExtensions]);
    }

    /**
     * @param  array<string, mixed>  $baseConfig
     * @param  array<string, mixed>  $editorConfig
     */
    private function mergeOptions(array $baseConfig, array $editorConfig): void
    {
        if (! isset($editorConfig['commonmark']) || ! is_array($editorConfig['commonmark']))
        {
            return;
        }

        $commonmarkConfig = $editorConfig['commonmark'];
        $editorOptions    = $commonmarkConfig['options'] ?? null;

        if (! is_array($editorOptions))
        {
            return;
        }

        $baseOptions = is_array($baseConfig['commonmark_options'] ?? null)
            ? $baseConfig['commonmark_options']
            : [];

        $mergedOptions = array_merge_recursive(
            $baseOptions,
            $editorOptions
        );

        config(['markdown.commonmark_options' => $mergedOptions]);
    }
}
