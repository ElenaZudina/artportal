<?php
require __DIR__ . '/../services/PriceCalculatorService.php';

$resultfromPrice = PriceCalculatorService::calculateFromPrice(
    100,
    15,
    20,
    true,
    0
);

$resultfromDesiredIncome = PriceCalculatorService::calculateFromDesiredIncome(
    100,
    15,
    20,
    true,
    0
);

print_r($resultfromPrice);
print_r($resultfromDesiredIncome);
?>