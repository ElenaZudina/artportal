<?php
/**
 * Paintings by Category View - displays paintings filtered by category
 * Shows category-specific painting gallery with search and pagination
 */
ob_start();
?>
<h1><?php echo htmlspecialchars($category['name'] ?? 'Paintings by category', ENT_QUOTES, 'UTF-8'); ?></h1>
<br>

<?php
// Render paintings already filtered by the selected category.
ViewPaintings::PaintingsGrid($arr, false, false);

// Pass captured page markup into the shared layout.
$content = ob_get_clean();
include_once 'views/layout.php';

?>
