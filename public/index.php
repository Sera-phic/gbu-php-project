<?php

declare(strict_types=1);

// Load Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Load application config (session hardening + env vars)
require_once dirname(__DIR__) . '/config/app.php';

// HTTPS redirect — enforce TLS in production
if (APP_ENV !== 'development') {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

    if (!$isHttps) {
        $host = $_SERVER['HTTP_HOST'] ?? parse_url(APP_URL, PHP_URL_HOST);
        $uri  = $_SERVER['REQUEST_URI'] ?? '/';
        header('Location: https://' . $host . $uri, true, 301);
        exit;
    }
}

// Start session with hardened settings applied in config/app.php
session_start();

// Bootstrap CSRF middleware
require_once dirname(__DIR__) . '/config/database.php';

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\PaymentController;
use App\Controllers\RegistrationController;
use App\Controllers\StudentPortalController;
use App\Middleware\AdminMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\SessionMiddleware;
use App\Services\NotificationService;
use App\Services\PaymentVerificationService;
use Razorpay\Api\Api as RazorpayApi;

// -------------------------------------------------------------------------
// Bootstrap services
// -------------------------------------------------------------------------
$csrf    = new CsrfMiddleware();
$csrf->bootstrap();

$session = new SessionMiddleware();
$admin   = new AdminMiddleware();

$db           = getAppDb();
$accountsDb   = getAccountsDb();
$notification = new NotificationService();
$razorpay     = new RazorpayApi(
    $_ENV['RAZORPAY_KEY_ID']     ?? getenv('RAZORPAY_KEY_ID'),
    $_ENV['RAZORPAY_KEY_SECRET'] ?? getenv('RAZORPAY_KEY_SECRET')
);

$authController         = new AuthController($db, $notification);
$portalController       = new StudentPortalController($db);
$registrationController = new RegistrationController($db);
$verificationService    = new PaymentVerificationService($db, $accountsDb, $razorpay, $notification);
$paymentController      = new PaymentController($db, $verificationService, $razorpay);
$adminController        = new AdminController($db, $verificationService, $notification);

// -------------------------------------------------------------------------
// Router
// -------------------------------------------------------------------------
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri    = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');

// Validate CSRF on all POST requests
if ($method === 'POST') {
    $csrf->validate();
}

// Helper to render a view
function renderView(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require dirname(__DIR__) . '/app/Views/' . $view . '.php';
}

// -------------------------------------------------------------------------
// Public routes (no session required)
// -------------------------------------------------------------------------
match (true) {

    // Sign-up
    $uri === '/signup' && $method === 'GET' => renderView('auth/signup'),
    $uri === '/signup' && $method === 'POST' => (function () use ($authController) {
        $result = $authController->signUp($_POST);
        if ($result['success']) {
            header('Location: /login?registered=1', true, 302);
            exit;
        }
        renderView('auth/signup', ['errors' => $result['errors']]);
    })(),

    // Login
    $uri === '/login' && $method === 'GET' => renderView('auth/login'),
    $uri === '/login' && $method === 'POST' => (function () use ($authController) {
        $result = $authController->login($_POST['roll_no'] ?? '', $_POST['mobile'] ?? '');
        if ($result['success']) {
            $_SESSION['pending_roll_no'] = $_POST['roll_no'] ?? '';
            header('Location: /otp', true, 302);
            exit;
        }
        renderView('auth/login', ['error' => $result['message']]);
    })(),

    // OTP verification
    $uri === '/otp' && $method === 'GET' => renderView('auth/otp'),
    $uri === '/otp' && $method === 'POST' => (function () use ($authController) {
        $rollNo = $_SESSION['pending_roll_no'] ?? '';
        $result = $authController->verifyOtp($rollNo, $_POST['otp'] ?? '');
        if ($result['success']) {
            unset($_SESSION['pending_roll_no']);
            header('Location: /portal', true, 302);
            exit;
        }
        renderView('auth/otp', ['error' => $result['message']]);
    })(),

    // Logout
    $uri === '/logout' => (function () use ($authController) {
        $authController->logout();
        header('Location: /login', true, 302);
        exit;
    })(),

    // -------------------------------------------------------------------------
    // Protected student routes
    // -------------------------------------------------------------------------

    // Student portal dashboard
    $uri === '/portal' && $method === 'GET' => (function () use ($session, $portalController) {
        $session->handle();
        $data = $portalController->dashboard((int) $_SESSION['student_id']);
        renderView('portal/dashboard', $data);
    })(),

    // Registration form
    $uri === '/register' && $method === 'GET' => (function () use ($session, $registrationController) {
        $session->handle();
        $studentId  = (int) $_SESSION['student_id'];
        $semesterId = (int) ($_GET['semester'] ?? 0);
        $data       = $registrationController->getRegistrationForm($studentId, $semesterId);
        renderView('registration/form', $data);
    })(),

    $uri === '/register' && $method === 'POST' => (function () use ($session, $registrationController) {
        $session->handle();
        $result = $registrationController->submitRegistration((int) $_SESSION['student_id'], $_POST);
        if ($result['success']) {
            header('Location: /payment?reg=' . $result['registration_id'], true, 302);
            exit;
        }
        renderView('registration/form', ['error' => $result['error']]);
    })(),

    // Payment selection
    $uri === '/payment' && $method === 'GET' => (function () use ($session, $paymentController) {
        $session->handle();
        $registrationId = (int) ($_GET['reg'] ?? 0);
        $status         = $paymentController->getPaymentStatus($registrationId);
        renderView('payment/select', ['registration_id' => $registrationId, 'status' => $status]);
    })(),

    // Initiate Razorpay payment
    $uri === '/payment/initiate' && $method === 'POST' => (function () use ($session, $paymentController) {
        $session->handle();
        $result = $paymentController->initiatePayment(
            (int) ($_POST['registration_id'] ?? 0),
            $_POST['method'] ?? 'razorpay'
        );
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    })(),

    // Razorpay callback
    $uri === '/payment/callback' && $method === 'POST' => (function () use ($paymentController) {
        $result = $paymentController->handleGatewayCallback($_POST);
        if ($result['success']) {
            header('Location: /portal?payment=success', true, 302);
            exit;
        }
        renderView('payment/select', ['error' => $result['error'] ?? 'Payment failed']);
    })(),

    // Bank transfer submission
    $uri === '/payment/bank-transfer' && $method === 'POST' => (function () use ($session, $paymentController) {
        $session->handle();
        $result = $paymentController->submitBankTransfer(
            (int) ($_POST['registration_id'] ?? 0),
            $_POST,
            $_FILES['receipt'] ?? null
        );
        if ($result['success']) {
            header('Location: /portal?payment=submitted', true, 302);
            exit;
        }
        renderView('payment/bank_transfer', ['error' => $result['error'] ?? 'Submission failed']);
    })(),

    // -------------------------------------------------------------------------
    // Admin routes
    // -------------------------------------------------------------------------

    $uri === '/admin' && $method === 'GET' => (function () use ($session, $admin, $adminController) {
        $session->handle();
        $admin->handle();
        $registrations = $adminController->getPendingRegistrations();
        renderView('admin/dashboard', ['registrations' => $registrations]);
    })(),

    $uri === '/admin/detail' && $method === 'GET' => (function () use ($session, $admin, $adminController) {
        $session->handle();
        $admin->handle();
        $data = $adminController->getRegistrationDetail((int) ($_GET['id'] ?? 0));
        renderView('admin/detail', $data);
    })(),

    $uri === '/admin/approve' && $method === 'POST' => (function () use ($session, $admin, $adminController) {
        $session->handle();
        $admin->handle();
        $result = $adminController->approveRegistration(
            (int) ($_POST['registration_id'] ?? 0),
            (int) ($_SESSION['student_id']   ?? 0)
        );
        header('Location: /admin?action=' . ($result['success'] ? 'approved' : 'error'), true, 302);
        exit;
    })(),

    $uri === '/admin/reject' && $method === 'POST' => (function () use ($session, $admin, $adminController) {
        $session->handle();
        $admin->handle();
        $result = $adminController->rejectRegistration(
            (int) ($_POST['registration_id'] ?? 0),
            (int) ($_SESSION['student_id']   ?? 0),
            trim($_POST['reason'] ?? '')
        );
        header('Location: /admin?action=' . ($result['success'] ? 'rejected' : 'error'), true, 302);
        exit;
    })(),

    // 404 fallback
    default => (function () {
        http_response_code(404);
        echo '<h1>404 Not Found</h1>';
    })(),
};
