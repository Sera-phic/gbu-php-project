# GBU Portal System – Event Management & Placement Cell

This project is a full-stack PHP-based web system designed to support college operations by providing two integrated portals:
1. **Event Management & Registration Portal**
2. **Placement Cell Student Portal**

The system is modular, scalable, and built with real-world deployment in mind. It allows students to interact with college services efficiently while enabling administrators to manage data securely and effectively.

---

## 📌 Project Objectives

- Improve student engagement through centralized digital services.
- Simplify event registration and placement management.
- Provide a real-world deployable system that can be integrated into the college website.
- Ensure equal learning and contribution for both team members.

---

## 🧱 Project Architecture

The project consists of a **common landing page** that directs users to two independent portals:
# GBU Portal System — Event Management & Placement Cell

TL;DR: A lightweight PHP-based system providing two portals: Event Management and Placement Cell, suitable for local development with XAMPP or deployment to any PHP host.

## Table of Contents
- Features
- Architecture
- Tech Stack & Requirements
- Installation (Local)
- Configuration
- Quickstart
- Folder Structure
- Contributing
- Security Notes
- License

## Features
- Two portals: Event Management (students + admin) and Placement Cell (students + admin).
- Student registration, event sign-ups, profile/resume upload, job listings, and admin dashboards.

## Architecture
The app uses a shared landing page that routes to `/event/` and `/placement/`. Common services (DB connection and auth) are in `config/` and `auth/`.

## Tech Stack & Requirements
- PHP 7.4 or newer
- MySQL 5.7+ (or MariaDB compatible)
- Apache 2.4+ (XAMPP recommended for local dev)
- PHP extensions: `mysqli`, `pdo_mysql`, `mbstring`, `fileinfo`, `openssl`

## Installation (Local)
1. Place the project folder in your web server root (for XAMPP: `C:\xampp\htdocs\gbu-php`).
2. Start Apache and MySQL using the XAMPP control panel.
3. Create a MySQL database for the app and import any provided `.sql` schema if available.
4. Update database credentials in `config/database.php` (see Configuration).

## Configuration
Edit `config/database.php` and set the `host`, `username`, `password`, and `database` variables to match your MySQL server. Do not commit production credentials to version control; keep secrets out of the repository.

## Quickstart
1. Start Apache and MySQL (XAMPP).
2. Configure `config/database.php` with your DB credentials.
3. Import the database schema if provided.
4. Open your browser to `http://localhost/gbu-php/` to view the landing page.

## Folder Structure (short)
`gbu-php/`
- `index.php` — main landing page
- `assets/` — CSS, JS, images
- `event/` — Event Management portal (student + admin)
- `placement/` — Placement portal (student + admin)
- `config/database.php` — DB connection
- `auth/` — login, register, logout handlers

## Contributing
Please open issues for bugs or feature requests. To contribute, fork the repo, create a feature branch, and open a pull request. Keep PHP code readable and follow consistent indentation. Add steps here for tests or linters if you want them.

## Security Notes
- Sanitize user inputs and validate uploads before saving.
- Use password hashing (password_hash) and HTTPS in production.
- Restrict file upload types and sizes.

## License
This project is released under the MIT License. See [LICENSE](LICENSE) for details.
- **Frontend:** HTML, CSS, JavaScript, Bootstrap

