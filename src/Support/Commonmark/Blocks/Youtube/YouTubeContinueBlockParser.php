<?php

declare(strict_types=1);

namespace Codeldev\LivewireMarkdownEditor\Support\Commonmark\Blocks\Youtube;

use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;

final class YouTubeContinueBlockParser extends AbstractBlockContinueParser
{
    private readonly YouTubeBlockElement $block;

    public function __construct(string $videoId)
    {
        $this->block = new YouTubeBlockElement($videoId);
    }

    public function getBlock(): YouTubeBlockElement
    {
        return $this->block;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        return BlockContinue::none();
    }
}
