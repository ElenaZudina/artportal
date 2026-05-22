<?php
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../helpers/CsrfHelper.php';
require_once __DIR__ . '/../../helpers/MenuHelper.php';
require_once __DIR__ . '/../../helpers/UIHelper.php';
require_once __DIR__ . '/../../models/Auth.php';
require_once __DIR__ . '/../../models/Categories.php';
require_once __DIR__ . '/../../models/Paintings.php';
require_once __DIR__ . '/../../models/Register.php';
require_once __DIR__ . '/../../config/Database.php';

class AuthControllerTest extends TestCase
{
    private string $marker = '__TEST__AUTH_CONTROLLER__';

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
     * Tests that the forgot password controller action renders the public request page.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testForgotPasswordFormRendersRequestPage()
    {
        // The shared layout loads menu categories from the test database.
        $this->assertTestEnvironment();

        $_SESSION = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';

        if (!defined('ACTIVE_ROUTE')) {
            define('ACTIVE_ROUTE', 'forgot-password');
        }

        ob_start();
        try {
            AuthController::forgotPasswordForm();
            $output = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        $this->assertStringContainsString('<html', $output);
        $this->assertStringContainsString('Forgot password', $output);
        $this->assertStringContainsString('<form method="POST" action="forgot-password-request">', $output);
        $this->assertStringContainsString('name="csrf_token"', $output);
        $this->assertStringContainsString('name="email"', $output);
    }

    /**
     * Tests that registration POST creates a user and renders the registration result page.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRegisterUserCreatesUserAndRendersSuccessPage()
    {
        // This controller action writes through RegisterService, so it must use the test database.
        $this->assertTestEnvironment();

        $db = new Database();
        $this->cleanupTestUsers($db);

        $_SESSION = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $token = CsrfHelper::token();
        $_POST = [
            'csrf_token' => $token,
            'name' => $this->marker . 'user',
            'email' => $this->testEmail(),
            'password' => 'Password1',
            'confirm' => 'Password1',
        ];

        if (!defined('ACTIVE_ROUTE')) {
            define('ACTIVE_ROUTE', 'register');
        }

        ob_start();
        try {
            AuthController::registerUser();
            $output = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            $this->cleanupTestUsers($db);
            throw $e;
        }

        $user = $db->getOne('SELECT * FROM users WHERE email = ? LIMIT 1', [$this->testEmail()]);

        $this->assertIsArray($user);
        $this->assertSame($this->marker . 'user', $user['username']);
        $this->assertSame((int)$user['id'], $_SESSION['userId']);
        $this->assertSame('user', $_SESSION['status']);
        $this->assertStringContainsString('User has been added', $output);
        $this->assertStringContainsString('Fill Artist Profile', $output);

        $this->cleanupTestUsers($db);
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

    /**
     * Builds a unique email address for a marked controller registration user.
     */
    private function testEmail(): string
    {
        return strtolower(str_replace('_', '-', trim($this->marker, '_'))) . '@example.test';
    }

    /**
     * Deletes only users created by this integration test.
     */
    private function cleanupTestUsers(Database $db): void
    {
        $db->executeRun(
            "DELETE FROM `users` WHERE `username` LIKE ? OR `email` = ?",
            [$this->marker . '%', $this->testEmail()]
        );
    }
}
