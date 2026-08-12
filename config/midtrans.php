<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'sanitize' => env('MIDTRANS_SANITIZE', true),
    'enable_3ds' => env('MIDTRANS_3DS', true),
    'boost_price' => env('BOOST_PRICE', 50000),
    'boost_duration_days' => env('BOOST_DURATION_DAYS', 30),
    'boost_trial_days' => env('BOOST_TRIAL_DAYS', 3),
];
