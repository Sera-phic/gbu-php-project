<?php
/**
 * Common helper functions for the registration portal.
 */

/**
 * Sanitize user input to prevent XSS.
 *
 * @param string $data The input string.
 * @return string The sanitized string.
 */
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

/**
 * Redirect to a specific URL.
 *
 * @param string $url The target URL.
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Check if the admin is logged in.
 */
function check_admin_auth() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        redirect('login.php');
    }
}

/**
 * Set a session flash message.
 */
function set_flash_message($message, $type = 'info') {
    $_SESSION['flash_message'] = [
        'text' => $message,
        'type' => $type
    ];
}

/**
 * Display the flash message if set.
 */
function display_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        echo "<div class='alert alert-{$msg['type']}'>{$msg['text']}</div>";
        unset($_SESSION['flash_message']);
    }
}
?>
