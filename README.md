# Background Job Runner Documentation

This background job runner allows you to execute PHP classes and methods as background jobs within a Laravel application. It operates independently of Laravel's built-in queue system, providing scalability, error handling, and configurability for running background tasks. This application uses Laravel with Laravel Breeze for authentication and SQLite for quick setup.

---

## Table of Contents

- [How the `runBackgroundJob` Function Works](#how-the-runbackgroundjob-function-works)
- [Function Parameters](#function-parameters)
- [Setup and Installation](#setup-and-installation)
- [Usage](#usage)
- [Configuration](#configuration)
  - [Retry Attempts and Delays](#retry-attempts-and-delays)
  - [Security Settings](#security-settings)
- [Logging and Error Handling](#logging-and-error-handling)
- [Running Test Cases](#running-test-cases)
- [Assumptions, Limitations, and Potential Improvements](#assumptions-limitations-and-potential-improvements)

---

## How the `runBackgroundJob` Function Works

The `runBackgroundJob` function serves as a helper to initiate a background job in Laravel. It accepts a class name, method, parameters, and retry attempts, then calls `BackgroundJobRunner` to execute the specified method in the background.

## Function Parameters

- **`$className`** *(string)*: Fully qualified name of the class to run (e.g., `App\Jobs\SampleJob`).
- **`$method`** *(string)*: Name of the method to execute within the class.
- **`$parameters`** *(array)*: Parameters to pass to the method.
- **`$retries`** *(int)*: Number of retry attempts if the job fails.

---

## Setup and Installation

### Prerequisites

- PHP 8.2+
- Composer
- Laravel CLI

### Cloning the Repository

1. **Clone the repository**:

    ```bash
    git clone place_holder_coming
    cd your-repository-name
    ```

2. **Install dependencies**:

    ```bash
    composer install
    ```

3. **Configure Environment**:

    - Copy `.env.example` to `.env`:

      ```bash
      cp .env.example .env
      ```

    - Update the `.env` file with any necessary configuration, especially `APP_KEY`, by running:

      ```bash
      php artisan key:generate
      ```

4. **Database Setup**:

    - This application uses an SQLite database by default for simplicity. Laravel will look for a `database/database.sqlite` file.
    - Create an SQLite database file:

      ```bash
      touch database/database.sqlite
      ```

    - In your `.env` file, configure the database settings as follows:

      ```plaintext
      DB_CONNECTION=sqlite
      DB_DATABASE=/full/path/to/your-project/database/database.sqlite
      ```

5. **Run Migrations**:

    ```bash
    php artisan migrate
    ```

### Running the Application

1. **Start the Laravel development server**:

    ```bash
    php artisan serve
    ```

2. **Authentication with Laravel Breeze**:

    - This application is set up with Laravel Breeze for basic authentication. After setting up the database, you can register users and access authenticated routes.

---

## Usage

### Running Background Jobs

The `runBackgroundJob` function takes four arguments:

- **`$className`**: Fully qualified class name (e.g., `App\Jobs\SampleJob`).
- **`$method`**: The method to execute within the class.
- **`$parameters`**: Parameters to pass to the method.
- **`$retries`**: Number of retry attempts if the job fails.

#### Example Routes

1. **Basic Job Execution**:

    ```php
    Route::get('/test-background-job', function () {
        runBackgroundJob('App\Jobs\SampleJob', 'execute', ['Hello, Background Job!'], 3);
        return 'Background job dispatched!';
    });
    ```

---

## Configuration

### Retry Attempts and Delays

1. **Retry Attempts**:
   - Specify retry attempts using the `$retries` parameter:

     ```php
     runBackgroundJob('App\Jobs\SampleJob', 'execute', ['Hello, Background Job!'], 5);
     ```
     This job will retry up to 5 times if it encounters an error.

2. **Delay Between Retries**:
   - A 5-second delay is set between retries in `BackgroundJobRunner`. Adjust this by modifying `sleep(5);` in the `run` method.

### Security Settings

To prevent unauthorized jobs from running:

- **Allowed Classes**: Define an `allowed_classes` array in `config/background_jobs.php`:

    ```php
    return [
        'allowed_classes' => [
            'App\Jobs\SampleJob',
            'App\Jobs\OrderJob',
        ],
    ];
    ```

- **Validation**: The system validates `$className` and `$method` to ensure they exist and are approved.

---

## Logging and Error Handling

Each job execution is logged with details:

- **Running**: Logged when the job starts.
- **Success**: Logged upon completion.
- **Failure**: Logs include error messages and details.

Each log entry contains:

- **Class name**
- **Method name**
- **Parameters**
- **Status** (running, completed, failed)
- **Timestamp**

### Example Log Entries

```plaintext
Job running | Class: App\Jobs\SampleJob | Method: execute | Parameters: {"param1":"Hello, Background Job!"} | Timestamp: 2024-11-12 12:00:00
```

```
Job completed successfully | Class: App\Jobs\SampleJob | Method: execute | Parameters: {"param1":"Hello, Background Job!"} | Timestamp: 2024-11-12 12:00:05
```

Running Test Cases
------------------

### Running the Tests

To run all tests in the application, use:

`php artisan test`

Alternatively, you can run PHPUnit directly:


`vendor/bin/phpunit`

### Test Files for `BackgroundJobRunner`

-   **Feature Tests**: Located in `tests/Feature/BackgroundJobRunnerTest.php`. These tests check the integration of the job runner with the Laravel environment, including logging and retry functionality.
-   **Unit Tests**: Located in `tests/Unit/BackgroundJobRunnerUnitTest.php`. These tests focus on the isolated behavior of the `BackgroundJobRunner` class, validating individual methods and responses.

### Example Test Cases

1.  **Feature Test** (`BackgroundJobRunnerTest.php`):

    -   Runs approved jobs and logs success.
    -   Prevents unapproved jobs from executing.
    -   Retries jobs on failure and logs each attempt.
2.  **Unit Test** (`BackgroundJobRunnerUnitTest.php`):

    -   Logs success when the job completes successfully.
    -   Logs error details when a job fails.
    -   Prevents unauthorized job classes from executing.

These tests ensure that the `BackgroundJobRunner` behaves as expected under various scenarios.

* * * * *

Assumptions, Limitations, and Potential Improvements
----------------------------------------------------

### Assumptions

-   Classes and methods provided for background jobs are approved and defined in `config/background_jobs.php`.
-   A default 5-second delay is sufficient for retries.

### Limitations

-   **Delay Control**: The retry delay is hardcoded. To support flexible delays, consider moving delay times to the configuration file.
-   **Priority Handling**: Currently, there's no built-in priority processing in `BackgroundJobRunner`.

### Potential Improvements

1.  **Enhanced Job Dashboard**: A frontend dashboard could provide a real-time view of job status, including logs and error details.
2.  **Flexible Delay Configuration**: Allow configurable delays by adding a `retry_delay` setting in `config/background_jobs.php`.
3.  **Prioritized Job Queue**: Implement priority handling to ensure high-priority jobs execute first.