<?php

namespace App;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Jobs\ExecuteBackgroundJob;

class BackgroundJobRunner
{
    
    /**
     * Dispatches a background job to the appropriate queue based on priority.
     *
     * @param string $className The fully qualified name of the job class.
     * @param string $method The method to be executed within the job class.
     * @param array $parameters An optional array of parameters to be passed to the job method.
     * @param int $retries The number of times the job should be retried in case of failure. Default is 3.
     * @param int $delay The delay in seconds before the job should be dispatched. Default is 0.
     * @param int $priority The priority of the job. Higher values indicate higher priority. Default is 0.
     *
     * @return void
     */
    public static function dispatch($className, $method, $parameters = [], $retries = 3, $delay = 0, $priority = 0)
    {
        // Check if the class and method are allowed and exist
        $allowedClasses = Config::get('background_jobs.allowed_classes', []);
        if (!in_array($className, $allowedClasses) || !class_exists($className) || !method_exists($className, $method)) {
            Log::channel('background_jobs_errors')->error("Unauthorized or invalid job class/method | Class: {$className} | Method: {$method} | Parameters: " . json_encode($parameters) . " | Timestamp: " . now());
            return;
        }

        // Determine queue based on priority
        $queue = match($priority) {
            3 => 'high_priority',
            2 => 'medium_priority',
            default => 'low_priority',
        };

        // Dispatch job to the appropriate queue with delay
        ExecuteBackgroundJob::dispatch($className, $method, $parameters, $retries, $priority)
            ->delay($delay)
            ->onQueue($queue);
    }
    
}
