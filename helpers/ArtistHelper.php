<?php
/**
 * Artist Helper - provides helper functions for artist-specific operations
 */

/**
 * Require artist profile exists for current user
 * Redirects to profile creation if artist profile not found
 * @return array Artist profile data
 */
function requireArtistProfile() {
    $artist = Artists::getArtistByUserId((int)$_SESSION['userId']);
    if (!$artist) {
        header('Location: profile');
        exit;
    }
    return $artist;
}
