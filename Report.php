<?php include 'includes/auth_guard.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Phosphor Icons -->
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.2/src/regular/style.css">
  <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.2/src/fill/style.css">
  <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.2/src/bold/style.css">
  <link rel="stylesheet" href="assets/National.css">
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
      padding: 2.25rem;
      border-radius: 0.5rem;
      border: 1px solid #cbd5e1;
      color: #0f172a;
      font-family: 'Roboto', sans-serif;
    }

    @media print {
      body * {
        visibility: hidden !important;
      }
      #printModal, #printModal .modal-box, #printModal .modal-body, #printableReportDocument, #printableReportDocument * {
        visibility: visible !important;
      }
      #printModal {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        background: #ffffff !important;
        padding: 0 !important;
        margin: 0 !important;
        display: block !important;
        box-shadow: none !important;
        z-index: 99999 !important;
      }
      #printModal .modal-box {
        max-width: 100% !important;
        width: 100% !important;
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
      }
      #printModal .modal-header,
      #printModal .print-action-bar,
      #printModal .print-status-banner,
      #printModal button {
        display: none !important;
      }
      .print-document-sheet {
        border: none !important;
        padding: 0 !important;
        box-shadow: none !important;
      }
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
            <button type="button" class="btn btn-outline font-roboto" onclick="openSignatureManagerModal()" style="display: inline-flex; align-items: center; gap: 0.45rem; background: #ffffff;">
              <i class="ph ph-signature"></i> Upload Executive Signature
            </button>
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
                      <th style="text-align: center;">Submission Status</th>
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

                  <!-- Submitter Verification Block -->
                  <div class="review-detail-group">
                    <label class="font-roboto"><i class="ph-fill ph-user-check"></i> Submitting Chapter Coordinator</label>
                    <div style="background: #ffffff; border: 1px solid var(--border); border-radius: 0.5rem; padding: 0.75rem;">
                      <p id="detailSignedByName" style="font-size: 0.85rem; font-weight: bold; color: #0f172a; margin: 0;">-</p>
                      <p id="detailSignedDate" style="font-size: 0.7rem; color: #64748b; margin: 2px 0 0;">-</p>
                      <span id="detailVerificationBadge" class="badge approved font-roboto" style="font-size: 0.65rem; margin-top: 6px; display: inline-flex;"><i class="ph ph-shield-check"></i> Verified Chapter Dossier</span>
                    </div>
                  </div>

                  <!-- Uploaded Attendance Sheet Document -->
                  <div id="detailAttendanceSheetBox" class="review-detail-group" style="display: none;">
                    <label class="font-roboto"><i class="ph-fill ph-file-arrow-down"></i> Official Attendance Sheet</label>
                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 0.5rem; padding: 0.65rem 0.75rem; display: flex; align-items: center; justify-content: space-between;">
                      <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="ph-bold ph-file-text" style="font-size: 1.25rem; color: #1e40af;"></i>
                        <span style="font-size: 0.75rem; font-weight: 700; color: #1e3a8a;">Signed Attendance Sheet</span>
                      </div>
                      <a id="detailAttendanceSheetBtn" href="#" target="_blank" class="btn btn-outline btn-sm font-roboto" style="background: #fff; font-size: 0.72rem; padding: 0.2rem 0.5rem; text-decoration: none; color: #1d4ed8; border-color: #93c5fd;">
                        <i class="ph ph-download-simple"></i> Download
                      </a>
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
        <!-- Executive Signature Status Toolbar -->
        <div class="print-status-banner" style="display: flex; align-items: center; justify-content: space-between; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 0.65rem 1rem; margin-bottom: 1rem;">
          <div style="display: flex; align-items: center; gap: 0.65rem;">
            <div style="width: 32px; height: 32px; border-radius: 6px; background: #dbeafe; color: #1d4ed8; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
              <i class="ph-bold ph-signature"></i>
            </div>
            <div>
              <div style="font-size: 0.85rem; font-weight: 700; color: #1e3a8a;">
                Chief Executive Signature:
                <span id="printSigStatusBadge" class="badge approved font-roboto" style="font-size: 0.7rem; margin-left: 0.4rem;">
                  <i class="ph ph-check"></i> Loaded
                </span>
              </div>
              <div style="font-size: 0.72rem; color: #475569;">National Administrator signature will be placed automatically above Chief Executive Operation for official report endorsement.</div>
            </div>
          </div>
          <div>
            <button type="button" class="btn btn-outline font-roboto btn-sm" onclick="openSignatureManagerModal()" style="background: #fff; font-size: 0.78rem;">
              <i class="ph ph-upload-simple"></i> Upload / Update Signature
            </button>
          </div>
        </div>

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
                <td style="padding: 6px 12px; font-weight: bold; font-size: 0.8rem; border: 1px solid #cbd5e1;">YTR Program Pillar</td>
                <td id="printProgramPillar" style="padding: 6px 12px; font-size: 0.8rem; border: 1px solid #cbd5e1; font-weight: 700; color: #1e40af;">-</td>
              </tr>
              <tr style="background: #f8fafc;">
                <td style="padding: 6px 12px; font-weight: bold; font-size: 0.8rem; border: 1px solid #cbd5e1;">Host Chapter</td>
                <td id="printChapterName" style="padding: 6px 12px; font-size: 0.8rem; border: 1px solid #cbd5e1;">-</td>
              </tr>
              <tr>
                <td style="padding: 6px 12px; font-weight: bold; font-size: 0.8rem; border: 1px solid #cbd5e1;">Date & Venue</td>
                <td id="printScheduleVenue" style="padding: 6px 12px; font-size: 0.8rem; border: 1px solid #cbd5e1;">-</td>
              </tr>
              <tr style="background: #f8fafc;">
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

          <!-- Attendance Sheet Document Banner -->
          <div id="printAttendanceSheetCard" style="display: none; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 0.75rem 1rem; margin-bottom: 1.5rem; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 0.65rem;">
              <div style="background: #dbeafe; color: #1d4ed8; width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                <i class="ph ph-file-arrow-down"></i>
              </div>
              <div>
                <div style="font-size: 0.8rem; font-weight: 700; color: #1e3a8a;">Official Attendance Sheet Attached</div>
                <div style="font-size: 0.7rem; color: #475569;">Physical sign-in scan / digital attendee roster uploaded by Chapter Admin.</div>
              </div>
            </div>
            <a id="printAttendanceSheetLink" href="#" target="_blank" class="btn btn-outline btn-sm font-roboto print-no-print" style="background: #ffffff; color: #1e40af; border-color: #93c5fd; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; padding: 0.3rem 0.65rem;">
              <i class="ph ph-download-simple"></i> Download Sheet File
            </a>
          </div>

          <!-- Official Digital Signatures & Certifications (2-Column Layout) -->
          <div class="print-signatures-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-top: 2rem; padding-top: 1.25rem; border-top: 2px solid #1a2f63;">
            
            <!-- 1. PREPARED BY (Chapter Submitter) -->
            <div style="display: flex; flex-direction: column; justify-content: flex-end; min-height: 110px; text-align: left;">
              <div style="font-size: 0.65rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px;">
                Prepared &amp; Submitted by:
              </div>
              <div id="printSubmitterSigBox" style="height: 48px; display: flex; align-items: flex-end; margin-bottom: 4px;">
                <span style="font-size: 0.72rem; color: #16a34a; font-weight: 600;"><i class="ph ph-check-circle"></i> Digital Dossier Signed</span>
              </div>
              <div style="border-top: 1.5px solid #0f172a; width: 100%; margin-bottom: 4px;"></div>
              <p id="printSignerName" style="font-size: 0.825rem; font-weight: 800; margin: 0; color: #0f172a;">Signer Name</p>
              <p id="printSignerTitle" style="font-size: 0.72rem; color: #475569; margin: 1px 0 0;">Chapter Administrator</p>
              <p id="printSignerTimestamp" style="font-size: 0.65rem; color: #64748b; margin: 1px 0 0;">Verified Chapter Submission</p>
            </div>

            <!-- 2. APPROVED BY (Chief Executive Operation) -->
            <div style="display: flex; flex-direction: column; justify-content: flex-end; min-height: 110px; text-align: left;">
              <div style="font-size: 0.65rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px;">
                Approved for National Records:
              </div>
              <div id="printNationalSigBox" style="height: 48px; display: flex; align-items: flex-end; margin-bottom: 4px;">
                <!-- National Admin Uploaded Signature Placed Here -->
              </div>
              <div style="border-top: 1.5px solid #0f172a; width: 100%; margin-bottom: 4px;"></div>
              <p id="printChiefExecName" style="font-size: 0.825rem; font-weight: 800; margin: 0; color: #0f172a;">Hamilton Jones Dela</p>
              <p id="printChiefExecTitle" style="font-size: 0.72rem; color: #1e3a8a; font-weight: 700; margin: 1px 0 0;">Chief Executive Operation</p>
              <p style="font-size: 0.65rem; color: #64748b; margin: 1px 0 0;">Youth on the Rock National Movement</p>
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

  <!-- NATIONAL ADMIN SIGNATURE MODAL -->
  <div id="signatureModal" class="modal-overlay hidden" style="display: none; z-index: 1050;">
    <div class="modal-md" style="max-width: 520px;">
      <div class="modal-header-banner" style="background: #eff6ff; border-bottom: 1px solid #bfdbfe;">
        <div style="display: flex; align-items: center; gap: 0.65rem;">
          <div style="background: #dbeafe; color: #2563eb; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
            <i class="ph-bold ph-signature"></i>
          </div>
          <div>
            <h3 style="color: #1e40af; margin: 0; font-size: 1rem;">National Administrator Signature</h3>
            <p style="color: #3b82f6; margin: 0; font-size: 0.75rem;">Upload or draw your official signature for Chief Executive Operation reports endorsement.</p>
          </div>
        </div>
        <button type="button" onclick="closeSignatureManagerModal()"><i class="ph ph-x"></i></button>
      </div>

      <div class="modal-body-scroll" style="background: #ffffff; padding: 1.25rem;">
        <!-- Signature Mode Switch -->
        <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
          <button type="button" id="tabUploadSig" class="btn btn-primary btn-sm font-roboto" onclick="switchSigMode('upload')">
            <i class="ph ph-upload-simple"></i> Upload Image (PNG/JPG)
          </button>
          <button type="button" id="tabDrawSig" class="btn btn-outline btn-sm font-roboto" onclick="switchSigMode('draw')">
            <i class="ph ph-pencil-line"></i> Draw Signature Pad
          </button>
        </div>

        <!-- Mode 1: File Upload -->
        <div id="sigUploadPanel">
          <div class="proof-dropzone" style="padding: 1.25rem; text-align: center; border: 2px dashed #cbd5e1; border-radius: 8px; background: #f8fafc; cursor: pointer; position: relative;">
            <input type="file" id="sigFileInput" accept="image/png, image/jpeg, image/webp" onchange="handleSigFileSelect(event)" style="position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;">
            <i class="ph-bold ph-cloud-arrow-up" style="font-size: 2rem; color: #2563eb;"></i>
            <div style="font-size: 0.85rem; font-weight: 700; color: #0f172a; margin-top: 0.35rem;">Click or drag signature image here</div>
            <p style="font-size: 0.72rem; color: #64748b; margin: 0.15rem 0 0;">PNG with transparent background or clean white JPG recommended</p>
          </div>
        </div>

        <!-- Mode 2: Draw Pad -->
        <div id="sigDrawPanel" style="display: none;">
          <div style="border: 1px solid #cbd5e1; border-radius: 8px; background: #f8fafc; padding: 4px; text-align: center;">
            <canvas id="nationalSigCanvas" width="460" height="150" style="background: #ffffff; border-radius: 6px; cursor: crosshair; display: block; margin: 0 auto; max-width: 100%;"></canvas>
          </div>
          <div style="display: flex; justify-content: flex-end; margin-top: 0.35rem;">
            <button type="button" class="btn btn-outline btn-sm font-roboto" onclick="clearNationalSigCanvas()" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
              <i class="ph ph-arrow-counter-clockwise"></i> Clear Canvas
            </button>
          </div>
        </div>

        <!-- Live Preview Box -->
        <div style="margin-top: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem;">
          <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Active Signature Preview</div>
          <div id="sigPreviewContainer" style="height: 70px; display: flex; align-items: center; justify-content: center; border-bottom: 2px solid #0f172a; margin: 0.5rem 0 0.25rem;">
            <span style="font-size: 0.75rem; color: #94a3b8; font-style: italic;">No signature loaded</span>
          </div>
          <div style="text-align: center; font-size: 0.8rem; font-weight: 800; color: #0f172a; margin-top: 2px;" id="sigPreviewName">
            Hamilton Jones Dela
          </div>
          <div style="text-align: center; font-size: 0.72rem; color: #2563eb; font-weight: 700;">
            Chief Executive Operation
          </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.65rem; margin-top: 1.25rem;">
          <button type="button" class="btn btn-outline font-roboto" onclick="closeSignatureManagerModal()">Close</button>
          <button type="button" class="btn btn-primary font-roboto" id="btnSaveNationalSig" onclick="saveNationalSignature()" style="background: #2563eb; border-color: #1d4ed8;">
            <i class="ph ph-check"></i> Save &amp; Apply Signature
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

    // Load Metrics from Python API
    async function loadReportMetrics() {
      const res = await YTR_API.reports.getMetrics();
      if (res.status === 'success' && res.data) {
        document.getElementById('repMetricActive').textContent = res.data.total_active_events || 0;
        document.getElementById('repMetricCompleted').textContent = res.data.completed_events || 0;
        document.getElementById('repMetricFirstTimers').textContent = res.data.first_timers || 0;
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
          const isSubmitted = !!r.submitted_at || !!r.total_participants;

          let thresholdBadge = `<span class="badge threshold-met font-roboto">${turnoutPercent}% Met</span>`;
          if (turnoutPercent < 70) {
            thresholdBadge = `<span class="badge threshold-warn font-roboto">${turnoutPercent}% Below</span>`;
          }

          let statusBadge = isSubmitted 
            ? `<span class="badge approved font-roboto"><i class="ph ph-check-circle"></i> Submitted</span>` 
            : `<span class="badge pending font-roboto"><i class="ph ph-clock"></i> Pending Submission</span>`;

          let sheetBadge = r.attendance_sheet_path
            ? `<div style="margin-top: 3px;"><a href="${YTR_API.reports.getAttendanceSheetUrl(r.report_id || r.id)}" target="_blank" class="badge" style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 0.68rem; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;" title="Download Official Attendance Sheet"><i class="ph ph-file-arrow-down"></i> Sheet Attached</a></div>`
            : '';

          row.innerHTML = `
            <td>
              <div style="display: flex; align-items: center; gap: 0.85rem;">
                <div style="width: 38px; height: 38px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0;">
                  <i class="ph-fill ph-file-text"></i>
                </div>
                <div>
                  <p class="font-roboto" style="font-weight: 800; color: #0f172a; font-size: 0.875rem; margin: 0;">${r.event_title}</p>
                  <div style="display: flex; gap: 0.35rem; align-items: center; flex-wrap: wrap; margin-top: 3px;">
                    <span class="badge chapter-admin font-roboto">${r.chapter_name || 'Chapter Event'}</span>
                    ${sheetBadge}
                  </div>
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
            <td style="text-align: center;">${statusBadge}</td>
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
      const emptyState = document.getElementById('emptyReportState');
      const activeContent = document.getElementById('activeReportContent');
      if (emptyState) emptyState.style.display = 'none';
      if (activeContent) activeContent.style.display = 'block';

      const titleEl = document.getElementById('detailEventTitle');
      if (titleEl) titleEl.textContent = r.event_title || 'Event Report';

      const chapterEl = document.getElementById('detailChapterName');
      if (chapterEl) chapterEl.textContent = r.chapter_name || 'National Secretariat';

      const scheduleEl = document.getElementById('detailDateVenue');
      if (scheduleEl) scheduleEl.textContent = (r.start_datetime ? r.start_datetime.substring(0, 10) : 'TBD') + ' • ' + (r.venue || 'TBD');
      
      const total = r.total_participants || 0;
      const target = r.target_turnout || 100;
      const percent = r.turnout_percentage || (target > 0 ? Math.round((total / target) * 100) : 100);

      const turnoutBadge = document.getElementById('detailTurnoutBadge');
      if (turnoutBadge) {
        turnoutBadge.textContent = `${percent}% Target`;
        turnoutBadge.className = `badge font-roboto ${percent >= 100 ? 'threshold-met' : 'threshold-warn'}`;
      }

      const progressBar = document.getElementById('detailTurnoutProgressBar');
      if (progressBar) {
        progressBar.style.width = `${Math.min(percent, 100)}%`;
        progressBar.className = `threshold-fill ${percent >= 100 ? 'met' : 'warn'}`;
      }

      const turnoutText = document.getElementById('detailTurnoutText');
      if (turnoutText) {
        turnoutText.textContent = `${total} attendees out of ${target} target.`;
      }

      const totalEl = document.getElementById('detailTotal');
      if (totalEl) totalEl.textContent = total;

      const firstTimersEl = document.getElementById('detailFirstTimers');
      if (firstTimersEl) firstTimersEl.textContent = r.first_timers || 0;

      const membersEl = document.getElementById('detailMembers');
      if (membersEl) membersEl.textContent = r.existing_members || 0;

      const leadersEl = document.getElementById('detailLeaders');
      if (leadersEl) leadersEl.textContent = r.youth_leaders_count || 0;

      const notesEl = document.getElementById('detailNotes');
      if (notesEl) notesEl.textContent = r.report_summary || 'No summary notes submitted yet.';

      // Submitter Information
      const signerNameP = document.getElementById('detailSignedByName');
      const signedDateP = document.getElementById('detailSignedDate');
      const vBadge = document.getElementById('detailVerificationBadge');

      if (r.submitted_at || r.total_participants) {
        if (signerNameP) signerNameP.textContent = r.signed_by_name || 'Chapter Administrator';
        if (signedDateP) signedDateP.textContent = 'Submitted on ' + (r.signed_at || r.submitted_at || 'Recently');
        if (vBadge) {
          vBadge.style.display = 'inline-flex';
          vBadge.className = 'badge approved font-roboto';
          vBadge.innerHTML = '<i class="ph ph-shield-check"></i> Verified Chapter Dossier';
        }
      } else {
        if (signerNameP) signerNameP.textContent = 'Pending Submission';
        if (signedDateP) signedDateP.textContent = 'Awaiting chapter submission';
        if (vBadge) {
          vBadge.style.display = 'inline-flex';
          vBadge.className = 'badge pending font-roboto';
          vBadge.innerHTML = '<i class="ph ph-clock"></i> Awaiting Dossier';
        }
      }

      // Sidebar Attendance Sheet Download Link
      const sheetBox = document.getElementById('detailAttendanceSheetBox');
      const sheetBtn = document.getElementById('detailAttendanceSheetBtn');
      if (r.attendance_sheet_path) {
        if (sheetBtn) sheetBtn.href = YTR_API.reports.getAttendanceSheetUrl(r.report_id || r.id);
        if (sheetBox) sheetBox.style.display = 'block';
      } else {
        if (sheetBox) sheetBox.style.display = 'none';
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

    let currentSignatories = {
      chief_executive_name: 'Hamilton Jones Dela',
      chief_executive_title: 'Chief Executive Operation',
      chief_executive_signature: null
    };

    let activeSigMode = 'upload';
    let natCanvas, natCtx, isNatDrawing = false;
    let pendingSignatureBase64 = null;

    // Load National Executive Signatories
    async function loadSignatories() {
      try {
        const res = await YTR_API.reports.getSignatories();
        if (res.status === 'success' && res.data) {
          currentSignatories.chief_executive_name = res.data.chief_executive_name || 'Hamilton Jones Dela';
          currentSignatories.chief_executive_title = res.data.chief_executive_title || 'Chief Executive Operation';
          if (res.data.chief_executive_signature) {
            currentSignatories.chief_executive_signature = res.data.chief_executive_signature;
          }
        }
      } catch (err) {
        console.warn('Could not load signatories from API:', err);
      }

      // Check localStorage fallback
      const cachedSig = localStorage.getItem('ytr_national_signature');
      if (!currentSignatories.chief_executive_signature && cachedSig) {
        currentSignatories.chief_executive_signature = cachedSig;
      }

      updateSignatureUI();
    }

    function updateSignatureUI() {
      const container = document.getElementById('sigPreviewContainer');
      const badge = document.getElementById('printSigStatusBadge');
      const nameEl = document.getElementById('sigPreviewName');

      if (nameEl) nameEl.textContent = currentSignatories.chief_executive_name;

      if (currentSignatories.chief_executive_signature) {
        if (container) {
          container.innerHTML = `<img src="${currentSignatories.chief_executive_signature}" alt="National Signature" style="max-height: 60px; max-width: 220px; object-fit: contain;">`;
        }
        if (badge) {
          badge.className = 'badge approved font-roboto';
          badge.innerHTML = '<i class="ph ph-check"></i> Signature Attached';
        }
      } else {
        if (container) {
          container.innerHTML = '<span style="font-size: 0.75rem; color: #94a3b8; font-style: italic;">No signature loaded yet</span>';
        }
        if (badge) {
          badge.className = 'badge pending font-roboto';
          badge.innerHTML = '<i class="ph ph-clock"></i> No Signature Uploaded';
        }
      }
    }

    // Signature Manager Modal Handlers
    function openSignatureManagerModal() {
      const modal = document.getElementById('signatureModal');
      modal.classList.remove('hidden');
      modal.style.display = 'flex';
      initNationalCanvas();
      updateSignatureUI();
    }

    function closeSignatureManagerModal() {
      const modal = document.getElementById('signatureModal');
      modal.classList.add('hidden');
      modal.style.display = 'none';
      pendingSignatureBase64 = null;
    }

    function switchSigMode(mode) {
      activeSigMode = mode;
      const tabUpload = document.getElementById('tabUploadSig');
      const tabDraw = document.getElementById('tabDrawSig');
      const panelUpload = document.getElementById('sigUploadPanel');
      const panelDraw = document.getElementById('sigDrawPanel');

      if (mode === 'upload') {
        tabUpload.className = 'btn btn-primary btn-sm font-roboto';
        tabDraw.className = 'btn btn-outline btn-sm font-roboto';
        panelUpload.style.display = 'block';
        panelDraw.style.display = 'none';
      } else {
        tabUpload.className = 'btn btn-outline btn-sm font-roboto';
        tabDraw.className = 'btn btn-primary btn-sm font-roboto';
        panelUpload.style.display = 'none';
        panelDraw.style.display = 'block';
        initNationalCanvas();
      }
    }

    function handleSigFileSelect(e) {
      const file = e.target.files && e.target.files[0];
      if (!file) return;

      if (!file.type.startsWith('image/')) {
        alert('Please upload a valid image file (PNG, JPG, or WEBP).');
        return;
      }

      const reader = new FileReader();
      reader.onload = function(evt) {
        pendingSignatureBase64 = evt.target.result;
        const container = document.getElementById('sigPreviewContainer');
        container.innerHTML = `<img src="${pendingSignatureBase64}" alt="Uploaded Signature Preview" style="max-height: 60px; max-width: 220px; object-fit: contain;">`;
      };
      reader.readAsDataURL(file);
    }

    function initNationalCanvas() {
      natCanvas = document.getElementById('nationalSigCanvas');
      if (!natCanvas) return;
      natCtx = natCanvas.getContext('2d');
      natCtx.strokeStyle = '#0f172a';
      natCtx.lineWidth = 2.5;
      natCtx.lineCap = 'round';
      natCtx.lineJoin = 'round';

      function getPos(e) {
        const rect = natCanvas.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return {
          x: (clientX - rect.left) * (natCanvas.width / rect.width),
          y: (clientY - rect.top) * (natCanvas.height / rect.height)
        };
      }

      function startDraw(e) {
        isNatDrawing = true;
        const pos = getPos(e);
        natCtx.beginPath();
        natCtx.moveTo(pos.x, pos.y);
        e.preventDefault();
      }

      function moveDraw(e) {
        if (!isNatDrawing) return;
        const pos = getPos(e);
        natCtx.lineTo(pos.x, pos.y);
        natCtx.stroke();
        e.preventDefault();
      }

      function stopDraw() {
        if (!isNatDrawing) return;
        isNatDrawing = false;
        pendingSignatureBase64 = natCanvas.toDataURL('image/png');
        const container = document.getElementById('sigPreviewContainer');
        container.innerHTML = `<img src="${pendingSignatureBase64}" alt="Drawn Signature Preview" style="max-height: 60px; max-width: 220px; object-fit: contain;">`;
      }

      natCanvas.onmousedown = startDraw;
      natCanvas.onmousemove = moveDraw;
      window.addEventListener('mouseup', stopDraw);

      natCanvas.ontouchstart = startDraw;
      natCanvas.ontouchmove = moveDraw;
      natCanvas.ontouchend = stopDraw;
    }

    function clearNationalSigCanvas() {
      if (natCanvas && natCtx) {
        natCtx.clearRect(0, 0, natCanvas.width, natCanvas.height);
        pendingSignatureBase64 = null;
        updateSignatureUI();
      }
    }

    async function saveNationalSignature() {
      const sigToSave = pendingSignatureBase64 || currentSignatories.chief_executive_signature;
      if (!sigToSave) {
        alert('Please upload an image or draw your signature on the pad before saving.');
        return;
      }

      const btn = document.getElementById('btnSaveNationalSig');
      btn.disabled = true;
      btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Saving...';

      try {
        const res = await YTR_API.accounts.uploadSignature(sigToSave);
        if (res.status === 'success' || res.ok) {
          currentSignatories.chief_executive_signature = sigToSave;
          localStorage.setItem('ytr_national_signature', sigToSave);
          updateSignatureUI();
          alert('National Administrator signature saved successfully! It will automatically appear on all official report print documents.');
          closeSignatureManagerModal();

          // Refresh print preview if currently open
          if (document.getElementById('printModal').classList.contains('open')) {
            openPrintPreview();
          }
        } else {
          alert(res.message || 'Failed to save signature.');
        }
      } catch (err) {
        // Fallback to localStorage if offline
        currentSignatories.chief_executive_signature = sigToSave;
        localStorage.setItem('ytr_national_signature', sigToSave);
        updateSignatureUI();
        alert('Signature cached locally. It will be placed on official report prints.');
        closeSignatureManagerModal();
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="ph ph-check"></i> Save &amp; Apply Signature';
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
      const printProgEl = document.getElementById('printProgramPillar');
      if (printProgEl) {
        printProgEl.textContent = r.ytr_program || 'Lead up (Youth Leadership)';
      }
      document.getElementById('printChapterName').textContent = r.chapter_name || 'National Secretariat';
      document.getElementById('printScheduleVenue').textContent = (r.start_datetime ? r.start_datetime.substring(0, 10) : 'TBD') + ' • ' + (r.venue || 'TBD');
      document.getElementById('printBudgetSpent').textContent = '₱' + Number(r.budget_spent || 0).toLocaleString();
      
      document.getElementById('printTotalAtt').textContent = r.total_participants || 0;
      document.getElementById('printFirstTimers').textContent = r.first_timers || 0;
      document.getElementById('printMembers').textContent = r.existing_members || 0;
      document.getElementById('printLeaders').textContent = r.youth_leaders_count || 0;
      document.getElementById('printThresholdRate').textContent = (r.turnout_percentage || 100) + '% Performance';

      document.getElementById('printSummaryText').textContent = r.report_summary || 'Activities successfully conducted with full chapter participation.';
      
      // Attendance Sheet Attachment Card in Print Preview
      const attSheetCard = document.getElementById('printAttendanceSheetCard');
      const attSheetLink = document.getElementById('printAttendanceSheetLink');
      if (r.attendance_sheet_path) {
        if (attSheetLink) attSheetLink.href = YTR_API.reports.getAttendanceSheetUrl(r.report_id || r.id);
        if (attSheetCard) attSheetCard.style.display = 'flex';
      } else {
        if (attSheetCard) attSheetCard.style.display = 'none';
      }

      // 1. Submitter Signatory Block
      document.getElementById('printSignerName').textContent = r.signed_by_name || 'Chapter Administrator';
      document.getElementById('printSignerTimestamp').textContent = 'Verified on ' + (r.signed_at || r.submitted_at || new Date().toLocaleDateString());
      const submitterSigBox = document.getElementById('printSubmitterSigBox');
      if (r.e_signature && r.e_signature.trim().length > 5) {
        submitterSigBox.innerHTML = `<img src="${r.e_signature}" alt="Chapter Signature" style="max-height: 44px; max-width: 140px; object-fit: contain;">`;
      } else {
        submitterSigBox.innerHTML = `<span style="font-size: 0.72rem; color: #16a34a; font-weight: 600;"><i class="ph ph-check-circle"></i> Digital Dossier Signed</span>`;
      }

      // 2. Chief Executive Operation Signatory Block
      document.getElementById('printChiefExecName').textContent = currentSignatories.chief_executive_name || 'Hamilton Jones Dela';
      document.getElementById('printChiefExecTitle').textContent = currentSignatories.chief_executive_title || 'Chief Executive Operation';
      
      const natSigBox = document.getElementById('printNationalSigBox');
      const sigStatusBadge = document.getElementById('printSigStatusBadge');
      if (currentSignatories.chief_executive_signature) {
        natSigBox.innerHTML = `<img src="${currentSignatories.chief_executive_signature}" alt="Chief Executive Signature" style="max-height: 46px; max-width: 160px; object-fit: contain;">`;
        if (sigStatusBadge) {
          sigStatusBadge.className = 'badge approved font-roboto';
          sigStatusBadge.innerHTML = '<i class="ph ph-check"></i> Signature Attached';
        }
      } else {
        natSigBox.innerHTML = `<span style="font-size: 0.72rem; color: #d97706; font-style: italic;"><i class="ph ph-warning"></i> Signature Pending Upload</span>`;
        if (sigStatusBadge) {
          sigStatusBadge.className = 'badge pending font-roboto';
          sigStatusBadge.innerHTML = '<i class="ph ph-clock"></i> No Signature Uploaded';
        }
      }

      document.getElementById('printModal').classList.add('open');
    }

    function closePrintModal() {
      document.getElementById('printModal').classList.remove('open');
    }

    // Initialize on DOM load
    window.addEventListener('DOMContentLoaded', () => {
      loadSignatories();
      loadReportMetrics();
      loadReports();
    });
  </script>
</body>
</html>