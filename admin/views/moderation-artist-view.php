<?php
ob_start();
?>

<div class="container" style="min-height:400px;">
    <div class="mb-4">
        <a class="btn btn-outline-secondary" href="moderation-artists">Back to requests</a>
    </div>

    <?php if (!empty($item)): ?>
        <div class="mb-3">
            <span class="badge bg-warning text-dark">Pending</span>
        </div>

        <!-- Artist Profile -->
        <div class="container my-4">
            <div class="row align-items-stretch gx-5">
                <!-- Left: Artist Image -->
                <div class="col-12 col-md-6 mb-4 mb-md-0">
                    <div class="single-artist-container">
                        <div class="single-artist-image-wrapper">
                            <img src="/artportal/images/artists/<?php echo htmlspecialchars($item['picture'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid w-100 single-artist-image" alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>" onerror="this.onerror=null;this.src='/artportal/images/test.jpg';" />
                        </div>
                    </div>
                </div>
                
                <!-- Right: Description -->
                <div class="col-12 col-md-6 d-flex flex-column justify-content-center">
                    <h1 class="single-card-title mb-4"><?php echo htmlspecialchars($item['name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p class="card-text"><svg class="location-marker-icon" width="14" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22s7-7.2 7-12a7 7 0 1 0-14 0c0 4.8 7 12 7 12z" fill="none" stroke="#4A5565" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path><circle cx="12" cy="10" r="2.4" fill="#4A5565"></circle></svg> <?php echo htmlspecialchars($item['location'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="card-text single-artist-description"><?php echo htmlspecialchars($item['bio'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
        </div>

        <!-- Portfolio -->
        <?php if (!empty($item['paintings']) && is_array($item['paintings'])): ?>
            <div class="container my-4">
                <h2 class="mb-3">Portfolio</h2>
                <div class="row g-3 artist-portfolio-grid">
                    <?php foreach ($item['paintings'] as $painting): ?>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                            <div class="card d-block h-100 rounded-5 overflow-hidden">
                                <div class="card-img-wrapper">
                                    <img src="/artportal/images/paintings/<?php echo htmlspecialchars($painting['image'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($painting['title'] ?? 'Untitled', ENT_QUOTES, 'UTF-8'); ?>" onerror="this.onerror=null;this.src='/artportal/images/test.jpg';">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="container my-4">
                <p>У этого художника пока нет картин.</p>
            </div>
        <?php endif; ?>

        <div class="d-flex flex-wrap gap-2 mt-4">
            <a class="btn btn-success" href="approve-artist?id=<?php echo (int)$item['id']; ?>">Approve</a>
            <a class="btn btn-danger" href="reject-artist?id=<?php echo (int)$item['id']; ?>">Reject</a>
            <a class="btn btn-outline-secondary" href="moderation-artists">Back to requests</a>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">Artist profile not found.</div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php include 'views/templates/layout.php'; ?>
