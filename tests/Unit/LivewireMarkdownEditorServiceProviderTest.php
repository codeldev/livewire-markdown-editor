<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Codeldev\LivewireMarkdownEditor\LivewireMarkdownEditorServiceProvider;
use Illuminate\Support\Facades\Config;

describe('LivewireMarkdownEditorServiceProvider', function (): void
{
    beforeEach(function (): void
    {
        Config::set('markdown');
        Config::set('livewire-markdown-editor');

        $this->provider = new LivewireMarkdownEditorServiceProvider(app());
    });

    describe('extendMarkdownConfig', function (): void
    {
        it('does nothing when markdown config is not set', function (): void
        {
            Config::set('markdown');
            Config::set('livewire-markdown-editor', ['some' => 'value']);

            $method = new ReflectionMethod($this->provider, 'extendMarkdownConfig');
            $method->setAccessible(true);
            $method->invoke($this->provider);

            expect(config('markdown'))
                ->toBeNull();
        });

        it('does nothing when livewire-markdown-editor config is not set', function (): void
        {
            Config::set('markdown', ['some' => 'value']);
            Config::set('livewire-markdown-editor');

            $method = new ReflectionMethod($this->provider, 'extendMarkdownConfig');
            $method->setAccessible(true);
            $method->invoke($this->provider);

            expect(config('markdown'))
                ->toBe(['some' => 'value']);
        });

        it('does nothing when markdown config is not an array', function (): void
        {
            Config::set('markdown', 'not-an-array');
            Config::set('livewire-markdown-editor', ['some' => 'value']);

            $method = new ReflectionMethod($this->provider, 'extendMarkdownConfig');
            $method->setAccessible(true);
            $method->invoke($this->provider);

            expect(config('markdown'))
                ->toBe('not-an-array');
        });

        it('does nothing when livewire-markdown-editor config is not an array', function (): void
        {
            Config::set('markdown', ['some' => 'value']);
            Config::set('livewire-markdown-editor', 'not-an-array');

            $method = new ReflectionMethod($this->provider, 'extendMarkdownConfig');
            $method->setAccessible(true);
            $method->invoke($this->provider);

            expect(config('markdown'))
                ->toBe(['some' => 'value']);
        });

        it('calls mergeExtensions and mergeOptions when configs are valid', function (): void
        {
            Config::set('markdown', [
                'extensions'         => ['BaseExt1', 'BaseExt2'],
                'commonmark_options' => ['option1' => 'value1'],
            ]);

            Config::set('livewire-markdown-editor', [
                'commonmark' => [
                    'extensions' => ['EditorExt1', 'EditorExt2'],
                    'options'    => ['option2' => 'value2'],
                ],
            ]);

            $method = new ReflectionMethod($this->provider, 'extendMarkdownConfig');
            $method->setAccessible(true);
            $method->invoke($this->provider);

            expect(config('markdown.extensions'))
                ->toBe(['BaseExt1', 'BaseExt2', 'EditorExt1', 'EditorExt2'])
                ->and(config('markdown.commonmark_options'))
                ->toBe([
                    'option1' => 'value1',
                    'option2' => 'value2',
                ]);
        });
    });

    describe('mergeExtensions', function (): void
    {
        it('does nothing when commonmark config is not set', function (): void
        {
            $baseConfig   = ['some' => 'value'];
            $editorConfig = ['other' => 'value'];

            $method = new ReflectionMethod($this->provider, 'mergeExtensions');
            $method->setAccessible(true);
            $method->invoke($this->provider, $baseConfig, $editorConfig);

            expect(config('markdown.extensions'))
                ->toBeNull();
        });

        it('does nothing when commonmark config is not an array', function (): void
        {
            $baseConfig   = ['some' => 'value'];
            $editorConfig = ['commonmark' => 'not-an-array'];

            $method = new ReflectionMethod($this->provider, 'mergeExtensions');
            $method->setAccessible(true);
            $method->invoke($this->provider, $baseConfig, $editorConfig);

            expect(config('markdown.extensions'))
                ->toBeNull();
        });

        it('does nothing when extensions are not set', function (): void
        {
            $baseConfig   = ['some' => 'value'];
            $editorConfig = ['commonmark' => ['something' => 'else']];

            $method = new ReflectionMethod($this->provider, 'mergeExtensions');
            $method->setAccessible(true);
            $method->invoke($this->provider, $baseConfig, $editorConfig);

            expect(config('markdown.extensions'))
                ->toBeNull();
        });

        it('does nothing when extensions are not an array', function (): void
        {
            $baseConfig   = ['some' => 'value'];
            $editorConfig = ['commonmark' => ['extensions' => 'not-an-array']];

            $method = new ReflectionMethod($this->provider, 'mergeExtensions');
            $method->setAccessible(true);
            $method->invoke($this->provider, $baseConfig, $editorConfig);

            expect(config('markdown.extensions'))
                ->toBeNull();
        });

        it('uses empty array when base extensions are not an array', function (): void
        {
            $method = new ReflectionMethod($this->provider, 'mergeExtensions');
            $method->setAccessible(true);
            $method->invoke($this->provider, [
                'extensions' => 'not-an-array',
            ], [
                'commonmark' => [
                    'extensions' => ['EditorExt1', 'EditorExt2'],
                ],
            ]);

            expect(config('markdown.extensions'))
                ->toBe(['EditorExt1', 'EditorExt2']);
        });

        it('merges and deduplicates extensions correctly', function (): void
        {
            $method = new ReflectionMethod($this->provider, 'mergeExtensions');
            $method->setAccessible(true);
            $method->invoke($this->provider, [
                'extensions' => ['BaseExt1', 'BaseExt2', 'CommonExt'],
            ], [
                'commonmark' => [
                    'extensions' => ['EditorExt1', 'EditorExt2', 'CommonExt'],
                ],
            ]);

            expect(config('markdown.extensions'))->toBe([
                'BaseExt1', 'BaseExt2', 'CommonExt', 'EditorExt1', 'EditorExt2',
            ]);
        });

        it('handles case when base extensions are null', function (): void
        {
            $method = new ReflectionMethod($this->provider, 'mergeExtensions');
            $method->setAccessible(true);
            $method->invoke($this->provider, [
                'some' => 'value',
            ], [
                'commonmark' => [
                    'extensions' => ['EditorExt1', 'EditorExt2'],
                ],
            ]);

            expect(config('markdown.extensions'))
                ->toBe(['EditorExt1', 'EditorExt2']);
        });
    });

    describe('mergeOptions', function (): void
    {
        it('does nothing when commonmark config is not set', function (): void
        {
            $method = new ReflectionMethod($this->provider, 'mergeOptions');
            $method->setAccessible(true);
            $method->invoke($this->provider, [
                'some' => 'value',
            ], [
                'other' => 'value',
            ]);

            expect(config('markdown.commonmark_options'))
                ->toBeNull();
        });

        it('does nothing when commonmark config is not an array', function (): void
        {
            $method = new ReflectionMethod($this->provider, 'mergeOptions');
            $method->setAccessible(true);
            $method->invoke($this->provider, [
                'some' => 'value',
            ], [
                'commonmark' => 'not-an-array',
            ]);

            expect(config('markdown.commonmark_options'))
                ->toBeNull();
        });

        it('does nothing when options are not set', function (): void
        {
            $method = new ReflectionMethod($this->provider, 'mergeOptions');
            $method->setAccessible(true);
            $method->invoke($this->provider, [
                'some' => 'value',
            ], [
                'commonmark' => ['something' => 'else'],
            ]);

            expect(config('markdown.commonmark_options'))
                ->toBeNull();
        });

        it('does nothing when options are not an array', function (): void
        {
            $method = new ReflectionMethod($this->provider, 'mergeOptions');
            $method->setAccessible(true);
            $method->invoke($this->provider, [
                'some' => 'value',
            ], [
                'commonmark' => ['options' => 'not-an-array'],
            ]);

            expect(config('markdown.commonmark_options'))
                ->toBeNull();
        });

        it('uses empty array when base options are not an array', function (): void
        {
            $method = new ReflectionMethod($this->provider, 'mergeOptions');
            $method->setAccessible(true);
            $method->invoke($this->provider, [
                'commonmark_options' => 'not-an-array',
            ], [
                'commonmark' => [
                    'options' => ['option1' => 'value1', 'option2' => 'value2'],
                ],
            ]);

            expect(config('markdown.commonmark_options'))->toBe([
                'option1' => 'value1',
                'option2' => 'value2',
            ]);
        });

        it('merges options correctly', function (): void
        {
            $method = new ReflectionMethod($this->provider, 'mergeOptions');
            $method->setAccessible(true);
            $method->invoke($this->provider, [
                'commonmark_options' => [
                    'option1'     => 'base_value1',
                    'unique_base' => 'base_unique',
                ]], [
                    'commonmark' => [
                        'options' => [
                            'option1'       => 'editor_value1',
                            'unique_editor' => 'editor_unique',
                        ],
                    ],
                ]);

            expect(config('markdown.commonmark_options'))->toBe([
                'option1'       => ['base_value1', 'editor_value1'],
                'unique_base'   => 'base_unique',
                'unique_editor' => 'editor_unique',
            ]);
        });

        it('handles nested arrays correctly', function (): void
        {
            $method = new ReflectionMethod($this->provider, 'mergeOptions');
            $method->setAccessible(true);
            $method->invoke($this->provider, [
                'commonmark_options' => [
                    'renderer' => [
                        'block_separator' => "\n",
                        'inner_separator' => "\n",
                    ],
                ],
            ], [
                'commonmark' => [
                    'options' => [
                        'renderer' => [
                            'block_separator' => "\n\n",
                            'new_option'      => 'value',
                        ],
                    ],
                ],
            ]);

            expect(config('markdown.commonmark_options'))->toBe([
                'renderer' => [
                    'block_separator' => ["\n", "\n\n"],
                    'inner_separator' => "\n",
                    'new_option'      => 'value',
                ],
            ]);
        });

        it('handles case when base options are null', function (): void
        {
            $method = new ReflectionMethod($this->provider, 'mergeOptions');
            $method->setAccessible(true);
            $method->invoke($this->provider, [
                'some' => 'value',
            ], [
                'commonmark' => [
                    'options' => ['option1' => 'value1', 'option2' => 'value2'],
                ],
            ]);

            expect(config('markdown.commonmark_options'))->toBe([
                'option1' => 'value1',
                'option2' => 'value2',
            ]);
        });
    });

    it('returns early when config has method returns false', function (): void
    {
        Config::partialMock()
            ->shouldReceive('has')
            ->once()
            ->with('markdown')
            ->andReturn(false);

        Config::set('livewire-markdown-editor', [
            'commonmark' => [
                'extensions' => ['SomeExtension'],
                'options'    => ['some' => 'option'],
            ],
        ]);

        $method = new ReflectionMethod($this->provider, 'extendMarkdownConfig');
        $method->setAccessible(true);
        $method->invoke($this->provider);

        expect(config('markdown.extensions'))
            ->toBeNull()
            ->and(config('markdown.commonmark_options'))
            ->toBeNull();
    });
});
