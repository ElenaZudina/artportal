<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../models/Favourite.php';
require_once __DIR__ . '/../../config/Database.php';

class FavoriteTest extends TestCase
{
    /**
     * Tests that adding a favorite inserts the user-painting relation.
     */
    public function testAddToFavoriteInsertsRelation()
    {
        // Add should bind user id and painting id in that order.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('INSERT INTO `favorites` (user_id, painting_id) VALUES (?, ?)'),
                $this->equalTo([2, 9])
            )
            ->willReturn(true);

        $this->assertTrue(Favorite::addToFavorite(2, 9, $dbMock));
    }

    /**
     * Tests that removing a favorite deletes the user-painting relation.
     */
    public function testRemoveFromFavoriteDeletesRelation()
    {
        // Remove should bind user id and painting id in that order.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('DELETE FROM `favorites` WHERE user_id = ? AND painting_id = ?'),
                $this->equalTo([2, 9])
            )
            ->willReturn(true);

        $this->assertTrue(Favorite::removeFromFavorite(2, 9, $dbMock));
    }

    /**
     * Tests that isFavorite returns true when a favorite row exists.
     */
    public function testIsFavoriteReturnsTrueWhenRowExists()
    {
        // Any returned row means the painting is favorited by the user.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT id FROM `favorites` WHERE user_id = ? AND painting_id = ? LIMIT 1'),
                $this->equalTo([2, 9])
            )
            ->willReturn(['id' => 5]);

        $this->assertTrue(Favorite::isFavorite(2, 9, $dbMock));
    }

    /**
     * Tests that isFavorite returns false when no favorite row exists.
     */
    public function testIsFavoriteReturnsFalseWhenNoRowExists()
    {
        // Null lookup results should be converted to false.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(null);

        $this->assertFalse(Favorite::isFavorite(2, 9, $dbMock));
    }

    /**
     * Tests that getUserFavorites returns joined favorite painting rows.
     */
    public function testGetUserFavoritesReturnsRowsFromDatabase()
    {
        // Favorites listing should join paintings, favorites, and artists.
        $rows = [
            ['painting_id' => 9, 'title' => 'Blue', 'artist_name' => 'Artist'],
        ];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with(
                $this->logicalAnd(
                    $this->stringContains('JOIN favorites ON paintings.id = favorites.painting_id'),
                    $this->stringContains('JOIN artists ON paintings.artist_id = artists.id'),
                    $this->stringContains('WHERE favorites.user_id = ?')
                ),
                $this->equalTo([2])
            )
            ->willReturn($rows);

        $this->assertSame($rows, Favorite::getUserFavorites(2, $dbMock));
    }
}
