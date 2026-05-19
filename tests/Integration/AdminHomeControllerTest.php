<?php
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../admin/controllers/Admin/HomeController.php';
require_once __DIR__ . '/../../services/StatsService.php';
require_once __DIR__ . '/../../models/Artists.php';
require_once __DIR__ . '/../../models/Auth.php';
require_once __DIR__ . '/../../models/Categories.php';
require_once __DIR__ . '/../../models/Collections.php';
require_once __DIR__ . '/../../models/Exhibitions.php';
require_once __DIR__ . '/../../config/Database.php';

class AdminHomeControllerTest extends TestCase
{
    private string $originalCwd;

    /**
     * Remembers the project working directory before switching to admin paths.
     */
    protected function setUp(): void
    {
        $this->assertTestEnvironment();
        $this->originalCwd = getcwd();
    }

    /**
     * Restores the project working directory after rendering admin views.
     */
    protected function tearDown(): void
    {
        if (!empty($this->originalCwd)) {
            chdir($this->originalCwd);
        }
    }

    /**
     * Tests that the admin start page renders dashboard statistics.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testStartAdminPanelRendersDashboard()
    {
        // Admin views use paths relative to the admin front controller directory.
        chdir(__DIR__ . '/../../admin');

        $_SESSION = [
            'userId' => 1,
            'name' => 'Test Admin',
            'status' => 'admin',
        ];

        ob_start();
        try {
            HomeController::startAdminPanel();
            $output = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        $this->assertStringContainsString('<html', $output);
        $this->assertStringContainsString('<title>Dashboard Test Admin</title>', $output);
        $this->assertStringContainsString('Admin Panel', $output);
        $this->assertStringContainsString('Dashboard summary', $output);
        $this->assertStringContainsString('User Growth', $output);
        $this->assertStringContainsString('chartDataContainer', $output);
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
