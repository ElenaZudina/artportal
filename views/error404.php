<?php
/**
 * Error 404 Page - displays 404 not found error
 * Shows user-friendly error message for missing pages
 */
ob_start();
// Default text is used when a controller does not provide a custom 404 message.
$errorTitle = $errorTitle ?? 'Oops! This canvas is blank... just like the page you were looking for.';
$errorMessage = $errorMessage ?? '';
$isErrorPage = true;
?>
<!-- Centered 404 message with optional supporting text. -->
<section class="error-page-shell" aria-labelledby="error-page-title">
    <div class="error-page-panel">
        <div class="error-page-code" aria-hidden="true">404</div>
        <h2 id="error-page-title"><?php echo htmlspecialchars($errorTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
        <?php if ($errorMessage !== ''): ?>
            <p class="lead mt-3 mb-0"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
    </div>
</section>
<?php
// Pass captured page markup into the shared layout.
$content = ob_get_clean();
include_once 'views/layout.php';
?>
