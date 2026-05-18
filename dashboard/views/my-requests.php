<?php
// My requests view
// Output buffering for template rendering
ob_start();
?>
<!-- Main container for my requests page -->
<div class="container my-4">
    <!-- Page title -->
    <h2 class="mb-4">My Requests</h2>

    <?php if (empty($requests)): ?>
        <!-- Info alert if no requests -->
        <div class="alert alert-info" role="alert">
            <p class="mb-0">You haven't sent any requests yet. <a href="../all">Browse our collection</a> to request a painting you like!</p>
        </div>
    <?php else: ?>
        <!-- List of requests -->
        <div class="list-group">
            <?php foreach ($requests as $request): ?>
                <div class="list-group-item mb-3 p-3 rounded-3 shadow-sm">
                    <div class="d-flex align-items-start gap-3">
                        <!-- Painting image -->
                        <div style="width:120px; flex:0 0 120px;">
                            <a href="../paintings?id=<?php echo (int)($request['painting_id'] ?? 0); ?>&from=dashboard" class="d-block">
                                <img src="/artportal/images/paintings/<?php echo htmlspecialchars($request['image'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($request['painting_title'] ?? 'Untitled', ENT_QUOTES, 'UTF-8'); ?>" onerror="this.onerror=null;this.src='/artportal/images/test.jpg';">
                            </a>
                        </div>
                        <!-- Request details -->
                        <div class="flex-grow-1">
                            <h5 class="mb-1">
                                <a href="../paintings?id=<?php echo (int)($request['painting_id'] ?? 0); ?>&from=dashboard" class="text-reset text-decoration-none">
                                    <?php echo htmlspecialchars($request['painting_title'] ?? 'Untitled', ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </h5>
                            <!-- Artist name -->
                            <p class="mb-1"><?php echo htmlspecialchars($request['artist_name'] ?? 'Unknown artist', ENT_QUOTES, 'UTF-8'); ?></p>
                            <!-- Request date -->
                            <p class="mb-2 small text-muted">Requested: <?php echo htmlspecialchars($request['created_at'] ?? 'Unknown date', ENT_QUOTES, 'UTF-8'); ?></p>
                            <!-- View painting button -->
                            <a href="../paintings?id=<?php echo (int)($request['painting_id'] ?? 0); ?>&from=dashboard" class="btn btn-sm btn-outline-primary">View painting</a>
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