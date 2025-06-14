<?php

declare(strict_types=1);

namespace Codeldev\LivewireMarkdownEditor\Exceptions;

use Exception;

final class UploadActionImplementationException extends Exception
{
    public function __construct(mixed $class)
    {
        $classString = is_string($class)
            ? $class
            : gettype($class);

        parent::__construct(trans(
            'livewire-markdown-editor::markdown-editor.uploads.exceptions.implements',
            ['class' => $classString]
        ));
    }
}
