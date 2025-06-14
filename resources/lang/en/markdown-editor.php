<?php

declare(strict_types=1);

return [
    'editor' => [
        'general' => [
            'command' => 'Type / to access the command palette',
            'preview' => 'Nothing to preview yet.',
            'image'   => 'Drop your image file here',
        ],
        'tabs' => [
            'write'   => 'Write',
            'preview' => 'Preview',
        ],
        'buttons' => [
            'cancel' => 'Cancel',
            'insert' => 'Insert',
        ],
        'statusbar' => [
            'characters' => [
                'singular' => 'Character',
                'plural'   => 'Characters',
            ],
            'words' => [
                'singular' => 'Word',
                'plural'   => 'Words',
            ],
        ],
        'commands' => [
            'text' => [
                'heading'    => 'Text',
                'subheading' => 'Add Plain text line.',
            ],
            'h1' => [
                'heading'    => 'Heading',
                'subheading' => 'Large heading text.',
            ],
            'h2' => [
                'heading'    => 'Heading 2',
                'subheading' => 'Medium heading text.',
            ],
            'h3' => [
                'heading'    => 'Heading 3',
                'subheading' => 'Small heading text.',
            ],
            'link' => [
                'heading'    => 'Link',
                'subheading' => 'Insert a text link.',
            ],
            'divider' => [
                'heading'    => 'Divider',
                'subheading' => 'Insert a divider line.',
            ],
            'ul' => [
                'heading'    => 'Bullet List',
                'subheading' => 'Insert a bullet list.',
            ],
            'ol' => [
                'heading'    => 'Number List',
                'subheading' => 'Insert a number list.',
            ],
            'quote' => [
                'heading'    => 'Quote',
                'subheading' => 'Insert a quote.',
            ],
            'code' => [
                'heading'    => 'Code Block',
                'subheading' => 'Add a code block.',
            ],
            'image' => [
                'heading'    => 'Image',
                'subheading' => 'Upload an image.',
            ],
            'youtube' => [
                'heading'    => 'YouTube',
                'subheading' => 'Add a Youtube video.',
            ],
        ],
        'popovers' => [
            'youtube' => [
                'label'       => 'Youtube',
                'placeholder' => 'Youtube Video URL',
            ],
            'link' => [
                'text' => [
                    'label'       => 'Link Text',
                    'placeholder' => 'Text',
                ],
                'url'  => [
                    'label'       => 'Link URL',
                    'placeholder' => 'https://example.com',
                ],
            ],
        ],
    ],
    'validation' => [
        'youtube' => [
            'empty'   => 'YouTube URL is required',
            'invalid' => 'Please enter a valid YouTube URL',
        ],
        'link' => [
            'text'    => 'Link text is required',
            'url'     => 'URL is required',
            'invalid' => 'Please enter a valid URL',
        ],
        'image' => [
            'nofile' => 'No file selected',
        ],
    ],
    'preview' => [
        'exceptions' => [
            'action'     => 'Invalid preview html action class configured. :class must exist and be a valid class string',
            'implements' => 'The preview html action class :class must implement the PreviewHtmlInterface',
            'renderer'   => 'Invalid renderer class: :class. Must be a string with a public toHtml method.',
        ],
    ],
    'uploads' => [
        'success' => 'Image uploaded successfully',
        'errors'  => [
            'exception'   => 'Error uploading image: :error',
            'base64'      => 'Invalid base64 image data received',
            'filesize'    => 'File size exceeds the :size limit',
            'unsupported' => 'Unsupported image format uploaded',
            'unreadable'  => 'Failed to read the image file',
        ],
        'exceptions' => [
            'store'      => 'Failed to store image.',
            'action'     => 'Invalid image upload action class configured. :class must exist and be a valid class string',
            'implements' => 'The image upload action class :class must implement the UploadImageInterface',
            'disk'       => [
                'invalid' => 'Storage disk :disk is not set in the filesystem config file.',
                'unset'   => 'No storage disk for the markdown editor has been set.',
            ],
        ],
    ],
];
