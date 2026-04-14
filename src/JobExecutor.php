<?php

namespace struktal\jobexecutor;

class JobExecutor {
    private static ?string $jobDirectory = null;

    /**
     * Set the directory where the job file is located
     * @param string $directory The directory path
     * @return void
     * @throws \InvalidArgumentException If the directory path contains potentially dangerous patterns
     */
    public static function setJobDirectory(string $directory): void {
        self::scanDangerousPatterns($directory);
        if (realpath($directory) === false || !is_dir($directory)) {
            throw new \InvalidArgumentException("The specified job directory does not exist or is not a directory: " . $directory);
        }

        self::$jobDirectory = $directory;
    }

    /**
     * Execute the specified PHP script file in the background
     * @param string $jobName The name of the PHP script file that should be executed
     * @return void
     * @throws \InvalidArgumentException If the job name or PHP binary path contains potentially dangerous patterns
     */
    public static function execute(string $jobName): void {
        self::scanDangerousPatterns(PHP_BINARY);
        self::scanDangerousPatterns($jobName);

        $file = "";
        $command = "";
        if (self::$jobDirectory !== null) {
            $command .= "cd " . escapeshellcmd(self::$jobDirectory) . " && ";
        } else {
            $file = ".";
        }

        if(str_ends_with($file, DIRECTORY_SEPARATOR) || str_starts_with($jobName, DIRECTORY_SEPARATOR)) {
            $file .= $jobName;
        } else {
            $file .= DIRECTORY_SEPARATOR . $jobName;
        }

        if (realpath($file) === false || !is_file($file) || !is_readable($file)) {
            throw new \InvalidArgumentException("The specified job file does not exist or is not readable: " . $file);
        }

        $command .= PHP_BINARY . " " . escapeshellcmd($jobName) . " > /dev/null 2>&1 &'";

        exec($command);
    }

    /**
     * Scan the string for potentially dangerous patterns that could lead to shell injection vulnerabilities.
     * If such patterns are found, an Exception is thrown, preventing the execution of the command.
     * @param string $string
     * @return void
     * @throws \InvalidArgumentException If the string contains potentially dangerous patterns
     */
    private static function scanDangerousPatterns(string $string): void {
        $dangerousPatterns = [
            ";",
            "&&",
            "|",
            "`",
            "$(",
            ">",
            "<"
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (str_contains($string, $pattern)) {
                throw new \InvalidArgumentException("The string contains a potentially dangerous pattern: " . $pattern);
            }
        }

        if (preg_match('/[\x00-\x1F]/', $string)) {
            throw new InvalidArgumentException("The string contains control characters, which are not allowed: " . $string);
        }
    }
}
