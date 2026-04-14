<?php

declare(strict_types=1);

/**
 * Database connection factory.
 * Returns PDO instances for the Application DB and the read-only Accounts DB.
 */

function getAppDb(): PDO
{
    static $appDb = null;

    if ($appDb === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $_ENV['DB_HOST'] ?? getenv('DB_HOST'),
            $_ENV['DB_NAME'] ?? getenv('DB_NAME')
        );

        $appDb = new PDO(
            $dsn,
            $_ENV['DB_USER'] ?? getenv('DB_USER'),
            $_ENV['DB_PASS'] ?? getenv('DB_PASS'),
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    return $appDb;
}

function getAccountsDb(): PDO
{
    static $accountsDb = null;

    if ($accountsDb === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $_ENV['ACCOUNTS_DB_HOST'] ?? getenv('ACCOUNTS_DB_HOST'),
            $_ENV['ACCOUNTS_DB_NAME'] ?? getenv('ACCOUNTS_DB_NAME')
        );

        $accountsDb = new PDO(
            $dsn,
            $_ENV['ACCOUNTS_DB_USER'] ?? getenv('ACCOUNTS_DB_USER'),
            $_ENV['ACCOUNTS_DB_PASS'] ?? getenv('ACCOUNTS_DB_PASS'),
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );

        // Enforce read-only by setting session to read-only transaction mode
        $accountsDb->exec("SET SESSION TRANSACTION READ ONLY");
    }

    return $accountsDb;
}
