<?php
// Profile edit form view
// Output buffering for template rendering
ob_start();
$formData = $formData ?? $profile ?? [];
?>

<!-- Main container for profile edit form -->
<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <!-- Page title -->
                    <h2 class="mb-3 text-center">Edit Profile</h2>

                    <!-- Success or error message after form submission -->
                    <?php if (isset($test)): ?>
                        <?php if ($test == true): ?>
                            <div class="alert alert-success">
                                <strong>Record updated successfully.</strong>
                                <div class="mt-3">
                                    <a href="profile" class="btn btn-success">Go to profile</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <strong><?php echo !empty($errorMessage) ? htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') : 'Error updating record!'; ?></strong>
                                <div class="mt-3">
                                    <a href="profile" class="btn btn-success">Go to profile</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Profile edit form -->
                    <?php if (!isset($test)): ?>
                    <form method="POST" action="update-profile" enctype="multipart/form-data">
                        <?php echo CsrfHelper::field(); ?>
                        <!-- Name input -->
                        <div class="mb-3">
                            <label for="artist_name" class="form-label">Name *</label>
                            <input id="artist_name" type="text" name="name" class="form-control" required maxlength="255" value="<?php echo htmlspecialchars($formData['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <!-- Location input -->
                        <div class="mb-3">
                            <label for="artist_location" class="form-label">Location *</label>
                            <input id="artist_location" type="text" name="location" class="form-control" required maxlength="100" value="<?php echo htmlspecialchars($formData['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <!-- Birth date input -->
                        <div class="mb-3">
                            <label for="artist_birth_date" class="form-label">Birth Date</label>
                            <input id="artist_birth_date" type="date" name="birth_date" class="form-control" value="<?php echo htmlspecialchars($formData['birth_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <!-- Bio input -->
                        <div class="mb-3">
                            <label for="artist_bio" class="form-label">Bio</label>
                            <textarea id="artist_bio" name="bio" class="form-control" rows="4"><?php echo htmlspecialchars($formData['bio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>

                        <!-- Picture upload input -->
                        <div class="mb-3">
                            <label for="artist_picture" class="form-label">Picture</label>
                            <input id="artist_picture" type="file" name="picture_file" class="form-control" accept="image/*">
                            <?php if (!empty($formData['picture'])): ?>
                                <div class="form-text">Current file: <?php echo htmlspecialchars($formData['picture'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Form action buttons -->
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <a href="profile" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'views/templates/layout.php';
?>
