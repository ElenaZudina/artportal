<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Art Portal</title>
            <!-- Bootstrap 5.3 CSS -->
             <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-..." crossorigin="anonymous">
             <!-- Custom CSS -->
             <link rel= "stylesheet" type ="text/css" href="public/css/style.css">
             <!-- Google Fonys -->
            <link href="https://fonts.googleapis.com/css?family=Noto+Serif" rel="stylesheet">
    </head>
    <body>
        <header>
            <h1>Art Portal</h1>
            <nav class="one">
            <ul class="topmenu">
                <li><a href="./">Home</a></li>
                <li><a href="testError">Info</a></li>
                <li><a href="artists">Artists</a></li>
                <li><a href="#">Categories<i class="fa fa-angle-down"></i></a>
                    <ul class="submenu">
                        <?php
                        Controller::AllCategories();
                        ?>
                    </ul>
                </li>
                <li><a href="registerForm">Register</a></li>
            </ul>
        </nav>
        </header>
        <main class = "container">
            <?php
                if (isset($content)) {
                    echo $content;
                }
                else {
                    echo '<h2>Content is gone</h2>';
                }
            ?>
        </main>
        <hr>
        <footer>
             <p class = "footer-text">JKTV24 2026a. &copy</p>
        </footer>
    </body>
</html>