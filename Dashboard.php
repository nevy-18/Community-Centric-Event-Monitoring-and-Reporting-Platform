<?php include 'includes/auth_guard.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>YTR | Dashboard</title>
  
  <!-- Phosphor Icons -->
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  
  <!-- Centralized CSS -->
  <link rel="stylesheet" href="assets/National.css?v=<?= time() ?>">
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
        </div>

        <!-- Four Responsive Metric Cards -->
        <!-- Four Responsive Metric Cards -->
        <div class="metrics-grid">
          <div class="metric-card">
            <div class="metric-icon amber"><i class="ph-fill ph-file-text"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Pending Chapter Reports</h5>
              <p class="metric-value font-roboto" id="metric-pending-reports">0</p>
              <p class="metric-sub">Conducted events awaiting submission</p>
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
            <div class="metric-icon rose"><i class="ph-fill ph-warning-circle"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Reports Overdue</h5>
              <p class="metric-value font-roboto" id="metric-due-reports">0</p>
              <p class="metric-sub">Past 2-week submission deadline</p>
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
        </div>

        <!-- Responsive Table Section -->
        <div class="table-section">
          <div class="table-header">
            <div>
              <h2 class="font-roboto" style="color: var(--primary); font-size: 1.25rem; font-weight: bold; margin: 0;">Chapter Conducted Events & Reporting Status</h2>
              <p style="margin: 0.2rem 0 0; color: var(--text-muted); font-size: 0.8rem;">
                Sorted by deadline urgency. Once submitted and validated, events are automatically cleared from the active action list.
              </p>
            </div>
            <div class="search-bar" style="margin: 0; width: 100%; max-width: 100%;">
              <i class="ph ph-magnifying-glass"></i>
              <input type="text" id="chapter-search" class="font-roboto" placeholder="Search Chapter, Event, or Region..." oninput="filterChapterTable()" style="width: 100%; flex: 1; min-width: 0;">
            </div>
          </div>

          <!-- Tabs -->
          <div class="tabs-container font-roboto">
            <button class="tab-btn active" id="tab-action" onclick="setChapterTab('needs_action', this)">
              <i class="ph ph-warning-circle"></i> Needs Action (<span id="count-action-events">0</span>)
            </button>
            <button class="tab-btn" id="tab-overdue" onclick="setChapterTab('overdue', this)">
              <i class="ph ph-clock-countdown"></i> Overdue (<span id="count-overdue-events">0</span>)
            </button>
            <button class="tab-btn" id="tab-due-soon" onclick="setChapterTab('due_soon', this)">
              <i class="ph ph-hourglass-high"></i> Due Soon (<span id="count-due-soon-events">0</span>)
            </button>
            <button class="tab-btn" id="tab-all" onclick="setChapterTab('all', this)">
              <i class="ph ph-list-bullets"></i> All Conducted Events (<span id="count-all-events">0</span>)
            </button>
          </div>
          
          <div class="table-responsive">
            <table>
              <thead class="font-roboto">
                <tr>
                  <th>Chapter Name</th>
                  <th>Event Conducted</th>
                  <th>Report Submitted?</th>
                  <th>Status</th>
                  <th>Deadline & Days Left</th>
                  <th style="text-align: right;">Action</th>
                </tr>
              </thead>
              <tbody id="chapter-table-body">
                <tr>
                  <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                    <i class="ph ph-spinner ph-spin" style="font-size: 1.5rem;"></i> Loading chapter events and report deadlines...
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- Interactive Remind Chapter Modal -->
  <div id="remindChapterModal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); z-index: 1050; align-items: center; justify-content: center; padding: 1rem;" onclick="if(event.target === this) closeRemindModal();">
    <div class="modal-md" style="max-width: 580px; width: 100%; background: #ffffff; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 20px 35px -10px rgba(15, 23, 42, 0.35); border: 1px solid #e2e8f0;">
      <!-- Header -->
      <div style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); color: #ffffff; padding: 1.1rem 1.35rem; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
          <div style="background: rgba(255, 255, 255, 0.15); width: 38px; height: 38px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
            <i class="ph-bold ph-bell-ringing"></i>
          </div>
          <div>
            <h3 class="font-roboto" style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #ffffff;">Send Report Reminder</h3>
            <p style="margin: 2px 0 0; font-size: 0.75rem; color: #bfdbfe;">Direct notification to chapter administrator</p>
          </div>
        </div>
        <button type="button" onclick="closeRemindModal()" style="background: none; border: none; color: #ffffff; font-size: 1.35rem; cursor: pointer; padding: 4px; display: flex; align-items: center; justify-content: center; border-radius: 6px; opacity: 0.85; transition: opacity 0.15s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.85'">&times;</button>
      </div>

      <!-- Body -->
      <div style="padding: 1.25rem 1.35rem; background: #ffffff; display: flex; flex-direction: column; gap: 1rem;">
        <!-- Target Chapter & Event Info Card -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem 1rem;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem; margin-bottom: 0.4rem;">
            <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em;">Target Chapter</span>
            <span id="remind-target-badge" class="badge" style="font-size: 0.68rem; padding: 2px 6px;"></span>
          </div>
          <div id="remind-target-chapter" style="font-size: 0.95rem; font-weight: 700; color: #0f172a;"></div>

          <div style="margin-top: 0.6rem; padding-top: 0.6rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
            <div>
              <span style="color: #64748b;">Event:</span>
              <strong id="remind-target-event" style="color: #1e3a8a; margin-left: 0.25rem;"></strong>
            </div>
            <div>
              <span style="color: #64748b;">Deadline:</span>
              <strong id="remind-target-deadline" style="color: #0f172a; margin-left: 0.25rem;"></strong>
            </div>
          </div>
        </div>

        <!-- Subject Field -->
        <div>
          <label for="remind-subject" style="display: block; font-size: 0.8rem; font-weight: 600; color: #334155; margin-bottom: 0.35rem;">
            Reminder Subject <span style="color: #ef4444;">*</span>
          </label>
          <input type="text" id="remind-subject" class="form-control" style="width: 100%; padding: 0.55rem 0.75rem; font-size: 0.875rem; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;" placeholder="Enter reminder subject...">
        </div>

        <!-- Message Textarea -->
        <div>
          <label for="remind-message" style="display: block; font-size: 0.8rem; font-weight: 600; color: #334155; margin-bottom: 0.35rem;">
            Reminder Message / Instructions <span style="color: #ef4444;">*</span>
          </label>
          <textarea id="remind-message" rows="5" class="form-control font-roboto" style="width: 100%; padding: 0.6rem 0.75rem; font-size: 0.85rem; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; line-height: 1.45; resize: vertical;" placeholder="Type your reminder message..."></textarea>
          <span style="font-size: 0.72rem; color: #64748b; margin-top: 0.25rem; display: block;">
            <i class="ph ph-info"></i> This reminder is sent directly to the Chapter Admin and displayed in their topbar notification bell.
          </span>
        </div>

        <!-- Feedback / Status message -->
        <div id="remind-feedback" style="display: none; padding: 0.6rem 0.85rem; border-radius: 6px; font-size: 0.825rem;"></div>
      </div>

      <!-- Footer -->
      <div style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 0.85rem 1.35rem; display: flex; justify-content: flex-end; align-items: center; gap: 0.65rem;">
        <button type="button" class="btn btn-outline" onclick="closeRemindModal()" style="padding: 0.45rem 1rem; font-size: 0.85rem; background: #ffffff;">
          Cancel
        </button>
        <button type="button" id="btn-send-reminder" class="btn btn-primary" onclick="submitReminder()" style="padding: 0.45rem 1.15rem; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.45rem; background: #2563eb; border-color: #1d4ed8;">
          <i class="ph-bold ph-paper-plane-tilt"></i> Send Reminder
        </button>
      </div>
    </div>
  </div>

  <script>
    let conductedEventsData = [];
    let currentFilter = 'needs_action';

    document.addEventListener('DOMContentLoaded', () => {
      loadDashboardOverview();
    });

    async function loadDashboardOverview() {
      try {
        const res = await YTR_API.dashboard.getOverview();
        if (res.ok && res.data) {
          const { metrics, conducted_events } = res.data;
          
          // Populate metrics
          document.getElementById('metric-pending-reports').textContent = metrics.pending_chapters_report || 0;
          document.getElementById('metric-total-reports').textContent = metrics.total_reports_submitted || 0;
          document.getElementById('metric-partner-chapters').textContent = metrics.partner_chapters_count || 0;
          document.getElementById('metric-due-reports').textContent = metrics.chapters_due_reports || 0;

          // Alert banner
          const totalAlerts = (metrics.pending_chapters_report || 0);
          if (totalAlerts > 0) {
            document.getElementById('dash-alert-banner').style.display = 'flex';
            document.getElementById('dash-alert-text').textContent = 
              `Notice: ${metrics.pending_chapters_report} post-event report(s) requiring chapter action, with ${metrics.chapters_due_reports} past the 2-week deadline.`;
          } else {
            document.getElementById('dash-alert-banner').style.display = 'none';
          }

          conductedEventsData = conducted_events || [];

          // Counts for tabs
          const actionCount = conductedEventsData.filter(e => e.needs_action).length;
          const overdueCount = conductedEventsData.filter(e => !e.is_report_submitted && e.days_left < 0).length;
          const dueSoonCount = conductedEventsData.filter(e => !e.is_report_submitted && e.days_left >= 0 && e.days_left <= 3).length;

          document.getElementById('count-action-events').textContent = actionCount;
          document.getElementById('count-overdue-events').textContent = overdueCount;
          document.getElementById('count-due-soon-events').textContent = dueSoonCount;
          document.getElementById('count-all-events').textContent = conductedEventsData.length;

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

      let filtered = conductedEventsData.filter(item => {
        // Tab filtering
        if (currentFilter === 'needs_action' && !item.needs_action) return false;
        if (currentFilter === 'overdue' && (item.is_report_submitted || item.days_left >= 0)) return false;
        if (currentFilter === 'due_soon' && (item.is_report_submitted || item.days_left < 0 || item.days_left > 3)) return false;

        // Search filtering
        if (search) {
          const matchChapter = (item.chapter_name || '').toLowerCase().includes(search);
          const matchCode = (item.chapter_code || '').toLowerCase().includes(search);
          const matchRegion = (item.region || '').toLowerCase().includes(search);
          const matchEvent = (item.event_title || '').toLowerCase().includes(search);
          const matchStatus = (item.status_label || '').toLowerCase().includes(search);
          return matchChapter || matchCode || matchRegion || matchEvent || matchStatus;
        }
        return true;
      });

      // Sort items: Pendings at the top, closest to deadline first
      filtered.sort((a, b) => {
        // Unsubmitted first
        if (a.is_report_submitted !== b.is_report_submitted) {
          return a.is_report_submitted ? 1 : -1;
        }
        // If both unsubmitted: overdue first (negative days_left)
        const aOverdue = a.days_left < 0;
        const bOverdue = b.days_left < 0;
        if (aOverdue !== bOverdue) {
          return aOverdue ? -1 : 1;
        }
        // Ascending by days_left (closest deadline first)
        return a.days_left - b.days_left;
      });

      if (filtered.length === 0) {
        let emptyMsg = 'No events matching the selected filter criteria.';
        if (currentFilter === 'needs_action') {
          emptyMsg = 'All post-event reports are submitted and up to date! No pending action items requiring follow-up.';
        } else if (currentFilter === 'overdue') {
          emptyMsg = 'Great news! There are no overdue event reports across any chapters.';
        }

        tbody.innerHTML = `
          <tr>
            <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
              <i class="ph-fill ph-check-circle" style="font-size: 2.5rem; color: #16a34a;"></i>
              <p style="margin-top: 0.5rem; font-weight: 600; color: #1e293b;">${emptyMsg}</p>
              ${currentFilter !== 'all' ? '<button class="btn btn-sm btn-outline" style="margin: 0.75rem auto 0;" onclick="document.getElementById(\'tab-all\').click()">View All Conducted Events</button>' : ''}
            </td>
          </tr>
        `;
        return;
      }

      tbody.innerHTML = filtered.map(item => {
        // 1. Report Submitted or Not Badge
        const submittedBadge = item.is_report_submitted
          ? `<span class="badge approved"><i class="ph-fill ph-check-circle"></i> Submitted</span>`
          : `<span class="badge overdue"><i class="ph-fill ph-x-circle"></i> Not Submitted</span>`;

        // 2. Status Badge
        let statusBadgeHtml = '';
        if (item.status_key === 'overdue') {
          statusBadgeHtml = `<span class="badge overdue"><i class="ph-bold ph-warning-circle"></i> Overdue</span>`;
        } else if (item.status_key === 'due_soon') {
          statusBadgeHtml = `<span class="badge due-soon"><i class="ph-bold ph-hourglass-high"></i> Due Soon</span>`;
        } else if (item.status_key === 'under_review') {
          statusBadgeHtml = `<span class="badge" style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;"><i class="ph-fill ph-clock"></i> Awaiting Review</span>`;
        } else if (item.status_key === 'approved') {
          statusBadgeHtml = `<span class="badge approved"><i class="ph-fill ph-check"></i> Validated</span>`;
        } else {
          statusBadgeHtml = `<span class="badge timer"><i class="ph ph-timer"></i> Pending Submission</span>`;
        }

        // 3. Days Left & Deadline display
        let daysLeftBadge = '';
        if (item.is_validated) {
          daysLeftBadge = `<span class="badge approved"><i class="ph ph-check"></i> Completed</span>`;
        } else if (item.days_left < 0) {
          daysLeftBadge = `<span class="badge overdue"><i class="ph-bold ph-warning"></i> Overdue by ${Math.abs(item.days_left)}d</span>`;
        } else if (item.days_left <= 3) {
          daysLeftBadge = `<span class="badge due-soon"><i class="ph-bold ph-hourglass"></i> ${item.days_left === 0 ? 'Due Today' : item.days_left + 'd left'}</span>`;
        } else {
          daysLeftBadge = `<span class="badge timer"><i class="ph-bold ph-timer"></i> ${item.days_left}d left</span>`;
        }

        // 4. Action button
        const actionBtn = item.is_report_submitted
          ? `<a href="Report.php" class="btn btn-sm btn-primary" style="text-decoration: none; padding: 0.35rem 0.75rem;">
               <i class="ph ph-clipboard-text"></i> View Report
             </a>`
          : `<button type="button" class="btn btn-sm btn-outline font-roboto" 
               onclick="openRemindModal(${item.chapter_id}, '${escapeJs(item.chapter_name)}', ${item.event_id}, '${escapeJs(item.event_title)}', ${item.days_left}, '${escapeJs(item.deadline_formatted || item.deadline)}')" 
               style="padding: 0.35rem 0.75rem; border-color: #3b82f6; color: #1d4ed8; background: #eff6ff; font-weight: 600; display: inline-flex; align-items: center; gap: 0.35rem; cursor: pointer; transition: all 0.15s ease;">
               <i class="ph-bold ph-bell"></i> Remind Chapter
             </button>`;

        let progBadge = '';
        if (item.ytr_program) {
          let pBg = '#eff6ff', pCol = '#1d4ed8', pBorder = '#bfdbfe';
          if (item.ytr_program.startsWith('Rise up')) {
            pBg = '#fffbeb'; pCol = '#b45309'; pBorder = '#fde68a';
          } else if (item.ytr_program.startsWith('Build up')) {
            pBg = '#f0fdf4'; pCol = '#15803d'; pBorder = '#bbf7d0';
          } else if (item.ytr_program.startsWith('Gear up')) {
            pBg = '#faf5ff'; pCol = '#7e22ce'; pBorder = '#e9d5ff';
          }
          progBadge = `<span class="badge" style="background: ${pBg}; color: ${pCol}; border: 1px solid ${pBorder}; font-size: 0.68rem; padding: 1px 6px; font-weight: 700;">
            <i class="ph-bold ph-compass"></i> ${escapeHtml(item.ytr_program)}
          </span>`;
        }

        return `
          <tr>
            <td data-label="Chapter Name">
              <div style="font-weight: 700; color: #0f172a; font-size: 0.925rem;">${escapeHtml(item.chapter_name)}</div>
              <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">
                <span>${escapeHtml(item.region || 'Region')}</span>
                <span class="badge" style="margin-left: 0.35rem; font-size: 0.68rem; padding: 2px 6px;">${escapeHtml(item.chapter_code || '')}</span>
              </div>
            </td>
            <td data-label="Event Conducted">
              <div style="font-weight: 700; color: #1e3a8a; font-size: 0.9rem;">${escapeHtml(item.event_title)}</div>
              ${progBadge ? `<div style="margin-top: 3px; margin-bottom: 2px;">${progBadge}</div>` : ''}
              <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">
                <i class="ph ph-calendar-blank"></i> Conducted: <strong>${item.event_date_formatted || item.event_date}</strong>
                ${item.venue ? ` &bull; <span>${escapeHtml(item.venue)}</span>` : ''}
              </div>
            </td>
            <td data-label="Report Submitted?">
              ${submittedBadge}
            </td>
            <td data-label="Status">
              ${statusBadgeHtml}
            </td>
            <td data-label="Deadline & Days Left">
              <div>
                ${daysLeftBadge}
                <div style="font-size: 0.75rem; color: #475569; margin-top: 0.25rem;">
                  Deadline: <strong>${item.deadline_formatted || item.deadline}</strong>
                </div>
              </div>
            </td>
            <td data-label="Action" style="text-align: right;">
              ${actionBtn}
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

    let currentRemindChapterId = null;
    let currentRemindEventId = null;

    function escapeJs(str) {
      if (!str) return '';
      return String(str).replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '&quot;');
    }

    function openRemindModal(chapterId, chapterName, eventId, eventTitle, daysLeft, deadline) {
      currentRemindChapterId = chapterId;
      currentRemindEventId = eventId;

      document.getElementById('remind-target-chapter').textContent = chapterName;
      document.getElementById('remind-target-event').textContent = eventTitle;
      document.getElementById('remind-target-deadline').textContent = deadline;

      const badgeEl = document.getElementById('remind-target-badge');
      if (daysLeft < 0) {
        badgeEl.className = 'badge overdue';
        badgeEl.textContent = `Overdue by ${Math.abs(daysLeft)}d`;
      } else if (daysLeft <= 3) {
        badgeEl.className = 'badge due-soon';
        badgeEl.textContent = daysLeft === 0 ? 'Due Today' : `${daysLeft}d left`;
      } else {
        badgeEl.className = 'badge timer';
        badgeEl.textContent = `${daysLeft}d left`;
      }

      document.getElementById('remind-subject').value = `Action Required: Submit Post-Event Report - ${eventTitle}`;
      
      const defaultMsg = `Dear ${chapterName} Coordinator and Admin Team,\n\nPlease be reminded to submit the official post-event accomplishment report and attendee roster for "${eventTitle}".\n\nAs per national policy, reports must be submitted within 2 weeks of event conclusion so budgets can be liquidated and official certificates endorsed. Please access the Chapter Portal to submit the report at your earliest convenience.\n\nThank you,\nNational Secretariat`;

      document.getElementById('remind-message').value = defaultMsg;

      const fb = document.getElementById('remind-feedback');
      fb.style.display = 'none';
      fb.textContent = '';
      fb.className = '';

      const btn = document.getElementById('btn-send-reminder');
      btn.disabled = false;
      btn.innerHTML = '<i class="ph-bold ph-paper-plane-tilt"></i> Send Reminder';

      document.getElementById('remindChapterModal').style.display = 'flex';
    }

    function closeRemindModal() {
      document.getElementById('remindChapterModal').style.display = 'none';
    }

    async function submitReminder() {
      const subject = document.getElementById('remind-subject').value.trim();
      const message = document.getElementById('remind-message').value.trim();
      const fb = document.getElementById('remind-feedback');
      const btn = document.getElementById('btn-send-reminder');

      if (!subject) {
        fb.style.display = 'block';
        fb.style.background = '#fef2f2';
        fb.style.color = '#991b1b';
        fb.style.border = '1px solid #fecaca';
        fb.textContent = 'Please enter a reminder subject.';
        return;
      }

      if (!message) {
        fb.style.display = 'block';
        fb.style.background = '#fef2f2';
        fb.style.color = '#991b1b';
        fb.style.border = '1px solid #fecaca';
        fb.textContent = 'Please enter your reminder message.';
        return;
      }

      btn.disabled = true;
      btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Sending Reminder...';
      fb.style.display = 'none';

      try {
        const payload = {
          event_id: currentRemindEventId,
          subject: subject,
          message: message
        };

        const res = await YTR_API.chapters.sendReminder(currentRemindChapterId, payload);
        if (res && res.status === 'success') {
          fb.style.display = 'block';
          fb.style.background = '#f0fdf4';
          fb.style.color = '#166534';
          fb.style.border = '1px solid #bbf7d0';
          fb.innerHTML = `<i class="ph-bold ph-check-circle"></i> ${res.message || 'Reminder sent successfully!'}`;

          btn.innerHTML = '<i class="ph-bold ph-check"></i> Sent';

          setTimeout(() => {
            closeRemindModal();
          }, 1600);
        } else {
          throw new Error((res && res.message) || 'Failed to send reminder.');
        }
      } catch (err) {
        console.error('Error sending reminder:', err);
        fb.style.display = 'block';
        fb.style.background = '#fef2f2';
        fb.style.color = '#991b1b';
        fb.style.border = '1px solid #fecaca';
        fb.textContent = err.message || 'An error occurred while sending the reminder.';
        btn.disabled = false;
        btn.innerHTML = '<i class="ph-bold ph-paper-plane-tilt"></i> Send Reminder';
      }
    }

  </script>
</body>
</html>