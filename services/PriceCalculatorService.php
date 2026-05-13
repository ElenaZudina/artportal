<?php

class PriceCalculatorService
{
    public static function calculateFromDesiredIncome(
        $desiredIncome,
        $commission,
        $tax,
        $isTaxResident,
        $expenses
    ) {

        $desiredIncome = max(0.0, $desiredIncome);
        $expenses = max(0.0, $expenses);

        // targetIncome includes desired income plus expenses (expenses are covered by the price)
        $targetIncome = $desiredIncome + $expenses;

        $commissionRate = $commission / 100;
        $taxRate = $isTaxResident ? $tax / 100 : 0.0;

        if ($commissionRate >= 1) {
            return [
                'price' => 0.0,
                'commissionAmount' => 0.0,
                'taxAmount' => 0.0,
                'netIncome' => 0.0,
                'error' => 'Invalid commission rate'
            ];
        }

        // сначала убираем налог (обратный шаг)
        $beforeTax = $isTaxResident
            ? $targetIncome / (1 - $taxRate)
            : $targetIncome;

        // потом убираем комиссию
        $price = $beforeTax / (1 - $commissionRate);

        $commissionAmount = $price * $commissionRate;
        $afterCommission = $price - $commissionAmount;
        $taxAmount = $afterCommission * $taxRate;
        // netIncome should reflect money received by the artist after commission and tax
        // expenses are already included in targetIncome, so do NOT subtract them here
        $netIncome = $afterCommission - $taxAmount;

        return [
            'price' => round($price, 2),
            'commissionAmount' => round($commissionAmount, 2),
            'taxAmount' => round($taxAmount, 2),
            'netIncome' => round($netIncome, 2),
        ];
    }

    public static function calculateFromPrice(
        $price,
        $commission,
        $tax,
        $isTaxResident,
        $expenses
    ) {

        $price = max(0.0, $price);
        $expenses = max(0.0, $expenses);

        $commissionRate = $commission / 100;
        $taxRate = $isTaxResident ? $tax / 100 : 0.0;

        $commissionAmount = $price * $commissionRate;
        $afterCommission = $price - $commissionAmount;

        $taxAmount = $afterCommission * $taxRate;

        $netIncome = $afterCommission - $taxAmount - $expenses;

        return [
            'price' => round($price, 2),
            'commissionAmount' => round($commissionAmount, 2),
            'taxAmount' => round($taxAmount, 2),
            'netIncome' => round($netIncome, 2),
        ];
    }
}
