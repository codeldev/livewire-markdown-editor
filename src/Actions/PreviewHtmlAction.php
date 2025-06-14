<?php

declare(strict_types=1);

namespace Codeldev\LivewireMarkdownEditor\Actions;

use Codeldev\LivewireMarkdownEditor\Abstracts\PreviewHtmlAbstract;
use Codeldev\LivewireMarkdownEditor\Contracts\PreviewHtmlInterface;

final class PreviewHtmlAction extends PreviewHtmlAbstract implements PreviewHtmlInterface
{
    public function handle(string $markdown): string
    {
        return $this->convert($markdown);
    }
}
