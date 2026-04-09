<?php
class ViewArtists{
    public static function ArtistsList($arr) {
        echo '<div class="container my-4">'; //общий контейнер
        echo '<div class="row g-4">'; // ряд для карточек
            foreach($arr as $value) {
                echo '<div class="col-sm-6 col-md-4 col-lg-4">'; // Адаптивные колонки
                echo '<div class="card h-100 p-3">'; // карточка
                    echo '<img src="images/' .htmlspecialchars( $value['picture'], ENT_QUOTES, 'UTF-8' ).'" class="rounded-circle mx-auto mb-3" alt="' . htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8') . '">'; // Изображение внутри обертки
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

    public static function SingleArtist($item) {
        echo '<div class="container my-4">'; //общий контейнер
        echo "<h2 class='mb-4'>".$item['name']."</h2>";
        //Controller::CommentsCountWithAncor($item['id']); ПОЗЖЕ
        
        echo '<img src="images/' . htmlspecialchars( $item['picture'], ENT_QUOTES, 'UTF-8' ) . '" class="painting-image" alt="' . htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') . '" />'; // Изображение внутри обертки
      
        //echo "<br><br>";
        echo "<h3 class='mb-3'>Birth Date</h3>";
        //echo "<p>".$item['name']."</p>";
        echo "<p>".$item['birth_date']."</p>";
        echo "<h3>Biography</h3>";
        echo "<p>".$item['bio']."</p>";
        // Проверяем, есть ли картины
       
        if (!empty($item['paintings']) && is_array($item['paintings'])) {
            echo '<h3 class="mb-3">Gallery</h3>';
    echo '<div class="container my-4">';
    echo '<div class="row g-3">';
        foreach ($item['paintings'] as $painting) {
            echo '<div class="col-12 col-sm-6 col-md-4 col-lg-3">'; // Адаптивные колонки
                echo '<div class="card h-100">'; // Карточка для каждой картины
                    echo '<img src="images/' . htmlspecialchars($painting['image'] ?? 'default.jpg', ENT_QUOTES, 'UTF-8') . '" 
                         class="card-img-top" 
                         alt="' . htmlspecialchars($painting['title'] ?? 'Без названия', ENT_QUOTES, 'UTF-8') . '">';
                echo '</div>';
            echo '</div>';
    }
    echo '</div>';
        } else {
                echo "<p>У этого художника пока нет картин.</p>";
                }
        
        //echo "<p>".$item['title']."</p>"; 
        //echo '<div class="painting-image-wrapper single-painting-image-wrapper">'; // Новая обертка для одиночной картины
        //echo "<p>".$item['title']."</p>";    
        //echo '<img src="images/' . htmlspecialchars( $item['picture'], ENT_QUOTES, 'UTF-8' ) . '" class="painting-image" alt="' . htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') . '" />'; // Изображение внутри обертки
        //echo '</div>'; // Закрываем обертку

        echo '</div>'; // закрываем общий контейнер
    }
    // добавить методы вывода для других представленных новостей


}
?>
