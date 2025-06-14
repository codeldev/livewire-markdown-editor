<?php

/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Codeldev\LivewireMarkdownEditor\Tests\Fixtures\UploadImage;

describe('UploadImageAbstract', function (): void
{
    beforeEach(function (): void
    {
        $this->uploadImage = new UploadImage;
    });

    it('validates mime types correctly', function (): void
    {
        expect($this->uploadImage->imageHasValidMimeType('image/jpeg'))
            ->toBeTrue()
            ->and($this->uploadImage->imageHasValidMimeType('image/png'))
            ->toBeTrue()
            ->and($this->uploadImage->imageHasValidMimeType('image/gif'))
            ->toBeTrue()
            ->and($this->uploadImage->imageHasValidMimeType('image/webp'))
            ->toBeTrue()
            ->and($this->uploadImage->imageHasValidMimeType('image/invalid'))
            ->toBeFalse()
            ->and($this->uploadImage->imageHasValidMimeType('application/pdf'))
            ->toBeFalse()
            ->and($this->uploadImage->imageHasValidMimeType('text/plain'))
            ->toBeFalse();
    });

    it('returns default file types when config is not an array', function (): void
    {
        config()->set('markdown-editor.image.file_types', 'not-an-array');

        expect($this->uploadImage->getAllowableFileTypes())->toBe([
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
        ]);
    });

    it('returns config file types when valid', function (): void
    {
        $configTypes = [
            'image/jpeg'   => 'jpg',
            'image/png'    => 'png',
            'image/custom' => 'custom',
        ];

        config()->set('markdown-editor.image.file_types', $configTypes);

        expect($this->uploadImage->getAllowableFileTypes())
            ->toBe($configTypes);
    });

    it('filters non-string keys and values from config file types', function (): void
    {
        config()->set('markdown-editor.image.file_types', [
            'image/jpeg'    => 'jpg',
            'image/png'     => 'png',
            123             => 'invalid',
            'image/invalid' => 456,
            'image/null'    => null,
        ]);

        expect($this->uploadImage->getAllowableFileTypes())->toBe([
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
        ]);
    });

    it('returns default file types when filtered result is empty', function (): void
    {
        config()->set('markdown-editor.image.file_types', [
            123             => 'invalid',
            'image/invalid' => 456,
            'image/null'    => null,
        ]);

        expect($this->uploadImage->getAllowableFileTypes())->toBe([
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
        ]);
    });

    it('decodes base64 image data correctly', function (): void
    {
        expect($this->uploadImage->getDecodedImage('image/png', 'data:image/png;base64,dGVzdA=='))
            ->toBe('test');
    });

    it('returns empty string for invalid base64', function (): void
    {
        expect($this->uploadImage->getDecodedImage('image/png', 'data:image/png;base64,invalid!@#'))
            ->toBe('');
    });

    it('returns correct image upload response structure', function (): void
    {
        expect($this->uploadImage->imageUploadResponse('Test message', true, '/path/to/image.jpg'))->toBe([
            'success' => true,
            'message' => 'Test message',
            'path'    => '/path/to/image.jpg',
        ]);
    });

    it('handles null path in image upload response', function (): void
    {
        expect($this->uploadImage->imageUploadResponse('Error message', false))->toBe([
            'success' => false,
            'message' => 'Error message',
            'path'    => null,
        ]);
    });

    it('validates image size correctly for small images', function (): void
    {
        $uploadImageMock = Mockery::mock(UploadImage::class)
            ->makePartial();

        $uploadImageMock->shouldReceive('getMaximumUploadSize')
            ->andReturn(10);

        expect($uploadImageMock->imageHasValidSize(str_repeat('a', 5 * 1024)))
            ->toBeTrue();
    });

    it('validates image size correctly for large images', function (): void
    {
        $uploadImageMock = Mockery::mock(UploadImage::class)
            ->makePartial();

        $uploadImageMock->shouldReceive('getMaximumUploadSize')
            ->andReturn(10);

        expect($uploadImageMock->imageHasValidSize(str_repeat('a', 15 * 1024)))
            ->toBeFalse();
    });

    it('returns config value for maximum upload size when int', function (): void
    {
        config()->set('markdown-editor.image.max_size', 2000);

        expect($this->uploadImage->getMaximumUploadSize())->toBe(2000);
    });

    it('casts numeric strings to int for maximum upload size', function (): void
    {
        config()->set('markdown-editor.image.max_size', '3000');

        expect($this->uploadImage->getMaximumUploadSize())
            ->toBe(3000);
    });

    it('returns default for non-numeric maximum upload size values', function (): void
    {
        config()->set('markdown-editor.image.max_size', 'not-a-number');

        expect($this->uploadImage->getMaximumUploadSize())
            ->toBe(5000);
    });

    it('returns correct file extension for mime type', function (): void
    {
        expect($this->uploadImage->getImageFileExtension('image/jpeg'))
            ->toBe('jpg')
            ->and($this->uploadImage->getImageFileExtension('image/png'))
            ->toBe('png')
            ->and($this->uploadImage->getImageFileExtension('image/gif'))
            ->toBe('gif')
            ->and($this->uploadImage->getImageFileExtension('image/webp'))
            ->toBe('webp');
    });

    it('returns jpg for unknown mime type', function (): void
    {
        expect($this->uploadImage->getImageFileExtension('image/unknown'))
            ->toBe('jpg');
    });

    it('uses custom extensions from config', function (): void
    {
        config()->set('markdown-editor.image.file_types', [
            'image/jpeg'   => 'jpeg',
            'image/custom' => 'custom',
        ]);

        expect($this->uploadImage->getImageFileExtension('image/jpeg'))
            ->toBe('jpeg')
            ->and($this->uploadImage->getImageFileExtension('image/custom'))
            ->toBe('custom')
            ->and($this->uploadImage->getImageFileExtension('image/unknown'))
            ->toBe('jpg');
    });
});
