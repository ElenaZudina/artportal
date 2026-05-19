<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../models/Register.php';
require_once __DIR__ . '/../../config/Database.php';

class RegisterTest extends TestCase
{
    /**
     * Tests that Register saves a new user when email and username are unique.
     */
    public function testSaveUserReturnsSuccessWhenUserIsCreated()
    {
        // A mock database returns no duplicates and accepts the insert.
        $cleanData = [
            'name' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => 'secret123',
        ];

        $dbMock = $this->createMock(Database::class);
        // Email and username uniqueness checks both return no existing user.
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(null, null);

        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo("INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`, `created_at`) VALUES (NULL, ?, ?, ?, 'user', 'active', ?)") ,
                $this->callback(function (array $params) use ($cleanData) {
                    // The model should hash the password and preserve the expected insert fields.
                    return $params[0] === $cleanData['name']
                        && $params[1] === $cleanData['email']
                        && is_string($params[2])
                        && $params[2] !== $cleanData['password']
                        && $params[3] === date('Y-m-d');
                })
            )
            ->willReturn(true);

        $dbMock->expects($this->once())
            ->method('getLastInsertId')
            ->willReturn('42');

        $result = Register::saveUser($cleanData, $dbMock);

        $this->assertTrue($result['success']);
        $this->assertSame(42, $result['user']['id']);
        $this->assertSame($cleanData['name'], $result['user']['username']);
        $this->assertSame('user', $result['user']['role']);
        $this->assertSame('active', $result['user']['status']);
    }

    /**
     * Tests that Register rejects an email that already exists.
     */
    public function testSaveUserReturnsErrorWhenEmailExists()
    {
        // Existing email should stop before username lookup or insert.
        $cleanData = [
            'name' => 'newuser',
            'email' => 'taken@example.com',
            'password' => 'secret123',
        ];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT * FROM users WHERE email = ?'),
                $this->equalTo([$cleanData['email']])
            )
            ->willReturn(['id' => 1]);

        $dbMock->expects($this->never())->method('executeRun');
        $dbMock->expects($this->never())->method('getLastInsertId');

        $result = Register::saveUser($cleanData, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['Email exists already'], $result['errors']);
    }

    /**
     * Tests that Register rejects a username that already exists.
     */
    public function testSaveUserReturnsErrorWhenUsernameExists()
    {
        // Existing username should stop before insert after email passes.
        $cleanData = [
            'name' => 'takenuser',
            'email' => 'new@example.com',
            'password' => 'secret123',
        ];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(null, ['id' => 2]);

        $dbMock->expects($this->never())->method('executeRun');
        $dbMock->expects($this->never())->method('getLastInsertId');

        $result = Register::saveUser($cleanData, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['Username exists already'], $result['errors']);
    }

    /**
     * Tests that Register reports an error when the database insert fails.
     */
    public function testSaveUserReturnsErrorWhenInsertFails()
    {
        // A failed insert should be returned as a database error.
        $cleanData = [
            'name' => 'newuser',
            'email' => 'new@example.com',
            'password' => 'secret123',
        ];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(null, null);

        $dbMock->expects($this->once())
            ->method('executeRun')
            ->willReturn(false);

        $dbMock->expects($this->never())->method('getLastInsertId');

        $result = Register::saveUser($cleanData, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['Database error: Unable to save user'], $result['errors']);
    }
}
