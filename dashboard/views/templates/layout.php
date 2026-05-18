<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard <?php echo $_SESSION["name"]; ?></title>
        <link href="/artportal/public/css/login.css" rel="stylesheet">
        <!-- <link rel="stylesheet" href="../public/css/font-awesome.min.css"> -->
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700;900&family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
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
            <!-- Top navigation bar: brand, menu, user info -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <a class="navbar-brand admin-brand" href="../" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                        <span class="logo-icon" style="width: 2.25rem; height: 2.25rem; border-radius: 0.5rem; color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.125rem; font-weight: 700; flex-shrink: 0;">A</span>
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


            <!-- Offcanvas sidebar: navigation for small screens -->
            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
              <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasSidebarLabel">Menu</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
              </div>
              <div class="offcanvas-body">
                <?php
        if(isset($_SESSION["status"]) && $_SESSION["status"]=="artist") {
        echo '<div class="mb-3">';
        echo '<h5 class="text-uppercase text-secondary">Account</h5>';
        echo '<ul class="nav flex-column" style="font-size:1rem;">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="account"><i class="fa-solid fa-user me-2"></i>View Account</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="edit-account"><i class="fa-solid fa-pencil me-2"></i>Edit Email / Username</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="change-password"><i class="fa-solid fa-lock me-2"></i>Change Password</a></li>';
        echo '</ul>';
        echo '</div>';

        echo '<div class="mb-3">';
        echo '<h5 class="text-uppercase text-secondary">Profile</h5>';
        echo '<ul class="nav flex-column" style="font-size:1rem;">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="profile"><i class="fa-solid fa-image me-2"></i>View Profile</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="edit-profile"><i class="fa-solid fa-pencil me-2"></i>Edit Profile</a></li>';
        echo '</ul>';
        echo '</div>';
        
        echo '<div class="mb-3">';
        echo '<h5 class="text-uppercase text-secondary">Portfolio</h5>';
        echo '<ul class="nav flex-column" style="font-size:1rem;">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="my-paintings"><i class="fa-solid fa-images me-2"></i>My Paintings</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem; color: #28a745;" href="add-painting"><i class="fa-solid fa-plus me-2"></i>Add Painting</a></li>';
        echo '</ul>';
        echo '</div>';
        
        echo '<div class="mb-3">';
        echo '<h5 class="text-uppercase text-secondary">Sales</h5>';
        echo '<ul class="nav flex-column" style="font-size:1rem;">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="purchase-requests"><i class="fa-solid fa-shopping-cart me-2"></i>Purchase Requests</a></li>';
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
        // Requests link for users (offcanvas)
        echo '<div class="mb-3">';
        echo '<h5 class="text-uppercase text-secondary">Requests</h5>';
        echo '<ul class="nav flex-column" style="font-size:1rem;">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="my-requests"><i class="fa-solid fa-list-check me-2"></i>My Requests</a></li>';
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
                <!-- Sidebar (desktop): navigation for large screens -->
                <aside class="col-lg-2 d-none d-lg-block bg-light admin-sidebar border-end">
                    <div class="p-3">
                        <?php
    if(isset($_SESSION["status"]) && $_SESSION["status"]=="artist") {
        // Account block for artist
        echo '<div class="mb-4">';
        echo '<h5 class="text-uppercase text-secondary">Account</h5>';
        echo '<ul class="nav flex-column small">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="account"><i class="fa-solid fa-user me-2"></i>View Account</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="edit-account"><i class="fa-solid fa-pencil me-2"></i>Edit Email / Username</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="change-password"><i class="fa-solid fa-lock me-2"></i>Change Password</a></li>';
        echo '</ul>';
        echo '</div>';
        // Profile block for artist
        echo '<div class="mb-4">';
        echo '<h5 class="text-uppercase text-secondary">Profile</h5>';
        echo '<ul class="nav flex-column small">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="profile"><i class="fa-solid fa-image me-2"></i>View Profile</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="edit-profile"><i class="fa-solid fa-pencil me-2"></i>Edit Profile</a></li>';
        echo '</ul>';
        echo '</div>';
        // Portfolio block for artist
        echo '<div class="mb-4">';
        echo '<h5 class="text-uppercase text-secondary">Portfolio</h5>';
        echo '<ul class="nav flex-column small">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="my-paintings"><i class="fa-solid fa-images me-2"></i>My Paintings</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="add-painting"><i class="fa-solid fa-plus me-2"></i>Add Painting</a></li>';
        echo '</ul>';
        echo '</div>';        
        echo '<div class="mb-4">';
        echo '<h5 class="text-uppercase text-secondary">Sales</h5>';
        echo '<ul class="nav flex-column small">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="purchase-requests"><i class="fa-solid fa-shopping-cart me-2"></i>Purchase Requests</a></li>';
        echo '</ul>';
        echo '</div>';    }
    elseif(isset($_SESSION["status"]) && $_SESSION["status"]=="user") {
        // Account block for user
        echo '<div class="mb-4">';
        echo '<h5 class="text-uppercase text-secondary">Account</h5>';
        echo '<ul class="nav flex-column small">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="account"><i class="fa-solid fa-user me-2"></i>View Account</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="edit-account"><i class="fa-solid fa-pencil me-2"></i>Edit Email / Username</a></li>';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="change-password"><i class="fa-solid fa-lock me-2"></i>Change Password</a></li>';
        echo '</ul>';
        echo '</div>';
        // Favorites block for user
        echo '<div class="mb-4">';
        echo '<h5 class="text-uppercase text-secondary">Favorites</h5>';
        echo '<ul class="nav flex-column small">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="my-favorites"><i class="fa-solid fa-heart me-2"></i>My Favorites</a></li>';
        echo '</ul>';
        echo '</div>';
        // Requests block for user (desktop)
        echo '<div class="mb-4">';
        echo '<h5 class="text-uppercase text-secondary">Requests</h5>';
        echo '<ul class="nav flex-column small">';
        echo '<li class="nav-item"><a class="nav-link" style="font-size:1rem;" href="my-requests"><i class="fa-solid fa-list-check me-2"></i>My Requests</a></li>';
        echo '</ul>';
        echo '</div>'; 
        
    }
    else {
        echo '<h4>Access denied!</h4>';
    }
    ?>
                    </div>
                </aside>

                <!-- Main content area: page content and alerts -->
                <main class="col-12 col-lg-10 p-4">
                    <div class="container">
                        <?php if (!isset($content)) { $content = ''; } ?>
                        <?php
                            if (isset($_SESSION['successString'])) {
                                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                                echo $_SESSION['successString'];
                                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                                echo '</div>';
                                unset($_SESSION['successString']);
                            }

                            if (isset($_SESSION['warningString'])) {
                                echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">';
                                echo $_SESSION['warningString'];
                                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                                echo '</div>';
                                unset($_SESSION['warningString']);
                            }

                            if (isset($_SESSION['errorString'])) {
                                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                                echo $_SESSION['errorString'];
                                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                                echo '</div>';
                                unset($_SESSION['errorString']);
                            }
                        ?>
                        <?php
                        // Main quick links under Hello and Logout (for artist)
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
                            echo ' &#187 <a href="purchase-requests">Purchase Requests </a>';
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
                // If there is no session, show access denied
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
        <script src="/artportal/public/js/purchase-request-loading.js"></script>
    </body>
</html>
