# Requirements Document

## Introduction

The Semester Online Registration System is a PHP-based web module integrated into the GBU university website that digitizes the offline semester registration and payment process. It allows enrolled students to create accounts, authenticate via multi-factor authentication, register for their current semester, submit payment details, and track approval status online. The system enforces enrollment-based access control, cross-references payments against the Accounts Department database, and provides an admin dashboard for final registration approval.

## Glossary

- **System**: The Semester Online Registration web application
- **AuthController**: The component responsible for sign-up, login, OTP generation/verification, and session management
- **StudentPortalController**: The component responsible for rendering the student dashboard and semester history
- **RegistrationController**: The component responsible for eligibility checks and registration form submission
- **PaymentController**: The component responsible for payment method routing, gateway integration, and bank transfer submission
- **PaymentVerificationService**: The component responsible for cross-referencing submitted payment data against the Accounts Department database
- **AdminController**: The component responsible for the admin dashboard, registration review, and approval/rejection actions
- **NotificationService**: The component responsible for sending SMS and email notifications to students
- **Student**: An enrolled university student who has created an account in the system
- **Admin**: A university staff member with administrative access to the system
- **College_ID**: The unique roll number or registration number assigned to an enrolled student
- **OTP**: A one-time password consisting of 6 numeric digits, valid for 5 minutes
- **Registration**: A semester registration record linking a student to a specific semester and academic year
- **Payment**: A payment record associated with a registration, submitted via UPI, Razorpay, or bank transfer
- **Accounts_DB**: The read-only Accounts Department database used for payment cross-reference
- **Application_DB**: The primary application database storing students, registrations, payments, and admin actions
- **Master_List**: The enrolled students master table used to validate College IDs at sign-up
- **Session**: A server-side PHP session issued after successful MFA, carrying student ID and role
- **Verification_Status**: The payment verification state: `pending`, `verified`, or `failed`
- **Registration_Status**: The registration lifecycle state: `draft`, `pending_payment`, `payment_submitted`, `payment_verified`, `approved`, or `rejected`

---

## Requirements

### Requirement 1: Student Account Creation (Sign-Up)

**User Story:** As an enrolled student, I want to create an account using my College ID, so that I can access the semester registration system.

#### Acceptance Criteria

1. WHEN a student submits a sign-up form with a College ID that does not exist in the Master_List, THEN THE AuthController SHALL reject the request and return the error "College ID not found. Only enrolled students may register."
2. WHEN a student submits a sign-up form with a College ID that already has an existing account, THEN THE AuthController SHALL reject the request and return the error "An account already exists for this College ID."
3. WHEN a student submits a sign-up form where the password and confirm_password fields do not match, THEN THE AuthController SHALL reject the request and return a password mismatch error.
4. WHEN a student submits a sign-up form with a mobile number that is not a valid 10-digit Indian mobile number, THEN THE AuthController SHALL reject the request and return a mobile validation error.
5. WHEN a student submits a valid sign-up form with a College ID present in the Master_List, THEN THE AuthController SHALL create a student account with `is_active = 1` and return `success: true`.
6. WHEN a student account is created, THE AuthController SHALL store the password as a bcrypt hash with a cost factor of at least 12 and SHALL NOT store the plaintext password.
7. WHEN a student account is created successfully, THE NotificationService SHALL send a welcome SMS to the student's registered mobile number.

---

### Requirement 2: Multi-Factor Authentication (Login)

**User Story:** As a registered student, I want to log in using my roll number and mobile number with OTP verification, so that my account is protected by multi-factor authentication.

#### Acceptance Criteria

1. WHEN a student submits a login request with a roll number that does not exist in the Application_DB, THEN THE AuthController SHALL return `success: false` with the message "Invalid credentials."
2. WHEN a student submits a login request with a roll number that exists but a non-matching mobile number, THEN THE AuthController SHALL return `success: false` with the message "Invalid credentials."
3. WHEN a student submits a login request for an account with `is_active = 0`, THEN THE AuthController SHALL return `success: false` with the message "Account is deactivated."
4. WHEN a student submits more than 3 OTP requests within a 10-minute window, THEN THE AuthController SHALL reject further OTP requests and return the message "Too many OTP requests. Try after 10 minutes."
5. WHEN a student submits valid login credentials and is within the OTP rate limit, THEN THE AuthController SHALL generate a 6-digit numeric OTP, store its bcrypt hash with a 5-minute expiry in the Application_DB, and send the OTP to the student's registered mobile number.
6. WHEN a student submits an OTP that has expired (past its `expires_at` timestamp), THEN THE AuthController SHALL return `success: false` with the message "OTP expired or invalid."
7. WHEN a student submits an OTP that does not match the stored hash, THEN THE AuthController SHALL return `success: false` with the message "OTP expired or invalid."
8. WHEN a student submits a valid, unexpired OTP, THEN THE AuthController SHALL mark the OTP token as used, create a PHP session with the student's ID and role set to "student", and return `success: true` with a session token.
9. WHILE a student session is active, THE AuthController SHALL expire the session after 30 minutes of inactivity.
10. WHEN a session is created or a privilege change occurs, THE AuthController SHALL regenerate the session ID to prevent session fixation.

---

### Requirement 3: Student Portal Dashboard

**User Story:** As an authenticated student, I want to view my personal dashboard with my profile and semester history, so that I can track my academic and registration records.

#### Acceptance Criteria

1. WHEN an authenticated student loads the portal dashboard, THE StudentPortalController SHALL retrieve and display the student's profile data and semester history from the Application_DB.
2. WHEN an authenticated student views the portal, THE StudentPortalController SHALL display the current registration status and payment status for the active semester.
3. IF a student attempts to access the portal without an active authenticated session, THEN THE System SHALL redirect the student to the login page.
4. WHILE a student session is active, THE StudentPortalController SHALL cache dashboard query results for up to 5 minutes to reduce database load during peak registration periods.

---

### Requirement 4: Semester Registration Eligibility

**User Story:** As an authenticated student, I want the system to check my eligibility before allowing me to register, so that only eligible students can submit a registration.

#### Acceptance Criteria

1. WHEN a student initiates semester registration and their `current_semester` does not match the requested `semesterId`, THEN THE RegistrationController SHALL return `eligible: false` with the reason "Semester mismatch."
2. WHEN a student initiates semester registration and an existing non-rejected registration record already exists for the same student, semester, and academic year, THEN THE RegistrationController SHALL return `eligible: false` with the reason "Already registered for this semester."
3. WHEN a student initiates semester registration and they have pending dues greater than zero, THEN THE RegistrationController SHALL return `eligible: false` with the reason "Pending dues of ₹{amount}."
4. WHEN a student passes all eligibility checks, THE RegistrationController SHALL return `eligible: true` and render the registration form.
5. THE RegistrationController SHALL perform eligibility checks without mutating any data in the Application_DB.

---

### Requirement 5: Semester Registration Form Submission

**User Story:** As an eligible student, I want to submit my semester registration form online, so that I can complete the registration process digitally.

#### Acceptance Criteria

1. WHEN an eligible student submits a valid registration form, THE RegistrationController SHALL persist a registration record in the Application_DB with status `pending_payment` and redirect the student to the payment step.
2. WHEN a registration form is submitted, THE RegistrationController SHALL record the `submitted_at` timestamp on the registration record.
3. IF a student attempts to submit a registration form without passing the eligibility check, THEN THE RegistrationController SHALL reject the submission and return an eligibility error.
4. THE RegistrationController SHALL store the selected subjects as a JSON array of subject codes in the registration record.

---

### Requirement 6: Payment Submission

**User Story:** As a student with a pending payment registration, I want to submit payment via UPI, Razorpay, or bank transfer, so that I can complete the financial step of registration.

#### Acceptance Criteria

1. WHEN a student selects UPI or Razorpay as the payment method, THE PaymentController SHALL redirect the student to the Razorpay payment gateway.
2. WHEN the Razorpay gateway returns a callback with a transaction ID, THE PaymentController SHALL record the transaction reference in the Application_DB and update the registration status to `payment_submitted`.
3. WHEN a student selects bank transfer as the payment method and submits bank transfer details, THE PaymentController SHALL record the bank name, account holder, transfer date, transfer amount, transaction reference, and receipt file path in the Application_DB and update the registration status to `payment_submitted`.
4. WHEN a payment record is saved, THE PaymentController SHALL trigger the PaymentVerificationService to begin verification.
5. IF a student attempts to submit payment for a registration that is not in `pending_payment` status, THEN THE PaymentController SHALL reject the submission.
6. WHEN a bank transfer receipt file is uploaded, THE System SHALL validate the file MIME type as PDF, JPEG, or PNG and store the file outside the web root.

---

### Requirement 7: Payment Verification

**User Story:** As a student who has submitted payment, I want the system to automatically verify my payment against the Accounts Department records, so that my registration can proceed to admin approval without manual intervention.

#### Acceptance Criteria

1. WHEN the PaymentVerificationService verifies a UPI or Razorpay payment and the payment gateway returns status `captured`, THE PaymentVerificationService SHALL update the payment `verification_status` to `verified` and the registration status to `payment_verified`.
2. WHEN the PaymentVerificationService verifies a UPI or Razorpay payment and the payment gateway does not return status `captured`, THE PaymentVerificationService SHALL return `verified: false` without updating the registration status.
3. WHEN the PaymentVerificationService verifies a bank transfer and a matching record is found in the Accounts_DB (matching college ID, amount, and date within a ±2-day range), THE PaymentVerificationService SHALL update the payment `verification_status` to `verified`, update the registration status to `payment_verified`, and notify the student.
4. WHEN the PaymentVerificationService verifies a bank transfer and no matching record is found in the Accounts_DB, THE PaymentVerificationService SHALL set the payment `verification_status` to `pending` and schedule a retry.
5. IF the Accounts_DB connection is unavailable during payment verification, THEN THE PaymentVerificationService SHALL log the error, set the payment status to `pending`, and return a user-friendly message "Verification is in progress. You will be notified."
6. WHILE a payment verification is in `pending` status, THE System SHALL retry verification via a scheduled cron job every 6 hours.
7. THE PaymentVerificationService SHALL query the Accounts_DB using a dedicated read-only database connection and SHALL NOT mutate any data in the Accounts_DB.

---

### Requirement 8: Admin Dashboard and Registration Approval

**User Story:** As an admin, I want to review and approve or reject verified registrations from a dashboard, so that I can complete the final step of the registration process.

#### Acceptance Criteria

1. WHEN an admin logs in to the admin dashboard, THE AdminController SHALL display all registrations with status `payment_verified`.
2. WHEN an admin opens a registration record, THE AdminController SHALL retrieve and display the full student profile and payment details from the Application_DB.
3. WHEN an admin approves a registration that is in `payment_verified` status, THE AdminController SHALL update the registration status to `approved`, log the action in `admin_actions`, and trigger a student notification.
4. WHEN an admin rejects a registration that is in `payment_verified` status, THE AdminController SHALL update the registration status to `rejected`, record the rejection reason in `admin_actions`, and trigger a student notification.
5. IF an admin attempts to approve or reject a registration that is not in `payment_verified` status, THEN THE AdminController SHALL return an error "Registration is not in a verifiable state."
6. IF a user without admin role attempts to access the admin dashboard, THEN THE System SHALL deny access and redirect to the login page.
7. WHEN an admin action is taken, THE System SHALL record the admin ID, registration ID, action type, and timestamp in the `admin_actions` table.

---

### Requirement 9: Student Notifications

**User Story:** As a student, I want to receive SMS or email notifications on key status changes, so that I am informed of my registration progress without having to check the portal manually.

#### Acceptance Criteria

1. WHEN a student account is created, THE NotificationService SHALL send a welcome SMS to the student's registered mobile number.
2. WHEN a student's payment is verified, THE NotificationService SHALL send a status update notification to the student.
3. WHEN an admin approves a student's registration, THE NotificationService SHALL send a notification with the message "Your semester registration has been approved."
4. WHEN an admin rejects a student's registration, THE NotificationService SHALL send a notification informing the student of the rejection.
5. WHEN an OTP is requested, THE NotificationService SHALL deliver the OTP to the student's registered mobile number.

---

### Requirement 10: Security and Access Control

**User Story:** As a system administrator, I want the application to enforce strict security controls, so that student data and the registration process are protected from unauthorized access and attacks.

#### Acceptance Criteria

1. THE System SHALL serve all pages exclusively over HTTPS and SHALL redirect any HTTP request to HTTPS.
2. THE System SHALL protect all POST form endpoints with a CSRF token that is validated server-side.
3. THE System SHALL use PDO prepared statements for all database queries to prevent SQL injection.
4. WHEN a session is created or a privilege change occurs, THE AuthController SHALL regenerate the session ID.
5. THE System SHALL set PHP session cookies with `httponly = 1`, `secure = 1`, and `use_strict_mode = 1`.
6. WHEN a student requests more than 3 OTPs within 10 minutes, THE AuthController SHALL block further OTP requests for that student for the remainder of the 10-minute window.
7. THE System SHALL store all passwords as bcrypt hashes with a cost factor of at least 12 and SHALL NOT store plaintext passwords.
8. THE System SHALL store OTP values as bcrypt hashes and SHALL NOT persist the raw OTP value.
9. WHEN a bank transfer receipt is uploaded, THE System SHALL validate the file MIME type as PDF, JPEG, or PNG and store the file outside the web root, accessible only through a session-authenticated controller.
10. THE System SHALL enforce role-based access control on all routes, verifying the session role before processing any request.

---

### Requirement 11: Registration Status Lifecycle

**User Story:** As a student or admin, I want registration status transitions to follow a defined lifecycle, so that the process is predictable and auditable.

#### Acceptance Criteria

1. THE System SHALL only allow registration status transitions in the forward direction: `draft` → `pending_payment` → `payment_submitted` → `payment_verified` → `approved` or `rejected`.
2. IF a registration status update would result in a backward transition, THEN THE System SHALL reject the update and return an error.
3. THE System SHALL record a timestamp (`submitted_at`) when a registration transitions from `draft` to `pending_payment`.
4. WHEN a registration is approved or rejected, THE System SHALL record the admin ID and timestamp in the `admin_actions` table.

---

### Requirement 12: OTP Generation

**User Story:** As a developer, I want the OTP generation function to produce cryptographically secure, correctly formatted tokens, so that the MFA system is reliable and secure.

#### Acceptance Criteria

1. THE AuthController SHALL generate OTPs consisting of exactly 6 numeric digits.
2. THE AuthController SHALL generate OTPs using a cryptographically secure random source (`random_int`).
3. WHEN an OTP is generated, THE AuthController SHALL store only the bcrypt hash of the OTP in the Application_DB and SHALL NOT store the raw OTP value.
4. WHEN an OTP token is successfully used for authentication, THE AuthController SHALL mark the token as used so it cannot be reused.
5. THE System SHALL expire OTP tokens after 5 minutes from generation.

---

### Requirement 13: Performance

**User Story:** As a student or admin, I want the system to respond promptly during peak registration periods, so that the registration process is not disrupted by slow performance.

#### Acceptance Criteria

1. THE System SHALL respond to Razorpay webhook callbacks within 5 seconds to prevent gateway retries.
2. THE System SHALL use indexed database lookups on `college_id` and `transfer_date` columns for Accounts_DB queries during payment verification.
3. THE System SHALL purge expired OTP tokens from the `otp_tokens` table via a scheduled cron job to prevent table bloat.
4. WHILE a student session is active, THE StudentPortalController SHALL cache dashboard query results with a 5-minute TTL to reduce database load.
