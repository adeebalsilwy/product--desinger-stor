<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Designer Configuration
    |--------------------------------------------------------------------------
    |
    | Default settings for the product designer canvas
    |
    */

    // Default canvas dimensions (in pixels)
    'canvas' => [
        'default_width' => 800,
        'default_height' => 800,
        'max_width' => 2000,
        'max_height' => 2000,
        'min_width' => 400,
        'min_height' => 400,
    ],

    // Allowed file formats for uploads
    'allowed_formats' => [
        'images' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'vectors' => ['svg'],
    ],

    // Maximum upload size in MB
    'max_upload_size' => 10,

    // Print settings
    'print' => [
        'dpi' => 300,
        'color_mode' => 'RGB', // or CMYK for professional printing
        'bleed_mm' => 3, // Bleed area around design
    ],

    // Export settings
    'export' => [
        'formats' => ['png', 'jpg', 'pdf', 'svg'],
        'default_format' => 'png',
        'high_res_multiplier' => 4,
        'preview_quality' => 85,
        'print_quality' => 100,
    ],

    // Text element defaults
    'text' => [
        'default_font' => 'Arial',
        'default_size' => 40,
        'default_color' => '#000000',
        'min_size' => 8,
        'max_size' => 500,
    ],

    // Available tools in designer
    'tools' => [
        'select' => true,
        'text' => true,
        'image' => true,
        'clipart' => true,
        'shapes' => true,
        'qr_code' => false, // Enable QR code generation
        'barcode' => false, // Enable barcode generation
    ],

    // Layer management
    'layers' => [
        'max_layers' => 50,
        'allow_reorder' => true,
        'allow_lock' => true,
        'allow_hide' => true,
    ],

    // Grid and snapping
    'grid' => [
        'enabled' => true,
        'snap_to_grid' => false,
        'grid_size' => 10,
        'show_guides' => true,
    ],

    // Storage disk for designs
    'storage_disk' => env('DESIGNER_STORAGE_DISK', 'public'),

];
