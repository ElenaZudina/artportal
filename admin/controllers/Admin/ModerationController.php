<?php

/**
 * Admin Moderation Controller - manages artist profile approvals
 * Handles artist profile review, approval, and rejection
 */
class ModerationController {
    /**
     * List pending artist profiles
     * Retrieves artists awaiting moderation and renders the list view
     */
    public static function pendingList() {
        $arr = Artists::getPendingArtists();
        include_once 'views/moderation-artists-list.php';
    }

    /**
     * Show a single artist profile for review
     * Loads artist data and associated paintings for moderator review
     * @param int $id Artist identifier
     */
    public static function viewProfile($id) {
        $item = Artists::getArtistByID($id);
        if (!$item) {
            include_once 'views/error404.php';
            return;
        }
        // Optionally include artist's paintings in the moderation view
        $item['paintings'] = Paintings::getPaintingsByArtistID($id);

        include_once 'views/moderation-artist-view.php';
    }

    /**
     * Approve artist profile
     * Marks the artist profile as approved and redirects to list
     * @param int $id Artist identifier
     */
    public static function approve($id) {
        Artists::approveArtist($id);
        header('Location: moderation-artists');
        exit;
    }

    /**
     * Reject artist profile
     * Marks the artist profile as rejected and redirects to list
     * @param int $id Artist identifier
     */
    public static function reject($id) {
        Artists::rejectArtist($id);
        header('Location: moderation-artists');
        exit;
    }
}
