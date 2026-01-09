<?php

return [
    'disk_name' => env('MEDIA_DISK', 'public'),

    'max_file_size' => 1024 * 1024 * 10, // 10MB

    'paths' => [
        'media' => '/media',
    ],

    'conversions' => [
        'default' => [
            [
                'name' => 'thumb',
                'manipulations' => [
                    ['resize', [300, 300]],
                ],
                'performOnCollections' => ['images'],
                'queued' => false,
            ],
            [
                'name' => 'preview',
                'manipulations' => [
                    ['resize', [600, 600]],
                ],
                'performOnCollections' => ['images'],
                'queued' => false,
            ],
        ],
    ],

    'temporary_directory_path' => storage_path('app/media-library/temp'),

    'media_model' => \Spatie\MediaLibrary\MediaCollections\Models\Media::class,
];
