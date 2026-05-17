<?php
ob_start();
?>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card shadow-sm border-0 mt-4 mb-5">
            <div class="card-body p-4">
                <h2 class="mb-3 text-center">Forgot password</h2>
                <p class="text-muted text-center mb-4">Enter your email and the admin will contact you with the password manually.</p>

                <form method="POST" action="forgot-password-request">
                    <?php echo CsrfHelper::field(); ?>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail Address</label>
                        <input id="email" type="email" class="form-control" name="email" required autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Send request</button>
                </form>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <a href="login" class="section-link">Back to login</a>
                    <span class="form-text">Admin will review the request</span>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
include_once 'views/layout.php';
?>
