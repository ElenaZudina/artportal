<?php
class ViewPaintings{
    public static function PaintingsList($arr) {
        echo '<div class="container my-4">';
        echo '<div class="row">';
        foreach($arr as $value) {
            echo '<div class="col-12 mb-4">'; // Полная ширина для вертикального списка
                echo '<div class="card h-100">';
                    echo '<div class="row g-0">';
                        echo '<div class="col-md-4">';
                            echo '<img src="images/' .htmlspecialchars( $value['image'] ?? 'test.jpg', ENT_QUOTES, 'UTF-8' ).'" class="img-fluid rounded-start" alt="' . htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8') . '">';
                        echo '</div>';
                        echo '<div class="col-md-8">';
                            echo '<div class="card-body">';
                                echo '<h5 class="card-title">' . htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8') . '</h5>';
                                //Controller::CommentsCount($value['id']); будет добавлено позже, когда будет реализована модель комментариев
                                echo '<a href="paintings?id=' . $value['id'] . '" class="btn btn-primary">View details</a>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    }

    public static function PaintingsGrid($arr) {
        echo '<div class="container my-4">';
        echo '<div class="row g-4">';
        foreach($arr as $value) {
            echo '<div class="col-sm-6 col-md-4 col-lg-4">'; // Адаптивные колонки
                echo '<div class="card h-100 rounded-5 overflow-hidden">';
                    echo '<div class="card-img-wrapper position-relative">';
                        echo '<img src="images/' .htmlspecialchars( $value['image'] ?? 'test.jpg', ENT_QUOTES, 'UTF-8' ).'" class="card-img-top" alt="' . htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8') . '">';
                        echo '<span class="category-badge">' . htmlspecialchars($value['category'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . '</span>';
                    echo '</div>';
                    echo '<div class="card-body">';
                    echo '<div class="painting-meta d-flex justify-content-between align-items-start gap-2 mb-2">';
                        echo '<h3 class="card-title painting-title mb-0">' . htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8') . '</h3>';
                        echo '<p class="card-text painting-price text-nowrap mb-0">' . htmlspecialchars($value['price'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') .  '</p>';
                    echo '</div>';
                        echo '<p class="card-text mb-1">' . htmlspecialchars($value['artist_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . '</p>';
                        echo '<p class="card-text mb-1">' . htmlspecialchars($value['category'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . '</p>';
                        
                        //Controller::CommentsCount($value['id']); будет добавлено позже, когда будет реализована модель комментариев
                        echo '<a href="paintings?id=' . $value['id'] . '" class="btn btn-primary">View details</a>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    }
  

    public static function OnePainting($item) {
        echo '<div class="container my-4">';
            echo '<div class="row align-items-start">';
                // Левая колонка: Изображение
                echo '<div class="col-12 col-md-6 mb-4 mb-md-0">';
                    echo '<img src="images/' . htmlspecialchars( $item['image'] ?? 'test.jpg', ENT_QUOTES, 'UTF-8' ) . '" class="img-fluid rounded" alt="' . htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') . '" />';
                echo '</div>';
                
                // Правая колонка: Описание
                echo '<div class="col-12 col-md-6">';
                    echo "<h2 class='mb-4'>" . htmlspecialchars($item['title'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . "</h2>";
                    //Controller::CommentsCountWithAncor($item['id']); ПОЗЖЕ
                    
                    echo "<h4 class='mb-3'>Artist</h4>";
                    echo "<p>" . htmlspecialchars($item['artist_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . "</p>";
                    if (!empty($item['artist_avatar'])) {
                        echo "<p><img src='images/" . htmlspecialchars($item['artist_avatar'] ?? 'test.jpg', ENT_QUOTES, 'UTF-8') . "' alt='Avatar' class='img-thumbnail' style='max-width: 100px;'></p>";
                    } else {
                        echo "<p><img src='images/test.jpg' alt='Avatar' class='img-thumbnail' style='max-width: 100px;'></p>";
                    }
                    
                    
                    echo "<h4 class='mb-3'>Category</h4>";
                    echo "<p>" . htmlspecialchars($item['category_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . "</p>";
                    
                    echo "<h4 class='mb-3'>Description</h4>";
                    echo "<p>" . htmlspecialchars($item['description'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . "</p>";

                    echo "<h4 class='mb-3'>Year Created</h4>";
                    echo "<p>" . htmlspecialchars($item['year_created'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . "</p>";

                    echo "<h4 class='mb-3'>Medium</h4>";
                    echo "<p>" . htmlspecialchars($item['medium'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . "</p>";

                    echo "<h4 class='mb-3'>Dimensions</h4>";
                    echo "<p>" . htmlspecialchars($item['dimensions'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . "</p>";
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }
    // добавить методы вывода для других представленных новостей


}
?>
