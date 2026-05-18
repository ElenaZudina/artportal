<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../models/PaintingTags.php';
require_once __DIR__ . '/../../config/Database.php';

class PaintingTagsTest extends TestCase
{
    /**
     * Tests that attaching a tag inserts the painting-tag relationship.
     */
    public function testAttachInsertsPaintingTagRelation()
    {
        // Attach should bind painting id and tag id in that order.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('INSERT INTO painting_tags (painting_id, tag_id) VALUES (?, ?)'),
                $this->equalTo([15, 4])
            );

        PaintingTags::attach(15, 4, $dbMock);
    }

    /**
     * Tests that detaching removes all tag links for one painting.
     */
    public function testDetachByPaintingIdDeletesPaintingRelations()
    {
        // Detach should delete by painting id only.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('DELETE FROM painting_tags WHERE painting_id = ?'),
                $this->equalTo([15])
            );

        PaintingTags::detachByPaintingId(15, $dbMock);
    }
}
