<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../models/Auth.php';
require_once __DIR__ . '/../../config/Database.php';

class AuthTest extends TestCase
{
    public function testFindUserByEmailUsesDatabaseAndReturnsUser()
    {
        $email = 'test@example.com';
        $expectedUser = [
            'id' => 123,
            'username' => 'tester',
            'email' => $email,
            'password' => password_hash('secret', PASSWORD_DEFAULT),
        ];

        // Create a mock for the Database class
        $dbMock = $this->createMock(Database::class);

        // Expect getOne to be called once with SQL and params, and return expected user
        $dbMock->expects($this->once())
               ->method('getOne')
               ->with($this->equalTo('SELECT * FROM `users` WHERE `email` = ?'), $this->equalTo([$email]))
               ->willReturn($expectedUser);

        $result = Auth::findUserByEmail($email, $dbMock);

        $this->assertIsArray($result);
        $this->assertEquals($expectedUser['email'], $result['email']);
        $this->assertEquals($expectedUser['id'], $result['id']);
    }

    public function testFindUserByEmailReturnsNullWhenNotFound()
    {
        $email = 'missing@example.com';

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
               ->method('getOne')
               ->with($this->equalTo('SELECT * FROM `users` WHERE `email` = ?'), $this->equalTo([$email]))
               ->willReturn(null);

        $result = Auth::findUserByEmail($email, $dbMock);
        $this->assertNull($result);
    }
}
