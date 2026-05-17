<?php
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card shadow-sm border-0 mt-4 mb-5">
            <div class="card-body p-4">
                <h2 class="mb-4 text-center">Register</h2>
                <form method="POST" action="registerAnswer">
                    <?php echo CsrfHelper::field(); ?>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input id="name" type="text" class="form-control" name="name" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail Address</label>
                        <input id="email" type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password-confirm" class="form-label">Confirm Password</label>
                        <input id="password-confirm" type="password" class="form-control" name="confirm" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" name="save">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include_once 'views/layout.php';
?>
