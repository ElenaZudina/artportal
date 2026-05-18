<?php
/**
 * Single Artist Profile Page View
 * Displays artist information, biography, and their paintings portfolio
 */
ob_start();
?>

<br>

<?php
ViewArtists::SingleArtist($item);

echo "<br>";


echo "<br>";


$content = ob_get_clean();
include_once 'views/layout.php';

?>