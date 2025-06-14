<?php

declare(strict_types=1);

namespace Codeldev\LivewireMarkdownEditor\Abstracts;

abstract class UploadImageAbstract
{
    protected function imageHasValidMimeType(string $mimeType): bool
    {
        return array_key_exists($mimeType, $this->getAllowableFileTypes());
    }

    /** @return array<string, string> */
    protected function getAllowableFileTypes(): array
    {
        $configTypes = config('markdown-editor.image.file_types');

        $defaultTypes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
        ];

        if (! is_array($configTypes))
        {
            return $defaultTypes;
        }

        $result = [];

        foreach ($configTypes as $mimeType => $extension)
        {
            if (is_string($mimeType) && is_string($extension))
            {
                $result[$mimeType] = $extension;
            }
        }

        return $result === []
            ? $defaultTypes
            : $result;
    }

    protected function getDecodedImage(string $mimeType, string $imageData): string
    {
        $decodedImage = base64_decode(str_replace(["data:{$mimeType};base64,", ' '], ['', '+'], $imageData), true);

        return $decodedImage === false
            ? ''
            : $decodedImage;
    }

    /** @return array<string, bool|string|null> */
    protected function imageUploadResponse(string $message, bool $success = false, ?string $imagePath = null): array
    {
        return [
            'success' => $success,
            'message' => $message,
            'path'    => $imagePath,
        ];
    }

    protected function imageHasValidSize(string $decodedImage): bool
    {
        return mb_strlen($decodedImage) <= ($this->getMaximumUploadSize() * 1024);
    }

    protected function getMaximumUploadSize(): int
    {
        $configSize = config('markdown-editor.image.max_size', 5000);

        if (is_int($configSize))
        {
            return $configSize;
        }

        if (is_numeric($configSize))
        {
            return (int) $configSize;
        }

        return 5000;
    }

    protected function getImageFileExtension(string $mimeType): string
    {
        $extensions = $this->getAllowableFileTypes();

        return $extensions[$mimeType] ?? 'jpg';
    }
}
