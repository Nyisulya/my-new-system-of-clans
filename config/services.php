<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'mpesa' => [
        'env' => env('MPESA_ENV', 'sandbox'),
        'consumer_key' => env('MPESA_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
        'passkey' => env('MPESA_PASSKEY'),
        'shortcode' => env('MPESA_SHORTCODE', '174379'),
        'callback_url' => env('MPESA_CALLBACK_URL'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'fcm' => [
        'api_key' => env('FCM_API_KEY', env('FIREBASE_API_KEY')),
        'auth_domain' => env('FCM_AUTH_DOMAIN', env('FIREBASE_AUTH_DOMAIN')),
        'project_id' => env('FCM_PROJECT_ID', env('FIREBASE_PROJECT_ID')),
        'storage_bucket' => env('FCM_STORAGE_BUCKET', env('FIREBASE_STORAGE_BUCKET')),
        'messaging_sender_id' => env('FCM_MESSAGING_SENDER_ID', env('FIREBASE_MESSAGING_SENDER_ID')),
        'app_id' => env('FCM_APP_ID', env('FIREBASE_APP_ID')),
        'vapid_key' => env('FCM_VAPID_KEY', env('FIREBASE_VAPID_KEY')),
    ],

    'google' => [
        'analytics_id' => env('GOOGLE_ANALYTICS_ID'),
    ],

];
