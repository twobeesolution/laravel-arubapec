<?php

return [
    // Login
    'user'          => env('PEC_USER', ''),
    'pass'          => env('PEC_PASS', ''),

    // PEC certified domain
    'domain'        => env('PEC_DOMAIN', ''),
    'class'         => env('PEC_CLASS', 'EMAIL'),

    // Renewal type. T for automatic, S for manual
    'renewal'       => env('PEC_RENEWAL_TYPE', 'T'),

    // Years to expiration (1-5)
    'expires_after' => env('PEC_EXPIRES_AFTER', '1'),

    // Codice Identificativo Gara
    'cig'           => env('PEC_CIG', ''),

    // Codice PA
    'codice_pa'     => env('PEC_CODICE_PA', ''),

    // Dry-run mode (doesn't call the Aruba API)
    'dry_run'       => env('PEC_DRY_RUN', false),
];