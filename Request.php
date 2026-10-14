<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Poster Approval | YTR Portal</title>
    <link rel="stylesheet" href="assets/dashboard.css">
    <!-- Include Phosphor Icons for icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar Include -->
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-wrapper">
            <!-- Topbar Include -->
            <?php include 'includes/topbar.php'; ?>

            <div class="page-content">
                
                <div class="page-header">
                    <div class="header-title-group">
                        <h1>Event Poster Approval</h1>
                    </div>
                </div>

                <!-- 1. Metric Cards Row -->
                <div class="metrics-grid">
                    <div class="metric-card">
                        <div class="metric-icon blue"><i class="ph ph-calendar-check"></i></div>
                        <div>
                            <span class="metric-title">Total Active Events</span>
                            <div class="metric-value">24</div>
                            <p class="metric-sub">Total approved posters waiting poster results</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon orange"><i class="ph ph-file-text"></i></div>
                        <div>
                            <span class="metric-title">Pending Poster Request</span>
                            <div class="metric-value">21</div>
                            <p class="metric-sub">New applications awaiting poster review</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon purple"><i class="ph ph-hourglass-high"></i></div>
                        <div>
                            <span class="metric-title">Awaiting Report</span>
                            <div class="metric-value">21</div>
                            <p class="metric-sub">Event Report is conditionally awaiting for the Event Happens</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon green"><i class="ph ph-check-circle"></i></div>
                        <div>
                            <span class="metric-title">Concluded Events</span>
                            <div class="metric-value">12</div>
                            <p class="metric-sub">Event Posters/Report accomplished</p>
                        </div>
                    </div>
                </div>

                <!-- 2. Tabs and Filters -->
                <div class="tab-controls-wrapper">
                    <div class="tabs-container font-roboto">
                        <!-- Default active is Accepted to match your right mockup -->
                        <button class="tab-btn active font-roboto" onclick="switchTab('accepted', this)"><i class="ph ph-check-circle"></i> Accepted</button>
                        <button class="tab-btn font-roboto" onclick="switchTab('queue', this)"><i class="ph ph-clock"></i> In Queue</button>
                    </div>
                    <div class="filters">
                        <button class="date-picker-btn font-roboto">
                            <i class="ph ph-calendar-blank"></i> September 5, 2026 - <i class="ph ph-caret-down"></i>
                        </button>
                    </div>
                </div>

                <!-- 3. Dynamic Card Grids -->
                
                <!-- TAB CONTENT: ACCEPTED -->
                <div id="accepted-grid" class="poster-grid tab-content active">
                    <!-- Example Accepted Card -->
                    <div class="poster-card type-accepted">
                        <div class="card-hero">
                            <img src="assets/images/poster-banner.jpg" alt="GOAT Banner">
                            <span class="status-badge green"><i class="ph ph-check-circle"></i> Event Accomplished</span>
                        </div>
                        <div class="card-body">
                            <h4 class="event-title">Greatest of all Time</h4>
                            <ul class="event-bullets">
                                <li>Sept 20, 2026</li>
                                <li>LSPU Los Baños Campus</li>
                                <li>YTR Target Sanctuary</li>
                            </ul>
                            <p class="event-desc">Join us for a life-changing encounter at the upcoming YTR Youth Camp! Our theme, "Greatest Of All Time (G.O.A.T.)," challenges us to discover true greatness through service, faith, and fellowship...</p>
                        </div>
                        <div class="card-footer dual-btn">
                            <button class="btn btn-outline"><i class="ph ph-download-simple"></i> Download</button>
                            <button class="btn btn-primary">View Details</button>
                        </div>
                    </div>
                    
                    <!-- Duplicate to demonstrate grid -->
                    <div class="poster-card type-accepted">
                        <div class="card-hero">
                            <img src="assets/images/poster-banner.jpg" alt="GOAT Banner">
                            <span class="status-badge green"><i class="ph ph-check-circle"></i> Event Accomplished</span>
                        </div>
                        <div class="card-body">
                            <h4 class="event-title">Greatest of all Time</h4>
                            <ul class="event-bullets">
                                <li>Sept 20, 2026</li>
                                <li>LSPU Los Baños Campus</li>
                                <li>YTR Target Sanctuary</li>
                            </ul>
                            <p class="event-desc">Join us for a life-changing encounter at the upcoming YTR Youth Camp! Our theme, "Greatest Of All Time (G.O.A.T.)," challenges us to discover true greatness through service, faith, and fellowship...</p>
                        </div>
                        <div class="card-footer dual-btn">
                            <button class="btn btn-outline"><i class="ph ph-download-simple"></i> Download</button>
                            <button class="btn btn-primary">View Details</button>
                        </div>
                    </div>
                </div>

                <!-- TAB CONTENT: IN QUEUE -->
                <div id="queue-grid" class="poster-grid tab-content" style="display: none;">
                    <!-- Example In-Queue Card -->
                    <div class="poster-card type-queue">
                        <div class="card-header flex-between">
                            <div class="chapter-info">
                                <div class="avatar-placeholder"><i class="ph ph-users"></i></div>
                                <div class="info-group">
                                    <span class="label">Chapter name</span>
                                    <span class="value">Laguna Chapter</span>
                                </div>
                            </div>
                            <div class="info-group text-right">
                                <span class="label">Date Requested</span>
                                <span class="value">12 Sept, 2026</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="info-group mb-3">
                                <span class="label">Contact person:</span>
                                <span class="value">Rayvhen P. Muñoz</span>
                            </div>
                            <div class="event-preview">
                                <img src="assets/images/poster-thumb.jpg" alt="Poster Thumbnail" class="poster-thumb">
                                <div class="preview-details">
                                    <div class="info-group mb-2">
                                        <span class="label">Poster Requested:</span>
                                        <span class="value">G.O.A.T</span>
                                    </div>
                                    <div class="info-group">
                                        <span class="label">Event Details:</span>
                                        <span class="value text-sm">Sept. 20-22, 2026<br>Resort YTR Assembly Center Amadeo</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer dual-btn">
                            <button class="btn btn-outline-danger"><i class="ph ph-x"></i> Reject</button>
                            <button class="btn btn-primary"><i class="ph ph-check"></i> Approve</button>
                        </div>
                    </div>
                    
                    <!-- Duplicate to demonstrate grid -->
                    <div class="poster-card type-queue">
                        <div class="card-header flex-between">
                            <div class="chapter-info">
                                <div class="avatar-placeholder"><i class="ph ph-users"></i></div>
                                <div class="info-group">
                                    <span class="label">Chapter name</span>
                                    <span class="value">Laguna Chapter</span>
                                </div>
                            </div>
                            <div class="info-group text-right">
                                <span class="label">Date Requested</span>
                                <span class="value">12 Sept, 2026</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="info-group mb-3">
                                <span class="label">Contact person:</span>
                                <span class="value">Rayvhen P. Muñoz</span>
                            </div>
                            <div class="event-preview">
                                <img src="assets/images/poster-thumb.jpg" alt="Poster Thumbnail" class="poster-thumb">
                                <div class="preview-details">
                                    <div class="info-group mb-2">
                                        <span class="label">Poster Requested:</span>
                                        <span class="value">G.O.A.T</span>
                                    </div>
                                    <div class="info-group">
                                        <span class="label">Event Details:</span>
                                        <span class="value text-sm">Sept. 20-22, 2026<br>Resort YTR Assembly Center Amadeo</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer dual-btn">
                            <button class="btn btn-outline-danger"><i class="ph ph-x"></i> Reject</button>
                            <button class="btn btn-primary"><i class="ph ph-check"></i> Approve</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Tab Switching Logic -->
    <script>
        function switchTab(tabId, clickedBtn) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });
            // Remove active class from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab and set button active
            document.getElementById(tabId + '-grid').style.display = 'grid';
            clickedBtn.classList.add('active');
        }
    </script>
</body>
</html>