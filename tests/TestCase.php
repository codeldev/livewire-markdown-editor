<?php

declare(strict_types=1);

namespace Codeldev\LivewireMarkdownEditor\Tests;

use Codeldev\LivewireMarkdownEditor\LivewireMarkdownEditorServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelMarkdown\MarkdownServiceProvider;

class TestCase extends Orchestra
{
    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            MarkdownServiceProvider::class,
            LivewireMarkdownEditorServiceProvider::class,
        ];
    }
}
