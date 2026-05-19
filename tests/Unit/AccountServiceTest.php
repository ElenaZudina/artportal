<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/AccountService.php';
require_once __DIR__ . '/../../models/Auth.php';
require_once __DIR__ . '/../../config/Database.php';

class AccountServiceTest extends TestCase
{
    /**
     * Tests that account updates reject missing form data.
     */
    public function testUpdateAccountReturnsErrorWhenNoDataProvided()
    {
        // Empty input returns before any account model lookup.
        $result = AccountService::updateAccount(10, []);

        $this->assertFalse($result['success']);
        $this->assertSame(['No data provided'], $result['errors']);
    }

    /**
     * Tests that account updates collect username and email validation errors.
     */
    public function testUpdateAccountCollectsValidationErrors()
    {
        // Invalid scalar fields should be reported and should not be saved.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturn(null);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = AccountService::updateAccount(10, [
            'username' => 'bad name',
            'email' => 'not-an-email',
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertContains('Username can only contain letters, numbers, and underscores', $result['errors']);
        $this->assertContains('Invalid email address', $result['errors']);
    }

    /**
     * Tests that account updates reject duplicate username and email values.
     */
    public function testUpdateAccountRejectsDuplicateUsernameAndEmail()
    {
        // Duplicate checks use the injected database through Auth model methods.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(['id' => 11], ['id' => 12]);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = AccountService::updateAccount(10, [
            'username' => 'taken',
            'email' => 'taken@example.com',
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertContains('Username exists already', $result['errors']);
        $this->assertContains('Email exists already', $result['errors']);
    }

    /**
     * Tests that account updates return an error when the database update fails.
     */
    public function testUpdateAccountReturnsErrorWhenSaveFails()
    {
        // Valid data with no duplicates should surface a failed update operation.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturn(null);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->willReturn(false);

        $result = AccountService::updateAccount(10, [
            'username' => 'newname',
            'email' => 'New@Example.COM',
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['Database error while updating account'], $result['errors']);
    }

    /**
     * Tests that account updates normalize and save valid account data.
     */
    public function testUpdateAccountReturnsSuccessWithNormalizedData()
    {
        // Username is trimmed and email is lowercased before being saved.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturn(null);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('UPDATE `users` SET `username` = ?, `email` = ? WHERE `id` = ?'),
                $this->equalTo(['newname', 'new@example.com', 10])
            )
            ->willReturn(true);

        $result = AccountService::updateAccount(10, [
            'username' => ' newname ',
            'email' => ' New@Example.COM ',
        ], $dbMock);

        $this->assertTrue($result['success']);
        $this->assertSame('newname', $result['username']);
        $this->assertSame('new@example.com', $result['email']);
    }

    /**
     * Tests that password updates reject missing form data.
     */
    public function testUpdatePasswordReturnsErrorWhenNoDataProvided()
    {
        // Empty input returns before loading a user account.
        $result = AccountService::updatePassword(10, []);

        $this->assertFalse($result['success']);
        $this->assertSame(['No data provided'], $result['errors']);
    }

    /**
     * Tests that password updates reject missing users.
     */
    public function testUpdatePasswordReturnsErrorWhenUserNotFound()
    {
        // A missing user stops password validation and avoids update calls.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT * FROM `users` WHERE `id` = ?'),
                $this->equalTo([10])
            )
            ->willReturn(null);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = AccountService::updatePassword(10, [
            'current_password' => 'oldpass',
            'new_password' => 'newpass',
            'confirm_password' => 'newpass',
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['User not found'], $result['errors']);
    }

    /**
     * Tests that password updates collect password validation errors.
     */
    public function testUpdatePasswordCollectsValidationErrors()
    {
        // Password field validation should prevent saving invalid passwords.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(['id' => 10, 'password' => password_hash('oldpass', PASSWORD_DEFAULT)]);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = AccountService::updatePassword(10, [
            'current_password' => '',
            'new_password' => '123',
            'confirm_password' => '456',
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertContains('All password fields are required', $result['errors']);
        $this->assertContains('New password must be at least 6 characters long', $result['errors']);
        $this->assertContains('Passwords do not match', $result['errors']);
        $this->assertContains('Current password is incorrect', $result['errors']);
    }

    /**
     * Tests that password updates return an error when saving fails.
     */
    public function testUpdatePasswordReturnsErrorWhenSaveFails()
    {
        // Valid passwords should surface a failed password update operation.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(['id' => 10, 'password' => password_hash('oldpass', PASSWORD_DEFAULT)]);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->willReturn(false);

        $result = AccountService::updatePassword(10, [
            'current_password' => 'oldpass',
            'new_password' => 'newpass',
            'confirm_password' => 'newpass',
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['Database error while changing password'], $result['errors']);
    }

    /**
     * Tests that password updates save a hashed password and return success.
     */
    public function testUpdatePasswordReturnsSuccessWhenPasswordIsSaved()
    {
        // The new password should be hashed before it is saved.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(['id' => 10, 'password' => password_hash('oldpass', PASSWORD_DEFAULT)]);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('UPDATE `users` SET `password` = ? WHERE `id` = ?'),
                $this->callback(function ($params) {
                    return is_array($params)
                        && count($params) === 2
                        && password_verify('newpass', $params[0])
                        && $params[1] === 10;
                })
            )
            ->willReturn(true);

        $result = AccountService::updatePassword(10, [
            'current_password' => 'oldpass',
            'new_password' => 'newpass',
            'confirm_password' => 'newpass',
        ], $dbMock);

        $this->assertTrue($result['success']);
    }
}
