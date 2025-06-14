<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Codeldev\LivewireMarkdownEditor\Actions\PreviewHtmlAction;
use Codeldev\LivewireMarkdownEditor\Contracts\PreviewHtmlInterface;

describe('PreviewHtmlAction', function (): void
{
    beforeEach(function (): void
    {
        $this->previewAction = new PreviewHtmlAction;
    });

    it('implements PreviewHtmlInterface', function (): void
    {
        expect($this->previewAction)
            ->toBeInstanceOf(PreviewHtmlInterface::class);
    });

    it('converts markdown to HTML', function (): void
    {
        $markdown = "# Heading\n\nThis is a paragraph with **bold** text.";

        expect($this->previewAction->handle($markdown))
            ->toContain('<h1 id="heading">Heading</h1>')
            ->toContain('<p>This is a paragraph with <strong>bold</strong> text.</p>');
    });

    it('handles empty markdown input', function (): void
    {
        expect($this->previewAction->handle(''))
            ->toBe('');
    });

    it('handles markdown with code blocks', function (): void
    {
        $markdown = "```php\n<?php\n\necho 'Hello World';\n```";

        expect($this->previewAction->handle($markdown))
            ->toContain('<code class="language-php">');
    });

    it('handles markdown with links', function (): void
    {
        $markdown = '[Link text](https://example.com)';

        expect($this->previewAction->handle($markdown))
            ->toContain('<a rel="noopener noreferrer" target="_blank" href="https://example.com">Link text</a>');
    });

    it('handles markdown with images', function (): void
    {
        $markdown = '![Alt text](https://example.com/image.jpg)';

        expect($this->previewAction->handle($markdown))
            ->toContain('<img src="https://example.com/image.jpg"')
            ->toContain('alt="Alt text"');
    });

    it('handles markdown with lists', function (): void
    {
        $markdown = "- Item 1\n- Item 2\n- Item 3";

        expect($this->previewAction->handle($markdown))
            ->toContain('<ul>')
            ->toContain('<li>Item 1</li>')
            ->toContain('<li>Item 2</li>')
            ->toContain('<li>Item 3</li>')
            ->toContain('</ul>');
    });

    it('handles markdown with tables', function (): void
    {
        $markdown = "| Header 1 | Header 2 |\n| -------- | -------- |\n| Cell 1   | Cell 2   |";

        expect($this->previewAction->handle($markdown))
            ->toContain('<table>')
            ->toContain('<th>Header 1</th>')
            ->toContain('<th>Header 2</th>')
            ->toContain('<td>Cell 1</td>')
            ->toContain('<td>Cell 2</td>')
            ->toContain('</table>');
    });
});
