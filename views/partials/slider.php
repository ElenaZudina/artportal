<?php
class ViewSlider {
    public static function render($sliderPaintings) {
        if (!empty($sliderPaintings)): ?>
        <div id="paintingsCarousel" class="carousel slide" data-bs-ride="carousel">

    <!-- индикаторы -->
    <div class="carousel-indicators">
        <?php foreach ($sliderPaintings as $i => $painting): ?>
            <button type="button"
                data-bs-target="#paintingsCarousel"
                data-bs-slide-to="<?= $i ?>"
                class="<?= $i === 0 ? 'active' : '' ?>"
                aria-current="<?= $i === 0 ? 'true' : 'false' ?>"
                aria-label="Slide <?= $i + 1 ?>">
            </button>
        <?php endforeach; ?>
    </div>

    <!-- слайды -->
    <div class="carousel-inner">

        <?php foreach ($sliderPaintings as $i => $painting): ?>
            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">

                <img src="images/paintings/<?= htmlspecialchars($painting['image']) ?>"
                     class="d-block w-100"
                     style="height:70vh; object-fit:cover;"
                     alt="<?= htmlspecialchars($painting['title']) ?>">

                <div class="carousel-caption d-none d-md-block slider-caption">
                    <h5 class="painting-title slider-painting-title"><?= htmlspecialchars($painting['title']) ?></h5>
                    <p class="slider-artist-name"><?= htmlspecialchars($painting['artist_name']) ?></p>
                </div>

            </div>
        <?php endforeach; ?>

    </div>

    <!-- стрелки -->
    <button class="carousel-control-prev custom-carousel-control" type="button"
            data-bs-target="#paintingsCarousel" data-bs-slide="prev" aria-label="Previous slide">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="carousel-arrow" aria-hidden="true">&#8249;</span>
        <span class="visually-hidden">Previous</span>
    </button>

    <button class="carousel-control-next custom-carousel-control" type="button"
            data-bs-target="#paintingsCarousel" data-bs-slide="next" aria-label="Next slide">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="carousel-arrow" aria-hidden="true">&#8250;</span>
        <span class="visually-hidden">Next</span>
    </button>

</div>

        <?php endif;
    }
}