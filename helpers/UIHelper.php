<?php
class UIHelper {
    /**
     * Returns paintings for the hero section
     * @param int $limit
     * @return array
     */
    public static function getHeroPaintings($limit = 3) {
        return Paintings::getLastPaintings($limit);
    }
}
