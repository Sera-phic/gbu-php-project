<?php

declare(strict_types=1);

namespace App\Middleware;

/**
 * SessionMiddleware — enforces authenticated session on protected routes.
 *
 * Requirements: 3.3, 10.10
 */
class SessionMiddleware
{
    /**
     * Check for an active authenticated session.
     * Redirects to the login page if the session is missing or expired.
     *
     * Call this at the top of any protected route handler.
     */
    public function handle(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Enforce 30-minute inactivity timeout (Requirement 2.9)
        if (!empty($_SESSION['last_activity'])) {
            $idle = time() - (int) $_SESSION['last_activity'];
            if ($idle > 1800) {
                $this->destroySession();
                $this->redirectToLogin();
                return;
            }
        }

        // Require authenticated session
        if (empty($_SESSION['student_id']) || empty($_SESSION['role'])) {
            $this->redirectToLogin();
            return;
        }

        // Refresh last activity timestamp
        $_SESSION['last_activity'] = time();
    }

    private function destroySession(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    private function redirectToLogin(): void
    {
        header('Location: /login', true, 302);
        exit;
    }
}
