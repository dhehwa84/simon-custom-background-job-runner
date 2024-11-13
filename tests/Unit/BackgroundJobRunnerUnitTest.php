<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\BackgroundJobRunner;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class BackgroundJobRunnerUnitTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Log::shouldReceive('channel')->andReturnSelf();
    }

    /**
     * This test method verifies that the BackgroundJobRunner logs the running and completion status of a job.
     *
     * @return void
     */
    public function it_logs_running_and_completion_status()
    {
        // Set the allowed job classes in the configuration
        Config::set('background_jobs.allowed_classes', [
            'App\Jobs\SampleJob'
        ]);

        // Define the job details
        $className = 'App\Jobs\SampleJob';
        $method = 'execute';
        $parameters = ['param1' => 'value1'];

        // Mock the Log::info method to expect a specific message when the job is running
        Log::shouldReceive('info')->once()->withArgs(function ($message) {
            return str_contains($message, 'Job running') && str_contains($message, 'SampleJob');
        });

        // Mock the Log::info method to expect a specific message when the job is completed successfully
        Log::shouldReceive('info')->once()->withArgs(function ($message) {
            return str_contains($message, 'Job completed successfully') && str_contains($message, 'SampleJob');
        });

        // Dispatch the job using the BackgroundJobRunner
        BackgroundJobRunner::dispatch($className, $method, $parameters);
    }

    /**
     * This test method verifies that the BackgroundJobRunner logs an error when an invalid job class or method is provided.
     *
     * @return void
     */
    public function it_logs_error_for_invalid_class_or_method()
    {
        // Set the allowed job classes in the configuration
        Config::set('background_jobs.allowed_classes', [
            'App\Jobs\AllowedJob'
        ]);

        // Define the job details
        $className = 'App\Jobs\InvalidJob'; // The class name of the job to be dispatched
        $method = 'nonExistentMethod'; // The method name of the job to be executed
        $parameters = ['param1' => 'value1']; // The parameters to be passed to the job method

        // Mock the Log::error method to expect a specific error message when an invalid job class or method is provided
        Log::shouldReceive('error')->once()->withArgs(function ($message) {
            return str_contains($message, 'Unauthorized or invalid job class/method') && str_contains($message, 'InvalidJob');
        });

        // Dispatch the job using the BackgroundJobRunner
        BackgroundJobRunner::dispatch($className, $method, $parameters);
    }

    /**
     * This test method verifies that the BackgroundJobRunner respects priority settings when dispatching a job.
     *
     * @return void
     */
    public function it_respects_priority_settings()
    {
        // Set the allowed job classes in the configuration
        Config::set('background_jobs.allowed_classes', [
            'App\Jobs\SampleJob'
        ]);

        // Define the job details
        $className = 'App\Jobs\SampleJob'; // The class name of the job to be dispatched
        $method = 'execute'; // The method name of the job to be executed
        $parameters = ['param1' => 'value1']; // The parameters to be passed to the job method
        $priority = 2; // The priority of the job (lower values indicate higher priority)

        // Mock the Log::info method to expect a specific message when the job is running
        Log::shouldReceive('info')->once()->withArgs(function ($message) {
            return str_contains($message, 'Job running') && str_contains($message, 'SampleJob');
        });

        // Mock the Log::info method to expect a specific message when the job is completed successfully
        Log::shouldReceive('info')->once()->withArgs(function ($message) {
            return str_contains($message, 'Job completed successfully') && str_contains($message, 'SampleJob');
        });

        // Dispatch the job using the BackgroundJobRunner with the specified priority
        BackgroundJobRunner::dispatch($className, $method, $parameters, 3, 0, $priority);
    }
}
