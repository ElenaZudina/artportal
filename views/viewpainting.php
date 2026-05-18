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
    <!-- Optional return link when the painting page is opened from dashboard. -->
    <a href="dashboard/startDashboard" class="btn btn-outline-secondary mb-3">
        ← Back to Dashboard
    </a>
<?php endif; ?>

<?php
// Render painting detail partial with current user's favorite state.
ViewPaintings::OnePainting($item, $isFavorite ?? false);

echo "<br>";


echo "<br>";


// Pass captured page markup into the shared layout.
$content = ob_get_clean();
include_once 'views/layout.php';

?>
