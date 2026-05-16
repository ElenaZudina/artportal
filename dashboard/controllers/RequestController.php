<?php
class RequestController {

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