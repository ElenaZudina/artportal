<?php
/**
 * Menu Categories Partial - renders category dropdown menu items
 * Displays category list for navigation dropdown
 */
echo "<li><a class='dropdown-item' href='all'>All</a></li>";
foreach($arr as $value) {
    echo "<li><a class='dropdown-item' href='category?id=" . urlencode($value['id']) . "'>" . htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8') . "</a></li>";
}

?>