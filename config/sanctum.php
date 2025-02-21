<?php

use Laravel\Sanctum\Sanctum;

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Requests from the following domains / hosts will receive stateful API
    | authentication cookies. Typically, these should include your local
    | and production domains which access your API via a frontend SPA.
    |
    */

    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,127.0.0.1,::1')),

    /*
    |--------------------------------------------------------------------------
    | Expiration Minutes
    |--------------------------------------------------------------------------
    |
    | This value controls the number of minutes until an issued token will be
    | considered expired. This will override any values set in the token's
    | "expires_at" attribute, but first-party sessions are not affected.
    |
    */

    'expiration' => null, // Để null nếu muốn token không hết hạn, hoặc đặt số phút (e.g., 60)

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    |
    | When authenticating your first-party SPA with Sanctum, you may need to
    | customize some of the middleware Sanctum uses while processing the
    | request. You may change the middleware listed below as required.
    |
    */

    'middleware' => [
        'verify_csrf_token' => \App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => \App\Http\Middleware\EncryptCookies::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Guard to Use for Authentication
    |--------------------------------------------------------------------------
    |
    | This value controls the authentication guard Sanctum will use while
    | authenticating requests. This value should typically be "web" as
    | Sanctum uses the web guard to manage user sessions.
    |
    */

    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Expiration of "Remember Me" Tokens
    |--------------------------------------------------------------------------
    |
    | This value controls the number of minutes until a "Remember Me" token
    | will be considered expired. These tokens are long-lived but can be
    | invalidated by the user at any time via the application's UI.
    |
    */

    'remember' => env('SANCTUM_REMEMBER_MINUTES', 43200), // 30 ngày (60 phút * 24 giờ * 30 ngày)

    /*
    |--------------------------------------------------------------------------
    | Single Device Authentication
    |--------------------------------------------------------------------------
    |
    | Sanctum may allow the use of single device authentication. When this is
    | enabled, each user may only authenticate with one device at a time.
    | This feature is useful for preventing multiple device logins.
    |
    */

    'single_device' => false, // Nếu muốn chỉ cho phép 1 thiết bị đăng nhập, đổi thành true

    /*
    |--------------------------------------------------------------------------
    | Sanctum Model
    |--------------------------------------------------------------------------
    |
    | When using the "HasApiTokens" trait from Sanctum, we need to know which
    | model should be used to retrieve your application's API tokens. You
    | may use your own model as long as it extends the Sanctum model.
    |
    */

    'model' => Sanctum::personalAccessTokenModel(),

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    |
    | When issuing tokens using Sanctum, you may specify a prefix that will
    | be applied to the token's abilities. This is useful when you have
    | multiple environments sharing the same authentication server.
    |
    */

    'prefix' => env('SANCTUM_PREFIX', 'Bearer'),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Routes Prefix / Middleware
    |--------------------------------------------------------------------------
    |
    | Here you may configure the route prefix and middleware for Sanctum's
    | endpoints. You can modify the prefix and middleware here as needed.
    |
    */

    'prefix' => 'sanctum',
    'middleware' => ['web'],
];
