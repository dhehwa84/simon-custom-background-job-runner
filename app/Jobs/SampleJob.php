<?php

namespace App\Jobs;
use Illuminate\Support\Facades\Log;

class SampleJob
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}
    public function execute($message = "Not given!")
    {
        Log::channel('background_jobs_errors')->info("Executing SampleJob with message: $message");
        throw new \Exception("Error Processing Request", 1);
        
    }
}
