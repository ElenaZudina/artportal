<?php
ob_start();
?>
<h2>Oops! This canvas is blank… just like the page you were looking for.</h2>
<?php
$content = ob_get_clean();
include_once 'views/templates/layout.php';
?>