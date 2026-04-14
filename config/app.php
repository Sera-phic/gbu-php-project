<?php

declare(strict_types=1);

/**
 * Application bootstrap: session hardening and environment constants.
 * Must be included before session_start() is called.
 */

// Load .env file if it exists (simple key=value parser, no external dependency)
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);
        if (!isset($_ENV[$key])) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Application constants
define('APP_ENV', $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'production');
define('APP_URL', $_ENV['APP_URL'] ?? getenv('APP_URL') ?: '');

// Session hardening — must be set before session_start()
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', APP_ENV === 'production' ? '1' : '0');  // Disable in dev
ini_set('session.use_strict_mode', '1');
ini_set('session.gc_maxlifetime', '1800');  // 30 minutes
ini_set('session.cookie_samesite', 'Strict');
