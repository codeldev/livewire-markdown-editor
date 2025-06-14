<?php

declare(strict_types=1);

namespace Codeldev\LivewireMarkdownEditor\Exceptions;

use Exception;

final class InvalidStorageDiskException extends Exception
{
    public function __construct(string $disk)
    {
        parent::__construct(trans('livewire-markdown-editor::uploads.exceptions.disk.invalid', ['disk' => $disk]));
    }
}
