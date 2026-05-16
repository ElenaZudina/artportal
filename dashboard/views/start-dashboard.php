<?php ob_start() ?>
<article>
    <div id="main" class="container">
        
        <?php if ($_SESSION['status'] === 'artist'): ?>
        
        <h3>Artist Dashboard</h3>

        <div class="card calculator-teaser shadow-sm mb-3">
            <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="text-uppercase small opacity-75 mb-1">Tool</div>
                    <h4 class="mb-2">Price Calculator</h4>
                    <div class="opacity-75">Open a quick pricing window for income, commission, tax, and expenses.</div>
                </div>
                <button type="button" class="btn btn-light btn-lg px-4" data-bs-toggle="modal" data-bs-target="#priceCalculatorModal">
                    Open Calculator
                </button>
            </div>
        </div>

        <div class="modal fade" id="priceCalculatorModal" tabindex="-1" aria-labelledby="priceCalculatorModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="priceCalculatorModalLabel">Price Calculator</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="price-calc-alert" class="alert alert-danger d-none" role="alert"></div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="calc_mode" class="form-label">Mode</label>
                                <select id="calc_mode" class="form-select">
                                    <option value="income">From desired income</option>
                                    <option value="price">From price</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="calc_value" class="form-label">Value</label>
                                <input id="calc_value" type="number" class="form-control" min="0" step="0.01" value="0">
                                <div class="form-text" id="calc_value_hint">Desired income amount</div>
                            </div>
                            <div class="col-md-6">
                                <label for="calc_commission" class="form-label">Commission (%)</label>
                                <input id="calc_commission" type="number" class="form-control" min="0" step="0.01" value="15">
                            </div>
                            <div class="col-md-6">
                                <label for="calc_tax" class="form-label">Tax (%)</label>
                                <input id="calc_tax" type="number" class="form-control" min="0" step="0.01" value="22">
                            </div>
                            <div class="col-md-6">
                                <label for="calc_expenses" class="form-label">Expenses</label>
                                <input id="calc_expenses" type="number" class="form-control" min="0" step="0.01" value="0">
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <input type="checkbox" id="calc_is_tax_resident" checked>
                                    <label for="calc_is_tax_resident">
                                        Tax resident
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div id="price-calc-result" class="d-none">
                            <div class="row g-3">
                                <div class="col-md-6"><strong id="result_main_label">Your price:</strong> <span id="calc_result_main">0.00</span></div>
                                <div class="col-md-6"><strong>Tax:</strong> <span id="calc_result_tax">0.00</span></div>
                                <div class="col-md-6"><strong>Commission:</strong> <span id="calc_result_commission">0.00</span></div>
                                <div class="col-md-6"><strong>Expenses:</strong> <span id="calc_result_expenses">0.00</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="calc_run_btn">Calculate</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="/artportal/public/js/price-calculator.js"></script>

        <!-- Top KPI summary (admin style) -->
        <div class="card p-3 dashboard-summary mb-3">
            <h5 class="mb-2">Dashboard summary</h5>
            <style>
                .kpi-link{display:block;color:inherit;text-decoration:none}
                .kpi-link .kpi-card{transition:background 0.12s}
                .kpi-link:hover .kpi-card{background:rgba(0,0,0,0.03);cursor:pointer}
            </style>
            <div class="row g-3">
                <div class="col-6 col-md-2">
                    <a href="my-paintings" class="kpi-link">
                        <div class="kpi-card text-center">
                            <div class="kpi-icon"><i class="fa-solid fa-image"></i></div>
                            <div class="kpi-value"><?php echo $paintingsCount ?? '—'; ?></div>
                            <div class="kpi-sub">Portfolio</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-2">
                    <a href="purchase-requests" class="kpi-link">
                        <div class="kpi-card text-center">
                            <div class="kpi-icon"><i class="fa-solid fa-inbox"></i></div>
                            <div class="kpi-value"><?php echo $requestsCount ?? '—'; ?></div>
                            <div class="kpi-sub">Requests</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-2">
                    <div class="kpi-card text-center">
                        <div class="kpi-icon"><i class="fa-solid fa-heart"></i></div>
                        <div class="kpi-value"><?php echo $favoritesCount ?? '—'; ?></div>
                        <div class="kpi-sub">Saved by users</div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="kpi-card text-center">
                        <div class="kpi-icon"><i class="fa-solid fa-eye"></i></div>
                        <div class="kpi-value"><?php echo $viewsTotal ?? '—'; ?></div>
                        <div class="kpi-sub">Views</div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="kpi-card text-center">
                        <div class="kpi-icon"><i class="fa-solid fa-user"></i></div>
                        <div class="kpi-value"><?php echo htmlspecialchars($user['username'] ?? $user['email'] ?? '—'); ?></div>
                        <div class="kpi-sub">Account</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card p-3 mb-3">
                    <h5>Requests <small class="text-muted">Count: <?php echo $requestsCount ?? 0; ?></small></h5>
                    <?php if (!empty($requests)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($requests as $r): ?>
                                <li class="list-group-item">
                                    <strong><?php echo htmlspecialchars($r['user_name'] ?? $r['user_email'] ?? 'Client'); ?></strong>
                                    <div class="small text-muted"><?php echo htmlspecialchars($r['painting_title'] ?? ''); ?></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="mb-0">No new requests</p>
                    <?php endif; ?>
                    <div class="mt-2"><a href="purchase-requests" class="btn btn-sm btn-outline-secondary">View all</a></div>
                </div>

                <div class="card p-3 mb-3">
                    <h5>Quick Actions</h5>
                    <div class="d-flex gap-2">
                        <a href="add-painting" class="btn btn-success">+ Add Painting</a>
                        <a href="purchase-requests" class="btn btn-outline-primary">Requests</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card p-3 mb-3">
                    <h5>Portfolio <small class="text-muted">Works: <?php echo $paintingsCount ?? 0; ?></small></h5>
                    <?php if (!empty($paintings)): ?>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach (array_slice($paintings, 0, 6) as $p): ?>
                                <div style="width:90px;text-align:center">
                                    <a href="viewpainting?id=<?php echo (int)$p['id']; ?>">
                                        <img src="/artportal/images/paintings/<?php echo htmlspecialchars($p['image'] ?? ''); ?>" alt="" style="width:90px;height:60px;object-fit:cover;border:1px solid #ddd">
                                    </a>
                                    <div class="small text-truncate"><?php echo htmlspecialchars($p['title'] ?? ''); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="mb-0">No works yet. <a href="add-painting">Add one</a></p>
                    <?php endif; ?>
                    <div class="mt-2"><a href="my-paintings" class="btn btn-sm btn-outline-secondary">View all</a></div>
                </div>

                <div class="card p-3 mb-3">
                    <h5>Statistics</h5>
                    <div class="row">
                        <div class="col-4 text-center">
                            <div class="h3 m-0"><?php echo $favoritesCount ?? 0; ?></div>
                            <div class="small text-muted">Saved by users</div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="h3 m-0"><?php echo $viewsTotal ?? 0; ?></div>
                            <div class="small text-muted">Views</div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="h3 m-0"><?php echo $requestsCount ?? 0; ?></div>
                            <div class="small text-muted">Requests</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php elseif ($_SESSION['status'] === 'user'): ?>
        
        <h3>Welcome to ArtPortal</h3>

        <!-- Top KPI summary (user style) -->
        <div class="card p-3 dashboard-summary mb-3">
            <h5 class="mb-2">Dashboard summary</h5>
            <style>
                .kpi-link{display:block;color:inherit;text-decoration:none}
                .kpi-link .kpi-card{transition:background 0.12s}
                .kpi-link:hover .kpi-card{background:rgba(0,0,0,0.03);cursor:pointer}
            </style>
            <div class="row g-3">
                <div class="col-6 col-md-2">
                    <a href="my-favorites" class="kpi-link">
                        <div class="kpi-card text-center">
                            <div class="kpi-icon"><i class="fa-solid fa-heart"></i></div>
                            <div class="kpi-value"><?php echo $favoritesCount ?? '—'; ?></div>
                            <div class="kpi-sub">Favorites</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-2">
                    <a href="my-requests" class="kpi-link">
                        <div class="kpi-card text-center">
                            <div class="kpi-icon"><i class="fa-solid fa-inbox"></i></div>
                            <div class="kpi-value"><?php echo $userRequestsCount ?? '—'; ?></div>
                            <div class="kpi-sub">Requests</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-2">
                    <a href="account" class="kpi-link">
                        <div class="kpi-card text-center">
                            <div class="kpi-icon"><i class="fa-solid fa-user"></i></div>
                            <div class="kpi-value"><?php echo htmlspecialchars($user['username'] ?? $user['email'] ?? '—'); ?></div>
                            <div class="kpi-sub">Account</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card p-3 mb-3">
                    <h5>My Requests <small class="text-muted">Count: <?php echo $userRequestsCount ?? 0; ?></small></h5>
                    <?php if (!empty($userRequests)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($userRequests as $r): ?>
                                <li class="list-group-item">
                                    <strong><?php echo htmlspecialchars($r['painting_title'] ?? 'Untitled'); ?></strong>
                                    <div class="small text-muted"><?php echo htmlspecialchars($r['artist_name'] ?? 'Unknown Artist'); ?></div>
                                    <div class="small text-muted">Requested: <?php echo htmlspecialchars($r['created_at'] ?? 'Unknown date'); ?></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="mb-0">No active requests</p>
                    <?php endif; ?>
                    <div class="mt-2"><a href="my-requests" class="btn btn-sm btn-outline-secondary">View all</a></div>
                </div>

                <div class="card p-3 mb-3">
                    <h5>Quick Actions</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="../all" class="btn btn-primary">Browse Paintings</a>
                        <a href="../artists" class="btn btn-outline-primary">Find Artists</a>
                    </div>
                    <div class="mt-3 p-3 rounded-4 bg-light border d-flex align-items-center justify-content-between gap-3 flex-wrap">
                        <div>
                            <div class="fw-semibold">Want to showcase your work?</div>
                            <div class="small text-muted">Create an artist profile when you're ready.</div>
                        </div>
                        <a href="/artportal/artistProfileForm" class="btn btn-sm btn-outline-primary">Become an artist</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card p-3 mb-3">
                    <h5>Favorites <small class="text-muted">Saved: <?php echo $favoritesCount ?? 0; ?></small></h5>
                    <?php if (!empty($favorites)): ?>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach (array_slice($favorites, 0, 6) as $f): ?>
                                <div style="width:90px;text-align:center">
                                    <a href="../paintings?id=<?php echo (int)($f['painting_id'] ?? 0); ?>&from=dashboard">
                                        <img src="/artportal/images/paintings/<?php echo htmlspecialchars($f['image'] ?? ''); ?>" alt="" style="width:90px;height:60px;object-fit:cover;border:1px solid #ddd">
                                    </a>
                                    <div class="small text-truncate"><?php echo htmlspecialchars($f['title'] ?? ''); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="mb-0">No favorites yet. <a href="../all">Add one</a></p>
                    <?php endif; ?>
                    <div class="mt-2"><a href="my-favorites" class="btn btn-sm btn-outline-secondary">View all</a></div>
                </div>

                <div class="card p-3 mb-3">
                    <h5>Latest Works</h5>
                    <?php if (!empty($recentPaintings)): ?>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach (array_slice($recentPaintings, 0, 6) as $p): ?>
                                <div style="width:90px;text-align:center">
                                    <a href="../paintings?id=<?php echo (int)($p['id'] ?? 0); ?>&from=dashboard">
                                        <img src="/artportal/images/paintings/<?php echo htmlspecialchars($p['image'] ?? ''); ?>" alt="" style="width:90px;height:60px;object-fit:cover;border:1px solid #ddd">
                                    </a>
                                    <div class="small text-truncate"><?php echo htmlspecialchars($p['title'] ?? ''); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="mb-0">No works available</p>
                    <?php endif; ?>
                    <div class="mt-2"><a href="../all" class="btn btn-sm btn-outline-secondary">Explore more</a></div>
                </div>
            </div>
        </div>
        
        <?php endif; ?>
        
    </div>
</article>

<?php $content = ob_get_clean(); ?>
<?php include "views/templates/layout.php"; ?>