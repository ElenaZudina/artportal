<?php
/**
 * Users List View
 * Displays all users in admin panel
 */
ob_start();
?>

<!-- Main content: Users table -->
<h2>Users</h2>

<div class="container" style="min-height:400px;">
    <div class="card p-3 mb-3">
        <form method="get" action="users" class="row g-2 align-items-center">
            <div class="col-12 col-md-8">
                <input type="text" name="q" class="form-control" placeholder="Search by username or email" value="<?php echo htmlspecialchars($searchQuery ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="users" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>

    <?php if (isset($_SESSION['errorString'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['errorString'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['errorString']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['successString'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['successString'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['successString']); ?></div>
    <?php endif; ?>

    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <tr>
                    <th width="6%">ID</th>
                    <th width="22%">Username</th>
                    <th width="24%">Email</th>
                    <th width="14%">Role</th>
                    <th width="14%">Status</th>
                    <th width="20%"></th>
                </tr>
                <?php if (!empty($arr)): ?>
                    <?php foreach ($arr as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars((string)($row['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['role'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php if (($row['status'] ?? 'active') === 'blocked'): ?>
                                    <span class="badge bg-danger">Blocked</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (($row['role'] ?? '') === 'admin' || (int)($row['id'] ?? 0) === (int)($_SESSION['userId'] ?? 0)): ?>
                                    <span class="text-muted">Protected account</span>
                                <?php else: ?>
                                    <form method="post" action="user-status" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                                        <input type="hidden" name="q" value="<?php echo htmlspecialchars($searchQuery ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php if (($row['status'] ?? 'active') === 'blocked'): ?>
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="btn btn-sm btn-success">Unblock</button>
                                        <?php else: ?>
                                            <input type="hidden" name="status" value="blocked">
                                            <button type="submit" class="btn btn-sm btn-warning">Block</button>
                                        <?php endif; ?>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No users found.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include 'views/templates/layout.php'; ?>