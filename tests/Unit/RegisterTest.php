<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../models/Register.php';
require_once __DIR__ . '/../../config/Database.php';

class RegisterTest extends TestCase
{
    public function testSaveUserReturnsSuccessWhenUserIsCreated()
    {
        $cleanData = [
            'name' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => 'secret123',
        ];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(null, null);

        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo("INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES (NULL, ?, ?, ?, 'user', ?)") ,
                $this->callback(function (array $params) use ($cleanData) {
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
    }

    public function testSaveUserReturnsErrorWhenEmailExists()
    {
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

    public function testSaveUserReturnsErrorWhenUsernameExists()
    {
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
}
