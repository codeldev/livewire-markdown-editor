<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Codeldev\LivewireMarkdownEditor\Exceptions\NoStorageDiskSetException;

describe('NoStorageDiskSetException', function (): void
{
    it('can be instantiated and is throwable', function (): void
    {
        $exception = new NoStorageDiskSetException;

        expect($exception)
            ->toBeInstanceOf(NoStorageDiskSetException::class)
            ->and($exception->getMessage())
            ->toBe(trans('livewire-markdown-editor::uploads.exceptions.disk.unset'))
            ->and(fn () => throw $exception)
            ->toThrow(NoStorageDiskSetException::class);
    });
});
