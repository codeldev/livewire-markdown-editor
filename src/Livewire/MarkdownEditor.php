<?php

declare(strict_types=1);

namespace Codeldev\LivewireMarkdownEditor\Livewire;

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
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

final class MarkdownEditor extends Component
{
    public ?string $dispatcher = null;

    public string $activeTab = 'write';

    public string $markdown = '';

    public ?string $key = null;

    public string $previewHtml = '';

    /** @throws Exception */
    public function mount(): void
    {
        $this
            ->checkStorageDiskIsSet()
            ->validateUploadActionClass()
            ->validatePreviewActionClass()
            ->setDispatcher()
            ->setKey();
    }

    public function render(): View
    {
        return view('livewire-markdown-editor::livewire.markdown-editor');
    }

    public function updateMarkdown(string $value): void
    {
        $this->dispatch($this->dispatcher, content: $value);
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab   = $tab;
        $this->previewHtml = '';

        if ($tab === 'preview')
        {
            $this->generatePreviewHtmlContent();
        }
    }

    /** @return array<string, bool|string|null> */
    public function uploadBase64Image(string $imageData, string $fileName, string $mimeType): array
    {
        try
        {
            $actionClass = config('livewire-markdown-editor.image.action');

            if (! is_string($actionClass))
            {
                $actionClass = UploadImageAction::class;
            }

            $actionInstance = app()->make($actionClass);

            if (! $actionInstance instanceof UploadImageInterface)
            {
                throw new UploadActionImplementationException($actionClass);
            }

            return $actionInstance->handle($imageData, $fileName, $mimeType);
        }
        catch (Exception $e)
        {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'path'    => null,
            ];
        }
    }

    private function generatePreviewHtmlContent(): void
    {
        if ($this->markdown === '' || $this->markdown === '0')
        {
            return;
        }

        try
        {
            $previewClass = config('livewire-markdown-editor.image.preview');

            if (! is_string($previewClass))
            {
                $previewClass = PreviewHtmlAction::class;
            }

            $previewInstance = app($previewClass);

            if (! $previewInstance instanceof PreviewHtmlInterface)
            {
                throw new PreviewActionImplementationException($previewClass);
            }

            $this->previewHtml = $previewInstance->handle($this->markdown);
        }
        catch (Exception $e)
        {
            report($e);
        }
    }

    /** @throws Exception */
    private function validateUploadActionClass(): self
    {
        $actionClass = config('livewire-markdown-editor.image.action');

        if (! is_string($actionClass) || ! class_exists($actionClass))
        {
            throw new InvalidUploadActionClassException($actionClass);
        }

        if (! is_subclass_of($actionClass, UploadImageInterface::class))
        {
            throw new UploadActionImplementationException($actionClass);
        }

        return $this;
    }

    /** @throws Exception */
    private function validatePreviewActionClass(): self
    {
        $actionClass = config('livewire-markdown-editor.image.preview');

        if (! is_string($actionClass) || ! class_exists($actionClass))
        {
            throw new InvalidPreviewActionClassException($actionClass);
        }

        if (! is_subclass_of($actionClass, PreviewHtmlInterface::class))
        {
            throw new PreviewActionImplementationException($actionClass);
        }

        return $this;
    }

    /** @throws Exception */
    private function checkStorageDiskIsSet(): self
    {
        $disk  = config('livewire-markdown-editor.image.disk');
        $disks = config('filesystems.disks', []);

        if (empty($disk))
        {
            throw new NoStorageDiskSetException;
        }

        $diskString = is_string($disk)
            ? $disk
            : 'public';

        $disksArray = is_array($disks)
            ? $disks
            : [];

        if (! array_key_exists($diskString, $disksArray))
        {
            throw new InvalidStorageDiskException($diskString);
        }

        return $this;
    }

    private function setDispatcher(): self
    {
        if ($this->dispatcher === null || $this->dispatcher === '' || $this->dispatcher === '0')
        {
            $dispatcherValue = config('livewire-markdown-editor.editor.dispatcher', 'markdown-editor');

            $this->dispatcher = is_string($dispatcherValue)
                ? $dispatcherValue
                : 'markdown-editor';
        }

        return $this;
    }

    private function setKey(): void
    {
        if ($this->key === null || $this->key === '' || $this->key === '0')
        {
            $this->key = Str::random(32);
        }
    }
}
