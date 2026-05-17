<?php
ob_start();

$profile = $existingProfile ?? null;
$formData = $formData ?? [];
?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="mb-3 text-center">Artist Profile Form</h2>

                    <?php if (!empty($profile)): ?>
                        <div class="alert alert-info">
                            You already have an artist profile with status:
                            <strong><?php echo htmlspecialchars($profile['status'] ?? 'pending', ENT_QUOTES, 'UTF-8'); ?></strong>
                        </div>
                    <?php else: ?>
                        <?php if (!empty($resultArtist['errors']) && is_array($resultArtist['errors'])): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($resultArtist['errors'] as $error): ?>
                                    <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <p class="text-muted mb-4">
                            Fill all artist table fields used for profile and analytics. Your profile will be created with
                            <strong>pending</strong> status by default.
                        </p>

                        <form method="POST" action="artistProfileSave" enctype="multipart/form-data">
                            <?php echo CsrfHelper::field(); ?>
                            <div class="mb-3">
                                <label for="artist_name" class="form-label">Name *</label>
                                <input id="artist_name" type="text" name="name" class="form-control" required maxlength="255" value="<?php echo htmlspecialchars($formData['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="artist_location" class="form-label">Location *</label>
                                <input id="artist_location" type="text" name="location" class="form-control" required maxlength="100" value="<?php echo htmlspecialchars($formData['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="artist_birth_date" class="form-label">Birth Date</label>
                                <input id="artist_birth_date" type="date" name="birth_date" class="form-control" value="<?php echo htmlspecialchars($formData['birth_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="artist_bio" class="form-label">Bio</label>
                                <textarea id="artist_bio" name="bio" class="form-control" rows="4"><?php echo htmlspecialchars($formData['bio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="artist_picture" class="form-label">Picture</label>
                                <input id="artist_picture" type="file" name="picture_file" class="form-control" accept="image/*">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Submit Artist Profile</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'views/layout.php';
?>
