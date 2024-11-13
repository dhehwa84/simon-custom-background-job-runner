<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Job;

class ExecuteBackgroundJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $className;
    public $method;
    public $parameters;
    public $retries;
    public $priority;

    // Define the number of retry attempts for the job
    public $tries;

    // Define a delay (in seconds) before each retry
    public $backoff = 5;

    public function __construct($className, $method, $parameters = [], $retries = 3, $priority = 5, $retryDelay = 5)
    {
        $this->className = $className;
        $this->method = $method;
        $this->parameters = $parameters;
        $this->retries = $retries;
        $this->priority = $priority;
        $this->backoff = $retryDelay; 
        $this->tries = $retries; 
    }

    /**
     * Execute the job.
     *
     * This method handles the execution of the background job. It creates a job record in the database,
     * logs the start of the job, runs the job, updates the job status on success, logs successful job completion,
     * increments retry count for logging and monitoring purposes, logs retry attempts, and rethrows exceptions for Laravel to handle retries.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        // create job record in the database
        $jobRecord = Job::create([
            'class_name' => $this->className,
            'method' => $this->method,
            'parameters' => json_encode($this->parameters),
            'retries' => $this->retries,
            'retry_count' => 0,
            'status' => 'running',
            'priority' => $this->priority,
        ]);

        try {
            // Log the start of job execution
            Log::channel('background_jobs_errors')->info("Job running | Class: {$this->className} | Method: {$this->method} | Parameters: " . json_encode($this->parameters) . " | Timestamp: " . now());

            // Run the job
            $instance = new $this->className();
            call_user_func_array([$instance, $this->method], $this->parameters);

            // Update job status on success
            $jobRecord->update(['status' => 'completed']);

            // Log successful job completion
            Log::channel('background_jobs_errors')->info("Job completed successfully | Class: {$this->className} | Method: {$this->method} | Parameters: " . json_encode($this->parameters) . " | Timestamp: " . now());
        } catch (\Exception $e) {
            // Increment retry count for logging and monitoring purposes
            $jobRecord->increment('retry_count');
            $jobRecord->update(['status' => 'retrying']);

            // Log retry attempt
            Log::channel('background_jobs_errors')->error("Job failed and will retry automatically | Class: {$this->className} | Method: {$this->method} | Error: {$e->getMessage()} | Parameters: " . json_encode($this->parameters) . " | Timestamp: " . now());

            // Let Laravel handle the retry by rethrowing the exception
            throw $e;
        }
    }

    /**
     * Set the delay between retries dynamically.
     *
     * @return int|array
     */
    public function backoff()
    {
        return $this->backoff;
    }

}
