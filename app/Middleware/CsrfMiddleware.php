<?php

declare(strict_types=1);

namespace App\Middleware;

/**
 * CsrfMiddleware — generates and validates CSRF tokens for all POST requests.
 *
 * Requirements: 10.2
 */
class CsrfMiddleware
{
    /**
     * Ensure a CSRF token exists in the session.
     * Call this on every request (already bootstrapped in public/index.php).
     */
    public function bootstrap(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    /**
     * Validate the CSRF token on POST requests.
     * Aborts with 403 if the token is missing or does not match.
     *
     * Property 23: CSRF protection rejects requests without valid token.
     */
    public function validate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $submitted = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $stored    = $_SESSION['csrf_token'] ?? '';

        if (!hash_equals($stored, $submitted)) {
            http_response_code(403);
            echo 'Forbidden: invalid CSRF token.';
            exit;
        }
    }

    /**
     * Return the current CSRF token for injection into form templates.
     */
    public function getToken(): string
    {
        return $_SESSION['csrf_token'] ?? '';
    }

    /**
     * Render a hidden CSRF input field for use in HTML forms.
     */
    public function inputField(): string
    {
        $token = htmlspecialchars($this->getToken(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }
}
