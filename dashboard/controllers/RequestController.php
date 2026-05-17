<?php
require_once __DIR__ . '/../../services/EmailService.php';

class RequestController {

    public static function create() {
        Auth::requireSession('user', 'Only registered users can send purchase requests.');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artportal/');
            exit;
        }

        if (!CsrfHelper::validate()) {
            $_SESSION['errorString'] = 'Invalid form token. Please try again.';
            header('Location: /artportal/');
            exit;
        }

        $paintingId = isset($_POST['painting_id']) ? (int)$_POST['painting_id'] : 0;
        $userId = (int)$_SESSION['userId'];

        if ($paintingId <= 0) {
            $redirect = $_SERVER['HTTP_REFERER'] ?? '/artportal/';
            header('Location: ' . $redirect);
            exit;
        }

        $painting = Paintings::getPublicPaintingByID($paintingId);
        if (!$painting) {
            header('Location: /artportal/');
            exit;
        }

        if (!empty($painting['artist_id']) && (int)$painting['artist_id'] === $userId) {
            header('Location: /artportal/');
            exit;
        }

        $lastRequestTime = PurchaseRequest::getLastRequestTime($userId, $paintingId);
        if ($lastRequestTime !== null) {
            $currentTime = time();
            $timePassed = $currentTime - $lastRequestTime;
            $timeInterval = 3600;
            
            if ($timePassed < $timeInterval) {
                $minutesRemaining = ceil(($timeInterval - $timePassed) / 60);
                $_SESSION['errorString'] = 'You can send a new request for this painting in ' . $minutesRemaining . ' minute(s). Please wait.';
                $redirect = $_SERVER['HTTP_REFERER'] ?? '/artportal/';
                header('Location: ' . $redirect);
                exit;
            }
        }

        $result = PurchaseRequest::create($userId, $paintingId);

        if (!empty($result['success'])) {
            $_SESSION['successString'] = $result['message'] ?? 'Request sent successfully';
            
            $request = PurchaseRequest::getRequestById($result['id']);
            if ($request) {
                $emailSent = EmailService::sendPurchaseRequestNotification($request);
                if (!$emailSent) {
                    $_SESSION['warningString'] = 'Request saved, but email notification to artist failed.';
                }
            }
        } else {
            $_SESSION['errorString'] = $result['message'] ?? 'Failed to create request';
        }

        $redirect = $_SERVER['HTTP_REFERER'] ?? '/artportal/';
        header('Location: ' . $redirect);
        exit;
    }

    public static function myRequests() {
        Auth::requireSession();

        $requests = PurchaseRequest::getUserRequests((int)$_SESSION['userId'], 100, 0) ?? [];
        include_once('views/my-requests.php');
    }

    public static function purchaseRequests() {
        Auth::requireSession();

        if (($_SESSION['status'] ?? '') !== 'artist') {
            header('Location: /artportal/dashboard/startDashboard');
            exit;
        }

        $artist = Artists::getArtistByUserId((int)$_SESSION['userId']);
        $artistId = $artist['id'] ?? null;

        if (empty($artistId)) {
            $requests = [];
        } else {
            $requests = PurchaseRequest::getArtistRequests($artistId, 100, 0) ?? [];
        }

        include_once('views/purchase-requests.php');
    }
}
?>
