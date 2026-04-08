<?php
ob_start();
?>

<br>

<?php
ViewArtists::SingleArtist($item);

echo "<br>";
//Controller::Comments($_GET['id']);

echo "<br>";
//ViewComments::CommentsForm();

$content = ob_get_clean();
include_once 'views/layout.php';

?>