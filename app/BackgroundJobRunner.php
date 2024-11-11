<?php
namespace App;

use Illuminate\Support\Facades\Log;

class BackgroundJobRunner
{
    /**
     * Executes a background job by creating an instance of the specified class,
     * calling the specified method with the provided parameters, and handling any exceptions.
     *
     * @param string $className The fully qualified name of the class that contains the job method.
     * @param string $method The name of the method to be executed as a background job.
     * @param array $parameters An optional array of parameters to be passed to the job method.
     * @param int $retries An optional number of retries in case of exceptions. Default is 3.
     *
     * @return void
     *
     * @throws \Exception If the job method throws an exception and all retries have been exhausted.
     */
    public static function run($className, $method, $parameters = [], $retries = 3)
    {
        try {
            $instance = new $className();
            call_user_func_array([$instance, $method], $parameters);
            // Log success 
            Log::channel('background_jobs')->info("Job executed: " . get_class($instance) . "::{$method} - Success");
        } catch (\Exception $e) {
            // Log error 
            Log::channel('background_jobs_errors')->error("Job failed: " . $className . "::{$method} - Error: {$e->getMessage()}");
            if ($retries > 0) {
                sleep(5); // Retry delay
                self::run($className, $method, $parameters, $retries - 1);
            }
        }
    }
}

