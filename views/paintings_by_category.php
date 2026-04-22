<?php
ob_start();
?>
<h1><?php echo htmlspecialchars($category['name'] ?? 'Paintings by category', ENT_QUOTES, 'UTF-8'); ?></h1>
<br>

<?php
ViewPaintings::PaintingsGrid($arr, false, false);
$content = ob_get_clean();
include_once 'views/layout.php';

?>