<?php 
// Start output buffering for template rendering
ob_start(); 
?>
<!-- Main article container for purchase requests page -->
<article>
    <div id="main" class="container">
        <!-- Page title -->
        <h3>Purchase Requests</h3>
        <div class="row mb-4">
            <div class="col-12">
                <?php if (empty($requests)): ?>
                    <!-- Info alert if no requests -->
                    <div class="alert alert-info">
                        <p>You have no purchase requests yet.</p>
                    </div>
                <?php else: ?>
                    <!-- Table of purchase requests -->
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
                                        <!-- Painting title -->
                                        <td>
                                            <strong><?php echo htmlspecialchars($request['painting_title'] ?? '—'); ?></strong>
                                        </td>
                                        <!-- Buyer name -->
                                        <td>
                                            <?php echo htmlspecialchars($request['user_name'] ?? '—'); ?>
                                        </td>
                                        <!-- Buyer email -->
                                        <td>
                                            <a href="mailto:<?php echo htmlspecialchars($request['user_email'] ?? ''); ?>">
                                                <?php echo htmlspecialchars($request['user_email'] ?? '—'); ?>
                                            </a>
                                        </td>
                                        <!-- Request date -->
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
