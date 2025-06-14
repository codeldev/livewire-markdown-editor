<?php

declare(strict_types=1);

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
            League\CommonMark\Extension\ExternalLink\ExternalLinkExtension::class,
            League\CommonMark\Extension\Table\TableExtension::class,
            Codeldev\LivewireMarkdownEditor\Support\Commonmark\Extensions\YouTubeExtension::class,
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
