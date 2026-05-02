<?php

return [
    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA Version
    |--------------------------------------------------------------------------
    |
    | The version of reCAPTCHA to use. Currently only 'v3' is supported.
    |
    */
    'version' => 'v3',

    /*
    |--------------------------------------------------------------------------
    | Site Key
    |--------------------------------------------------------------------------
    |
    | The site key provided by Google reCAPTCHA admin console.
    | Get it from: https://www.google.com/recaptcha/admin/
    |
    */
    'site_key' => env('RECAPTCHA_SITE_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Secret Key
    |--------------------------------------------------------------------------
    |
    | The secret key provided by Google reCAPTCHA admin console.
    |
    */
    'secret_key' => env('RECAPTCHA_SECRET_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Score Threshold
    |--------------------------------------------------------------------------
    |
    | The minimum score required to pass the reCAPTCHA check.
    | Score ranges from 0.0 to 1.0, where 1.0 is very likely a good interaction.
    | Recommended: 0.5
    |
    */
    'score_threshold' => env('RECAPTCHA_SCORE_THRESHOLD', 0.5),

    /*
    |--------------------------------------------------------------------------
    | Skip IP Addresses
    |--------------------------------------------------------------------------
    |
    | IP addresses that should skip reCAPTCHA verification (useful for testing).
    |
    */
    'skip_ips' => [
        // '127.0.0.1',
    ],
];
