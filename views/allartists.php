<?php
ob_start();
?>
<h1>All artists </h1>
<br>

<?php
ViewArtists::ArtistsGrid($arr, true);
$content = ob_get_clean();
include_once 'views/layout.php';

?>