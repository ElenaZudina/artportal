<?php
class PurchaseRequestController {
    
    public static function create() {
        // Проверка авторизации
        if (empty($_SESSION['userId'])) {
            header('Location: /artportal/login');
            exit;
        }

        // Только обычный пользователь может отправлять заявку
        if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'user') {
            header('Location: /artportal/');
            exit;
        }

        // Проверка метода запроса
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artportal/');
            exit;
        }

        // Получение painting_id
        $paintingId = isset($_POST['painting_id']) ? (int)$_POST['painting_id'] : 0;
        $userId = (int)$_SESSION['userId'];

        // Валидация painting_id
        if ($paintingId <= 0) {
            $redirect = $_SERVER['HTTP_REFERER'] ?? '/artportal/';
            header('Location: ' . $redirect);
            exit;
        }

        // Проверка существования картины
        $painting = Paintings::getPaintingByID($paintingId);
        if (!$painting) {
            header('Location: /artportal/');
            exit;
        }

        // Нельзя отправить заявку на свою же картину
        if (!empty($painting['artist_id']) && (int)$painting['artist_id'] === $userId) {
            header('Location: /artportal/');
            exit;
        }

        // Создание записи в БД
        $result = PurchaseRequest::create($userId, $paintingId);

        if (!empty($result['success'])) {
            $_SESSION['successString'] = $result['message'] ?? 'Request sent successfully';
        } else {
            $_SESSION['errorString'] = $result['message'] ?? 'Failed to create request';
        }

        // Redirect назад
        $redirect = $_SERVER['HTTP_REFERER'] ?? '/artportal/';
        header('Location: ' . $redirect);
        exit;
    }
}
?>
