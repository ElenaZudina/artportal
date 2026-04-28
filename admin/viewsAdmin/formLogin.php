<?php
if (isset ($_SESSION['userId']) && isset($_SESSION['status'])) {
    switch ($_SESSION['status']) {
        case 'admin':
            header('Location: startAdmin');
            break;
        case 'artist':
            header('Location: startArtist');
            break;
        case 'user':
            header('Location: startUser');
            break;
        default:
            header('Location: login');
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login</title>
        <!-- Bootstrap 5.3 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <!-- Font Awesome -->
        <link rel="stylesheet" type="text/css" href="public/css/font-awesome.min.css">
        <link href="public/css/login.css" rel="stylesheet">
    </head>
    <body>
           <div class="container">
               <form class="form-signin" action="auth" method="POST">
            <h3 class="form-signin-heading">Enter your details</h3>
            <input type="text" name="email" class="form-control" placeholder="Email" autofocus><!--required -->
            <input type="password" name="password" class="form-control" placeholder="Password" ><!--required --> 
            <button class="btn btn-lg btn-primary btn-block" type="submit" name="btnLogin">Enter</button>

            <p style="padding-top:10px;">
                <?php
                if (isset($_SESSION['errorString'])) {
                    echo $_SESSION['errorString'];
                    unset($_SESSION['errorString']);
                }
                ?>
            </p>
            <p style="padding-top:10px;"><a href="../">Web site</a></p>
        </form>
        </div> <!--container -->
      <!-- Bootstrap 5.3 JS Bundle with Popper -->
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>
</html>