<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Codeldev\LivewireMarkdownEditor\Exceptions\UploadActionImplementationException;

describe('UploadActionImplementationException', function (): void
{
    it('can be instantiated with a string class name and is throwable', function (): void
    {
        $exception = new UploadActionImplementationException('InvalidClass');

        expect($exception)
            ->toBeInstanceOf(UploadActionImplementationException::class)
            ->and($exception->getMessage())
            ->toBe(trans('livewire-markdown-editor::markdown-editor.uploads.exceptions.implements', ['class' => 'InvalidClass']))
            ->and(fn () => throw $exception)
            ->toThrow(UploadActionImplementationException::class);
    });

    it('can be instantiated with a non-string value and is throwable', function (): void
    {
        $exception = new UploadActionImplementationException(null);

        expect($exception)
            ->toBeInstanceOf(UploadActionImplementationException::class)
            ->and($exception->getMessage())
            ->toBe(trans('livewire-markdown-editor::markdown-editor.uploads.exceptions.implements', ['class' => 'NULL']))
            ->and(fn () => throw $exception)
            ->toThrow(UploadActionImplementationException::class);
    });
});
