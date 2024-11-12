<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\BackgroundJobRunner;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Mockery;

class BackgroundJobRunnerUnitTest extends TestCase
{
    /**
     * Sets up the test environment for the BackgroundJobRunnerUnitTest class.
     *
     * This method is called before each test case in the class. It initializes the parent setup,
     * configures the logging spy to monitor log messages, and prepares the environment for testing.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        Log::spy(); // Spy on the log for assertions
    }

    /**
     * This function tests the scenario where a job completes successfully.
     * It configures the allowed classes, mocks the job class to expect 'execute' to be called,
     * runs the job, and asserts that an info log message with 'Job executed successfully' is created.
     *
     * @return void
     */
    public function it_logs_success_when_job_completes()
    {
        // Configure allowed classes
        Config::set('background_jobs.allowed_classes', ['App\Jobs\SampleJob']);

        // Mock the SampleJob class and expect 'execute' to be called
        $jobMock = Mockery::mock('App\Jobs\SampleJob');
        $jobMock->shouldReceive('execute')->once();

        // Run the job
        BackgroundJobRunner::run('App\Jobs\SampleJob', 'execute');

        // Assert that a success log message was created
        Log::shouldHaveReceived('info')->withArgs(function ($message) {
            return str_contains($message, 'Job executed successfully');
        });
    }

    /**
     * This function tests the scenario where a job fails to execute.
     * It configures the allowed classes, mocks the job class to throw an exception,
     * runs the job, and asserts that an error log message is created.
     *
     * @return void
     */
    public function it_logs_error_when_job_fails()
    {
        // Configure allowed classes
        Config::set('background_jobs.allowed_classes', ['App\Jobs\SampleJob']);

        // Mock the SampleJob class and force it to throw an exception
        $jobMock = Mockery::mock('App\Jobs\SampleJob');
        $jobMock->shouldReceive('execute')->andThrow(new \Exception('Simulated failure'));

        // Run the job
        BackgroundJobRunner::run('App\Jobs\SampleJob', 'execute', [], 0);

        // Assert that an error log was created
        Log::shouldHaveReceived('error')->withArgs(function ($message) {
            return str_contains($message, 'Job failed') && str_contains($message, 'Simulated failure');
        });
    }

    /**
     * This function checks if a given job class is approved to run based on the configuration.
     * If the class is not approved, it logs an error message.
     *
     * @param string $jobClass The fully qualified name of the job class to be checked.
     * @param string $method The method of the job class to be executed.
     * @param array $parameters The parameters to be passed to the job method.
     * @param int $timeout The maximum execution time for the job in seconds.
     *
     * @return void
     */
    public function it_checks_if_class_is_not_approved()
    {
        // Configure allowed classes to exclude the job
        Config::set('background_jobs.allowed_classes', []);

        // Run the job
        BackgroundJobRunner::run('App\Jobs\SampleJob', 'execute');

        // Assert that an unauthorized log was created
        Log::shouldHaveReceived('error')->withArgs(function ($message) {
            return str_contains($message, 'Unauthorized class attempted to run');
        });
    }
}
