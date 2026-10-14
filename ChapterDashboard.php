<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chapter Workspace | Youth on the Rock</title>
  
  <!-- Phosphor Icons -->
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  
  <!-- Centralized CSS -->
  <link rel="stylesheet" href="assets/dashboard.css">
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
            <div style="display: flex; align-items: center; gap: 0.35rem; background: #ffffff; padding: 0.3rem 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px;">
              <label for="chapter-switch-select" style="font-size: 0.8rem; font-weight: 600; color: #64748b;">Active Chapter:</label>
              <select id="chapter-switch-select" class="chapter-pill-select" onchange="onChapterChange(this.value)" style="border: none; outline: none; background: transparent; padding: 0.2rem 0.4rem; font-size: 0.85rem; font-weight: 700; color: #1e3a8a; cursor: pointer;">
                <option value="1">Laguna Chapter (YTR-LAG)</option>
              </select>
            </div>
            
            <a href="ChapterProposal.php" class="btn btn-outline" style="text-decoration: none;">
              <i class="ph ph-file-plus"></i> Submit Proposal
            </a>
            <a href="ChapterReport.php" class="btn btn-primary" style="background: #16a34a; border-color: #15803d; text-decoration: none;">
              <i class="ph ph-file-arrow-up"></i> Submit Event Report
            </a>
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
          <a href="ChapterReport.php" class="btn btn-primary" style="background: #d97706; border-color: #b45309; padding: 0.4rem 0.85rem; font-size: 0.825rem; text-decoration: none;">
            Submit Report Now
          </a>
        </div>

        <!-- Metric Cards Grid -->
        <div class="metrics-grid">
          <div class="metric-card">
            <div class="metric-icon blue"><i class="ph-fill ph-calendar-check"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Active Chapter Events</h5>
              <p class="metric-value font-roboto" id="metric-active-events">0</p>
              <p class="metric-sub">Events currently scheduled or ongoing</p>
            </div>
          </div>
          
          <div class="metric-card">
            <div class="metric-icon amber"><i class="ph-fill ph-file-arrow-up"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Pending Reports</h5>
              <p class="metric-value font-roboto" id="metric-pending-reports">0</p>
              <p class="metric-sub">Completed events awaiting submission</p>
            </div>
          </div>
          
          <div class="metric-card">
            <div class="metric-icon purple"><i class="ph-fill ph-image"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Poster Requests</h5>
              <p class="metric-value font-roboto" id="metric-posters">0</p>
              <p class="metric-sub">Media requests currently in production</p>
            </div>
          </div>
          
          <div class="metric-card">
            <div class="metric-icon green"><i class="ph-fill ph-trend-up"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Turnout Achievement</h5>
              <p class="metric-value font-roboto" id="metric-turnout-rate">0%</p>
              <p class="metric-sub">Actual turnout vs goal threshold</p>
            </div>
          </div>
        </div>

        <!-- Quick Access Chapter Action Shortcuts -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
          <div style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; padding: 1.25rem; display: flex; align-items: flex-start; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="background: #eff6ff; color: #2563eb; width: 44px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
              <i class="ph ph-file-plus"></i>
            </div>
            <div>
              <h4 style="margin: 0 0 0.25rem; color: #0f172a; font-size: 1rem;">1. Submit Event Proposal</h4>
              <p style="margin: 0 0 0.65rem; color: #64748b; font-size: 0.85rem;">Draft new event plans, set turnout targets, and request budget allocation from National Admin.</p>
              <a href="ChapterProposal.php" class="btn btn-outline" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; text-decoration: none;">Create Proposal &rarr;</a>
            </div>
          </div>

          <div style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; padding: 1.25rem; display: flex; align-items: flex-start; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="background: #fdf4ff; color: #a855f7; width: 44px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
              <i class="ph ph-image"></i>
            </div>
            <div>
              <h4 style="margin: 0 0 0.25rem; color: #0f172a; font-size: 1rem;">2. Request Event Poster</h4>
              <p style="margin: 0 0 0.65rem; color: #64748b; font-size: 0.85rem;">Send concept details to National Media Team for official graphic design and publicity release.</p>
              <a href="ChapterRequest.php" class="btn btn-outline" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; text-decoration: none;">Request Poster &rarr;</a>
            </div>
          </div>

          <div style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; padding: 1.25rem; display: flex; align-items: flex-start; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="background: #f0fdf4; color: #16a34a; width: 44px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
              <i class="ph ph-file-arrow-up"></i>
            </div>
            <div>
              <h4 style="margin: 0 0 0.25rem; color: #0f172a; font-size: 1rem;">3. Submit Event Report</h4>
              <p style="margin: 0 0 0.65rem; color: #64748b; font-size: 0.85rem;">Submit turnout numbers, attendee rosters, budget liquidation, and digital signature.</p>
              <a href="ChapterReport.php" class="btn btn-primary" style="background: #16a34a; border-color: #15803d; padding: 0.35rem 0.75rem; font-size: 0.8rem; text-decoration: none;">Submit Report &rarr;</a>
            </div>
          </div>
        </div>

        <!-- Chapter Events Table -->
        <div class="table-section">
          <div class="table-header">
            <div>
              <h2 class="font-roboto" style="color: var(--primary); font-size: 1.25rem; font-weight: bold; margin: 0;">My Chapter Events & Deadlines</h2>
              <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0.25rem 0 0;">Events scheduled under your chapter branch with current compliance status.</p>
            </div>
            <div class="search-bar" style="margin: 0; min-width: 240px;">
              <i class="ph ph-magnifying-glass"></i>
              <input type="text" id="chapter-events-search" class="font-roboto" placeholder="Search event title..." oninput="renderEventsTable()">
            </div>
          </div>
          
          <div class="table-responsive">
            <table>
              <thead class="font-roboto">
                <tr>
                  <th>Event Title & Type</th>
                  <th>Schedule & Venue</th>
                  <th>Proposal Status</th>
                  <th>Report Status</th>
                  <th>Turnout Threshold</th>
                  <th>Actions</th>
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
        </div>

      </main>
    </div>
  </div>

  <script>
    let activeChapterId = parseInt(localStorage.getItem('ytr_chapter_id') || '1');
    let chapterWorkspaceData = null;

    document.addEventListener('DOMContentLoaded', () => {
      loadChapterList();
      loadWorkspace(activeChapterId);
    });

    async function loadChapterList() {
      try {
        const res = await YTR_API.chapters.getAll();
        if (res.ok && res.data) {
          const sel = document.getElementById('chapter-switch-select');
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
      activeChapterId = parseInt(newId);
      localStorage.setItem('ytr_chapter_id', activeChapterId);
      loadWorkspace(activeChapterId);
    }

    async function loadWorkspace(chapterId) {
      try {
        const res = await YTR_API.chapters.getWorkspace(chapterId);
        if (res.ok && res.data) {
          chapterWorkspaceData = res.data;
          const { chapter, metrics, events } = res.data;

          // Header titles
          document.getElementById('chapter-portal-title').textContent = `${chapter.chapter_name} Workspace`;
          const sidebarCh = document.getElementById('sidebar-chapter-name');
          if (sidebarCh) sidebarCh.textContent = chapter.chapter_name.toUpperCase();

          // Metrics
          document.getElementById('metric-active-events').textContent = metrics.active_events || 0;
          document.getElementById('metric-pending-reports').textContent = metrics.pending_reports || 0;
          document.getElementById('metric-posters').textContent = metrics.posters_in_progress || 0;
          document.getElementById('metric-turnout-rate').textContent = `${metrics.turnout_achievement_pct || 0}%`;

          // Alerts
          if (metrics.pending_reports > 0) {
            document.getElementById('chapter-alert-banner').style.display = 'flex';
            document.getElementById('chapter-alert-text').textContent = 
              `Notice: Your chapter has ${metrics.pending_reports} completed event(s) that require official report and attendee roster submission.`;
          } else {
            document.getElementById('chapter-alert-banner').style.display = 'none';
          }

          renderEventsTable();
        }
      } catch (err) {
        console.error('Failed to load chapter workspace:', err);
      }
    }

    function renderEventsTable() {
      const tbody = document.getElementById('chapter-events-body');
      if (!chapterWorkspaceData || !chapterWorkspaceData.events) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">No events found.</td></tr>';
        return;
      }

      const q = (document.getElementById('chapter-events-search').value || '').toLowerCase().trim();
      const events = chapterWorkspaceData.events.filter(e => {
        if (!q) return true;
        return (e.title && e.title.toLowerCase().includes(q)) || (e.venue && e.venue.toLowerCase().includes(q));
      });

      if (events.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748b;">
              <i class="ph ph-calendar-x" style="font-size: 2rem; color: #94a3b8;"></i>
              <p style="margin-top: 0.5rem;">No events found for this chapter. Click <strong>Submit Proposal</strong> above to add one.</p>
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
          propBadge = `<span class="badge pending"><i class="ph ph-clock"></i> Pending National Review</span>`;
        } else if (e.status === 'draft') {
          propBadge = `<span class="badge draft">Draft</span>`;
        } else {
          propBadge = `<span class="badge">${e.status}</span>`;
        }

        // Report status badge
        let reportBadge = '';
        let actionBtn = '';
        if (e.report_id) {
          reportBadge = `<span class="badge threshold-met"><i class="ph ph-check-circle"></i> Report Submitted</span>`;
          actionBtn = `<a href="ChapterReport.php" class="btn btn-outline" style="padding: 0.35rem 0.65rem; font-size: 0.8rem; text-decoration: none;"><i class="ph ph-file-text"></i> View Report</a>`;
        } else if (e.status === 'approved') {
          reportBadge = `<span class="badge overdue"><i class="ph ph-warning"></i> Report Due</span>`;
          actionBtn = `<a href="ChapterReport.php?event_id=${e.id}" class="btn btn-primary" style="background: #16a34a; border-color: #15803d; padding: 0.35rem 0.65rem; font-size: 0.8rem; text-decoration: none;"><i class="ph ph-file-arrow-up"></i> Submit Report</a>`;
        } else {
          reportBadge = `<span class="badge" style="color: #64748b; background: #f1f5f9;">Awaiting Approval</span>`;
          actionBtn = `<a href="ChapterProposal.php" class="btn btn-outline" style="padding: 0.35rem 0.65rem; font-size: 0.8rem; text-decoration: none;">View Proposal</a>`;
        }

        // Turnout calculation
        const target = e.target_turnout || 100;
        const actual = e.total_participants || 0;
        const pct = target > 0 ? Math.min(100, Math.round((actual / target) * 100)) : 0;
        const turnoutBadge = actual > 0 
          ? `<span class="badge ${pct >= 100 ? 'threshold-met' : 'threshold-warn'}">${actual}/${target} (${pct}%)</span>`
          : `<span class="badge" style="background: #f1f5f9; color: #64748b;">Target: ${target}</span>`;

        return `
          <tr>
            <td>
              <div style="font-weight: 700; color: #0f172a;">${escapeHtml(e.title)}</div>
              <small style="color: #64748b; text-transform: uppercase;">${escapeHtml(e.event_type || 'Gathering')}</small>
            </td>
            <td>
              <div style="font-size: 0.875rem; color: #1e293b;">${escapeHtml(e.start_datetime ? e.start_datetime.substring(0, 10) : 'Date TBD')}</div>
              <small style="color: #64748b;">${escapeHtml(e.venue || 'Venue TBD')}</small>
            </td>
            <td>${propBadge}</td>
            <td>${reportBadge}</td>
            <td>${turnoutBadge}</td>
            <td>${actionBtn}</td>
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
