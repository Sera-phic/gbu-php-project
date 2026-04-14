<?php

declare(strict_types=1);

namespace App\Middleware;

/**
 * AdminMiddleware — restricts access to admin routes.
 * Denies any session that does not carry role = 'admin'.
 *
 * Requirements: 8.6, 10.10
 * Property 24: Non-admin sessions cannot access admin routes.
 */
class AdminMiddleware
{
    /**
     * Verify the current session has admin role.
     * Redirects to the login page if the check fails.
     */
    public function handle(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /login', true, 302);
            exit;
        }
    }
}
