<?php
/**
 * Artist Profile Result View
 * Shows success or validation errors after artist profile submission
 */
ob_start();
?>

<!-- Result message shown after attempting to create an artist profile. -->
<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php if (!empty($resultArtist['success'])): ?>
                <!-- Success state links the user back to the dashboard. -->
                <div class="my-alert-success text-center p-4" style="border-radius: var(--radius);">
                    <strong>Artist profile submitted successfully.</strong>
                    <p class="mt-3 mb-3">Your profile has been created with status <strong>pending</strong>.</p>
                    <a href="dashboard/startDashboard" class="my-alert-btn">Go to Dashboard</a>
                </div>
            <?php else: ?>
                <!-- Failure state lists validation or persistence errors. -->
                <div class="my-alert-error text-center p-4" style="border-radius: var(--radius);">
                    <strong>Unable to create artist profile.</strong><br>
                    <?php
                    if (!empty($resultArtist['errors']) && is_array($resultArtist['errors'])) {
                        foreach ($resultArtist['errors'] as $error) {
                            echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '<br>';
                        }
                    }
                    ?>
                    <div class="mt-3">
                        <a href="artistProfileForm" class="my-alert-btn">Back to Artist Profile Form</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Pass captured page markup into the shared layout.
$content = ob_get_clean();
include 'views/layout.php';
?>
