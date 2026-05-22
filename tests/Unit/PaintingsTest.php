<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../models/Collections.php';
require_once __DIR__ . '/../../models/Paintings.php';
require_once __DIR__ . '/../../config/Database.php';

class PaintingsTest extends TestCase
{
    /**
     * Tests that latest paintings use approved artists and an integer limit.
     */
    public function testGetLastPaintingsReturnsRowsWithIntegerLimit()
    {
        // The limit is appended to SQL only after being cast to an integer.
        $rows = [['id' => 3, 'title' => 'Blue']];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with($this->logicalAnd(
                $this->stringContains("artists.status = 'approved'"),
                $this->stringContains('LIMIT 7')
            ))
            ->willReturn($rows);

        $this->assertSame($rows, Paintings::getLastPaintings('7abc', $dbMock));
    }

    /**
     * Tests that the public painting list returns rows from approved artists.
     */
    public function testGetAllPaintingsReturnsRowsFromDatabase()
    {
        // Public listing should join artists and categories for display fields.
        $rows = [['id' => 2, 'artist_name' => 'Artist', 'category' => 'Oil']];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with($this->logicalAnd(
                $this->stringContains('JOIN artists ON paintings.artist_id = artists.id'),
                $this->stringContains('JOIN categories ON paintings.category_id = categories.id'),
                $this->stringContains("WHERE artists.status = 'approved'")
            ))
            ->willReturn($rows);

        $this->assertSame($rows, Paintings::getAllPaintings($dbMock));
    }

    /**
     * Tests that the public painting count is cast to an integer.
     */
    public function testGetAllPaintingsCountReturnsIntegerCount()
    {
        // Count values returned as strings should be normalized to integers.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with($this->logicalAnd(
                $this->stringContains('SELECT COUNT(*) AS total'),
                $this->stringContains("WHERE artists.status = 'approved'")
            ))
            ->willReturn(['total' => '19']);

        $this->assertSame(19, Paintings::getAllPaintingsCount($dbMock));
    }

    /**
     * Tests that paginated paintings cast limit and offset before SQL is built.
     */
    public function testGetAllPaintingsPaginatedCastsLimitAndOffset()
    {
        // Pagination values should become integers before they are appended to the query.
        $rows = [['id' => 4]];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with($this->logicalAnd(
                $this->stringContains('LIMIT 6 OFFSET 12'),
                $this->stringContains("artists.status = 'approved'")
            ))
            ->willReturn($rows);

        $this->assertSame($rows, Paintings::getAllPaintingsPaginated('6abc', '12abc', $dbMock));
    }

    /**
     * Tests that empty painting search count uses the plain approved count.
     */
    public function testGetSearchPaintingsCountUsesPlainCountForEmptySearch()
    {
        // Whitespace search should behave like no search.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with($this->stringContains("WHERE artists.status = 'approved'"))
            ->willReturn(['total' => '5']);

        $this->assertSame(5, Paintings::getSearchPaintingsCount('  ', $dbMock));
    }

    /**
     * Tests that non-empty painting search binds title, description, and artist LIKE values.
     */
    public function testGetSearchPaintingsCountBindsLikeParameters()
    {
        // Search should stay parameterized across all three searchable fields.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->callback(function ($query) {
                    return substr_count($query, 'LIKE ?') === 3
                        && strpos($query, 'paintings.title LIKE ?') !== false
                        && strpos($query, 'artists.name LIKE ?') !== false;
                }),
                $this->equalTo(['%sun%', '%sun%', '%sun%'])
            )
            ->willReturn(['total' => '2']);

        $this->assertSame(2, Paintings::getSearchPaintingsCount('sun', $dbMock));
    }

    /**
     * Tests that empty paginated search delegates to normal pagination.
     */
    public function testGetSearchPaintingsPaginatedUsesRegularPaginationForEmptySearch()
    {
        // Empty search should use the same approved listing query as normal pagination.
        $rows = [['id' => 9]];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with($this->stringContains('LIMIT 8 OFFSET 16'))
            ->willReturn($rows);

        $this->assertSame($rows, Paintings::getSearchPaintingsPaginated('', 8, 16, $dbMock));
    }

    /**
     * Tests that non-empty paginated search binds LIKE parameters and pagination.
     */
    public function testGetSearchPaintingsPaginatedBindsLikeParameters()
    {
        // Search pagination should bind wildcard values and append integer pagination.
        $rows = [['id' => 10]];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with(
                $this->callback(function ($query) {
                    return substr_count($query, 'LIKE ?') === 3
                        && strpos($query, 'LIMIT 4 OFFSET 20') !== false;
                }),
                $this->equalTo(['%portrait%', '%portrait%', '%portrait%'])
            )
            ->willReturn($rows);

        $this->assertSame($rows, Paintings::getSearchPaintingsPaginated('portrait', 4, 20, $dbMock));
    }

    /**
     * Tests that category lookup binds category id and requires approved artists.
     */
    public function testGetPaintingsByCategoryIdUsesCategoryParameter()
    {
        // Category pages should show only paintings from approved artists.
        $rows = [['id' => 12, 'category_id' => 3]];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with(
                $this->logicalAnd(
                    $this->stringContains('WHERE paintings.category_id = ?'),
                    $this->stringContains("artists.status = 'approved'")
                ),
                $this->equalTo([3])
            )
            ->willReturn($rows);

        $this->assertSame($rows, Paintings::getPaintingsByCategoryID(3, $dbMock));
    }

    /**
     * Tests that compact artist painting lookup binds artist id.
     */
    public function testGetPaintingsByArtistIdUsesArtistParameter()
    {
        // Compact artist lookup should request only id, title, and image.
        $rows = [['id' => 15, 'title' => 'Study', 'image' => 'study.jpg']];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with(
                $this->equalTo('SELECT id, title, image FROM paintings where artist_id = ? ORDER BY id DESC'),
                $this->equalTo([7])
            )
            ->willReturn($rows);

        $this->assertSame($rows, Paintings::getPaintingsByArtistID(7, $dbMock));
    }

    /**
     * Tests that portfolio lookup joins category names and binds artist id.
     */
    public function testGetPaintingsByArtistPortfolioUsesArtistParameter()
    {
        // Portfolio listing should include category display names for each painting.
        $rows = [['id' => 16, 'category_name' => 'Sketch']];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with(
                $this->logicalAnd(
                    $this->stringContains('categories.name AS category_name'),
                    $this->stringContains('WHERE paintings.artist_id = ?')
                ),
                $this->equalTo([8])
            )
            ->willReturn($rows);

        $this->assertSame($rows, Paintings::getPaintingsByArtistPortfolio(8, $dbMock));
    }

    /**
     * Tests that internal painting lookup binds painting id.
     */
    public function testGetPaintingByIdUsesPaintingParameter()
    {
        // Internal lookup should include category and artist display fields.
        $painting = ['id' => 18, 'artist_name' => 'Artist'];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->logicalAnd(
                    $this->stringContains('categories.name AS category_name'),
                    $this->stringContains('WHERE paintings.id = ?')
                ),
                $this->equalTo([18])
            )
            ->willReturn($painting);

        $this->assertSame($painting, Paintings::getPaintingByID(18, $dbMock));
    }

    /**
     * Tests that public painting lookup requires approved artist status.
     */
    public function testGetPublicPaintingByIdRequiresApprovedArtist()
    {
        // Public detail pages should not expose paintings from non-approved artists.
        $painting = ['id' => 19, 'artist_name' => 'Artist'];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->logicalAnd(
                    $this->stringContains('WHERE paintings.id = ?'),
                    $this->stringContains("AND artists.status = 'approved'")
                ),
                $this->equalTo([19])
            )
            ->willReturn($painting);

        $this->assertSame($painting, Paintings::getPublicPaintingByID(19, $dbMock));
    }

    /**
     * Tests that empty file hashes return null without querying the database.
     */
    public function testGetPaintingByFileHashReturnsNullForEmptyHash()
    {
        // Empty hashes are ignored before any database lookup is attempted.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->never())
            ->method('getOne');

        $this->assertNull(Paintings::getPaintingByFileHash('', $dbMock));
    }

    /**
     * Tests that file hash lookup binds the hash value.
     */
    public function testGetPaintingByFileHashUsesHashParameter()
    {
        // Duplicate upload checks should query by the stored file hash.
        $painting = ['id' => 22, 'title' => 'Duplicate'];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT id, title, image FROM paintings WHERE file_hash = ? LIMIT 1'),
                $this->equalTo(['abc123'])
            )
            ->willReturn($painting);

        $this->assertSame($painting, Paintings::getPaintingByFileHash('abc123', $dbMock));
    }

    /**
     * Tests that inserting a painting with a file hash stores the hash and returns the new id.
     */
    public function testInsertPaintingWithFileHashReturnsLastInsertId()
    {
        // Insert with file hash should include the file_hash column and return the generated id.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->callback(function ($query) {
                    return strpos($query, 'INSERT INTO paintings') !== false
                        && strpos($query, 'file_hash') !== false;
                }),
                $this->equalTo([
                    'Water',
                    'Description',
                    'water.jpg',
                    'hash-1',
                    '2024',
                    2,
                    5,
                    'Oil',
                    '30x40',
                    1000,
                ])
            )
            ->willReturn(true);
        $dbMock->expects($this->once())
            ->method('getLastInsertId')
            ->willReturn('44');

        $this->assertSame('44', Paintings::insertPainting($this->paintingData(['file_hash' => 'hash-1']), $dbMock));
    }

    /**
     * Tests that inserting a painting without a file hash omits the hash column.
     */
    public function testInsertPaintingWithoutFileHashOmitsHashColumn()
    {
        // Insert without file hash should use the shorter column and parameter list.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->callback(function ($query) {
                    return strpos($query, 'INSERT INTO paintings') !== false
                        && strpos($query, 'file_hash') === false;
                }),
                $this->equalTo([
                    'Water',
                    'Description',
                    'water.jpg',
                    '2024',
                    2,
                    5,
                    'Oil',
                    '30x40',
                    1000,
                ])
            )
            ->willReturn(true);
        $dbMock->expects($this->once())
            ->method('getLastInsertId')
            ->willReturn('45');

        $this->assertSame('45', Paintings::insertPainting($this->paintingData(), $dbMock));
    }

    /**
     * Tests that updating a painting with a file hash binds all editable fields.
     */
    public function testUpdatePaintingWithFileHashUsesHashParameters()
    {
        // Update with a new upload should include the file_hash column.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->callback(function ($query) {
                    return strpos($query, 'UPDATE paintings') !== false
                        && strpos($query, 'file_hash = ?') !== false
                        && strpos($query, 'WHERE id = ? AND artist_id = ?') !== false;
                }),
                $this->equalTo([
                    'Water',
                    'Description',
                    'water.jpg',
                    'hash-2',
                    '2024',
                    2,
                    'Oil',
                    '30x40',
                    1000,
                    30,
                    5,
                ])
            )
            ->willReturn(true);

        $this->assertTrue(Paintings::updatePainting(30, $this->paintingData(['file_hash' => 'hash-2']), $dbMock));
    }

    /**
     * Tests that updating a painting without a file hash omits the hash field.
     */
    public function testUpdatePaintingWithoutFileHashOmitsHashColumn()
    {
        // Update without a new hash should leave the stored hash untouched.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->callback(function ($query) {
                    return strpos($query, 'UPDATE paintings') !== false
                        && strpos($query, 'file_hash = ?') === false;
                }),
                $this->equalTo([
                    'Water',
                    'Description',
                    'water.jpg',
                    '2024',
                    2,
                    'Oil',
                    '30x40',
                    1000,
                    30,
                    5,
                ])
            )
            ->willReturn(true);

        $this->assertTrue(Paintings::updatePainting(30, $this->paintingData(), $dbMock));
    }

    /**
     * Tests that deleting a painting binds both painting id and artist id.
     */
    public function testDeletePaintingUsesPaintingAndArtistParameters()
    {
        // Delete should be scoped to the artist that owns the painting.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('DELETE FROM paintings WHERE id = ? AND artist_id = ?'),
                $this->equalTo([31, 5])
            )
            ->willReturn(true);

        $this->assertTrue(Paintings::deletePainting(31, 5, $dbMock));
    }

    /**
     * Tests that missing collection records return an empty painting list.
     */
    public function testGetPaintingsByCollectionIdReturnsEmptyForMissingCollection()
    {
        // A missing collection should stop before any painting query runs.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(false);
        $dbMock->expects($this->never())
            ->method('getAll');

        $this->assertSame([], Paintings::getPaintingsByCollectionID(99, $dbMock));
    }

    /**
     * Tests that keyword collections bind title and description LIKE parameters.
     */
    public function testGetPaintingsByCollectionIdHandlesKeywordCollection()
    {
        // Keyword collections should search titles and descriptions with the collection parameter.
        $rows = [['id' => 40]];

        $dbMock = $this->collectionDbMock(['id' => 1, 'type' => 'keyword', 'param' => 'blue']);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with(
                $this->callback(function ($query) {
                    return strpos($query, 'paintings.title LIKE ?') !== false
                        && strpos($query, 'paintings.description LIKE ?') !== false;
                }),
                $this->equalTo(['%blue%', '%blue%'])
            )
            ->willReturn($rows);

        $this->assertSame($rows, Paintings::getPaintingsByCollectionID(1, $dbMock));
    }

    /**
     * Tests that latest collections request the newest approved paintings.
     */
    public function testGetPaintingsByCollectionIdHandlesLatestCollection()
    {
        // Latest collections should order approved paintings by creation date.
        $rows = [['id' => 41]];

        $dbMock = $this->collectionDbMock(['id' => 2, 'type' => 'latest', 'param' => '']);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with($this->logicalAnd(
                $this->stringContains('ORDER BY paintings.created_at DESC'),
                $this->stringContains('LIMIT 10')
            ))
            ->willReturn($rows);

        $this->assertSame($rows, Paintings::getPaintingsByCollectionID(2, $dbMock));
    }

    /**
     * Tests that random collections request random approved paintings.
     */
    public function testGetPaintingsByCollectionIdHandlesRandomCollection()
    {
        // Random collections should use RAND() and keep the result limited.
        $rows = [['id' => 42]];

        $dbMock = $this->collectionDbMock(['id' => 3, 'type' => 'random', 'param' => '']);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with($this->logicalAnd(
                $this->stringContains('ORDER BY RAND()'),
                $this->stringContains('LIMIT 10')
            ))
            ->willReturn($rows);

        $this->assertSame($rows, Paintings::getPaintingsByCollectionID(3, $dbMock));
    }

    /**
     * Tests that popular collections order paintings by views.
     */
    public function testGetPaintingsByCollectionIdHandlesPopularCollection()
    {
        // Popular collections should use the views column for ranking.
        $rows = [['id' => 43]];

        $dbMock = $this->collectionDbMock(['id' => 4, 'type' => 'popular', 'param' => '']);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with($this->stringContains('ORDER BY paintings.views DESC'))
            ->willReturn($rows);

        $this->assertSame($rows, Paintings::getPaintingsByCollectionID(4, $dbMock));
    }

    /**
     * Tests that AI collections split keywords and bind them as tag names.
     */
    public function testGetPaintingsByCollectionIdHandlesAiCollection()
    {
        // AI collections should join tags and rank paintings by matched tag count.
        $rows = [['id' => 44]];

        $dbMock = $this->collectionDbMock(['id' => 5, 'type' => 'ai', 'param' => 'Sky Ocean']);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with(
                $this->logicalAnd(
                    $this->stringContains('JOIN painting_tags pt'),
                    $this->stringContains('t.name IN (?,?)'),
                    $this->stringContains('ORDER BY COUNT(t.id) DESC')
                ),
                $this->equalTo(['sky', 'ocean'])
            )
            ->willReturn($rows);

        $this->assertSame($rows, Paintings::getPaintingsByCollectionID(5, $dbMock));
    }

    /**
     * Tests that unsupported collection types return an empty list.
     */
    public function testGetPaintingsByCollectionIdReturnsEmptyForUnknownType()
    {
        // Unknown collection types should not run a painting query.
        $dbMock = $this->collectionDbMock(['id' => 6, 'type' => 'unknown', 'param' => '']);
        $dbMock->expects($this->never())
            ->method('getAll');

        $this->assertSame([], Paintings::getPaintingsByCollectionID(6, $dbMock));
    }

    private function paintingData(array $overrides = [])
    {
        return array_merge([
            'title' => 'Water',
            'description' => 'Description',
            'image' => 'water.jpg',
            'year_created' => '2024',
            'category_id' => 2,
            'artist_id' => 5,
            'medium' => 'Oil',
            'dimensions' => '30x40',
            'price' => 1000,
        ], $overrides);
    }

    private function collectionDbMock(array $collection)
    {
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT * FROM collections WHERE id = ?'),
                $this->equalTo([$collection['id']])
            )
            ->willReturn($collection);

        return $dbMock;
    }
}
