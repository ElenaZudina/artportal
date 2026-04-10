<?php
ob_start();
?>
<div class="container my-4">
    <h2 class="mb-3">TOP 10 PAINTINGS </h2>
    <?php
        ViewPaintings::PaintingsGrid($arr);
    ?>

    <h2 class="mt-5">TOP 10 ARTISTS</h2>
    <?php
        ViewArtists::ArtistsGrid($artistArr);
    ?>
</div>


<?php
$content = ob_get_clean();
include_once 'views/layout.php';
?>