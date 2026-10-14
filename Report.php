<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>YTR | Chapter Event Reports & Monitoring</title>
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <link rel="stylesheet" href="assets/dashboard.css">
  <style>
    /* Modal styles */
    .modal-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.65);
      z-index: 100;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      backdrop-filter: blur(3px);
    }
    .modal-overlay.open {
      display: flex;
    }
    .modal-box {
      background: var(--white);
      border-radius: 1rem;
      width: 100%;
      max-width: 720px;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      border: 1px solid var(--border);
    }
    .modal-header {
      padding: 1.25rem 1.5rem;
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .modal-body {
      padding: 1.5rem;
    }
    .form-grid-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }
    .form-grid-4 {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 0.75rem;
    }
    @media (max-width: 640px) {
      .form-grid-2, .form-grid-4 {
        grid-template-columns: 1fr;
      }
    }

    /* Print Preview Modal */
    .print-document-sheet {
      background: #ffffff;
      padding: 2.5rem;
      border-radius: 0.5rem;
      border: 1px solid #cbd5e1;
      color: #0f172a;
      font-family: 'Roboto', sans-serif;
    }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-wrapper">
      <?php include 'includes/topbar.php'; ?>

      <main class="page-content">
        
        <!-- Page Header -->
        <div class="page-header flex-between" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
          <div class="header-title-group">
            <div class="header-icon"><i class="ph-fill ph-shield-check"></i></div>
            <div>
              <div style="display: flex; align-items: center; gap: 0.65rem;">
                <h1 class="font-roboto" style="margin: 0; color: #0f172a; font-weight: 800;">National Reports & Compliance Audit</h1>
                <span class="portal-indicator national"><i class="ph-fill ph-shield-check"></i> National Admin Portal</span>
              </div>
              <p class="font-roboto" style="margin-top: 0.25rem;">Audit chapter-submitted dossiers, inspect attendee turnouts and thresholds, verify digital e-signatures, and print official documents.</p>
            </div>
          </div>
          <div style="display: flex; gap: 0.5rem; align-items: center;">
            <a href="ChapterReport.php" class="btn btn-primary" style="background: #16a34a; border-color: #15803d; text-decoration: none;" title="Open Chapter Submission Portal">
              <i class="ph ph-arrow-square-out"></i> Go to Chapter Submission View
            </a>
          </div>
        </div>

        <!-- Role Submission Policy Alert Banner -->
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 0.85rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
          <div style="display: flex; align-items: center; gap: 0.75rem;">
            <i class="ph-fill ph-info" style="font-size: 1.5rem; color: #1d4ed8;"></i>
            <div style="font-size: 0.875rem; color: #1e40af;">
              <strong>National Audit Authority:</strong> Post-event reports and attendee rosters are submitted strictly by <strong>Chapter Admins</strong>. As National Administrator, use this screen to audit submissions, verify digital signatures, check thresholds, and print official records.
            </div>
          </div>
          <a href="ChapterReport.php" class="btn btn-outline" style="font-size: 0.8rem; padding: 0.35rem 0.75rem; background: #ffffff; text-decoration: none;">
            <i class="ph ph-buildings"></i> Test Chapter Submission
          </a>
        </div>

        <!-- 2-Column Responsive Layout -->
        <div class="two-column-layout">
          
          <!-- LEFT COLUMN (Metrics & Table) -->
          <div class="layout-main">
            
            <!-- Metrics (4 Columns) -->
            <div class="metrics-grid">
              <div class="metric-card metric-card-row">
                <div class="metric-icon blue"><i class="ph-fill ph-files"></i></div>
                <div>
                  <h5 class="metric-title font-roboto">Active Events</h5>
                  <p class="metric-value font-roboto" id="repMetricActive">...</p>
                  <p class="metric-sub font-roboto">Approved chapter events</p>
                </div>
              </div>
              <div class="metric-card metric-card-row">
                <div class="metric-icon green"><i class="ph-fill ph-check-circle"></i></div>
                <div>
                  <h5 class="metric-title font-roboto">Completed Reports</h5>
                  <p class="metric-value font-roboto" id="repMetricCompleted">...</p>
                  <p class="metric-sub font-roboto">Reports successfully filed</p>
                </div>
              </div>
              <div class="metric-card metric-card-row">
                <div class="metric-icon purple"><i class="ph-fill ph-users"></i></div>
                <div>
                  <h5 class="metric-title font-roboto">First Timers Total</h5>
                  <p class="metric-value font-roboto" id="repMetricFirstTimers">...</p>
                  <p class="metric-sub font-roboto">New community attendees</p>
                </div>
              </div>
              <div class="metric-card metric-card-row">
                <div class="metric-icon amber"><i class="ph-fill ph-chart-line-up"></i></div>
                <div>
                  <h5 class="metric-title font-roboto">Avg Turnout Threshold</h5>
                  <p class="metric-value font-roboto" id="repMetricAvgTurnout">108%</p>
                  <p class="metric-sub font-roboto">Target performance rate</p>
                </div>
              </div>
            </div>

            <!-- Table Section -->
            <div class="table-section">
              <div class="table-header">
                <div style="display: flex; align-items: center; gap: 1rem;">
                  <h2 class="font-roboto">Chapter Event Turnout Records</h2>
                </div>
                <div class="search-bar">
                  <i class="ph ph-magnifying-glass"></i>
                  <input type="text" id="reportSearch" class="font-roboto" placeholder="Search event or chapter..." onkeyup="searchReports()">
                </div>
              </div>

              <!-- Tabs -->
              <div class="tabs-container font-roboto">
                <button class="tab-btn active" onclick="switchReportTab('all', this)"><i class="ph ph-calendar"></i> All Events</button>
                <button class="tab-btn" onclick="switchReportTab('completed', this)"><i class="ph ph-check-circle"></i> Completed Reports</button>
                <button class="tab-btn" onclick="switchReportTab('pending', this)"><i class="ph ph-clock"></i> Pending Submission</button>
              </div>

              <div class="table-responsive">
                <table>
                  <thead class="font-roboto">
                    <tr>
                      <th>Event & Chapter</th>
                      <th>Date & Venue</th>
                      <th style="text-align: center;">Turnout vs Target</th>
                      <th style="text-align: center;">1st Timers</th>
                      <th style="text-align: center;">Members</th>
                      <th style="text-align: center;">E-Signature</th>
                      <th style="text-align: center;">Action</th>
                    </tr>
                  </thead>
                  <tbody id="reportsTableBody">
                    <tr>
                      <td colspan="7" style="text-align: center; padding: 2.5rem; color: #64748b;">
                        <i class="ph ph-spinner" style="font-size: 1.5rem; animation: spin 1s linear infinite;"></i>
                        <br>Loading event reports from Python backend...
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
                <h4 class="font-roboto" style="color: #0f172a; font-size: 0.95rem; font-weight: bold; margin-bottom: 0.35rem;">No Event Selected</h4>
                <p style="font-size: 0.75rem; line-height: 1.4;">Click <strong>View</strong> on any event report row to inspect turnout metrics, attendee roster, threshold rate, and e-signatures.</p>
              </div>

              <div id="activeReportContent" style="display: none;">
                <div class="review-header">
                  <div class="review-header-icon"><i class="ph-fill ph-file-text"></i></div>
                  <div class="review-header-text">
                    <h4 id="detailEventTitle" class="font-roboto" style="color: #0f172a; font-weight: 800;">Event Name</h4>
                    <p id="detailChapterName" style="color: #2563eb; font-weight: 600;">Chapter Name</p>
                  </div>
                </div>

                <div class="review-details">
                  <div class="review-detail-group">
                    <label class="font-roboto"><i class="ph-fill ph-calendar-blank"></i> Date & Venue</label>
                    <p id="detailDateVenue" class="font-roboto" style="font-size: 0.8rem; font-weight: bold; color: #0f172a;">-</p>
                  </div>

                  <!-- Turnout Threshold Progress -->
                  <div style="background: #f8fafc; padding: 0.85rem; border-radius: 0.5rem; border: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                      <span style="font-size: 0.7rem; color: #334155; font-weight: 700;">Attendance Threshold</span>
                      <span id="detailTurnoutBadge" class="badge threshold-met font-roboto">100% Target</span>
                    </div>
                    <div class="threshold-bar">
                      <div id="detailTurnoutProgressBar" class="threshold-fill met" style="width: 100%;"></div>
                    </div>
                    <p id="detailTurnoutText" style="font-size: 0.7rem; color: #64748b; margin-top: 4px;">120 attendees out of 100 target.</p>
                  </div>

                  <!-- 4-Column Turnout Breakdown -->
                  <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.4rem; background: #f8fafc; padding: 0.65rem; border-radius: 0.5rem; border: 1px solid var(--border); margin: 0.25rem 0;">
                    <div style="text-align: center;">
                      <span style="font-size: 0.65rem; color: #64748b; display: block;">Total</span>
                      <strong id="detailTotal" style="color: #0f172a; font-size: 1rem;">0</strong>
                    </div>
                    <div style="text-align: center;">
                      <span style="font-size: 0.65rem; color: #16a34a; display: block;">1st Timers</span>
                      <strong id="detailFirstTimers" style="color: #16a34a; font-size: 1rem;">0</strong>
                    </div>
                    <div style="text-align: center;">
                      <span style="font-size: 0.65rem; color: #2563eb; display: block;">Members</span>
                      <strong id="detailMembers" style="color: #2563eb; font-size: 1rem;">0</strong>
                    </div>
                    <div style="text-align: center;">
                      <span style="font-size: 0.65rem; color: #9333ea; display: block;">Leaders</span>
                      <strong id="detailLeaders" style="color: #9333ea; font-size: 1rem;">0</strong>
                    </div>
                  </div>

                  <div class="review-detail-group">
                    <label class="font-roboto"><i class="ph-fill ph-article"></i> Executive Summary</label>
                    <div id="detailNotes" class="reason-box font-roboto" style="font-size: 0.75rem; max-height: 90px; overflow-y: auto;">
                      -
                    </div>
                  </div>

                  <!-- E-Signature Verification Block -->
                  <div class="review-detail-group">
                    <label class="font-roboto"><i class="ph-fill ph-signature"></i> E-Signature Verification</label>
                    <div style="background: #ffffff; border: 1px solid var(--border); border-radius: 0.5rem; padding: 0.75rem; display: flex; align-items: center; gap: 0.85rem;">
                      <div id="signatureImgWrapper">
                        <img id="detailSignatureImg" src="" alt="Digital Signature" class="signature-preview-img" style="display: none;">
                        <span id="noSignatureText" style="font-size: 0.75rem; color: #94a3b8; font-style: italic;">No e-signature recorded</span>
                      </div>
                      <div>
                        <p id="detailSignedByName" style="font-size: 0.75rem; font-weight: bold; color: #0f172a; margin: 0;">-</p>
                        <p id="detailSignedDate" style="font-size: 0.65rem; color: #64748b; margin: 2px 0 0;">-</p>
                        <span class="badge approved font-roboto" style="font-size: 0.6rem; margin-top: 4px;"><i class="ph ph-shield-check"></i> Verified Signature</span>
                      </div>
                    </div>
                  </div>

                  <!-- Action Buttons: Print Report -->
                  <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1rem;">
                    <button class="btn btn-primary font-roboto" style="width: 100%;" onclick="openPrintPreview()">
                      <i class="ph ph-printer"></i> Print Official Report
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

  <!-- MODAL: SUBMIT REPORT & ATTENDEES -->
  <div id="reportModal" class="modal-overlay">
    <div class="modal-box">
      <div class="modal-header">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
          <div style="width: 36px; height: 36px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
            <i class="ph ph-file-plus"></i>
          </div>
          <div>
            <h3 class="font-roboto" style="font-size: 1.1rem; color: #0f172a; font-weight: 800;">Submit Event Report & Attendance Roster</h3>
            <p style="font-size: 0.75rem; color: #64748b; margin: 0;">File event turnout, attendee breakdown, and digital e-signature.</p>
          </div>
        </div>
        <button onclick="closeReportModal()" style="background: none; border: none; font-size: 1.25rem; color: var(--text-muted); cursor: pointer;"><i class="ph ph-x"></i></button>
      </div>

      <div class="modal-body">
        <form id="eventReportForm" onsubmit="handleReportFormSubmit(event)">
          <div class="form-group">
            <label class="form-label font-roboto">Select Event to Report <span style="color: #f43f5e;">*</span></label>
            <select id="formReportEventId" class="form-select font-roboto" required>
              <option value="" disabled selected>Loading eligible events...</option>
            </select>
          </div>

          <!-- Attendance Turnout Breakdown (4 Fields) -->
          <div class="form-group">
            <label class="form-label font-roboto">Attendance Turnout Breakdown <span style="color: #f43f5e;">*</span></label>
            <div class="form-grid-4">
              <div>
                <label style="font-size: 0.7rem; color: #64748b;">Total Attendees</label>
                <input type="number" id="formReportTotal" class="form-input font-roboto" placeholder="e.g. 142" min="1" required onchange="calculateTurnoutBreakdown()">
              </div>
              <div>
                <label style="font-size: 0.7rem; color: #16a34a;">1st Timers</label>
                <input type="number" id="formReportFirstTimers" class="form-input font-roboto" placeholder="e.g. 38" min="0" required>
              </div>
              <div>
                <label style="font-size: 0.7rem; color: #2563eb;">Members</label>
                <input type="number" id="formReportMembers" class="form-input font-roboto" placeholder="e.g. 84" min="0" required>
              </div>
              <div>
                <label style="font-size: 0.7rem; color: #9333ea;">Youth Leaders</label>
                <input type="number" id="formReportLeaders" class="form-input font-roboto" placeholder="e.g. 20" min="0" required>
              </div>
            </div>
          </div>

          <div class="form-grid-2">
            <div class="form-group">
              <label class="form-label font-roboto">Budget Utilized (PHP)</label>
              <input type="number" id="formReportBudget" class="form-input font-roboto" placeholder="e.g. 18500" min="0">
            </div>
            <div class="form-group">
              <label class="form-label font-roboto">Signer Full Name <span style="color: #f43f5e;">*</span></label>
              <input type="text" id="formReportSignerName" class="form-input font-roboto" placeholder="e.g. Joan Dela Cruz (Chapter Admin)" required>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label font-roboto">Attendee Roster Submission (Names, Contacts, Church/Campus)</label>
            <textarea id="formReportRoster" class="form-textarea font-roboto" rows="3" placeholder="1. Maria Santos - 0917-123-4567 - Laguna Campus - First Timer&#10;2. Mark Reyes - 0918-987-6543 - Member&#10;3. Daniel Gomez - 0922-555-4321 - Youth Leader"></textarea>
          </div>

          <div class="form-group">
            <label class="form-label font-roboto">Executive Summary & Highlights <span style="color: #f43f5e;">*</span></label>
            <textarea id="formReportSummary" class="form-textarea font-roboto" rows="2" placeholder="Summary of ministry activities, program flow, and spiritual breakthroughs..." required></textarea>
          </div>

          <!-- Digital E-Signature Pad Section -->
          <div class="form-group">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
              <label class="form-label font-roboto" style="margin-bottom: 0;">Digital E-Signature <span style="color: #f43f5e;">*</span></label>
              <button type="button" class="btn btn-outline btn-sm font-roboto" onclick="clearSignatureCanvas()">
                <i class="ph ph-eraser"></i> Clear Pad
              </button>
            </div>
            <div class="signature-box">
              <canvas id="signatureCanvas" class="signature-canvas"></canvas>
              <p style="font-size: 0.65rem; color: #94a3b8; margin-top: 4px;">Sign with your mouse, stylus, or finger inside the box</p>
            </div>
          </div>

          <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem;">
            <button type="button" class="btn btn-outline font-roboto" onclick="closeReportModal()">Cancel</button>
            <button type="submit" id="btnSubmitReport" class="btn btn-primary font-roboto">
              <i class="ph ph-signature"></i> E-Sign & Submit Report
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- PRINT PREVIEW MODAL -->
  <div id="printModal" class="modal-overlay">
    <div class="modal-box" style="max-width: 800px;">
      <div class="modal-header">
        <h3 class="font-roboto" style="font-size: 1.1rem; color: #0f172a; font-weight: 800;">
          <i class="ph ph-printer" style="color: #2563eb;"></i> Official Event Report Print Preview
        </h3>
        <button onclick="closePrintModal()" style="background: none; border: none; font-size: 1.25rem; color: var(--text-muted); cursor: pointer;"><i class="ph ph-x"></i></button>
      </div>

      <div class="modal-body">
        <div id="printableReportDocument" class="print-document-sheet">
          <!-- Document Header with Official YTR Branding -->
          <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 3px double #1a2f63; padding-bottom: 1rem; margin-bottom: 1.5rem;">
            <div>
              <h2 style="font-size: 1.4rem; font-weight: 900; color: #1a2f63; margin: 0; letter-spacing: -0.02em;">YOUTH ON THE ROCK</h2>
              <p style="font-size: 0.8rem; color: #475569; margin: 2px 0 0; text-transform: uppercase; font-weight: bold;">Community-Centric Event Monitoring & Reporting Platform</p>
              <p style="font-size: 0.7rem; color: #64748b;">Official Post-Event Evaluation & Attendance Report</p>
            </div>
            <div style="text-align: right;">
              <span id="printReportIdBadge" style="display: inline-block; padding: 4px 10px; background: #eff6ff; color: #1e3a8a; font-weight: bold; font-size: 0.75rem; border-radius: 4px; border: 1px solid #bfdbfe;">
                REPORT #YTR-2026
              </span>
              <p id="printGeneratedDate" style="font-size: 0.65rem; color: #64748b; margin-top: 4px;">Date: Oct 14, 2026</p>
            </div>
          </div>

          <!-- Event Information Table -->
          <table style="width: 100%; border: 1px solid #cbd5e1; margin-bottom: 1.25rem; border-collapse: collapse;">
            <tbody>
              <tr style="background: #f8fafc;">
                <td style="padding: 6px 12px; font-weight: bold; width: 25%; font-size: 0.8rem; border: 1px solid #cbd5e1;">Event Title</td>
                <td id="printEventTitle" style="padding: 6px 12px; font-weight: bold; color: #1a2f63; font-size: 0.85rem; border: 1px solid #cbd5e1;">-</td>
              </tr>
              <tr>
                <td style="padding: 6px 12px; font-weight: bold; font-size: 0.8rem; border: 1px solid #cbd5e1;">Host Chapter</td>
                <td id="printChapterName" style="padding: 6px 12px; font-size: 0.8rem; border: 1px solid #cbd5e1;">-</td>
              </tr>
              <tr style="background: #f8fafc;">
                <td style="padding: 6px 12px; font-weight: bold; font-size: 0.8rem; border: 1px solid #cbd5e1;">Date & Venue</td>
                <td id="printScheduleVenue" style="padding: 6px 12px; font-size: 0.8rem; border: 1px solid #cbd5e1;">-</td>
              </tr>
              <tr>
                <td style="padding: 6px 12px; font-weight: bold; font-size: 0.8rem; border: 1px solid #cbd5e1;">Budget Utilized</td>
                <td id="printBudgetSpent" style="padding: 6px 12px; font-size: 0.8rem; border: 1px solid #cbd5e1;">-</td>
              </tr>
            </tbody>
          </table>

          <!-- Attendance Turnout & Threshold Section -->
          <h4 style="font-size: 0.9rem; font-weight: 800; color: #1a2f63; margin-bottom: 0.5rem;">ATTENDANCE TURNOUT & THRESHOLD METRICS</h4>
          <table style="width: 100%; border: 1px solid #cbd5e1; margin-bottom: 1.25rem; text-align: center; border-collapse: collapse;">
            <thead>
              <tr style="background: #1a2f63; color: #ffffff; font-size: 0.75rem;">
                <th style="padding: 6px; border: 1px solid #cbd5e1;">Total Participants</th>
                <th style="padding: 6px; border: 1px solid #cbd5e1;">First Timers</th>
                <th style="padding: 6px; border: 1px solid #cbd5e1;">Existing Members</th>
                <th style="padding: 6px; border: 1px solid #cbd5e1;">Youth Leaders</th>
                <th style="padding: 6px; border: 1px solid #cbd5e1;">Threshold Rate</th>
              </tr>
            </thead>
            <tbody>
              <tr style="font-size: 0.85rem; font-weight: bold;">
                <td id="printTotalAtt" style="padding: 8px; border: 1px solid #cbd5e1; color: #0f172a;">-</td>
                <td id="printFirstTimers" style="padding: 8px; border: 1px solid #cbd5e1; color: #16a34a;">-</td>
                <td id="printMembers" style="padding: 8px; border: 1px solid #cbd5e1; color: #2563eb;">-</td>
                <td id="printLeaders" style="padding: 8px; border: 1px solid #cbd5e1; color: #9333ea;">-</td>
                <td id="printThresholdRate" style="padding: 8px; border: 1px solid #cbd5e1; color: #059669;">-</td>
              </tr>
            </tbody>
          </table>

          <!-- Summary & Highlights -->
          <h4 style="font-size: 0.9rem; font-weight: 800; color: #1a2f63; margin-bottom: 0.35rem;">EXECUTIVE SUMMARY & HIGHLIGHTS</h4>
          <div id="printSummaryText" style="padding: 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; font-size: 0.75rem; line-height: 1.5; margin-bottom: 1.5rem;">
            -
          </div>

          <!-- Digital Signature & Certification Stamp -->
          <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 2rem; padding-top: 1rem; border-top: 1px dashed #cbd5e1;">
            <div style="font-size: 0.7rem; color: #64748b;">
              <p>Certified Genuine and Approved by Chapter Leadership</p>
              <p>Youth on the Rock National Secretariat Records</p>
            </div>
            <div style="text-align: center; width: 220px;">
              <img id="printSignatureImage" src="" alt="Signer E-Signature" style="max-height: 65px; margin-bottom: 4px; display: inline-block;">
              <div style="border-top: 1px solid #0f172a; width: 100%; margin: 2px auto 4px;"></div>
              <p id="printSignerName" style="font-size: 0.75rem; font-weight: bold; margin: 0; color: #0f172a;">Signer Name</p>
              <p id="printSignerTimestamp" style="font-size: 0.65rem; color: #64748b; margin: 0;">Digitally Signed</p>
            </div>
          </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.25rem;">
          <button type="button" class="btn btn-outline font-roboto" onclick="closePrintModal()">Close</button>
          <button type="button" class="btn btn-primary font-roboto" onclick="window.print()">
            <i class="ph ph-printer"></i> Print / Save as PDF
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="assets/api-client.js"></script>
  <script>
    let currentReportFilter = 'all';
    let selectedReportData = null;
    let allReports = [];

    // Signature Pad Canvas Handling
    const canvas = document.getElementById('signatureCanvas');
    const ctx = canvas ? canvas.getContext('2d') : null;
    let isDrawing = false;
    let hasDrawnSignature = false;

    if (canvas && ctx) {
      function resizeCanvas() {
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width;
        canvas.height = 130;
        ctx.strokeStyle = '#1a2f63';
        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
      }

      window.addEventListener('resize', resizeCanvas);

      function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const clientX = e.clientX || (e.touches && e.touches[0].clientX);
        const clientY = e.clientY || (e.touches && e.touches[0].clientY);
        return {
          x: clientX - rect.left,
          y: clientY - rect.top
        };
      }

      canvas.addEventListener('mousedown', (e) => {
        isDrawing = true;
        hasDrawnSignature = true;
        const pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
      });

      canvas.addEventListener('mousemove', (e) => {
        if (!isDrawing) return;
        const pos = getPos(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
      });

      window.addEventListener('mouseup', () => { isDrawing = false; });

      // Touch Events for Mobile / Tablet
      canvas.addEventListener('touchstart', (e) => {
        e.preventDefault();
        isDrawing = true;
        hasDrawnSignature = true;
        const pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
      }, { passive: false });

      canvas.addEventListener('touchmove', (e) => {
        if (!isDrawing) return;
        e.preventDefault();
        const pos = getPos(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
      }, { passive: false });

      canvas.addEventListener('touchend', () => { isDrawing = false; });
    }

    function clearSignatureCanvas() {
      if (ctx && canvas) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasDrawnSignature = false;
      }
    }

    // Load Metrics from Python API
    async function loadReportMetrics() {
      const res = await YTR_API.reports.getMetrics();
      if (res.status === 'success' && res.data) {
        document.getElementById('repMetricActive').textContent = res.data.total_active_events || 0;
        document.getElementById('repMetricCompleted').textContent = res.data.completed_events || 0;
        document.getElementById('repMetricFirstTimers').textContent = res.data.first_timers || 0;
      }
    }

    // Load Eligible Events into Modal Select
    async function loadEligibleEvents() {
      const res = await YTR_API.proposals.list('all');
      const select = document.getElementById('formReportEventId');
      if (res.status === 'success' && Array.isArray(res.data)) {
        select.innerHTML = '<option value="" disabled selected>Select event to file report for</option>';
        res.data.forEach(ev => {
          const opt = document.createElement('option');
          opt.value = ev.id;
          opt.textContent = `${ev.title} - ${ev.chapter_name || 'All'} (${ev.status.toUpperCase()})`;
          select.appendChild(opt);
        });
      }
    }

    // Load and Render Reports Table
    async function loadReports(filter = currentReportFilter) {
      const tbody = document.getElementById('reportsTableBody');
      const search = document.getElementById('reportSearch').value.trim();

      const res = await YTR_API.reports.list(filter, search);
      if (res.status === 'success' && Array.isArray(res.data)) {
        allReports = res.data;
        if (res.data.length === 0) {
          tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 2.5rem; color: #64748b;">No event reports match the filter.</td></tr>';
          return;
        }

        tbody.innerHTML = '';
        res.data.forEach((r, idx) => {
          const row = document.createElement('tr');
          const startDate = r.start_datetime ? r.start_datetime.substring(0, 10) : 'TBD';
          const totalAtt = r.total_participants || 0;
          const targetAtt = r.target_turnout || 100;
          const turnoutPercent = r.turnout_percentage || 0;
          const hasSignature = !!r.e_signature;

          let thresholdBadge = `<span class="badge threshold-met font-roboto">${turnoutPercent}% Met</span>`;
          if (turnoutPercent < 70) {
            thresholdBadge = `<span class="badge threshold-warn font-roboto">${turnoutPercent}% Below</span>`;
          }

          let sigBadge = `<span class="badge approved font-roboto"><i class="ph ph-check"></i> E-Signed</span>`;
          if (!hasSignature) {
            sigBadge = `<span class="badge pending font-roboto">Pending Signature</span>`;
          }

          row.innerHTML = `
            <td>
              <div style="display: flex; align-items: center; gap: 0.85rem;">
                <div style="width: 38px; height: 38px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0;">
                  <i class="ph-fill ph-file-text"></i>
                </div>
                <div>
                  <p class="font-roboto" style="font-weight: 800; color: #0f172a; font-size: 0.875rem; margin: 0;">${r.event_title}</p>
                  <span class="badge chapter-admin font-roboto" style="margin-top: 3px;">${r.chapter_name || 'Chapter Event'}</span>
                </div>
              </div>
            </td>
            <td>
              <p class="font-roboto" style="font-weight: bold; font-size: 0.8rem; color: #0f172a; margin: 0;">${startDate}</p>
              <p style="font-size: 0.7rem; color: #64748b; margin: 2px 0 0;"><i class="ph ph-map-pin"></i> ${r.venue || 'TBD'}</p>
            </td>
            <td style="text-align: center;">
              <strong style="color: #0f172a; font-size: 0.9rem;">${totalAtt} / ${targetAtt}</strong>
              <div style="margin-top: 3px;">${thresholdBadge}</div>
            </td>
            <td style="text-align: center; font-weight: bold; color: #16a34a;">${r.first_timers || 0}</td>
            <td style="text-align: center; font-weight: bold; color: #2563eb;">${r.existing_members || 0}</td>
            <td style="text-align: center;">${sigBadge}</td>
            <td style="text-align: center;">
              <button class="btn btn-primary btn-sm font-roboto" style="margin: 0 auto;" onclick='selectReportRecord(${JSON.stringify(r)})'>
                <i class="ph ph-eye"></i> View
              </button>
            </td>
          `;
          tbody.appendChild(row);

          if (idx === 0 && !selectedReportData) {
            selectReportRecord(r);
          }
        });
      }
    }

    // Select Report Record into Sidebar Overview
    function selectReportRecord(r) {
      selectedReportData = r;
      document.getElementById('emptyReportState').style.display = 'none';
      document.getElementById('activeReportContent').style.display = 'block';

      document.getElementById('detailEventTitle').textContent = r.event_title;
      document.getElementById('detailChapterName').textContent = r.chapter_name || 'National Secretariat';
      document.getElementById('detailDateVenue').textContent = (r.start_datetime ? r.start_datetime.substring(0, 10) : 'TBD') + ' • ' + (r.venue || 'TBD');
      
      const total = r.total_participants || 0;
      const target = r.target_turnout || 100;
      const percent = r.turnout_percentage || 0;

      document.getElementById('detailTotal').textContent = total;
      document.getElementById('detailFirstTimers').textContent = r.first_timers || 0;
      document.getElementById('detailMembers').textContent = r.existing_members || 0;
      document.getElementById('detailLeaders').textContent = r.youth_leaders_count || 0;
      document.getElementById('detailNotes').textContent = r.report_summary || 'No summary notes submitted.';

      // Threshold indicators
      const badge = document.getElementById('detailTurnoutBadge');
      const fill = document.getElementById('detailTurnoutProgressBar');
      const text = document.getElementById('detailTurnoutText');

      badge.textContent = `${percent}% Target Rate`;
      fill.style.width = `${Math.min(percent, 100)}%`;
      text.textContent = `${total} participants achieved against ${target} expected attendees threshold.`;

      if (percent >= 100) {
        badge.className = 'badge threshold-met font-roboto';
        fill.className = 'threshold-fill met';
      } else if (percent >= 70) {
        badge.className = 'badge threshold-warn font-roboto';
        fill.className = 'threshold-fill warn';
      } else {
        badge.className = 'badge rose font-roboto';
        fill.className = 'threshold-fill low';
      }

      // Digital Signature preview
      const sigImg = document.getElementById('detailSignatureImg');
      const noSigText = document.getElementById('noSignatureText');
      const signerNameP = document.getElementById('detailSignedByName');
      const signedDateP = document.getElementById('detailSignedDate');

      if (r.e_signature) {
        sigImg.src = r.e_signature;
        sigImg.style.display = 'block';
        noSigText.style.display = 'none';
        signerNameP.textContent = r.signed_by_name || 'Verified Signer';
        signedDateP.textContent = 'Signed on ' + (r.signed_at || r.submitted_at || 'Recently');
      } else {
        sigImg.style.display = 'none';
        noSigText.style.display = 'block';
        signerNameP.textContent = 'Pending Signature';
        signedDateP.textContent = 'Awaiting coordinator sign-off';
      }
    }

    // Switch Tabs
    function switchReportTab(filter, btn) {
      currentReportFilter = filter;
      document.querySelectorAll('.tabs-container .tab-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      loadReports(filter);
    }

    // Live search
    let reportSearchTimeout;
    function searchReports() {
      clearTimeout(reportSearchTimeout);
      reportSearchTimeout = setTimeout(loadReports, 300);
    }

    // Modal Controls
    function openReportModal() {
      document.getElementById('reportModal').classList.add('open');
      setTimeout(resizeCanvas, 150);
    }
    function closeReportModal() {
      document.getElementById('reportModal').classList.remove('open');
    }

    // Calculate turnout breakdown if total is changed
    function calculateTurnoutBreakdown() {
      const total = parseInt(document.getElementById('formReportTotal').value) || 0;
      const ft = document.getElementById('formReportFirstTimers');
      const mem = document.getElementById('formReportMembers');
      const ldr = document.getElementById('formReportLeaders');

      if (!ft.value && !mem.value && total > 0) {
        ft.value = Math.round(total * 0.3);
        mem.value = Math.round(total * 0.6);
        ldr.value = total - parseInt(ft.value) - parseInt(mem.value);
      }
    }

    // Handle Form Submit with Digital Signature & Attendees Roster
    async function handleReportFormSubmit(e) {
      e.preventDefault();
      const submitBtn = document.getElementById('btnSubmitReport');
      submitBtn.disabled = true;

      // Check signature
      if (!hasDrawnSignature) {
        alert('Please draw your digital e-signature in the box before submitting.');
        submitBtn.disabled = false;
        return;
      }

      const sigDataUrl = canvas.toDataURL('image/png');

      const payload = {
        event_id: document.getElementById('formReportEventId').value,
        total_participants: document.getElementById('formReportTotal').value,
        first_timers: document.getElementById('formReportFirstTimers').value,
        existing_members: document.getElementById('formReportMembers').value,
        youth_leaders_count: document.getElementById('formReportLeaders').value,
        budget_spent: document.getElementById('formReportBudget').value || 0,
        signed_by_name: document.getElementById('formReportSignerName').value.trim(),
        attendee_roster: document.getElementById('formReportRoster').value.trim(),
        report_summary: document.getElementById('formReportSummary').value.trim(),
        e_signature: sigDataUrl
      };

      try {
        const res = await YTR_API.reports.submit(payload);
        if (res.status === 'success') {
          alert('Event report, attendee breakdown, and digital e-signature have been submitted successfully!');
          closeReportModal();
          e.target.reset();
          clearSignatureCanvas();
          loadReportMetrics();
          loadReports();
        } else {
          alert(res.message || 'Error submitting report.');
        }
      } catch (err) {
        alert('Failed to submit report. Please ensure the Python backend is running.');
      } finally {
        submitBtn.disabled = false;
      }
    }

    // Print Modal & Document Preview
    function openPrintPreview() {
      if (!selectedReportData) {
        alert('Please select a report row to print.');
        return;
      }
      const r = selectedReportData;
      document.getElementById('printEventTitle').textContent = r.event_title;
      document.getElementById('printChapterName').textContent = r.chapter_name || 'National Secretariat';
      document.getElementById('printScheduleVenue').textContent = (r.start_datetime ? r.start_datetime.substring(0, 10) : 'TBD') + ' • ' + (r.venue || 'TBD');
      document.getElementById('printBudgetSpent').textContent = '₱' + Number(r.budget_spent || 0).toLocaleString();
      
      document.getElementById('printTotalAtt').textContent = r.total_participants || 0;
      document.getElementById('printFirstTimers').textContent = r.first_timers || 0;
      document.getElementById('printMembers').textContent = r.existing_members || 0;
      document.getElementById('printLeaders').textContent = r.youth_leaders_count || 0;
      document.getElementById('printThresholdRate').textContent = (r.turnout_percentage || 100) + '% Performance';

      document.getElementById('printSummaryText').textContent = r.report_summary || 'Activities successfully conducted with full chapter participation.';
      document.getElementById('printSignerName').textContent = r.signed_by_name || 'Joan Dela Cruz';
      document.getElementById('printSignerTimestamp').textContent = 'Digitally Signed on ' + (r.signed_at || new Date().toLocaleDateString());

      const printSig = document.getElementById('printSignatureImage');
      if (r.e_signature) {
        printSig.src = r.e_signature;
        printSig.style.display = 'inline-block';
      } else {
        printSig.style.display = 'none';
      }

      document.getElementById('printModal').classList.add('open');
    }

    function closePrintModal() {
      document.getElementById('printModal').classList.remove('open');
    }

    // Initialize on DOM load
    window.addEventListener('DOMContentLoaded', () => {
      loadReportMetrics();
      loadEligibleEvents();
      loadReports();
    });
  </script>
</body>
</html>