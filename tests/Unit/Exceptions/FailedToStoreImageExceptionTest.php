<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Codeldev\LivewireMarkdownEditor\Exceptions\FailedToStoreImageException;

describe('FailedToStoreImageException', function (): void
{
    it('can be instantiated and is throwable', function (): void
    {
        $exception = new FailedToStoreImageException;

        expect($exception)
            ->toBeInstanceOf(FailedToStoreImageException::class)
            ->and($exception->getMessage())
            ->toBe(trans('livewire-markdown-editor::markdown-editor.uploads.exceptions.store'))
            ->and(fn () => throw $exception)
            ->toThrow(FailedToStoreImageException::class);
    });
});
