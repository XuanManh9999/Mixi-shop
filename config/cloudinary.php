<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Cloudinary settings. Cloudinary is a cloud
    | service that offers a solution to a web application's entire image
    | management pipeline.
    |
    */

    'cloud_name' => env('CLOUDINARY_CLOUD_NAME', 'dpbo17rbt'),
    'api_key' => env('CLOUDINARY_API_KEY', '923369223654775'),
    'api_secret' => env('CLOUDINARY_API_SECRET', '7szIKlRno-q8XTeuFI2YIeLuZ4'),
    'secure' => env('CLOUDINARY_SECURE', true),
    
    /*
    |--------------------------------------------------------------------------
    | Upload Settings
    |--------------------------------------------------------------------------
    */
    
    'folder' => env('CLOUDINARY_FOLDER', 'mixishop'),
    'quality' => env('CLOUDINARY_QUALITY', 'auto'),
    'format' => env('CLOUDINARY_FORMAT', 'auto'),
    
    /*
    |--------------------------------------------------------------------------
    | Transformation Settings
    |--------------------------------------------------------------------------
    */
    
    'transformations' => [
        'thumbnail' => [
            'width' => 300,
            'height' => 300,
            'crop' => 'fill',
            'quality' => 'auto',
            'format' => 'auto'
        ],
        'medium' => [
            'width' => 600,
            'height' => 600,
            'crop' => 'fill',
            'quality' => 'auto',
            'format' => 'auto'
        ],
        'large' => [
            'width' => 1200,
            'height' => 1200,
            'crop' => 'fill',
            'quality' => 'auto',
            'format' => 'auto'
        ]
    ]
];
