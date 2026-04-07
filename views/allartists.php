<?php
ob_start();
?>
<h1>All artists </h1>
<br>

<?php
ViewArtists::ArtistsList($arr);
$content = ob_get_clean();
include_once 'views/layout.php';

?>