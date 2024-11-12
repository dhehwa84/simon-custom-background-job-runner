<?php

namespace App\Jobs;
use Illuminate\Support\Facades\Log;

class SampleJob1
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}
    public function execute($message)
    {
        Log::channel('background_jobs_errors')->info("Executing SampleJob with message: $message");
        // throw new \Exception("Error Processing Request", 1);
        
    }
}
