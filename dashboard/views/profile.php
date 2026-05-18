<?php
// Output buffering for template rendering
ob_start();
?>

<!-- Main container for profile page -->
<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <!-- Header with page title and action button -->
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
                        <div>
                            <h2 class="mb-1">My Profile</h2>
                            <p class="text-muted mb-0">Artist profile details</p>
                        </div>
                        <?php if (!empty($profile)): ?>
                            <a class="btn btn-outline-primary" href="edit-profile">Edit</a>
                        <?php else: ?>
                            <a class="btn btn-primary" href="/artportal/artistProfileForm">Become an artist</a>
                        <?php endif; ?>
                    </div>

                    <!-- Artist profile details or info alert -->
                    <div class="border rounded-4 p-3 h-100">
                        <h5 class="mb-3">Artist Profile</h5>
                        <?php if (!empty($profile)): ?>
                            <div class="row g-3 align-items-start">
                                <!-- Artist picture -->
                                <div class="col-md-4">
                                    <img src="/artportal/images/artists/<?php echo htmlspecialchars($profile['picture'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid rounded-4" alt="<?php echo htmlspecialchars($profile['name'] ?? 'Artist', ENT_QUOTES, 'UTF-8'); ?>" onerror="this.onerror=null;this.src='/artportal/images/test.jpg';">
                                </div>
                                <div class="col-md-8">
                                    <!-- Artist name -->
                                    <div class="mb-2"><strong>Name:</strong> <?php echo htmlspecialchars($profile['name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></div>
                                    <!-- Artist location -->
                                    <div class="mb-2"><strong>Location:</strong> <?php echo htmlspecialchars($profile['location'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></div>
                                    <!-- Artist birth date -->
                                    <div class="mb-2"><strong>Birth date:</strong> <?php echo htmlspecialchars($profile['birth_date'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></div>
                                    <!-- Artist status -->
                                    <div class="mb-2"><strong>Status:</strong> <?php echo htmlspecialchars($profile['status'] ?? 'pending', ENT_QUOTES, 'UTF-8'); ?></div>
                                    <!-- Artist bio -->
                                    <div class="mb-2"><strong>Bio:</strong><br><?php echo nl2br(htmlspecialchars($profile['bio'] ?? '', ENT_QUOTES, 'UTF-8')); ?></div>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Info alert if no artist profile -->
                            <div class="alert alert-info mb-0">
                                You do not have an artist profile yet.
                                <?php if (($_SESSION['status'] ?? '') !== 'artist'): ?>
                                    <div class="mt-2">Fill the artist form if you want to become an artist.</div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
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