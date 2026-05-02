<?php
ob_start();
?>

<h2>Exhibitions List</h2>

<div class="container" style="min-height:400px;">
    <div class="mb-3">
        <a class="btn btn-primary" href="create-exhibition" role="button">Add exhibition</a>
    </div>
    <div class="col-md-11">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <tr>
                    <th width="6%">ID</th>
                    <th width="16%">Title</th>
                    <th width="25%">Description</th>
                    <th width="15%">Collection</th>
                    <th width="12%">Start Date</th>
                    <th width="12%">End Date</th>
                    <th width="14%"></th>
                </tr>
                <?php if (!empty($arr)): ?>
                    <?php foreach ($arr as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars((string)($row['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['collection_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-nowrap"><?php echo htmlspecialchars($row['start_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-nowrap"><?php echo htmlspecialchars($row['end_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-nowrap">
                                <a href="#">Edit <i class="fa fa-pen" aria-hidden="true"></i></a>
                                <a href="#">Delete <i class="fa fa-trash" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No exhibitions found.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php include "views/templates/layout.php"; ?>