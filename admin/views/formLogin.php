<?php
if (isset ($_SESSION['userId']) && isset($_SESSION['status'])) {
    switch ($_SESSION['status']) {
        case 'admin':
            header('Location: /artportal/admin/startAdmin');
            break;
        case 'artist':
            header('Location: /artportal/dashboard/startDashboard');
            break;
        case 'user':
            header('Location: /artportal/dashboard/startDashboard');
            break;
        default:
            header('Location: /artportal/admin/login');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card shadow-sm border-0 mt-4 mb-5">
            <div class="card-body p-4">
                <h2 class="mb-4 text-center">Login</h2>
                <form method="POST" action="auth">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail Address</label>
                        <input id="email" type="email" class="form-control" name="email" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" class="form-control" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" name="btnLogin">Login</button>
                    <?php if (isset($_SESSION['errorString'])): ?>
                        <div class="alert alert-danger mt-3" role="alert">
                            <?php echo $_SESSION['errorString']; unset($_SESSION['errorString']); ?>
                        </div>
                    <?php endif; ?>
                </form>
                <div class="text-center mt-3">
                    <a href="../">Web site</a>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
