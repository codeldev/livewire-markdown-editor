<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Codeldev\LivewireMarkdownEditor\Exceptions\PreviewActionImplementationException;

describe('PreviewActionImplementationException', function (): void
{
    it('can be instantiated with a string class name and is throwable', function (): void
    {
        $exception = new PreviewActionImplementationException('InvalidClass');

        expect($exception)
            ->toBeInstanceOf(PreviewActionImplementationException::class)
            ->and($exception->getMessage())
            ->toBe(trans('livewire-markdown-editor::markdown-editor.preview.exceptions.implements', ['class' => 'InvalidClass']))
            ->and(fn () => throw $exception)
            ->toThrow(PreviewActionImplementationException::class);
    });

    it('can be instantiated with a non-string value and is throwable', function (): void
    {
        $exception = new PreviewActionImplementationException(null);

        expect($exception)
            ->toBeInstanceOf(PreviewActionImplementationException::class)
            ->and($exception->getMessage())
            ->toBe(trans('livewire-markdown-editor::markdown-editor.preview.exceptions.implements', ['class' => 'NULL']))
            ->and(fn () => throw $exception)
            ->toThrow(PreviewActionImplementationException::class);
    });
});
