<?php

/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Codeldev\LivewireMarkdownEditor\Exceptions\InvalidRendererClassException;
use Codeldev\LivewireMarkdownEditor\Tests\Fixtures\PreviewHtmlAbstractTestData;
use Illuminate\Support\Facades\Config;
use Spatie\LaravelMarkdown\MarkdownRenderer;

describe('PreviewHtmlAbstract', function ()
{
    beforeEach(function ()
    {
        $this->previewHtml   = PreviewHtmlAbstractTestData::testPreviewHtml();
        $this->validRenderer = PreviewHtmlAbstractTestData::validRendererClass();
    });

    afterEach(function ()
    {
        Mockery::close();
    });

    it('converts markdown using default renderer', function ()
    {
        Config::set('markdown.renderer_class', $this->validRenderer::class);
        Config::set('markdown.commonmark_options', ['html_input' => 'strip']);

        expect($this->previewHtml->convertPublic('# Hello World'))
            ->toBe('<p># Hello World</p>');
    });

    it('converts markdown using custom renderer', function ()
    {
        expect($this->previewHtml->convertPublic('# Hello World', $this->validRenderer::class))
            ->toBe('<p># Hello World</p>');
    });

    it('falls back to basic markdown when renderer fails', function ()
    {
        $rendererClass = PreviewHtmlAbstractTestData::rendererClassD();

        Config::set('markdown.renderer_class', get_class($rendererClass));

        expect($this->previewHtml->convertPublic('**Bold text**'))
            ->toContain('<p><strong>Bold text</strong></p>');
    });

    it('falls back to basic markdown when renderer returns non-string', function ()
    {
        $rendererClass = PreviewHtmlAbstractTestData::rendererClassC();

        Config::set('markdown.renderer_class', get_class($rendererClass));

        expect($this->previewHtml->convertPublic('*Italic text*'))
            ->toContain('<p><em>Italic text</em></p>');
    });

    it('falls back to basic markdown when renderer does not have commonmarkOptions method', function ()
    {
        $rendererClass = PreviewHtmlAbstractTestData::rendererClassB();

        Config::set('markdown.renderer_class', get_class($rendererClass));

        expect($this->previewHtml->convertPublic('## Heading'))
            ->toContain('<h2>Heading</h2>');
    });

    it('handles empty markdown', function ()
    {
        Config::set('markdown.renderer_class', $this->validRenderer::class);

        expect($this->previewHtml->convertPublic(''))
            ->toBe('<p></p>');
    });

    it('handles null custom renderer class', function ()
    {
        Config::set('markdown.renderer_class', $this->validRenderer::class);

        expect($this->previewHtml->convertPublic('Test', null))
            ->toBe('<p>Test</p>');
    });

    it('converts markdown using Str::markdown', function ()
    {
        expect($this->previewHtml->useBasicMarkdownPublic('**Bold** and *italic*'))
            ->toContain('<p><strong>Bold</strong> and <em>italic</em></p>');
    });

    it('handles empty string', function ()
    {
        expect($this->previewHtml->useBasicMarkdownPublic(''))
            ->toBe('');
    });

    it('throws exception for invalid renderer class', function ()
    {
        expect(fn() => $this->previewHtml->validateAndGetCustomRendererPublic('NonExistentClass'))
            ->toThrow(InvalidRendererClassException::class);
    });

    it('throws exception for renderer without toHtml method', function ()
    {
        $invalidRenderer = PreviewHtmlAbstractTestData::getInvalidRendererClassE();

        expect(fn() => $this->previewHtml->validateAndGetCustomRendererPublic(get_class($invalidRenderer)))
            ->toThrow(InvalidRendererClassException::class);
    });

    it('returns configured renderer class', function ()
    {
        Config::set('markdown.renderer_class', $this->validRenderer::class);

        expect($this->previewHtml->getRendererClassPublic())
            ->toBe($this->validRenderer::class);
    });

    it('throws exception when configured class is invalid', function ()
    {
        Config::set('markdown.renderer_class', 'NonExistentClass');

        expect(fn() => $this->previewHtml->getRendererClassPublic())
            ->toThrow(InvalidRendererClassException::class);
    });

    it('throws exception when configured class is not a string', function ()
    {
        Config::set('markdown.renderer_class', 123);

        expect(fn() => $this->previewHtml->getRendererClassPublic())
            ->toThrow(InvalidRendererClassException::class);
    });

    it('returns true for valid renderer class', function ()
    {
        expect($this->previewHtml->isValidRendererClassPublic($this->validRenderer::class))
            ->toBeTrue();
    });

    it('returns false for non-string input', function ()
    {
        expect($this->previewHtml->isValidRendererClassPublic(123))
            ->toBeFalse()
            ->and($this->previewHtml->isValidRendererClassPublic(null))
            ->toBeFalse()
            ->and($this->previewHtml->isValidRendererClassPublic([]))
            ->toBeFalse()
            ->and($this->previewHtml->isValidRendererClassPublic(true))
            ->toBeFalse();
    });

    it('returns false for non-existent class', function ()
    {
        expect($this->previewHtml->isValidRendererClassPublic('NonExistentClass'))
            ->toBeFalse();
    });

    it('returns false for class without toHtml method', function ()
    {
        $invalidRenderer = PreviewHtmlAbstractTestData::getInvalidRendererClassD();

        expect($this->previewHtml->isValidRendererClassPublic(get_class($invalidRenderer)))
            ->toBeFalse();
    });

    it('returns false for class with toHtml method having wrong number of parameters', function ()
    {
        $invalidRendererB = PreviewHtmlAbstractTestData::getInvalidRendererClassB();
        $invalidRendererC = PreviewHtmlAbstractTestData::getInvalidRendererClassC();

        expect($this->previewHtml->isValidRendererClassPublic(get_class($invalidRendererB)))
            ->toBeFalse()
            ->and($this->previewHtml->isValidRendererClassPublic(get_class($invalidRendererC)))
            ->toBeFalse();
    });

    it('returns true for MarkdownRenderer class', function ()
    {
        expect($this->previewHtml->isValidRendererClassPublic(MarkdownRenderer::class))
            ->toBeTrue();
    });

    it('handles complete workflow with valid configuration', function ()
    {
        Config::set('markdown.renderer_class', $this->validRenderer::class);
        Config::set('markdown.commonmark_options', ['html_input' => 'strip']);

        expect($this->previewHtml->convertPublic('# Title\n\n**Bold text**'))
            ->toBeString()
            ->toContain('Title')
            ->toContain('Bold text');
    });

    it('gracefully degrades when everything fails', function ()
    {
        Config::set('markdown.renderer_class', 'NonExistentClass');

        expect($this->previewHtml->convertPublic('**Fallback test**'))
            ->toContain('<p><strong>Fallback test</strong></p>');
    });

    it('returns false for class with private toHtml method', function ()
    {
        $invalidRenderer = PreviewHtmlAbstractTestData::getInvalidRendererClass();

        expect($this->previewHtml->isValidRendererClassPublic(get_class($invalidRenderer)))
            ->toBeFalse();
    });

    it('returns false for class with private commonmarkOptions method', function ()
    {
        $invalidRenderer = PreviewHtmlAbstractTestData::getInvalidRendererClassWithPrivateCommonmarkOptions();

        expect($this->previewHtml->isValidRendererClassPublic(get_class($invalidRenderer)))
            ->toBeFalse();
    });

    it('rejects invalid renderer classes', function ()
    {
        collect(PreviewHtmlAbstractTestData::getInvalidRendererClasses())->each(
            fn (string $class) => expect($this->previewHtml->isValidRendererClassPublic($class))->toBeFalse()
        );
    });

    it('rejects non-string inputs for renderer class validation', function ()
    {
        collect(PreviewHtmlAbstractTestData::getNonStringInputs())->each(
            fn($input) => expect($this->previewHtml->isValidRendererClassPublic($input))->toBeFalse()
        );
    });
});
