<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>YTR | Dashboard</title>
  
  <!-- Phosphor Icons -->
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  
  <!-- Centralized CSS -->
  <link rel="stylesheet" href="assets/dashboard.css">
</head>
<body>

  <div class="app-wrapper">
    <!-- INJECT SIDEBAR -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-wrapper">
      <!-- INJECT TOPBAR -->
      <?php include 'includes/topbar.php'; ?>

      <main class="page-content">
        
        <div class="page-header">
          <div>
            <h1 class="font-roboto">Chapter Overview</h1>
            <p>Monitor real-time chapter metrics, compliance status, and active reporting streams across all branches.</p>
          </div>
        </div>

        <!-- Four Responsive Cards -->
        <div class="metrics-grid">
          <div class="metric-card">
            <div class="metric-icon amber"><i class="ph-fill ph-file-text"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Pending Chapters Report</h5>
              <p class="metric-value font-roboto">0</p>
            </div>
          </div>
          
          <div class="metric-card">
            <div class="metric-icon green"><i class="ph-fill ph-chart-line-up"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Total Reports Submitted</h5>
              <p class="metric-value font-roboto">0</p>
            </div>
          </div>
          
          <div class="metric-card">
            <div class="metric-icon blue"><i class="ph-fill ph-users"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Partner Chapters Request</h5>
              <p class="metric-value font-roboto">0</p>
            </div>
          </div>
          
          <div class="metric-card">
            <div class="metric-icon rose"><i class="ph-fill ph-warning-circle"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Chapters Due Reports</h5>
              <p class="metric-value font-roboto">0</p>
            </div>
          </div>
        </div>

        <!-- Responsive Table -->
        <div class="table-section">
          <div class="table-header">
            <h2 class="font-roboto" style="color: var(--primary); font-size: 1.25rem; font-weight: bold;">Partner Chapters</h2>
            <div class="search-bar">
              <i class="ph ph-magnifying-glass"></i>
              <input type="text" class="font-roboto" placeholder="Search Chapter...">
            </div>
          </div>

          <!-- Tabs -->
          <div class="tabs-container font-roboto">
            <button class="tab-btn active"><i class="ph ph-buildings"></i> All Chapters</button>
            <button class="tab-btn"><i class="ph ph-check-circle"></i> Active Chapters</button>
            <button class="tab-btn"><i class="ph ph-warning-circle"></i> Pending Reports</button>
          </div>
          
          <div class="table-responsive">
            <table>
              <thead class="font-roboto">
                <tr>
                  <th>Chapter Name</th>
                  <th>Conducted Winning Events</th>
                  <th>Report Submitted</th>
                  <th>Non-Provided</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Laguna Chapter</td>
                  <td>12</td>
                  <td>12</td>
                  <td>12</td>
                </tr>
                <tr>
                  <td>NCR Chapter</td>
                  <td>25</td>
                  <td>25</td>
                  <td>25</td>
                </tr>
                <tr>
                  <td>Pampanga Chapter</td>
                  <td>10</td>
                  <td>10</td>
                  <td>10</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

</body>
</html>