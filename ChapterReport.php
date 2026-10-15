<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chapter Event Reporting & Attendees | YTR Portal</title>
  
  <!-- Phosphor Icons -->
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  
  <!-- Centralized CSS -->
  <link rel="stylesheet" href="assets/dashboard.css">
  <script src="assets/api-client.js"></script>
</head>
<body>

  <div class="app-wrapper">
    <!-- Dedicated Chapter Sidebar Include -->
    <?php include 'includes/sidebar_chapter.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-wrapper">
      <!-- Topbar Include -->
      <?php include 'includes/topbar.php'; ?>

      <main class="page-content">
        
        <!-- Header -->
        <div class="page-header flex-between" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
          <div>
            <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.4rem;">
              <h1 class="font-roboto" style="margin: 0; color: #0f172a; font-weight: 800;">Submit Event Report & Attendees</h1>
              <span class="portal-indicator chapter"><i class="ph-fill ph-buildings"></i> Chapter Branch Portal</span>
            </div>
            <p style="color: var(--text-muted); margin: 0;">File official post-event documentation, attendee rosters, turnout threshold performance, and digital e-signatures.</p>
          </div>
          
          <div style="display: flex; align-items: center; gap: 0.65rem;">
            <button class="btn btn-primary" style="background: #16a34a; border-color: #15803d;" onclick="openSubmitReportModal()">
              <i class="ph ph-file-arrow-up"></i> File Post-Event Report
            </button>
          </div>
        </div>

        <!-- Role Policy Notice Banner -->
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 0.85rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.85rem;">
          <i class="ph-fill ph-shield-check" style="font-size: 1.5rem; color: #16a34a;"></i>
          <div style="font-size: 0.875rem; color: #166534;">
            <strong>Chapter Administrator Exclusive:</strong> Post-event evaluation dossiers and attendee rosters are submitted exclusively by Chapter Admins. National Admins audit, verify e-signatures, and endorse printable records.
          </div>
        </div>

        <!-- 1. Metric Cards Row -->
        <div class="metrics-grid">
          <div class="metric-card">
            <div class="metric-icon green"><i class="ph-fill ph-file-check"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Reports Submitted</h5>
              <p class="metric-value font-roboto" id="metric-reports-count">0</p>
              <p class="metric-sub">Total official dossiers filed by your chapter</p>
            </div>
          </div>
          
          <div class="metric-card">
            <div class="metric-icon blue"><i class="ph-fill ph-users-three"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Total Youth Reached</h5>
              <p class="metric-value font-roboto" id="metric-total-turnout">0</p>
              <p class="metric-sub">Aggregated attendance across chapter events</p>
            </div>
          </div>
          
          <div class="metric-card">
            <div class="metric-icon purple"><i class="ph-fill ph-sparkle"></i></div>
            <div>
              <h5 class="metric-title font-roboto">First-Timers Welcomed</h5>
              <p class="metric-value font-roboto" id="metric-first-timers">0</p>
              <p class="metric-sub">New guests introduced to fellowship</p>
            </div>
          </div>
          
          <div class="metric-card">
            <div class="metric-icon amber"><i class="ph-fill ph-hourglass-high"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Reports Awaiting Filing</h5>
              <p class="metric-value font-roboto" id="metric-awaiting-reports">0</p>
              <p class="metric-sub">Concluded events pending submission</p>
            </div>
          </div>
        </div>

        <!-- 2. Events Needing Post-Event Reports -->
        <div id="unreported-section" style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; padding: 1.25rem; margin-bottom: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: none;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 class="font-roboto" style="margin: 0; color: #92400e; font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem;">
              <i class="ph-fill ph-warning-circle" style="color: #d97706;"></i> Events Awaiting Submission
            </h3>
            <span class="badge due-soon" id="unreported-badge">Action Needed</span>
          </div>
          <div id="unreported-list" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
            <!-- Populated via JavaScript -->
          </div>
        </div>

        <!-- 3. Submitted Reports Table Section -->
        <div class="table-section">
          <div class="table-header">
            <div>
              <h2 class="font-roboto" style="color: var(--primary); font-size: 1.25rem; font-weight: bold; margin: 0;">My Chapter Submitted Reports</h2>
              <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0.25rem 0 0;">Historical record of event evaluations, turnout performance, and e-signatures.</p>
            </div>
            <div class="search-bar" style="margin: 0; min-width: 240px;">
              <i class="ph ph-magnifying-glass"></i>
              <input type="text" id="report-search" placeholder="Search event title..." oninput="filterReportsTable()">
            </div>
          </div>

          <div class="table-responsive">
            <table>
              <thead class="font-roboto">
                <tr>
                  <th>Event Details</th>
                  <th>Attendance Breakdown</th>
                  <th>Turnout Threshold</th>
                  <th>Budget Spent</th>
                  <th>E-Signature Stamp</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="chapter-reports-tbody">
                <tr>
                  <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                    <i class="ph ph-spinner ph-spin" style="font-size: 1.5rem;"></i> Loading chapter reports...
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- MODAL: SUBMIT POST-EVENT REPORT & ATTENDEES -->
  <div id="submit-report-modal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #ffffff; border-radius: 12px; max-width: 780px; width: 92%; max-height: 92vh; overflow-y: auto; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.25);">
      <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; margin-bottom: 1.25rem;">
        <div>
          <h3 class="font-roboto" style="margin: 0; color: #0f172a; font-size: 1.3rem; font-weight: 700;">
            <i class="ph-fill ph-file-arrow-up" style="color: #16a34a;"></i> File Post-Event Report & Attendees
          </h3>
          <p style="margin: 0.25rem 0 0; color: #64748b; font-size: 0.85rem;">Document attendance turnouts, roster, financial liquidation, and digital signature endorsement.</p>
        </div>
        <button type="button" onclick="closeSubmitReportModal()" style="background: none; border: none; font-size: 1.4rem; cursor: pointer; color: #64748b;">
          <i class="ph ph-x"></i>
        </button>
      </div>

      <form id="chapter-report-form" onsubmit="handleReportFormSubmit(event)">
        <!-- Event Selection -->
        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #0f172a; margin-bottom: 0.35rem;">Select Chapter Event *</label>
          <select id="rep-event-id" required onchange="onSelectEvent(this.value)" style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; font-weight: 600;">
            <option value="">Choose an approved event to report...</option>
          </select>
        </div>

        <!-- Target vs Actual Threshold Indicator -->
        <div id="threshold-meter-card" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; margin-bottom: 1.25rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="font-size: 0.85rem; font-weight: 700; color: #1e293b;">
              <i class="ph ph-chart-line-up"></i> Turnout Target Threshold Performance:
            </span>
            <span id="rep-threshold-badge" class="badge" style="background: #e2e8f0; color: #475569;">Awaiting Turnout Input</span>
          </div>
          <div class="threshold-bar-container" style="background: #e2e8f0; height: 10px; border-radius: 5px; overflow: hidden;">
            <div id="rep-threshold-fill" class="threshold-bar-fill met" style="width: 0%; height: 100%; background: #16a34a; transition: width 0.3s ease;"></div>
          </div>
          <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #64748b; margin-top: 0.35rem;">
            <span>Target Goal: <strong id="rep-target-display">100</strong> attendees</span>
            <span>Current Actual: <strong id="rep-actual-display">0</strong> (<span id="rep-pct-display">0%</span>)</span>
          </div>
        </div>

        <!-- Turnout Breakdown Fields -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; margin-bottom: 1.25rem;">
          <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #0f172a; margin-bottom: 0.25rem;">Total Turnout *</label>
            <input type="number" id="rep-total" required min="1" placeholder="0" oninput="recalcThreshold()" style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-weight: 700; font-size: 1rem; color: #0f172a;">
          </div>
          <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #1e293b; margin-bottom: 0.25rem;">First-Timers *</label>
            <input type="number" id="rep-first-timers" required min="0" placeholder="0" style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem;">
          </div>
          <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #1e293b; margin-bottom: 0.25rem;">Active Members</label>
            <input type="number" id="rep-members" min="0" placeholder="0" style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem;">
          </div>
          <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #1e293b; margin-bottom: 0.25rem;">Youth Leaders</label>
            <input type="number" id="rep-leaders" min="0" placeholder="0" style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem;">
          </div>
        </div>

        <!-- Budget Spent Field -->
        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #0f172a; margin-bottom: 0.35rem;">
            <i class="ph ph-currency-circle-dollar"></i> Actual Budget Liquidated (PHP) *
          </label>
          <input type="number" id="rep-budget-spent" required min="0" step="100" placeholder="e.g. 18500" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem;">
          <small style="color: #64748b; font-size: 0.75rem;">Chapter threshold ceiling is PHP 25,000. Expenses must have physical official receipts.</small>
        </div>

        <!-- Attendee Roster Section -->
        <div style="margin-bottom: 1.25rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <label style="font-size: 0.85rem; font-weight: 700; color: #0f172a; margin: 0;">
              <i class="ph ph-address-book"></i> Attendee Roster Ledger
            </label>
            <button type="button" class="btn btn-outline" onclick="addRosterRow()" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">
              <i class="ph ph-plus"></i> Add Attendee
            </button>
          </div>
          <p style="margin: 0 0 0.75rem; color: #64748b; font-size: 0.8rem;">List attendees, first-timers, and student leaders present at this event.</p>
          <div id="roster-container">
            <div class="roster-grid-row">
              <input type="text" placeholder="Full Name" class="roster-name" style="padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.85rem;">
              <input type="text" placeholder="Contact / Email / School" class="roster-contact" style="padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.85rem;">
              <select class="roster-role" style="padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.85rem;">
                <option value="First-Timer">First-Timer</option>
                <option value="Member">Member</option>
                <option value="Youth Leader">Youth Leader</option>
              </select>
              <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 1.1rem;"><i class="ph ph-trash"></i></button>
            </div>
          </div>
        </div>

        <!-- Narrative Summary -->
        <div style="margin-bottom: 1rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #0f172a; margin-bottom: 0.35rem;">Executive Narrative & Testimonies *</label>
          <textarea id="rep-summary" rows="3" required placeholder="Summarize the event highlights, spiritual breakthroughs, and student responses..." style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;"></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Challenges Encountered</label>
            <textarea id="rep-challenges" rows="2" placeholder="e.g. Venue acoustics, weather disruptions, audio delays..." style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.85rem;"></textarea>
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Recommendations for Next Event</label>
            <textarea id="rep-recommendations" rows="2" placeholder="e.g. Expand seating, earlier tech check-in, more usher volunteers..." style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.85rem;"></textarea>
          </div>
        </div>

        <!-- E-Signature Section -->
        <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <label style="font-size: 0.9rem; font-weight: 700; color: #0f172a; margin: 0;">
              <i class="ph ph-signature"></i> Chapter Admin Digital E-Signature *
            </label>
            <button type="button" class="btn btn-outline" onclick="clearSignatureCanvas()" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">
              <i class="ph ph-eraser"></i> Clear Signature
            </button>
          </div>
          <p style="margin: 0 0 0.5rem; color: #64748b; font-size: 0.8rem;">Please draw your official signature inside the box using mouse or finger touch.</p>
          
          <div style="background: #ffffff; border: 2px dashed #94a3b8; border-radius: 6px; height: 130px; position: relative;">
            <canvas id="chapter-sig-canvas" width="680" height="130" style="width: 100%; height: 100%; cursor: crosshair; display: block;"></canvas>
          </div>

          <div style="margin-top: 0.75rem;">
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #1e293b; margin-bottom: 0.25rem;">Signer Printed Full Name *</label>
            <input type="text" id="rep-signer-name" required placeholder="e.g. Joan Dela Cruz (Chapter Admin)" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.9rem;">
          </div>
        </div>

        <!-- Form Actions -->
        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
          <button type="button" class="btn btn-outline" onclick="closeSubmitReportModal()">Cancel</button>
          <button type="submit" class="btn btn-primary" id="rep-submit-btn" style="background: #16a34a; border-color: #15803d;">
            <i class="ph ph-check-circle"></i> Endorse & Submit Report
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL: PRINT CHAPTER RECEIPT -->
  <div id="print-modal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #ffffff; border-radius: 12px; max-width: 820px; width: 92%; max-height: 92vh; overflow-y: auto; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.25);">
      <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; margin-bottom: 1.25rem;">
        <h3 class="font-roboto" style="margin: 0; color: #0f172a; font-size: 1.25rem; font-weight: 700;">
          <i class="ph ph-printer"></i> Official Post-Event Evaluation Dossier
        </h3>
        <div>
          <button type="button" class="btn btn-primary" onclick="window.print()" style="margin-right: 0.5rem;">
            <i class="ph ph-printer"></i> Print / Save as PDF
          </button>
          <button type="button" onclick="closePrintModal()" style="background: none; border: none; font-size: 1.4rem; cursor: pointer; color: #64748b;">
            <i class="ph ph-x"></i>
          </button>
        </div>
      </div>
      
      <div id="print-dossier-content" class="printable-document">
        <!-- Rendered dynamically -->
      </div>
    </div>
  </div>

  <script>
    let currentUser = null;
    try {
      const raw = localStorage.getItem('ytr_user');
      if (raw) currentUser = JSON.parse(raw);
    } catch(e) {}
    let chapterId = parseInt((currentUser && currentUser.chapter_id) ? currentUser.chapter_id : (localStorage.getItem('ytr_chapter_id') || '1'));
    let chapterReports = [];
    let unreportedEvents = [];
    let selectedEventTarget = 100;

    // Signature Pad State
    let isDrawing = false;
    let canvas, ctx;

    document.addEventListener('DOMContentLoaded', () => {
      initCanvas();
      loadChapterReports();
      loadUnreportedEvents();

      // Check if URL specifies event_id
      const urlParams = new URLSearchParams(window.location.search);
      const preselectedEvent = urlParams.get('event_id');
      if (preselectedEvent) {
        setTimeout(() => {
          openSubmitReportModal();
          const sel = document.getElementById('rep-event-id');
          sel.value = preselectedEvent;
          onSelectEvent(preselectedEvent);
        }, 600);
      }
    });

    function initCanvas() {
      canvas = document.getElementById('chapter-sig-canvas');
      if (!canvas) return;
      ctx = canvas.getContext('2d');
      ctx.strokeStyle = '#0f172a';
      ctx.lineWidth = 2.5;
      ctx.lineCap = 'round';
      ctx.lineJoin = 'round';

      function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return {
          x: (clientX - rect.left) * (canvas.width / rect.width),
          y: (clientY - rect.top) * (canvas.height / rect.height)
        };
      }

      function startDraw(e) {
        e.preventDefault();
        isDrawing = true;
        const p = getPos(e);
        ctx.beginPath();
        ctx.moveTo(p.x, p.y);
      }

      function draw(e) {
        if (!isDrawing) return;
        e.preventDefault();
        const p = getPos(e);
        ctx.lineTo(p.x, p.y);
        ctx.stroke();
      }

      function stopDraw() {
        isDrawing = false;
      }

      canvas.addEventListener('mousedown', startDraw);
      canvas.addEventListener('mousemove', draw);
      canvas.addEventListener('mouseup', stopDraw);
      canvas.addEventListener('mouseleave', stopDraw);

      canvas.addEventListener('touchstart', startDraw, { passive: false });
      canvas.addEventListener('touchmove', draw, { passive: false });
      canvas.addEventListener('touchend', stopDraw);
    }

    function clearSignatureCanvas() {
      if (!ctx || !canvas) return;
      ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    async function loadChapterReports() {
      try {
        const res = await YTR_API.reports.list('all', '');
        if (res.ok && res.data) {
          // Filter to this chapter
          chapterReports = res.data.filter(r => r.chapter_id === chapterId);
          updateMetrics();
          renderReportsTable();
        }
      } catch (err) {
        console.error('Failed to load chapter reports:', err);
      }
    }

    async function loadUnreportedEvents() {
      try {
        const res = await YTR_API.chapters.getUnreportedEvents(chapterId);
        if (res.ok && res.data) {
          unreportedEvents = res.data;
          renderUnreportedSection();
          populateEventDropdown();
        }
      } catch (err) {
        console.error('Failed to load unreported events:', err);
      }
    }

    function updateMetrics() {
      const reportsCount = chapterReports.length;
      const totalTurnout = chapterReports.reduce((s, r) => s + (r.total_participants || 0), 0);
      const firstTimers = chapterReports.reduce((s, r) => s + (r.first_timers || 0), 0);
      const awaiting = unreportedEvents.length;

      document.getElementById('metric-reports-count').textContent = reportsCount;
      document.getElementById('metric-total-turnout').textContent = totalTurnout.toLocaleString();
      document.getElementById('metric-first-timers').textContent = firstTimers.toLocaleString();
      document.getElementById('metric-awaiting-reports').textContent = awaiting;
    }

    function renderUnreportedSection() {
      const section = document.getElementById('unreported-section');
      const list = document.getElementById('unreported-list');
      if (unreportedEvents.length === 0) {
        section.style.display = 'none';
        return;
      }

      section.style.display = 'block';
      document.getElementById('unreported-badge').textContent = `${unreportedEvents.length} Event(s) Awaiting Report`;

      list.innerHTML = unreportedEvents.map(e => `
        <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 1rem; display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <h4 style="margin: 0 0 0.35rem; color: #92400e; font-size: 0.95rem;">${escapeHtml(e.title)}</h4>
            <div style="font-size: 0.8rem; color: #78350f; margin-bottom: 0.5rem;">
              <i class="ph ph-calendar"></i> ${escapeHtml(e.start_datetime ? e.start_datetime.substring(0, 10) : 'Date TBD')}<br>
              <i class="ph ph-map-pin"></i> ${escapeHtml(e.venue || 'Venue TBD')}<br>
              <i class="ph ph-users"></i> Target Goal: <strong>${e.target_turnout || 100} attendees</strong>
            </div>
          </div>
          <button class="btn btn-primary" style="background: #d97706; border-color: #b45309; padding: 0.4rem 0.75rem; font-size: 0.8rem; width: 100%;" onclick="startReportForEvent(${e.id})">
            <i class="ph ph-file-arrow-up"></i> File Report Now
          </button>
        </div>
      `).join('');
    }

    function populateEventDropdown() {
      const sel = document.getElementById('rep-event-id');
      sel.innerHTML = '<option value="">Choose an approved event to report...</option>';
      unreportedEvents.forEach(e => {
        const opt = document.createElement('option');
        opt.value = e.id;
        opt.textContent = `${e.title} (Target: ${e.target_turnout || 100})`;
        opt.dataset.target = e.target_turnout || 100;
        sel.appendChild(opt);
      });
    }

    function startReportForEvent(eventId) {
      openSubmitReportModal();
      const sel = document.getElementById('rep-event-id');
      sel.value = eventId;
      onSelectEvent(eventId);
    }

    function onSelectEvent(val) {
      const ev = unreportedEvents.find(e => e.id == val);
      if (ev) {
        selectedEventTarget = ev.target_turnout || 100;
      } else {
        selectedEventTarget = 100;
      }
      document.getElementById('rep-target-display').textContent = selectedEventTarget;
      recalcThreshold();
    }

    function recalcThreshold() {
      const actual = parseInt(document.getElementById('rep-total').value || '0');
      document.getElementById('rep-actual-display').textContent = actual;

      const pct = selectedEventTarget > 0 ? Math.round((actual / selectedEventTarget) * 100) : 0;
      document.getElementById('rep-pct-display').textContent = `${pct}%`;

      const fill = document.getElementById('rep-threshold-fill');
      const badge = document.getElementById('rep-threshold-badge');

      fill.style.width = `${Math.min(100, pct)}%`;

      if (actual === 0) {
        badge.className = 'badge';
        badge.style.background = '#e2e8f0';
        badge.style.color = '#475569';
        badge.textContent = 'Awaiting Input';
      } else if (pct >= 100) {
        badge.className = 'badge threshold-met';
        badge.innerHTML = `<i class="ph ph-check-circle"></i> Threshold Met (${pct}%)`;
        fill.style.background = '#16a34a';
      } else if (pct >= 70) {
        badge.className = 'badge';
        badge.style.background = '#fef3c7';
        badge.style.color = '#92400e';
        badge.innerHTML = `Adequate (${pct}%)`;
        fill.style.background = '#d97706';
      } else {
        badge.className = 'badge threshold-warn';
        badge.innerHTML = `<i class="ph ph-warning"></i> Below Threshold (${pct}%)`;
        fill.style.background = '#dc2626';
      }
    }

    function addRosterRow() {
      const c = document.getElementById('roster-container');
      const div = document.createElement('div');
      div.className = 'roster-grid-row';
      div.innerHTML = `
        <input type="text" placeholder="Full Name" class="roster-name" style="padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.85rem;">
        <input type="text" placeholder="Contact / Email / School" class="roster-contact" style="padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.85rem;">
        <select class="roster-role" style="padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.85rem;">
          <option value="First-Timer">First-Timer</option>
          <option value="Member">Member</option>
          <option value="Youth Leader">Youth Leader</option>
        </select>
        <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 1.1rem;"><i class="ph ph-trash"></i></button>
      `;
      c.appendChild(div);
    }

    function filterReportsTable() {
      renderReportsTable();
    }

    function renderReportsTable() {
      const tbody = document.getElementById('chapter-reports-tbody');
      const q = (document.getElementById('report-search').value || '').toLowerCase().trim();

      const filtered = chapterReports.filter(r => {
        if (!q) return true;
        return (r.event_title && r.event_title.toLowerCase().includes(q)) || (r.signed_by_name && r.signed_by_name.toLowerCase().includes(q));
      });

      if (filtered.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748b;">
              <i class="ph ph-file-dashed" style="font-size: 2rem; color: #94a3b8;"></i>
              <p style="margin-top: 0.5rem;">No submitted reports matching search. Click <strong>File Post-Event Report</strong> to submit one.</p>
            </td>
          </tr>
        `;
        return;
      }

      tbody.innerHTML = filtered.map(r => {
        const thresholdPct = r.target_turnout > 0 ? Math.round((r.total_participants / r.target_turnout) * 100) : 100;
        const thresholdMet = thresholdPct >= 100;
        const threshBadge = thresholdMet 
          ? `<span class="badge threshold-met"><i class="ph ph-check-circle"></i> ${thresholdPct}% Met</span>`
          : `<span class="badge threshold-warn"><i class="ph ph-warning"></i> ${thresholdPct}% Under</span>`;

        const sigHtml = r.e_signature 
          ? `<img src="${r.e_signature}" class="signature-preview-img" alt="Digital Sig"><br><small style="color: #64748b;">${escapeHtml(r.signed_by_name || 'Signed')}</small>`
          : `<span class="badge" style="background: #f1f5f9; color: #64748b;">Pending E-Sign</span>`;

        return `
          <tr>
            <td>
              <div style="font-weight: 700; color: #0f172a;">${escapeHtml(r.event_title)}</div>
              <small style="color: #64748b;"><i class="ph ph-calendar"></i> ${escapeHtml(r.submitted_at ? r.submitted_at.substring(0, 10) : 'Recent')}</small>
            </td>
            <td>
              <div style="font-size: 0.9rem; font-weight: 700; color: #0f172a;">${r.total_participants} Total</div>
              <small style="color: #64748b;">
                First-Timers: <strong>${r.first_timers || 0}</strong> | Leaders: <strong>${r.youth_leaders_count || 0}</strong>
              </small>
            </td>
            <td>
              ${threshBadge}
              <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Goal: ${r.target_turnout || 100}</div>
            </td>
            <td>
              <span style="font-weight: 600; color: #0f172a;">PHP ${(r.budget_spent || 0).toLocaleString()}</span>
            </td>
            <td>${sigHtml}</td>
            <td>
              <button class="btn btn-outline" style="padding: 0.35rem 0.65rem; font-size: 0.8rem;" onclick="viewPrintDossier(${r.id})">
                <i class="ph ph-printer"></i> Print Receipt
              </button>
            </td>
          </tr>
        `;
      }).join('');
    }

    function openSubmitReportModal() {
      document.getElementById('submit-report-modal').style.display = 'flex';
      if (currentUser && currentUser.full_name) {
        const signerInput = document.getElementById('rep-signer-name');
        if (signerInput && !signerInput.value) {
          signerInput.value = currentUser.full_name;
        }
      }
      setTimeout(() => {
        clearSignatureCanvas();
      }, 100);
    }

    function closeSubmitReportModal() {
      document.getElementById('submit-report-modal').style.display = 'none';
    }

    async function handleReportFormSubmit(e) {
      e.preventDefault();
      const btn = document.getElementById('rep-submit-btn');
      btn.disabled = true;
      btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Endorsing & Submitting...';

      // Export E-Signature from canvas
      const sigData = canvas ? canvas.toDataURL('image/png') : '';

      // Build attendee roster array
      const rosterRows = document.querySelectorAll('#roster-container .roster-grid-row');
      const rosterList = [];
      rosterRows.forEach(row => {
        const name = row.querySelector('.roster-name')?.value.trim();
        const contact = row.querySelector('.roster-contact')?.value.trim();
        const role = row.querySelector('.roster-role')?.value;
        if (name) {
          rosterList.push({ name, contact: contact || 'N/A', role: role || 'Member' });
        }
      });

      const payload = {
        event_id: parseInt(document.getElementById('rep-event-id').value),
        chapter_id: chapterId,
        submitted_by: currentUser ? currentUser.id : null,
        total_participants: parseInt(document.getElementById('rep-total').value),
        first_timers: parseInt(document.getElementById('rep-first-timers').value),
        existing_members: parseInt(document.getElementById('rep-members').value || '0'),
        youth_leaders_count: parseInt(document.getElementById('rep-leaders').value || '0'),
        budget_spent: parseFloat(document.getElementById('rep-budget-spent').value || '0'),
        report_summary: document.getElementById('rep-summary').value.trim(),
        challenges: document.getElementById('rep-challenges').value.trim(),
        recommendations: document.getElementById('rep-recommendations').value.trim(),
        attendee_roster: JSON.stringify(rosterList),
        e_signature: sigData,
        signed_by_name: document.getElementById('rep-signer-name').value.trim()
      };

      try {
        const res = await YTR_API.reports.submit(payload);
        if (res.ok) {
          alert('Report and attendee roster submitted and digitally signed successfully! National Admin has been notified.');
          document.getElementById('chapter-report-form').reset();
          closeSubmitReportModal();
          loadChapterReports();
          loadUnreportedEvents();
        } else {
          alert('Submission error: ' + (res.message || 'Failed to submit report.'));
        }
      } catch (err) {
        console.error('Error submitting report:', err);
        alert('Network error connecting to Python API.');
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="ph ph-check-circle"></i> Endorse & Submit Report';
      }
    }

    async function viewPrintDossier(reportId) {
      try {
        const res = await YTR_API.reports.getById(reportId);
        if (res.ok && res.data) {
          const r = res.data;
          renderPrintDocument(r);
          document.getElementById('print-modal').style.display = 'flex';
        }
      } catch (err) {
        console.error('Failed to load report for print:', err);
      }
    }

    function renderPrintDocument(r) {
      let roster = [];
      try {
        if (r.attendee_roster) roster = JSON.parse(r.attendee_roster);
      } catch (e) {}

      const container = document.getElementById('print-dossier-content');
      container.innerHTML = `
        <div style="border-bottom: 2px solid #0f172a; padding-bottom: 1rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-start;">
          <div>
            <h2 style="margin: 0; color: #0f172a; font-size: 1.4rem; text-transform: uppercase; letter-spacing: 0.05em;">Youth on the Rock National Movement</h2>
            <h4 style="margin: 0.25rem 0 0; color: #475569; font-size: 1rem;">Official Post-Event Evaluation Dossier & Attendees Record</h4>
          </div>
          <div style="text-align: right;">
            <span class="badge approved" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">OFFICIALLY SUBMITTED</span>
            <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">Date: ${escapeHtml(r.submitted_at ? r.submitted_at.substring(0, 10) : 'Recent')}</div>
          </div>
        </div>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; font-size: 0.9rem;">
          <tr style="border-bottom: 1px solid #e2e8f0;">
            <td style="padding: 0.5rem 0; width: 140px; color: #64748b;"><strong>Chapter:</strong></td>
            <td style="padding: 0.5rem 0; font-weight: 700;">${escapeHtml(r.chapter_name)} (${escapeHtml(r.chapter_code)})</td>
            <td style="padding: 0.5rem 0; width: 140px; color: #64748b;"><strong>Event Type:</strong></td>
            <td style="padding: 0.5rem 0;">Gathering</td>
          </tr>
          <tr style="border-bottom: 1px solid #e2e8f0;">
            <td style="padding: 0.5rem 0; color: #64748b;"><strong>Event Title:</strong></td>
            <td style="padding: 0.5rem 0; font-weight: 700; color: #0f172a;">${escapeHtml(r.event_title)}</td>
            <td style="padding: 0.5rem 0; color: #64748b;"><strong>Venue:</strong></td>
            <td style="padding: 0.5rem 0;">${escapeHtml(r.venue || 'N/A')}</td>
          </tr>
        </table>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem;">
          <h4 style="margin: 0 0 0.75rem; color: #0f172a; font-size: 1rem;">Attendance Breakdown & Performance Metrics</h4>
          <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; text-align: center;">
            <div style="background: #fff; padding: 0.75rem; border-radius: 6px; border: 1px solid #e2e8f0;">
              <span style="font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Total Turnout</span>
              <div style="font-size: 1.3rem; font-weight: 800; color: #0f172a;">${r.total_participants}</div>
              <small style="color: #16a34a; font-weight: 600;">Goal: ${r.target_turnout || 100}</small>
            </div>
            <div style="background: #fff; padding: 0.75rem; border-radius: 6px; border: 1px solid #e2e8f0;">
              <span style="font-size: 0.75rem; color: #64748b; text-transform: uppercase;">First-Timers</span>
              <div style="font-size: 1.3rem; font-weight: 800; color: #7c3aed;">${r.first_timers || 0}</div>
            </div>
            <div style="background: #fff; padding: 0.75rem; border-radius: 6px; border: 1px solid #e2e8f0;">
              <span style="font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Members</span>
              <div style="font-size: 1.3rem; font-weight: 800; color: #0284c7;">${r.existing_members || 0}</div>
            </div>
            <div style="background: #fff; padding: 0.75rem; border-radius: 6px; border: 1px solid #e2e8f0;">
              <span style="font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Budget Liquidated</span>
              <div style="font-size: 1.2rem; font-weight: 800; color: #0f172a;">PHP ${(r.budget_spent || 0).toLocaleString()}</div>
            </div>
          </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
          <h4 style="margin: 0 0 0.5rem; color: #0f172a;">Executive Summary & Testimonies</h4>
          <p style="margin: 0; font-size: 0.9rem; line-height: 1.6; color: #334155; white-space: pre-line;">${escapeHtml(r.report_summary || 'No summary provided.')}</p>
        </div>

        ${roster.length > 0 ? `
          <div style="margin-bottom: 1.5rem;">
            <h4 style="margin: 0 0 0.5rem; color: #0f172a;">Submitted Attendees Roster (${roster.length})</h4>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; border: 1px solid #e2e8f0;">
              <thead style="background: #f1f5f9;">
                <tr>
                  <th style="padding: 6px; text-align: left; border: 1px solid #e2e8f0;">#</th>
                  <th style="padding: 6px; text-align: left; border: 1px solid #e2e8f0;">Name</th>
                  <th style="padding: 6px; text-align: left; border: 1px solid #e2e8f0;">Contact / School</th>
                  <th style="padding: 6px; text-align: left; border: 1px solid #e2e8f0;">Category</th>
                </tr>
              </thead>
              <tbody>
                ${roster.map((att, i) => `
                  <tr>
                    <td style="padding: 6px; border: 1px solid #e2e8f0;">${i + 1}</td>
                    <td style="padding: 6px; border: 1px solid #e2e8f0; font-weight: 600;">${escapeHtml(att.name)}</td>
                    <td style="padding: 6px; border: 1px solid #e2e8f0;">${escapeHtml(att.contact)}</td>
                    <td style="padding: 6px; border: 1px solid #e2e8f0;"><span class="badge" style="background: #f1f5f9; color: #334155;">${escapeHtml(att.role)}</span></td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        ` : ''}

        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 2.5rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0;">
          <div>
            <div style="font-size: 0.8rem; color: #64748b;">Authenticated Digital Submission Stamp</div>
            <div style="font-size: 0.75rem; color: #94a3b8;">YTR Verified Digital Audit Ledger · ${r.signed_at || 'Verified'}</div>
          </div>
          <div style="text-align: center;">
            ${r.e_signature ? `<img src="${r.e_signature}" style="max-height: 80px; max-width: 200px; display: block; margin: 0 auto 0.5rem;" alt="E-Signature">` : '<div style="height: 60px;"></div>'}
            <div style="border-top: 1.5px solid #0f172a; width: 220px; padding-top: 0.35rem; font-weight: 700; font-size: 0.9rem;">
              ${escapeHtml(r.signed_by_name || 'Chapter Administrator')}
            </div>
            <div style="font-size: 0.8rem; color: #64748b;">Chapter Administrator / Signer</div>
          </div>
        </div>
      `;
    }

    function closePrintModal() {
      document.getElementById('print-modal').style.display = 'none';
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
