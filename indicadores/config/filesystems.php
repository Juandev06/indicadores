<?php

return [
    'default' => env('FILESYSTEM_DRIVER', 'do_s3'),
    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        ],
        // Digital Ocean Spaces (s3 SDK)
        'do_s3' => [
            'driver' => 's3',
            'key' => env('DO_S3_KEY'),
            'secret' => env('DO_S3_SECRET'),
            'region' => env('DO_S3_REGION'),
            'bucket' => env('DO_S3_BUCKET'),
            'endpoint' => env('DO_S3_ENDPOINT'),
            'url' => env('DO_S3_URL'),
            'cdn_endpoint' => env('DO_S3_CDN_ENDPOINT'),
            'use_path_style_endpoint' => env('DO_S3_USE_PATH_STYLE_ENDPOINT', false),
            'bucket_endpoint' => true,
        ],

    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
