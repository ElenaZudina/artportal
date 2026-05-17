<?php
function getMenuCategories() {
    static $categories = null;

    if ($categories === null) {
        $categories = Categories::getAllCategories();
    }

    return $categories;
}
