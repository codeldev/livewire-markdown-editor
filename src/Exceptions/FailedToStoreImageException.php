<?php

declare(strict_types=1);

namespace Codeldev\LivewireMarkdownEditor\Exceptions;

use Exception;

final class FailedToStoreImageException extends Exception
{
    public function __construct()
    {
        parent::__construct(trans('livewire-markdown-editor::markdown-editor.uploads.exceptions.store'));
    }
}
