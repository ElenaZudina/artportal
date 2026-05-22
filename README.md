# ArtPortal

ArtPortal is a PHP/MySQL web application for browsing, publishing, and managing digital art collections. It includes a public gallery, user and artist dashboards, an admin panel, artist moderation, purchase requests, favorites, exhibitions, collections, and automated tests.

The project is built as a custom MVC-style application and is designed to run locally with XAMPP/Apache under the `/artportal` base path.

## Features

- Public gallery with paintings, artists, categories, exhibitions, and detailed pages.
- User registration, login, logout, account editing, and password change.
- Artist profile creation and moderation workflow.
- Artist dashboard for managing paintings and incoming purchase requests.
- Favorites and purchase request flows for users.
- Admin dashboard with users, categories, collections, exhibitions, and artist moderation.
- CSRF protection helpers, server-side validation, prepared PDO queries, and password hashing.
- Email-based password reset request flow for administrators.
- PHPUnit unit/integration tests and Playwright end-to-end tests.

## Tech Stack

- PHP with a custom MVC architecture
- MySQL with PDO
- Apache/XAMPP
- Composer
- `vlucas/phpdotenv`
- PHPMailer
- PHPUnit
- Playwright
- HTML, CSS, JavaScript

## Project Structure

```text
admin/                 Admin panel controllers, routes, and views
config/                Database and environment configuration
controllers/           Public application controllers
dashboard/             User/artist dashboard controllers, routes, and views
database/              SQL dump for local setup
helpers/               UI, CSRF, pagination, menu, and artist helpers
images/                Demo and uploaded image assets
models/                Data access models
public/                CSS and JavaScript assets
routes/                Public router
services/              Business logic services
tests/                 PHPUnit unit and integration tests
e2e/                   Playwright end-to-end tests
views/                 Public-facing views and partials
```

## Requirements

- PHP 8.2+ recommended
- MySQL 5.7+ or MariaDB
- Apache with `mod_rewrite`
- Composer
- Node.js and npm, only for Playwright tests
- XAMPP or a similar local PHP/MySQL stack

## Local Installation

1. Clone the repository into your XAMPP web root:

```bash
git clone <repository-url> C:/xampp/htdocs/artportal
cd C:/xampp/htdocs/artportal
```

2. Install PHP dependencies:

```bash
composer install
```

3. Install JavaScript test dependencies:

```bash
npm install
npx playwright install
```

4. Create a MySQL database, for example `art_portal`, and import the dump:

```bash
mysql -u root -p art_portal < database/art_portal.sql
```

You can also import `database/art_portal.sql` through phpMyAdmin.

5. Create a `.env` file in the project root:

```env
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=art_portal

ADMIN_EMAIL=admin@example.com
GOOGLE_VISION_API_KEY=
```

6. Make sure Apache is serving the project from:

```text
http://localhost/artportal/
```

## Test Environment

The application supports a separate test environment through `.env.test`. Integration and E2E tests expect a test database named `art_portal_test`.

Example `.env.test`:

```env
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=art_portal_test

ADMIN_EMAIL=admin@example.com
GOOGLE_VISION_API_KEY=
```

Import the same SQL dump into the test database before running integration or E2E tests:

```bash
mysql -u root -p art_portal_test < database/art_portal.sql
```

## Running Tests

Run PHPUnit tests:

```bash
vendor/bin/phpunit
```

Run only unit tests:

```bash
vendor/bin/phpunit --testsuite unit
```

Run only integration tests:

```bash
vendor/bin/phpunit --testsuite integration
```

Run Playwright tests from the project root:

```bash
npx playwright test
```

The root Playwright configuration uses:

```text
http://localhost/artportal/
```

and sends the `X-App-Env: test` header so the application loads `.env.test` during local E2E runs.

## Main Routes

Public routes:

- `/artportal/` - home page
- `/artportal/all` - all paintings
- `/artportal/category?id={id}` - paintings by category
- `/artportal/paintings?id={id}` - painting details
- `/artportal/artists` - artists list
- `/artportal/artist?id={id}` - artist details
- `/artportal/exhibitions` - exhibitions list
- `/artportal/current-exhibition?id={id}` - exhibition details
- `/artportal/registerForm` - registration form
- `/artportal/login` - login form
- `/artportal/forgot-password` - password reset request form

Dashboard routes:

- `/artportal/dashboard/startDashboard` - dashboard home
- `/artportal/dashboard/profile` - artist profile
- `/artportal/dashboard/account` - account details
- `/artportal/dashboard/my-paintings` - artist paintings
- `/artportal/dashboard/my-favorites` - user favorites
- `/artportal/dashboard/my-requests` - user's purchase requests
- `/artportal/dashboard/purchase-requests` - purchase requests received by an artist

Admin routes:

- `/artportal/admin/startAdmin` - admin dashboard
- `/artportal/admin/users` - user management
- `/artportal/admin/categories` - category management
- `/artportal/admin/collections` - collection management
- `/artportal/admin/exhibitions` - exhibition management
- `/artportal/admin/moderation-artists` - artist moderation

## Documentation

Additional project documentation is available in the `docs/` directory:

- `docs/uml/`

## Security Notes

- Keep `.env` and `.env.test` out of version control.
- Use strong credentials outside local development.
- Do not manually assign admin roles in production without a review process.
- Uploaded images and form data should be treated as untrusted input.

## Author

Elena Zudina
