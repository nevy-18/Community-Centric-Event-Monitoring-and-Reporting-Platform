<?php include 'includes/auth_guard.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chapter Workspace | Youth on the Rock</title>
  
  <!-- Phosphor Icons -->
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  
  <!-- Centralized CSS -->
  <link rel="stylesheet" href="assets/Chapter.css?v=<?= time() ?>">
  <style>
    /* Table Simplification & Strict Pixel-Perfect Alignment */
    .table-responsive {
      overflow-x: auto !important;
      -webkit-overflow-scrolling: touch !important;
      width: 100% !important;
    }
    #chapterEventsTable {
      width: 100% !important;
      border-collapse: collapse !important;
      table-layout: fixed !important;
      min-width: 820px !important;
    }
    #chapterEventsTable th,
    #chapterEventsTable td {
      white-space: nowrap !important;
      vertical-align: middle !important;
      padding: 0.9rem 1rem !important;
      box-sizing: border-box !important;
    }
    #chapterEventsTable th:nth-child(1), #chapterEventsTable td:nth-child(1) { width: 42%; text-align: left; }
    #chapterEventsTable th:nth-child(2), #chapterEventsTable td:nth-child(2) { width: 15%; text-align: center; }
    #chapterEventsTable th:nth-child(3), #chapterEventsTable td:nth-child(3) { width: 14%; text-align: center; }
    #chapterEventsTable th:nth-child(4), #chapterEventsTable td:nth-child(4) { width: 14%; text-align: center; }
    #chapterEventsTable th:nth-child(5), #chapterEventsTable td:nth-child(5) { width: 15%; text-align: right; }

    /* Uniform Status Badges */
    .status-pill-fixed {
      width: 124px !important;
      min-width: 124px !important;
      max-width: 124px !important;
      height: 28px !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 0.35rem !important;
      border-radius: 9999px !important;
      font-size: 0.74rem !important;
      font-weight: 700 !important;
      box-sizing: border-box !important;
      white-space: nowrap !important;
      margin: 0 auto !important;
      text-transform: none !important;
      letter-spacing: 0.01em !important;
    }
    .status-pill-fixed.pending {
      background: #fffbeb !important;
      color: #b45309 !important;
      border: 1px solid #fde68a !important;
    }
    .status-pill-fixed.approved {
      background: #f0fdf4 !important;
      color: #15803d !important;
      border: 1px solid #bbf7d0 !important;
    }
    .status-pill-fixed.revision {
      background: #fff1f2 !important;
      color: #e11d48 !important;
      border: 1px solid #fecdd3 !important;
    }
    .status-pill-fixed.neutral {
      background: #f1f5f9 !important;
      color: #475569 !important;
      border: 1px solid #cbd5e1 !important;
    }

    /* Uniform Action Buttons */
    .action-btn-uniform {
      width: 80px !important;
      min-width: 80px !important;
      max-width: 80px !important;
      height: 32px !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 0.35rem !important;
      font-size: 0.8rem !important;
      font-weight: 600 !important;
      border-radius: 6px !important;
      box-sizing: border-box !important;
      white-space: nowrap !important;
      cursor: pointer !important;
      text-decoration: none !important;
      transition: all 0.15s ease !important;
      padding: 0 !important;
    }
    .action-btn-uniform.btn-details {
      border: 1px solid #cbd5e1 !important;
      background: #ffffff !important;
      color: #334155 !important;
    }
    .action-btn-uniform.btn-details:hover {
      background: #f8fafc !important;
      border-color: #94a3b8 !important;
      color: #0f172a !important;
    }
    .action-btn-uniform.btn-review {
      background: #2563eb !important;
      color: #ffffff !important;
      border: 1px solid #2563eb !important;
    }
    .action-btn-uniform.btn-review:hover {
      background: #1d4ed8 !important;
      border-color: #1d4ed8 !important;
    }
  </style>
  <script src="assets/api-client.js"></script>
</head>
<body>

  <div class="app-wrapper">
    <!-- INJECT DEDICATED CHAPTER SIDEBAR -->
    <?php include 'includes/sidebar_chapter.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-wrapper">
      <!-- INJECT TOPBAR -->
      <?php include 'includes/topbar.php'; ?>

      <main class="page-content">
        
        <!-- Header -->
        <div class="page-header flex-between" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
          <div>
            <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.4rem;">
              <h1 class="font-roboto" id="chapter-portal-title" style="margin: 0; color: var(--text-main, #0f172a); font-weight: 800;">Partner Chapter Dashboard</h1>
              <span class="portal-indicator chapter"><i class="ph-fill ph-buildings"></i> Chapter Branch Portal</span>
            </div>
            <p style="color: var(--text-muted); margin: 0;">Dedicated chapter workspace for drafting proposals, requesting promotional media, and submitting post-event reports.</p>
          </div>
          
          <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
            <div id="chapter-selector-wrapper" style="display: flex; align-items: center; gap: 0.35rem; background: #ffffff; padding: 0.3rem 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px;">
              <label for="chapter-switch-select" style="font-size: 0.8rem; font-weight: 600; color: #64748b;">Active Chapter:</label>
              <select id="chapter-switch-select" class="chapter-pill-select" onchange="onChapterChange(this.value)" style="border: none; outline: none; background: transparent; padding: 0.2rem 0.4rem; font-size: 0.85rem; font-weight: 700; color: #1e3a8a; cursor: pointer;">
                <option value="1">Laguna Chapter</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Pending Reporting Alert Banner -->
        <div id="chapter-alert-banner" class="pending-alert-banner" style="display: none;">
          <div style="display: flex; align-items: center; gap: 0.85rem;">
            <i class="ph-fill ph-bell-ringing" style="font-size: 1.6rem; color: #b45309;"></i>
            <div>
              <strong style="color: #92400e; font-size: 0.95rem;">Post-Event Report Due For Your Chapter</strong>
              <p style="margin: 0; color: #b45309; font-size: 0.85rem;" id="chapter-alert-text">You have completed events awaiting final attendance turnout submission and e-signature.</p>
            </div>
          </div>
        </div>

        <!-- Metric Cards Grid (Key Metrics & Pending Monitoring Status Cards) -->
        <div class="metrics-grid">
          <div class="metric-card" onclick="setDashboardTab('all')" style="cursor: pointer;">
            <div class="metric-icon blue"><i class="ph-fill ph-calendar-check"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Active Chapter Events</h5>
              <p class="metric-value font-roboto" id="metric-active-events">0</p>
              <p class="metric-sub">Events currently scheduled or ongoing</p>
            </div>
          </div>
          
          <div class="metric-card" onclick="setDashboardTab('proposals')" style="cursor: pointer;">
            <div class="metric-icon amber"><i class="ph-fill ph-clock"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Pending Proposals</h5>
              <p class="metric-value font-roboto" id="metric-pending-proposals">0</p>
              <p class="metric-sub">Awaiting National Admin review</p>
            </div>
          </div>
          
          <div class="metric-card" onclick="setDashboardTab('posters')" style="cursor: pointer;">
            <div class="metric-icon purple"><i class="ph-fill ph-image"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Poster Requests</h5>
              <p class="metric-value font-roboto" id="metric-posters">0</p>
              <p class="metric-sub">Media requests in production pipeline</p>
            </div>
          </div>
          
          <div class="metric-card" onclick="setDashboardTab('due')" style="cursor: pointer;">
            <div class="metric-icon red" style="background: #fef2f2; color: #dc2626;"><i class="ph-fill ph-warning-circle"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Reports Due / Overdue</h5>
              <p class="metric-value font-roboto" id="metric-pending-reports">0</p>
              <p class="metric-sub">Post-event reports requiring submission</p>
            </div>
          </div>
        </div>

        <!-- Centralized Activity & Pending Monitoring Table Section -->
        <div class="table-section">
          <div class="table-header" style="flex-direction: column; align-items: stretch; gap: 1rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
              <div>
                <h2 class="font-roboto" style="color: var(--primary); font-size: 1.25rem; font-weight: bold; margin: 0;">Centralized Activity & Pending Monitoring</h2>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0.25rem 0 0;">Monitor proposals, poster pipelines, attendance thresholds, and post-event deadlines.</p>
              </div>
              <div class="search-bar" style="margin: 0; width: 100%; max-width: 100%;">
                <i class="ph ph-magnifying-glass"></i>
                <input type="text" id="chapter-events-search" class="font-roboto" placeholder="Search event or topic..." oninput="renderMonitoringTable()" style="width: 100%; flex: 1; min-width: 0;">
              </div>
            </div>

            <!-- Interactive Filter Tabs -->
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;" role="tablist">
              <button type="button" class="dash-tab active" id="tab-btn-all" onclick="setDashboardTab('all')">
                All Events <span class="badge" id="tab-cnt-all" style="background: #e2e8f0; color: #334155;">0</span>
              </button>
              <button type="button" class="dash-tab" id="tab-btn-proposals" onclick="setDashboardTab('proposals')">
                Pending Proposals <span class="badge pending" id="tab-cnt-proposals">0</span>
              </button>
              <button type="button" class="dash-tab" id="tab-btn-posters" onclick="setDashboardTab('posters')">
                Poster Requests <span class="badge" id="tab-cnt-posters" style="background: #f3e8ff; color: #7e22ce;">0</span>
              </button>
              <button type="button" class="dash-tab" id="tab-btn-due" onclick="setDashboardTab('due')">
                Reports Due / Overdue <span class="badge overdue" id="tab-cnt-due">0</span>
              </button>
              <button type="button" class="dash-tab" id="tab-btn-submitted" onclick="setDashboardTab('submitted')">
                Submitted Reports <span class="badge approved" id="tab-cnt-submitted">0</span>
              </button>
            </div>
          </div>
          
          <!-- Events / Tasks Table -->
          <div class="table-responsive" id="table-container-events">
            <table id="chapterEventsTable">
              <thead class="font-roboto">
                <tr>
                  <th style="width: 42%; text-align: left;">Event Title</th>
                  <th style="width: 15%; text-align: center;">Schedule</th>
                  <th style="width: 14%; text-align: center;">Status</th>
                  <th style="width: 14%; text-align: center;">Turnout</th>
                  <th style="width: 15%; text-align: right;">Action</th>
                </tr>
              </thead>
              <tbody id="chapter-events-body">
                <tr>
                  <td colspan="5" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                    <i class="ph ph-spinner ph-spin" style="font-size: 1.5rem;"></i> Loading chapter events...
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Poster Requests Table (Displayed when Posters Tab is Active) -->
          <div class="table-responsive" id="table-container-posters" style="display: none;">
            <table>
              <thead class="font-roboto">
                <tr>
                  <th>Event Title</th>
                  <th>Schedule & Deadline</th>
                  <th>Milestone Stage</th>
                  <th>Status</th>
                  <th>Poster</th>
                  <th style="text-align: right;">Action</th>
                </tr>
              </thead>
              <tbody id="chapter-posters-body">
                <tr>
                  <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                    <i class="ph ph-spinner ph-spin" style="font-size: 1.5rem;"></i> Loading poster requests...
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- Chapter Event Details Pop-up Modal -->
  <div id="chapterEventDetailsModal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); z-index: 1050; align-items: center; justify-content: center; padding: 1rem;" onclick="if(event.target === this) closeChapterEventDetailsModal();">
    <div class="modal-md" style="max-width: 620px; width: 100%; background: #ffffff; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 20px 35px -10px rgba(15, 23, 42, 0.35); border: 1px solid #e2e8f0; max-height: 90vh;">
      <!-- Header -->
      <div style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); color: #ffffff; padding: 1.15rem 1.4rem; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
          <div style="background: rgba(255, 255, 255, 0.15); width: 38px; height: 38px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
            <i class="ph-bold ph-info"></i>
          </div>
          <div>
            <h3 class="font-roboto" style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #ffffff;">Event Specifications</h3>
            <p style="margin: 2px 0 0; font-size: 0.75rem; color: #bfdbfe;">Detailed event parameters, attendance turnout, and reporting status</p>
          </div>
        </div>
        <button type="button" onclick="closeChapterEventDetailsModal()" style="background: none; border: none; color: #ffffff; font-size: 1.35rem; cursor: pointer; padding: 4px; display: flex; align-items: center; justify-content: center; border-radius: 6px; opacity: 0.85; transition: opacity 0.15s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.85'">&times;</button>
      </div>

      <!-- Scrollable Body -->
      <div style="padding: 1.25rem 1.4rem; overflow-y: auto; display: flex; flex-direction: column; gap: 1.15rem;">
        <!-- Hero -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem 1.15rem;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.75rem; flex-wrap: wrap;">
            <div>
              <span id="ch-modal-program-badge" class="badge" style="margin-bottom: 0.35rem; display: inline-block;"></span>
              <h4 id="ch-modal-title" style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #0f172a;"></h4>
            </div>
            <div id="ch-modal-status-badge"></div>
          </div>
        </div>

        <!-- Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <!-- Left: Specifications -->
          <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem 1rem;">
            <h5 style="margin: 0 0 0.65rem; font-size: 0.75rem; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; font-weight: 700; display: flex; align-items: center; gap: 0.35rem;">
              <i class="ph-bold ph-calendar"></i> Schedule & Logistics
            </h5>
            <div style="font-size: 0.85rem; display: flex; flex-direction: column; gap: 0.45rem;">
              <div>
                <span style="color: #64748b; font-size: 0.75rem; display: block;">Event Type:</span>
                <strong id="ch-modal-type" style="color: #0f172a;"></strong>
              </div>
              <div>
                <span style="color: #64748b; font-size: 0.75rem; display: block;">Official Venue:</span>
                <span id="ch-modal-venue" style="color: #1e293b;"></span>
              </div>
              <div>
                <span style="color: #64748b; font-size: 0.75rem; display: block;">Date & Time:</span>
                <span id="ch-modal-schedule" style="color: #1e293b; font-weight: 500;"></span>
              </div>
              <div>
                <span style="color: #64748b; font-size: 0.75rem; display: block;">Target Audience:</span>
                <span id="ch-modal-target-audience" style="color: #1e293b;"></span>
              </div>
            </div>
          </div>

          <!-- Right: Turnout Progress -->
          <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem 1rem;">
            <h5 style="margin: 0 0 0.65rem; font-size: 0.75rem; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; font-weight: 700; display: flex; align-items: center; gap: 0.35rem;">
              <i class="ph-bold ph-users-three"></i> Attendance Turnout
            </h5>
            <div style="font-size: 0.85rem; display: flex; flex-direction: column; gap: 0.5rem;">
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #64748b; font-size: 0.8rem;">Goal vs Actual:</span>
                <strong id="ch-modal-turnout-text" style="color: #0f172a;"></strong>
              </div>
              <div class="threshold-progress-wrap" style="height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden; margin-top: 4px;">
                <div id="ch-modal-progress-bar" style="height: 100%; transition: width 0.3s ease;"></div>
              </div>
              <div id="ch-modal-breakdown" style="font-size: 0.75rem; color: #64748b; padding-top: 0.35rem;"></div>
            </div>
          </div>
        </div>

        <!-- Compliance & Reporting Card -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem 1rem;">
          <h5 style="margin: 0 0 0.65rem; font-size: 0.75rem; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; font-weight: 700; display: flex; align-items: center; gap: 0.35rem;">
            <i class="ph-bold ph-file-text"></i> Post-Event Report Status
          </h5>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 0.75rem; font-size: 0.85rem;">
            <div>
              <span style="color: #64748b; font-size: 0.75rem; display: block;">Report Status:</span>
              <span id="ch-modal-report-status"></span>
            </div>
            <div>
              <span style="color: #64748b; font-size: 0.75rem; display: block;">Due Date:</span>
              <strong id="ch-modal-due-date" style="color: #0f172a;"></strong>
            </div>
            <div>
              <span style="color: #64748b; font-size: 0.75rem; display: block;">E-Signature / Endorsement:</span>
              <span id="ch-modal-signature" style="color: #1e293b; font-weight: 500;"></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 0.85rem 1.4rem; display: flex; justify-content: flex-end; align-items: center; gap: 0.75rem;">
        <button type="button" class="btn btn-outline font-roboto" onclick="closeChapterEventDetailsModal()" style="padding: 0.5rem 1rem;">Close</button>
        <div id="ch-modal-action-container"></div>
      </div>
    </div>
  </div>

  <!-- View Announcement Details Modal -->
  <div id="announcement-detail-modal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.65); z-index: 1000; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: #ffffff; border-radius: 12px; width: 100%; max-width: 540px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.25); border: 1px solid #e2e8f0; padding: 1.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.85rem; margin-bottom: 1rem;">
        <div>
          <span id="modal-announcement-badge" class="badge" style="margin-bottom: 0.4rem; display: inline-block;">Official</span>
          <h3 id="modal-announcement-title" class="font-roboto" style="margin: 0; font-size: 1.15rem; color: #0f172a; font-weight: 800;">Announcement Title</h3>
        </div>
        <button type="button" onclick="closeAnnouncementModal()" style="background: none; border: none; font-size: 1.3rem; color: #64748b; cursor: pointer;">&times;</button>
      </div>
      <p id="modal-announcement-date" style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.85rem; font-weight: 600;"></p>
      <div id="modal-announcement-body" style="font-size: 0.9rem; color: #334155; line-height: 1.6; margin-bottom: 1.25rem;"></div>
      <div style="display: flex; justify-content: flex-end;">
        <button type="button" class="btn btn-outline" onclick="closeAnnouncementModal()">Close</button>
      </div>
    </div>
  </div>

  <style>
    .dash-tab {
      background: none;
      border: none;
      padding: 0.45rem 0.85rem;
      font-size: 0.825rem;
      font-weight: 600;
      color: #64748b;
      cursor: pointer;
      border-radius: 6px;
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      transition: all 0.15s ease;
    }
    .dash-tab:hover {
      background: #f1f5f9;
      color: #0f172a;
    }
    .dash-tab.active {
      background: #eff6ff;
      color: #1d4ed8;
      font-weight: 700;
    }
    .threshold-progress-wrap {
      width: 100%;
      height: 6px;
      background: #e2e8f0;
      border-radius: 9999px;
      overflow: hidden;
      margin-top: 4px;
    }
    .threshold-progress-fill {
      height: 100%;
      border-radius: 9999px;
      transition: width 0.3s ease;
    }
    .threshold-progress-fill.met { background: #16a34a; }
    .threshold-progress-fill.warn { background: #d97706; }
  </style>

  <script>
    let currentUser = null;
    try {
      const raw = localStorage.getItem('ytr_user');
      if (raw) currentUser = JSON.parse(raw);
    } catch(e) {}

    const isChapterAdmin = (currentUser && currentUser.role === 'chapter_admin');
    let activeChapterId = parseInt((isChapterAdmin && currentUser.chapter_id) ? currentUser.chapter_id : (localStorage.getItem('ytr_chapter_id') || '1'));
    let chapterWorkspaceData = null;
    let activeTab = 'all';

    document.addEventListener('DOMContentLoaded', () => {
      loadChapterList();
      loadWorkspace(activeChapterId);
      const sel = document.getElementById('chapter-switch-select');
      if (sel) {
        sel.addEventListener('focus', loadChapterList);
      }
    });

    async function loadChapterList() {
      const wrapper = document.getElementById('chapter-selector-wrapper');
      if (isChapterAdmin) {
        const chName = currentUser.chapter_name || 'My Chapter';
        if (wrapper) {
          wrapper.innerHTML = `
            <div style="display: flex; align-items: center; gap: 0.4rem; padding: 0.25rem 0.65rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; font-size: 0.825rem; font-weight: 700; color: #166534;">
              <i class="ph-fill ph-buildings"></i> <span>${escapeHtml(chName)}</span>
              <span class="badge" style="background: #dcfce7; color: #15803d; font-size: 0.65rem; padding: 2px 6px; margin-left: 0.35rem;">Assigned Branch</span>
            </div>
          `;
        }
        return;
      }

      try {
        const res = await YTR_API.chapters.getAll();
        if (res.ok && res.data) {
          const sel = document.getElementById('chapter-switch-select');
          if (!sel) return;
          sel.innerHTML = '';
          res.data.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = `${c.chapter_name} (${c.chapter_code})`;
            if (c.id === activeChapterId) opt.selected = true;
            sel.appendChild(opt);
          });
        }
      } catch (err) {
        console.error('Failed to load chapters:', err);
      }
    }

    function onChapterChange(newId) {
      if (isChapterAdmin) return;
      activeChapterId = parseInt(newId);
      localStorage.setItem('ytr_chapter_id', activeChapterId);
      loadWorkspace(activeChapterId);
    }

    async function loadWorkspace(chapterId) {
      try {
        const res = await YTR_API.chapters.getWorkspace(chapterId);
        if (res.ok && res.data) {
          chapterWorkspaceData = res.data;
          const { chapter, metrics, events, poster_requests, announcements } = res.data;

          // Header titles
          document.getElementById('chapter-portal-title').textContent = `${chapter.chapter_name} Workspace`;
          const sidebarCh = document.getElementById('sidebar-chapter-name');
          if (sidebarCh) sidebarCh.textContent = chapter.chapter_name.toUpperCase();

          // Metrics cards
          document.getElementById('metric-active-events').textContent = metrics.active_events || 0;
          document.getElementById('metric-pending-proposals').textContent = metrics.pending_proposals || 0;
          document.getElementById('metric-posters').textContent = metrics.posters_in_progress || 0;
          document.getElementById('metric-pending-reports').textContent = metrics.pending_reports || 0;

          // Update tab count badges
          const totalEvents = (events || []).length;
          const totalPosters = (poster_requests || []).length;
          const totalProposals = (events || []).filter(e => e.status === 'pending_approval' || e.status === 'draft').length;
          const totalDue = (events || []).filter(e => e.status === 'approved' && !e.report_id).length;
          const totalSubmitted = (events || []).filter(e => !!e.report_id).length;

          document.getElementById('tab-cnt-all').textContent = totalEvents;
          document.getElementById('tab-cnt-proposals').textContent = totalProposals;
          document.getElementById('tab-cnt-posters').textContent = totalPosters;
          document.getElementById('tab-cnt-due').textContent = totalDue;
          document.getElementById('tab-cnt-submitted').textContent = totalSubmitted;

          // Alerts banner
          if (metrics.pending_reports > 0) {
            document.getElementById('chapter-alert-banner').style.display = 'flex';
            document.getElementById('chapter-alert-text').textContent = 
              `Notice: Your chapter has ${metrics.pending_reports} event(s) that require official post-event report and attendee turnout submission.`;
          } else {
            document.getElementById('chapter-alert-banner').style.display = 'none';
          }

          // Render tables and widgets
          renderMonitoringTable();
          renderAnnouncementsWidget(announcements || []);
        }
      } catch (err) {
        console.error('Failed to load chapter workspace:', err);
      }
    }

    function setDashboardTab(tab) {
      activeTab = tab;
      document.querySelectorAll('.dash-tab').forEach(b => b.classList.remove('active'));
      const activeBtn = document.getElementById(`tab-btn-${tab}`);
      if (activeBtn) activeBtn.classList.add('active');

      const eventsContainer = document.getElementById('table-container-events');
      const postersContainer = document.getElementById('table-container-posters');

      if (tab === 'posters') {
        eventsContainer.style.display = 'none';
        postersContainer.style.display = 'block';
        renderPostersTable();
      } else {
        eventsContainer.style.display = 'block';
        postersContainer.style.display = 'none';
        renderMonitoringTable();
      }
    }

    let currentFilteredChapterEvents = [];

    function renderMonitoringTable() {
      const tbody = document.getElementById('chapter-events-body');
      if (!chapterWorkspaceData || !chapterWorkspaceData.events) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 2rem;">No events found.</td></tr>';
        return;
      }

      const q = (document.getElementById('chapter-events-search').value || '').toLowerCase().trim();
      let events = chapterWorkspaceData.events;

      // Filter by active tab
      if (activeTab === 'proposals') {
        events = events.filter(e => e.status === 'pending_approval' || e.status === 'draft');
      } else if (activeTab === 'due') {
        events = events.filter(e => e.status === 'approved' && !e.report_id);
      } else if (activeTab === 'submitted') {
        events = events.filter(e => !!e.report_id);
      }

      // Filter by search query
      if (q) {
        events = events.filter(e => (e.title && e.title.toLowerCase().includes(q)) || (e.venue && e.venue.toLowerCase().includes(q)) || (e.ytr_program && e.ytr_program.toLowerCase().includes(q)));
      }

      currentFilteredChapterEvents = events;

      if (events.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="5" style="text-align: center; padding: 2.5rem; color: #64748b;">
              <i class="ph ph-calendar-x" style="font-size: 2rem; color: #94a3b8;"></i>
              <p style="margin-top: 0.5rem; font-weight: 500;">No events match the selected filter.</p>
            </td>
          </tr>
        `;
        return;
      }

      tbody.innerHTML = events.map((e, index) => {
        // Proposal badge
        let propBadge = '';
        if (e.status === 'approved') {
          propBadge = `<span class="status-pill-fixed approved font-roboto"><i class="ph-bold ph-check"></i> Approved</span>`;
        } else if (e.status === 'pending_approval') {
          propBadge = `<span class="status-pill-fixed pending font-roboto"><i class="ph-bold ph-clock"></i> Pending Review</span>`;
        } else if (e.status === 'draft') {
          propBadge = `<span class="status-pill-fixed revision font-roboto"><i class="ph-bold ph-pencil-simple"></i> Draft</span>`;
        } else {
          propBadge = `<span class="status-pill-fixed neutral font-roboto">${escapeHtml(e.status)}</span>`;
        }

        // Primary Action button
        let actionBtn = '';
        if (e.report_id) {
          actionBtn = `<a href="ChapterReport.php" class="action-btn-uniform font-roboto" style="background: #ffffff; border: 1px solid #cbd5e1; color: #334155; text-decoration: none;" title="View Report"><i class="ph ph-file-text"></i> Report</a>`;
        } else if (e.status === 'approved') {
          actionBtn = `<a href="ChapterReport.php?event_id=${e.id}" class="action-btn-uniform font-roboto" style="background: #16a34a; border: 1px solid #15803d; color: #ffffff; text-decoration: none;" title="Submit Post-Event Report"><i class="ph ph-file-arrow-up"></i> Report</a>`;
        } else {
          actionBtn = `<a href="ChapterProposal.php" class="action-btn-uniform font-roboto" style="background: #2563eb; border: 1px solid #2563eb; color: #ffffff; text-decoration: none;" title="View Proposal"><i class="ph ph-eye"></i> View</a>`;
        }

        // Turnout calculation
        const target = e.target_turnout || 100;
        const actual = e.total_participants || 0;
        const pct = target > 0 ? Math.min(100, Math.round((actual / target) * 100)) : 0;
        const isMet = pct >= 100;

        const dateStr = e.start_datetime ? e.start_datetime.substring(0, 10) : 'Date TBD';

        return `
          <tr>
            <td data-label="Event Title" style="text-align: left; vertical-align: middle;">
              <span class="font-roboto" style="font-weight: 700; color: #0f172a; font-size: 0.92rem; display: block; width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${escapeHtml(e.title)}">
                ${escapeHtml(e.title)}
              </span>
            </td>
            <td data-label="Schedule" style="white-space: nowrap; font-size: 0.85rem; color: #1e293b; font-weight: 500; text-align: center; vertical-align: middle;">
              ${escapeHtml(dateStr)}
            </td>
            <td data-label="Status" style="white-space: nowrap; text-align: center; vertical-align: middle;">
              ${propBadge}
            </td>
            <td data-label="Turnout" style="white-space: nowrap; font-size: 0.85rem; font-weight: 600; text-align: center; vertical-align: middle;">
              <span>${actual} / ${target}</span>
              <small style="color: ${isMet ? '#16a34a' : '#d97706'}; margin-left: 0.3rem;">(${pct}%)</small>
            </td>
            <td data-label="Action" style="text-align: right; white-space: nowrap; vertical-align: middle;">
              <div style="display: inline-flex; align-items: center; gap: 0.45rem; justify-content: flex-end; white-space: nowrap;">
                <button type="button" class="action-btn-uniform btn-details font-roboto" onclick="openChapterEventDetailsModal(${index})" title="View event details">
                  <i class="ph ph-info"></i> Details
                </button>
                ${actionBtn}
              </div>
            </td>
          </tr>
        `;
      }).join('');
    }

    function openChapterEventDetailsModal(index) {
      const e = (currentFilteredChapterEvents && currentFilteredChapterEvents[index]) || (chapterWorkspaceData && chapterWorkspaceData.events && chapterWorkspaceData.events[index]);
      if (!e) return;

      document.getElementById('ch-modal-title').textContent = e.title || 'Untitled Event';

      const progEl = document.getElementById('ch-modal-program-badge');
      if (e.ytr_program) {
        progEl.style.display = 'inline-flex';
        progEl.className = 'badge';
        progEl.style.background = '#eff6ff';
        progEl.style.color = '#1d4ed8';
        progEl.style.border = '1px solid #bfdbfe';
        progEl.innerHTML = `<i class="ph-bold ph-compass"></i> ${escapeHtml(e.ytr_program)}`;
      } else {
        progEl.style.display = 'none';
      }

      // Status badge
      let propBadge = '';
      if (e.status === 'approved') {
        propBadge = `<span class="badge approved"><i class="ph ph-check"></i> Approved</span>`;
      } else if (e.status === 'pending_approval') {
        propBadge = `<span class="badge pending"><i class="ph ph-clock"></i> Pending Review</span>`;
      } else if (e.status === 'draft') {
        propBadge = `<span class="badge draft">Draft</span>`;
      } else {
        propBadge = `<span class="badge">${escapeHtml(e.status)}</span>`;
      }
      document.getElementById('ch-modal-status-badge').innerHTML = propBadge;

      // Logistics
      document.getElementById('ch-modal-type').textContent = e.event_type || 'Gathering / Activity';
      document.getElementById('ch-modal-venue').textContent = e.venue || 'Venue TBD';
      
      const startStr = e.start_datetime ? e.start_datetime.replace('T', ' ') : 'TBD';
      const endStr = e.end_datetime ? e.end_datetime.replace('T', ' ') : '';
      document.getElementById('ch-modal-schedule').textContent = endStr ? `${startStr} to ${endStr}` : startStr;
      document.getElementById('ch-modal-target-audience').textContent = e.target_audience || 'Open for All';

      // Turnout
      const target = e.target_turnout || 100;
      const actual = e.total_participants || 0;
      const pct = target > 0 ? Math.min(100, Math.round((actual / target) * 100)) : 0;
      const isMet = pct >= 100;

      document.getElementById('ch-modal-turnout-text').textContent = `${actual} / ${target} Attendees (${pct}%)`;
      const bar = document.getElementById('ch-modal-progress-bar');
      bar.style.width = `${pct}%`;
      bar.style.background = isMet ? '#16a34a' : '#d97706';

      let breakdownText = '';
      if (e.report_id) {
        breakdownText = `First-Timers: <strong>${e.first_timers || 0}</strong> &bull; Existing: <strong>${e.existing_members || 0}</strong> &bull; Leaders: <strong>${e.youth_leaders_count || 0}</strong>`;
      } else {
        breakdownText = `Post-event attendee roster pending filing.`;
      }
      document.getElementById('ch-modal-breakdown').innerHTML = breakdownText;

      // Report status & due date
      const repEl = document.getElementById('ch-modal-report-status');
      if (e.report_id) {
        repEl.innerHTML = `<span class="badge approved"><i class="ph-fill ph-check-circle"></i> Filed</span>`;
      } else {
        repEl.innerHTML = `<span class="badge overdue"><i class="ph-fill ph-x-circle"></i> Pending Filing</span>`;
      }

      document.getElementById('ch-modal-due-date').textContent = e.due_date ? e.due_date.substring(0, 10) : 'Flexible';

      const sigEl = document.getElementById('ch-modal-signature');
      if (e.signed_by_name) {
        sigEl.textContent = `Signed by ${e.signed_by_name}`;
      } else if (e.report_id) {
        sigEl.textContent = 'Digital Endorsement on File';
      } else {
        sigEl.textContent = 'Awaiting Post-Event Report';
      }

      // Footer action
      const actEl = document.getElementById('ch-modal-action-container');
      if (e.report_id) {
        actEl.innerHTML = `<a href="ChapterReport.php" class="btn btn-primary font-roboto" style="text-decoration: none; padding: 0.5rem 1.15rem; display: inline-flex; align-items: center; gap: 0.4rem;"><i class="ph ph-file-text"></i> View Report</a>`;
      } else if (e.status === 'approved') {
        actEl.innerHTML = `<a href="ChapterReport.php?event_id=${e.id}" class="btn btn-primary font-roboto" style="background: #16a34a; border-color: #15803d; color: #ffffff; text-decoration: none; padding: 0.5rem 1.15rem; display: inline-flex; align-items: center; gap: 0.4rem;"><i class="ph ph-file-arrow-up"></i> Submit Report</a>`;
      } else {
        actEl.innerHTML = `<a href="ChapterProposal.php" class="btn btn-outline font-roboto" style="text-decoration: none; padding: 0.5rem 1.15rem;">View Proposal</a>`;
      }

      document.getElementById('chapterEventDetailsModal').style.display = 'flex';
    }

    function closeChapterEventDetailsModal() {
      document.getElementById('chapterEventDetailsModal').style.display = 'none';
    }

    function renderPostersTable() {
      const tbody = document.getElementById('chapter-posters-body');
      if (!chapterWorkspaceData || !chapterWorkspaceData.poster_requests) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem;">No poster requests found.</td></tr>';
        return;
      }

      const q = (document.getElementById('chapter-events-search').value || '').toLowerCase().trim();
      let posters = chapterWorkspaceData.poster_requests;
      if (q) {
        posters = posters.filter(p => (p.event_title && p.event_title.toLowerCase().includes(q)) || (p.theme_or_concept && p.theme_or_concept.toLowerCase().includes(q)));
      }

      if (posters.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748b;">
              <i class="ph ph-image-square" style="font-size: 2rem; color: #94a3b8;"></i>
              <p style="margin-top: 0.5rem; font-weight: 500;">No poster requests found. Click "Request Media Poster" to submit one.</p>
            </td>
          </tr>
        `;
        return;
      }

      tbody.innerHTML = posters.map(p => {
        const stepNum = p.current_step || 1;
        const stepLabels = {
          1: 'Stage 1: Poster Creation',
          2: 'Stage 2: Official Page Posting',
          3: 'Stage 3: Promotional Video',
          4: 'Stage 4: Documentation Review',
          5: 'Stage 5: Concluded'
        };
        const currentStageText = stepLabels[stepNum] || `Stage ${stepNum}`;

        let statusBadge = `<span class="badge in-progress" style="white-space: nowrap;">${escapeHtml(p.status)}</span>`;
        if (p.status === 'completed') statusBadge = `<span class="badge approved" style="white-space: nowrap;"><i class="ph ph-check"></i> Completed</span>`;
        if (p.status === 'submitted') statusBadge = `<span class="badge pending" style="white-space: nowrap;">Awaiting Review</span>`;

        let posterDownload = `<span style="color: #94a3b8; font-size: 0.8rem; white-space: nowrap;">In Production</span>`;
        if (p.final_poster_path) {
          posterDownload = `<a href="${p.final_poster_path}" target="_blank" download class="btn btn-outline" style="padding: 0.25rem 0.6rem; font-size: 0.75rem; color: #16a34a; border-color: #bbf7d0; text-decoration: none; white-space: nowrap;"><i class="ph ph-download-simple"></i> Download</a>`;
        }

        return `
          <tr>
            <td data-label="Event Title">
              <span class="font-roboto dash-text-truncate" style="font-weight: 700; color: #0f172a; font-size: 0.92rem; display: inline-block; max-width: 240px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; vertical-align: middle;" title="${escapeHtml(p.event_title)}">
                ${escapeHtml(p.event_title)}
              </span>
            </td>
            <td data-label="Schedule & Deadline" style="white-space: nowrap; font-size: 0.85rem; color: #1e293b;">
              <span>${escapeHtml(p.event_date ? p.event_date.substring(0, 10) : 'TBD')}</span>
              <small style="color: #64748b; margin-left: 0.35rem;">(Due: ${escapeHtml(p.requested_deadline ? p.requested_deadline.substring(0, 10) : 'TBD')})</small>
            </td>
            <td data-label="Milestone Stage">
              <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-size: 0.75rem; font-weight: 700; white-space: nowrap;">
                ${currentStageText}
              </span>
            </td>
            <td data-label="Status">${statusBadge}</td>
            <td data-label="Poster">${posterDownload}</td>
            <td data-label="Action" style="text-align: right; white-space: nowrap;">
              <a href="ChapterRequest.php" class="btn btn-sm btn-outline font-roboto" style="padding: 0.35rem 0.65rem; font-size: 0.8rem; text-decoration: none; white-space: nowrap; display: inline-flex; align-items: center; gap: 0.35rem;">Track &rarr;</a>
            </td>
          </tr>
        `;
      }).join('');
    }
    function renderAnnouncementsWidget(announcements) {
      const container = document.getElementById('dashboard-announcements-list');
      if (!container) return;
      if (!announcements || announcements.length === 0) {
        container.innerHTML = `
          <div style="text-align: center; color: #64748b; font-size: 0.8rem; padding: 1.5rem 0;">
            <i class="ph ph-newspaper" style="font-size: 1.8rem; color: #cbd5e1; margin-bottom: 0.35rem;"></i>
            <p style="margin: 0;">No active announcements.</p>
          </div>
        `;
        return;
      }

      container.innerHTML = announcements.slice(0, 3).map(a => {
        const isNational = a.target_audience === 'everyone' || a.target_audience === 'chapter_admins';
        const dateStr = a.published_at ? a.published_at.substring(0, 10) : 'Recent';
        const safeBody = escapeHtml(a.body || '');
        const safeTitle = escapeHtml(a.title || '');

        return `
          <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
              <span class="badge" style="background: ${isNational ? '#eff6ff' : '#f0fdf4'}; color: ${isNational ? '#1d4ed8' : '#15803d'}; font-size: 0.65rem;">
                ${isNational ? 'National' : 'Chapter'}
              </span>
              <span style="font-size: 0.7rem; color: #94a3b8;">${dateStr}</span>
            </div>
            <h4 style="font-size: 0.85rem; font-weight: 700; color: #0f172a; margin: 0 0 0.25rem; line-height: 1.3;">${safeTitle}</h4>
            <p style="font-size: 0.75rem; color: #64748b; margin: 0 0 0.4rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
              ${safeBody}
            </p>
            <button type="button" onclick="openAnnouncementModal('${encodeURIComponent(safeTitle)}', '${encodeURIComponent(dateStr)}', '${encodeURIComponent(safeBody)}', '${isNational ? 'National Broadcast' : 'Chapter Announcement'}')" style="background: none; border: none; color: #2563eb; font-size: 0.72rem; font-weight: 700; cursor: pointer; padding: 0;">
              Read details &rarr;
            </button>
          </div>
        `;
      }).join('');
    }

    function openAnnouncementModal(encodedTitle, encodedDate, encodedBody, badgeText) {
      document.getElementById('modal-announcement-title').textContent = decodeURIComponent(encodedTitle);
      document.getElementById('modal-announcement-date').textContent = decodeURIComponent(encodedDate);
      document.getElementById('modal-announcement-body').textContent = decodeURIComponent(encodedBody);
      document.getElementById('modal-announcement-badge').textContent = badgeText;
      document.getElementById('announcement-detail-modal').style.display = 'flex';
    }

    function closeAnnouncementModal() {
      document.getElementById('announcement-detail-modal').style.display = 'none';
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
