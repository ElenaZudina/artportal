<?php
// 404 error page view
// Output buffering for template rendering
ob_start();
?>
<!-- Main message for 404 error page -->
<section class="error-page-shell" aria-labelledby="error-page-title">
    <div class="error-page-panel">
        <div class="error-page-code" aria-hidden="true">404</div>
        <h2 id="error-page-title">Oops! This canvas is blank&hellip; just like the page you were looking for.</h2>
    </div>
</section>
<?php
// End output buffering and include layout template
$content = ob_get_clean();
include_once 'views/templates/layout.php';
?>
