<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>YTR | Manage Accounts</title>
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <link rel="stylesheet" href="assets/dashboard.css">
</head>
<body>
  <div class="app-wrapper">
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-wrapper">
      <?php include 'includes/topbar.php'; ?>

      <main class="page-content">
        
        <!-- Page Header -->
        <div class="page-header">
          <div class="header-title-group">
            <div class="header-icon"><i class="ph-fill ph-user"></i></div>
            <div>
              <h1 class="font-roboto">Manage Account</h1>
              <p>Review account requests, assign roles, and manage system access.</p>
            </div>
          </div>
        </div>

        <!-- 2-Column Responsive Layout -->
        <div class="two-column-layout">
          
          <!-- LEFT COLUMN (Metrics & Table) -->
          <div class="layout-main">
            
            <!-- Metrics -->
            <div class="metrics-grid">
              <div class="metric-card metric-card-row">
                <div class="metric-icon blue"><i class="ph-fill ph-users"></i></div>
                <div>
                  <h5 class="metric-title font-roboto">Total Accounts</h5>
                  <p class="metric-value font-roboto">41</p>
                  <p class="metric-sub font-roboto">Events conducted this month</p>
                </div>
              </div>
              <div class="metric-card metric-card-row">
                <div class="metric-icon amber"><i class="ph-fill ph-clock"></i></div>
                <div>
                  <h5 class="metric-title font-roboto">Pending Request</h5>
                  <p class="metric-value font-roboto">9</p>
                  <p class="metric-sub font-roboto">Across all events</p>
                </div>
              </div>
              <div class="metric-card metric-card-row">
                <div class="metric-icon green"><i class="ph-fill ph-shield-check"></i></div>
                <div>
                  <h5 class="metric-title font-roboto">National Admin</h5>
                  <p class="metric-value font-roboto">5</p>
                  <p class="metric-sub font-roboto">With full system access</p>
                </div>
              </div>
              <div class="metric-card metric-card-row">
                <div class="metric-icon purple"><i class="ph-fill ph-user-gear"></i></div>
                <div>
                  <h5 class="metric-title font-roboto">Chapter Admins</h5>
                  <p class="metric-value font-roboto">36</p>
                  <p class="metric-sub font-roboto">Manage assigned chapters</p>
                </div>
              </div>
            </div>

            <!-- Table Section -->
            <div class="table-section">
              
              <!-- Tabs -->
              <div class="tabs-container font-roboto">
                <button class="tab-btn"><i class="ph ph-users"></i> All Accounts</button>
                <button class="tab-btn active"><i class="ph ph-clock"></i> Pending Requests</button>
                <button class="tab-btn"><i class="ph ph-shield-check"></i> National Admins</button>
                <button class="tab-btn"><i class="ph ph-user-gear"></i> Chapter Admins</button>
                <button class="tab-btn"><i class="ph ph-prohibit"></i> Disabled</button>
              </div>

              <!-- Data Table -->
              <div class="table-responsive">
                <table class="font-roboto">
                  <thead>
                    <tr>
                      <th>Applicant</th>
                      <th>Email</th>
                      <th>Chapter</th>
                      <th style="text-align: center;">Requested Role</th>
                      <th>Date Requested</th>
                      <th style="text-align: center;">Status</th>
                      <th style="text-align: center;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Row 1 -->
                    <tr>
                      <td>
                        <div class="td-user">
                          <img src="https://placehold.co/32x32/e2e8f0/64748b" alt="Avatar">
                          <span class="font-roboto">Joan Dela Cruz</span>
                        </div>
                      </td>
                      <td class="td-sub">joan.dela@gmail.com</td>
                      <td class="td-sub">Laguna Chapter</td>
                      <td style="text-align: center;"><span class="badge chapter-admin font-roboto">Chapter Admin</span></td>
                      <td class="td-sub">Oct 13, 2026<br>10:30 AM</td>
                      <td style="text-align: center;"><span class="badge pending font-roboto">Pending</span></td>
                      <td style="text-align: center;">
                        <button class="btn btn-primary btn-sm font-roboto" style="margin: 0 auto;">Review</button>
                      </td>
                    </tr>
                    
                    <!-- Row 2 -->
                    <tr>
                      <td>
                        <div class="td-user">
                          <img src="https://placehold.co/32x32/e2e8f0/64748b" alt="Avatar">
                          <span class="font-roboto">Mark Bautista</span>
                        </div>
                      </td>
                      <td class="td-sub">mark.bautista@gmail.com</td>
                      <td class="td-sub">NCR Chapter</td>
                      <td style="text-align: center;"><span class="badge chapter-admin font-roboto">Chapter Admin</span></td>
                      <td class="td-sub">Oct 13, 2026<br>09:15 AM</td>
                      <td style="text-align: center;"><span class="badge pending font-roboto">Pending</span></td>
                      <td style="text-align: center;">
                        <button class="btn btn-primary btn-sm font-roboto" style="margin: 0 auto;">Review</button>
                      </td>
                    </tr>

                    <!-- Row 3 -->
                    <tr>
                      <td>
                        <div class="td-user">
                          <img src="https://placehold.co/32x32/e2e8f0/64748b" alt="Avatar">
                          <span class="font-roboto">Elena Reyes</span>
                        </div>
                      </td>
                      <td class="td-sub">elena.reyes@gmail.com</td>
                      <td class="td-sub">Cavite Chapter</td>
                      <td style="text-align: center;"><span class="badge national-admin font-roboto">National Admin</span></td>
                      <td class="td-sub">Oct 12, 2026<br>04:45 PM</td>
                      <td style="text-align: center;"><span class="badge pending font-roboto">Pending</span></td>
                      <td style="text-align: center;">
                        <button class="btn btn-primary btn-sm font-roboto" style="margin: 0 auto;">Review</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- RIGHT COLUMN (Review Panel) -->
          <div class="layout-sidebar">
            <h2 class="review-panel-title font-roboto">Review</h2>

            <!-- Card 1: Account Access Request -->
            <div class="review-card">
              <div class="review-header">
                <div class="review-header-icon"><i class="ph-fill ph-user"></i></div>
                <div class="review-header-text">
                  <h4 class="font-roboto">Account Access Request</h4>
                  <p>Review the details and approve or reject this account request.</p>
                </div>
              </div>

              <div class="review-user-info">
                <div class="review-user-profile">
                  <img src="https://placehold.co/40x40/e2e8f0/64748b" alt="User Avatar">
                  <div>
                    <h5 class="font-roboto">Joan Dela Cruz</h5>
                    <p>joan.dela@gmail.com</p>
                  </div>
                </div>
                <span class="badge pending font-roboto">Pending</span>
              </div>

              <div class="review-details">
                <div class="review-detail-group">
                  <label class="font-roboto"><i class="ph-fill ph-flag"></i> Chapter</label>
                  <p class="font-roboto">Laguna Chapter</p>
                </div>
                <div class="review-detail-group">
                  <label class="font-roboto"><i class="ph-fill ph-user-circle"></i> Requested Role</label>
                  <p class="font-roboto">Chapter Admin</p>
                </div>
                <div class="review-detail-group">
                  <label class="font-roboto"><i class="ph-fill ph-file-text"></i> Reason for Request</label>
                  <div class="reason-box font-roboto">
                    I am newly appointed at YTR Laguna Chapter and I would like to help in managing our chapter events and activities. This will also help me stay updated with the latest announcements and coordinate with the national.
                  </div>
                </div>
              </div>
            </div>

            <!-- Card 2: Access & Role -->
            <div class="review-card">
              <div class="review-header">
                <div class="review-header-icon"><i class="ph-fill ph-shield-check"></i></div>
                <div class="review-header-text">
                  <h4 class="font-roboto">Access & Role</h4>
                  <p>Manage user permissions, security clearance level, and account access controls.</p>
                </div>
              </div>

              <div class="form-group">
                <label class="form-label font-roboto">Assign Role</label>
                <div class="radio-group font-roboto">
                  <label class="radio-label">
                    <input type="radio" name="role" value="national"> National Admin
                  </label>
                  <label class="radio-label">
                    <input type="radio" name="role" value="chapter" checked> Chapter Admin
                  </label>
                </div>
              </div>

              <div class="form-group">
                <label class="form-label font-roboto">Assign Chapter</label>
                <select class="form-select">
                  <option value="laguna">Laguna Chapter</option>
                  <option value="ncr">NCR Chapter</option>
                  <option value="cavite">Cavite Chapter</option>
                </select>
              </div>

              <div class="button-group">
                <button class="btn btn-outline-danger font-roboto">
                  <i class="ph ph-x"></i> Decline
                </button>
                <button class="btn btn-primary font-roboto">
                  <i class="ph ph-check"></i> Approve
                </button>
              </div>
            </div>

          </div>
        </div>
      </main>
    </div>
  </div>
</body>
</html>