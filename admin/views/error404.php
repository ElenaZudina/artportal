<?php
/**
 * 404 Error View
 * Displays not found message for admin panel
 */
ob_start();
?>
<!-- Main content: 404 error message -->
<h2>Oops! This canvas is blank… just like the page you were looking for.</h2>
<?php
$content = ob_get_clean();
include_once 'views/templates/layout.php';
?>