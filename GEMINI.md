# Art Portal Project Instructions

Welcome to the Art Portal project. This document provides foundational guidance for development, architecture, and workflows within this codebase.

## Project Overview

Art Portal is a PHP-based web application designed for artists to showcase their work, for users to browse paintings and make purchase requests, and for administrators to manage the platform's content.

### Core Technologies
- **Language:** PHP 8.x
- **Database:** MariaDB/MySQL (using PDO)
- **Dependency Management:** Composer
- **Testing:** PHPUnit 11.x
- **Environment Management:** `vlucas/phpdotenv`
- **Email:** PHPMailer
- **AI Integration:** Google Cloud Vision API (for image tagging)

### Architecture
The project follows a layered architecture with clear separation of concerns as defined in `ARCHITECTURE_PATTERN.md`:

1.  **View Layer:** HTML templates located in `views/` and partials in `views/partials/`.
2.  **Controller Layer:** Located in `controllers/`, `admin/controllers/`, and `dashboard/controllers/`.
3.  **Service Layer:** Located in `services/`. Contains all business logic and validation.
4.  **Model Layer (Repository Pattern):** Located in `models/`. Responsible strictly for SQL queries and data access.
5.  **Database Wrapper:** `config/Database.php` provides a PDO-based interface.

## Development Workflows

### Setup
1.  **Dependencies:** Run `composer install`.
2.  **Environment:** Create a `.env` file with:
    ```env
    DB_HOST=localhost
    DB_NAME=art_portal
    DB_USER=root
    DB_PASSWORD=your_password
    GOOGLE_VISION_API_KEY=your_key
    ADMIN_EMAIL=admin@example.com
    ```
3.  **Database:** Import `database/art_portal.sql`.
4.  **Web Server:** Use XAMPP or similar, pointing to the project root.

### Entry Points
-   **Public Site:** `index.php`
-   **Admin Panel:** `admin/index.php` (Protected by `admin` role)
-   **User Dashboard:** `dashboard/index.php` (Protected by authenticated session)

### Running Tests
-   **Run all tests:** `./vendor/bin/phpunit`
-   **Unit tests:** `./vendor/bin/phpunit --testsuite unit`
-   **Integration tests:** `./vendor/bin/phpunit --testsuite integration`

## Engineering Standards

### Architectural Rules (Mandatory)
-   **Zero SQL in Services:** Services must interact with Models, never execute SQL directly.
-   **Zero Business Logic in Models:** Models handle data retrieval/persistence only.
-   **DI for Testing:** Methods should accept an optional `Database $db` for mocking.

### Security Mandates (Critical)
Based on `SECURITY_AUDIT_JUNIOR_LEVEL.md`, strictly follow these rules:
1.  **Prepared Statements:** ALWAYS use `?` placeholders. Never concatenate variables into SQL strings. (Note: `Auth::findUserByEmail` is a known vulnerability that needs fixing).
2.  **CSRF Protection:** All POST forms must include a CSRF token. Validate this token in the controller.
3.  **Access Control:** Always verify that a user has permission to edit the specific resource (e.g., check that `artist_id` matches the session user). Use `$_SESSION['userId']` directly instead of relying on POSTed IDs.
4.  **XSS Prevention:** Use `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')` when outputting user-generated content in views.
5.  **Safe File Uploads:** Use unique filenames (`uniqid`), validate extensions against a whitelist, and check `is_uploaded_file()`.

### Coding Style
-   Follow PSR-12 coding standards.
-   Ensure all new features or bug fixes include corresponding tests.
-   Refer to `PHPUNIT-CHEATSHEET.md` for testing tips.

## Key Resources
-   `ARCHITECTURE_PATTERN.md`: Detailed architectural guidelines.
-   `SECURITY_AUDIT_JUNIOR_LEVEL.md`: Current security status and required fixes.
-   `USER_MANUAL.md`: Route list and user flow documentation.
-   `FILE_HASH_SETUP.md`: Information on file integrity checks.
