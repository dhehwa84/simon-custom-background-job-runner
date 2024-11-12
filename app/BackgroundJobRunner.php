<?php
namespace App;

use Illuminate\Support\Facades\Log;

class BackgroundJobRunner
{
    
    /**
     * Runs a background job by creating an instance of the specified class, validating the class and method,
     * and logging the job's status. If the job fails, it will retry up to the specified number of times.
     *
     * @param string $className The fully qualified name of the class to run the job.
     * @param string $method The name of the method to call within the class.
     * @param array $parameters An optional array of parameters to pass to the method.
     * @param int $retries The number of times to retry the job in case of failure. Default is 3.
     *
     * @return void
     */
    public static function run($className, $method, $parameters = [], $retries = 3)
    {
        // Load the allowed classes from config
        $allowedClasses = config('background_jobs.allowed_classes', []);

        // Validate class name
        if (!in_array($className, $allowedClasses)) {
            Log::channel('background_jobs_errors')->error("Unauthorized class attempted to run | Class: {$className} | Method: {$method} | Parameters: " . json_encode($parameters) . " | Timestamp: " . now());
            return;
        }

        // Ensure the class exists
        if (!class_exists($className)) {
            Log::channel('background_jobs_errors')->error("Class does not exist | Class: {$className} | Method: {$method} | Parameters: " . json_encode($parameters) . " | Timestamp: " . now());
            return;
        }

        // Validate method name
        if (!method_exists($className, $method)) {
            Log::channel('background_jobs_errors')->error("Method does not exist in class | Class: {$className} | Method: {$method} | Parameters: " . json_encode($parameters) . " | Timestamp: " . now());
            return;
        }

        try {
            // Create an instance of the approved class
            $instance = new $className();

            // Log the "running" status
            Log::channel('background_jobs_errors')->info("Job running | Class: " . get_class($instance) . " | Method: {$method} | Parameters: " . json_encode($parameters) . " | Timestamp: " . now());

            // Call the specified method with the provided parameters
            call_user_func_array([$instance, $method], $parameters);

            // Log success status
            Log::channel('background_jobs_errors')->info("Job completed successfully | Class: " . get_class($instance) . " | Method: {$method} | Parameters: " . json_encode($parameters) . " | Timestamp: " . now());
        } catch (\Exception $e) {
            // Log failure status
            Log::channel('background_jobs_errors')->error("Job failed | Class: " . get_class($instance) . " | Method: {$method} | Error: {$e->getMessage()} | Parameters: " . json_encode($parameters) . " | Timestamp: " . now());

            // Retry logic if retries are still available
            if ($retries > 0) {
                sleep(5); // Retry delay
                self::run($className, $method, $parameters, $retries - 1);
            }
        }
    }
}

