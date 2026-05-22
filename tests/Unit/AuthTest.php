<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../models/Auth.php';
require_once __DIR__ . '/../../config/Database.php';

class AuthTest extends TestCase
{
    /**
     * Clears session state before each Auth test.
     */
    protected function setUp(): void
    {
        // Auth session tests should not inherit state from previous tests.
        $_SESSION = [];
    }

    /**
     * Clears session state after each Auth test.
     */
    protected function tearDown(): void
    {
        // Leave the global session clean for the rest of the suite.
        $_SESSION = [];
    }

    /**
     * Tests that Auth uses the injected database to fetch and return a user by email.
     */
    public function testFindUserByEmailUsesDatabaseAndReturnsUser()
    {
        // The Auth model should query the injected database and return the matching user row.
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

    /**
     * Tests that Auth returns null when no user exists for the email.
     */
    public function testFindUserByEmailReturnsNullWhenNotFound()
    {
        // Missing users should be represented as null from the injected database.
        $email = 'missing@example.com';

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
               ->method('getOne')
               ->with($this->equalTo('SELECT * FROM `users` WHERE `email` = ?'), $this->equalTo([$email]))
               ->willReturn(null);

        $result = Auth::findUserByEmail($email, $dbMock);
        $this->assertNull($result);
    }

    /**
     * Tests that Auth fetches a user by id through the injected database.
     */
    public function testGetUserByIdUsesParameterizedQuery()
    {
        // User lookup should bind the id parameter.
        $user = ['id' => 10, 'username' => 'artist'];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT * FROM `users` WHERE `id` = ?'),
                $this->equalTo([10])
            )
            ->willReturn($user);

        $this->assertSame($user, Auth::getUserByID(10, $dbMock));
    }

    /**
     * Tests that Auth returns all users when no search term is provided.
     */
    public function testGetUsersWithoutSearchUsesDefaultListQuery()
    {
        // Empty search should use the plain admin user listing query.
        $rows = [['id' => 2, 'username' => 'user']];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with($this->equalTo('SELECT id, username, email, role, status, created_at FROM users ORDER BY id DESC'))
            ->willReturn($rows);

        $this->assertSame($rows, Auth::getUsers('', $dbMock));
    }

    /**
     * Tests that Auth searches users by username or email.
     */
    public function testGetUsersWithSearchUsesLikeParameters()
    {
        // Non-empty search should be trimmed and wrapped in wildcard parameters.
        $rows = [['id' => 3, 'username' => 'searchable']];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with(
                $this->equalTo('SELECT id, username, email, role, status, created_at FROM users WHERE username LIKE ? OR email LIKE ? ORDER BY id DESC'),
                $this->equalTo(['%needle%', '%needle%'])
            )
            ->willReturn($rows);

        $this->assertSame($rows, Auth::getUsers(' needle ', $dbMock));
    }

    /**
     * Tests that updateStatus rejects unsupported status values before database access.
     */
    public function testUpdateStatusRejectsInvalidStatus()
    {
        // Invalid statuses should return false and never call the database.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $this->assertFalse(Auth::updateStatus(10, 'pending', $dbMock));
    }

    /**
     * Tests that updateStatus writes valid account status changes.
     */
    public function testUpdateStatusUpdatesValidStatus()
    {
        // Valid statuses should be saved with user id cast to integer.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('UPDATE `users` SET `status` = ? WHERE `id` = ?'),
                $this->equalTo(['blocked', 10])
            )
            ->willReturn(true);

        $this->assertTrue(Auth::updateStatus('10', 'blocked', $dbMock));
    }

    /**
     * Tests that syncSessionStatus refreshes active user session fields.
     */
    public function testSyncSessionStatusRefreshesActiveSession()
    {
        // Active users should refresh role, name, and account status in the session.
        $_SESSION['userId'] = 10;
        $user = ['id' => 10, 'username' => 'viewer', 'role' => 'user', 'status' => 'active'];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT * FROM `users` WHERE `id` = ?'),
                $this->equalTo([10])
            )
            ->willReturn($user);

        $this->assertSame($user, Auth::syncSessionStatus($dbMock));
        $this->assertSame('user', $_SESSION['status']);
        $this->assertSame('viewer', $_SESSION['name']);
        $this->assertSame('active', $_SESSION['accountStatus']);
    }

    /**
     * Tests that email uniqueness checks return true when another user has the email.
     */
    public function testExistsEmailExceptUserReturnsTrueWhenRowExists()
    {
        // Any returned row means the email belongs to another user.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT id FROM `users` WHERE `email` = ? AND `id` <> ? LIMIT 1'),
                $this->equalTo(['taken@example.com', 10])
            )
            ->willReturn(['id' => 11]);

        $this->assertTrue(Auth::existsEmailExceptUser('taken@example.com', '10', $dbMock));
    }

    /**
     * Tests that username uniqueness checks return false when no row exists.
     */
    public function testExistsUsernameExceptUserReturnsFalseWhenNoRowExists()
    {
        // Null lookup results should be converted to false.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT id FROM `users` WHERE `username` = ? AND `id` <> ? LIMIT 1'),
                $this->equalTo(['available', 10])
            )
            ->willReturn(null);

        $this->assertFalse(Auth::existsUsernameExceptUser('available', '10', $dbMock));
    }

    /**
     * Tests that updateAccount saves username and email fields.
     */
    public function testUpdateAccountUsesExpectedQuery()
    {
        // Account updates should bind username, email, and user id in order.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('UPDATE `users` SET `username` = ?, `email` = ? WHERE `id` = ?'),
                $this->equalTo(['newname', 'new@example.com', 10])
            )
            ->willReturn(true);

        $this->assertTrue(Auth::updateAccount('10', 'newname', 'new@example.com', $dbMock));
    }

    /**
     * Tests that updatePassword saves a password hash for a user.
     */
    public function testUpdatePasswordUsesExpectedQuery()
    {
        // Password updates should bind the hash and integer user id.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('UPDATE `users` SET `password` = ? WHERE `id` = ?'),
                $this->equalTo(['hash-value', 10])
            )
            ->willReturn(true);

        $this->assertTrue(Auth::updatePassword('10', 'hash-value', $dbMock));
    }

    /**
     * Tests that count returns the total user count as an integer.
     */
    public function testCountReturnsIntegerCount()
    {
        // The count method should cast the cnt field to integer.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with($this->equalTo('SELECT COUNT(*) AS cnt FROM users'))
            ->willReturn(['cnt' => '25']);

        $this->assertSame(25, Auth::count($dbMock));
    }

    /**
     * Tests that countByRole returns the role-specific count as an integer.
     */
    public function testCountByRoleReturnsIntegerCount()
    {
        // Role counts should bind the role and cast the cnt field to integer.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT COUNT(*) AS cnt FROM users WHERE role = ?'),
                $this->equalTo(['artist'])
            )
            ->willReturn(['cnt' => '4']);

        $this->assertSame(4, Auth::countByRole('artist', $dbMock));
    }
}
