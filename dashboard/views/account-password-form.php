<?php
// Change password form view
// Output buffering for template rendering
ob_start();
?>

<!-- Main container for the change password form -->
<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <!-- Page title -->
                    <h2 class="mb-3 text-center">Change Password</h2>

                    <!-- Success or error message after password change attempt -->
                    <?php if (isset($test)): ?>
                        <?php if ($test == true): ?>
                            <div class="alert alert-success">
                                <strong>Password changed successfully.</strong>
                                <div class="mt-3">
                                    <a href="account" class="btn btn-success">Go to account</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <strong><?php echo !empty($errorMessage) ? htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') : 'Error changing password!'; ?></strong>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Change password form -->
                    <form method="POST" action="update-password">
                        <?php echo CsrfHelper::field(); ?>
                        <!-- Current password input -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current password *</label>
                            <input id="current_password" type="password" name="current_password" class="form-control" required>
                        </div>

                        <!-- New password input -->
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New password *</label>
                            <input id="new_password" type="password" name="new_password" class="form-control" required minlength="6">
                        </div>

                        <!-- Confirm new password input -->
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm new password *</label>
                            <input id="confirm_password" type="password" name="confirm_password" class="form-control" required minlength="6">
                        </div>

                        <!-- Submit and cancel buttons -->
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">Update password</button>
                            <a href="account" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// End output buffering and include layout template
$content = ob_get_clean();
include 'views/templates/layout.php';
?>
