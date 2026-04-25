<?php
ob_start();

if (isset($result)) {
    if ($result[0] === true) {
        ?>
        <div class="alert alert-info text-center mb-4 mt-5 p-4">
            <strong>User has been added.</strong><br>
            <a href="admin" class="btn btn-outline-primary mt-3">Go to Dashboard</a>
        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-warning text-center mb-4 mt-5 p-4">
            <strong>Error!</strong> <?php echo htmlspecialchars($result[1]); ?><br>
            <a href="registerForm" class="btn btn-outline-secondary mt-3">Back to Registration form</a>
        </div>
        <?php
    }
}

$content = ob_get_clean();
include "views/layout.php";