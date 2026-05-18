<?php
/**
 * Home Page View
 * Displays main landing page with hero section and featured exhibitions
 */
ob_start();
?>
<!-- Current exhibition heading shown above the homepage carousel. -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0"> <?php echo date('F Y'); ?> Online Exhibition</h2>
</div>
<?php
// Homepage carousel uses recently selected painting records.
ViewSlider::render($sliderPaintings);
?>

<!-- Featured artist preview with a link to the full artists page. -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Our Artists</h2>
    <a href="artists" class="section-link">View All <span class="ms-1 small">&#8599;</span></a>
</div>


<?php
    // Render a compact set of artists for the homepage.
    ViewArtists::ArtistsGrid($artistArr);
?>

<h2 class="mb-3">Gallery Collection</h2>
<?php
    // Render featured gallery paintings below the artist section.
    ViewPaintings::PaintingsGrid($arr);
?>


<?php
// Pass captured page markup into the shared layout.
$content = ob_get_clean();
include_once 'views/layout.php';
?>
