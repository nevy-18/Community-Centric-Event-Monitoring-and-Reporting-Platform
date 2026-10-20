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
  <link rel="stylesheet" href="assets/Chapter.css">
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
              <h1 class="font-roboto" id="chapter-portal-title" style="margin: 0; color: #0f172a; font-weight: 800;">Laguna Chapter Workspace</h1>
              <span class="portal-indicator chapter"><i class="ph-fill ph-buildings"></i> Chapter Branch Portal</span>
            </div>
            <p style="color: var(--text-muted); margin: 0;">Dedicated chapter workspace for drafting proposals, requesting promotional media, and submitting post-event reports.</p>
          </div>
          
          <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
            <div id="chapter-selector-wrapper" style="display: flex; align-items: center; gap: 0.35rem; background: #ffffff; padding: 0.3rem 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px;">
              <label for="chapter-switch-select" style="font-size: 0.8rem; font-weight: 600; color: #64748b;">Active Chapter:</label>
              <select id="chapter-switch-select" class="chapter-pill-select" onchange="onChapterChange(this.value)" style="border: none; outline: none; background: transparent; padding: 0.2rem 0.4rem; font-size: 0.85rem; font-weight: 700; color: #1e3a8a; cursor: pointer;">
                <option value="1">Laguna Chapter (YTR-LAG)</option>
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

        <!-- 2-Column Responsive Dashboard Layout (Left: Monitoring Hub, Right: Announcements & Actions) -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start; margin-bottom: 2rem;">
          
          <!-- LEFT COLUMN: Main Monitoring Table & Tabs -->
          <div>
            <div class="table-section" style="margin-bottom: 0;">
              <div class="table-header" style="flex-direction: column; align-items: stretch; gap: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                  <div>
                    <h2 class="font-roboto" style="color: var(--primary); font-size: 1.25rem; font-weight: bold; margin: 0;">Centralized Activity & Pending Monitoring</h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0.25rem 0 0;">Monitor proposals, poster pipelines, attendance thresholds, and post-event deadlines.</p>
                  </div>
                  <div class="search-bar" style="margin: 0; min-width: 220px;">
                    <i class="ph ph-magnifying-glass"></i>
                    <input type="text" id="chapter-events-search" class="font-roboto" placeholder="Search event or topic..." oninput="renderMonitoringTable()">
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
                <table>
                  <thead class="font-roboto">
                    <tr>
                      <th>Event Title & Type</th>
                      <th>Schedule & Venue</th>
                      <th>Proposal Status</th>
                      <th>Due Date & Deadline</th>
                      <th>Turnout Goal vs Actual</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="chapter-events-body">
                    <tr>
                      <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
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
                      <th>Event Title & Theme</th>
                      <th>Event Date & Deadline</th>
                      <th>Current Milestone Stage</th>
                      <th>Status</th>
                      <th>Official Poster</th>
                      <th>Action</th>
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
          </div>

          <!-- RIGHT COLUMN: Live Announcements Feed -->
          <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Announcements & Broadcasts Widget -->
            <div style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 class="font-roboto" style="font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                  <i class="ph-fill ph-broadcast" style="color: #16a34a;"></i> Announcements
                </h3>
                <a href="Announcement.php" style="font-size: 0.75rem; color: #2563eb; font-weight: 600; text-decoration: none;">View All &rarr;</a>
              </div>
              <div id="dashboard-announcements-list" style="display: flex; flex-direction: column; gap: 0.85rem;">
                <div style="text-align: center; color: #64748b; font-size: 0.8rem; padding: 1rem 0;">
                  <i class="ph ph-spinner ph-spin"></i> Loading broadcasts...
                </div>
              </div>
            </div>

          </div>

        </div>

      </main>
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

    function renderMonitoringTable() {
      const tbody = document.getElementById('chapter-events-body');
      if (!chapterWorkspaceData || !chapterWorkspaceData.events) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem;">No events found.</td></tr>';
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
        events = events.filter(e => (e.title && e.title.toLowerCase().includes(q)) || (e.venue && e.venue.toLowerCase().includes(q)));
      }

      if (events.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748b;">
              <i class="ph ph-calendar-x" style="font-size: 2rem; color: #94a3b8;"></i>
              <p style="margin-top: 0.5rem; font-weight: 500;">No events match the selected filter.</p>
            </td>
          </tr>
        `;
        return;
      }

      tbody.innerHTML = events.map(e => {
        // Proposal badge
        let propBadge = '';
        if (e.status === 'approved') {
          propBadge = `<span class="badge approved"><i class="ph ph-check"></i> Approved</span>`;
        } else if (e.status === 'pending_approval') {
          propBadge = `<span class="badge pending"><i class="ph ph-clock"></i> Pending Review</span>`;
        } else if (e.status === 'draft') {
          propBadge = `<span class="badge draft">Draft</span>`;
        } else {
          propBadge = `<span class="badge">${e.status}</span>`;
        }

        // Due date & Deadline badge
        let dueBadgeHtml = '';
        if (e.due_badge) {
          dueBadgeHtml = `<span class="${e.due_badge.cls}" style="font-size: 0.75rem;">${e.due_badge.label}</span>`;
        } else if (e.due_date) {
          dueBadgeHtml = `<span class="badge" style="font-size: 0.75rem;">Due ${e.due_date.substring(0, 10)}</span>`;
        } else {
          dueBadgeHtml = `<span class="badge" style="background: #f1f5f9; color: #64748b; font-size: 0.75rem;">Flexible</span>`;
        }

        // Action button
        let actionBtn = '';
        if (e.report_id) {
          actionBtn = `<a href="ChapterReport.php" class="btn btn-outline" style="padding: 0.35rem 0.65rem; font-size: 0.8rem; text-decoration: none;"><i class="ph ph-file-text"></i> View Report</a>`;
        } else if (e.status === 'approved') {
          actionBtn = `<a href="ChapterReport.php?event_id=${e.id}" class="btn btn-primary" style="background: #16a34a; border-color: #15803d; padding: 0.35rem 0.65rem; font-size: 0.8rem; text-decoration: none;"><i class="ph ph-file-arrow-up"></i> Submit Report</a>`;
        } else {
          actionBtn = `<a href="ChapterProposal.php" class="btn btn-outline" style="padding: 0.35rem 0.65rem; font-size: 0.8rem; text-decoration: none;">View Proposal</a>`;
        }

        // Turnout calculation & progress bar
        const target = e.target_turnout || 100;
        const actual = e.total_participants || 0;
        const pct = target > 0 ? Math.min(100, Math.round((actual / target) * 100)) : 0;
        const isMet = pct >= 100;

        return `
          <tr>
            <td>
              <div style="font-weight: 700; color: #0f172a;">${escapeHtml(e.title)}</div>
              <div style="display: flex; gap: 0.35rem; align-items: center; flex-wrap: wrap; margin-top: 2px;">
                <small style="color: #64748b; text-transform: uppercase; font-size: 0.72rem;">${escapeHtml(e.event_type || 'Gathering')}</small>
                ${e.ytr_program ? `<span class="badge" style="background: #eff6ff; color: #1d4ed8; font-size: 0.68rem; padding: 1px 5px; font-weight: 700; border: 1px solid #bfdbfe;"><i class="ph-bold ph-compass"></i> ${escapeHtml(e.ytr_program)}</span>` : ''}
              </div>
            </td>
            <td>
              <div style="font-size: 0.85rem; color: #1e293b; font-weight: 500;">${escapeHtml(e.start_datetime ? e.start_datetime.substring(0, 10) : 'Date TBD')}</div>
              <small style="color: #64748b;">${escapeHtml(e.venue || 'Venue TBD')}</small>
            </td>
            <td>${propBadge}</td>
            <td>${dueBadgeHtml}</td>
            <td style="min-width: 140px;">
              <div style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 600;">
                <span>${actual} / ${target}</span>
                <span style="color: ${isMet ? '#16a34a' : '#d97706'};">${pct}%</span>
              </div>
              <div class="threshold-progress-wrap">
                <div class="threshold-progress-fill ${isMet ? 'met' : 'warn'}" style="width: ${pct}%;"></div>
              </div>
            </td>
            <td>${actionBtn}</td>
          </tr>
        `;
      }).join('');
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

        let statusBadge = `<span class="badge in-progress">${p.status}</span>`;
        if (p.status === 'completed') statusBadge = `<span class="badge approved"><i class="ph ph-check"></i> Completed</span>`;
        if (p.status === 'submitted') statusBadge = `<span class="badge pending">Awaiting Review</span>`;

        let posterDownload = `<span style="color: #94a3b8; font-size: 0.8rem;">In Production</span>`;
        if (p.final_poster_path) {
          posterDownload = `<a href="${p.final_poster_path}" target="_blank" download class="btn btn-outline" style="padding: 0.25rem 0.6rem; font-size: 0.75rem; color: #16a34a; border-color: #bbf7d0; text-decoration: none;"><i class="ph ph-download-simple"></i> Download</a>`;
        }

        return `
          <tr>
            <td>
              <div style="font-weight: 700; color: #0f172a;">${escapeHtml(p.event_title)}</div>
              <small style="color: #64748b;">${escapeHtml(p.theme_or_concept || 'Concept specified')}</small>
            </td>
            <td>
              <div style="font-size: 0.85rem; color: #1e293b;">${escapeHtml(p.event_date ? p.event_date.substring(0, 10) : 'TBD')}</div>
              <small style="color: #64748b;">Deadline: ${escapeHtml(p.requested_deadline ? p.requested_deadline.substring(0, 10) : 'TBD')}</small>
            </td>
            <td>
              <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-size: 0.75rem; font-weight: 700;">
                ${currentStageText}
              </span>
            </td>
            <td>${statusBadge}</td>
            <td>${posterDownload}</td>
            <td>
              <a href="ChapterRequest.php" class="btn btn-outline" style="padding: 0.35rem 0.65rem; font-size: 0.8rem; text-decoration: none;">Track &rarr;</a>
            </td>
          </tr>
        `;
      }).join('');
    }

    function renderAnnouncementsWidget(announcements) {
      const container = document.getElementById('dashboard-announcements-list');
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
