<?php
use App\BackgroundJobRunner;
use Illuminate\Support\Facades\Log;

/**
 * Dispatches a background job to the queue using the specified class and method.
 *
 * @param string $className The fully qualified class name of the job.
 * @param string $method The method to be executed within the job class.
 * @param array $parameters An optional array of parameters to be passed to the job method.
 * @param int $retries The number of times the job should be retried in case of failure. Default is 3.
 * @param int $priority The priority of the job. Default is 0 (low).
 * @param int $delay The delay in seconds before the job is executed. Default is 0.
 *
 * @return void
 */
function runBackgroundJob($className, $method, $parameters = [], $retries = 3, $priority = 0, $delay = 0)
{
    if (stristr(PHP_OS, 'WIN')) {
        // Windows command
        pclose(popen("start /B php artisan background-job:dispatch {$className} {$method}", "r"));
    } else {
        // Unix command
        exec("php artisan background-job:dispatch {$className} {$method} > /dev/null &");
    }

    // Validate that the class and method exist
    if (class_exists($className)) {
        $instance = new $className();

        if (method_exists($instance, $method)) {
            // Dispatch the job to the queue using BackgroundJobRunner::dispatch
            BackgroundJobRunner::dispatch($className, $method, $parameters, $retries, $delay, $priority);
        } else {
            // Log error if method does not exist
            Log::channel('background_jobs_errors')->error("Method {$method} does not exist in class {$className}.");
        }
    } else {
        // Log error if class does not exist
        Log::channel('background_jobs_errors')->error("Class {$className} not found.");
    }
}
