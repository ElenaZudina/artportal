<?php
class ViewPaintings{
    public static function PaintingsList($arr) {
        echo '<div class="container my-4">';
        echo '<div class="row">';
        foreach($arr as $value) {
            echo '<div class="col-12 mb-4">'; // Полная ширина для вертикального списка
                echo '<a href="paintings?id=' . $value['id'] . '" class="d-block h-100 text-reset">';
                    echo '<div class="card h-100">';
                        echo '<div class="row g-0">';
                            echo '<div class="col-md-4">';
                                echo '<img src="images/' . htmlspecialchars($value['image'] ?? '', ENT_QUOTES, 'UTF-8') . '" class="img-fluid rounded-start" alt="' . htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8') . '" onerror="this.onerror=null;this.src=\'images/test.jpg\';">';
                            echo '</div>';
                            echo '<div class="col-md-8">';
                                echo '<div class="card-body">';
                                    echo '<h5 class="card-title">' . htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8') . '</h5>';
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';
                echo '</a>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    }

    public static function PaintingsGrid($arr, $imagesOnly = false, $asymmetric = true) {
        echo '<div class="container my-4">';
        $gridClass = $asymmetric ? 'row g-4 paintings-grid paintings-grid--asymmetric' : 'row g-4 paintings-grid paintings-grid--regular';
        echo '<div class="' . $gridClass . '">';
        foreach($arr as $index => $value) {
            $itemClasses = $asymmetric ? 'paintings-grid-item' : 'col-sm-6 col-md-4 col-lg-4';
            $displayIndex = $index + 1;

            if ($asymmetric) {
                if ($displayIndex % 7 === 1) {
                    $itemClasses .= ' paintings-grid-item--large';
                }
                if ($displayIndex % 5 === 1) {
                    $itemClasses .= ' paintings-grid-item--tall';
                }
            }

            echo '<div class="' . $itemClasses . '">';
                $linkClass = 'd-block h-100 text-reset';
                $cardClass = $asymmetric ? 'card rounded-5 overflow-hidden' : 'card h-100 rounded-5 overflow-hidden';
                echo '<a href="paintings?id=' . $value['id'] . '" class="' . $linkClass . '">';
                    echo '<div class="' . $cardClass . '">';
                        echo '<div class="card-img-wrapper position-relative">';
                            echo '<img src="images/paintings/' . htmlspecialchars($value['image'] ?? '', ENT_QUOTES, 'UTF-8') . '" class="card-img-top" alt="' . htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8') . '" onerror="this.onerror=null;this.src=\'images/test.jpg\';">';
                            if (!$imagesOnly) {
                                echo '<span class="category-badge">' . htmlspecialchars($value['category'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . '</span>';
                            }
                        echo '</div>';
                        if (!$imagesOnly) {
                            echo '<div class="card-body">';
                            echo '<div class="painting-meta d-flex justify-content-between align-items-start gap-2 mb-2">';
                                echo '<h3 class="card-title painting-title mb-0">' . htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8') . '</h3>';
                                echo '<p class="card-text painting-price text-nowrap mb-0">' . htmlspecialchars($value['price'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . ' €</p>';
                            echo '</div>';
                                echo '<p class="card-text mb-1">' . htmlspecialchars($value['artist_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . '</p>';
                                //echo '<p class="card-text mb-1">' . htmlspecialchars($value['category'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . '</p>';
                            echo '</div>';
                        }
                    echo '</div>';
                echo '</a>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    }
  

    public static function OnePainting($item) {
        echo '<div class="container my-4">';
            echo '<div class="row align-items-start gx-5">';
                // Левая колонка: Изображение
                echo '<div class="col-12 col-md-6 mb-4 mb-md-0">';
                    echo '<div class="one-painting-container">';
                        echo '<div class="one-painting-image-wrapper">';
                            echo '<div class="painting-overlays">';
                                echo '<span class="category-badge category-badge--accent">' . htmlspecialchars($item['category_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . '</span>';
                                echo '<form method="POST" action="/artportal/toggle-favorite" class="painting-favorite-form">';
                                    echo '<input type="hidden" name="painting_id" value="' . (int)($item['id'] ?? 0) . '">';
                                    $isFavorite = false;
                                    $favIcon = 'fa-heart-o';
                                    $favLabel = 'Add to favorites';
                                    if (isset($_SESSION['userId'])) {
                                        $isFavorite = Favorite::isFavorite((int)$_SESSION['userId'], (int)($item['id'] ?? 0));
                                        if ($isFavorite) {
                                            $favIcon = 'fa-heart';
                                            $favLabel = 'Remove from favorites';
                                        }
                                    }
                                    $favStateClass = $isFavorite ? 'is-active' : 'is-inactive';
                                    echo '<button type="submit" class="category-badge category-badge--favorite ' . $favStateClass . '" aria-label="' . htmlspecialchars($favLabel, ENT_QUOTES, 'UTF-8') . '">';
                                        echo '<i class="fa ' . htmlspecialchars($favIcon, ENT_QUOTES, 'UTF-8') . '"></i>';
                                    echo '</button>';
                                echo '</form>';
                            echo '</div>';
                            echo '<img src="images/paintings/' . htmlspecialchars($item['image'] ?? '', ENT_QUOTES, 'UTF-8') . '" class="img-fluid one-painting-image" alt="' . htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') . '" onerror="this.onerror=null;this.src=\'images/test.jpg\';" />';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
                
                // Правая колонка: Описание
                echo '<div class="col-12 col-md-6">';
                    echo "<h1 class='single-card-title mb-4'>" . htmlspecialchars($item['title'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . "</h1>";
                    //Controller::CommentsCountWithAncor($item['id']); ПОЗЖЕ
                    
                    echo '<div class="artist-profile mb-4">';
                        $artistAvatar = $item['artist_avatar'] ?? '';
                        echo '<img src="images/artists/' . htmlspecialchars($artistAvatar, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($item['artist_name'] ?? 'Artist', ENT_QUOTES, 'UTF-8') . '" class="artist-avatar" onerror="this.onerror=null;this.src=\'images/test.jpg\';">';
                        echo '<div class="artist-profile-content">';
                            echo '<p class="painting-specs-label">Artist</p>';
                            echo '<a href="artist?id=' . urlencode((string)($item['artist_id'] ?? '')) . '" class="artist-profile-link">' . htmlspecialchars($item['artist_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . '</a>';
                        echo '</div>';
                    echo '</div>';

                    echo '<div class="card rounded-5 mb-4">';
                        echo '<div class="card-body">';
                            echo "<p class='painting-specs-label painting-price-label mb-2'>Price</p>";
                            echo "<p class='painting-specs-value painting-price-value mb-0'>" . htmlspecialchars($item['price'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . " €</p>";
                        echo '</div>';
                    echo '</div>';

                    echo "<h3 class='mb-3'>About This Work</h3>";
                    echo "<p class='one-painting-description'>" . htmlspecialchars($item['description'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . "</p>";

                    echo '<div class="row g-3 mb-4">';
                        echo '<div class="col-12 col-md-6">';
                            echo '<div class="card rounded-5 h-100">';
                                echo '<div class="card-body">';
                                    echo "<p class='painting-specs-label mb-2'>Year</p>";
                                    echo "<p class='painting-specs-value mb-0'>" . htmlspecialchars($item['year_created'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . "</p>";
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';

                        echo '<div class="col-12 col-md-6">';
                            echo '<div class="card rounded-5 h-100">';
                                echo '<div class="card-body">';
                                    echo "<p class='painting-specs-label mb-2'>Medium</p>";
                                    echo "<p class='painting-specs-value mb-0'>" . htmlspecialchars($item['medium'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . "</p>";
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';

                        echo '<div class="col-12">';
                            echo '<div class="card rounded-5 h-100">';
                                echo '<div class="card-body">';
                                    echo "<p class='painting-specs-label mb-2'>Dimensions</p>";
                                    echo "<p class='painting-specs-value mb-0'>" . htmlspecialchars($item['dimensions'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . "</p>";
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';

                    echo '<div class="action-buttons">';
                        echo '<button type="button" class="btn buy-button">Inquire About Purchase</button>';
                        echo '<button type="button" class="btn collection-button">Add to collection</button>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }
    // добавить методы вывода для других представленных новостей


}
?>
