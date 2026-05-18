<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/PriceCalculatorService.php';

class PriceCalculatorUnitTest extends TestCase
{
    /**
     * Tests the standard final-price breakdown with commission and tax.
     */
    public function testCalculateFromPriceBasic()
    {
        // Basic price calculation applies commission first, then tax.
        $res = PriceCalculatorService::calculateFromPrice(100, 15, 20, true, 0);
        $this->assertIsArray($res);
        $this->assertEquals(100.00, $res['price']);
        $this->assertEquals(15.00, $res['commissionAmount']);
        $this->assertEquals(17.00, $res['taxAmount']);
        $this->assertEquals(68.00, $res['netIncome']);
    }

    /**
     * Tests that no commission and no tax leave net income unchanged.
     */
    public function testCalculateFromPriceNoTaxNoCommission()
    {
        // No deductions should leave the full price as net income.
        $res = PriceCalculatorService::calculateFromPrice(200, 0, 0, false, 0);
        $this->assertEquals(200.00, $res['price']);
        $this->assertEquals(0.00, $res['commissionAmount']);
        $this->assertEquals(0.00, $res['taxAmount']);
        $this->assertEquals(200.00, $res['netIncome']);
    }

    /**
     * Tests that expenses are subtracted from net income.
     */
    public function testCalculateFromPriceWithExpenses()
    {
        // Expenses reduce net income after commission and tax.
        $res = PriceCalculatorService::calculateFromPrice(150, 10, 10, true, 5);
        $this->assertEquals(150.00, $res['price']);
        $this->assertEquals(15.00, $res['commissionAmount']);
        $this->assertEquals(13.50, $res['taxAmount']);
        $this->assertEquals(116.50, $res['netIncome']);
    }

    /**
     * Tests that impossible commission rates return an error.
     */
    public function testCalculateFromDesiredIncomeEdgeCommission100()
    {
        // A 100% commission would divide by zero, so the service returns an error.
        $res = PriceCalculatorService::calculateFromDesiredIncome(100, 100, 20, true, 0);
        $this->assertArrayHasKey('error', $res);
    }

    /**
     * Tests that negative price inputs are clamped to zero.
     */
    public function testNegativeInputsClamped()
    {
        // Negative price and expenses are clamped to zero before calculation.
        $res = PriceCalculatorService::calculateFromPrice(-10, -5, -20, true, -100);
        $this->assertEquals(0.00, $res['price']);
        $this->assertEquals(0.00, $res['commissionAmount']);
        $this->assertEquals(0.00, $res['taxAmount']);
        $this->assertEquals(0.00, $res['netIncome']);
    }
}
