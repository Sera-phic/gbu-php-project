# Semester Online Registration System

A PHP/MySQL web application that digitizes the GBU university semester registration and payment process.

## Requirements

- PHP 8.1+
- MySQL 8.0+
- Composer
- Apache with `mod_rewrite` (or Nginx with equivalent `try_files` config)
- SSL certificate (HTTPS required)

## Setup

1. **Clone the repository**
   ```bash
   git clone <repo-url>
   cd semester-online-registration
   ```

2. **Copy environment file and fill in values**
   ```bash
   cp .env.example .env
   # Edit .env with your database credentials, API keys, and app URL
   ```

3. **Install PHP dependencies**
   ```bash
   composer install --no-dev   # production
   # or
   composer install            # development (includes PHPUnit)
   ```

4. **Run database migrations** (in order)
   ```bash
   mysql -u <user> -p <app_db_name> < database/migrations/001_create_students.sql
   mysql -u <user> -p <app_db_name> < database/migrations/002_create_otp_tokens.sql
   mysql -u <user> -p <app_db_name> < database/migrations/003_create_registrations.sql
   mysql -u <user> -p <app_db_name> < database/migrations/004_create_payments.sql
   mysql -u <user> -p <app_db_name> < database/migrations/005_create_admin_actions.sql
   ```

5. **Configure web server**
   - Point the document root to the `public/` directory.
   - Ensure `mod_rewrite` is enabled (Apache) or configure `try_files` (Nginx).
   - Enable HTTPS — HTTP requests are automatically redirected.

6. **Set up cron jobs**
   ```cron
   # Retry pending payment verifications every 6 hours
   0 */6 * * * php /path/to/app/scripts/retry_verification.php

   # Purge expired OTP tokens hourly
   0 * * * * php /path/to/app/scripts/purge_otp_tokens.php
   ```

7. **Create the uploads directory** (outside web root)
   ```bash
   mkdir -p storage/uploads
   chmod 750 storage/uploads
   ```

## Running Tests

```bash
./vendor/bin/phpunit --testdox
```

## Directory Structure

```
├── app/
│   ├── Controllers/
│   ├── Services/
│   ├── Models/
│   ├── Views/
│   └── Middleware/
├── config/
│   ├── app.php
│   └── database.php
├── database/
│   └── migrations/
├── public/          ← web root
│   ├── index.php
│   └── .htaccess
├── scripts/
├── storage/
│   └── uploads/     ← receipt files (outside web root)
├── tests/
├── .env.example
└── composer.json
```
