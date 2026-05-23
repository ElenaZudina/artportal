<?php
/**
 * 404 Error View
 * Displays not found message for admin panel
 */
ob_start();
?>
<!-- Main content: 404 error message -->
<section class="error-page-shell" aria-labelledby="error-page-title">
    <div class="error-page-panel">
        <div class="error-page-code" aria-hidden="true">404</div>
        <h2 id="error-page-title">Oops! This canvas is blank&hellip; just like the page you were looking for.</h2>
    </div>
</section>
<?php
$content = ob_get_clean();
include_once 'views/templates/layout.php';
?>
