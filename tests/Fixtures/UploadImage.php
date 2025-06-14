<?php

declare(strict_types=1);

namespace Codeldev\LivewireMarkdownEditor\Tests\Fixtures;

use Codeldev\LivewireMarkdownEditor\Abstracts\UploadImageAbstract;

class UploadImage extends UploadImageAbstract
{
    public function imageHasValidMimeType(string $mimeType): bool
    {
        return parent::imageHasValidMimeType($mimeType);
    }

    /** @return array<string, string> */
    public function getAllowableFileTypes(): array
    {
        return parent::getAllowableFileTypes();
    }

    public function getDecodedImage(string $mimeType, string $imageData): string
    {
        return parent::getDecodedImage($mimeType, $imageData);
    }

    /** @return array<string, bool|string|null> */
    public function imageUploadResponse(string $message, bool $success = false, ?string $imagePath = null): array
    {
        return parent::imageUploadResponse($message, $success, $imagePath);
    }

    public function imageHasValidSize(string $decodedImage): bool
    {
        return parent::imageHasValidSize($decodedImage);
    }

    public function getMaximumUploadSize(): int
    {
        return parent::getMaximumUploadSize();
    }

    public function getImageFileExtension(string $mimeType): string
    {
        return parent::getImageFileExtension($mimeType);
    }
}
