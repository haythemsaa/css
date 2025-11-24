<?php

return [
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Social Authentication
    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    // Tunisian Payment Gateways
    'd17' => [
        'api_key' => env('D17_API_KEY'),
        'api_secret' => env('D17_API_SECRET'),
        'merchant_id' => env('D17_MERCHANT_ID'),
        'webhook_secret' => env('D17_WEBHOOK_SECRET'),
        'base_url' => env('D17_BASE_URL', 'https://api.d17.tn'),
    ],

    'konnect' => [
        'api_key' => env('KONNECT_API_KEY'),
        'wallet_id' => env('KONNECT_WALLET_ID'),
        'receiver_id' => env('KONNECT_RECEIVER_ID'),
        'base_url' => env('KONNECT_BASE_URL', 'https://api.konnect.network'),
    ],

    'paymee' => [
        'api_key' => env('PAYMEE_API_KEY'),
        'token' => env('PAYMEE_TOKEN'),
        'base_url' => env('PAYMEE_BASE_URL', 'https://api.paymee.tn'),
    ],

    'sadad' => [
        'merchant_id' => env('SADAD_MERCHANT_ID'),
        'api_key' => env('SADAD_API_KEY'),
        'base_url' => env('SADAD_BASE_URL', 'https://api.sadad.tn'),
    ],

    // Firebase
    'fcm' => [
        'server_key' => env('FCM_SERVER_KEY'),
        'sender_id' => env('FCM_SENDER_ID'),
    ],

    // Cloudflare Stream
    'cloudflare_stream' => [
        'api_token' => env('CLOUDFLARE_STREAM_API_TOKEN'),
        'account_id' => env('CLOUDFLARE_STREAM_ACCOUNT_ID'),
    ],

    // Geolocation
    'geolocation' => [
        'provider' => env('GEOLOCATION_PROVIDER', 'google'),
        'api_key' => env('GEOLOCATION_API_KEY'),
    ],

    // SMS Gateway
    'sms' => [
        'provider' => env('SMS_PROVIDER', 'twilio'),
        'api_key' => env('SMS_API_KEY'),
        'api_secret' => env('SMS_API_SECRET'),
        'from' => env('SMS_FROM'),
    ],
];
