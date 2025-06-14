<?php

declare(strict_types=1);

namespace Codeldev\LivewireMarkdownEditor\Contracts;

interface PreviewHtmlInterface
{
    public function handle(string $markdown): string;
}
