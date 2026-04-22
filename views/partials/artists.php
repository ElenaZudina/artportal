<?php
class ViewArtists{
    public static function ArtistsGrid($arr, $showProfile = false) {
        echo '<div class="container my-4">'; //общий контейнер
        echo '<div class="row g-4 artists-grid">'; // ряд для карточек
            foreach($arr as $value) {
                echo '<div class="col-sm-6 col-md-4 col-lg-4">'; // Адаптивные колонки
                echo '<a href="artist?id=' . $value['id'] . '" class="d-block h-100 text-reset">';
                    echo '<div class="card h-100 rounded-5 overflow-hidden">'; // карточка
                        echo '<div class="card-img-wrapper">';
                            echo '<img src="images/artists/' . htmlspecialchars($value['picture'] ?? '', ENT_QUOTES, 'UTF-8') . '" class="card-img-top" alt="' . htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8') . '" onerror="this.onerror=null;this.src=\'images/test.jpg\';">'; // Изображение внутри обертки
                        echo '</div>';
                        echo '<div class="card-body d-flex flex-column pb-4">';
                            echo '<h3 class="card-title">' . htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8') . '</h3>';
                            echo '<p class="card-text">'. htmlspecialchars($value['location'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . '</p>';
                            echo '<p class="card-text artist-description">'. htmlspecialchars($value['bio'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . '</p>';
                            //Controller::CommentsCount($value['id']); будет добавлено позже, когда будет реализована модель комментариев
                            if ($showProfile) {
                                echo '<span class="section-link mt-auto align-self-end">View Profile <span class="ms-1 small">&#8599;</span></span>';
                            }
                        echo '</div>'; // Закрываем card-body
                    echo '</div>'; // Закрываем карточку
                echo '</a>';
                echo '</div>'; // Закрываем колонки
        }
        echo '</div>'; // Закрываем ряд
        echo '</div>'; // закрываем общий контейнер
    }

    public static function ArtistsList($arr, $showProfile = false) {
        echo '<div class="container my-4">';
        echo '<div class="row">';
        foreach($arr as $value) {
            echo '<div class="col-12 mb-4">'; // Полная ширина для вертикального списка
                echo '<a href="artist?id=' . $value['id'] . '" class="d-block h-100 text-reset">';
                    echo '<div class="card h-100 ">';
                        echo '<div class="row g-0">';
                            echo '<div class="col-md-4">';
                                echo '<img src="images/' . htmlspecialchars($value['picture'] ?? '', ENT_QUOTES, 'UTF-8') . '" class="img-fluid rounded-start" alt="' . htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8') . '" onerror="this.onerror=null;this.src=\'images/test.jpg\';">';
                            echo '</div>';
                            echo '<div class="col-md-8">';
                                echo '<div class="card-body d-flex flex-column pb-4">';
                                    echo '<h5 class="card-title">' . htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8') . '</h5>';
                                    //Controller::CommentsCount($value['id']); будет добавлено позже, когда будет реализована модель комментариев
                                    if ($showProfile) {
                                        echo '<span class="section-link mt-auto align-self-end">View Profile <span class="ms-1 small">&#8599;</span></span>';
                                    }
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

    public static function SingleArtist($item) {
        echo '<div class="container my-4">';
        
            echo '<div class="row align-items-stretch gx-5">';
                // Левая колонка: Изображение
                echo '<div class="col-12 col-md-6 mb-4 mb-md-0">';
                    echo '<div class="single-artist-container">';
                        echo '<div class="single-artist-image-wrapper">';
                            echo '<img src="images/artists/' . htmlspecialchars($item['picture'] ?? '', ENT_QUOTES, 'UTF-8') . '" class="img-fluid w-100 single-artist-image" alt="' . htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') . '" onerror="this.onerror=null;this.src=\'images/test.jpg\';" />';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
                
                // Правая колонка: Описание
                echo '<div class="col-12 col-md-6 d-flex flex-column justify-content-center">';
                    echo "<h1 class='single-card-title mb-4'>" . htmlspecialchars($item['name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . "</h1>";
                    //Controller::CommentsCountWithAncor($item['id']); ПОЗЖЕ
                    
                    echo "<p class='card-text'><svg class='location-marker-icon' width='14' height='18' viewBox='0 0 24 24' aria-hidden='true' focusable='false'><path d='M12 22s7-7.2 7-12a7 7 0 1 0-14 0c0 4.8 7 12 7 12z' fill='none' stroke='#4A5565' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'></path><circle cx='12' cy='10' r='2.4' fill='#4A5565'></circle></svg> " . htmlspecialchars($item['location'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . "</p>";
                    
                    //echo "<h4 class='mb-3'>Birth Date</h4>";
                    //echo "<p>" . htmlspecialchars($item['birth_date'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . "</p>";
                    
                    echo "<p class='card-text single-artist-description'>" . htmlspecialchars($item['bio'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . "</p>";

                    //echo "<h4 class='mb-3'>Joined</h4>";
                    //echo "<p>" . htmlspecialchars(date("Y", strtotime($item['created_at'] ?? 'now')), ENT_QUOTES, 'UTF-8') . "</p>";
                echo '</div>';
            echo '</div>';
        echo '</div>';

        // Контейнер 2: Портфолио
        // Проверяем, есть ли картины
        if (!empty($item['paintings']) && is_array($item['paintings'])) {
            echo '<div class="container my-4">'; // контейнер для портфолио
    
            echo '<h2 class="mb-3">Portfolio</h2>';
            echo '<div class="row g-3 artist-portfolio-grid">';
                foreach ($item['paintings'] as $painting) {
                    echo '<div class="col-12 col-sm-6 col-md-6 col-lg-4">'; // Более крупные карточки портфолио
                        echo '<a href="paintings?id=' . urlencode((string)($painting['id'] ?? '')) . '" class="d-block h-100 text-reset">';
                            echo '<div class="card h-100 rounded-5 overflow-hidden">'; // Карточка для каждой картины
                                echo '<div class="card-img-wrapper">';
                                     echo '<img src="images/' . htmlspecialchars($painting['image'] ?? '', ENT_QUOTES, 'UTF-8') . '" 
                                         class="card-img-top" 
                                         alt="' . htmlspecialchars($painting['title'] ?? 'Без названия', ENT_QUOTES, 'UTF-8') . '" 
                                         onerror="this.onerror=null;this.src=\'images/test.jpg\';">';
                                echo '</div>';
                            echo '</div>';
                        echo '</a>';
                    echo '</div>';
                }
            echo '</div>';
            echo '</div>'; // закрываем контейнер для портфолио
        } else {
            echo '<div class="container my-4">'; // контейнер для сообщения об отсутствии картин
                echo "<p>У этого художника пока нет картин.</p>";
            echo '</div>';
        }
    }
    // добавить методы вывода для других представленных новостей


}
?>

