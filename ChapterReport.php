<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chapter Event Reporting & Attendees | YTR Portal</title>
  
  <!-- Phosphor Icons -->
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.2/src/regular/style.css">
  <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.2/src/fill/style.css">
  <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.2/src/bold/style.css">
  
  <!-- Centralized CSS -->
  <link rel="stylesheet" href="assets/Chapter.css">
  <script src="assets/api-client.js"></script>
  <style>
    @media print {
      @page {
        size: A4 portrait;
        margin: 12mm 15mm;
      }
      html, body {
        background: #ffffff !important;
        color: #000000 !important;
        height: auto !important;
        min-height: 100% !important;
        overflow: visible !important;
        margin: 0 !important;
        padding: 0 !important;
      }
      .app-wrapper,
      .sidebar,
      .topbar,
      .page-header,
      .tabs-container,
      .search-bar,
      .print-no-print,
      button,
      .btn {
        display: none !important;
      }

      /* Unhide the print receipt modal and render it as flat normal document flow */
      #print-modal {
        display: block !important;
        position: static !important;
        inset: auto !important;
        width: 100% !important;
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
        background: transparent !important;
        padding: 0 !important;
        margin: 0 !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        z-index: auto !important;
      }
      #print-modal > div {
        display: block !important;
        max-width: 100% !important;
        width: 100% !important;
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
        padding: 0 !important;
        margin: 0 !important;
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
      }
      #print-modal .print-no-print,
      #print-modal button {
        display: none !important;
      }

      .printable-document {
        display: block !important;
        position: static !important;
        width: 100% !important;
        height: auto !important;
        overflow: visible !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        background: #ffffff !important;
        font-family: 'Roboto', sans-serif !important;
        page-break-inside: auto;
      }
      .printable-document,
      .printable-document * {
        visibility: visible !important;
        color: #000000 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
      .printable-document table {
        width: 100% !important;
        border-collapse: collapse !important;
        page-break-inside: auto;
      }
      .printable-document tr {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
      }
      .printable-document th,
      .printable-document td {
        border: 1px solid #94a3b8 !important;
        color: #000000 !important;
        padding: 6px 10px !important;
      }
      .print-signatures-row {
        display: flex !important;
        flex-direction: row !important;
        justify-content: space-between !important;
        gap: 3rem !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        margin-top: 2rem !important;
        padding-top: 1.25rem !important;
        border-top: 2px solid #000000 !important;
        visibility: visible !important;
      }
      .print-signatures-row > div {
        width: 45% !important;
        min-height: auto !important;
        visibility: visible !important;
      }
    }
  </style>
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
            <div class="metric-icon green"><i class="ph-fill ph-files"></i></div>
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
                  <th>Submission Status</th>
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

          <!-- Official Attendance Sheet Document Upload -->
          <div style="margin-top: 1rem; padding-top: 0.85rem; border-top: 1px dashed #cbd5e1;">
            <label style="display: block; font-size: 0.825rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
              <i class="ph ph-file-arrow-up" style="color: #2563eb;"></i> Official Attendance Sheet File Upload (Optional / Recommended)
            </label>
            <div style="border: 1.5px dashed #94a3b8; border-radius: 6px; background: #f8fafc; padding: 0.85rem; text-align: center; position: relative;">
              <input type="file" id="rep-attendance-sheet" accept=".pdf,.xlsx,.xls,.csv,.docx,.doc,image/*" onchange="handleAttendanceSheetSelect(event)" style="position: absolute; inset: 0; opacity: 0; width: 100%; height: 100%; cursor: pointer;">
              <div id="att-sheet-idle-msg">
                <i class="ph ph-cloud-arrow-up" style="font-size: 1.6rem; color: #3b82f6;"></i>
                <div style="font-size: 0.825rem; font-weight: 600; color: #1e293b; margin-top: 2px;">
                  Click or drag signed attendance sheet / sign-in log here
                </div>
                <small style="color: #64748b; font-size: 0.72rem;">PDF, Excel (.xlsx/.csv), Word, or scanned photo of physical attendance sheet (Max 25MB)</small>
              </div>
              <div id="att-sheet-selected-msg" style="display: none; align-items: center; justify-content: center; gap: 0.5rem;">
                <i class="ph-bold ph-file-text" style="font-size: 1.4rem; color: #16a34a;"></i>
                <span id="att-sheet-filename" style="font-size: 0.85rem; font-weight: 700; color: #0f172a;"></span>
                <button type="button" onclick="clearAttendanceSheet(event)" style="background: none; border: none; color: #ef4444; font-size: 0.9rem; cursor: pointer; padding: 2px 6px;" title="Remove file"><i class="ph ph-x-circle"></i> Remove</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Google Drive Photo Album & Documentation Link -->
        <div style="margin-bottom: 1.25rem; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 1rem;">
          <label style="display: flex; align-items: center; gap: 0.45rem; font-size: 0.85rem; font-weight: 700; color: #0369a1; margin-bottom: 0.35rem;">
            <i class="ph-bold ph-google-drive-logo" style="font-size: 1.2rem; color: #0284c7;"></i>
            <span>Google Drive / Cloud Album Link (Event Pictures)</span>
          </label>
          <div style="position: relative;">
            <input type="url" id="rep-drive-link" placeholder="https://drive.google.com/drive/folders/... (Google Drive, OneDrive, or Dropbox folder)" style="width: 100%; padding: 0.6rem 0.75rem 0.6rem 2.4rem; border: 1px solid #7dd3fc; border-radius: 6px; font-size: 0.875rem; background: #ffffff; color: #0f172a;">
            <div style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #0284c7; pointer-events: none;">
              <i class="ph-bold ph-link"></i>
            </div>
          </div>
          <small style="color: #0369a1; font-size: 0.75rem; margin-top: 0.35rem; display: block;">
            Submit your shared Google Drive folder link containing event photos, stage shots, attendee group pictures, and video clips for national media and archiving.
          </small>
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

        <!-- Submitter Information Section with Signature (Draw or Upload) -->
        <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">
            <i class="ph ph-user-check"></i> Submitting Chapter Coordinator / Administrator Full Name *
          </label>
          <input type="text" id="rep-signer-name" required placeholder="e.g. Joan Dela Cruz (Chapter Admin)" style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
          <small style="color: #64748b; font-size: 0.75rem; margin-top: 4px; display: block;">Official endorsement by authorized chapter personnel.</small>

          <div style="margin-top: 1.1rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
              <label style="font-size: 0.825rem; font-weight: 700; color: #1e293b; margin: 0;">
                <i class="ph ph-signature"></i> Submitter Signature Endorsement *
              </label>
              <!-- Mode Tabs -->
              <div style="display: flex; gap: 0.35rem;">
                <button type="button" id="tabChapDrawSig" class="btn btn-primary btn-sm font-roboto" onclick="switchChapterSigMode('draw')" style="font-size: 0.75rem; padding: 0.25rem 0.6rem;">
                  <i class="ph ph-pencil-line"></i> Sign on Pad
                </button>
                <button type="button" id="tabChapUploadSig" class="btn btn-outline btn-sm font-roboto" onclick="switchChapterSigMode('upload')" style="font-size: 0.75rem; padding: 0.25rem 0.6rem;">
                  <i class="ph ph-upload-simple"></i> Upload Signature Image
                </button>
              </div>
            </div>

            <!-- Mode 1: Sign on Pad -->
            <div id="chapSigDrawPanel">
              <div style="border: 1px solid #cbd5e1; border-radius: 6px; background: #ffffff; padding: 2px;">
                <canvas id="chapter-sig-canvas" width="600" height="130" style="width: 100%; height: 130px; display: block; cursor: crosshair; touch-action: none; border-radius: 4px;"></canvas>
              </div>
              <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                <small style="color: #64748b; font-size: 0.75rem;">Draw signature above using mouse, stylus, or touch screen.</small>
                <button type="button" class="btn btn-outline btn-sm font-roboto" onclick="clearSignatureCanvas()" style="font-size: 0.75rem; padding: 0.15rem 0.5rem;">
                  <i class="ph ph-arrow-counter-clockwise"></i> Clear Pad
                </button>
              </div>
            </div>

            <!-- Mode 2: Upload Signature Image -->
            <div id="chapSigUploadPanel" style="display: none;">
              <div style="border: 1.5px dashed #94a3b8; border-radius: 6px; background: #ffffff; padding: 1rem; text-align: center; position: relative;">
                <input type="file" id="rep-signature-file" accept="image/png, image/jpeg, image/webp" onchange="handleChapterSigFileSelect(event)" style="position: absolute; inset: 0; opacity: 0; width: 100%; height: 100%; cursor: pointer;">
                <div id="chap-sig-upload-idle">
                  <i class="ph ph-image" style="font-size: 1.8rem; color: #16a34a;"></i>
                  <div style="font-size: 0.825rem; font-weight: 600; color: #1e293b; margin-top: 2px;">
                    Click or drag your signature image here
                  </div>
                  <small style="color: #64748b; font-size: 0.72rem;">PNG transparent background or clean white JPG recommended</small>
                </div>
                <div id="chap-sig-upload-preview" style="display: none; flex-direction: column; align-items: center; gap: 0.35rem;">
                  <img id="chap-sig-preview-img" src="" alt="Signature Preview" style="max-height: 60px; max-width: 220px; object-fit: contain; border-bottom: 1.5px solid #0f172a; padding-bottom: 2px;">
                  <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 4px;">
                    <span id="chap-sig-filename" style="font-size: 0.78rem; font-weight: 600; color: #0f172a;"></span>
                    <button type="button" onclick="clearChapterSigUpload(event)" style="background: none; border: none; color: #ef4444; font-size: 0.85rem; cursor: pointer;" title="Remove image"><i class="ph ph-x-circle"></i> Change</button>
                  </div>
                </div>
              </div>
            </div>

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
      <div class="print-no-print" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; margin-bottom: 1.25rem;">
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

    // Signature & Attendance Sheet State
    let activeChapSigMode = 'draw';
    let uploadedChapSigFile = null;
    let uploadedChapSigBase64 = null;
    let selectedAttendanceSheetFile = null;
    let isDrawing = false;
    let hasDrawnOnCanvas = false;
    let canvas, ctx;
    let nationalExecSignature = null;
    let nationalExecName = 'Hamilton Jones Dela';

    function switchChapterSigMode(mode) {
      activeChapSigMode = mode;
      const tabDraw = document.getElementById('tabChapDrawSig');
      const tabUpload = document.getElementById('tabChapUploadSig');
      const panelDraw = document.getElementById('chapSigDrawPanel');
      const panelUpload = document.getElementById('chapSigUploadPanel');

      if (mode === 'draw') {
        tabDraw.className = 'btn btn-primary btn-sm font-roboto';
        tabUpload.className = 'btn btn-outline btn-sm font-roboto';
        panelDraw.style.display = 'block';
        panelUpload.style.display = 'none';
        setTimeout(initCanvas, 50);
      } else {
        tabDraw.className = 'btn btn-outline btn-sm font-roboto';
        tabUpload.className = 'btn btn-primary btn-sm font-roboto';
        panelDraw.style.display = 'none';
        panelUpload.style.display = 'block';
      }
    }

    function handleChapterSigFileSelect(e) {
      const file = e.target.files && e.target.files[0];
      if (!file) return;
      if (!file.type.startsWith('image/')) {
        alert('Please select a valid image file (PNG, JPG, or WEBP).');
        return;
      }
      uploadedChapSigFile = file;
      const reader = new FileReader();
      reader.onload = function(evt) {
        uploadedChapSigBase64 = evt.target.result;
        const previewImg = document.getElementById('chap-sig-preview-img');
        const fnameEl = document.getElementById('chap-sig-filename');
        const idleEl = document.getElementById('chap-sig-upload-idle');
        const previewBox = document.getElementById('chap-sig-upload-preview');

        if (previewImg) previewImg.src = uploadedChapSigBase64;
        if (fnameEl) fnameEl.textContent = file.name;
        if (idleEl) idleEl.style.display = 'none';
        if (previewBox) previewBox.style.display = 'flex';
      };
      reader.readAsDataURL(file);
    }

    function clearChapterSigUpload(e) {
      if (e) e.stopPropagation();
      uploadedChapSigFile = null;
      uploadedChapSigBase64 = null;
      const fileInput = document.getElementById('rep-signature-file');
      if (fileInput) fileInput.value = '';
      const previewImg = document.getElementById('chap-sig-preview-img');
      if (previewImg) previewImg.src = '';
      const fnameEl = document.getElementById('chap-sig-filename');
      if (fnameEl) fnameEl.textContent = '';
      const idleEl = document.getElementById('chap-sig-upload-idle');
      if (idleEl) idleEl.style.display = 'block';
      const previewBox = document.getElementById('chap-sig-upload-preview');
      if (previewBox) previewBox.style.display = 'none';
    }

    function handleAttendanceSheetSelect(e) {
      const file = e.target.files && e.target.files[0];
      if (!file) return;
      if (file.size > 25 * 1024 * 1024) {
        alert('Attendance sheet file exceeds 25MB limit.');
        return;
      }
      selectedAttendanceSheetFile = file;
      const sizeKb = (file.size / 1024).toFixed(1);
      const fnameEl = document.getElementById('att-sheet-filename');
      if (fnameEl) fnameEl.textContent = `${file.name} (${sizeKb} KB)`;
      const idleMsg = document.getElementById('att-sheet-idle-msg');
      if (idleMsg) idleMsg.style.display = 'none';
      const selMsg = document.getElementById('att-sheet-selected-msg');
      if (selMsg) selMsg.style.display = 'flex';
    }

    function clearAttendanceSheet(e) {
      if (e) e.stopPropagation();
      selectedAttendanceSheetFile = null;
      const fileInput = document.getElementById('rep-attendance-sheet');
      if (fileInput) fileInput.value = '';
      const fnameEl = document.getElementById('att-sheet-filename');
      if (fnameEl) fnameEl.textContent = '';
      const idleMsg = document.getElementById('att-sheet-idle-msg');
      if (idleMsg) idleMsg.style.display = 'block';
      const selMsg = document.getElementById('att-sheet-selected-msg');
      if (selMsg) selMsg.style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
      initCanvas();
      loadExecSignatories();
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

    async function loadExecSignatories() {
      try {
        const res = await YTR_API.reports.getSignatories();
        if (res.status === 'success' && res.data) {
          nationalExecName = res.data.chief_executive_name || 'Hamilton Jones Dela';
          if (res.data.chief_executive_signature) {
            nationalExecSignature = res.data.chief_executive_signature;
          }
        }
      } catch(e) {}
      if (!nationalExecSignature) {
        nationalExecSignature = localStorage.getItem('ytr_national_signature') || null;
      }
    }

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
        hasDrawnOnCanvas = true;
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
      hasDrawnOnCanvas = false;
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

    function getProgramBadge(program) {
      if (!program) return '';
      let pBg = '#eff6ff', pCol = '#1d4ed8', pBorder = '#bfdbfe';
      if (program.startsWith('Rise up')) {
        pBg = '#fffbeb'; pCol = '#b45309'; pBorder = '#fde68a';
      } else if (program.startsWith('Build up')) {
        pBg = '#f0fdf4'; pCol = '#15803d'; pBorder = '#bbf7d0';
      } else if (program.startsWith('Gear up')) {
        pBg = '#faf5ff'; pCol = '#7e22ce'; pBorder = '#e9d5ff';
      }
      return `<span class="badge" style="background: ${pBg}; color: ${pCol}; border: 1px solid ${pBorder}; font-size: 0.72rem; padding: 2px 7px; font-weight: 700;">
        <i class="ph-bold ph-compass"></i> ${escapeHtml(program)}
      </span>`;
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
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem; margin-bottom: 0.35rem;">
              <h4 style="margin: 0; color: #92400e; font-size: 0.95rem;">${escapeHtml(e.title)}</h4>
            </div>
            <div style="margin-bottom: 0.5rem;">
              ${getProgramBadge(e.ytr_program || 'Lead up (Youth Leadership)')}
            </div>
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
        const progShort = e.ytr_program ? e.ytr_program.split('(')[0].trim() : 'Lead up';
        opt.textContent = `${e.title} [${progShort}] (Target: ${e.target_turnout || 100})`;
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

        const sigHtml = `<span class="badge" style="background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;"><i class="ph ph-check-circle"></i> Filed</span><br><small style="color: #64748b;">${escapeHtml(r.signed_by_name || 'Chapter Admin')}</small>`;

        const sheetBadge = r.attendance_sheet_path
          ? `<div style="margin-top: 4px;"><a href="${YTR_API.reports.getAttendanceSheetUrl(r.id)}" target="_blank" class="badge" style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 0.7rem; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;" title="Download Attendance Sheet Document"><i class="ph ph-file-arrow-down"></i> Sheet Attached</a></div>`
          : '';

        const driveBadge = r.photos_drive_link
          ? `<div style="margin-top: 4px;"><a href="${escapeHtml(r.photos_drive_link)}" target="_blank" rel="noopener noreferrer" class="badge" style="background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; font-size: 0.7rem; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;" title="Open Event Photos Drive Folder"><i class="ph-bold ph-google-drive-logo"></i> Photos Drive</a></div>`
          : '';

        return `
          <tr>
            <td>
              <div style="font-weight: 700; color: #0f172a;">${escapeHtml(r.event_title)}</div>
              <div style="display: flex; gap: 0.35rem; align-items: center; flex-wrap: wrap; margin-top: 3px; margin-bottom: 3px;">
                ${getProgramBadge(r.ytr_program || 'Lead up (Youth Leadership)')}
              </div>
              <small style="color: #64748b;"><i class="ph ph-calendar"></i> ${escapeHtml(r.submitted_at ? r.submitted_at.substring(0, 10) : 'Recent')}</small>
            </td>
            <td>
              <div style="font-size: 0.9rem; font-weight: 700; color: #0f172a;">${r.total_participants} Total</div>
              <small style="color: #64748b;">
                First-Timers: <strong>${r.first_timers || 0}</strong> | Leaders: <strong>${r.youth_leaders_count || 0}</strong>
              </small>
              ${sheetBadge}
              ${driveBadge}
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
      clearSignatureCanvas();
      clearChapterSigUpload();
      clearAttendanceSheet();
      switchChapterSigMode('draw');
      const driveInput = document.getElementById('rep-drive-link');
      if (driveInput) driveInput.value = '';
      if (currentUser && currentUser.full_name) {
        const signerInput = document.getElementById('rep-signer-name');
        if (signerInput && !signerInput.value) {
          signerInput.value = currentUser.full_name;
        }
      }
    }

    function closeSubmitReportModal() {
      document.getElementById('submit-report-modal').style.display = 'none';
    }

    async function handleReportFormSubmit(e) {
      e.preventDefault();
      const btn = document.getElementById('rep-submit-btn');
      btn.disabled = true;
      btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Submitting Report...';

      const hasSignature = (activeChapSigMode === 'upload' && uploadedChapSigFile) || (activeChapSigMode === 'draw' && hasDrawnOnCanvas);
      if (!hasSignature) {
        alert('Please provide your official endorsement signature by drawing on the pad or uploading your signature image.');
        btn.disabled = false;
        btn.innerHTML = '<i class="ph ph-check-circle"></i> Endorse & Submit Report';
        return;
      }

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

      const formData = new FormData();
      formData.append('event_id', document.getElementById('rep-event-id').value);
      formData.append('chapter_id', chapterId);
      if (currentUser && currentUser.id) formData.append('submitted_by', currentUser.id);
      formData.append('total_participants', document.getElementById('rep-total').value);
      formData.append('first_timers', document.getElementById('rep-first-timers').value);
      formData.append('existing_members', document.getElementById('rep-members').value || '0');
      formData.append('youth_leaders_count', document.getElementById('rep-leaders').value || '0');
      formData.append('budget_spent', document.getElementById('rep-budget-spent').value || '0');
      formData.append('report_summary', document.getElementById('rep-summary').value.trim());
      formData.append('challenges', document.getElementById('rep-challenges').value.trim());
      formData.append('recommendations', document.getElementById('rep-recommendations').value.trim());
      formData.append('signed_by_name', document.getElementById('rep-signer-name').value.trim());
      formData.append('attendee_roster', JSON.stringify(rosterList));

      const driveLinkVal = (document.getElementById('rep-drive-link')?.value || '').trim();
      if (driveLinkVal) {
        formData.append('photos_drive_link', driveLinkVal);
      }

      if (selectedAttendanceSheetFile) {
        formData.append('attendance_sheet', selectedAttendanceSheetFile);
      }

      if (activeChapSigMode === 'upload' && uploadedChapSigFile) {
        formData.append('signature_file', uploadedChapSigFile);
      } else if (canvas && hasDrawnOnCanvas) {
        formData.append('e_signature', canvas.toDataURL('image/png'));
      }

      try {
        const res = await YTR_API.reports.submit(formData);
        if (res.ok) {
          alert('Report, attendance sheet, and endorsement submitted successfully! National Admin has been notified.');
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

      const execName = nationalExecName || 'Hamilton Jones Dela';
      const execSignatureHtml = nationalExecSignature
        ? `<img src="${nationalExecSignature}" alt="Chief Executive Signature" style="max-height: 46px; max-width: 160px; object-fit: contain;">`
        : `<span style="font-size: 0.72rem; color: #64748b; font-style: italic;"><i class="ph ph-check-circle"></i> National Executive Approved</span>`;
      
      const submitterSignatureHtml = (r.e_signature && r.e_signature.trim().length > 5)
        ? `<img src="${r.e_signature}" alt="Chapter Signature" style="max-height: 44px; max-width: 140px; object-fit: contain;">`
        : `<span style="font-size: 0.72rem; color: #16a34a; font-weight: 600;"><i class="ph ph-check-circle"></i> Digital Dossier Signed</span>`;

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
            <td style="padding: 0.5rem 0;">${escapeHtml(r.event_type || 'Gathering')}</td>
          </tr>
          <tr style="border-bottom: 1px solid #e2e8f0;">
            <td style="padding: 0.5rem 0; color: #64748b;"><strong>Event Title:</strong></td>
            <td style="padding: 0.5rem 0; font-weight: 700; color: #0f172a;">${escapeHtml(r.event_title)}</td>
            <td style="padding: 0.5rem 0; color: #64748b;"><strong>YTR Program:</strong></td>
            <td style="padding: 0.5rem 0; font-weight: 700; color: #1e40af;">${escapeHtml(r.ytr_program || 'Lead up (Youth Leadership)')}</td>
          </tr>
          <tr style="border-bottom: 1px solid #e2e8f0;">
            <td style="padding: 0.5rem 0; color: #64748b;"><strong>Date / Time:</strong></td>
            <td style="padding: 0.5rem 0;">${escapeHtml(r.start_datetime ? r.start_datetime.substring(0, 16).replace('T', ' ') : 'N/A')}</td>
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

        ${r.attendance_sheet_path ? `
          <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 0.65rem;">
              <div style="background: #dbeafe; color: #1d4ed8; width: 34px; height: 34px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                <i class="ph ph-file-arrow-down"></i>
              </div>
              <div>
                <div style="font-size: 0.85rem; font-weight: 700; color: #1e3a8a;">Official Physical / Scanned Attendance Sheet Attached</div>
                <div style="font-size: 0.72rem; color: #475569;">Verified attendance sign-in document archived with this report.</div>
              </div>
            </div>
            <a href="${YTR_API.reports.getAttendanceSheetUrl(r.id)}" target="_blank" class="btn btn-outline btn-sm font-roboto print-no-print" style="background: #ffffff; color: #1e40af; border-color: #93c5fd; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.78rem;">
              <i class="ph ph-download-simple"></i> Download Sheet File
            </a>
          </div>
        ` : ''}

        ${r.photos_drive_link ? `
          <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 0.65rem;">
              <div style="background: #e0f2fe; color: #0284c7; width: 34px; height: 34px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                <i class="ph-bold ph-google-drive-logo"></i>
              </div>
              <div>
                <div style="font-size: 0.85rem; font-weight: 700; color: #0369a1;">Event Pictures &amp; Documentation Album (Google Drive)</div>
                <div style="font-size: 0.72rem; color: #0284c7; word-break: break-all;">
                  <a href="${escapeHtml(r.photos_drive_link)}" target="_blank" rel="noopener noreferrer" style="color: #0284c7; text-decoration: underline;">${escapeHtml(r.photos_drive_link)}</a>
                </div>
              </div>
            </div>
            <a href="${escapeHtml(r.photos_drive_link)}" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-sm font-roboto print-no-print" style="background: #ffffff; color: #0369a1; border-color: #7dd3fc; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.78rem;">
              <i class="ph-bold ph-arrow-square-out"></i> Open Album
            </a>
          </div>
        ` : ''}

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

        <!-- Official Digital Signatures & Certifications (2-Column Layout) -->
        <div class="print-signatures-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-top: 2rem; padding-top: 1.25rem; border-top: 2px solid #1a2f63;">
          
          <!-- 1. PREPARED BY (Chapter Submitter) -->
          <div style="display: flex; flex-direction: column; justify-content: flex-end; min-height: 110px; text-align: left;">
            <div style="font-size: 0.65rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px;">
              Prepared &amp; Submitted by:
            </div>
            <div style="height: 48px; display: flex; align-items: flex-end; margin-bottom: 4px;">
              ${submitterSignatureHtml}
            </div>
            <div style="border-top: 1.5px solid #0f172a; width: 100%; margin-bottom: 4px;"></div>
            <p style="font-size: 0.825rem; font-weight: 800; margin: 0; color: #0f172a;">${escapeHtml(r.signed_by_name || 'Chapter Administrator')}</p>
            <p style="font-size: 0.72rem; color: #475569; margin: 1px 0 0;">Chapter Administrator / Submitter</p>
            <p style="font-size: 0.65rem; color: #64748b; margin: 1px 0 0;">Verified Chapter Submission</p>
          </div>

          <!-- 2. APPROVED BY (Chief Executive Operation) -->
          <div style="display: flex; flex-direction: column; justify-content: flex-end; min-height: 110px; text-align: left;">
            <div style="font-size: 0.65rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px;">
              Approved for National Records:
            </div>
            <div style="height: 48px; display: flex; align-items: flex-end; margin-bottom: 4px;">
              ${execSignatureHtml}
            </div>
            <div style="border-top: 1.5px solid #0f172a; width: 100%; margin-bottom: 4px;"></div>
            <p style="font-size: 0.825rem; font-weight: 800; margin: 0; color: #0f172a;">${escapeHtml(execName)}</p>
            <p style="font-size: 0.72rem; color: #1e3a8a; font-weight: 700; margin: 1px 0 0;">Chief Executive Operation</p>
            <p style="font-size: 0.65rem; color: #64748b; margin: 1px 0 0;">Youth on the Rock National Movement</p>
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
