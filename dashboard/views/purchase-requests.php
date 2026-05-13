<?php ob_start() ?>
<article>
    <div id="main" class="container">
        <h3>Purchase Requests</h3>
        <div class="row mb-4">
            <div class="col-12">
                <?php if (empty($requests)): ?>
                    <div class="alert alert-info">
                        <p>You have no purchase requests yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Painting</th>
                                    <th>Buyer Name</th>
                                    <th>Buyer Email</th>
                                    <th>Request Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $request): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($request['painting_title'] ?? '—'); ?></strong>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($request['user_name'] ?? '—'); ?>
                                        </td>
                                        <td>
                                            <a href="mailto:<?php echo htmlspecialchars($request['user_email'] ?? ''); ?>">
                                                <?php echo htmlspecialchars($request['user_email'] ?? '—'); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php 
                                                $date = $request['created_at'] ?? '';
                                                echo !empty($date) ? date('d.m.Y H:i', strtotime($date)) : '—'; 
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</article>

<?php $content = ob_get_clean(); ?>
<?php include "views/templates/layout.php"; ?>
