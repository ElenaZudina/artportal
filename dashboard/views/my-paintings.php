<?php
ob_start();
?>

<div class="container mt-4 mb-5">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
                <div>
                    <h2 class="mb-1">My Paintings</h2>
                    <p class="text-muted mb-0">Portfolio items from your artist profile</p>
                </div>
                <a class="btn btn-success" href="add-painting">
                    <i class="fa fa-plus me-1"></i>Add Painting
                </a>
            </div>

            <?php if (empty($paintings)): ?>
                <div class="alert alert-info mb-0">
                    You do not have any paintings yet.
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($paintings as $painting): ?>
                        <div class="col-md-6 col-xl-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <img
                                    src="/artportal/images/paintings/<?php echo htmlspecialchars($painting['image'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    class="card-img-top"
                                    alt="<?php echo htmlspecialchars($painting['title'] ?? 'Untitled', ENT_QUOTES, 'UTF-8'); ?>"
                                    onerror="this.onerror=null;this.src='/artportal/images/test.jpg';"
                                    style="object-fit: cover; height: 240px;"
                                >
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title mb-2"><?php echo htmlspecialchars($painting['title'] ?? 'Untitled', ENT_QUOTES, 'UTF-8'); ?></h5>
                                    <div class="small text-muted mb-3">
                                        <div><strong>Category:</strong> <?php echo htmlspecialchars($painting['category_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></div>
                                        <div><strong>Year:</strong> <?php echo htmlspecialchars((string)($painting['year_created'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8'); ?></div>
                                        <div><strong>Medium:</strong> <?php echo htmlspecialchars($painting['medium'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></div>
                                        <div><strong>Dimensions:</strong> <?php echo htmlspecialchars($painting['dimensions'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></div>
                                        <div><strong>Price:</strong> <?php echo htmlspecialchars(number_format((float)($painting['price'] ?? 0), 2), ENT_QUOTES, 'UTF-8'); ?></div>
                                    </div>
                                    <p class="card-text text-muted small mb-4">
                                        <?php echo htmlspecialchars(mb_strimwidth((string)($painting['description'] ?? 'Unknown'), 0, 150, '...'), ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                    <div class="mt-auto d-flex gap-2 flex-wrap">
                                        <a class="btn btn-outline-primary btn-sm" href="edit-painting?id=<?php echo (int)($painting['id'] ?? 0); ?>">Edit</a>
                                        <a class="btn btn-outline-danger btn-sm" href="delete-painting?id=<?php echo (int)($painting['id'] ?? 0); ?>">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'views/templates/layout.php';
?>