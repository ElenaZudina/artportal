<?php
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../helpers/CsrfHelper.php';
require_once __DIR__ . '/../../helpers/MenuHelper.php';
require_once __DIR__ . '/../../helpers/UIHelper.php';
require_once __DIR__ . '/../../models/Categories.php';
require_once __DIR__ . '/../../models/Paintings.php';
require_once __DIR__ . '/../../config/Database.php';

class AuthControllerTest extends TestCase
{
    /**
     * Tests that the login controller action renders the public login page.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testFormLoginSiteRendersLoginPage()
    {
        // The shared layout loads menu categories from the test database.
        $this->assertTestEnvironment();

        $_SESSION = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';

        if (!defined('ACTIVE_ROUTE')) {
            define('ACTIVE_ROUTE', 'login');
        }

        ob_start();
        try {
            AuthController::formLoginSite();
            $output = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        $this->assertStringContainsString('<html', $output);
        $this->assertStringContainsString('<title>Art Portal</title>', $output);
        $this->assertStringContainsString('<h2 class="mb-4 text-center">Login</h2>', $output);
        $this->assertStringContainsString('<form method="POST" action="auth">', $output);
        $this->assertStringContainsString('name="csrf_token"', $output);
        $this->assertStringContainsString('name="email"', $output);
        $this->assertStringContainsString('name="password"', $output);
        $this->assertStringContainsString('Forgot password?', $output);
        $this->assertStringContainsString('Register', $output);
    }

    /**
     * Tests that the registration controller action renders the public registration page.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRegisterFormRendersRegistrationPage()
    {
        // The shared layout loads menu categories from the test database.
        $this->assertTestEnvironment();

        $_SESSION = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';

        if (!defined('ACTIVE_ROUTE')) {
            define('ACTIVE_ROUTE', 'register');
        }

        ob_start();
        try {
            AuthController::registerForm();
            $output = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        $this->assertStringContainsString('<html', $output);
        $this->assertStringContainsString('<title>Art Portal</title>', $output);
        $this->assertStringContainsString('<h2 class="mb-4 text-center">Register</h2>', $output);
        $this->assertStringContainsString('<form method="POST" action="registerAnswer">', $output);
        $this->assertStringContainsString('name="csrf_token"', $output);
        $this->assertStringContainsString('name="name"', $output);
        $this->assertStringContainsString('name="email"', $output);
        $this->assertStringContainsString('name="password"', $output);
        $this->assertStringContainsString('name="confirm"', $output);
    }

    /**
     * Stops the test if the test database is not active.
     */
    private function assertTestEnvironment(): void
    {
        $dbName = $_ENV['DB_NAME'] ?? '';

        if (($_SERVER['APP_ENV'] ?? '') !== 'test' || $dbName !== 'art_portal_test') {
            $this->fail('Integration tests must run only with APP_ENV=test and DB_NAME=art_portal_test.');
        }
    }
}
