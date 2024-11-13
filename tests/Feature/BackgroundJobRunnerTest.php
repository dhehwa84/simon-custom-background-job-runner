<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ExecuteBackgroundJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class BackgroundJobRunnerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up the test environment.
     *
     * This method is called before each test case and is used to reset the state of the application
     * and prepare it for testing. It configures the notification, CSRF middleware, queue, and logging
     * for the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        // Fake the notification
        Notification::fake();

        // Disable CSRF for testing
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

        Queue::fake();
        Log::shouldReceive('channel')->andReturnSelf();
    }

    /**
     * This test case verifies that an approved job is dispatched correctly.
     *
     * @return void
     */
    public function it_dispatches_an_approved_job()
    {
        // Set up the allowed job classes for testing
        config(['background_jobs.allowed_classes' => [
            'App\Jobs\SampleJob'
        ]]);

        // Define the job details
        $className = 'App\Jobs\SampleJob';
        $method = 'execute';
        $parameters = ['param1' => 'value1'];

        // Set up expectations for logging
        Log::shouldReceive('info')->once()->withArgs(function ($message) {
            return str_contains($message, 'Job running');
        });

        // Dispatch the job using the BackgroundJobRunner
        \App\BackgroundJobRunner::dispatch($className, $method, $parameters, 3, 0, 1);

        // Verify that the job was pushed to the queue with the correct details
        Queue::assertPushed(ExecuteBackgroundJob::class, function ($job) use ($className, $method, $parameters) {
            return $job->className === $className &&
                   $job->method === $method &&
                   $job->parameters === $parameters;
        });
    }

    /**
     * This test case verifies that an unapproved job is not dispatched.
     *
     * @return void
     */
    public function it_does_not_dispatch_unapproved_job()
    {
        // Set up the allowed job classes for testing
        config(['background_jobs.allowed_classes' => [
            'App\Jobs\AllowedJob'
        ]]);

        // Define the job details
        $className = 'App\Jobs\UnapprovedJob';
        $method = 'execute';
        $parameters = ['param1' => 'value1'];

        // Set up expectations for logging
        Log::shouldReceive('error')->once()->withArgs(function ($message) {
            return str_contains($message, 'Unauthorized or invalid job class/method');
        });

        // Dispatch the job using the BackgroundJobRunner
        \App\BackgroundJobRunner::dispatch($className, $method, $parameters, 3, 0, 1);

        // Verify that the job was not pushed to the queue
        Queue::assertNotPushed(ExecuteBackgroundJob::class);
    }

    /**
     * This test case verifies that a failed job is retried with a delay.
     *
     * @return void
     */
    public function it_retries_failed_job_with_delay()
    {
        // Set up the allowed job classes for testing
        config(['background_jobs.allowed_classes' => [
            'App\Jobs\SampleJob'
        ]]);

        // Define the job details
        $className = 'App\Jobs\SampleJob';
        $method = 'execute';
        $parameters = ['param1' => 'value1'];

        // Set up expectations for logging
        // We expect an error log to be recorded 3 times
        Log::shouldReceive('error')->times(3);

        // Dispatch the job using the BackgroundJobRunner
        // The job should be retried 3 times with a delay of 5 seconds between retries
        \App\BackgroundJobRunner::dispatch($className, $method, $parameters, 3, 5, 1);

        // Verify that the job was pushed to the queue with the correct retry and backoff settings
        Queue::assertPushed(ExecuteBackgroundJob::class, function ($job) {
            return $job->retries === 3 && $job->backoff === 5;
        });
    }
}
