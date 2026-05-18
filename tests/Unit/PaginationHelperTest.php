<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../helpers/PaginationHelper.php';

class PaginationHelperTest extends TestCase
{
    /**
     * Clears query parameters before each pagination test.
     */
    protected function setUp(): void
    {
        // Each test owns the page query value explicitly.
        $_GET = [];
    }

    /**
     * Clears query parameters after each pagination test.
     */
    protected function tearDown(): void
    {
        $_GET = [];
    }

    /**
     * Tests that missing page query defaults to the first page.
     */
    public function testDefaultsToFirstPageWhenPageIsMissing()
    {
        // Missing page parameter should produce the first page and zero offset.
        $result = PaginationHelper::getPaginationData(25, 10);

        $this->assertSame(1, $result['pagination']['currentPage']);
        $this->assertSame(0, $result['pagination']['offset']);
        $this->assertSame(3, $result['pagination']['totalPages']);
        $this->assertSame(10, $result['pagination']['perPage']);
    }

    /**
     * Tests that pagination calculates the correct offset for a requested page.
     */
    public function testCalculatesOffsetForRequestedPage()
    {
        // Page 3 with 10 items per page starts after the first 20 items.
        $_GET['page'] = '3';

        $result = PaginationHelper::getPaginationData(45, 10);

        $this->assertSame(3, $result['pagination']['currentPage']);
        $this->assertSame(20, $result['pagination']['offset']);
    }

    /**
     * Tests that page numbers lower than one are clamped to page one.
     */
    public function testPageLowerThanOneIsClampedToFirstPage()
    {
        // Invalid low page numbers are normalized before offset calculation.
        $_GET['page'] = '-5';

        $result = PaginationHelper::getPaginationData(45, 10);

        $this->assertSame(1, $result['pagination']['currentPage']);
        $this->assertSame(0, $result['pagination']['offset']);
    }

    /**
     * Tests that page numbers beyond the result set are clamped to the last page.
     */
    public function testPageGreaterThanTotalPagesIsClampedToLastPage()
    {
        // Requests past the end should land on the last available page.
        $_GET['page'] = '99';

        $result = PaginationHelper::getPaginationData(25, 10);

        $this->assertSame(3, $result['pagination']['currentPage']);
        $this->assertSame(20, $result['pagination']['offset']);
    }

    /**
     * Tests that empty result sets report zero total pages.
     */
    public function testZeroItemsKeepsTotalPagesAtZero()
    {
        // Empty collections have no pages, while the current page remains the safe default.
        $result = PaginationHelper::getPaginationData(0, 10);

        $this->assertSame(1, $result['pagination']['currentPage']);
        $this->assertSame(0, $result['pagination']['offset']);
        $this->assertSame(0, $result['pagination']['totalPages']);
    }
}
