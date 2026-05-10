<?php
ob_start();
?>

<br>

<?php if (isset($_GET['from']) && $_GET['from'] === 'dashboard'): ?>
    <a href="dashboard/startDashboard" class="btn btn-outline-secondary mb-3">
        ← Back to Dashboard
    </a>
<?php endif; ?>

<?php
ViewPaintings::OnePainting($item);

echo "<br>";
//Controller::Comments($_GET['id']);

echo "<br>";
//ViewComments::CommentsForm();

$content = ob_get_clean();
include_once 'views/layout.php';

?>