<?php
/**
 * Single Painting Detail Page View
 * Shows painting information, artist details, price, and action buttons
 * Displays favorite status and purchase options
 */
ob_start();
?>

<br>

<?php if (isset($_GET['from']) && $_GET['from'] === 'dashboard'): ?>
    <a href="dashboard/startDashboard" class="btn btn-outline-secondary mb-3">
        ← Back to Dashboard
    </a>
<?php endif; ?>

<?php
ViewPaintings::OnePainting($item, $isFavorite ?? false);

echo "<br>";


echo "<br>";


$content = ob_get_clean();
include_once 'views/layout.php';

?>