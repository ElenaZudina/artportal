<?php
class ViewPaintings{
    public static function PaintingsList($arr) {
        echo '<div class="painting-list-container">';
        foreach($arr as $value) {
            echo '<div class="painting-card">';
                echo '<div class="painting-image-wrapper">'; // Новая обертка
                    echo '<img src="images/' .htmlspecialchars( $value['image'], ENT_QUOTES, 'UTF-8' ).'" class="painting-image" alt="' . htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8') . '">'; // Изображение внутри обертки
                echo '</div>'; // Закрываем обертку
                echo '<h3>' . htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8') . '</h3>';
                //Controller::CommentsCount($value['id']); будет добавлено позже, когда будет реализована модель комментариев
                echo '<a href="paintings?id=' . $value['id'] . '" class="btn-view-details">View details</a>';
            echo '</div>'; // Закрываем карточку
        }
        echo '</div>'; // Закрываем общий контейнер
    }

    public static function PaintingsGrid($arr) {
        echo '<div class="row g-3">';
        foreach($arr as $value) {
            echo '<div class="col-sm-6">'; // Адаптивные колонки
            echo '<div class="painting-card">';
                echo '<div class="painting-image-wrapper">'; // Новая обертка
                    echo '<img src="images/' .htmlspecialchars( $value['image'], ENT_QUOTES, 'UTF-8' ).'" class="painting-image" alt="' . htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8') . '">'; // Изображение внутри обертки
                echo '</div>'; // Закрываем обертку
                echo '<h3>' . htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8') . '</h3>';
                //Controller::CommentsCount($value['id']); будет добавлено позже, когда будет реализована модель комментариев
                echo '<a href="paintings?id=' . $value['id'] . '" class="btn-view-details">View details</a>';
            echo '</div>'; // Закрываем карточку
            echo '</div>'; // Закрываем колонки
        }
        echo '</div>'; // Закрываем общий контейнер
    }
  

    public static function OnePainting($item) {
        echo "<h2>".$item['title']."</h2>";
        //Controller::CommentsCountWithAncor($item['id']); ПОЗЖЕ
        echo '<div class="painting-image-wrapper single-painting-image-wrapper">'; // Новая обертка для одиночной картины
            echo '<img src="images/' . htmlspecialchars( $item['image'], ENT_QUOTES, 'UTF-8' ) . '" class="painting-image" alt="' . htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') . '" />'; // Изображение внутри обертки
        echo '</div>'; // Закрываем обертку
        echo "<br><br>";
        echo "<p>".$item['artist_name']."</p>";
        echo "<p>".$item['year_created']."</p>";
        echo "<p>".$item['style_name']."</p>";
        echo "<p>".$item['description']."</p>";
    }
    // добавить методы вывода для других представленных новостей


}
?>
