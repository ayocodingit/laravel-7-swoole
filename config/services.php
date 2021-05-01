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
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_CALLBACK'),
    ],

    'keycloak' => [
        'authServerUrl'         => env('KEYCLOAK_AUTHSERVERURL'),
        'realm'                 => env('KEYCLOAK_REALM'),
        'clientId'              => env('KEYCLOAK_CLIENTID'),
        'clientSecret'          => env('KEYCLOAK_CLIENTSECRET'),
        'redirectUri'           => env('KEYCLOAK_REDIRECTURI'),
        'encryptionAlgorithm'   => env('KEYCLOAK_ENCRYPTIONALGORITHM'),
        'encryptionKeyPath'     => env('KEYCLOAK_ENCRYPTIONKEYPATH'),
        'encryptionKey'         => env('KEYCLOAK_ENCRYPTIONKEY'),
    ],

    'pelaporan' => [
        'api_key' => env('PELAPORAN_KEY'),
        'url' => env('PELAPORAN_URL'),
    ],

    'tes_masif' => [
        'api_key' => env('TES_MASIF_KEY'),
    ],

    'gcould' => [
        'type' => env('GCLOUD_TYPE'),
        'project_id' => env('GCLOUD_PROJECT_ID'),
        'private_key_id' => env('GCLOUD_PRIVATE_KEY_ID'),
        'private_key' => env('GCLOUD_PRIVATE_KEY'),
        'client_email' => env('GCLOUD_CLIENT_EMAIL'),
        'client_id' => env('GCLOUD_CLIENT_ID'),
        'auth_uri' => env('GCLOUD_AUTH_URI'),
        'token_uri' => env('GCLOUD_TOKEN_URI'),
        'auth_provider_x509_cert_url' => env('GCLOUD_AUTH_PROVIDER_X509_CERT_URL'),
        'client_x509_cert_url' => env('GCLOUD_CLIENT_X509_CERT_URL'),
    ],

    'gcould_pubsub_topic_name' => [
        'sample_validated' => env('GCLOUD_PUBSUB_SAMPLE_VALIDATED_TOPIC_NAME')
    ]

];
