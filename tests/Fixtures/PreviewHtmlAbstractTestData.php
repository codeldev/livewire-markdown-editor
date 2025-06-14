<?php

namespace Codeldev\LivewireMarkdownEditor\Tests\Fixtures;

use Codeldev\LivewireMarkdownEditor\Abstracts\PreviewHtmlAbstract;
use Codeldev\LivewireMarkdownEditor\Exceptions\InvalidRendererClassException;
use RuntimeException;
use stdClass;

class PreviewHtmlAbstractTestData
{
    public static function getInvalidRendererClass(): object
    {
        return new class
        {
            private function toHtml(string $markdown): string
            {
                return '<p>' . $markdown . '</p>';
            }
        };
    }

    public static function getInvalidRendererClassB(): object
    {
        return new class
        {
            public function toHtml(string $markdown, array $options): string
            {
                return '<p>' . $markdown . '</p>';
            }
        };
    }

    public static function getInvalidRendererClassC(): object
    {
        return new class
        {
            public function toHtml(): string
            {
                return '<p>test</p>';
            }
        };
    }

    public static function getInvalidRendererClassD(): object
    {
        return new class
        {
            public function render(string $markdown): string
            {
                return '<p>' . $markdown . '</p>';
            }
        };
    }

    public static function getInvalidRendererClassE(): object
    {
        return new class
        {
            public function render(string $markdown): string
            {
                return '<p>' . $markdown . '</p>';
            }
        };
    }

    public static function getNonStringInputs(): array
    {
        return [
            'integer'       => 123,
            'null'          => null,
            'array'         => [],
            'boolean true'  => true,
            'boolean false' => false,
            'object'        => new stdClass
        ];
    }

    public static function getInvalidRendererClasses(): array
    {
        return [
            'non-existent class'   => 'NonExistentClass',
            'class without toHtml' => get_class(new class
            {
                public function render(string $markdown): string
                {
                    return '<p>' . $markdown . '</p>';
                }
            }),
            'class with private toHtml' => get_class(new class
            {
                private function toHtml(string $markdown): string
                {
                    return '<p>' . $markdown . '</p>';
                }
            }),
            'class with wrong parameters (2)' => get_class(new class
            {
                public function toHtml(string $markdown, array $options): string
                {
                    return '<p>' . $markdown . '</p>';
                }
            }),
            'class with no parameters' => get_class(new class
            {
                public function toHtml(): string
                {
                    return '<p>test</p>';
                }
            }),
        ];
    }

    public static function rendererClassB(): object
    {
        return new class
        {
            public function toHtml(string $markdown): string
            {
                return '<p>' . $markdown . '</p>';
            }
        };
    }

    public static function rendererClassC(): object
    {
        return new class
        {
            public function toHtml(string $markdown): array
            {
                return ['html' => '<p>' . $markdown . '</p>'];
            }

            public function commonmarkOptions(array $options): self
            {
                return $this;
            }
        };
    }

    public static function rendererClassD(): object
    {
        return new class
        {
            public function toHtml(string $markdown): string
            {
                throw new RuntimeException('Renderer failed');
            }

            public function commonmarkOptions(array $options): self
            {
                return $this;
            }
        };
    }

    public static function testPreviewHtml(): object
    {
        return new class extends PreviewHtmlAbstract
        {
            public function convertPublic(string $markdown, ?string $customRendererClass = null): string
            {
                return $this->convert($markdown, $customRendererClass);
            }

            public function validateAndGetCustomRendererPublic(string $customRendererClass): string
            {
                return $this->validateAndGetCustomRenderer($customRendererClass);
            }

            public function getRendererClassPublic(): string
            {
                return $this->getRendererClass();
            }

            public function isValidRendererClassPublic(mixed $rendererClass): bool
            {
                return $this->isValidRendererClass($rendererClass);
            }

            public function useBasicMarkdownPublic(string $markdown): string
            {
                return $this->useBasicMarkdown($markdown);
            }
        };
    }

    public static function validRendererClass(): object
    {
        return new class
        {
            public function toHtml(string $markdown): string
            {
                return '<p>' . $markdown . '</p>';
            }

            public function commonmarkOptions(array $options): self
            {
                return $this;
            }
        };
    }

    public static function getInvalidRendererClassWithPrivateCommonmarkOptions(): object
    {
        return new class
        {
            public function toHtml(string $markdown): string
            {
                return '<p>' . $markdown . '</p>';
            }

            private function commonmarkOptions(array $options): self
            {
                return $this;
            }
        };
    }
}
