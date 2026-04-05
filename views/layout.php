<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Art Portal</title>
            <!-- Bootstrap не использую -->
            <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
             integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2M2w1T"
             crossorigin="anonymous" -->
             <link rel= "stylesheet" type ="text/css" href="public/css/style.css">
        <link href="https://fonts.googleapis.com/css?family=Noto+Serif" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">

    </head>
    <body>
        <header>
            <h1>Art Portal</h1>
            <nav class="one">
            <ul class="topmenu">
                <li><a href="#">Styles<i class="fa fa-angle-down"></i></a>
                    <ul class="submenu">
                        <?php
                        Controller::AllStyles();
                        ?>
                    </ul>
                </li>
                <li><a href="testError">Info</a></li>
                <li><a href="./">Homepage</a></li>
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