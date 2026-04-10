<?php
class ViewArtists{
    public static function ArtistsGrid($arr) {
        echo '<div class="container my-4">'; //общий контейнер
        echo '<div class="row g-4">'; // ряд для карточек
            foreach($arr as $value) {
                echo '<div class="col-sm-6 col-md-4 col-lg-4">'; // Адаптивные колонки
                echo '<div class="card h-100 p-">'; // карточка
                    echo '<img src="images/' .htmlspecialchars( $value['picture'] ?? 'test.jpg', ENT_QUOTES, 'UTF-8' ).'" class="rounded-circle mx-auto mb-3 img-fluid" alt="' . htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8') . '">'; // Изображение внутри обертки
                    echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8') . '</h5>';
                        //Controller::CommentsCount($value['id']); будет добавлено позже, когда будет реализована модель комментариев
                        echo '<a href="artist?id=' . $value['id'] . '" class="btn btn-primary mt-2">View details</a>';
                    echo '</div>'; // Закрываем card-body
                echo '</div>'; // Закрываем карточку
                echo '</div>'; // Закрываем колонки
        }
        echo '</div>'; // Закрываем ряд
        echo '</div>'; // закрываем общий контейнер
    }

    public static function ArtistsList($arr) {
        echo '<div class="container my-4">';
        echo '<div class="row">';
        foreach($arr as $value) {
            echo '<div class="col-12 mb-4">'; // Полная ширина для вертикального списка
                echo '<div class="card h-100">';
                    echo '<div class="row g-0">';
                        echo '<div class="col-md-4">';
                            echo '<img src="images/' .htmlspecialchars( $value['picture'] ?? 'test.jpg', ENT_QUOTES, 'UTF-8' ).'" class="img-fluid rounded-start" alt="' . htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8') . '">';
                        echo '</div>';
                        echo '<div class="col-md-8">';
                            echo '<div class="card-body">';
                                echo '<h5 class="card-title">' . htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8') . '</h5>';
                                //Controller::CommentsCount($value['id']); будет добавлено позже, когда будет реализована модель комментариев
                                echo '<a href="artist?id=' . $value['id'] . '" class="btn btn-primary">View details</a>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    }

    public static function SingleArtist($item) {
        // Контейнер 1: Фото + Биография
        echo '<div class="container my-4">'; //контейнер для фото и биографии

        echo '<div class="row align-items-start">'; // ряд
        //Controller::CommentsCountWithAncor($item['id']); ПОЗЖЕ

        echo '<div class="col-12 col-md-4 mb-4 mb-md-0">'; // Левая колонка для изображения
            echo '<img src="images/' . htmlspecialchars( $item['picture'] ?? 'test.jpg', ENT_QUOTES, 'UTF-8' ) . '" class="img-fluid rounded" alt="' . htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') . '" />'; // Изображение внутри обертки
        echo '</div>'; // Закрываем левую колонку

        echo '<div class="col-12 col-md-8">'; // Правая колонка для текста
            echo "<h2 class='mb-4'>".$item['name']."</h2>";
            echo "<h3 class='mb-3'>Birth Date</h3>";
            echo "<p>".$item['birth_date']."</p>";
            echo "<h3 class='mb-3'>Biography</h3>";
            echo "<p>".$item['bio']."</p>";
        echo '</div>'; // Закрываем правую колонку

        echo "</div>"; // закрываем ряд
        echo '</div>'; // закрываем контейнер для фото и биографии

        // Контейнер 2: Портфолио
        // Проверяем, есть ли картины
        if (!empty($item['paintings']) && is_array($item['paintings'])) {
            echo '<div class="container my-4">'; // контейнер для портфолио
    
            echo '<h3 class="mb-3">Portfolio</h3>';
            echo '<div class="row g-3">';
                foreach ($item['paintings'] as $painting) {
                    echo '<div class="col-12 col-sm-6 col-md-4 col-lg-3">'; // Адаптивные колонки
                        echo '<div class="card h-100">'; // Карточка для каждой картины
                            echo '<img src="images/' . htmlspecialchars($painting['image'] ?? 'test.jpg', ENT_QUOTES, 'UTF-8') . '" 
                                 class="card-img-top" 
                                 alt="' . htmlspecialchars($painting['title'] ?? 'Без названия', ENT_QUOTES, 'UTF-8') . '">';
                        echo '</div>';
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
