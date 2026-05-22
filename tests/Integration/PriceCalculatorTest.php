<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/PriceCalculatorService.php';

class PriceCalculatorTest extends TestCase
{
    /**
     * Tests a basic price calculation through the integration test suite.
     */
    public function testCalculateFromPriceBasic()
    {
        // Calculates deductions from a fixed selling price and verifies the full breakdown.
        $res = PriceCalculatorService::calculateFromPrice(100, 15, 20, true, 0);
        $this->assertIsArray($res);
        $this->assertArrayHasKey('price', $res);
        $this->assertEquals(100.00, $res['price']);
        $this->assertEquals(15.00, $res['commissionAmount']);
        // tax = (price - commissionAmount) * taxRate = (100-15)*0.2 = 17.0
        $this->assertEquals(17.00, $res['taxAmount']);
        // netIncome = afterCommission - taxAmount - expenses = 85 - 17 = 68
        $this->assertEquals(68.00, $res['netIncome']);
    }

    /**
     * Tests desired-income calculation through the integration test suite.
     */
    public function testCalculateFromDesiredIncomeBasic()
    {
        // Works backwards from desired income and checks that the final price is higher than income.
        $res = PriceCalculatorService::calculateFromDesiredIncome(100, 15, 20, true, 0);
        $this->assertIsArray($res);
        $this->assertArrayHasKey('price', $res);
        $this->assertGreaterThan(100, $res['price']);
    }
}
