<?php
ob_start();
?>

<h2>Collections List</h2>

<div class="container" style="min-height:400px;">
    <div class="mb-3">
        <a class="btn btn-primary" href="create-collection" role="button">Add collection</a>
    </div>
    <div class="col-md-11">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <tr>
                    <th width="8%">ID</th>
                    <th width="40%">Title</th>
                    <th width="18%">Type</th>
                    <th width="20%">Param</th>
                    <th width="14%"></th>
                </tr>
                <?php if (!empty($arr)): ?>
                    <?php foreach ($arr as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars((string)$row['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['type'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['param'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-nowrap">
                                <a href="edit-collection?id=<?php echo (int)$row['id']; ?>">Edit <i class="fa fa-pen" aria-hidden="true"></i></a>
                                <a href="delete-collection?id=<?php echo (int)$row['id']; ?>">Delete <i class="fa fa-trash" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No collections found.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php include "views/templates/layout.php"; ?>