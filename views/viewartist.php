<?php
/**
 * Single Artist Profile Page View
 * Displays artist information, biography, and their paintings portfolio
 */
ob_start();
?>

<br>

<?php
// Render the artist detail partial with portfolio data prepared by the controller.
ViewArtists::SingleArtist($item);

echo "<br>";


echo "<br>";


// Pass captured page markup into the shared layout.
$content = ob_get_clean();
include_once 'views/layout.php';

?>
