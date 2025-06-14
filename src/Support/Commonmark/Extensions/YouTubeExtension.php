<?php

declare(strict_types=1);

namespace Codeldev\LivewireMarkdownEditor\Support\Commonmark\Extensions;

use Codeldev\LivewireMarkdownEditor\Support\Commonmark\Blocks\Youtube\YouTubeBlockElement;
use Codeldev\LivewireMarkdownEditor\Support\Commonmark\Blocks\Youtube\YouTubeBlockParser;
use Codeldev\LivewireMarkdownEditor\Support\Commonmark\Blocks\Youtube\YouTubeBlockRenderer;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ExtensionInterface;

final class YouTubeExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addBlockStartParser(new YouTubeBlockParser, 250);
        $environment->addRenderer(YouTubeBlockElement::class, new YouTubeBlockRenderer);
    }
}
