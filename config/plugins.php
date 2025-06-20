<?php

return [
    'path' => base_path('Modules'),
    'namespace' => 'Modules',
    'max_upload_size' => env('UPLOADS_MAX_SIZE', 10240) * 1024, // Convert KB to bytes
    'allowed_extensions' => ['zip'],
    'auto_discovery' => true,
    'cache_enabled' => true,
    'module_types' => [
        'plugin' => [
            'enabled' => true,
            'auto_register' => true,
        ],
        'theme' => [
            'enabled' => true,
            'auto_register' => false, // Themes are activated manually
        ],
    ],
];
