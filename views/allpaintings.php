<?php
ob_start();
?>
<h1>All paintings </h1>
<br>

<?php
ViewPaintings::PaintingsList($arr);
$content = ob_get_clean();
include_once 'views/layout.php';

?>