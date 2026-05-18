<?php

require_once __DIR__ . '/../../services/PriceCalculatorService.php';

/**
 * Dashboard Price Controller - manages painting pricing
 * Calculates and manages painting prices and costs
 */
class PriceController {
    public static function calculate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        header('Content-Type: application/json');

        if (empty($_SESSION['userId'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = $_POST;
        $mode = $input['mode'] ?? null;
        $value = $input['value'] ?? null;
        $commission = $input['commission'] ?? null;
        $tax = $input['tax'] ?? null;
        $isTaxResident = $input['isTaxResident'] ?? true;
        $expenses = $input['expenses'] ?? 0;

        if (!$mode || $value === null || $commission === null || $tax === null) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        if ($mode !== 'income' && $mode !== 'price') {
            echo json_encode(['success' => false, 'message' => 'Invalid mode']);
            exit;
        }

        $value = (float) $value;
        $commission = (float) $commission;
        $tax = (float) $tax;
        $expenses = (float) $expenses;
        $isTaxResident = (bool) $isTaxResident;

        try {
            if ($mode === 'income') {
                $result = PriceCalculatorService::calculateFromDesiredIncome(
                    $value,
                    $commission,
                    $tax,
                    $isTaxResident,
                    $expenses
                );
            } else {
                $result = PriceCalculatorService::calculateFromPrice(
                    $value,
                    $commission,
                    $tax,
                    $isTaxResident,
                    $expenses
                );
            }

            if (isset($result['error'])) {
                echo json_encode(['success' => false, 'message' => $result['error'] ?? 'Unknown error']);
            } else {
                $result['expenses'] = $expenses;
                echo json_encode(['success' => true, 'data' => $result]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}
