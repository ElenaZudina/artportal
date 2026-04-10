<?php
ob_start();
?>
<h1> Paintings (styles)</h1>
<br>

<?php
ViewPaintings::PaintingsGrid($arr);
$content = ob_get_clean();
include_once 'views/layout.php';

?>