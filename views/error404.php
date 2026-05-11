<?php
ob_start();
$errorTitle = $errorTitle ?? 'Oops! This canvas is blank… just like the page you were looking for.';
$errorMessage = $errorMessage ?? '';
?>
<div class="text-center py-5">
	<h2><?php echo htmlspecialchars($errorTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
	<?php if ($errorMessage !== ''): ?>
		<p class="lead mt-3 mb-0"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
	<?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include_once 'views/layout.php';
?>