<?php
ob_start();
?>
<h1>TOP 10 PAINTINGS </h1>
<br>
<?php
ViewPaintings::PaintingsList($arr);

$content = ob_get_clean();

include_once 'views/layout.php';

?>