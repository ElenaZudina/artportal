<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../models/Register.php';

class RegisterServiceTest extends TestCase
{
    public function testRegisterReturnsErrorWhenNoDataProvided()
    {
        $result = RegisterService::register([]);
        $this->assertFalse($result['success']);
        $this->assertContains('No data provided', $result['errors']);
    }

    public function testRegisterReturnsErrorWhenNameEmpty()
    {
        $data = ['name' => '', 'email' => 'a@b.com', 'password' => 'Password1', 'confirm' => 'Password1'];
        $result = RegisterService::register($data);
        $this->assertFalse($result['success']);
        $this->assertContains('Name is required', $result['errors']);
    }

    public function testRegisterReturnsErrorWhenNameHasInvalidChars()
    {
        $data = ['name' => 'invalid name!', 'email' => 'a@b.com', 'password' => 'Password1', 'confirm' => 'Password1'];
        $result = RegisterService::register($data);
        $this->assertFalse($result['success']);
        $this->assertContains('Name can only contain letters, numbers, and underscores', $result['errors']);
    }

    public function testRegisterReturnsErrorWhenEmailInvalid()
    {
        $data = ['name' => 'validname', 'email' => 'not-an-email', 'password' => 'Password1', 'confirm' => 'Password1'];
        $result = RegisterService::register($data);
        $this->assertFalse($result['success']);
        $this->assertContains('Invalid email address', $result['errors']);
    }

    public function testRegisterReturnsErrorWhenPasswordWeak()
    {
        $data = ['name' => 'validname', 'email' => 'a@b.com', 'password' => 'short', 'confirm' => 'short'];
        $result = RegisterService::register($data);
        $this->assertFalse($result['success']);
        $this->assertContains('Password must be at least 8 characters and contain at least one letter and one number', $result['errors']);
    }

    public function testRegisterReturnsErrorWhenPasswordsDoNotMatch()
    {
        $data = ['name' => 'validname', 'email' => 'a@b.com', 'password' => 'Password1', 'confirm' => 'Password2'];
        $result = RegisterService::register($data);
        $this->assertFalse($result['success']);
        $this->assertContains('Passwords do not match', $result['errors']);
    }
}
