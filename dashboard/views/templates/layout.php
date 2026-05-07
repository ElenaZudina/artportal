<html>
    <head>
        <title>Dashboard <?php echo $_SESSION["name"]; ?></title>
        <link href="../public/css/login.css" rel="stylesheet">
        <!-- <link rel="stylesheet" href="../public/css/font-awesome.min.css"> -->
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        
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
            if (isset($_SESSION["userId"]))
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
                <h5 class="offcanvas-title" id="offcanvasSidebarLabel">Menu</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
              </div>
              <div class="offcanvas-body">
                <?php
        if(isset($_SESSION["status"]) && $_SESSION["status"]=="admin") {
        // Offcanvas: show same block structure as desktop sidebar
        echo '<div class="mb-3">';
        echo '<h5 class="text-uppercase text-secondary">Moderation</h5>';
        echo '<ul class="nav flex-column" style="font-size:1rem;">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="moderation-artists">Approve artist profiles</a></li>';
        echo '</ul>';
        echo '</div>';

        echo '<div class="mb-3">';
        echo '<h5 class="text-uppercase text-secondary">Content management</h5>';
        echo '<ul class="nav flex-column" style="font-size:1rem;">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="categories">Categories</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="paintingsAdmin">Paintings List</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="collections">Collections</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="exhibitions">Exhibitions</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="users">Users</a></li>';
        echo '</ul>';
        echo '</div>';
    }
    elseif(isset($_SESSION["status"]) && $_SESSION["status"]=="artist") {
        echo '<div class="mb-3">';
        echo '<h5 class="text-uppercase text-secondary">Account</h5>';
        echo '<ul class="nav flex-column" style="font-size:1rem;">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="account">View Account</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="edit-account">Edit Email / Username</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="change-password">Change Password</a></li>';
        echo '</ul>';
        echo '</div>';

        echo '<div class="mb-3">';
        echo '<h5 class="text-uppercase text-secondary">Profile</h5>';
        echo '<ul class="nav flex-column" style="font-size:1rem;">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="profile">View Profile</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="edit-profile">Edit Profile</a></li>';
        echo '</ul>';
        echo '</div>';
        
        echo '<div class="mb-3">';
        echo '<h5 class="text-uppercase text-secondary">Portfolio</h5>';
        echo '<ul class="nav flex-column" style="font-size:1rem;">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="my-paintings">My Paintings</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem; color: #28a745;" href="add-painting">Add Painting</a></li>';
        echo '</ul>';
        echo '</div>';
    }
    elseif(isset($_SESSION["status"]) && $_SESSION["status"]=="user") {
        echo '<div class="mb-3">';
        echo '<h5 class="text-uppercase text-secondary">Account</h5>';
        echo '<ul class="nav flex-column" style="font-size:1rem;">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="account">View Account</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="edit-account">Edit Email / Username</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="change-password">Change Password</a></li>';
        echo '</ul>';
        echo '</div>';

        echo '<div class="mb-3">';
        echo '<h5 class="text-uppercase text-secondary">Favorites</h5>';
        echo '<ul class="nav flex-column" style="font-size:1rem;">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="my-favorites">My Favorites</a></li>';
        echo '</ul>';
        echo '</div>';
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
        // Admin sidebar blocks
        echo '<div class="mb-4">';
        echo '<h4><a href="../" target= _blank>ArtPortal</a></h4>';
        echo '</div>';
        // Moderation block
        echo '<div class="mb-4">';
        echo '<h5 class="text-uppercase text-secondary">Moderation</h5>';
        // Здесь будут ссылки на управление пользователями (добавить позже)
        echo '<ul class="nav flex-column small">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="moderation-artists">Approve artist profiles</a></li>';
        echo '</ul>';
        echo '</div>';
        // Content management block
        echo '<div class="mb-4">';
        echo '<h5 class="text-uppercase text-secondary">Content management</h5>';
        echo '<ul class="nav flex-column small">';
        //echo '<li class="nav-item"><a class="nav-link" href="./startAdmin">Start '.$_SESSION["name"].'</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="categories">Categories</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="paintingsAdmin">Paintings List</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="collections">Collections List</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="exhibitions">Exhibitions</a></li>';
        echo '</ul>';
        echo '</div>';
        // Статистика (будет добавлена позже)
        echo '<div class="mb-4">';
        echo '<h5 class="text-uppercase text-secondary">Statistics</h5>';
        echo '<span class="text-muted">Coming soon...</span>';
        echo '</div>';
    }
    elseif(isset($_SESSION["status"]) && $_SESSION["status"]=="artist") {
        echo '<div class="mb-4">';
        echo '<h4><a href="../" target= _blank>ArtPortal</a></h4>';
        echo '</div>';
        // Account block
        echo '<div class="mb-4">';
        echo '<h5 class="text-uppercase text-secondary">Account</h5>';
        echo '<ul class="nav flex-column small">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="account">View Account</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="edit-account">Edit Email / Username</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="change-password">Change Password</a></li>';
        echo '</ul>';
        echo '</div>';
        // Profile block
        echo '<div class="mb-4">';
        echo '<h5 class="text-uppercase text-secondary">Profile</h5>';
        echo '<ul class="nav flex-column small">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="profile">View Profile</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="edit-profile">Edit Profile</a></li>';
        echo '</ul>';
        echo '</div>';
        // Portfolio block
        echo '<div class="mb-4">';
        echo '<h5 class="text-uppercase text-secondary">Portfolio</h5>';
        echo '<ul class="nav flex-column small">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="my-paintings">My Paintings</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="add-painting">Add Painting</a></li>';
        echo '</ul>';
        echo '</div>';
    }
    elseif(isset($_SESSION["status"]) && $_SESSION["status"]=="user") {
        echo '<div class="mb-4">';
        echo '<h4><a href="../" target= _blank>ArtPortal</a></h4>';
        echo '</div>';
        // User account block
        echo '<div class="mb-4">';
        echo '<h5 class="text-uppercase text-secondary">Account</h5>';
        echo '<ul class="nav flex-column small">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="account">View Account</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="edit-account">Edit Email / Username</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="change-password">Change Password</a></li>';
        echo '</ul>';
        echo '</div>';
        // Favorites block
        echo '<div class="mb-4">';
        echo '<h5 class="text-uppercase text-secondary">Favorites</h5>';
        echo '<ul class="nav flex-column small">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="my-favorites">My Favorites</a></li>';
        echo '</ul>';
        echo '</div>';
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
                        <?php
                        // Основные ссылки под Hello и Logout
                        if (isset($_SESSION["userId"]) && isset($_SESSION["status"]) && $_SESSION["status"]=="artist") {
                            echo '<div class="mb-4">';
                            echo '<h4 style="font-size:1.1rem;">';
                            echo '<a href="../" target="_blank">WEB SITE</a>';
                            echo ' &#187; <a href="/artportal/dashboard/startDashboard">Start</a>';
                            echo ' &#187 <a href="account"> View Account </a>';
                            echo ' &#187 <a href="edit-account"> Edit Account </a>';
                            echo ' &#187 <a href="change-password"> Change Password </a>';
                            echo ' &#187 <a href="profile"> View Profile </a>';
                            echo ' &#187 <a href="edit-profile"> Edit Profile </a>';
                            echo ' &#187 <a href="my-paintings"> My Paintings </a>';
                            echo ' &#187 <a href="add-painting">Add Painting </a>';
                            echo '</h4>';
                            echo '</div>';
                        }
                        elseif (isset($_SESSION["userId"]) && isset($_SESSION["status"]) && $_SESSION["status"]=="user") {
                            echo '<div class="mb-4">';
                            echo '<h4 style="font-size:1.1rem;">';
                            echo '<a href="../" target="_blank">WEB SITE</a>';
                            echo ' &#187; <a href="/artportal/dashboard/startDashboard">Start</a>';
                            echo ' &#187 <a href="account"> View Account </a>';
                            echo ' &#187 <a href="edit-account"> Edit Account </a>';
                            echo ' &#187 <a href="change-password"> Change Password </a>';
                            echo ' &#187 <a href="my-favorites"> My Favorites </a>';
                            echo '</h4>';
                            echo '</div>';
                        }
                        ?>
                        <?php echo $content ?>
                    </div>
                </main>
            </div>

            <?php
            } else {
                // Если нет сессии, выводим отказ в доступе
                echo '<div class="container py-4"><h4>Access denied!</h4></div>';
            }
            ?>

            <footer class="footer mt-5 bg-light border-top">
                <div class="container py-3">
                    <p class="mb-0 text-center">&copy; 2026 Design Dashboard <i class="fa fa-child"></i></p>
                </div>
            </footer>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>