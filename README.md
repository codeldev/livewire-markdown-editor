# Livewire Markdown Editor

[![Latest Version on Packagist](https://img.shields.io/packagist/v/codeldev/livewire-markdown-editor.svg?style=flat-square)](https://packagist.org/packages/codeldev/livewire-markdown-editor)
[![GitHub Pest Test Action Status](https://github.com/codeldev/livewire-markdown-editor/actions/workflows/tests.yml/badge.svg)](https://github.com/codeldev/livewire-markdown-editor/actions?query=workflow%3Atests+branch%3Amaster)
[![GitHub PHP Stan Action Status](https://github.com/codeldev/livewire-markdown-editor/actions/workflows/phpstan.yml/badge.svg)](https://github.com/codeldev/livewire-markdown-editor/actions?query=workflow%3Aphpstan+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/codeldev/livewire-markdown-editor.svg?style=flat-square)](https://packagist.org/packages/codeldev/livewire-markdown-editor)

A clean, simple to use markdown editor that you can drop into any other livewire component.

**Pest Tests:** 100% Code Coverage | **PHP Stan**: Level Max

⚠️ ** IN ACTIVE DEVELOPMENT. USE AT YOUR OWN RISK ** ⚠️

---

## Table of Contents
- [Features Overview](#features-overview)
- [Installation](#installation)
- [Translations](#translations)
- [Usage](#usage)
    - [Set the Dispatcher](#set-the-dispatcher)
    - [Set Initial Value](#set-initial-value)
    - [Set Up Your Parent Component](#set-up-your-parent-component)
- [Using the Editor](#using-the-editor)
    - [Slash Commands](#slash-commands)
    - [Keyboard Shortcuts](#keyboard-shortcuts)
    - [Image Uploads](#image-uploads)
    - [YouTube Embedding](#youtube-embedding)
    - [Html Previews](#html-previews)
- [Advanced Configuration](#advanced-configuration)
- [Component Architecture](#component-architecture)
- [Custom Implementations](#custom-implementations)
- [Troubleshooting](#troubleshooting)
- [Testing](#testing)
- [Changelog](#changelog)
- [Security](#security-vulnerabilities)
- [Credits](#credits)
- [License](#license)

---

## Features Overview

The Codel Markdown Editor comes with a rich set of features:

- **Slash Command Menu** - Press `/` to access formatting options
- **Rich Text Formatting** - Headings, lists, quotes, code blocks, and more
- **Image Uploads** - Drag & drop or file selection with preview
- **YouTube Embedding** - Easily embed YouTube videos
- **Link Management** - Insert and validate links
- **Live Preview** - Toggle between write and preview modes
- **Word & Character Count** - Track your content length
- **Auto-growing Editor** - Expands as you type
- **Responsive Design** - Works on all screen sizes

## Installation

This package has been built for Laravel version 12+ on PHP 8.4 and requires the following dependencies:

- [spatie/laravel-markdown v2](https://github.com/spatie/laravel-markdown)
- [livewire/livewire v3](https://github.com/livewire/livewire)

### Install the package via composer:

```bash
composer require codeldev/livewire-markdown-editor
```
### Install the Tailwind Typography plugin (required for preview styling):

```bash
npm install @tailwindcss/typography
```

### Configure Tailwind to include the package styles. 

Choose one of the following methods

#### Option A: Tailwind 4+ (CSS-based configuration)

Add to your ```app.css``` file:

```css
@import "tailwindcss";
@plugin "@tailwindcss/typography";
@source '../../vendor/codeldev/livewire-markdown-editor/resources/views';
```

#### Option B: Traditional config file approach

Update your ```tailwind.config.js```:

```javascript
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        // Add this line for the markdown editor package
        './vendor/codeldev/livewire-markdown-editor/resources/views/**/*.blade.php',
    ],
    plugins: [
        // Add this line for markdown preview styling
        require('@tailwindcss/typography'),
        // ... your other plugins
    ]
}
```

### Config File
If required, you may publish the config file with:

```bash
php artisan vendor:publish --tag=livewire-markdown-editor-config
```

This is the contents of the published config file:

```php
return [

    /**
     * --------------------------------------------------------------------------
     *  Image Upload Configuration
     * --------------------------------------------------------------------------
     *  Settings for handling image uploads including storage location, file size
     *  limits, and supported file types for the markdown editor.
     */
    'image' => [

        /**
         * --------------------------------------------------------------------------
         *  Storage Disk
         * --------------------------------------------------------------------------
         *  This value is the disk name set in your filesystems config file where
         *  uploaded images will be stored. If no disk is provided the default
         *  will be used. If a disk name is set that does not exist, then an
         *  exception will be thrown
         */
        'disk' => env(key: 'MD_EDITOR_STORAGE_DISK', default: 'public'),

        /**
         * --------------------------------------------------------------------------
         *  Store File As
         * --------------------------------------------------------------------------
         *  This value represents the path within the configured disk where images
         *  will be stored. You may use any of the available attributes passed
         *  to the path string. attributes available are:
         *
         *  [date] - Current date in  Y-m-d format.
         *  [id]   - A randomly generated uuid string
         *  [file] - The file name with the extension included.
         *
         *  Example: images/2025-06-01/f47ac10b-58cc-4372-a567-0e02b2c3d479/photo.jpg
         */
        'path' => env(key: 'MD_EDITOR_STORAGE_PATH', default: 'images/:date/:id/:file'),

        /**
         * --------------------------------------------------------------------------
         *  Max File Upload Size
         * --------------------------------------------------------------------------
         *  This value is the maximum file size that will be allowable for image
         *  uploads processed by the editor. The value should be in kilobytes
         *  which will be converted to bytes. If this value is not set, a value
         *  of 5000 kilobytes will be used as default.
         */
        'max_size' => env(key: 'MD_EDITOR_IMG_MAX_SIZE', default: 5000),

        /**
         * --------------------------------------------------------------------------
         *  Image Extensions and Mime Types
         * --------------------------------------------------------------------------
         *  An array of image file types allowable for upload. Each key:pair value
         *  maps a MIME type to its corresponding file extension for validation
         *  and processing during upload operations.
         */
        'types' => [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
        ],

        /**
         * --------------------------------------------------------------------------
         *  Upload Action Class
         * --------------------------------------------------------------------------
         *  This value specifies the action class responsible for processing image
         *  uploads. The default action handles base64 decoding, validation, file
         *  storage, and path generation. You can create a custom action class to
         *  implement your own upload logic, such as cloud storage integration,
         *  image optimization, or custom validation rules.
         *
         *  Custom action classes must:
         *  - extend: Codeldev\LivewireMarkdownEditor\Abstracts\UploadImageAbstract
         *  - implement: Codeldev\LivewireMarkdownEditor\Contracts\UploadImageInterface
         * 
         *  The handle() method receives the image data, filename, and MIME type, and must
         *  return an array with success status, message, and file path.
         *
         *  Example custom action:
         *  'action' => App\Actions\CustomImageUploadAction::class,
         */
         
        'action' => Codeldev\LivewireMarkdownEditor\Actions\UploadImageAction::class,
        
        /**
         * --------------------------------------------------------------------------
         *  Preview HTML Action Class
         * --------------------------------------------------------------------------
         *  This value specifies the action class responsible for converting markdown
         *  content to HTML for the preview pane. The default action provides basic
         *  markdown parsing with security features like HTML sanitization. You can
         *  create a custom action class to implement advanced markdown features,
         *  custom syntax highlighting, or specialized rendering logic.
         *
         *  Custom action classes must implement:
         *  - Codeldev\LivewireMarkdownEditor\Contracts\PreviewHtmlInterface
         *
         *  The handle() method receives the raw markdown string and must return
         *  the rendered HTML string for display in the preview pane.
         *
         *  Example custom action:
         *  'preview' => App\Actions\CustomPreviewAction::class,
         */
        
        'preview' => Codeldev\LivewireMarkdownEditor\Actions\PreviewHtmlAction::class,
    ],

    /**
     * --------------------------------------------------------------------------
     *  CommonMark Configuration
     * --------------------------------------------------------------------------
     *  These settings extend the base spatie/laravel-markdown configuration by
     *  adding custom extensions and options specific to the markdown editor.
     *  Values defined here will be merged with existing markdown config
     *  without overriding your customizations.
     */

    'commonmark' => [

        /**
         * --------------------------------------------------------------------------
         *  Custom Extensions
         * --------------------------------------------------------------------------
         *  Additional CommonMark extensions to enhance markdown rendering capabilities.
         *  These extensions are automatically merged with any existing extensions
         *  from spatie/laravel-markdown configuration. Each extension adds
         *  specific functionality to the Markdown parser:
         *
         *  - ImageExtension: Enhanced image handling with size attributes and captions
         *  - YouTubeExtension: Converts YouTube URLs to embedded video players
         *  - ExternalLinkExtension: Automatically handles external links with security
         *
         *  To add custom extensions, ensure they implement:
         *  League\CommonMark\Extension\ExtensionInterface
         *
         *  Note: Extension classes must be available in the application's autoloader.
         *  Package-specific extensions are automatically available, but custom
         *  extensions should be placed in your app's namespace.
         */

        'extensions' => [
            Codeldev\LivewireMarkdownEditor\Support\Commonmark\Extensions\YouTubeExtension::class,
            League\CommonMark\Extension\ExternalLink\ExternalLinkExtension::class,
        ],

        /**
         * --------------------------------------------------------------------------
         *  CommonMark Options
         * --------------------------------------------------------------------------
         *  Configuration options passed to the CommonMark parser. These options are
         *  merged recursively with the existing commonmark_options from the base
         *  markdown configuration, allowing fine-grained control over parsing
         *  behavior without losing existing customizations.
         *
         *  For a complete list of available options, refer to:
         *  https://commonmark.thephpleague.com/2.4/configuration/
         */

        'options' => [
            /**
             * Controls how external links are handled in the markdown preview. Links
             * matching 'internal_hosts' are treated as internal navigation, while
             * external links automatically open in new windows/tabs for better
             * user experience and site retention.
             *
             * Docs: https://commonmark.thephpleague.com/2.7/extensions/external-links/
             */

            'external_link' => [
                'internal_hosts'     => env('APP_URL'),
                'open_in_new_window' => true,
            ],

            /**
             * Defines table rendering behavior in the preview. The 'wrap' setting
             * controls whether tables are wrapped in container elements, while
             * 'alignment_attributes' maps Markdown table alignment syntax
             * (left, center, right) to their corresponding HTML
             * attributes for proper visual formatting.
             *
             * Docs: https://commonmark.thephpleague.com/2.7/extensions/tables/
             */

            'table' => [
                'wrap' => [
                    'enabled'    => false,
                    'tag'        => 'div',
                    'attributes' => [],
                ],
                'alignment_attributes' => [
                    'left'   => ['align' => 'left'],
                    'center' => ['align' => 'center'],
                    'right'  => ['align' => 'right'],
                ],
            ],
        ],
    ],

    /**
     * --------------------------------------------------------------------------
     *  Editor Configuration
     * --------------------------------------------------------------------------
     *  General settings for the markdown editor behavior, timing, and available
     *  commands. These options control user interaction and interface elements.
     */
    'editor' => [

        /**
         * --------------------------------------------------------------------------
         *  Event Dispatcher Name
         * --------------------------------------------------------------------------
         *  The default name of the event that will be dispatched when markdown content
         *  is updated. This allows other components to listen for changes and react
         *  accordingly to editor modifications. Different event names can be set
         *  during component initialization
         */
        'dispatcher' => env(key: 'MD_EDITOR_DISPATCHER', default: 'markdown-editor'),

        /**
         * --------------------------------------------------------------------------
         *  Content Update Debounce
         * --------------------------------------------------------------------------
         *  The delay in milliseconds before sending content updates to the server
         *  after the user stops typing. This prevents excessive server requests
         *  during active editing while ensuring changes are saved promptly.
         */
        'debounce' => (int) env(key: 'MD_EDITOR_DEBOUNCE_MS', default: 1000),

        /**
         * --------------------------------------------------------------------------
         *  Error Message Timing
         * --------------------------------------------------------------------------
         *  Controls how long error messages are displayed to users and the fade
         *  transition duration when they disappear. Display time includes the
         *  full visibility period before fade-out. Both values are set in
         *  milliseconds.
         */
        'messages' => [
            'display' => (int) env(key: 'MD_EDITOR_ERROR_DISPLAY_MS', default: 2500),
            'fadeout' => (int) env(key: 'MD_EDITOR_ERROR_FADE_MS', default: 300),
        ],

        /**
         * --------------------------------------------------------------------------
         *  Editor Commands Configuration
         * --------------------------------------------------------------------------
         *  Available commands for the command palette in the editor. To disable
         *  specific commands, comment them out rather than removing them. Do
         *  not modify the indexes as they relate to the script handlers.
         */
        'commands' => [

            'text' => [
                'index' => 0,
                'icon'  => 'text',
            ],
            'h1' => [
                'index' => 1,
                'icon'  => 'h1',
            ],
            'h2' => [
                'index' => 2,
                'icon'  => 'h2',
            ],
            'h3' => [
                'index' => 3,
                'icon'  => 'h3',
            ],
            'link' => [
                'index' => 4,
                'icon'  => 'link',
            ],
            'divider' => [
                'index' => 5,
                'icon'  => 'divider',
            ],
            'ul' => [
                'index' => 6,
                'icon'  => 'list-ul',
            ],
            'ol' => [
                'index' => 7,
                'icon'  => 'list-ol',
            ],
            'quote' => [
                'index' => 8,
                'icon'  => 'quote',
            ],
            'code' => [
                'index' => 9,
                'icon'  => 'code',
            ],
            'image' => [
                'index' => 10,
                'icon'  => 'image',
            ],
            'youtube' => [
                'index' => 11,
                'icon'  => 'youtube',
            ],
        ],
    ],
];
```

#### Environment Variables

The following env variables are available to configure the editor using your env file.

```dotenv
MD_EDITOR_STORAGE_DISK=public
MD_EDITOR_STORAGE_PATH=images/:date/:id/:file
MD_EDITOR_IMG_MAX_SIZE=5000
MD_EDITOR_DISPATCHER=markdown-editor
MD_EDITOR_DEBOUNCE_MS=1000
MD_EDITOR_ERROR_DISPLAY_MS=2500
MD_EDITOR_ERROR_FADE_MS=300
```
---

## Translations

You can publish (optional) the translations file with:

```bash
php artisan vendor:publish --tag=livewire-markdown-editor-translations
```

## Usage

### Set the Dispatcher

This is the event name the editor dispatches to when updating markdown content and passing the updates to your parent component.

```html 
<livewire:markdown-editor
    dispatcher="markdown-content"
/>
```

### Set Initial Value
Initialize the editor with current markdown content, e.g., when updating a post:

```html 
<livewire:markdown-editor
    :markdown="$post->content"
/>
```

### Set Up Your Parent Component
In your parent Livewire component, add the following to receive the editor updates:

```php
#[On('markdown-content')]
public function setContent(string $content): void
{
    $this->page_content = $content;
}
```

Example:

```php
class CreatePost extends Component
{
    public string $title   = '';
    public string $content = '';
    
    #[On('markdown-content')]
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
    
    public function save(): void
    {
        $validated = $this->validate();
        
        Post::create($validated);
    }
}
```

### Multiple Editor component configuration

Multiple markdown editors can be added to a single page / form. Each should have its own unique key and dispatcher. As an Example:

```html
@foreach($posts as $post)
    <livewire:markdown-editor
        :key="'editor-' . $post->id"
        :markdown="$post->content"
        :dispatcher="'post-content-' . $post->id"
    />
@endforeach
```

---

## Using the Editor

### Slash Commands

Press `/` in the editor to open the command menu. Available commands:

- Heading 1-3
- Link
- Divider
- Bullet List
- Number List
- Quote
- Code Block
- Image Upload
- YouTube Embed

Example usage:

1. Type `/` to open the command menu
2. Use arrow keys to navigate or click on a command
3. Press Enter to select a command

### Keyboard Shortcuts

| Action                | Shortcut            |
|-----------------------|---------------------|
| Open command menu     | `/`                 |
| Navigate command menu | Arrow keys          |
| Select command        | Enter               |
| Close command/popover | Escape              |
| Show command hint     | Enter (on new line) |

### Image Uploads

The editor supports image uploads with the following specifications:

- **Supported formats**: All formats provided in the config file.
- **Maximum file size**: Max file size provided in the config file.
- **Upload methods**:
    - Drag & drop into the editor
    - Click the image option in the slash command menu

Images are processed internally by the editor component using the class defined in the config file:

```php
'image' => [
    'action' => Codeldev\LivewireMarkdownEditor\Actions\UploadImageAction::class,
],
```

If required, you may use your own [custom implementation](#custom-implementations) for uploading images

### YouTube Embedding

To embed a YouTube video:

1. Press `/` to open the command menu
2. Select the YouTube option
3. Paste a valid YouTube URL (youtube.com or youtu.be)
4. Press Insert


### Html Previews 

Html for the preview uses the Spatie laravel-markdown package. Please review the readme of this package for available options to configure this package. By default, the editor uses:

#### Extensions:

```php 
League\CommonMark\Extension\ExternalLink\ExternalLinkExtension::class,
League\CommonMark\Extension\Table\TableExtension::class,
Codeldev\LivewireMarkdownEditor\Support\Commonmark\Extensions\YouTubeExtension::class,
```

#### Commonmark options

```php 
'external_link' => [
    'internal_hosts'     => env('APP_URL'),
    'open_in_new_window' => true,
],
'table' => [
    'wrap' => [
        'enabled'    => false,
        'tag'        => 'div',
        'attributes' => [],
    ],
    'alignment_attributes' => [
        'left'   => ['align' => 'left'],
        'center' => ['align' => 'center'],
        'right'  => ['align' => 'right'],
    ],
],
```

Any config options you set for the laravel-markdown package (either directly in the published config file OR your env file), will be merged with the options above.

If required, you may use your own [custom implementation](#custom-implementations) for converting markdown to html.

---

## Advanced Configuration

You can customize the editor with additional parameters:

```html
<livewire:markdown-editor
    dispatcher="markdown-content"
    :markdown="$post->content"
    :key="'editor-' . $post->id"
/>
```

The editor uses a unique key to identify instances when multiple editors are used on the same page.

---

## Component Architecture

The markdown editor is composed of several components:

- **Main Component**: Orchestrates the editor functionality
- **Header Layout**: Contains the tab navigation
- **Editor Layout**: The textarea for writing markdown
- **Preview Layout**: Renders the HTML preview
- **Commands Component**: Handles the slash command menu
- **Link/YouTube Components**: Manages link and YouTube popover interfaces
- **Upload Component**: Handles file uploads

The editor uses Alpine.js for frontend interactivity and Livewire for backend communication.

If you wish to customize the component files, publish them with:

```bash
php artisan vendor:publish --tag=livewire-markdown-editor-components
```

---

## Custom Implementations

The editor has been built with default Image processing and Preview HTML rendering. However, if you wish to use your own custom processing and rendering classes you may do so.

### Custom upload action implementation

```php
namespace App\Actions;

use Codeldev\LivewireMarkdownEditor\Abstracts\UploadImageAbstract;
use Codeldev\LivewireMarkdownEditor\Contracts\UploadImageInterface;

class S3ImageUploadAction extends UploadImageAbstract implements UploadImageInterface
{
    public function handle(string $imageData, string $fileName, string $mimeType): array
    {
        // Custom S3 upload logic
        // Image optimization
        // CDN integration
    }
}
```

### Custom html preview action implementation

```php
namespace App\Actions;

use Codeldev\LivewireMarkdownEditor\Contracts\PreviewHtmlInterface;

class CustomPreviewAction implements PreviewHtmlInterface
{
    public function handle(string $markdown): string
    {
        // Custom syntax highlighting
        // Custom markdown extensions
        // Security sanitization
    }
}
```

## Troubleshooting

### Common Issues

**Issue**: Editor doesn't update the parent component
**Solution**: Ensure the dispatcher name matches the `#[On()]` attribute in your parent component

```php
#[On('markdown-content')]
public function setContent(string $content): void
{
    $this->page_content = $content;
}
```

**Issue**: Editor appears empty after initialization
**Solution**: Make sure you're passing the markdown content correctly:

```html
<livewire:markdown-editor
    :markdown="$post->content ?? ''"
/>
```

---

## Testing

```bash
composer test
```

---

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

---

## Security Vulnerabilities

Please review [security policy](SECURITY.md) on how to report security vulnerabilities.

---

## Credits

- [Clive Hawkins](https://github.com/codeldev)

---

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
