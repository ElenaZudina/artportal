<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../models/Tags.php';
require_once __DIR__ . '/../../config/Database.php';

class TagsTest extends TestCase
{
    /**
     * Tests that an existing tag id is returned without inserting a new row.
     */
    public function testGetOrCreateTagReturnsExistingTagId()
    {
        // Existing tag lookup should short-circuit before insert.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT id FROM tags WHERE name = ?'),
                $this->equalTo(['abstract'])
            )
            ->willReturn(['id' => '7']);
        $dbMock->expects($this->never())
            ->method('executeRun');
        $dbMock->expects($this->never())
            ->method('getLastInsertId');

        $this->assertSame(7, Tags::getOrCreateTag('abstract', $dbMock));
    }

    /**
     * Tests that a missing tag is inserted and the new id is returned.
     */
    public function testGetOrCreateTagCreatesMissingTag()
    {
        // Missing tag lookup should insert the tag and return the generated id.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT id FROM tags WHERE name = ?'),
                $this->equalTo(['landscape'])
            )
            ->willReturn(null);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('INSERT INTO tags (name) VALUES (?)'),
                $this->equalTo(['landscape'])
            );
        $dbMock->expects($this->once())
            ->method('getLastInsertId')
            ->willReturn('12');

        $this->assertSame(12, Tags::getOrCreateTag('landscape', $dbMock));
    }
}
