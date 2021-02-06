<?php

return [

    /*
    |--------------------------------------------------------------------------
    | BRAINTREE Keys
    |--------------------------------------------------------------------------
    |
    | The BRAINTREE publishable key and secret key give you access to BRAINTREE's
    | API. The "publishable" key is typically used when interacting with
    | BRAINTREE.js while the "secret" key accesses private API endpoints.
    |
    */

    'key' => env('BRAINTREE_KEY'),

    'secret' => env('BRAINTREE_SECRET'),

    'merchant_key' => env('BRAINTREE_MERCHANT_KEY'),

    'environment' => env('BRAINTREE_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Cashier Path
    |--------------------------------------------------------------------------
    |
    | This is the base URI path where Cashier's views, such as the payment
    | verification screen, will be available from. You're free to tweak
    | this path according to your preferences and application design.
    |
    */

    'path' => env('CASHIER_PATH', 'BRAINTREE'),

    /*
    |--------------------------------------------------------------------------
    | BRAINTREE Webhooks
    |--------------------------------------------------------------------------
    |
    | Your BRAINTREE webhook secret is used to prevent unauthorized requests to
    | your BRAINTREE webhook handling controllers. The tolerance setting will
    | check the drift between the current time and the signed request's.
    |
    */

    'webhook' => [
        'secret' => env('BRAINTREE_WEBHOOK_SECRET'),
        'tolerance' => env('BRAINTREE_WEBHOOK_TOLERANCE', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cashier Model
    |--------------------------------------------------------------------------
    |
    | This is the model in your application that implements the Billable trait
    | provided by Cashier. It will serve as the primary model you use while
    | interacting with Cashier related methods, subscriptions, and so on.
    |
    */

    'model' => env('CASHIER_MODEL', class_exists(App\Models\User::class) ? App\Models\User::class : App\User::class),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | This is the default currency that will be used when generating charges
    | from your application. Of course, you are welcome to use any of the
    | various world currencies that are currently supported via BRAINTREE.
    |
    */

    'currency' => env('CASHIER_CURRENCY', 'usd'),

    /*
    |--------------------------------------------------------------------------
    | Currency Locale
    |--------------------------------------------------------------------------
    |
    | This is the default locale in which your money values are formatted in
    | for display. To utilize other locales besides the default en locale
    | verify you have the "intl" PHP extension installed on the system.
    |
    */

    'currency_locale' => env('CASHIER_CURRENCY_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Invoice Paper Size
    |--------------------------------------------------------------------------
    |
    | This option is the default paper size for all invoices generated using
    | Cashier. You are free to customize this settings based on the usual
    | paper size used by the customers using your Laravel applications.
    |
    | Supported sizes: 'letter', 'legal', 'A4'
    |
    */

    'paper' => env('CASHIER_PAPER', 'letter'),
];