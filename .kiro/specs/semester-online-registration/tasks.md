# Implementation Plan: Semester Online Registration System

## Overview

Implement a PHP/MySQL MVC web application that digitizes the university semester registration and payment process. The implementation follows the layered architecture defined in the design: AuthController → StudentPortalController → RegistrationController → PaymentController → PaymentVerificationService → AdminController, backed by a MySQL application database and a read-only Accounts Department database.

## Tasks

- [x] 1. Set up project structure, database schema, and core infrastructure
  - Create Composer-managed PHP project with directory structure: `app/Controllers/`, `app/Services/`, `app/Models/`, `app/Views/`, `config/`, `public/`, `tests/`
  - Write `composer.json` requiring PHP 8.1+, Razorpay PHP SDK, PHPMailer, and PHPUnit
  - Create `config/database.php` with PDO connection factory for Application_DB and read-only Accounts_DB
  - Create `config/app.php` for session hardening settings (`httponly`, `secure`, `use_strict_mode`)
  - Create `public/index.php` front controller with HTTPS redirect and CSRF middleware bootstrap
  - Write all five SQL migration files: `students`, `otp_tokens`, `registrations`, `payments`, `admin_actions`
  - Add DB indexes on `college_id` and `transfer_date` for Accounts_DB query performance
  - _Requirements: 10.1, 10.3, 10.5, 13.2_

- [-] 2. Implement AuthController — sign-up
  - [x] 2.1 Implement `signUp()` in `AuthController`
    - Validate College ID against Master_List using PDO prepared statement
    - Check for duplicate College ID in `students` table
    - Validate password match and 10-digit Indian mobile format
    - Hash password with `password_hash()` at bcrypt cost ≥ 12
    - Insert student record with `is_active = 1`; trigger `NotificationService::sendWelcomeSms()`
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 1.7_

  - [~] 2.2 Write property test for sign-up rejection of invalid College IDs
    - **Property 1: Invalid College ID always rejected at sign-up**
    - **Validates: Requirements 1.1**

  - [~] 2.3 Write property test for duplicate College ID rejection
    - **Property 2: Duplicate College ID always rejected at sign-up**
    - **Validates: Requirements 1.2**

  - [~] 2.4 Write property test for invalid sign-up inputs
    - **Property 3: Invalid sign-up inputs always rejected**
    - **Validates: Requirements 1.3, 1.4**

  - [~] 2.5 Write property test for password storage
    - **Property 4: Passwords are never stored in plaintext**
    - **Validates: Requirements 1.6, 10.7**

- [-] 3. Implement AuthController — MFA login and OTP
  - [x] 3.1 Implement `generateNumericOtp()` helper
    - Use `random_int()` to generate a 6-digit numeric string
    - _Requirements: 12.1, 12.2_

  - [~] 3.2 Write property test for OTP format invariant
    - **Property 7: OTP format invariant**
    - **Validates: Requirements 2.5, 12.1**

  - [x] 3.3 Implement `login()` in `AuthController`
    - Look up student by roll number; verify mobile match and `is_active = 1`
    - Enforce OTP rate limit: count `otp_tokens` rows for student in last 10 minutes; block if ≥ 3
    - Generate OTP via `generateNumericOtp(6)`, store bcrypt hash with 5-minute `expires_at` in `otp_tokens`
    - Call `NotificationService::sendOtp()` to deliver OTP via SMS
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 10.6, 10.8, 12.3_

  - [~] 3.4 Write property test for invalid login credentials rejection
    - **Property 5: Invalid login credentials always rejected**
    - **Validates: Requirements 2.1, 2.2, 2.3**

  - [~] 3.5 Write property test for OTP rate limiting
    - **Property 6: OTP rate limiting enforced**
    - **Validates: Requirements 2.4, 10.6**

  - [~] 3.6 Write property test for OTP plaintext storage
    - **Property 8: OTP is never stored in plaintext**
    - **Validates: Requirements 2.5, 12.3, 10.8**

  - [x] 3.7 Implement `verifyOtp()` in `AuthController`
    - Fetch latest unused, unexpired OTP token for student
    - Verify submitted OTP against stored bcrypt hash
    - Mark token `used = 1`; create PHP session with `student_id` and `role = 'student'`
    - Regenerate session ID on session creation; configure 30-minute inactivity timeout
    - _Requirements: 2.6, 2.7, 2.8, 2.9, 2.10, 10.4, 10.5_

  - [~] 3.8 Write property test for expired OTP rejection
    - **Property 9: Expired OTP always rejected**
    - **Validates: Requirements 2.6, 12.5**

  - [~] 3.9 Write property test for OTP reuse prevention
    - **Property 10: Used OTP cannot be reused**
    - **Validates: Requirements 2.8, 12.4**

- [x] 4. Checkpoint — Ensure all authentication tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [-] 5. Implement session middleware, CSRF protection, and role-based access control
  - [x] 5.1 Implement session middleware
    - Create `Middleware/SessionMiddleware.php` that checks for active session on protected routes
    - Redirect unauthenticated requests to login page
    - _Requirements: 3.3, 10.10_

  - [x] 5.2 Implement CSRF middleware
    - Generate and store CSRF token in session; inject into all forms via a view helper
    - Validate CSRF token on every POST request; reject and abort if missing or mismatched
    - _Requirements: 10.2_

  - [~] 5.3 Write property test for CSRF protection
    - **Property 23: CSRF protection rejects requests without valid token**
    - **Validates: Requirements 10.2**

  - [x] 5.4 Implement admin role guard
    - Create `Middleware/AdminMiddleware.php` that checks `session.role === 'admin'`; deny and redirect otherwise
    - _Requirements: 8.6, 10.10_

  - [~] 5.5 Write property test for non-admin session access denial
    - **Property 24: Non-admin sessions cannot access admin routes**
    - **Validates: Requirements 8.6, 10.10**

- [-] 6. Implement StudentPortalController
  - [x] 6.1 Implement `dashboard()` and `getSemesterHistory()` in `StudentPortalController`
    - Fetch student profile and semester history from Application_DB using PDO prepared statements
    - Cache query results in session with 5-minute TTL
    - _Requirements: 3.1, 3.2, 3.4, 13.4_

  - [x] 6.2 Implement `getRegistrationStatus()` in `StudentPortalController`
    - Fetch current registration and payment status for the active semester
    - _Requirements: 3.2_

  - [~] 6.3 Write unit tests for StudentPortalController
    - Test dashboard data aggregation, cache hit/miss behavior, and status display
    - _Requirements: 3.1, 3.2, 3.4_

- [-] 7. Implement RegistrationController
  - [x] 7.1 Implement `checkEligibility()` in `RegistrationController`
    - Check semester match, existing non-rejected registration, and pending dues — all read-only queries
    - Return `eligible: bool` and `reason: string` without mutating any DB row
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

  - [~] 7.2 Write property test for eligibility check read-only invariant
    - **Property 11: Eligibility check is read-only (pure function)**
    - **Validates: Requirements 4.5**

  - [~] 7.3 Write property test for ineligible student registration block
    - **Property 12: Ineligible students cannot register**
    - **Validates: Requirements 4.1, 4.2, 4.3, 5.3**

  - [x] 7.4 Implement `submitRegistration()` in `RegistrationController`
    - Re-run eligibility check before persisting; reject if ineligible
    - Insert registration record with `status = 'pending_payment'`, `submitted_at = NOW()`, subjects as JSON
    - Redirect student to payment step
    - _Requirements: 5.1, 5.2, 5.3, 5.4_

  - [~] 7.5 Write property test for registration initial state
    - **Property 13: Registration submission creates correct initial state**
    - **Validates: Requirements 5.1, 5.2**

  - [~] 7.6 Write property test for subjects JSON round-trip
    - **Property 14: Subjects JSON round-trip**
    - **Validates: Requirements 5.4**

- [x] 8. Checkpoint — Ensure all registration tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [-] 9. Implement PaymentController
  - [x] 9.1 Implement `initiatePayment()` for UPI/Razorpay in `PaymentController`
    - Verify registration is in `pending_payment` status before proceeding
    - Integrate Razorpay PHP SDK to create an order and redirect student to gateway
    - _Requirements: 6.1, 6.5_

  - [x] 9.2 Implement `handleGatewayCallback()` in `PaymentController`
    - Validate Razorpay callback signature; record `transaction_ref` in `payments` table
    - Update registration status to `payment_submitted`; trigger `PaymentVerificationService::verify()`
    - _Requirements: 6.2, 6.4_

  - [x] 9.3 Implement `submitBankTransfer()` in `PaymentController`
    - Verify registration is in `pending_payment` status
    - Validate uploaded receipt file MIME type (PDF/JPEG/PNG); store file outside web root
    - Persist bank transfer details in `payments` table; update registration to `payment_submitted`
    - Trigger `PaymentVerificationService::verify()`
    - _Requirements: 6.3, 6.4, 6.5, 6.6, 10.9_

  - [~] 9.4 Write property test for payment submission status guard
    - **Property 15: Payment submission only allowed from pending_payment status**
    - **Validates: Requirements 6.5**

  - [~] 9.5 Write unit tests for PaymentController
    - Test Razorpay redirect, callback recording, bank transfer validation, and file upload rejection for invalid MIME types
    - _Requirements: 6.1, 6.2, 6.3, 6.6_

- [-] 10. Implement PaymentVerificationService
  - [x] 10.1 Implement `verify()` for UPI/Razorpay in `PaymentVerificationService`
    - Query Razorpay SDK for payment status using stored `transaction_ref`
    - If `captured`: update `payments.verification_status = 'verified'` and `registrations.status = 'payment_verified'`
    - If not `captured`: return `verified: false` without modifying registration
    - _Requirements: 7.1, 7.2_

  - [~] 10.2 Write property test for gateway payment verification determinism
    - **Property 16: Gateway payment verification outcome is deterministic**
    - **Validates: Requirements 7.1, 7.2**

  - [x] 10.3 Implement `verify()` for bank transfer in `PaymentVerificationService`
    - Query Accounts_DB via read-only PDO connection using college ID, amount, and ±2-day date range
    - If match found: update statuses to `verified`/`payment_verified`; call `NotificationService::sendStatusUpdate()`
    - If no match: set `verification_status = 'pending'`; handle Accounts_DB unavailability with error log and user-friendly message
    - _Requirements: 7.3, 7.4, 7.5, 7.7_

  - [ ] 10.4 Write property test for bank transfer verification criteria
    - **Property 17: Bank transfer verification matches on correct criteria**
    - **Validates: Requirements 7.3, 7.4**

  - [ ] 10.5 Write property test for Accounts_DB immutability
    - **Property 18: Accounts_DB is never mutated by verification**
    - **Validates: Requirements 7.7**

  - [x] 10.6 Implement `pollVerification()` and cron job script
    - Create `scripts/retry_verification.php` that queries all `pending` payments and calls `verify()` for each
    - Register cron job to run every 6 hours
    - _Requirements: 7.6_

- [-] 11. Implement registration status lifecycle enforcement
  - [x] 11.1 Implement `updateRegistrationStatus()` with forward-only transition guard
    - Define ordered lifecycle array: `['draft','pending_payment','payment_submitted','payment_verified','approved','rejected']`
    - Reject any update where the target status index ≤ current status index (except `rejected` as terminal)
    - Return `false` and do not mutate the record on invalid transitions
    - _Requirements: 11.1, 11.2, 11.3_

  - [ ] 11.2 Write property test for forward-only status transitions
    - **Property 22: Registration status transitions are strictly forward**
    - **Validates: Requirements 11.1, 11.2**

- [x] 12. Checkpoint — Ensure all payment and lifecycle tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [-] 13. Implement AdminController
  - [x] 13.1 Implement `getPendingRegistrations()` in `AdminController`
    - Query Application_DB for all registrations with `status = 'payment_verified'` only
    - _Requirements: 8.1_

  - [ ] 13.2 Write property test for admin dashboard filter
    - **Property 19: Admin dashboard shows only payment_verified registrations**
    - **Validates: Requirements 8.1**

  - [x] 13.3 Implement `getRegistrationDetail()` in `AdminController`
    - Fetch full student profile and payment details for a given registration ID
    - _Requirements: 8.2_

  - [x] 13.4 Implement `approveRegistration()` and `rejectRegistration()` in `AdminController`
    - Guard: reject if registration status is not `payment_verified`
    - On approve: call `updateRegistrationStatus('approved')`, insert into `admin_actions`, call `NotificationService::sendStatusUpdate()`
    - On reject: call `updateRegistrationStatus('rejected')`, record reason in `admin_actions`, notify student
    - _Requirements: 8.3, 8.4, 8.5, 8.7, 11.4_

  - [ ] 13.5 Write property test for admin approval/rejection status guard
    - **Property 20: Admin approval/rejection only allowed from payment_verified status**
    - **Validates: Requirements 8.5**

  - [ ] 13.6 Write property test for admin action recording
    - **Property 21: Admin action is fully recorded**
    - **Validates: Requirements 8.3, 8.4, 8.7, 11.4**

- [-] 14. Implement NotificationService
  - [x] 14.1 Implement `sendOtp()` and `sendStatusUpdate()` in `NotificationService`
    - Integrate SMS gateway API (MSG91 or Twilio) for OTP delivery and status updates
    - Integrate PHPMailer for email notifications as a fallback channel
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5_

  - [ ] 14.2 Write unit tests for NotificationService
    - Mock SMS gateway and email transport; verify correct message content and recipient for each notification type
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5_

- [x] 15. Implement OTP token cleanup cron job
  - Create `scripts/purge_otp_tokens.php` that deletes `otp_tokens` rows where `expires_at < NOW()`
  - Register cron job to run on a scheduled basis (e.g., hourly)
  - _Requirements: 13.3_

- [-] 16. Wire all components together via the front controller and router
  - [x] 16.1 Implement router in `public/index.php`
    - Map URL routes to controller actions; apply `SessionMiddleware` to all protected routes and `AdminMiddleware` to admin routes
    - _Requirements: 3.3, 8.6, 10.10_

  - [x] 16.2 Create view templates for all student-facing pages
    - Sign-up form, login form, OTP entry, student portal dashboard, registration form, payment selection, bank transfer form, status tracker
    - Inject CSRF token into all form templates
    - _Requirements: 10.2_

  - [x] 16.3 Create view templates for admin dashboard
    - Pending registrations list, registration detail view with approve/reject actions
    - _Requirements: 8.1, 8.2, 8.3, 8.4_

  - [ ] 16.4 Write integration tests for the full student flow
    - Test sign-up → login → OTP → portal → eligibility → registration → payment → verification → approval end-to-end using a test database with fixture data
    - Mock Razorpay SDK and Accounts_DB with known fixture records
    - _Requirements: 1.5, 2.8, 4.4, 5.1, 6.2, 7.1, 8.3_

- [x] 17. Final checkpoint — Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for a faster MVP
- Each task references specific requirements for traceability
- Checkpoints at tasks 4, 8, 12, and 17 ensure incremental validation
- Property tests use PHPUnit data providers or the `eris` library for PHP property-based testing
- Unit tests and property tests are complementary — both should be run together
- The Accounts_DB connection must always use a separate read-only PDO credential
- All DB queries must use PDO prepared statements (no raw string interpolation)
