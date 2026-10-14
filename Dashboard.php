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
  <script src="assets/api-client.js"></script>
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
        
        <div class="page-header flex-between" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
          <div>
            <h1 class="font-roboto">Chapter Overview</h1>
            <p style="color: var(--text-muted); margin-top: 0.25rem;">Monitor real-time chapter metrics, compliance status, and active reporting streams across all branches.</p>
          </div>
          <div style="display: flex; gap: 0.5rem;">
            <a href="Proposal.php" class="btn btn-primary" style="text-decoration: none;">
              <i class="ph ph-calendar-plus"></i> View Proposals
            </a>
            <a href="Report.php" class="btn btn-outline" style="text-decoration: none;">
              <i class="ph ph-file-text"></i> View Reports
            </a>
          </div>
        </div>

        <!-- Pending Review Visual Banner -->
        <div id="dash-alert-banner" class="pending-alert-banner" style="display: none;">
          <div style="display: flex; align-items: center; gap: 0.85rem;">
            <i class="ph-fill ph-bell-ringing" style="font-size: 1.6rem; color: #b45309;"></i>
            <div>
              <strong style="color: #92400e; font-size: 0.95rem;">Action Items Pending</strong>
              <p style="margin: 0; color: #b45309; font-size: 0.85rem;" id="dash-alert-text">You have pending reports awaiting submission or review.</p>
            </div>
          </div>
          <a href="Report.php" class="btn btn-primary" style="background: #d97706; border-color: #b45309; padding: 0.4rem 0.85rem; font-size: 0.825rem; text-decoration: none;">
            Resolve Reports
          </a>
        </div>

        <!-- Four Responsive Metric Cards -->
        <div class="metrics-grid">
          <div class="metric-card">
            <div class="metric-icon amber"><i class="ph-fill ph-file-text"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Pending Chapters Report</h5>
              <p class="metric-value font-roboto" id="metric-pending-reports">0</p>
              <p class="metric-sub">Events awaiting post-activity reports</p>
            </div>
          </div>
          
          <div class="metric-card">
            <div class="metric-icon green"><i class="ph-fill ph-chart-line-up"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Total Reports Submitted</h5>
              <p class="metric-value font-roboto" id="metric-total-reports">0</p>
              <p class="metric-sub">Accomplished official event dossiers</p>
            </div>
          </div>
          
          <div class="metric-card">
            <div class="metric-icon blue"><i class="ph-fill ph-users"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Partner Chapters</h5>
              <p class="metric-value font-roboto" id="metric-partner-chapters">0</p>
              <p class="metric-sub">Active community & collegiate branches</p>
            </div>
          </div>
          
          <div class="metric-card">
            <div class="metric-icon rose"><i class="ph-fill ph-warning-circle"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Chapters Due Reports</h5>
              <p class="metric-value font-roboto" id="metric-due-reports">0</p>
              <p class="metric-sub">Reports past scheduled deadline</p>
            </div>
          </div>
        </div>

        <!-- Responsive Table Section -->
        <div class="table-section">
          <div class="table-header">
            <h2 class="font-roboto" style="color: var(--primary); font-size: 1.25rem; font-weight: bold; margin: 0;">Partner Chapters Performance</h2>
            <div class="search-bar" style="margin: 0; min-width: 250px;">
              <i class="ph ph-magnifying-glass"></i>
              <input type="text" id="chapter-search" class="font-roboto" placeholder="Search Chapter or Region..." oninput="filterChapterTable()">
            </div>
          </div>

          <!-- Tabs -->
          <div class="tabs-container font-roboto">
            <button class="tab-btn active" id="tab-all" onclick="setChapterTab('all', this)">
              <i class="ph ph-buildings"></i> All Chapters (<span id="count-all-chapters">0</span>)
            </button>
            <button class="tab-btn" id="tab-active" onclick="setChapterTab('active', this)">
              <i class="ph ph-check-circle"></i> Active Chapters
            </button>
            <button class="tab-btn" id="tab-pending" onclick="setChapterTab('pending', this)">
              <i class="ph ph-warning-circle"></i> Pending Reports (<span id="count-pending-chapters">0</span>)
            </button>
          </div>
          
          <div class="table-responsive">
            <table>
              <thead class="font-roboto">
                <tr>
                  <th>Chapter Name</th>
                  <th>Region / Code</th>
                  <th>Conducted Winning Events</th>
                  <th>Reports Submitted</th>
                  <th>Non-Provided (Pending)</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody id="chapter-table-body">
                <tr>
                  <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                    <i class="ph ph-spinner ph-spin" style="font-size: 1.5rem;"></i> Loading chapter metrics from Python API...
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

  <script>
    let chaptersData = [];
    let currentFilter = 'all';

    document.addEventListener('DOMContentLoaded', () => {
      loadDashboardOverview();
    });

    async function loadDashboardOverview() {
      try {
        const res = await YTR_API.dashboard.getOverview();
        if (res.ok && res.data) {
          const { metrics, chapters } = res.data;
          
          // Populate metrics
          document.getElementById('metric-pending-reports').textContent = metrics.pending_chapters_report || 0;
          document.getElementById('metric-total-reports').textContent = metrics.total_reports_submitted || 0;
          document.getElementById('metric-partner-chapters').textContent = metrics.partner_chapters_count || 0;
          document.getElementById('metric-due-reports').textContent = metrics.chapters_due_reports || 0;

          // Alert banner
          const totalAlerts = (metrics.pending_chapters_report || 0) + (metrics.chapters_due_reports || 0);
          if (totalAlerts > 0) {
            document.getElementById('dash-alert-banner').style.display = 'flex';
            document.getElementById('dash-alert-text').textContent = 
              `Notice: ${metrics.pending_chapters_report} post-event report(s) pending, including ${metrics.chapters_due_reports} past due date.`;
          } else {
            document.getElementById('dash-alert-banner').style.display = 'none';
          }

          chaptersData = chapters || [];
          document.getElementById('count-all-chapters').textContent = chaptersData.length;
          
          const pendingCount = chaptersData.filter(c => c.pending_reports > 0).length;
          document.getElementById('count-pending-chapters').textContent = pendingCount;

          renderChapterTable();
        }
      } catch (err) {
        console.error('Failed to load dashboard overview:', err);
      }
    }

    function setChapterTab(filter, btn) {
      currentFilter = filter;
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      if (btn) btn.classList.add('active');
      renderChapterTable();
    }

    function filterChapterTable() {
      renderChapterTable();
    }

    function renderChapterTable() {
      const tbody = document.getElementById('chapter-table-body');
      const search = (document.getElementById('chapter-search').value || '').toLowerCase().trim();

      let filtered = chaptersData.filter(c => {
        if (currentFilter === 'active' && c.status !== 'active') return false;
        if (currentFilter === 'pending' && c.pending_reports <= 0) return false;

        if (search) {
          const matchName = (c.chapter_name || '').toLowerCase().includes(search);
          const matchCode = (c.chapter_code || '').toLowerCase().includes(search);
          const matchRegion = (c.region || '').toLowerCase().includes(search);
          return matchName || matchCode || matchRegion;
        }
        return true;
      });

      if (filtered.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">
              <i class="ph ph-buildings" style="font-size: 2rem; color: #94a3b8;"></i>
              <p style="margin-top: 0.5rem;">No chapters matching the selected filter criteria.</p>
            </td>
          </tr>
        `;
        return;
      }

      tbody.innerHTML = filtered.map(c => {
        const pendingBadge = c.pending_reports > 0
          ? `<span class="badge overdue"><i class="ph ph-warning"></i> ${c.pending_reports} Missing</span>`
          : `<span class="badge threshold-met"><i class="ph ph-check"></i> Up to Date</span>`;

        return `
          <tr>
            <td>
              <div style="font-weight: 700; color: #0f172a;">${escapeHtml(c.chapter_name)}</div>
            </td>
            <td>
              <span style="font-size: 0.85rem; color: #475569;">${escapeHtml(c.region || 'Region')}</span>
              <span class="badge" style="margin-left: 0.35rem; font-size: 0.75rem;">${escapeHtml(c.chapter_code)}</span>
            </td>
            <td style="font-weight: 600; color: #1e293b;">${c.conducted_events}</td>
            <td style="font-weight: 600; color: #16a34a;">${c.reports_submitted}</td>
            <td>${pendingBadge}</td>
            <td>
              <span class="status-badge ${c.status === 'active' ? 'green' : 'amber'}">
                <i class="ph ${c.status === 'active' ? 'ph-check-circle' : 'ph-clock'}"></i>
                ${c.status ? c.status.toUpperCase() : 'ACTIVE'}
              </span>
            </td>
          </tr>
        `;
      }).join('');
    }

    function escapeHtml(str) {
      if (!str) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }
  </script>
</body>
</html>