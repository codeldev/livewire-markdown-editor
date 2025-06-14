<?php

declare(strict_types=1);

namespace Codeldev\LivewireMarkdownEditor\Exceptions;

use Exception;

final class NoStorageDiskSetException extends Exception
{
    public function __construct()
    {
        parent::__construct(message: trans('livewire-markdown-editor::uploads.exceptions.disk.unset'));
    }
}
