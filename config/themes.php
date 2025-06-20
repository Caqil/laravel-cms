<?php

return [
    'path' => base_path('Modules'),
    'namespace' => 'Modules',
    'max_upload_size' => env('UPLOADS_MAX_SIZE', 10240) * 1024, // Convert KB to bytes
    'allowed_extensions' => ['zip'],
    'default_theme' => 'default',
    'cache_enabled' => true,
    'theme_types' => [
        'frontend' => [
            'active_theme' => null,
            'fallback_theme' => 'default',
        ],
        'admin' => [
            'active_theme' => null,
            'fallback_theme' => 'admin-default',
        ],
    ],
];
