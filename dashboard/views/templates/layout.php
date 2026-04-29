<html>
    <head>
        <title>Dashbord <?php echo $_SESSION["name"]; ?></title>
        <!-- <link href="../public/css/bootstrap.css" rel="stylesheet"> -->
        <link href="../public/css/login.css" rel="stylesheet">
        <!-- Font Awesome--> <link rel="stylesheet" href="../public/css/font-awesome.min.css">
        <!-- SCRIPT -->
        <script src="../public/js/jquery.min.js"></script>
        <!-- <script src="../public/js/bootstrap.min.js"></script> -->
         <!-- Bootstrap 5.3 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <script type="text/javascript" src="../public/js/ajaxupload.3.5.js"></script>
    </head>
    <body>
        <div class="container">
<!-- -->
        <?php
        if (isset($_SESSION["userId"]) && isset($_SESSION["sessionId"]))
        {
            ?>
        <div class="header clearfix">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
<!-- -->
        <?php
        echo '<ul class="nav nav-pills pull-right">
        <li role="button"><p>Hello, '.$_SESSION["name"].'</p>
        <a href="logout" style="display: inline;">Logout <i class="fa fa-sign-out"></i></a></li></ul>';

        if(isset($_SESSION["status"]) && $_SESSION["status"]=="admin") {

            echo '<h4><a href="../" target= _blank>WEB SITE PAINTERS ONLINE</a>';
            echo ' &#187 <a href="./" >Start '.$_SESSION["name"].' </a>';
            echo ' &#187 <a href="categoryAdmin">Styles </a>';
            echo ' &#187 <a href="paintingsAdmin"> Paintings List </a>';
            
            echo ' </h4>';
        }
        elseif(isset($_SESSION["status"]) && $_SESSION["status"]=="artist") {
            echo '<h4><a href="../" target= _blank>WEB SITE PAINTERS ONLINE</a>';
            echo ' &#187 <a href="./" >Start '.$_SESSION["name"].' </a>';
            echo ' &#187 <a href="paintingsAdmin"> My Paintings List </a>';
            echo ' </h4>';
        }
        elseif(isset($_SESSION["status"]) && $_SESSION["status"]=="user") {
            echo '<h4><a href="../" target= _blank>WEB SITE PAINTERS ONLINE</a>';
            echo ' &#187 <a href="./" >Start '.$_SESSION["name"].' </a>';
            echo ' </h4>';
        }
        else {
            echo '<h4>Access denied!</h4>';
        }
        ?>
                </div>
            </nav>
        </div>
<!-- -->
        <?php
        }
        ?>
        <div id="content" style="padding-top:20px; ">

<!-- -->
        <?php echo $content ?>
        </div>
        <footer class="footer">
            <p> &copy; 2026 Design Admin Dashboard<i class="fa fa-child"></i></p>
        </footer>
        </div><!-- /container -->
    </body>
</html>