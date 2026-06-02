<?php

return [

    /*
    |--------------------------------------------------------------------------
    | School Information
    |--------------------------------------------------------------------------
    |
    | Used on the printable Paystack payment receipt. Override these values
    | in the .env file (SCHOOL_NAME, SCHOOL_ADDRESS, SCHOOL_PHONE, SCHOOL_EMAIL)
    | so the school can be re-branded without touching code.
    |
    */

    'name' => env('SCHOOL_NAME', 'Alven International Schools'),

    'address' => env('SCHOOL_ADDRESS', ''),

    'phone' => env('SCHOOL_PHONE', ''),

    'email' => env('SCHOOL_EMAIL', ''),

];
