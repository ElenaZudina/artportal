<?php
ob_start();

if (isset($result)) {
    if ($result['success'] === true) {
        ?>
        <div class="my-alert-success text-center mb-4 mt-5 p-4" style="border-radius: var(--radius);">
            <strong>User has been added</strong><br>
            <a href="admin" class="my-alert-btn">Go to Dashboard</a>
        </div>
        <?php
    } else {
        ?>
        <div class="my-alert-error text-center mb-4 mt-5 p-4" style="border-radius: var(--radius);">
            <?php
            foreach ($result['errors'] as $error) {
                echo htmlspecialchars($error) . "<br>";
            }
            ?>
            <a href="registerForm" class="my-alert-btn">Back to Registration form</a>
        </div>
        <?php
    }
}

$content = ob_get_clean();
include "views/layout.php";