<?php
use App\BackgroundJobRunner;

if (!function_exists('runBackgroundJob')) {
    /**
     * Runs a background job using the specified class and method.
     *
     * @param string $className The fully qualified class name of the job.
     * @param string $method The method to be executed within the job class.
     * @param array $parameters An optional array of parameters to be passed to the job method.
     * @param int $retries The number of times the job should be retried in case of failure. Default is 3.
     *
     * @return void
     */
    function runBackgroundJob($className, $method, $parameters = [], $retries = 3)
    {
        if (stristr(PHP_OS, 'WIN')) {
            // Windows command
            pclose(popen("start /B php artisan background-job:run {$className} {$method}", "r"));
        } else {
            // Unix command
            exec("php artisan background-job:run {$className} {$method} > /dev/null &");
        }

        // Dynamically resolve the class from the string
        if (class_exists($className)) {
            $instance = app($className);
            BackgroundJobRunner::run($instance, $method, $parameters, $retries);
        } else {
            Log::channel('background_jobs_errors')->error("Class {$className} not found.");
        }
    }
}

