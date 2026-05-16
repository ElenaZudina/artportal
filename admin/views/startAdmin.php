<?php ob_start() ?>
<article>
    <div id="main" class="container">
        <h3>Admin Panel</h3>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card p-3 dashboard-summary">
                    <h5 class="mb-3">Dashboard summary</h5>
                    <style>
                        .kpi-link{display:block;color:inherit;text-decoration:none}
                        .kpi-link .kpi-card{transition:background 0.12s}
                        .kpi-link:hover .kpi-card{background:rgba(0,0,0,0.03);cursor:pointer}
                    </style>
                    <div class="row g-3">
                        <div class="col-6 col-md-2">
                            <div class="kpi-card text-center">
                                <div class="kpi-icon"><i class="fa-solid fa-palette"></i></div>
                                <div class="kpi-value"><?php echo isset($counts['artists']) ? $counts['artists'] : '—'; ?></div>
                                <div class="kpi-sub">Artists</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <a href="moderation-artists" class="kpi-link">
                                <div class="kpi-card text-center">
                                    <div class="kpi-icon"><i class="fa-solid fa-shield-halved"></i></div>
                                    <div class="kpi-value"><?php echo isset($counts['pending_profiles']) ? $counts['pending_profiles'] : '—'; ?></div>
                                    <div class="kpi-sub">Pending profiles</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-2">
                            <a href="collections" class="kpi-link">
                                <div class="kpi-card text-center">
                                    <div class="kpi-icon"><i class="fa-solid fa-folder-open"></i></div>
                                    <div class="kpi-value"><?php echo isset($counts['collections']) ? $counts['collections'] : '—'; ?></div>
                                    <div class="kpi-sub">Collections</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-2">
                            <a href="exhibitions" class="kpi-link">
                                <div class="kpi-card text-center">
                                    <div class="kpi-icon"><i class="fa-solid fa-image"></i></div>
                                    <div class="kpi-value"><?php echo isset($counts['exhibitions']) ? $counts['exhibitions'] : '—'; ?></div>
                                    <div class="kpi-sub">Exhibitions</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-2">
                            <a href="users" class="kpi-link">
                                <div class="kpi-card text-center">
                                    <div class="kpi-icon"><i class="fa-solid fa-users"></i></div>
                                    <div class="kpi-value"><?php echo isset($counts['users']) ? $counts['users'] : '—'; ?></div>
                                    <div class="kpi-sub">Users</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-2">
                            <a href="categories" class="kpi-link">
                                <div class="kpi-card text-center">
                                    <div class="kpi-icon"><i class="fa-solid fa-tags"></i></div>
                                    <div class="kpi-value"><?php echo isset($counts['categories']) ? $counts['categories'] : '—'; ?></div>
                                    <div class="kpi-sub">Categories</div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <!-- summary chart removed per request -->
                    </div>
                </div>
            </div>
        <div class="row mb-3">
            <div class="col-12 col-md-6">
                <div class="card p-3">
                    <h5 class="mb-3">Pending Review</h5>
                    <div class="row g-2">
                        <div class="col-12 text-center">
                            <div class="text-muted small">Artist Profiles</div>
                            <div class="h5 mb-0"><?php echo isset($counts['pending_profiles']) ? $counts['pending_profiles'] : '—'; ?></div>
                            <div class="mt-3">
                                <a href="moderation-artists" class="btn btn-outline-primary btn-sm">Review profiles</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card p-3">
                    <h5 class="mb-3">Quick Actions</h5>
                    <div class="d-flex flex-column gap-2">
                        <a href="moderation-artists" class="btn btn-outline-primary">Review profiles</a>
                        <a href="users" class="btn btn-outline-dark">Moderate users</a>
                        <a href="create-category" class="btn btn-outline-secondary">Add category</a>
                        <a href="create-exhibition" class="btn btn-outline-success">Create exhibition</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card p-3">
                    <?php $periodDays = (isset($userGrowthChart['periodDays']) ? $userGrowthChart['periodDays'] : (is_array($userGrowth) ? count($userGrowth) : 7)); ?>
                    <h5 class="mb-3">User Growth (last <?php echo $periodDays; ?> days)</h5>
                    <div style="height:220px;">
                        <canvas id="usersGrowthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <?php
            // Read prepared chart data from service via controller
            $gLabels = isset($userGrowthChart['labels']) && is_array($userGrowthChart['labels']) ? $userGrowthChart['labels'] : [];
            $gTotals = isset($userGrowthChart['values']) && is_array($userGrowthChart['values']) ? $userGrowthChart['values'] : [];
            if (empty($gLabels) || empty($gTotals)) {
                // fallback sample
                $gLabels = ['01.05','02.05','03.05'];
                $gTotals = [3,7,2];
            }
        ?>

        <div id="chartDataContainer" data-labels='<?php echo json_encode($gLabels, JSON_UNESCAPED_UNICODE); ?>' data-values='<?php echo json_encode($gTotals, JSON_UNESCAPED_UNICODE); ?>'></div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="/artportal/public/js/user-growth-chart.js"></script>
            <div class="row">
    </div>
</article>

<?php $content = ob_get_clean(); ?>
<?php include "views/templates/layout.php"; ?>