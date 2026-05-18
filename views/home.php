<?php
/**
 * Home Page View
 * Displays main landing page with hero section and featured exhibitions
 */
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0"> <?php echo date('F Y'); ?> Online Exhibition</h2>
</div>
<?php
ViewSlider::render($sliderPaintings);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Our Artists</h2>
    <a href="artists" class="section-link">View All <span class="ms-1 small">&#8599;</span></a>
</div>


<?php
    ViewArtists::ArtistsGrid($artistArr);
?>

<h2 class="mb-3">Gallery Collection</h2>
<?php
    ViewPaintings::PaintingsGrid($arr);
?>


<?php
$content = ob_get_clean();
include_once 'views/layout.php';
?>