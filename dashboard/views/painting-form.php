<?php
ob_start();
$isEdit = !empty($painting['id'] ?? null);
$currentPainting = $painting ?? [];
$formData = $formData ?? $currentPainting ?? [];
?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
                        <div>
                            <h2 class="mb-1"><?php echo $isEdit ? 'Edit Painting' : 'Add Painting'; ?></h2>
                            <p class="text-muted mb-0"><?php echo $isEdit ? 'Update your portfolio work' : 'Create a new portfolio work'; ?></p>
                        </div>
                        <a class="btn btn-outline-secondary" href="my-paintings">Back to list</a>
                    </div>

                    <?php if (isset($test)): ?>
                        <?php if ($test == true): ?>
                            <div class="alert alert-success">
                                <strong>Record updated successfully.</strong>
                                <div class="mt-3">
                                    <a href="my-paintings" class="btn btn-success">Go to paintings list</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <strong><?php echo !empty($errorMessage) ? htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') : 'Error saving record!'; ?></strong>
                                <div class="mt-3">
                                    <a href="my-paintings" class="btn btn-success">Go to paintings list</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (!isset($test) || $test == false): ?>
                        <form method="POST" action="<?php echo $isEdit ? 'update-painting?id=' . (int)($currentPainting['id'] ?? 0) : 'store-painting'; ?>" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="painting_title" class="form-label">Title *</label>
                                <input id="painting_title" type="text" name="title" class="form-control" required maxlength="255" value="<?php echo htmlspecialchars($formData['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="painting_description" class="form-label">Description *</label>
                                <textarea id="painting_description" name="description" class="form-control" rows="5" required><?php echo htmlspecialchars($formData['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4 mb-3">
                                    <label for="painting_year" class="form-label">Year *</label>
                                    <input id="painting_year" type="number" name="year_created" class="form-control" required min="1000" max="9999" value="<?php echo htmlspecialchars((string)($formData['year_created'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label for="painting_category" class="form-label">Category *</label>
                                    <select id="painting_category" name="category_id" class="form-select" required>
                                        <option value="">Select category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo (int)$category['id']; ?>" <?php echo ((int)($formData['category_id'] ?? 0) === (int)$category['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label for="painting_medium" class="form-label">Medium *</label>
                                    <input id="painting_medium" type="text" name="medium" class="form-control" required maxlength="255" value="<?php echo htmlspecialchars($formData['medium'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="painting_dimensions" class="form-label">Dimensions *</label>
                                    <input id="painting_dimensions" type="text" name="dimensions" class="form-control" required maxlength="100" value="<?php echo htmlspecialchars($formData['dimensions'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="painting_price" class="form-label">Price *</label>
                                <input id="painting_price" type="number" name="price" class="form-control" required min="0" step="0.01" value="<?php echo htmlspecialchars((string)($formData['price'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="painting_image" class="form-label">Image</label>
                                <input id="painting_image" type="file" name="image_file" class="form-control" accept="image/*" <?php echo $isEdit ? '' : 'required'; ?>>
                                <?php if (!empty($formData['image'])): ?>
                                    <div class="form-text">Current file: <?php echo htmlspecialchars($formData['image'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Save Changes' : 'Create Painting'; ?></button>
                                <a href="my-paintings" class="btn btn-outline-secondary">Cancel</a>
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