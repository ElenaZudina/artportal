<?php
/**
 * Moderation Artists List View
 * Displays list of pending artist requests in admin panel
 */
ob_start();
?>

<!-- Main content: Pending artist requests table -->
<h2>Artist Requests</h2>

<div class="container" style="min-height:400px;">
    <div class="mb-3">
        <span class="text-muted">Pending artist applications waiting for approval.</span>
    </div>
    <div class="col-md-11">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <tr>
                    <th width="5%">ID</th>
                    <th width="25%">Name</th>
                    <th width="25%">Request Date</th>
                    <th width="15%">Status</th>
                    <th width="30%"></th>
                </tr>
                <?php if (!empty($arr)): ?>
                    <?php foreach ($arr as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars((string)$row['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="badge bg-warning text-dark">Pending</span></td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a class="btn btn-success btn-sm" href="approve-artist?id=<?php echo (int)$row['id']; ?>">Approve</a>
                                    <a class="btn btn-danger btn-sm" href="reject-artist?id=<?php echo (int)$row['id']; ?>">Reject</a>
                                    <a class="btn btn-outline-primary btn-sm" href="moderation-artist?id=<?php echo (int)$row['id']; ?>">View profile</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No pending artist requests.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include 'views/templates/layout.php'; ?>
