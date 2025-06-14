<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Codeldev\LivewireMarkdownEditor\Actions\PreviewHtmlAction;
use Codeldev\LivewireMarkdownEditor\Actions\UploadImageAction;
use Codeldev\LivewireMarkdownEditor\Contracts\PreviewHtmlInterface;
use Codeldev\LivewireMarkdownEditor\Contracts\UploadImageInterface;
use Codeldev\LivewireMarkdownEditor\Exceptions\InvalidPreviewActionClassException;
use Codeldev\LivewireMarkdownEditor\Exceptions\InvalidStorageDiskException;
use Codeldev\LivewireMarkdownEditor\Exceptions\InvalidUploadActionClassException;
use Codeldev\LivewireMarkdownEditor\Exceptions\NoStorageDiskSetException;
use Codeldev\LivewireMarkdownEditor\Exceptions\PreviewActionImplementationException;
use Codeldev\LivewireMarkdownEditor\Exceptions\UploadActionImplementationException;
use Codeldev\LivewireMarkdownEditor\Livewire\MarkdownEditor;
use Livewire\Livewire;

describe('MarkdownEditor', function (): void
{
    beforeEach(function ()
    {
        Config::set([
            'app.key'                                => 'base64:' . base64_encode(random_bytes(32)),
            'livewire-markdown-editor.image.action'  => UploadImageAction::class,
            'livewire-markdown-editor.image.preview' => PreviewHtmlAction::class,
            'livewire-markdown-editor.image.disk'    => 'public',
            'filesystems.disks.public'               => [
                'driver' => 'local',
                'root'   => storage_path('app/public'),
            ]
        ]);
    });

    it('can render the component', function (): void
    {
        Livewire::test(MarkdownEditor::class)
            ->assertSet('activeTab', 'write')
            ->assertSet('markdown', '')
            ->assertSet('previewHtml', '')
            ->assertSet('dispatcher', 'markdown-editor')
            ->assertSee('markdownEditor()');
    });

    it('can switch tabs', function (): void
    {
        Livewire::test(MarkdownEditor::class)
            ->assertSet('activeTab', 'write')
            ->call('switchTab', 'preview')
            ->assertSet('activeTab', 'preview');
    });

    it('generates preview HTML when switching to preview tab', function (): void
    {
        $component = Livewire::test(MarkdownEditor::class)
            ->set('markdown', '# Hello World')
            ->call('switchTab', 'preview');

        expect($component->get('previewHtml'))
            ->toContain('<h1 id="hello-world">Hello World</h1>');
    });

    it('does not generate preview HTML when markdown is empty', function (): void
    {
        Livewire::test(MarkdownEditor::class)
            ->set('markdown', '')
            ->call('switchTab', 'preview')
            ->assertSet('previewHtml', '');
    });

    it('dispatches update event when markdown is updated', function (): void
    {
        $markdown = '# Updated Content';

         Livewire::test(MarkdownEditor::class)
            ->call('updateMarkdown', $markdown)
            ->assertDispatched('markdown-editor', fn ($event, $params) =>  $params['content'] === $markdown);
    });

    it('uses configuration fallbacks when not explicitly set', function (): void
    {
        config()->set('livewire-markdown-editor.editor.dispatcher');

        $component = Livewire::test(MarkdownEditor::class)
            ->assertSet('dispatcher', 'markdown-editor')
            ->assertSet('activeTab', 'write');

        expect($component->get('key'))
            ->not->toBeNull();
    });

    it('throws exception for invalid upload action class', function (): void
    {
        config()->set('livewire-markdown-editor.image.action', 'NonExistentClass');

        expect(fn() => (new MarkdownEditor)->mount())
            ->toThrow(InvalidUploadActionClassException::class);
    });

    it('throws exception for invalid preview action class', function (): void
    {
        config()->set(
            'livewire-markdown-editor.image.preview',
            'NonExistentClass'
        );

        expect(fn() => (new MarkdownEditor)->mount())
            ->toThrow(InvalidPreviewActionClassException::class);
    });

    it('throws exception when storage disk is not set', function (): void
    {
        config()->set('livewire-markdown-editor.image.disk');

        expect(fn() => (new MarkdownEditor)->mount())
            ->toThrow(NoStorageDiskSetException::class);
    });

    it('throws exception when storage disk does not exist', function (): void
    {
        config()->set(
            'livewire-markdown-editor.image.disk',
            'nonexistent-disk'
        );

        expect(fn() => (new MarkdownEditor)->mount())
            ->toThrow(InvalidStorageDiskException::class);
    });

    it('clears preview HTML when switching away from preview tab', function (): void
    {
        Livewire::test(MarkdownEditor::class)
            ->set('markdown', '# Test')
            ->call('switchTab', 'preview')
            ->call('switchTab', 'write')
            ->assertSet('previewHtml', '');
    });

    it('handles upload action class fallback when config is not string', function (): void
    {
        config()->set(
            'livewire-markdown-editor.image.action',
            ['not', 'a', 'string']
        );

        $result = (new MarkdownEditor)->uploadBase64Image(
            'data:image/png;base64,iVBORw0KGgoAAAANSUhEUg',
            'test.png',
            'image/png'
        );

        expect($result)->toHaveKey('success');
    });

    it('returns error array when image upload fails', function (): void
    {
        // Mock a failing upload action
        $mockAction = new class implements UploadImageInterface
        {
            public function handle(string $imageData, string $fileName, string $mimeType): array
            {
                throw new RuntimeException('Upload failed');
            }

            public function storeFileAndReturnPath(string $mimeType, string $fileName, string $decodedImage): string
            {
                return '';
            }

            public function buildImagePath(string $mimeType, string $fileName): string
            {
                return '';
            }
        };

        app()->bind(UploadImageAction::class, fn() => $mockAction);

        $component = new MarkdownEditor;
        $result = $component->uploadBase64Image('invalid-data', 'test.png', 'image/png');

        expect($result)
            ->toHaveKey('success', false)
            ->toHaveKey('message', 'Upload failed')
            ->toHaveKey('path', null);
    });

    it('does not generate preview for markdown with only zero string', function (): void
    {
        Livewire::test(MarkdownEditor::class)
            ->set('markdown', '0')
            ->call('switchTab', 'preview')
            ->assertSet('previewHtml', '');
    });

    it('preserves existing dispatcher when already set', function (): void
    {
        Livewire::test(MarkdownEditor::class)
            ->set('dispatcher', 'custom-dispatcher')
            ->assertSet('dispatcher', 'custom-dispatcher');
    });

    it('preserves existing key when already set', function (): void
    {
        $component = new MarkdownEditor;
        $component->key = 'existing-key';

        $reflection = new ReflectionClass($component);
        $method = $reflection->getMethod('setKey');
        $method->setAccessible(true);
        $method->invoke($component);

        expect($component->key)
            ->toBe('existing-key');
    });

    it('uses default upload action class when config is not string', function (): void
    {
        $component = new MarkdownEditor;
        $component->mount();

        config()->set('livewire-markdown-editor.image.action', null);

        app()->bind(UploadImageAction::class, fn() => new class implements UploadImageInterface
        {
            public function handle(string $imageData, string $fileName, string $mimeType): array
            {
                return ['success' => true, 'path' => 'test.png', 'message' => null];
            }

            public function storeFileAndReturnPath(string $mimeType, string $fileName, string $decodedImage): string
            {
                return 'test.png';
            }

            public function buildImagePath(string $mimeType, string $fileName): string
            {
                return 'images/' . $fileName;
            }
        });

        $result = $component->uploadBase64Image('data:image/png;base64,test', 'test.png', 'image/png');

        expect($result)
            ->toHaveKey('success', true)
            ->toHaveKey('path', 'test.png');
    });

    it('uses default preview action class when config is not string', function (): void
    {
        app()->bind(PreviewHtmlAction::class, fn() => new class implements PreviewHtmlInterface
        {
            public function handle(string $markdown): string
            {
                return '<p>Mocked preview</p>';
            }
        });

        $component = Livewire::test(MarkdownEditor::class);

        config()->set('livewire-markdown-editor.image.preview', ['not', 'a', 'string']);

        $component->set('markdown', '# Test')
            ->call('switchTab', 'preview')
            ->assertSet('previewHtml', '<p>Mocked preview</p>');
    });

    it('throws exception when preview action does not implement interface', function (): void
    {
        app()->bind(stdClass::class, fn() => new class implements UploadImageInterface
        {
            public function handle(string $imageData, string $fileName, string $mimeType): array
            {
                return ['success' => true, 'path' => 'test.png', 'message' => null];
            }

            public function storeFileAndReturnPath(string $mimeType, string $fileName, string $decodedImage): string
            {
                return 'test.png';
            }

            public function buildImagePath(string $mimeType, string $fileName): string
            {
                return 'images/' . $fileName;
            }
        });

        config()->set('livewire-markdown-editor.image.preview', stdClass::class);

        expect(fn() => (new MarkdownEditor)->mount())
            ->toThrow(PreviewActionImplementationException::class);
    });

    it('handles exceptions in preview generation gracefully', function (): void
    {
        app()->bind(PreviewHtmlAction::class, fn() => new class implements PreviewHtmlInterface
        {
            public function handle(string $markdown): string
            {
                throw new RuntimeException('Preview generation failed');
            }
        });

        $component = Livewire::test(MarkdownEditor::class)
            ->set('markdown', '# Test');

        config()->set('livewire-markdown-editor.image.preview');

        $component->call('switchTab', 'preview')
            ->assertSet('previewHtml', '');
    });

    it('throws exception when upload action does not implement interface', function (): void
    {
        config()->set('livewire-markdown-editor.image.action', stdClass::class);

        expect(fn() => (new MarkdownEditor)->mount())
            ->toThrow(UploadActionImplementationException::class);
    });

    it('throws preview implementation exception during validation', function (): void
    {
        config()->set('livewire-markdown-editor.image.preview', stdClass::class);

        expect(fn() => (new MarkdownEditor)->mount())
            ->toThrow(PreviewActionImplementationException::class);
    });

    it('uses public disk as fallback when disk config is not string', function (): void
    {
        config()->set('livewire-markdown-editor.image.disk', ['not', 'a', 'string']);

        $component = new MarkdownEditor;
        $component->mount();

        expect(true)
            ->toBeTrue();
    });

    it('uses empty array as fallback when filesystems config is not array', function (): void
    {
        config()->set('filesystems.disks', 'not-an-array');
        config()->set('livewire-markdown-editor.image.disk', 'nonexistent');

        expect(fn() => (new MarkdownEditor)->mount())
            ->toThrow(InvalidStorageDiskException::class);
    });

    it('preview html is empty when previewHtml class exists but does not implement interface', function (): void
    {
        $component = new MarkdownEditor;
        $component->mount();
        $component->markdown = '# Test';

        Config::set('livewire-markdown-editor.image.preview');

        app()->bind(PreviewHtmlAction::class, fn() => new class {});

        $reflection = new ReflectionClass($component);
        $method = $reflection->getMethod('generatePreviewHtmlContent');
        $method->setAccessible(true);
        $method->invoke($component);

        expect($component->previewHtml)
            ->toBeEmpty();
    });

    it('throws UploadActionImplementationException when class does not implement interface', function (): void
    {
        $component = new MarkdownEditor;
        $component->mount();

        Config::set('livewire-markdown-editor.image.action');

        app()->bind(UploadImageAction::class, fn() => new class {});

        $result = $component->uploadBase64Image(
            'data:image/png;base64,test',
            'test.png',
            'image/png'
        );

        expect($result)
            ->toHaveKey('success', false)
            ->toHaveKey('message')
            ->toHaveKey('path', null);
    });
});
