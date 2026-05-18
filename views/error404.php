<?php
/**
 * Error 404 Page - displays 404 not found error
 * Shows user-friendly error message for missing pages
 */
ob_start();
// Default text is used when a controller does not provide a custom 404 message.
$errorTitle = $errorTitle ?? 'Oops! This canvas is blank… just like the page you were looking for.';
$errorMessage = $errorMessage ?? '';
?>
<!-- Centered 404 message with optional supporting text. -->
<div class="text-center py-5">
	<h2><?php echo htmlspecialchars($errorTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
	<?php if ($errorMessage !== ''): ?>
		<p class="lead mt-3 mb-0"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
	<?php endif; ?>
</div>
<?php
// Pass captured page markup into the shared layout.
$content = ob_get_clean();
include_once 'views/layout.php';
?>
