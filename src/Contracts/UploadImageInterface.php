<?php

declare(strict_types=1);

namespace Codeldev\LivewireMarkdownEditor\Contracts;

use Exception;

interface UploadImageInterface
{
    /** @return array<string, bool|string|null> */
    public function handle(
        string $imageData,
        string $fileName,
        string $mimeType
    ): array;

    /** @throws Exception */
    public function storeFileAndReturnPath(
        string $mimeType,
        string $fileName,
        string $decodedImage
    ): string;

    public function buildImagePath(
        string $mimeType,
        string $fileName
    ): string;
}
