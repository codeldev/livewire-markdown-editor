<?php

declare(strict_types=1);

namespace Codeldev\LivewireMarkdownEditor\Support\Commonmark\Blocks\Youtube;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use Stringable;
use Throwable;

final class YouTubeBlockRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): Stringable
    {
        try
        {
            YouTubeBlockElement::assertInstanceOf($node);

            /** @var YouTubeBlockElement $youtubeNode */
            $youtubeNode = $node;

            $html = view('livewire-markdown-editor::components.markdown-editor.extensions.youtube', [
                'videoId' => $youtubeNode->getVideoId(),
            ])->render();
        }
        catch (Throwable $e)
        {
            $html = '';

            report($e);
        }

        return new readonly class($html) implements Stringable
        {
            public function __construct(private string $html) {}

            public function __toString(): string
            {
                return $this->html;
            }
        };
    }
}
