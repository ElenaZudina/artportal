<?php
ob_start();
?>

<br>

<?php
ViewPaintings::OnePainting($item);

echo "<br>";
//Controller::Comments($_GET['id']);

echo "<br>";
//ViewComments::CommentsForm();

$content = ob_get_clean();
include_once 'views/layout.php';

?>