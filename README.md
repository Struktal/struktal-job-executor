# Struktal-Job-Executor

PHP library for executing jobs in a separate PHP process

## Installation

To install this library, include it in your project using Composer:

```bash
composer require struktal/struktal-job-executor
```

## Usage

You can (optionally) set a base path for your jobs by calling the `setJobDirectory` method:

```php
\struktal\jobexecutor\JobExecutor::setJobDirectory(__DIR__ . "/jobs");
```

To execute a job in the background, call the `execute` method:

```php
\struktal\jobexecutor\JobExecutor::execute("my-job");
```

This will execute the `my-job.php` script file with the following command:

```bash
cd /path/to/your/app/jobs && php my-job.php > /dev/null 2>&1 &
```

> [!CAUTION]
> Note that both methods can throw exceptions if any of the passed parameters, or the `PHP_BINARY` constant are deemed unsafe, e.g. by checking for common patterns used in command injection attacks. However, this **must not be considered a complete safety measure against all possible attacks**. This library is designed to directly execute PHP scripts in your production environment's console, and you should therefore **never, ever pass any user input to these methods**.

## License

This software is licensed under the MIT license.
See the [LICENSE](LICENSE) file for more information.
