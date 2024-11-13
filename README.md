Simon Custom Background Job Runner
==================================

This background job runner allows you to execute PHP classes and methods as background jobs within a Laravel application. It operates independently of Laravel's built-in queue system, offering scalability, error handling, and configurable options for running background tasks. This project uses Laravel with Laravel Breeze for authentication, along with Bootstrap and Tailwind CSS for styling.

GitHub Repository: [Simon Custom Background Job Runner](https://github.com/dhehwa84/simon-custom-background-job-runner.git)

* * * * *

Table of Contents
-----------------

-   [Project Overview](#project-overview)
-   [Setup and Installation](#setup-and-installation)
-   [How the `runBackgroundJob` Function Works](#how-the-runbackgroundjob-function-works)
-   [Usage](#usage)
-   [Configuration](#configuration)
    -   [Retry Attempts and Delays](#retry-attempts-and-delays)
    -   [Security Settings](#security-settings)
-   [Logging and Error Handling](#logging-and-error-handling)
-   [Running Test Cases](#running-test-cases)
-   [Job Dashboard](#job-dashboard)
-   [Assumptions, Limitations, and Potential Improvements](#assumptions-limitations-and-potential-improvements)

* * * * *

Project Overview
----------------

The `Simon Custom Background Job Runner` enables you to run background tasks in a Laravel application with custom retry and delay mechanisms, priority handling, and a dedicated dashboard to monitor job statuses. This setup includes both Bootstrap and Tailwind CSS for styling flexibility and uses Laravel Breeze for basic authentication.

* * * * *

Setup and Installation
----------------------

### Prerequisites

-   PHP 8.2+
-   Composer
-   Laravel CLI
-   Node.js and npm

### Steps to Set Up the Project

1.  **Clone the Repository**:

    `git clone https://github.com/dhehwa84/simon-custom-background-job-runner.git`
    
    `cd simon-custom-background-job-runner`

3.  **Install PHP Dependencies**:


    `composer install`

4.  **Install Node Modules and Compile Assets**:

    `npm install`

    `npm run dev`

5.  **Configure Environment Variables**:

    -   Copy `.env.example` to `.env`:

        `cp .env.example .env`

    -   Update the `.env` file with necessary configurations, especially `APP_KEY`, by running:

        `php artisan key:generate`

6.  **Set Up the Database**:

    -   This application uses an SQLite database for simplicity. Create an SQLite file:


        `touch database/database.sqlite`

    -   Configure the database in the `.env` file:

        `DB_CONNECTION=sqlite`

7.  **Run Migrations**:

    `php artisan migrate`

8.  **Run the Application**:

    `php artisan serve`

9.  **Run Queues with Priority**:

    To ensure jobs are processed in the order of priority, start the queue worker as follows:

    `php artisan queue:work --queue=high_priority,medium_priority,low_priority`

This command will process jobs in the `high_priority` queue first, then `medium_priority`, and finally `low_priority` if the higher priority queues are empty.

After setup, you can log in using the Laravel Breeze authentication or register a new account.

* * * * *

How the `runBackgroundJob` Function Works
-----------------------------------------

The `runBackgroundJob` function is a helper that initiates background jobs by dispatching them to the `BackgroundJobRunner`. This function accepts parameters like class name, method, parameters, and retry attempts, allowing you to run background tasks with configurable options.

Usage
-----

### Running Background Jobs with the Helper Function

The `runBackgroundJob` function allows you to run background jobs by specifying the class name, method, parameters, and retry attempts.

#### Example Usage

1.  **Basic Job Execution**:

    `Route::get('/test-background-job', function () {
        runBackgroundJob('App\Jobs\SampleJob', 'execute', ['Hello, Background Job!'], 3);
        return 'Background job dispatched!';
    });`

### Using the Dashboard

Navigate to `/dashboard` to view, monitor, and manage all dispatched background jobs. The dashboard displays job details such as class name, method, parameters, status, retry count, and priority.

* * * * *

Configuration
-------------

### Retry Attempts and Delays

-   **Retry Attempts**: You can specify the number of retry attempts by setting the `$retries` parameter in `runBackgroundJob`.

-   **Delay Between Retries**: The `BackgroundJobRunner` includes a delay setting between retries. Modify the delay duration by setting `$retryDelay` in the helper function.

### Security Settings

To restrict jobs to authorized classes only, add allowed classes in `config/background_jobs.php`:

`return [
    'allowed_classes' => [
        'App\Jobs\SampleJob',
        'App\Jobs\OrderJob',
    ],
];`

This validation prevents unauthorized classes from being executed as background jobs.

* * * * *

Logging and Error Handling
--------------------------

Each job is logged at different stages:

-   **Running**: Logged when the job starts.
-   **Success**: Logged upon successful completion.
-   **Failure**: Logs include error details and the retry count.

### Example Log Entries


`Job running | Class: App\Jobs\SampleJob | Method: execute | Parameters: {"param1":"Hello, Background Job!"} | Timestamp: 2024-11-12 12:00:00`

`Job completed successfully | Class: App\Jobs\SampleJob | Method: execute | Parameters: {"param1":"Hello, Background Job!"} | Timestamp: 2024-11-12 12:00:05`

### Job Dashboard Logging

You can view job statuses, retry counts, and error messages directly on the dashboard at `/dashboard`.

* * * * *

Running Test Cases
------------------

### Running Tests

To run all tests in the application:

`php artisan test`

or run PHPUnit directly:

`vendor/bin/phpunit`

### Test Files for `BackgroundJobRunner`

-   **Feature Tests**: Located in `tests/Feature/BackgroundJobRunnerTest.php`.
-   **Unit Tests**: Located in `tests/Unit/BackgroundJobRunnerUnitTest.php`.

### Example Test Cases

1.  **Feature Test** (`BackgroundJobRunnerTest.php`): Verifies integration with Laravel.
2.  **Unit Test** (`BackgroundJobRunnerUnitTest.php`): Validates the behavior of `BackgroundJobRunner`.

* * * * *

Job Dashboard
-------------

The job dashboard provides a web interface to monitor and manage background jobs. Key functionalities include:

-   **View Job Status**: Displays jobs and their statuses (running, completed, failed).
-   **Retry Count**: Shows the number of retry attempts.
-   **Cancel Jobs**: Allows you to cancel running jobs.
-   **Job Priority**: High-priority jobs execute before lower-priority ones.

* * * * *

Assumptions, Limitations, and Potential Improvements
----------------------------------------------------

### Assumptions

-   All classes and methods are pre-approved in `config/background_jobs.php`.

### Limitations

-   **Fixed Delay Control**: Delay between retries is set manually.
-   **Priority Handling**: Limited prioritization of jobs.

### Potential Improvements

1.  **Enhanced Dashboard**: Add real-time monitoring and filtering options.
2.  **Advanced Priority Queue**: Enable more granular job prioritization.

* * * * *
