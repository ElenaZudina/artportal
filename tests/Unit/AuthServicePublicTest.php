<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../models/Auth.php';
require_once __DIR__ . '/../../models/Register.php';
require_once __DIR__ . '/../../config/Database.php';

class AuthServicePublicTest extends TestCase
{
    /**
     * Tests that login returns an error when no user matches the email.
     */
    public function testLoginReturnsErrorWhenUserNotFound()
    {
        // Valid email input should be normalized before the Auth lookup.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT * FROM `users` WHERE `email` = ?'),
                $this->equalTo(['missing@example.com'])
            )
            ->willReturn(null);

        $result = AuthService::login([
            'email' => ' Missing@Example.COM ',
            'password' => 'secret',
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['User not found'], $result['errors']);
    }

    /**
     * Tests that login rejects incorrect passwords.
     */
    public function testLoginReturnsErrorWhenPasswordIsIncorrect()
    {
        // Password verification should fail when the submitted password does not match the hash.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn([
                'id' => 10,
                'email' => 'user@example.com',
                'password' => password_hash('correct-password', PASSWORD_DEFAULT),
                'status' => 'active',
            ]);

        $result = AuthService::login([
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['Incorrect password'], $result['errors']);
    }

    /**
     * Tests that login rejects blocked user accounts.
     */
    public function testLoginReturnsBlockedErrorWhenAccountIsBlocked()
    {
        // Blocked accounts should be marked with the blocked flag after password verification.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn([
                'id' => 10,
                'email' => 'user@example.com',
                'password' => password_hash('secret123', PASSWORD_DEFAULT),
                'status' => 'blocked',
            ]);

        $result = AuthService::login([
            'email' => 'user@example.com',
            'password' => 'secret123',
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertTrue($result['blocked']);
        $this->assertSame(['Your account is blocked. Please contact the administrator.'], $result['errors']);
    }

    /**
     * Tests that login returns the authenticated active user.
     */
    public function testLoginReturnsSuccessForActiveUser()
    {
        // Active accounts with a matching password should be returned unchanged.
        $user = [
            'id' => 10,
            'email' => 'user@example.com',
            'username' => 'viewer',
            'password' => password_hash('secret123', PASSWORD_DEFAULT),
            'status' => 'active',
        ];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn($user);

        $result = AuthService::login([
            'email' => 'user@example.com',
            'password' => 'secret123',
        ], $dbMock);

        $this->assertTrue($result['success']);
        $this->assertSame($user, $result['user']);
    }

    /**
     * Tests that valid registration data is normalized and saved.
     */
    public function testRegisterReturnsSuccessWithNormalizedData()
    {
        // Registration should trim username, lowercase email, hash the password, and return the new user.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturn(null);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo("INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`, `created_at`) VALUES (NULL, ?, ?, ?, 'user', 'active', ?)"),
                $this->callback(function ($params) {
                    return is_array($params)
                        && count($params) === 4
                        && $params[0] === 'newuser'
                        && $params[1] === 'new@example.com'
                        && password_verify('Password1', $params[2])
                        && preg_match('/^\d{4}-\d{2}-\d{2}$/', $params[3]);
                })
            )
            ->willReturn(true);
        $dbMock->expects($this->once())
            ->method('getLastInsertId')
            ->willReturn(25);

        $result = RegisterService::register([
            'name' => ' newuser ',
            'email' => ' New@Example.COM ',
            'password' => 'Password1',
            'confirm' => 'Password1',
        ], $dbMock);

        $this->assertTrue($result['success']);
        $this->assertSame(25, $result['user']['id']);
        $this->assertSame('newuser', $result['user']['username']);
        $this->assertSame('user', $result['user']['role']);
        $this->assertSame('active', $result['user']['status']);
    }
}
