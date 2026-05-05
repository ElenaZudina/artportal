<?php
ob_start();
?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
                        <div>
                            <h2 class="mb-1">My Account</h2>
                            <p class="text-muted mb-0">Data from users table</p>
                        </div>
                    </div>

                    <div class="border rounded-4 p-3 h-100 bg-light">
                        <h5 class="mb-3">Account Details</h5>
                        <div class="mb-2"><strong>Name:</strong> <?php echo htmlspecialchars($user['username'] ?? $_SESSION['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="mb-2"><strong>Role:</strong> <?php echo htmlspecialchars($user['role'] ?? ($_SESSION['status'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="mb-2"><strong>User ID:</strong> <?php echo htmlspecialchars((string)($user['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'views/templates/layout.php';
?>