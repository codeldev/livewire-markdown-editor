<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Codeldev\LivewireMarkdownEditor\Exceptions\InvalidPreviewActionClassException;

describe('InvalidPreviewActionClassException', function (): void
{
    it('can be instantiated with a string class name and is throwable', function (): void
    {
        $exception = new InvalidPreviewActionClassException('InvalidClass');

        expect($exception)
            ->toBeInstanceOf(InvalidPreviewActionClassException::class)
            ->and($exception->getMessage())
            ->toBe(trans('livewire-markdown-editor::markdown-editor.preview.exceptions.action', ['class' => 'InvalidClass']))
            ->and(fn () => throw $exception)
            ->toThrow(InvalidPreviewActionClassException::class);
    });

    it('can be instantiated with a non-string value and is throwable', function (): void
    {
        $exception = new InvalidPreviewActionClassException(null);

        expect($exception)
            ->toBeInstanceOf(InvalidPreviewActionClassException::class)
            ->and($exception->getMessage())
            ->toBe(trans('livewire-markdown-editor::markdown-editor.preview.exceptions.action', ['class' => 'NULL']))
            ->and(fn () => throw $exception)
            ->toThrow(InvalidPreviewActionClassException::class);
    });
});
