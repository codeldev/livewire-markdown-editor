<?php

declare(strict_types=1);

namespace Codeldev\LivewireMarkdownEditor\Abstracts;

use Codeldev\LivewireMarkdownEditor\Exceptions\InvalidRendererClassException;
use Illuminate\Support\Str;
use ReflectionClass;
use Spatie\LaravelMarkdown\MarkdownRenderer;
use Throwable;

abstract class PreviewHtmlAbstract
{
    protected function convert(string $markdown, ?string $customRendererClass = null): string
    {
        try
        {
            $rendererClass = $customRendererClass !== null
                ? $this->validateAndGetCustomRenderer($customRendererClass)
                : $this->getRendererClass();

            $renderer = app($rendererClass);

            if (
                is_object($renderer)
                && method_exists($renderer, 'toHtml')
                && method_exists($renderer, 'commonmarkOptions')
            ) {
                $options = config('markdown.commonmark_options', []);

                // @phpstan-ignore-next-line
                $result  = $renderer
                    ->commonmarkOptions($options)
                    ->toHtml($markdown);

                if (is_string($result))
                {
                    return $result;
                }
            }
        }
        catch (Throwable $e)
        {
            report($e);
        }

        return $this->useBasicMarkdown($markdown);
    }

    protected function useBasicMarkdown(string $markdown): string
    {
        return Str::markdown($markdown);
    }

    /** @throws InvalidRendererClassException */
    protected function validateAndGetCustomRenderer(string $customRendererClass): string
    {
        if (! $this->isValidRendererClass($customRendererClass))
        {
            throw new InvalidRendererClassException($customRendererClass);
        }

        return $customRendererClass;
    }

    /** @throws InvalidRendererClassException */
    protected function getRendererClass(): string
    {
        /** @var string|mixed $rendererClass */
        $rendererClass = config(
            'markdown.renderer_class',
            MarkdownRenderer::class
        );

        if (! $this->isValidRendererClass($rendererClass))
        {
            throw new InvalidRendererClassException($rendererClass);
        }

        assert(is_string($rendererClass));

        return $rendererClass;
    }

    protected function isValidRendererClass(mixed $rendererClass): bool
    {
        if (! is_string($rendererClass))
        {
            return false;
        }

        if (! class_exists($rendererClass))
        {
            return false;
        }

        $reflection = new ReflectionClass($rendererClass);

        if (! $reflection->hasMethod('toHtml'))
        {
            return false;
        }

        if (! $reflection->hasMethod('commonmarkOptions'))
        {
            return false;
        }

        $toHtmlMethod  = $reflection->getMethod('toHtml');
        $optionsMethod = $reflection->getMethod('commonmarkOptions');

        if (! $toHtmlMethod->isPublic() || ! $optionsMethod->isPublic())
        {
            return false;
        }

        return count($toHtmlMethod->getParameters())  === 1
            && count($optionsMethod->getParameters()) === 1;
    }
}
