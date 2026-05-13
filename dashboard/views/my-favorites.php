<?php
ob_start();
?>
<div class="container my-4">
    <h2 class="mb-4">My Favorites</h2>
    
    <?php if (empty($favorites)): ?>
        <div class="alert alert-info" role="alert">
            <p class="mb-0">You haven't added any paintings to favorites yet. <a href="../all">Browse our collection</a> to find artworks you love!</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach($favorites as $painting): ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card rounded-5 overflow-hidden h-100">
                        <div class="card-img-wrapper position-relative">
                            <a href="../paintings?id=<?php echo (int)($painting['painting_id'] ?? 0); ?>&from=dashboard" class="d-block text-reset text-decoration-none h-100">
                                <img src="/artportal/images/paintings/<?php echo htmlspecialchars($painting['image'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($painting['title'], ENT_QUOTES, 'UTF-8'); ?>" onerror="this.onerror=null;this.src='/artportal/images/test.jpg';">
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="painting-meta d-flex justify-content-between align-items-start gap-2 mb-2">
                                <h3 class="card-title painting-title mb-0">
                                    <a href="../paintings?id=<?php echo (int)($painting['painting_id'] ?? 0); ?>&from=dashboard" class="text-reset text-decoration-none">
                                        <?php echo htmlspecialchars($painting['title'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </h3>
                                <p class="card-text painting-price text-nowrap mb-0"><?php echo htmlspecialchars($painting['price'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?> €</p>
                            </div>
                            <p class="card-text mb-2"><?php echo htmlspecialchars($painting['artist_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></p>
                            <form method="POST" action="../purchase-request" class="mb-2">
                                <input type="hidden" name="painting_id" value="<?php echo (int)($painting['painting_id'] ?? 0); ?>">
                                <button type="submit" class="btn btn-sm btn-outline-primary w-100">Inquire About Purchase</button>
                            </form>
                            <form method="POST" action="../toggle-favorite" class="mt-3">
                                <input type="hidden" name="painting_id" value="<?php echo (int)($painting['painting_id'] ?? 0); ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">Remove from Favorites</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include_once 'templates/layout.php';
?>
