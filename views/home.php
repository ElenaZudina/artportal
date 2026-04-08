<?php
ob_start();
?>
<div class="row">
    <div class="col-lg-8">
        <h2 class="mb-3">TOP 10 PAINTINGS </h2>
        <?php
            ViewPaintings::PaintingsGrid($arr);
        ?>
    </div>
    <div class="col-lg-4">
        <h2>TOP 10 ARTISTS</h2>
        <?php
            ViewArtists::ArtistsList($artistArr);
        ?>
    </div>
</div>


<?php
$content = ob_get_clean();
include_once 'views/layout.php';
?>