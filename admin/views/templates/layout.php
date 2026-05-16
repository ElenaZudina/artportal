<html>
    <head>
        <title>Dashboard <?php echo $_SESSION["name"]; ?></title>
        <link href="/artportal/public/css/login.css" rel="stylesheet">
        <!-- <link rel="stylesheet" href="../public/css/font-awesome.min.css"> -->
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700;900&family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
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
                    <a class="navbar-brand admin-brand" href="../" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                        <span class="logo-icon" style="width: 2.25rem; height: 2.25rem; border-radius: 0.5rem; color: var(--primary-foreground); display: inline-flex; align-items: center; justify-content: center; font-size: 1.125rem; font-weight: 700; flex-shrink: 0;">A</span>
                        <span style="font-family: var(--font-heading); font-size: var(--text-xl); font-weight: 700;">ArtPortal</span>
                    </a>
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
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="moderation-artists"><i class="fa-solid fa-shield-halved me-2"></i>Approve artist profiles</a></li>';
        echo '</ul>';
        echo '</div>';

        echo '<div class="mb-3">';
        echo '<h5 class="text-uppercase text-secondary">Content management</h5>';
        echo '<ul class="nav flex-column" style="font-size:1rem;">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="categories"><i class="fa-solid fa-tag me-2"></i>Categories</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="collections"><i class="fa-solid fa-folder me-2"></i>Collections</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="exhibitions"><i class="fa-solid fa-image me-2"></i>Exhibitions</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="users"><i class="fa-solid fa-users me-2"></i>Users</a></li>';
        echo '</ul>';
        echo '</div>';
    }
    elseif(isset($_SESSION["status"]) && $_SESSION["status"]=="artist") {
        echo '<h4><a href="/artportal/dashboard/startDashboard" >Start '.$_SESSION["name"].' </a></h4>';
    }
    elseif(isset($_SESSION["status"]) && $_SESSION["status"]=="user") {
        echo '<h4><a href="/artportal/dashboard/startDashboard" >Start '.$_SESSION["name"].' </a></h4>';
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
        // Moderation block
        echo '<div class="mb-4">';
        echo '<h5 class="text-uppercase text-secondary">Moderation</h5>';
        // Здесь будут ссылки на управление пользователями (добавить позже)
        echo '<ul class="nav flex-column small">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="moderation-artists"><i class="fa-solid fa-shield-halved me-2"></i>Approve artist profiles</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="users"><i class="fa-solid fa-users me-2"></i>Users</a></li>';
        echo '</ul>';
        echo '</div>';
        // Content management block
        echo '<div class="mb-4">';
        echo '<h5 class="text-uppercase text-secondary">Content management</h5>';
        echo '<ul class="nav flex-column small">';
        //echo '<li class="nav-item"><a class="nav-link" href="./startAdmin">Start '.$_SESSION["name"].'</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="categories"><i class="fa-solid fa-tag me-2"></i>Categories</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="collections"><i class="fa-solid fa-folder me-2"></i>Collections List</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="exhibitions"><i class="fa-solid fa-image me-2"></i>Exhibitions</a></li>';
        echo '</ul>';
        echo '</div>';
        // Статистика (будет добавлена позже)
        echo '<div class="mb-4">';
        echo '<h5 class="text-uppercase text-secondary">Statistics</h5>';
        echo '<span class="text-muted">Coming soon...</span>';
        echo '</div>';
    }
    elseif(isset($_SESSION["status"]) && $_SESSION["status"]=="artist") {
        echo '<h4><a href="/artportal/dashboard/startDashboard" >Start '.$_SESSION["name"].' </a></h4>';
    }
    elseif(isset($_SESSION["status"]) && $_SESSION["status"]=="user") {
        echo '<h4><a href="/artportal/dashboard/startDashboard" >Start '.$_SESSION["name"].' </a></h4>';
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
                        if (isset($_SESSION["userId"]) && isset($_SESSION["status"]) && $_SESSION["status"]=="admin") {
                            echo '<div class="mb-4">';
                            echo '<h4 style="font-size:1.1rem;">';
                            echo '<a href="../" target="_blank">WEB SITE</a>';
                            echo ' &#187; <a href="/artportal/admin/startAdmin">Start</a>';
                            echo ' &#187; <a href="moderation-artists">Approve Artist Profiles</a>';
                            echo ' &#187; <a href="categories">Categories</a>';
                            echo ' &#187; <a href="collections">Collections</a>';
                            echo ' &#187; <a href="exhibitions">Exhibitions</a>';
                            echo ' &#187; <a href="users">Users</a>';
                            echo '</h4>';
                            echo '</div>';
                        }
                        elseif (isset($_SESSION["userId"]) && isset($_SESSION["status"]) && $_SESSION["status"]=="artist") {
                            echo '<div class="mb-4">';
                            echo '<h4 style="font-size:1.1rem;">';
                            echo '<a href="../" target="_blank">WEB SITE</a>';
                            echo ' &#187; <a href="/artportal/dashboard/startDashboard">Start</a>';
                            echo ' &#187; <a href="account">View Account</a>';
                            echo ' &#187; <a href="edit-account">Edit Account</a>';
                            echo ' &#187; <a href="change-password">Change Password</a>';
                            echo ' &#187; <a href="profile">View Profile</a>';
                            echo ' &#187; <a href="edit-profile">Edit Profile</a>';
                            echo '</h4>';
                            echo '</div>';
                        }
                        elseif (isset($_SESSION["userId"]) && isset($_SESSION["status"]) && $_SESSION["status"]=="user") {
                            echo '<div class="mb-4">';
                            echo '<h4 style="font-size:1.1rem;">';
                            echo '<a href="../" target="_blank">WEB SITE</a>';
                            echo ' &#187; <a href="/artportal/dashboard/startDashboard">Start</a>';
                            echo ' &#187; <a href="account">View Account</a>';
                            echo ' &#187; <a href="edit-account">Edit Account</a>';
                            echo ' &#187; <a href="change-password">Change Password</a>';
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