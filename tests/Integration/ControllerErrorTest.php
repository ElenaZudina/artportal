<?php
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../controllers/Controller.php';
require_once __DIR__ . '/../../helpers/MenuHelper.php';
require_once __DIR__ . '/../../helpers/UIHelper.php';
require_once __DIR__ . '/../../models/Categories.php';
require_once __DIR__ . '/../../models/Paintings.php';
require_once __DIR__ . '/../../config/Database.php';

class ControllerErrorTest extends TestCase
{
    /**
     * Tests that the base controller renders the 404 page through the layout.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testError404RendersNotFoundPage()
    {
        // The shared layout loads menu categories from the test database.
        $this->assertTestEnvironment();

        $_SESSION = [];
        if (!defined('ACTIVE_ROUTE')) {
            define('ACTIVE_ROUTE', 'testError');
        }

        ob_start();
        try {
            Controller::error404();
            $output = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        $this->assertStringContainsString('<html', $output);
        $this->assertStringContainsString('<title>Art Portal</title>', $output);
        $this->assertStringContainsString('ArtPortal', $output);
        $this->assertStringContainsString('This canvas is blank', $output);
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
