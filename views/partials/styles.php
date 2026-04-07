<?php
echo "<li class='submenuunit'><a href='all'>ALL</a></li><br>";
foreach($arr as $value) {
    echo "<li class='submenuunit'>
    <a href='style?id=" . urlencode($value['id']) . "'>" . htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8') .'</a></li><br>';
}

?>