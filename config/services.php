<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    
    'firebase' => [
        'database_url' => env('FIREBASE_DATABASE_URL'),
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'private_key_id' => env('FIREBASE_PRIVATE_KEY_ID'),
        'private_key' => "-----BEGIN PRIVATE KEY-----".str_replace("\\n", "\n", env('FIREBASE_PRIVATE_KEY'))."-----END PRIVATE KEY-----\n",
        'client_email' => env('FIREBASE_CLIENT_EMAIL'),
        'client_id' => env('FIREBASE_CLIENT_ID'),
        'client_x509_cert_url' => env('FIREBASE_CLIENT_x509_CERT_URL'),
    ],
    
    'googleappscript1' => [
        'googleas_project_id' => env('GOOGLEAS_PROJECT_ID'),
        'googleas_private_key_id' => env('GOOGLEAS_PRIVATE_KEY_ID'),
        'googleas_client_id' => env('GOOGLEAS_CLIENT_ID'),
        'googleas_private_key' => "-----BEGIN PRIVATE KEY-----".str_replace("\\n", "\n", env('GOOGLEAS_PRIVATE_KEY'))."-----END PRIVATE KEY-----\n",
        'googleas_auth_uri' => env('GOOGLEAS_AUTH_URI'),
        'googleas_token_uri' => env('GOOGLEAS_TOKEN_URI'),
        'googleas_auth_provider_x509_cert_url' => env('GOOGLEAS_AUTH_PROVIDER_X509_CERT_URL'),
        'client_secret' => env('CLIENT_SECRET'),
    ],
    
    'googleappscript' => [
        'googleas_client_json' => env('GOOGLEAS_CLIENT_JSON'),
    ]
];
