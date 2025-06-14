<?php

declare(strict_types=1);

namespace Codeldev\LivewireMarkdownEditor\Support\Commonmark\Blocks\Youtube;

use League\CommonMark\Node\Block\AbstractBlock;

final class YouTubeBlockElement extends AbstractBlock
{
    public function __construct(private readonly string $videoId)
    {
        parent::__construct();
    }

    public function getVideoId(): string
    {
        return $this->videoId;
    }
}
