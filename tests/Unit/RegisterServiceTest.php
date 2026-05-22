<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../models/Register.php';

class RegisterServiceTest extends TestCase
{
    /**
     * Tests that registration rejects empty input.
     */
    public function testRegisterReturnsErrorWhenNoDataProvided()
    {
        // Empty registration input should be rejected before user creation.
        $result = RegisterService::register([]);
        $this->assertFalse($result['success']);
        $this->assertContains('No data provided', $result['errors']);
    }

    /**
     * Tests that registration requires a name.
     */
    public function testRegisterReturnsErrorWhenNameEmpty()
    {
        // The username/name field is required.
        $data = ['name' => '', 'email' => 'a@b.com', 'password' => 'Password1', 'confirm' => 'Password1'];
        $result = RegisterService::register($data);
        $this->assertFalse($result['success']);
        $this->assertContains('Name is required', $result['errors']);
    }

    /**
     * Tests that registration rejects invalid name characters.
     */
    public function testRegisterReturnsErrorWhenNameHasInvalidChars()
    {
        // Names may only contain letters, numbers, and underscores.
        $data = ['name' => 'invalid name!', 'email' => 'a@b.com', 'password' => 'Password1', 'confirm' => 'Password1'];
        $result = RegisterService::register($data);
        $this->assertFalse($result['success']);
        $this->assertContains('Name can only contain letters, numbers, and underscores', $result['errors']);
    }

    /**
     * Tests that registration rejects invalid email format.
     */
    public function testRegisterReturnsErrorWhenEmailInvalid()
    {
        // Invalid email format should stop registration validation.
        $data = ['name' => 'validname', 'email' => 'not-an-email', 'password' => 'Password1', 'confirm' => 'Password1'];
        $result = RegisterService::register($data);
        $this->assertFalse($result['success']);
        $this->assertContains('Invalid email address', $result['errors']);
    }

    /**
     * Tests that registration rejects weak passwords.
     */
    public function testRegisterReturnsErrorWhenPasswordWeak()
    {
        // Passwords must meet the minimum length and composition rule.
        $data = ['name' => 'validname', 'email' => 'a@b.com', 'password' => 'short', 'confirm' => 'short'];
        $result = RegisterService::register($data);
        $this->assertFalse($result['success']);
        $this->assertContains('Password must be at least 8 characters and contain at least one letter and one number', $result['errors']);
    }

    /**
     * Tests that registration requires matching password confirmation.
     */
    public function testRegisterReturnsErrorWhenPasswordsDoNotMatch()
    {
        // Confirmation password must match the original password.
        $data = ['name' => 'validname', 'email' => 'a@b.com', 'password' => 'Password1', 'confirm' => 'Password2'];
        $result = RegisterService::register($data);
        $this->assertFalse($result['success']);
        $this->assertContains('Passwords do not match', $result['errors']);
    }
}
