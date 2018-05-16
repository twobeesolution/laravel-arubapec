<?php

return [
    // Login
    'user' => '',
    'pass' => '',

    // PEC certified domain
    'domain' => '',
    'class'  => 'EMAIL',

    // Renewal type. T for automatic, S for manual
    'renewal' => 'T',

    // Years to expiration (1-5)
    'expires_after' => '1',

    // Codice Identificativo Gara
    'cig' => '',

    // Dry-run mode (doesn't call the Aruba API)
    'dry_run' => false,
];