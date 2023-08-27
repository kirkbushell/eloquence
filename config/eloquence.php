<?php

return [
    'logging' => [
        'enabled' => env('ELOQUENCE_LOGGING_ENABLED', false),
        'driver' => env('ELOQUENCE_LOGGING_DRIVER', env('LOG_CHANNEL', 'stack')),
    ]
];
