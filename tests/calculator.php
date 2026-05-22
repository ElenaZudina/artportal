<?php
require __DIR__ . '/../services/PriceCalculatorService.php';

// Manual smoke check: calculate deductions from a fixed final price.
$resultfromPrice = PriceCalculatorService::calculateFromPrice(
    100,
    15,
    20,
    true,
    0
);

// Manual smoke check: calculate final price from desired artist income.
$resultfromDesiredIncome = PriceCalculatorService::calculateFromDesiredIncome(
    100,
    15,
    20,
    true,
    0
);

// Print both calculation results for quick browser/CLI inspection.
print_r($resultfromPrice);
print_r($resultfromDesiredIncome);
?>
