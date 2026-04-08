<?php
class ViewArtists{
    public static function ArtistsList($arr) {
        echo '<div class="container my-4 painting-list-container">';
        foreach($arr as $value) {
            echo '<div class="painting-card">';
                echo '<div class="painting-image-wrapper">'; // Новая обертка
                    echo '<img src="images/' .htmlspecialchars( $value['picture'], ENT_QUOTES, 'UTF-8' ).'" class="painting-image" alt="' . htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8') . '">'; // Изображение внутри обертки
                echo '</div>'; // Закрываем обертку
                echo '<h3>' . htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8') . '</h3>';
                //Controller::CommentsCount($value['id']); будет добавлено позже, когда будет реализована модель комментариев
                echo '<a href="artist?id=' . $value['id'] . '" class="btn-view-details">View details</a>';
            echo '</div>'; // Закрываем карточку
        }
        echo '</div>'; // Закрываем общий контейнер
    }

    public static function SingleArtist($item) {
        echo "<h2 class='mb-4'>".$item['name']."</h2>";
        //Controller::CommentsCountWithAncor($item['id']); ПОЗЖЕ
        echo '<div class="container my-4">'; 
            echo '<img src="images/' . htmlspecialchars( $item['picture'], ENT_QUOTES, 'UTF-8' ) . '" class="painting-image" alt="' . htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') . '" />'; // Изображение внутри обертки
        echo '</div>'; // Закрываем обертку
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
    }
    // добавить методы вывода для других представленных новостей


}
?>
