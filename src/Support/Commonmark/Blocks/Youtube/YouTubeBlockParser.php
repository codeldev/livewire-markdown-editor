<?php

declare(strict_types=1);

namespace Codeldev\LivewireMarkdownEditor\Support\Commonmark\Blocks\Youtube;

use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;

final class YouTubeBlockParser implements BlockStartParserInterface
{
    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        $line    = $cursor->getLine();
        $pattern = '/^https:\/\/(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})(\S*)?$/';

        if (preg_match($pattern, $line, $matches))
        {
            $videoId = $matches[3];

            $cursor->advanceToEnd();

            return BlockStart::of(new YouTubeContinueBlockParser($videoId))
                ->at($cursor);
        }

        return BlockStart::none();
    }
}
