<?php
echo "<li><a class='dropdown-item' href='all'>ALL</a></li>";
foreach($arr as $value) {
    echo "<li><a class='dropdown-item' href='category?id=" . urlencode($value['id']) . "'>" . htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8') . "</a></li>";
}

?>