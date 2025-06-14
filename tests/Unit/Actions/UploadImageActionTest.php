<?php

/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Codeldev\LivewireMarkdownEditor\Actions\UploadImageAction;
use Codeldev\LivewireMarkdownEditor\Contracts\UploadImageInterface;
use Codeldev\LivewireMarkdownEditor\Exceptions\FailedToStoreImageException;
use Illuminate\Support\Facades\Storage;

describe('UploadImageAction', function (): void
{
    beforeEach(function (): void
    {
        $this->uploadAction = new UploadImageAction;

        Storage::fake('public');

        $this->validImageData   = 'data:image/png;base64,' . base64_encode('test-image-content');
        $this->invalidImageData = 'data:image/png;base64,invalid-base64!';
        $this->fileName         = 'test-image.png';
        $this->validMimeType    = 'image/png';
        $this->invalidMimeType  = 'application/pdf';

        Config::set([
            'markdown-editor.storage.disk'     => 'public',
            'markdown-editor.image.path'       => 'test-path/:file',
            'markdown-editor.image.max_size'   => 5000,
            'markdown-editor.image.file_types' => [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif',
                'image/webp' => 'webp',
            ],
        ]);
    });

    it('implements UploadImageInterface', function (): void
    {
        expect($this->uploadAction)->toBeInstanceOf(UploadImageInterface::class);
    });

    it('returns error response for invalid mime type', function (): void
    {
        $response = $this->uploadAction->handle(
            $this->validImageData,
            $this->fileName,
            $this->invalidMimeType
        );

        expect($response)
            ->toBeArray()
            ->toHaveKey('success', false)
            ->toHaveKey('message')
            ->toHaveKey('path', null);
    });

    it('returns error response for invalid base64 data', function (): void
    {
        $response = $this->uploadAction->handle(
            $this->invalidImageData,
            $this->fileName,
            $this->validMimeType
        );

        expect($response)
            ->toBeArray()
            ->toHaveKey('success', false)
            ->toHaveKey('message')
            ->toHaveKey('path', null);
    });

    it('returns error response for oversized image', function (): void
    {
        config()->set('markdown-editor.image.max_size', 0);

        $response = $this->uploadAction->handle(
            $this->validImageData,
            $this->fileName,
            $this->validMimeType
        );

        expect($response)
            ->toBeArray()
            ->toHaveKey('success', false)
            ->toHaveKey('message')
            ->toHaveKey('path', null);
    });

    it('returns success response for valid image upload', function (): void
    {
        $response = $this->uploadAction->handle(
            $this->validImageData,
            $this->fileName,
            $this->validMimeType
        );

        expect($response)
            ->toBeArray()
            ->toHaveKey('success', true)
            ->toHaveKey('message')
            ->toHaveKey('path')
            ->and($response['path'])
            ->toStartWith('/storage/test-path/');
    });

    it('stores file in the configured disk', function (): void
    {
        config()->set('markdown-editor.storage.disk', 'custom-disk');
        Storage::fake('custom-disk');

        $path = $this->uploadAction->storeFileAndReturnPath(
            $this->validMimeType,
            $this->fileName,
            'test-image-content'
        );

        $storagePath = str_replace('/storage/', '', $path);

        Storage::disk('custom-disk')
            ->assertExists($storagePath);

        Storage::disk('public')
            ->assertMissing($storagePath);

        expect($path)
            ->toStartWith('/storage/')
            ->toContain('test-path/')
            ->toContain('test-image');
    });

    it('falls back to public disk when config is invalid', function (): void
    {
        config()->set('markdown-editor.storage.disk');

        $path = $this->uploadAction->storeFileAndReturnPath(
            $this->validMimeType,
            $this->fileName,
            'test-image-content'
        );

        Storage::disk('public')
            ->assertExists(str_replace('/storage/', '', $path));

        expect($path)
            ->toStartWith('/storage/');
    });

    it('throws exception when file storage fails', function (): void
    {
        $fakeDisk = Mockery::mock();
        $fakeDisk->shouldReceive('put')->once();
        $fakeDisk->shouldReceive('exists')->andReturn(false);

        Storage::shouldReceive('disk')
            ->with('public')
            ->andReturn($fakeDisk);

        expect(fn () => $this->uploadAction->storeFileAndReturnPath(
            $this->validMimeType,
            $this->fileName,
            'test-image-content'
        ))->toThrow(FailedToStoreImageException::class);
    });

    it('builds image path with date, uuid, and filename placeholders', function (): void
    {
        config()->set('markdown-editor.image.path', 'images/:date/:id/:file');

        $date = now()->format('Y-m-d');
        $path = $this->uploadAction->buildImagePath(
            $this->validMimeType,
            'test-image.png'
        );

        expect($path)
            ->toContain('images/')
            ->toContain($date)
            ->toContain('test-image.png')
            ->toMatch("/images\/{$date}\/[0-9a-f-]+\/test-image\.png/");
    });

    it('uses default path template when config value is not a string', function (): void
    {
        config()->set('markdown-editor.image.path', ['invalid', 'array']);

        $date = now()->format('Y-m-d');
        $path = $this->uploadAction->buildImagePath(
            $this->validMimeType,
            'test-image.png'
        );

        expect($path)
            ->toContain('images/')
            ->toContain($date)
            ->toContain('test-image.png')
            ->toMatch("/images\/{$date}\/[0-9a-f-]+\/test-image\.png/");
    });

    it('slugifies filenames with special characters', function (): void
    {
        $path = $this->uploadAction->buildImagePath(
            $this->validMimeType,
            'Test Image With Spaces & Special Chars!.png'
        );

        expect($path)
            ->toContain('test-image-with-spaces-special-chars.png')
            ->not->toContain('Test Image With Spaces & Special Chars!.png');
    });

    it('generates random filename when input is empty', function (): void
    {
        $path = $this->uploadAction->buildImagePath($this->validMimeType, '');

        expect($path)
            ->toContain('test-path/')
            ->toMatch('/\.png$/')
            ->toMatch('/test-path\/[a-zA-Z0-9-]+\.png/');
    });

    it('uses correct file extension based on mime type', function (): void
    {
        $jpgPath = $this->uploadAction->buildImagePath(
            'image/jpeg',
            'test-image'
        );

        $webpPath = $this->uploadAction->buildImagePath(
            'image/webp',
            'test-image'
        );

        expect($jpgPath)
            ->toMatch('/\.jpg$/')
            ->and($webpPath)
            ->toMatch('/\.webp$/');
    });

    it('includes exception message in error response', function (): void
    {
        $fakeDisk = Mockery::mock();
        $fakeDisk->shouldReceive('put')->andThrow(new Exception('Custom error message'));

        Storage::shouldReceive('disk')
            ->with('public')
            ->andReturn($fakeDisk);

        $response = $this->uploadAction->handle(
            $this->validImageData,
            $this->fileName,
            $this->validMimeType
        );

        expect($response)
            ->toBeArray()
            ->toHaveKey('success', false)
            ->toHaveKey('message')
            ->and($response['message'])
            ->not->toBeEmpty();
    });

    it('throws FailedToStoreImageException when file storage fails', function (): void
    {
        $fakeDisk = Mockery::mock();
        $fakeDisk->shouldReceive('put')->once();
        $fakeDisk->shouldReceive('exists')->andReturn(false);

        Storage::shouldReceive('disk')
            ->with('public')
            ->andReturn($fakeDisk);

        expect(fn () => $this->uploadAction->storeFileAndReturnPath(
            $this->validMimeType,
            $this->fileName,
            'test-image-content'
        ))->toThrow(FailedToStoreImageException::class);
    });

    it('uses translation for error messages', function (): void
    {
        $response = $this->uploadAction->handle(
            $this->validImageData,
            $this->fileName,
            $this->invalidMimeType
        );

        expect($response['message'])
            ->toBe(trans('livewire-markdown-editor::uploads.errors.unsupported'));
    });
});
