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
                        <form method="POST" action="<?php echo $isEdit ? 'update-painting?id=' . (int)($currentPainting['id'] ?? 0) : 'store-painting'; ?>" enctype="multipart/form-data" id="paintingSaveForm">
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
                                <?php if (!$isEdit): ?>
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#priceCalculatorModal">
                                        Open price calculator
                                    </button>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="painting_image" class="form-label">Image</label>
                                <input id="painting_image" type="file" name="image_file" class="form-control" accept="image/*" <?php echo $isEdit ? '' : 'required'; ?>>
                                <?php if (!empty($formData['image'])): ?>
                                    <div class="form-text">Current file: <?php echo htmlspecialchars($formData['image'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary" id="paintingSaveSubmit">
                                    <span id="paintingSaveSpinner" class="spinner-border spinner-border-sm me-2 d-none" aria-hidden="true"></span>
                                    <span id="paintingSaveSubmitText"><?php echo $isEdit ? 'Save Changes' : 'Create Painting'; ?></span>
                                </button>
                                <a href="my-paintings" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>

                        <?php if (!$isEdit): ?>
                            <div class="modal fade" id="priceCalculatorModal" tabindex="-1" aria-labelledby="priceCalculatorModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="priceCalculatorModalLabel">Price Calculator</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="price-calc-alert" class="alert alert-danger d-none" role="alert"></div>

                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="calc_mode" class="form-label">Mode</label>
                                                    <select id="calc_mode" class="form-select">
                                                        <option value="income">From desired income</option>
                                                        <option value="price">From price</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="calc_value" class="form-label">Value</label>
                                                    <input id="calc_value" type="number" class="form-control" min="0" step="0.01" value="0">
                                                    <div class="form-text" id="calc_value_hint">Desired income amount</div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="calc_commission" class="form-label">Commission (%)</label>
                                                    <input id="calc_commission" type="number" class="form-control" min="0" step="0.01" value="15">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="calc_tax" class="form-label">Tax (%)</label>
                                                    <input id="calc_tax" type="number" class="form-control" min="0" step="0.01" value="22">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="calc_expenses" class="form-label">Expenses</label>
                                                    <input id="calc_expenses" type="number" class="form-control" min="0" step="0.01" value="0">
                                                </div>
                                                <div class="col-md-6">
                                                    <div>
                                                        <input type="checkbox" id="calc_is_tax_resident" checked>
                                                        <label for="calc_is_tax_resident">
                                                            Tax resident
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>

                                            <div id="price-calc-result" class="d-none">
                                                <div class="row g-3">
                                                    <div class="col-md-6"><strong id="result_main_label">Your price:</strong> <span id="calc_result_main">0.00</span></div>
                                                    <div class="col-md-6"><strong>Tax:</strong> <span id="calc_result_tax">0.00</span></div>
                                                    <div class="col-md-6"><strong>Commission:</strong> <span id="calc_result_commission">0.00</span></div>
                                                    <div class="col-md-6"><strong>Expenses:</strong> <span id="calc_result_expenses">0.00</span></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary" id="calc_run_btn">Calculate</button>
                                            <button type="button" class="btn btn-success" id="calc_use_price_btn" disabled>Use price in form</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script src="/artportal/public/js/price-calculator.js"></script>
                        <?php endif; ?>
                        <script src="/artportal/public/js/painting-create-loading.js"></script>
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
