<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\BackgroundJobRunner;
use App\Jobs\SampleJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Mockery;

class BackgroundJobRunnerTest extends TestCase
{
    /**
     * Set up the test environment.
     *
     * This method is called before each test case and is used to reset the state of the application
     * and prepare it for testing. It also spies on the log to assert log messages.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        Log::spy(); // Spy on the log to assert log messages
    }

    /**
     * This test case verifies that an approved job is successfully run by the BackgroundJobRunner.
     *
     * @return void
     */
    public function it_runs_an_approved_job_successfully()
    {
        // Configure allowed classes to include SampleJob
        Config::set('background_jobs.allowed_classes', [SampleJob::class]);

        // Mock the SampleJob class and expect 'execute' method to be called once
        $jobMock = Mockery::mock(SampleJob::class);
        $jobMock->shouldReceive('execute')->once();

        // Run the job with the specified class, method, and parameters
        BackgroundJobRunner::run(SampleJob::class, 'execute', ['Hello, Test!']);

        // Assert that the job logs a successful run message
        Log::shouldHaveReceived('info')->withArgs(function ($message) {
            return str_contains($message, 'Job executed successfully');
        });
    }

    /**
     * This test case verifies that an unapproved job is not run by the BackgroundJobRunner.
     *
     * @return void
     */
    public function it_does_not_run_unapproved_jobs()
    {
        // Configure allowed classes to exclude SampleJob
        Config::set('background_jobs.allowed_classes', []);

        // Run the job with the specified class, method, and parameters
        BackgroundJobRunner::run(SampleJob::class, 'execute', ['Hello, Test!']);

        // Assert that an unauthorized log message is generated
        Log::shouldHaveReceived('error')->withArgs(function ($message) {
            return str_contains($message, 'Unauthorized class attempted to run');
        });
    }

    /**
     * This test case verifies that the BackgroundJobRunner retries a failed job.
     *
     * @return void
     */
    public function it_retries_on_failure()
    {
        // Configure allowed classes to include SampleJob
        Config::set('background_jobs.allowed_classes', [SampleJob::class]);

        // Mock the SampleJob class and force it to throw an exception on execution
        $jobMock = Mockery::mock(SampleJob::class);
        $jobMock->shouldReceive('execute')->andThrow(new \Exception('Simulated failure'));

        // Run the job with retries
        BackgroundJobRunner::run(SampleJob::class, 'execute', ['Hello, Test!'], 2);

        // Assert that error log was called multiple times due to retries
        Log::shouldHaveReceived('error')->withArgs(function ($message) {
            return str_contains($message, 'Job failed');
        })->times(3); // Initial attempt + 2 retries
    }
}
