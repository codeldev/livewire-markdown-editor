<?php

declare(strict_types=1);

namespace Codeldev\LivewireMarkdownEditor\Exceptions;

use Exception;

final class InvalidPreviewActionClassException extends Exception
{
    public function __construct(mixed $class)
    {
        $classString = is_string(value: $class)
            ? $class
            : gettype(value: $class);

        parent::__construct(trans(
            'livewire-markdown-editor::markdown-editor.preview.exceptions.action',
            ['class' => $classString]
        ));
    }
}
