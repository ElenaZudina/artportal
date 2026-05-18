<?php
// Account edit form view: handles editing username and email
// Output buffering for template rendering
ob_start();
$formData = $formData ?? [
    'username' => $user['username'] ?? '',
    'email' => $user['email'] ?? ''
];
?>

<!-- Main container for account edit form -->
<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Card for form UI -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <!-- Page title -->
                    <h2 class="mb-3 text-center">Edit Account</h2>

                    <!-- Success or error alert after form submission -->
                    <?php if (isset($test)): ?>
                        <?php if ($test == true): ?>
                            <div class="alert alert-success">
                                <strong>Account updated successfully.</strong>
                                <div class="mt-3">
                                    <a href="account" class="btn btn-success">Go to account</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <strong><?php echo !empty($errorMessage) ? htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') : 'Error updating account!'; ?></strong>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Edit account form -->
                    <form method="POST" action="update-account">
                        <?php echo CsrfHelper::field(); ?>
                        <!-- Username input -->
                        <div class="mb-3">
                            <label for="account_username" class="form-label">Username *</label>
                            <input id="account_username" type="text" name="username" class="form-control" required maxlength="100" value="<?php echo htmlspecialchars($formData['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <!-- Email input -->
                        <div class="mb-3">
                            <label for="account_email" class="form-label">Email *</label>
                            <input id="account_email" type="email" name="email" class="form-control" required maxlength="255" value="<?php echo htmlspecialchars($formData['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <!-- Form action buttons -->
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <a href="account" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Output content to layout
$content = ob_get_clean();
include 'views/templates/layout.php';
?>
