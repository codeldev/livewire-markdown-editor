<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Codeldev\LivewireMarkdownEditor\Exceptions\InvalidUploadActionClassException;

describe('InvalidUploadActionClassException', function (): void
{
    it('can be instantiated with a string class name and is throwable', function (): void
    {
        $exception = new InvalidUploadActionClassException('InvalidClass');

        expect($exception)
            ->toBeInstanceOf(InvalidUploadActionClassException::class)
            ->and($exception->getMessage())
            ->toBe(trans('livewire-markdown-editor::markdown-editor.uploads.exceptions.action', ['class' => 'InvalidClass']))
            ->and(fn () => throw $exception)
            ->toThrow(InvalidUploadActionClassException::class);
    });

    it('can be instantiated with a non-string value and is throwable', function (): void
    {
        $exception = new InvalidUploadActionClassException(null);

        expect($exception)
            ->toBeInstanceOf(InvalidUploadActionClassException::class)
            ->and($exception->getMessage())
            ->toBe(trans('livewire-markdown-editor::markdown-editor.uploads.exceptions.action', ['class' => 'NULL']))
            ->and(fn () => throw $exception)
            ->toThrow(InvalidUploadActionClassException::class);
    });
});
