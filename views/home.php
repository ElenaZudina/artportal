<?php
ob_start();
?>

<h2 class="mt-5">Our Artists</h2>
    <?php
        ViewArtists::ArtistsGrid($artistArr);
    ?>
    
<div class="container my-4">
    <h2 class="mb-3">Gallery Collection</h2>
    <?php
        ViewPaintings::PaintingsGrid($arr);
    ?>
</div>


<?php
$content = ob_get_clean();
include_once 'views/layout.php';
?>