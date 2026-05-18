<?php
// 404 error page view
// Output buffering for template rendering
ob_start();
?>
<!-- Main message for 404 error page -->
<h2>Oops! This canvas is blank just like the page you were looking for.</h2>
<?php
// End output buffering and include layout template
$content = ob_get_clean();
include_once 'views/templates/layout.php';
?>