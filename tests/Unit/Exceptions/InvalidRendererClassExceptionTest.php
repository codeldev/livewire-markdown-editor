<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Codeldev\LivewireMarkdownEditor\Exceptions\InvalidRendererClassException;

describe('InvalidRendererClassException', function (): void
{
    it('can be instantiated with a string class name and is throwable', function (): void
    {
        $exception = new InvalidRendererClassException('InvalidClass');

        expect($exception)
            ->toBeInstanceOf(InvalidRendererClassException::class)
            ->and($exception->getMessage())
            ->toBe(trans('livewire-markdown-editor::markdown-editor.preview.exceptions.renderer', ['class' => 'InvalidClass']))
            ->and(fn () => throw $exception)
            ->toThrow(InvalidRendererClassException::class);
    });

    it('can be instantiated with a non-string value and is throwable', function (): void
    {
        $exception = new InvalidRendererClassException(null);

        expect($exception)
            ->toBeInstanceOf(InvalidRendererClassException::class)
            ->and($exception->getMessage())
            ->toBe(trans('livewire-markdown-editor::markdown-editor.preview.exceptions.renderer', ['class' => 'NULL']))
            ->and(fn () => throw $exception)
            ->toThrow(InvalidRendererClassException::class);
    });
});
