<?php
/**
 * UI Helper - provides helper methods for displaying UI components
 * Retrieves data for specific page sections
 */
class UIHelper {
    
    /**
     * Get recent paintings to display in hero section slider
     * Returns newest paintings from approved artists
     * @param int $limit Number of paintings to retrieve (default: 3)
     * @return array Array of painting data for hero display
     */
    public static function getHeroPaintings($limit = 3) {
        return Paintings::getLastPaintings($limit);
    }
}
