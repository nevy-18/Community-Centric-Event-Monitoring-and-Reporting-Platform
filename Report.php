<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>YTR | Chapter Reports</title>
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
            <div class="header-icon"><i class="ph-fill ph-file-text"></i></div>
            <div>
              <h1 class="font-roboto">Chapter Reports</h1>
              <p>Track and review monthly event reports, financial health, and overall chapter ratings.</p>
            </div>
          </div>
        </div>

        <!-- 2-Column Responsive Layout -->
        <div class="two-column-layout">
          
          <!-- LEFT COLUMN (Metrics & Table) -->
          <div class="layout-main">
            
            <!-- Metrics (3 Columns) -->
            <div class="metrics-grid">
              <div class="metric-card metric-card-row">
                <div class="metric-icon blue"><i class="ph-fill ph-files"></i></div>
                <div>
                  <h5 class="metric-title font-roboto">Total Active Events</h5>
                  <p class="metric-value font-roboto">24</p>
                  <p class="metric-sub font-roboto">Across all active chapters</p>
                </div>
              </div>
              <div class="metric-card metric-card-row">
                <div class="metric-icon green"><i class="ph-fill ph-check-circle"></i></div>
                <div>
                  <h5 class="metric-title font-roboto">Completed Events</h5>
                  <p class="metric-value font-roboto">24</p>
                  <p class="metric-sub font-roboto">Reports successfully submitted</p>
                </div>
              </div>
              <div class="metric-card metric-card-row">
                <div class="metric-icon purple"><i class="ph-fill ph-users"></i></div>
                <div>
                  <h5 class="metric-title font-roboto">First Timers</h5>
                  <p class="metric-value font-roboto">90</p>
                  <p class="metric-sub font-roboto">Newly joined attendees</p>
                </div>
              </div>
            </div>

            <!-- Table Section -->
            <div class="table-section">
              <div class="table-header">
                <div style="display: flex; align-items: center; gap: 1rem;">
                  <h2 class="font-roboto" style="color: var(--primary); font-size: 1.15rem; font-weight: bold;">Chapter Events</h2>
                </div>
                <div class="search-bar">
                  <i class="ph ph-magnifying-glass"></i>
                  <input type="text" id="reportSearch" class="font-roboto" placeholder="Search event or chapter...">
                </div>
              </div>

              <!-- Tabs -->
              <div class="tabs-container font-roboto">
                <button class="tab-btn active"><i class="ph ph-calendar"></i> All Events</button>
                <button class="tab-btn"><i class="ph ph-check-circle"></i> Completed Reports</button>
                <button class="tab-btn"><i class="ph ph-clock"></i> Pending Submission</button>
              </div>

              <div class="table-responsive">
                <table>
                  <thead class="font-roboto">
                    <tr>
                      <th>Event Chapter</th>
                      <th>Date & Venue</th>
                      <th style="text-align: center;">Participants</th>
                      <th style="text-align: center;">First Timers</th>
                      <th style="text-align: center;">Members</th>
                      <th style="text-align: center;">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                          <img src="https://placehold.co/44x58/1e3a8a/ffffff?text=G.O.A.T" alt="GOAT Banner" style="width: 44px; height: 58px; border-radius: 6px; object-fit: cover;">
                          <div>
                            <p class="font-roboto" style="font-weight: bold; color: var(--primary); font-size: 0.875rem;">Greatest of All Time</p>
                            <span class="badge chapter-admin font-roboto" style="margin-top: 3px;">Laguna Chapter</span>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="font-roboto" style="font-weight: bold; font-size: 0.8rem; color: var(--text-main);">Sept 28, 2026</p>
                        <p style="font-size: 0.7rem; color: var(--text-muted);"><i class="ph ph-map-pin"></i> SJB Learning Center</p>
                      </td>
                      <td style="text-align: center; font-weight: bold; color: var(--text-main);"><i class="ph-fill ph-users" style="color: #60a5fa;"></i> 120</td>
                      <td style="text-align: center; font-weight: bold; color: #16a34a;"><i class="ph-fill ph-user-plus" style="color: #22c55e;"></i> 45</td>
                      <td style="text-align: center; font-weight: bold; color: #2563eb;"><i class="ph-fill ph-user" style="color: #3b82f6;"></i> 75</td>
                      <td style="text-align: center;">
                        <button class="btn btn-primary btn-sm font-roboto" onclick="showEventDetails('Greatest of All Time', 'Laguna Chapter', 'Sept 28, 2026', 'SJB Learning Center, Calamba', 120, 45, 75, 'Youth fellowship and leadership orientation with worship service.')" style="margin: 0 auto;">
                          <i class="ph ph-eye"></i> View
                        </button>
                      </td>
                    </tr>

                    <tr>
                      <td>
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                          <img src="https://placehold.co/44x58/047857/ffffff?text=SUMMIT" alt="Summit Banner" style="width: 44px; height: 58px; border-radius: 6px; object-fit: cover;">
                          <div>
                            <p class="font-roboto" style="font-weight: bold; color: var(--primary); font-size: 0.875rem;">Youth Leadership Summit</p>
                            <span class="badge chapter-admin font-roboto" style="margin-top: 3px;">NCR Chapter</span>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="font-roboto" style="font-weight: bold; font-size: 0.8rem; color: var(--text-main);">Oct 05, 2026</p>
                        <p style="font-size: 0.7rem; color: var(--text-muted);"><i class="ph ph-map-pin"></i> QC Assembly Hall</p>
                      </td>
                      <td style="text-align: center; font-weight: bold; color: var(--text-main);"><i class="ph-fill ph-users" style="color: #60a5fa;"></i> 160</td>
                      <td style="text-align: center; font-weight: bold; color: #16a34a;"><i class="ph-fill ph-user-plus" style="color: #22c55e;"></i> 60</td>
                      <td style="text-align: center; font-weight: bold; color: #2563eb;"><i class="ph-fill ph-user" style="color: #3b82f6;"></i> 100</td>
                      <td style="text-align: center;">
                        <button class="btn btn-primary btn-sm font-roboto" onclick="showEventDetails('Youth Leadership Summit', 'NCR Chapter', 'Oct 05, 2026', 'QC Assembly Hall, Quezon City', 160, 60, 100, 'Metro youth convergence and capacity development session.')" style="margin: 0 auto;">
                          <i class="ph ph-eye"></i> View
                        </button>
                      </td>
                    </tr>

                    <tr>
                      <td>
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                          <img src="https://placehold.co/44x58/b45309/ffffff?text=CAMP" alt="Camp Banner" style="width: 44px; height: 58px; border-radius: 6px; object-fit: cover;">
                          <div>
                            <p class="font-roboto" style="font-weight: bold; color: var(--primary); font-size: 0.875rem;">Tremendous Momentum Camp</p>
                            <span class="badge chapter-admin font-roboto" style="margin-top: 3px;">Cavite Chapter</span>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="font-roboto" style="font-weight: bold; font-size: 0.8rem; color: var(--text-main);">Oct 12, 2026</p>
                        <p style="font-size: 0.7rem; color: var(--text-muted);"><i class="ph ph-map-pin"></i> Tagaytay Retreat Park</p>
                      </td>
                      <td style="text-align: center; font-weight: bold; color: var(--text-main);"><i class="ph-fill ph-users" style="color: #60a5fa;"></i> 95</td>
                      <td style="text-align: center; font-weight: bold; color: #16a34a;"><i class="ph-fill ph-user-plus" style="color: #22c55e;"></i> 30</td>
                      <td style="text-align: center; font-weight: bold; color: #2563eb;"><i class="ph-fill ph-user" style="color: #3b82f6;"></i> 65</td>
                      <td style="text-align: center;">
                        <button class="btn btn-primary btn-sm font-roboto" onclick="showEventDetails('Tremendous Momentum Camp', 'Cavite Chapter', 'Oct 12, 2026', 'Tagaytay Retreat Park, Cavite', 95, 30, 65, 'Three-day spiritual retreat and community building camp.')" style="margin: 0 auto;">
                          <i class="ph ph-eye"></i> View
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- RIGHT COLUMN (Preview Panel) -->
          <div class="layout-sidebar">
            <h2 class="review-panel-title font-roboto">Report Overview</h2>

            <!-- Detail Panel Content -->
            <div id="reportDetailCard" class="review-card">
              <div id="emptyReportState" style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
                <div style="width: 52px; height: 52px; border-radius: 50%; background: #eff6ff; color: #2563eb; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 0.85rem;">
                  <i class="ph ph-clipboard-text"></i>
                </div>
                <h4 class="font-roboto" style="color: var(--primary); font-size: 0.95rem; font-weight: bold; margin-bottom: 0.35rem;">No Event Selected</h4>
                <p style="font-size: 0.75rem; line-height: 1.4;">Click <strong>View</strong> on any chapter report row to preview attendee turnout and details.</p>
              </div>

              <div id="activeReportContent" style="display: none;">
                <div class="review-header">
                  <div class="review-header-icon"><i class="ph-fill ph-file-text"></i></div>
                  <div class="review-header-text">
                    <h4 id="detailEventTitle" class="font-roboto">Event Name</h4>
                    <p id="detailChapterName">Chapter Name</p>
                  </div>
                </div>

                <div class="review-details">
                  <div class="review-detail-group">
                    <label class="font-roboto"><i class="ph-fill ph-calendar-blank"></i> Date & Venue</label>
                    <p id="detailDateVenue" class="font-roboto" style="font-size: 0.75rem;">-</p>
                  </div>

                  <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem; background: #f8fafc; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border); margin: 0.5rem 0;">
                    <div style="text-align: center;">
                      <span style="font-size: 0.65rem; color: var(--text-muted); display: block;">Total</span>
                      <strong id="detailTotal" style="color: var(--primary); font-size: 1.1rem;">0</strong>
                    </div>
                    <div style="text-align: center;">
                      <span style="font-size: 0.65rem; color: #16a34a; display: block;">1st Timers</span>
                      <strong id="detailFirstTimers" style="color: #16a34a; font-size: 1.1rem;">0</strong>
                    </div>
                    <div style="text-align: center;">
                      <span style="font-size: 0.65rem; color: #2563eb; display: block;">Members</span>
                      <strong id="detailMembers" style="color: #2563eb; font-size: 1.1rem;">0</strong>
                    </div>
                  </div>

                  <div class="review-detail-group">
                    <label class="font-roboto"><i class="ph-fill ph-article"></i> Summary Notes</label>
                    <div id="detailNotes" class="reason-box font-roboto" style="font-size: 0.75rem;">
                      -
                    </div>
                  </div>

                  <div style="margin-top: 1rem;">
                    <button class="btn btn-primary font-roboto" style="width: 100%;" onclick="alert('Full report PDF download will be generated from database records.');">
                      <i class="ph ph-file-pdf"></i> Download Full Report
                    </button>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

      </main>
    </div>
  </div>

  <script>
    function showEventDetails(title, chapter, date, venue, total, firstTimers, members, notes) {
      document.getElementById('emptyReportState').style.display = 'none';
      document.getElementById('activeReportContent').style.display = 'block';

      document.getElementById('detailEventTitle').textContent = title;
      document.getElementById('detailChapterName').textContent = chapter;
      document.getElementById('detailDateVenue').textContent = date + ' • ' + venue;
      document.getElementById('detailTotal').textContent = total;
      document.getElementById('detailFirstTimers').textContent = firstTimers;
      document.getElementById('detailMembers').textContent = members;
      document.getElementById('detailNotes').textContent = notes;
    }
  </script>
</body>
</html>