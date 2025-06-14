<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Codeldev\LivewireMarkdownEditor\Exceptions\InvalidStorageDiskException;

describe('InvalidStorageDiskException', function (): void
{
    it('can be instantiated and is throwable', function (): void
    {
        $exception = new InvalidStorageDiskException('invalid-disk');

        expect($exception)
            ->toBeInstanceOf(InvalidStorageDiskException::class)
            ->and($exception->getMessage())
            ->toBe(trans('livewire-markdown-editor::uploads.exceptions.disk.invalid', ['disk' => 'invalid-disk']))
            ->and(fn () => throw $exception)
            ->toThrow(InvalidStorageDiskException::class);
    });
});
