<?php
/**
 * Registration Result View
 * Shows success or validation errors after account registration
 */
ob_start();

if (isset($result)) {
    // Success state offers next steps after creating the account.
    if ($result['success'] === true) {
        ?>
        <div class="my-alert-success text-center mb-4 mt-5 p-4" style="border-radius: var(--radius);">
            <strong>User has been added</strong><br>
            <p class="mt-3 mb-3">If you want to become an artist, please fill out your artist profile.</p>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a href="artistProfileForm" class="my-alert-btn">Fill Artist Profile</a>
                <a href="login" class="my-alert-btn">Go to Login</a>
            </div>
        </div>
        <?php
    } else {
        ?>
        <!-- Failure state lists registration validation errors. -->
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

// Pass captured page markup into the shared layout.
$content = ob_get_clean();
include "views/layout.php";
