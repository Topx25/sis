<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "gd", "imagick"
    |
    */
    'driver' => env('IMAGE_DRIVER', 'gd'),

    /*
    |--------------------------------------------------------------------------
    | Image Resize Constraints
    |--------------------------------------------------------------------------
    |
    | Maximum allowed dimensions in pixels. Images exceeding these dimensions
    | will be downscaled while maintaining the aspect ratio without up-sizing.
    |
    */
    'resize' => [
        'width' => (int) env('IMAGE_MAX_WIDTH', 1200),
        'height' => (int) env('IMAGE_MAX_HEIGHT', 1200),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compression Quality
    |--------------------------------------------------------------------------
    |
    | Quality level from 0 to 100 for encoded images.
    |
    */
    'quality' => (int) env('IMAGE_QUALITY', 80),

    /*
    |--------------------------------------------------------------------------
    | Upload Limits
    |--------------------------------------------------------------------------
    |
    | Maximum file size in kilobytes (10MB = 10240 KB) and allowed extensions.
    |
    */
    'max_size_kb' => (int) env('IMAGE_MAX_SIZE_KB', 10240),
    'allowed_mimes' => ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'],
    'allowed_extensions' => ['jpeg', 'jpg', 'png', 'webp'],
];
