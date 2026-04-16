<?php
$activeRoute = defined('ACTIVE_ROUTE') ? ACTIVE_ROUTE : 'home';

$isHome = $activeRoute === 'home';
$isInfo = $activeRoute === 'testError';
$isArtists = $activeRoute === 'artists' || $activeRoute === 'artist';
$isCategories = $activeRoute === 'category';
$isRegister = $activeRoute === 'registerForm' || $activeRoute === 'registerAnswer';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Art Portal</title>
            <!-- Bootstrap 5.3 CSS -->
             <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
             <!-- Custom CSS -->
             <link rel="stylesheet" type="text/css" href="public/css/style.css">
             <link rel="stylesheet" type="text/css" href="public/css/custom.css">
             <!-- Google Fonts -->
            <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    </head>
    <body class="d-flex flex-column min-vh-100">
        <header>
            <h1 class="visually-hidden">ArtPortal</h1>
            <nav class="navbar navbar-expand-lg bg-white">
                <div class="container">
                    <a class="navbar-brand" href="./">
                        <span class="logo-icon">A</span>
                        <span class="logo-text">ArtPortal</span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                        </button>
                    <div class="collapse navbar-collapse" id="navbarContent">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item"><a class="nav-link<?php echo $isHome ? ' active' : ''; ?>" href="./">Home</a></li>
                            <li class="nav-item"><a class="nav-link<?php echo $isInfo ? ' active' : ''; ?>" href="testError">Info</a></li>
                            <li class="nav-item"><a class="nav-link<?php echo $isArtists ? ' active' : ''; ?>" href="artists">Artists</a></li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle<?php echo $isCategories ? ' active' : ''; ?>" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Categories</a>
                                <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                                    <?php
                                        Controller::AllCategories();
                                    ?>
                                </ul>
                            </li>
                            <li class="nav-item"><a class="nav-link<?php echo $isRegister ? ' active' : ''; ?>" href="registerForm">Register</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <main class="container my-5 flex-grow-1">
            <?php
                if (isset($content)) {
                    echo $content;
                }
                else {
                    echo '<h2>Content is gone</h2>';
                }
            ?>
        </main>
        <footer>
            <div class="container text-center py-5">
                <h2 class="h2-footer">ArtPortal</h2>
                <p>Footer text placeholder for your future final text.</p>
                <p>JKTV24 2026a. &copy; All Rights Reserved</p>
            </div>
        </footer>
        <!-- Bootstrap 5.3 JS Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>
</html>