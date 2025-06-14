<?php

declare(strict_types=1);

namespace Codeldev\LivewireMarkdownEditor\Actions;

use Codeldev\LivewireMarkdownEditor\Abstracts\UploadImageAbstract;
use Codeldev\LivewireMarkdownEditor\Contracts\UploadImageInterface;
use Codeldev\LivewireMarkdownEditor\Exceptions\FailedToStoreImageException;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class UploadImageAction extends UploadImageAbstract implements UploadImageInterface
{
    /** @return array<string, bool|string|null> */
    public function handle(string $imageData, string $fileName, string $mimeType): array
    {
        try
        {
            if (! $this->imageHasValidMimeType(mimeType: $mimeType))
            {
                return $this->imageUploadResponse(
                    trans('livewire-markdown-editor::uploads.errors.unsupported')
                );
            }

            $decodedImage = $this->getDecodedImage($mimeType, $imageData);

            if ($decodedImage === '' || $decodedImage === '0')
            {
                return $this->imageUploadResponse(
                    trans('livewire-markdown-editor::uploads.errors.base64')
                );
            }

            if (! $this->imageHasValidSize($decodedImage))
            {
                return $this->imageUploadResponse(trans(
                    'livewire-markdown-editor::uploads.errors.filesize',
                    ['size' => $this->getMaximumUploadSize()]
                ));
            }

            $filePath = $this->storeFileAndReturnPath($mimeType, $fileName, $decodedImage);

            return $this->imageUploadResponse(
                trans('livewire-markdown-editor::uploads.success'),
                true,
                $filePath
            );
        }
        catch (Exception $e)
        {
            return $this->imageUploadResponse(trans(
                'livewire-markdown-editor::uploads.errors.exception',
                ['error' => $e->getMessage()]
            ));
        }
    }

    public function storeFileAndReturnPath(string $mimeType, string $fileName, string $decodedImage): string
    {
        $diskName = config(
            key    : 'markdown-editor.storage.disk',
            default: 'public'
        );

        $disk = Storage::disk(
            name: is_string($diskName)
            ? $diskName
            : 'public'
        );

        $path = $this->buildImagePath(
            mimeType: $mimeType,
            fileName: $fileName,
        );

        $disk->put(path: $path, contents: $decodedImage);

        if (! $disk->exists(path: $path))
        {
            throw new FailedToStoreImageException;
        }

        return "/storage/{$path}";
    }

    public function buildImagePath(string $mimeType, string $fileName): string
    {
        $baseName = Str::slug(title: pathinfo(
            path: $fileName,
            flags: PATHINFO_FILENAME
        ));

        if (empty($baseName))
        {
            $baseName = Str::random(10);
        }

        $extension = $this->getImageFileExtension(
            mimeType: $mimeType
        );

        $imagePath = config(
            key    : 'markdown-editor.image.path',
            default: 'images/:date/:id/:file'
        );

        $imagePathString = is_string($imagePath)
            ? $imagePath
            : 'images/:date/:id/:file';

        $dateFormatted = now()->format(format: 'Y-m-d');
        $imageUuid     = (string) Str::uuid();

        return trans(key: $imagePathString, replace: [
            'date' => $dateFormatted,
            'id'   => $imageUuid,
            'file' => "{$baseName}.{$extension}",
        ]);
    }
}
