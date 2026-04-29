<html>
    <head>
        <title>Dashbord <?php echo $_SESSION["name"]; ?></title>
        <link href="../public/css/login.css" rel="stylesheet">
        <link rel="stylesheet" href="../public/css/font-awesome.min.css">
        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <script type="text/javascript" src="../public/js/ajaxupload.3.5.js"></script>
        <style>
        /* Basic sidebar styles for admin */
        .admin-sidebar { min-height: 100vh; padding-top: 1rem; }
        .admin-sidebar .nav-link { color: #333; }
        .admin-brand { font-weight: 600; }
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <?php
            if (isset($_SESSION["userId"]) && isset($_SESSION["sessionId"]))
            {
            ?>
            <!-- Top navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <a class="navbar-brand admin-brand" href="../" target="_blank">ArtPortal</a>
                    <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">Menu</button>
                    <div class="d-flex ms-auto align-items-center">
        <?php
        echo '<ul class="nav ms-auto align-items-center">
                    <li class="nav-item">
                        <span class="nav-link">Hello, '.$_SESSION["name"].'</span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-secondary ms-2" href="logout">Logout</a>
                    </li>
            </ul>';
        ?>
                    </div>
                </div>
            </nav>


            <!-- Offcanvas sidebar for small screens (move to body root) -->
            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
              <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasSidebarLabel">Admin Menu</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
              </div>
              <div class="offcanvas-body">
                <?php
    if(isset($_SESSION["status"]) && $_SESSION["status"]=="admin") {
        echo '<h4><a href="../" target= _blank>ArtPortal</a>';
        echo ' &#187 <a href="./startAdmin" >Start '.$_SESSION["name"].' </a>';
        echo ' &#187 <a href="categoryAdmin">Styles </a>';
        echo ' &#187 <a href="paintingsAdmin"> Paintings List </a>';
        echo ' </h4>';
    }
    elseif(isset($_SESSION["status"]) && $_SESSION["status"]=="artist") {
        echo '<h4><a href="../" target= _blank>ArtPortal</a>';
        echo ' &#187 <a href="./" >Start '.$_SESSION["name"].' </a>';
        echo ' &#187 <a href="paintingsAdmin"> My Paintings List </a>';
        echo ' </h4>';
    }
    elseif(isset($_SESSION["status"]) && $_SESSION["status"]=="user") {
        echo '<h4><a href="../" target= _blank>ArtPorta</a>';
        echo ' &#187 <a href="./" >Start '.$_SESSION["name"].' </a>';
        echo ' </h4>';
    }
    else {
        echo '<h4>Access denied!</h4>';
    }
    ?>
              </div>
            </div>

            <div class="row g-0">
                <!-- Sidebar (desktop) -->
                <aside class="col-lg-2 d-none d-lg-block bg-light admin-sidebar border-end">
                    <div class="p-3">
                        <?php
    if(isset($_SESSION["status"]) && $_SESSION["status"]=="admin") {

        echo '<h4><a href="../" target= _blank>ArtPortal</a>';
        echo ' &#187 <a href="./startAdmin" >Start '.$_SESSION["name"].' </a>';
        echo ' &#187 <a href="categoryAdmin">Styles </a>';
        echo ' &#187 <a href="paintingsAdmin"> Paintings List </a>';
        
        echo ' </h4>';
    }
    elseif(isset($_SESSION["status"]) && $_SESSION["status"]=="artist") {
        echo '<h4><a href="../" target= _blank>ArtPortal</a>';
        echo ' &#187 <a href="./" >Start '.$_SESSION["name"].' </a>';
        echo ' &#187 <a href="paintingsAdmin"> My Paintings List </a>';
        echo ' </h4>';
    }
    elseif(isset($_SESSION["status"]) && $_SESSION["status"]=="user") {
        echo '<h4><a href="../" target= _blank>ArtPortal</a>';
        echo ' &#187 <a href="./" >Start '.$_SESSION["name"].' </a>';
        echo ' </h4>';
    }
    else {
        echo '<h4>Access denied!</h4>';
    }
    ?>
                    </div>
                </aside>

                <!-- Main content area -->
                <main class="col-12 col-lg-10 p-4">
                    <div class="container">
                        <?php echo $content ?>
                    </div>
                </main>
            </div>

            <?php
            } else {
                // If no session, show content full-width
            ?>
            <div class="container py-4">
                <?php echo $content ?>
            </div>
            <?php
            }
            ?>

            <footer class="footer mt-5 bg-light border-top">
                <div class="container py-3">
                    <p class="mb-0">&copy; 2026 Design Admin Dashboard <i class="fa fa-child"></i></p>
                </div>
            </footer>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>