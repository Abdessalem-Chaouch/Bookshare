<?php

return [

    'paths' => ['api/*', '/speak'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:8000'], // ou ['*'] pour tester

    'allowed_headers' => ['Content-Type', 'X-CSRF-TOKEN', 'Authorization', '*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
