<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/PriceCalculatorService.php';

class PriceCalculatorTest extends TestCase
{
    public function testCalculateFromPriceBasic()
    {
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

    public function testCalculateFromDesiredIncomeBasic()
    {
        $res = PriceCalculatorService::calculateFromDesiredIncome(100, 15, 20, true, 0);
        $this->assertIsArray($res);
        $this->assertArrayHasKey('price', $res);
        $this->assertGreaterThan(100, $res['price']);
    }
}
