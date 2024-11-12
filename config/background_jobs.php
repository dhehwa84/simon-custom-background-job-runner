<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Allowed Background Job Classes
    |--------------------------------------------------------------------------
    |
    | This array contains the fully qualified class names of all the classes
    | that are approved to be run as background jobs. Only the classes listed
    | here will be allowed to execute when called by the BackgroundJobRunner.
    |
    */

    'allowed_classes' => [
        'App\Jobs\SampleJob', // Example job class
        'App\Jobs\AnotherJob', // Another example job class
        // Add other approved job classes here
    ],
];
