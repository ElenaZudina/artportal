<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/AuthService.php';

class AuthServiceValidationTest extends TestCase
{
    /**
     * Tests that login rejects empty input before database lookup.
     */
    public function testLoginReturnsErrorWhenNoDataProvided()
    {
        // Empty input returns before email lookup or password verification.
        $result = AuthService::login([]);

        $this->assertFalse($result['success']);
        $this->assertSame(['No data provided'], $result['errors']);
    }

    /**
     * Tests that login rejects invalid email format before database lookup.
     */
    public function testLoginReturnsErrorForInvalidEmail()
    {
        // Invalid email format is rejected before the Auth model is used.
        $result = AuthService::login(['email' => 'not-an-email', 'password' => 'secret']);

        $this->assertFalse($result['success']);
        $this->assertSame(['Invalid email address'], $result['errors']);
    }
}
