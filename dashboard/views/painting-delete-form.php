<?php
ob_start();
?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="mb-3">Delete Painting</h2>

                    <?php if (isset($test)): ?>
                        <?php if ($test == true): ?>
                            <div class="alert alert-info">
                                <strong>The painting has been deleted.</strong>
                                <div class="mt-3">
                                    <a href="my-paintings" class="btn btn-success">Go to paintings list</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <strong><?php echo !empty($errorMessage) ? htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') : 'Error deleting painting!'; ?></strong>
                                <div class="mt-3">
                                    <a href="my-paintings" class="btn btn-success">Go to paintings list</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="row g-3 align-items-start">
                            <div class="col-md-5">
                                <img
                                    src="/artportal/images/paintings/<?php echo htmlspecialchars($painting['image'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    class="img-fluid rounded-4"
                                    alt="<?php echo htmlspecialchars($painting['title'] ?? 'Untitled', ENT_QUOTES, 'UTF-8'); ?>"
                                    onerror="this.onerror=null;this.src='/artportal/images/test.jpg';"
                                >
                            </div>
                            <div class="col-md-7">
                                <div class="mb-2"><strong>Title:</strong> <?php echo htmlspecialchars($painting['title'] ?? 'Untitled', ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="mb-2"><strong>Category:</strong> <?php echo htmlspecialchars($painting['category_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="mb-2"><strong>Year:</strong> <?php echo htmlspecialchars((string)($painting['year_created'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="mb-2"><strong>Price:</strong> <?php echo htmlspecialchars(number_format((float)($painting['price'] ?? 0), 2), ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="mb-2"><strong>Medium:</strong> <?php echo htmlspecialchars($painting['medium'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="mb-2"><strong>Dimensions:</strong> <?php echo htmlspecialchars($painting['dimensions'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                        </div>

                        <form method="POST" action="destroy-painting?id=<?php echo (int)($painting['id'] ?? 0); ?>" class="mt-4">
                            <?php echo CsrfHelper::field(); ?>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-danger">Delete</button>
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
