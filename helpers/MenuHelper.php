<?php
/**
 * Menu Helper - provides category menu data for navigation
 * Caches categories to avoid multiple database queries
 */

/**
 * Get all product categories for menu navigation
 * Caches result using static variable for performance
 * @return array Array of all categories
 */
function getMenuCategories() {
    static $categories = null;

    if ($categories === null) {
        $categories = Categories::getAllCategories();
    }

    return $categories;
}
