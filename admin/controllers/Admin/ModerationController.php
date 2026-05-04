<?php

class ModerationController {
    public static function pendingList() {
        $arr = Artists::getPendingArtists();
        include_once 'views/moderation-artists-list.php';
    }

    public static function viewProfile($id) {
        $item = Artists::getArtistByIDAny($id);
        if (!$item) {
            include_once 'views/error404.php';
            return;
        }
        $item['paintings'] = Paintings::getPaintingsByArtistID($id);//может быть, лишнее в профиле

        include_once 'views/moderation-artist-view.php';
    }

    public static function approve($id) {
        Artists::approveArtist($id);
        header('Location: moderation-artists');
        exit;
    }

    public static function reject($id) {
        Artists::rejectArtist($id);
        header('Location: moderation-artists');
        exit;
    }
}
