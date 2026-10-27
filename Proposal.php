<?php include 'includes/auth_guard.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>YTR | Chapter Event Proposals</title>
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <link rel="stylesheet" href="assets/National.css?v=<?= time() ?>">
  <style>
    /* Proposal-specific modal and interactive styles */
    .modal-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.6);
      z-index: 100;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      backdrop-filter: blur(2px);
    }
    .modal-overlay.open {
      display: flex;
    }
    .modal-box {
      background: var(--white);
      border-radius: 1rem;
      width: 100%;
      max-width: 680px;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
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

    @media print {
      @page {
        size: A4 portrait;
        margin: 10mm 12mm;
      }
      *, *::before, *::after {
        box-shadow: none !important;
        text-shadow: none !important;
      }
      html, body {
        background: #ffffff !important;
        color: #0f172a !important;
        height: auto !important;
        min-height: 100% !important;
        overflow: visible !important;
        margin: 0 !important;
        padding: 0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
      .app-wrapper,
      .sidebar,
      .topbar,
      .page-header,
      .metrics-grid,
      .tabs-container,
      .search-bar,
      .layout-sidebar,
      .card,
      .print-no-print,
      .modal-header,
      .print-status-banner,
      .print-action-bar,
      button,
      .btn {
        display: none !important;
      }

      /* Unhide the proposal dossier modal container and render it as flat normal document flow */
      #proposalDossierModal {
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
      #proposalDossierModal .modal-box,
      #proposalDossierModal > div {
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
      #proposalDossierModal .modal-body {
        padding: 0 !important;
        margin: 0 !important;
        background: transparent !important;
      }

      #printableProposalDossier {
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
      #printableProposalDossier,
      #printableProposalDossier * {
        visibility: visible !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
      #printableProposalDossier table {
        width: 100% !important;
        border-collapse: collapse !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        margin-bottom: 1rem !important;
      }
      #printableProposalDossier tr {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
      }
      #printableProposalDossier th,
      #printableProposalDossier td {
        border: 1px solid #cbd5e1 !important;
        padding: 6px 10px !important;
      }
      #printableProposalDossier th {
        background: #1a2f63 !important;
        color: #ffffff !important;
      }
      #printableProposalDossier td {
        color: #0f172a !important;
      }
      .print-signatures-row {
        display: flex !important;
        flex-direction: row !important;
        justify-content: space-between !important;
        gap: 3rem !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        margin-top: 1.5rem !important;
        padding-top: 1rem !important;
        border-top: 2px solid #1a2f63 !important;
        visibility: visible !important;
      }
      .print-signatures-row > div {
        width: 46% !important;
        min-height: auto !important;
        visibility: visible !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
      }
      #dossierSubmitterName,
      #dossierSubmitterTitle,
      #dossierSubmitterStamp,
      #dossierReviewerName,
      #dossierReviewerTitle,
      #dossierReviewNotes {
        display: block !important;
        visibility: visible !important;
      }
      #dossierSubmitterName,
      #dossierReviewerName {
        font-size: 0.95rem !important;
        font-weight: 900 !important;
        color: #0f172a !important;
      }
      #dossierSubmitterTitle,
      #dossierReviewerTitle {
        font-size: 0.8rem !important;
        font-weight: 700 !important;
        color: #1e3a8a !important;
      }
    }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <!-- INJECT SIDEBAR -->
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-wrapper">
      <!-- INJECT TOPBAR -->
      <?php include 'includes/topbar.php'; ?>

      <main class="page-content">
        
        <!-- Page Header -->
        <div class="page-header">
          <div class="header-title-group">
            <div class="header-icon"><i class="ph-fill ph-file-plus"></i></div>
            <div>
              <h1 class="font-roboto">Chapter Event Proposals</h1>
              <p>Review, evaluate, and monitor upcoming event plans and budget proposals submitted by partner chapters.</p>
            </div>
          </div>
          <span class="portal-indicator national" style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 0.8rem; font-weight: 700; padding: 0.45rem 0.9rem; border-radius: 9999px; display: inline-flex; align-items: center; gap: 0.4rem;">
            <i class="ph-fill ph-shield-check"></i> National Evaluation & Approval Portal
          </span>
        </div>

        <!-- Metric Cards -->
        <div class="metrics-grid">
          <div class="metric-card metric-card-row">
            <div class="metric-icon blue"><i class="ph-fill ph-files"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Total Chapter Proposals</h5>
              <p class="metric-value font-roboto" id="propMetricTotal">...</p>
              <p class="metric-sub font-roboto">Across all chapters</p>
            </div>
          </div>
          <div class="metric-card metric-card-row">
            <div class="metric-icon amber"><i class="ph-fill ph-clock"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Pending Review</h5>
              <p class="metric-value font-roboto" id="propMetricPending">...</p>
              <p class="metric-sub font-roboto">Awaiting National evaluation</p>
            </div>
          </div>
          <div class="metric-card metric-card-row">
            <div class="metric-icon green"><i class="ph-fill ph-check-circle"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Approved Proposals</h5>
              <p class="metric-value font-roboto" id="propMetricApproved">...</p>
              <p class="metric-sub font-roboto">Cleared for poster creation</p>
            </div>
          </div>
          <div class="metric-card metric-card-row">
            <div class="metric-icon rose"><i class="ph-fill ph-warning-circle"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Needs Revision</h5>
              <p class="metric-value font-roboto" id="propMetricRevision">...</p>
              <p class="metric-sub font-roboto">Notes returned to chapters</p>
            </div>
          </div>
        </div>

        <!-- Full Width Layout -->
        <div class="layout-main" style="width: 100%;">
            
            <!-- High-Contrast Visual Pending Queue Banner -->
            <div id="pendingReviewBanner" class="pending-alert-banner" style="display: none;">
              <div class="pending-banner-left">
                <div class="pending-banner-icon"><i class="ph-fill ph-bell-ringing"></i></div>
                <div class="pending-banner-text">
                  <h4 class="font-roboto"><span id="pendingBannerCount">0</span> Proposals Awaiting National Approval</h4>
                  <p class="font-roboto">Review chapter submissions promptly to maintain event lead times and activate poster production.</p>
                </div>
              </div>
              <button class="pending-banner-btn font-roboto" onclick="filterProposals('pending', document.querySelectorAll('.tabs-container .tab-btn')[1])">
                <i class="ph ph-lightning"></i> Review Queue
              </button>
            </div>

            <div class="table-section">
              
              <!-- Tabs for Filter -->
              <div class="tabs-container font-roboto">
                <button class="tab-btn active" onclick="filterProposals('all', this)"><i class="ph ph-files"></i> All Proposals</button>
                <button class="tab-btn" onclick="filterProposals('pending', this)"><i class="ph ph-clock"></i> Pending Review</button>
                <button class="tab-btn" onclick="filterProposals('approved', this)"><i class="ph ph-check-circle"></i> Approved</button>
                <button class="tab-btn" onclick="filterProposals('revision', this)"><i class="ph ph-warning-circle"></i> Needs Revision</button>
              </div>

              <!-- Filter & Search Bar -->
              <div class="table-header">
                <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
                  <select id="chapterFilterSelect" class="form-select font-roboto" style="width: auto; padding: 0.45rem 0.85rem;" onchange="filterByChapter(this.value)">
                    <option value="all">All Chapters</option>
                    <option value="Laguna Chapter">Laguna Chapter</option>
                    <option value="NCR Chapter">NCR Chapter</option>
                    <option value="Cavite Chapter">Cavite Chapter</option>
                    <option value="Pampanga Chapter">Pampanga Chapter</option>
                    <option value="Batangas Chapter">Batangas Chapter</option>
                  </select>
                </div>
                <div class="search-bar">
                  <i class="ph ph-magnifying-glass"></i>
                  <input type="text" id="proposalSearchInput" class="font-roboto" placeholder="Search by event or chapter..." onkeyup="searchProposals()">
                </div>
              </div>

              <!-- Data Table -->
              <div class="table-responsive">
                <table id="proposalsTable">
                  <thead class="font-roboto">
                    <tr>
                      <th>Event Proposal</th>
                      <th>Chapter</th>
                      <th>Proposed Date & Venue</th>
                      <th style="text-align: center;">Target / Budget</th>
                      <th style="text-align: center;">Status</th>
                      <th style="text-align: center;">Action</th>
                    </tr>
                  </thead>
                  <tbody id="proposalsTableBody">
                    <tr>
                      <td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748b;">
                        <i class="ph ph-spinner" style="font-size: 1.5rem; animation: spin 1s linear infinite;"></i>
                        <br>Loading chapter proposals...
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>

      
  <!-- MODAL: PROPOSAL EVALUATION & REVIEW (POP UP VIEW) -->
  <div id="proposalReviewModal" class="modal-overlay hidden" style="display: none;" onclick="if (event.target === this) closeProposalReviewModal()">
    <div class="modal-md" style="max-width: 640px; width: 95%; max-height: 90vh; background: #ffffff; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
      <div class="modal-header-banner" style="background: #eff6ff; border-bottom: 1px solid #bfdbfe; padding: 1.1rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
          <div style="background: #dbeafe; color: #1e40af; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0;">
            <i class="ph-fill ph-file-plus"></i>
          </div>
          <div>
            <h3 id="modalProposalTitle" class="font-roboto" style="color: #1e3a8a; margin: 0; font-size: 1.05rem; font-weight: 800;">Proposal Evaluation</h3>
            <p id="modalProposalChapter" class="font-roboto" style="color: #2563eb; margin: 0; font-size: 0.78rem; font-weight: 600;">Laguna Chapter partner Chapter</p>
          </div>
        </div>
        <button type="button" onclick="closeProposalReviewModal()" style="background: none; border: none; font-size: 1.4rem; color: #64748b; cursor: pointer; padding: 0.2rem; display: flex; align-items: center; justify-content: center; border-radius: 6px;" title="Close"><i class="ph ph-x"></i></button>
      </div>

      <div class="modal-body-scroll" style="background: #ffffff; padding: 1.25rem; overflow-y: auto; max-height: calc(90vh - 130px);">
        <!-- Badges Container -->
        <div id="detailProposalBadges" style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.5rem;"></div>

        <div class="review-detail-group" style="margin-bottom: 0.85rem;">
          <label class="font-roboto"><i class="ph-fill ph-compass"></i> YTR Program Pillar</label>
          <div style="margin-top: 3px;">
            <span id="detailProposalProgram" class="badge font-roboto" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.78rem; font-weight: 700; padding: 4px 8px;">
              Lead up (Youth Leadership)
            </span>
          </div>
        </div>

        <div class="review-detail-group" style="margin-bottom: 0.85rem;">
          <label class="font-roboto"><i class="ph-fill ph-users-three"></i> Target Audience</label>
          <div style="margin-top: 3px;">
            <span id="detailProposalAudience" class="badge font-roboto" style="background: #f1f5f9; color: #0f172a; border: 1px solid #cbd5e1; font-size: 0.78rem; font-weight: 700; padding: 4px 8px;">
              Open for All
            </span>
          </div>
        </div>

        <div class="review-detail-group" style="margin-bottom: 0.85rem;">
          <label class="font-roboto"><i class="ph-fill ph-calendar-blank"></i> Proposed Schedule &amp; Venue</label>
          <p id="detailProposalSchedule" class="font-roboto" style="font-size: 0.85rem; color: #0f172a; font-weight: bold; margin: 3px 0 0;">-</p>
          <p id="detailApprovalTimer" style="font-size: 0.75rem; color: #166534; font-weight: 700; margin-top: 2px;"></p>
        </div>

        <!-- Attendance & Budget Thresholds -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.65rem; margin: 0.85rem 0;">
          <div style="background: #f8fafc; padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
              <span style="font-size: 0.7rem; color: #334155; font-weight: bold;">Attendance Threshold</span>
              <strong id="detailProposalTurnout" style="color: #1a2f63; font-size: 0.85rem;">100 Target</strong>
            </div>
            <div class="threshold-bar">
              <div id="detailTurnoutFill" class="threshold-fill met" style="width: 100%;"></div>
            </div>
          </div>

          <div style="background: #f8fafc; padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border); display: flex; flex-direction: column; justify-content: center;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <span style="font-size: 0.7rem; color: #334155; font-weight: bold;">Budget Threshold</span>
              <span id="detailProposalBudget" class="badge threshold-met font-roboto">₱0 / ₱25,000 Cap</span>
            </div>
          </div>
        </div>

        <div class="review-detail-group" style="margin-bottom: 0.85rem;">
          <label class="font-roboto"><i class="ph-fill ph-article"></i> Objectives &amp; Concept Description</label>
          <div id="detailProposalNotes" class="reason-box font-roboto" style="font-size: 0.8rem; max-height: 140px; overflow-y: auto; line-height: 1.45; background: #f8fafc; padding: 0.65rem 0.85rem; border-radius: 8px; border: 1px solid #e2e8f0;">
            -
          </div>
        </div>

        <div class="review-detail-group" style="margin-bottom: 1rem;">
          <label class="font-roboto"><i class="ph-fill ph-file-text"></i> Event Proposal Soft Copy &amp; Dossier</label>
          <div style="display: flex; flex-direction: column; gap: 0.45rem; margin-top: 4px;">
            <button type="button" class="btn btn-primary btn-sm font-roboto" id="btnOpenProposalDossier" onclick="openProposalDossier(activeProposalId)" style="background: #1e3a8a; border-color: #172554; display: flex; align-items: center; justify-content: center; gap: 0.45rem; padding: 0.55rem 0.75rem; width: 100%; text-decoration: none; border-radius: 6px;">
              <i class="ph-bold ph-printer"></i> View &amp; Download Soft Copy (Dossier PDF)
            </button>
            <div id="detailProposalAttachmentContainer"></div>
          </div>
        </div>

        <!-- Decision Actions -->
        <div id="proposalActionButtons" style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1rem;">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.65rem;">
            <button class="btn btn-outline-danger font-roboto" onclick="handleReviewDecision('draft')">
              <i class="ph ph-pencil-simple"></i> Request Revision
            </button>
            <button class="btn btn-primary font-roboto" onclick="handleReviewDecision('approved')">
              <i class="ph ph-check"></i> Approve Proposal
            </button>
          </div>
        </div>
      </div>

      <div class="modal-footer" style="padding: 0.85rem 1.25rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end;">
        <button type="button" class="btn btn-outline font-roboto" onclick="closeProposalReviewModal()">Close</button>
      </div>
    </div>
  </div>

      </main>
    </div> <!-- /.main-wrapper -->
  </div> <!-- /.app-wrapper -->

  <!-- PROPOSAL DOSSIER & SOFT COPY MODAL -->
  <div id="proposalDossierModal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 1050; align-items: center; justify-content: center; padding: 1.5rem;">
    <div class="modal-box" style="max-width: 820px; width: 100%; max-height: 90vh; overflow-y: auto; background: #ffffff; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
      <div class="modal-header print-no-print">
        <h3 class="font-roboto" style="font-size: 1.1rem; color: #0f172a; font-weight: 800; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
          <i class="ph-bold ph-file-text" style="color: #2563eb;"></i> Official Event Proposal Dossier (Soft Copy)
        </h3>
        <button type="button" onclick="closeProposalDossierModal()" style="background: none; border: none; font-size: 1.25rem; color: var(--text-muted); cursor: pointer;"><i class="ph ph-x"></i></button>
      </div>

      <div class="modal-body">
        <!-- Notice toolbar -->
        <div class="print-status-banner print-no-print" style="display: flex; align-items: center; justify-content: space-between; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 0.65rem 1rem; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
          <div style="display: flex; align-items: center; gap: 0.65rem;">
            <div style="width: 32px; height: 32px; border-radius: 6px; background: #dbeafe; color: #1d4ed8; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0;">
              <i class="ph-bold ph-download-simple"></i>
            </div>
            <div>
              <div style="font-size: 0.85rem; font-weight: 700; color: #1e40af;">
                Official Proposal Soft Copy Ready for Download
              </div>
              <div style="font-size: 0.72rem; color: #475569;">
                Save this complete dossier as PDF via browser print, or download any uploaded chapter attachment below.
              </div>
            </div>
          </div>
          <div id="dossierAttachmentDownloadBtnContainer"></div>
        </div>

        <!-- Printable Document Sheet -->
        <div id="printableProposalDossier" class="print-document-sheet" style="background: #ffffff; padding: 2rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: 'Roboto', sans-serif;">
          
          <!-- Official Letterhead Header -->
          <div class="print-letterhead" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 3px double #1a2f63; padding-bottom: 0.85rem; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.85rem;">
              <img src="assets/images/LOGO.jpg" alt="YTR Logo" style="width: 52px; height: 52px; object-fit: contain; border-radius: 6px; border: 1px solid #cbd5e1; padding: 2px; background: #ffffff; flex-shrink: 0;">
              <div>
                <h2 style="font-size: 1.25rem; font-weight: 900; color: #1a2f63; margin: 0; text-transform: uppercase; letter-spacing: 0.04em;">YOUTH ON THE ROCK NATIONAL MOVEMENT</h2>
                <p style="font-size: 0.78rem; color: #334155; margin: 2px 0 0; text-transform: uppercase; font-weight: 700;">Community-Centric Event Monitoring &amp; Reporting Platform</p>
                <p style="font-size: 0.72rem; color: #64748b; margin: 1px 0 0;">Official Chapter Event Proposal Dossier &amp; Executive Clearance Record</p>
              </div>
            </div>
            <div style="text-align: right; flex-shrink: 0;">
              <span id="dossierRefCode" style="display: inline-block; padding: 4px 10px; background: #eff6ff; color: #1e3a8a; font-weight: 800; font-size: 0.78rem; border-radius: 4px; border: 1px solid #bfdbfe;">
                PROP #YTR-2026-0001
              </span>
              <p id="dossierSubmissionDate" style="font-size: 0.7rem; color: #64748b; margin-top: 4px; font-weight: 600;">Submitted: Oct 14, 2026</p>
            </div>
          </div>

          <!-- Section 1: Event Information Matrix -->
          <h4 style="font-size: 0.9rem; font-weight: 800; color: #1a2f63; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.03em;">
            1. Event Specifications &amp; Classifications
          </h4>
          <table style="width: 100%; border: 1px solid #cbd5e1; margin-bottom: 1.25rem; border-collapse: collapse; font-size: 0.825rem;">
            <tbody>
              <tr style="background: #f8fafc;">
                <td style="padding: 7px 12px; font-weight: bold; width: 25%; border: 1px solid #cbd5e1; color: #334155;">Event Title</td>
                <td id="dossierTitle" style="padding: 7px 12px; font-weight: 800; color: #1a2f63; font-size: 0.9rem; border: 1px solid #cbd5e1;">-</td>
              </tr>
              <tr>
                <td style="padding: 7px 12px; font-weight: bold; border: 1px solid #cbd5e1; color: #334155;">YTR Program Pillar</td>
                <td id="dossierProgram" style="padding: 7px 12px; font-weight: 700; color: #1e40af; border: 1px solid #cbd5e1;">-</td>
              </tr>
              <tr style="background: #f8fafc;">
                <td style="padding: 7px 12px; font-weight: bold; border: 1px solid #cbd5e1; color: #334155;">Target Audience / Demographics</td>
                <td id="dossierAudience" style="padding: 7px 12px; font-weight: 700; color: #0f172a; border: 1px solid #cbd5e1;">-</td>
              </tr>
              <tr>
                <td style="padding: 7px 12px; font-weight: bold; border: 1px solid #cbd5e1; color: #334155;">Host Chapter</td>
                <td id="dossierChapter" style="padding: 7px 12px; font-weight: 700; border: 1px solid #cbd5e1;">-</td>
              </tr>
              <tr>
                <td style="padding: 7px 12px; font-weight: bold; border: 1px solid #cbd5e1; color: #334155;">Event Classification</td>
                <td id="dossierEventType" style="padding: 7px 12px; border: 1px solid #cbd5e1; text-transform: capitalize;">-</td>
              </tr>
              <tr style="background: #f8fafc;">
                <td style="padding: 7px 12px; font-weight: bold; border: 1px solid #cbd5e1; color: #334155;">Schedule (Start - End)</td>
                <td id="dossierSchedule" style="padding: 7px 12px; border: 1px solid #cbd5e1;">-</td>
              </tr>
              <tr>
                <td style="padding: 7px 12px; font-weight: bold; border: 1px solid #cbd5e1; color: #334155;">Official Venue / Location</td>
                <td id="dossierVenue" style="padding: 7px 12px; border: 1px solid #cbd5e1; font-weight: 600;">-</td>
              </tr>
            </tbody>
          </table>

          <!-- Section 2: Turnout Goal, Financial Allocation & Deadlines -->
          <h4 style="font-size: 0.9rem; font-weight: 800; color: #1a2f63; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.03em;">
            2. Attendance Thresholds &amp; Resource Allocation
          </h4>
          <table style="width: 100%; border: 1px solid #cbd5e1; margin-bottom: 1.25rem; text-align: center; border-collapse: collapse; font-size: 0.825rem;">
            <thead>
              <tr style="background: #1a2f63; color: #ffffff;">
                <th style="padding: 7px; border: 1px solid #cbd5e1;">Target Attendance Turnout</th>
                <th style="padding: 7px; border: 1px solid #cbd5e1;">Proposed Budget Request</th>
                <th style="padding: 7px; border: 1px solid #cbd5e1;">Chapter Budget Ceiling</th>
                <th style="padding: 7px; border: 1px solid #cbd5e1;">Post-Event Report Due Date</th>
              </tr>
            </thead>
            <tbody>
              <tr style="font-weight: bold;">
                <td id="dossierTurnout" style="padding: 8px; border: 1px solid #cbd5e1; color: #1e40af; font-size: 0.9rem;">-</td>
                <td id="dossierBudget" style="padding: 8px; border: 1px solid #cbd5e1; color: #0f172a; font-size: 0.9rem;">-</td>
                <td id="dossierThreshold" style="padding: 8px; border: 1px solid #cbd5e1; color: #15803d; font-size: 0.9rem;">-</td>
                <td id="dossierDueDate" style="padding: 8px; border: 1px solid #cbd5e1; color: #c2410c; font-size: 0.9rem;">-</td>
              </tr>
            </tbody>
          </table>

          <!-- Section 3: Event Concept & Program Outline -->
          <h4 style="font-size: 0.9rem; font-weight: 800; color: #1a2f63; margin-bottom: 0.35rem; text-transform: uppercase; letter-spacing: 0.03em;">
            3. Program Concept, Objectives &amp; Ministry Scope
          </h4>
          <div id="dossierDescription" style="padding: 0.85rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.8rem; line-height: 1.6; color: #1e293b; white-space: pre-line; margin-bottom: 1.25rem;">
            -
          </div>

          <!-- Section 4: Attached Soft Copy Reference -->
          <h4 style="font-size: 0.9rem; font-weight: 800; color: #1a2f63; margin-bottom: 0.35rem; text-transform: uppercase; letter-spacing: 0.03em;">
            4. Attached Submission Files &amp; Collaterals
          </h4>
          <div id="dossierAttachmentInfo" style="padding: 0.75rem 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; font-size: 0.8rem; margin-bottom: 1.5rem;">
            -
          </div>

          <!-- Section 5: Official Endorsements & Evaluation Block -->
          <div class="print-signatures-row" style="display: flex; justify-content: space-between; gap: 3rem; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 2px solid #1a2f63;">
            <!-- Prepared By -->
            <div style="flex: 1; display: flex; flex-direction: column; justify-content: flex-end; min-height: 95px; text-align: left;">
              <div style="font-size: 0.65rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px;">
                Prepared &amp; Submitted by:
              </div>
              <div style="height: 38px; display: flex; align-items: flex-end; margin-bottom: 4px;">
                <span style="font-size: 0.72rem; color: #16a34a; font-weight: 700;"><i class="ph ph-check-circle"></i> Digital Chapter Endorsement</span>
              </div>
              <div style="border-top: 1.5px solid #0f172a; width: 100%; margin-bottom: 4px;"></div>
              <p id="dossierSubmitterName" style="font-size: 0.825rem; font-weight: 800; margin: 0; color: #0f172a;">Chapter Coordinator</p>
              <p id="dossierSubmitterTitle" style="font-size: 0.72rem; color: #475569; margin: 1px 0 0;">Chapter Administrator</p>
              <p id="dossierSubmitterStamp" style="font-size: 0.65rem; color: #64748b; margin: 1px 0 0;">Official Chapter Submission</p>
            </div>

            <!-- Evaluated By -->
            <div style="flex: 1; display: flex; flex-direction: column; justify-content: flex-end; min-height: 95px; text-align: left;">
              <div style="font-size: 0.65rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px;">
                National Secretariat Evaluation:
              </div>
              <div style="height: 38px; display: flex; align-items: flex-end; margin-bottom: 4px;">
                <span id="dossierStatusBadge" class="badge approved font-roboto" style="font-size: 0.75rem; padding: 3px 8px;">
                  APPROVED
                </span>
              </div>
              <div style="border-top: 1.5px solid #0f172a; width: 100%; margin-bottom: 4px;"></div>
              <p id="dossierReviewerName" style="font-size: 0.825rem; font-weight: 800; margin: 0; color: #0f172a;">Hamilton Jones Dela</p>
              <p id="dossierReviewerTitle" style="font-size: 0.72rem; color: #1e3a8a; font-weight: 700; margin: 1px 0 0;">Chief Executive Operation</p>
              <p id="dossierReviewNotes" style="font-size: 0.65rem; color: #64748b; margin: 1px 0 0;">Cleared for Chapter Execution</p>
            </div>
          </div>

        </div>

        <div class="print-no-print print-action-bar" style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.25rem;">
          <button type="button" class="btn btn-outline font-roboto" onclick="closeProposalDossierModal()">Close</button>
          <button type="button" class="btn btn-primary font-roboto" onclick="window.print()" style="background: #1e3a8a; border-color: #172554;">
            <i class="ph-bold ph-printer"></i> Print / Save as PDF (Soft Copy)
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="assets/api-client.js"></script>
  <!-- Interactive JavaScript Logic -->
  <script>
    let activeProposalId = null;
    let currentStatus = 'all';
    let currentChapter = 'all';
    let allProposalsData = [];

    // Load Metrics from Python backend
    async function loadProposalMetrics() {
      const res = await YTR_API.proposals.getMetrics();
      if (res.status === 'success' && res.data) {
        document.getElementById('propMetricTotal').textContent = res.data.total_proposals || 0;
        document.getElementById('propMetricPending').textContent = res.data.pending_review || 0;
        document.getElementById('propMetricApproved').textContent = res.data.approved || 0;
        document.getElementById('propMetricRevision').textContent = res.data.needs_revision || 0;

        // Visual Interface for Pending Proposals
        const banner = document.getElementById('pendingReviewBanner');
        const countSpan = document.getElementById('pendingBannerCount');
        if (res.data.pending_review > 0) {
          banner.style.display = 'flex';
          countSpan.textContent = res.data.pending_review;
        } else {
          banner.style.display = 'none';
        }
      }
    }

    // Load Chapters for Dropdowns
    async function loadChapters() {
      const res = await YTR_API.chapters.getAll();
      if (res.status === 'success' && Array.isArray(res.data)) {
        const filterSelect = document.getElementById('chapterFilterSelect');
        const formSelect = document.getElementById('formPropChapter');
        
        filterSelect.innerHTML = '<option value="all">All Chapters</option>';
        formSelect.innerHTML = '<option value="" disabled selected>Select Chapter</option>';

        res.data.forEach(ch => {
          const opt1 = document.createElement('option');
          opt1.value = ch.id;
          opt1.textContent = ch.chapter_name;
          filterSelect.appendChild(opt1);

          const opt2 = document.createElement('option');
          opt2.value = ch.id;
          opt2.textContent = ch.chapter_name + (ch.province ? ` (${ch.province})` : '');
          formSelect.appendChild(opt2);
        });
      }
    }

    function selectProposalById(id) {
      const p = allProposalsData.find(item => item.id == id);
      if (p) selectProposalRecord(p);
    }

    // Load Proposals list
    async function loadProposals() {
      const tbody = document.getElementById('proposalsTableBody');
      try {
        const search = (document.getElementById('proposalSearchInput')?.value || '').trim();
        const res = await YTR_API.proposals.list(currentStatus, currentChapter, search);
        if (res.status === 'success' && Array.isArray(res.data)) {
          allProposalsData = res.data;
          if (res.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748b;"><i class="ph ph-file-dashed" style="font-size: 2rem; color: #94a3b8; display: block; margin-bottom: 0.5rem;"></i>No proposals match the selected filters.</td></tr>';
            return;
          }

          tbody.innerHTML = '';
          res.data.forEach((p, idx) => {
            const row = document.createElement('tr');
            const isPending = p.status === 'pending_approval';
            const isApproved = ['approved', 'published', 'ongoing', 'completed'].includes(p.status);
            const isRevision = p.status === 'draft';

            let statusBadge = '<span class="badge pending font-roboto">Pending Review</span>';
            if (isApproved) statusBadge = '<span class="badge approved font-roboto"><i class="ph ph-check"></i> Approved</span>';
            if (isRevision) statusBadge = '<span class="badge pending font-roboto" style="background: #fff1f2; color: #e11d48; border-color: #fecdd3;">Needs Revision</span>';

            // Due Date Badge
            let dueBadgeHtml = '';
            if (p.due_badge) {
              const bText = typeof p.due_badge === 'object' ? (p.due_badge.label || p.due_badge.text) : p.due_badge;
              const bType = typeof p.due_badge === 'object' ? (p.due_badge.type || 'on_track') : 'on_track';
              const badgeClass = bType === 'overdue' ? 'badge overdue' : (bType === 'due_soon' ? 'badge due-soon' : 'badge timer');
              const icon = bType === 'overdue' ? 'ph-warning-octagon' : 'ph-clock';
              dueBadgeHtml = `<span class="${badgeClass} font-roboto" style="margin-top: 4px; display: inline-flex;"><i class="ph ${icon}"></i> ${escapeHtml(bText)}</span>`;
            }

            // Approval Timer Badge
            let timerBadgeHtml = '';
            if (p.approval_timer) {
              const tText = typeof p.approval_timer === 'object' ? (p.approval_timer.label || p.approval_timer.text) : p.approval_timer;
              timerBadgeHtml = `<span class="badge timer font-roboto" style="margin-top: 4px; display: inline-flex;"><i class="ph ph-hourglass-high"></i> ${escapeHtml(tText)}</span>`;
            }

            const startDate = p.start_datetime ? p.start_datetime.substring(0, 10) : 'TBD';
            const budgetFormatted = Number(p.budget_amount || 0).toLocaleString();
            const targetTurnout = p.target_turnout || 100;

            row.className = 'proposal-row';
            row.innerHTML = `
              <td data-label="Event Proposal">
                <div class="td-user">
                  <div style="width: 36px; height: 36px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0;">
                    <i class="ph-fill ph-compass"></i>
                  </div>
                  <div>
                    <span class="proposal-name font-roboto" style="font-weight: 800; color: #0f172a; font-size: 0.9rem; display: block;">${escapeHtml(p.title)}</span>
                    <div style="display: flex; gap: 0.35rem; align-items: center; flex-wrap: wrap; margin: 2px 0;">
                      <span class="td-sub font-roboto" style="font-weight: 600; color: #2563eb;">${escapeHtml(String(p.event_type || 'chapter').toUpperCase())} EVENT</span>
                      ${p.ytr_program ? `<span class="badge font-roboto" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.7rem; padding: 1px 6px; font-weight: 700;"><i class="ph-bold ph-compass"></i> ${escapeHtml(p.ytr_program)}</span>` : ''}
                      <span class="badge font-roboto" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; font-size: 0.7rem; padding: 1px 6px; font-weight: 600;"><i class="ph ph-users-three"></i> ${escapeHtml(p.target_audience || 'Open for All')}</span>
                      ${p.attachment_path ? `<span class="badge font-roboto" style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-size: 0.7rem; padding: 1px 6px;" title="Proposal File Soft Copy Attached"><i class="ph-bold ph-paperclip"></i> Soft Copy</span>` : ''}
                      ${p.drive_link ? `<a href="${escapeHtml(p.drive_link)}" target="_blank" rel="noopener noreferrer" class="badge font-roboto" style="background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; font-size: 0.7rem; padding: 1px 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 2px;" title="Open Cloud / Google Drive Proposal"><i class="ph-bold ph-google-drive-logo"></i> Drive</a>` : ''}
                    </div>
                    <div>${timerBadgeHtml} ${dueBadgeHtml}</div>
                  </div>
                </div>
              </td>
              <td data-label="Chapter"><span class="badge chapter-admin font-roboto">${escapeHtml(p.chapter_name || 'All Chapters')}</span></td>
              <td data-label="Schedule & Venue">
                <p class="font-roboto" style="font-size: 0.825rem; font-weight: bold; color: #0f172a; margin: 0;">${startDate}</p>
                <p class="td-sub font-roboto" style="margin: 2px 0 0;"><i class="ph ph-map-pin"></i> ${escapeHtml(p.venue || 'TBD')}</p>
              </td>
              <td data-label="Target / Budget" style="text-align: center;">
                <p class="font-roboto" style="font-weight: bold; font-size: 0.85rem; color: #0f172a; margin: 0;"><i class="ph-fill ph-users" style="color: #60a5fa;"></i> ${targetTurnout} Target</p>
                <p class="td-sub font-roboto" style="margin: 2px 0 0;">₱${budgetFormatted}</p>
              </td>
              <td data-label="Status" style="text-align: center;">${statusBadge}</td>
              <td data-label="Action" style="text-align: center;">
                <div style="display: flex; gap: 0.35rem; justify-content: center; align-items: center;">
                  <button class="btn btn-primary btn-sm font-roboto" onclick="selectProposalById(${p.id})">
                    <i class="ph ph-eye"></i> ${isPending ? 'Review' : 'View'}
                  </button>
                  <button class="btn btn-outline btn-sm font-roboto" title="View &amp; Download Soft Copy (Dossier PDF)" onclick="openProposalDossier(${p.id})" style="padding: 0.4rem 0.55rem; color: #1e3a8a; border-color: #cbd5e1;">
                    <i class="ph-bold ph-printer"></i>
                  </button>
                </div>
              </td>
            `;
            tbody.appendChild(row);

          });

          if (allProposalsData.length > 0 && activeProposalId) {
            const currentExists = allProposalsData.some(p => p.id === activeProposalId);
            if (!currentExists) activeProposalId = null;
          }
        } else {
          tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2.5rem; color: #ef4444;"><i class="ph ph-warning-circle" style="font-size: 1.5rem; margin-bottom: 0.25rem;"></i><br>' + (res.message || 'Unable to load proposals from backend.') + '</td></tr>';
        }
      } catch (err) {
        console.error('Failed to load proposals:', err);
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2.5rem; color: #ef4444;"><i class="ph ph-warning-circle" style="font-size: 1.5rem; margin-bottom: 0.25rem;"></i><br>Network or service error loading proposals.</td></tr>';
      }
    }

    function closeProposalReviewModal() {
      const modal = document.getElementById('proposalReviewModal');
      if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
      }
    }

    // Select proposal to review (Pop Up View)
    function selectProposalRecord(p) {
      activeProposalId = p.id;
      const modal = document.getElementById('proposalReviewModal');
      if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
      }
      const titleModal = document.getElementById('modalProposalTitle');
      if (titleModal) titleModal.textContent = p.title;
      const chapModal = document.getElementById('modalProposalChapter');
      if (chapModal) chapModal.textContent = p.chapter_name || 'Laguna Chapter partner Chapter';
      document.getElementById('emptyProposalState').style.display = 'none';
      document.getElementById('activeProposalContent').style.display = 'block';

      document.getElementById('detailProposalTitle').textContent = p.title;
      document.getElementById('detailProposalChapter').textContent = p.chapter_name || 'National HQ';
      
      const programEl = document.getElementById('detailProposalProgram');
      if (programEl) {
        const prog = p.ytr_program || 'Lead up (Youth Leadership)';
        programEl.textContent = prog;
        if (prog.startsWith('Rise up')) {
          programEl.style.background = '#fffbeb'; programEl.style.color = '#b45309'; programEl.style.borderColor = '#fde68a';
        } else if (prog.startsWith('Build up')) {
          programEl.style.background = '#f0fdf4'; programEl.style.color = '#15803d'; programEl.style.borderColor = '#bbf7d0';
        } else if (prog.startsWith('Gear up')) {
          programEl.style.background = '#faf5ff'; programEl.style.color = '#7e22ce'; programEl.style.borderColor = '#e9d5ff';
        } else {
          programEl.style.background = '#eff6ff'; programEl.style.color = '#1d4ed8'; programEl.style.borderColor = '#bfdbfe';
        }
      }

      const audienceEl = document.getElementById('detailProposalAudience');
      if (audienceEl) {
        audienceEl.textContent = p.target_audience || 'Open for All';
      }

      document.getElementById('detailProposalSchedule').textContent = (p.start_datetime ? p.start_datetime.substring(0, 10) : 'TBD') + ' • ' + (p.venue || 'TBD');
      document.getElementById('detailProposalNotes').textContent = p.description || 'No description provided.';

      // Badges Container
      const badgesContainer = document.getElementById('detailProposalBadges');
      badgesContainer.innerHTML = '';

      // Status Badge
      const isApproved = ['approved', 'published', 'ongoing', 'completed'].includes(p.status);
      const isPending = p.status === 'pending_approval';
      const statusBadge = document.createElement('span');
      statusBadge.className = isApproved ? 'badge approved font-roboto' : (isPending ? 'badge pending font-roboto' : 'badge rose font-roboto');
      statusBadge.textContent = p.status.toUpperCase();
      badgesContainer.appendChild(statusBadge);

      // Due Badge
      if (p.due_badge) {
        const dueSpan = document.createElement('span');
        dueSpan.className = (p.due_badge.type === 'overdue' ? 'badge overdue' : 'badge due-soon') + ' font-roboto';
        dueSpan.innerHTML = `<i class="ph ph-clock"></i> ${p.due_badge.text}`;
        badgesContainer.appendChild(dueSpan);
      }

      // Approval Timer
      const timerP = document.getElementById('detailApprovalTimer');
      if (p.approval_timer) {
        timerP.textContent = `⏱️ ${p.approval_timer}`;
        timerP.style.display = 'block';
      } else {
        timerP.style.display = 'none';
      }

      // Thresholds: Turnout
      const targetTurnout = p.target_turnout || 100;
      document.getElementById('detailProposalTurnout').textContent = `${targetTurnout} Attendees Target`;
      document.getElementById('detailTurnoutFill').style.width = '100%';

      // Thresholds: Budget
      const budgetAmt = Number(p.budget_amount || 0);
      const budgetCap = Number(p.budget_threshold || 25000);
      const budgetBadge = document.getElementById('detailProposalBudget');
      if (budgetAmt > budgetCap) {
        budgetBadge.className = 'badge threshold-warn font-roboto';
        budgetBadge.textContent = `₱${budgetAmt.toLocaleString()} (Exceeds ₱${budgetCap.toLocaleString()} Cap)`;
      } else {
        budgetBadge.className = 'badge threshold-met font-roboto';
        budgetBadge.textContent = `₱${budgetAmt.toLocaleString()} / ₱${budgetCap.toLocaleString()} Cap (Passed)`;
      }

      // Populate attached file link in sidebar
      const attBox = document.getElementById('detailProposalAttachmentContainer');
      if (attBox) {
        let attHtml = '';
        if (p.attachment_path) {
          attHtml += `
            <a href="${p.attachment_path}" download class="btn btn-outline btn-sm font-roboto" style="display: flex; align-items: center; justify-content: center; gap: 0.4rem; font-size: 0.75rem; color: #166534; border-color: #86efac; background: #f0fdf4; text-decoration: none; padding: 0.4rem 0.65rem; margin-bottom: 0.4rem;">
              <i class="ph-bold ph-file-arrow-down"></i> Download Uploaded Soft Copy
            </a>
          `;
        }
        if (p.drive_link) {
          attHtml += `
            <a href="${escapeHtml(p.drive_link)}" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-sm font-roboto" style="display: flex; align-items: center; justify-content: center; gap: 0.4rem; font-size: 0.75rem; color: #0369a1; border-color: #7dd3fc; background: #f0f9ff; text-decoration: none; padding: 0.4rem 0.65rem;">
              <i class="ph-bold ph-google-drive-logo"></i> Open Google Drive Proposal
            </a>
          `;
        }
        if (!p.attachment_path && !p.drive_link) {
          attHtml = `
            <span style="font-size: 0.72rem; color: #64748b; font-style: italic; display: block; text-align: center; margin-top: 2px;">
              <i class="ph ph-info"></i> No external file or drive link attached. Use button above for complete soft copy.
            </span>
          `;
        }
        attBox.innerHTML = attHtml;
      }

      // Dynamic Action & Tracking Panel:
      // If Pending: Show Accept & Reject (Revise) buttons
      // If Accepted: Only show status update and timeline tracking (no accept/reject buttons)
      // If Revision: Show revision feedback notice
      const actionBox = document.getElementById('proposalActionButtons');
      if (actionBox) {
        if (isPending) {
          actionBox.innerHTML = `
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; margin-top: 0.75rem;">
              <label class="font-roboto" style="display: block; font-size: 0.75rem; font-weight: 700; color: #334155; margin-bottom: 0.5rem; text-transform: uppercase;">
                <i class="ph ph-gavel"></i> National Evaluation Action
              </label>
              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                <button class="btn btn-outline-danger font-roboto" style="display: flex; align-items: center; justify-content: center; gap: 0.35rem; font-size: 0.8rem;" onclick="handleReviewProposal('draft')">
                  <i class="ph ph-x"></i> Reject / Revise
                </button>
                <button class="btn btn-primary font-roboto" style="display: flex; align-items: center; justify-content: center; gap: 0.35rem; font-size: 0.8rem; background: #16a34a; border-color: #15803d;" onclick="handleReviewProposal('approved')">
                  <i class="ph ph-check"></i> Accept & Approve
                </button>
              </div>
            </div>
          `;
        } else if (isApproved) {
          actionBox.innerHTML = `
            <div style="background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; padding: 0.85rem; margin-top: 0.75rem;">
              <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <div style="width: 22px; height: 22px; border-radius: 50%; background: #16a34a; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; flex-shrink: 0;">
                  <i class="ph ph-check"></i>
                </div>
                <strong style="color: #166534; font-size: 0.85rem;">Proposal Accepted & Endorsed</strong>
              </div>
              <p style="font-size: 0.75rem; color: #15803d; margin: 0 0 0.5rem; line-height: 1.4;">
                This proposal is officially accepted. You can monitor progress and status updates below:
              </p>
              <div style="background: #ffffff; border: 1px solid #bbf7d0; border-radius: 6px; padding: 0.5rem 0.65rem; font-size: 0.75rem; display: flex; flex-direction: column; gap: 0.35rem;">
                <div style="display: flex; justify-content: space-between;">
                  <span style="color: #64748b;">Current Status:</span>
                  <strong style="color: #0f172a; text-transform: uppercase;">${p.status}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                  <span style="color: #64748b;">Approval Timer:</span>
                  <strong style="color: #166534;">${p.approval_timer || 'Recently Approved'}</strong>
                </div>
                ${p.review_note ? `<div style="border-top: 1px dashed #e2e8f0; padding-top: 0.35rem; color: #334155;"><strong>Endorsement Note:</strong> ${escapeHtml(p.review_note)}</div>` : ''}
              </div>
              <div style="margin-top: 0.6rem;">
                <a href="Request.php" class="btn btn-outline btn-sm font-roboto" style="display: flex; align-items: center; justify-content: center; gap: 0.35rem; width: 100%; text-decoration: none; font-size: 0.75rem;">
                  <i class="ph ph-image-square"></i> Check Poster Collateral in Requests
                </a>
              </div>
            </div>
          `;
        } else {
          actionBox.innerHTML = `
            <div style="background: #fff7ed; border: 1px solid #fdba74; border-radius: 8px; padding: 0.85rem; margin-top: 0.75rem;">
              <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <i class="ph-fill ph-warning-circle" style="color: #c2410c; font-size: 1.15rem;"></i>
                <strong style="color: #9a3412; font-size: 0.85rem;">Revision Requested</strong>
              </div>
              <p style="font-size: 0.75rem; color: #9a3412; margin: 0 0 0.5rem; line-height: 1.4;">
                Feedback has been returned to the chapter coordinator. Awaiting updated proposal resubmission.
              </p>
              ${p.review_note ? `<div style="background: #ffffff; border: 1px solid #fed7aa; border-radius: 6px; padding: 0.5rem; font-size: 0.75rem; color: #7c2d12;"><strong>Revision Notes:</strong> ${escapeHtml(p.review_note)}</div>` : ''}
            </div>
          `;
        }
      }
    }

    // Filter proposals by status tab
    function filterProposals(status, clickedBtn) {
      currentStatus = status;
      document.querySelectorAll('.tabs-container .tab-btn').forEach(btn => btn.classList.remove('active'));
      clickedBtn.classList.add('active');
      loadProposals();
    }

    // Filter proposals by Chapter dropdown
    function filterByChapter(chapterId) {
      currentChapter = chapterId;
      loadProposals();
    }

    // Live search proposals
    let searchTimeout;
    function searchProposals() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(loadProposals, 300);
    }

    // Review Actions (Approve / Request Revision)
    async function handleReviewProposal(status) {
      if (!activeProposalId) {
        alert('Please select a proposal from the list first.');
        return;
      }

      let note = '';
      if (status === 'draft') {
        note = prompt('Please specify the revision notes / reasons for rejection to the chapter coordinator:');
        if (note === null) return;
      } else if (status === 'approved') {
        const confirmApprove = confirm('Are you sure you want to ACCEPT and APPROVE this event proposal?');
        if (!confirmApprove) return;
        const inputNote = prompt('Optional endorsement note or instructions (press OK to proceed):', 'Approved for poster creation and chapter schedule.');
        if (inputNote !== null) {
          note = inputNote;
        }
      }

      try {
        const res = await YTR_API.proposals.review(activeProposalId, status, note);
        if (res.status === 'success') {
          alert(status === 'approved' ? 'Proposal officially accepted! Poster creation request created and approval timer started.' : 'Revision request sent to chapter coordinator.');
          loadProposalMetrics();
          loadProposals();
        } else {
          alert(res.message || 'Error updating proposal.');
        }
      } catch (err) {
        console.error('Proposal review error:', err);
        alert('Network error communicating with the Python API.');
      }
    }

    // Open Proposal Dossier / Soft Copy Modal
    async function openProposalDossier(proposalId) {
      const pid = proposalId || activeProposalId;
      if (!pid) {
        if (allProposalsData && allProposalsData.length > 0) {
          selectProposalRecord(allProposalsData[0]);
          return openProposalDossier(allProposalsData[0].id);
        }
        alert('Please select a proposal first.');
        return;
      }

      let p = null;
      try {
        const res = await YTR_API.proposals.getById(pid);
        if ((res.status === 'success' || res.ok) && res.data) {
          p = res.data;
        }
      } catch(err) {
        console.warn('API getById failed, using cached proposal details:', err);
      }

      if (!p) {
        p = allProposalsData.find(item => item.id == pid);
      }

      if (!p) {
        alert('Could not load proposal details.');
        return;
      }

      // Populate Dossier fields
      document.getElementById('dossierRefCode').textContent = 'PROP #YTR-2026-' + String(p.id).padStart(4, '0');
      document.getElementById('dossierSubmissionDate').textContent = 'Submitted: ' + (p.created_at ? p.created_at.substring(0, 10) : 'Recent');
      document.getElementById('dossierTitle').textContent = p.title || 'Untitled Event';
      document.getElementById('dossierProgram').textContent = p.ytr_program || 'Lead up (Youth Leadership)';
      document.getElementById('dossierAudience').textContent = p.target_audience || 'Open for All';
      document.getElementById('dossierChapter').textContent = (p.chapter_name || 'All Chapters') + (p.chapter_code ? ` (${p.chapter_code})` : '') + (p.region ? ` • ${p.region}` : '');
      document.getElementById('dossierEventType').textContent = ((p.event_type || 'chapter') + ' Event').toUpperCase();

      const startStr = p.start_datetime ? p.start_datetime.substring(0, 16).replace('T', ' ') : 'TBD';
      const endStr = p.end_datetime ? p.end_datetime.substring(0, 16).replace('T', ' ') : 'TBD';
      document.getElementById('dossierSchedule').textContent = `${startStr} to ${endStr}`;
      document.getElementById('dossierVenue').textContent = p.venue || 'TBD';

      document.getElementById('dossierTurnout').textContent = (p.target_turnout || 100) + ' Attendees Goal';
      document.getElementById('dossierBudget').textContent = 'PHP ' + Number(p.budget_amount || 0).toLocaleString();
      document.getElementById('dossierThreshold').textContent = 'PHP ' + Number(p.budget_threshold || 25000).toLocaleString() + ' Cap';
      document.getElementById('dossierDueDate').textContent = p.due_date || (p.start_datetime ? p.start_datetime.substring(0, 10) : 'TBD');
      document.getElementById('dossierDescription').textContent = p.description || 'No description provided.';

      // Attached file information
      const attInfo = document.getElementById('dossierAttachmentInfo');
      const bannerAttContainer = document.getElementById('dossierAttachmentDownloadBtnContainer');
      let attHtml = '';

      if (p.attachment_path) {
        const fileName = p.attachment_path.split('/').pop() || 'Proposal_Document.pdf';
        attHtml += `
          <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; background: #ffffff; padding: 0.65rem 0.75rem; border-radius: 6px; border: 1px solid #bbf7d0; margin-bottom: ${p.drive_link ? '0.65rem' : '0'};">
            <div>
              <div style="font-weight: 700; color: #166534;"><i class="ph-bold ph-file-check"></i> Official Proposal Soft Copy Attached</div>
              <div style="font-size: 0.75rem; color: #334155; margin-top: 2px;">File: <strong>${escapeHtml(fileName)}</strong> (Verified chapter submission document)</div>
            </div>
            <a href="${p.attachment_path}" download class="btn btn-primary btn-sm font-roboto print-no-print" style="background: #16a34a; border-color: #15803d; text-decoration: none; padding: 0.35rem 0.75rem; font-size: 0.78rem;">
              <i class="ph-bold ph-download-simple"></i> Download File
            </a>
          </div>
        `;
        bannerAttContainer.innerHTML = `
          <a href="${p.attachment_path}" download class="btn btn-outline font-roboto btn-sm print-no-print" style="background: #fff; font-size: 0.78rem; text-decoration: none; color: #16a34a; border-color: #86efac;">
            <i class="ph ph-file-arrow-down"></i> Download Uploaded File
          </a>
        `;
      } else {
        bannerAttContainer.innerHTML = '';
      }

      if (p.drive_link) {
        attHtml += `
          <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; background: #ffffff; padding: 0.65rem 0.75rem; border-radius: 6px; border: 1px solid #bae6fd;">
            <div>
              <div style="font-weight: 700; color: #0369a1;"><i class="ph-bold ph-google-drive-logo"></i> Proposal Cloud / Google Drive Link</div>
              <div style="font-size: 0.72rem; color: #0284c7; word-break: break-all; margin-top: 2px;">
                <a href="${escapeHtml(p.drive_link)}" target="_blank" rel="noopener noreferrer" style="color: #0284c7; text-decoration: underline;">${escapeHtml(p.drive_link)}</a>
              </div>
            </div>
            <a href="${escapeHtml(p.drive_link)}" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-sm font-roboto print-no-print" style="background: #ffffff; color: #0369a1; border-color: #7dd3fc; text-decoration: none; padding: 0.35rem 0.65rem; font-size: 0.75rem;">
              <i class="ph-bold ph-arrow-square-out"></i> Open Link
            </a>
          </div>
        `;
      }

      if (!p.attachment_path && !p.drive_link) {
        attHtml = `
          <div style="color: #64748b; font-style: italic; font-size: 0.8rem;">
            <i class="ph ph-info"></i> No external document or drive link attached. This electronic dossier comprises the complete official proposal record.
          </div>
        `;
      }

      attInfo.innerHTML = attHtml;

      // Endorsements
      document.getElementById('dossierSubmitterName').textContent = p.requester_name || 'Chapter Coordinator';
      document.getElementById('dossierSubmitterTitle').textContent = p.requester_position || 'Chapter Administrator';
      document.getElementById('dossierSubmitterStamp').textContent = 'Verified Chapter Submission';

      const statusBadge = document.getElementById('dossierStatusBadge');
      const isApproved = ['approved', 'published', 'ongoing', 'completed'].includes(p.status);
      const isPending = p.status === 'pending_approval';

      if (isApproved) {
        statusBadge.className = 'badge approved font-roboto';
        statusBadge.style.cssText = 'background: #dcfce7; color: #15803d; border: 1px solid #86efac; font-size: 0.75rem; padding: 3px 8px; font-weight: 800;';
        statusBadge.textContent = 'OFFICIALLY APPROVED';
        document.getElementById('dossierReviewerName').textContent = p.reviewer_name || 'Hamilton Jones Dela';
        document.getElementById('dossierReviewerTitle').textContent = p.reviewer_position || 'Chief Executive Operation';
        document.getElementById('dossierReviewNotes').textContent = p.review_note ? `Note: "${p.review_note}"` : 'Cleared for Chapter Execution';
      } else if (isPending) {
        statusBadge.className = 'badge pending font-roboto';
        statusBadge.style.cssText = 'background: #fef9c3; color: #854d0e; border: 1px solid #fde047; font-size: 0.75rem; padding: 3px 8px; font-weight: 800;';
        statusBadge.textContent = 'PENDING NATIONAL REVIEW';
        document.getElementById('dossierReviewerName').textContent = 'Pending Review Clearance';
        document.getElementById('dossierReviewerTitle').textContent = 'National Secretariat Review Board';
        document.getElementById('dossierReviewNotes').textContent = 'Official review pending by National Secretariat';
      } else {
        statusBadge.className = 'badge rose font-roboto';
        statusBadge.style.cssText = 'background: #ffe4e6; color: #9f1239; border: 1px solid #fecdd3; font-size: 0.75rem; padding: 3px 8px; font-weight: 800;';
        statusBadge.textContent = 'REVISION REQUESTED';
        document.getElementById('dossierReviewerName').textContent = p.reviewer_name || 'National Secretariat';
        document.getElementById('dossierReviewerTitle').textContent = p.reviewer_position || 'Executive Reviewer';
        document.getElementById('dossierReviewNotes').textContent = p.review_note ? `Revision Note: "${p.review_note}"` : 'Action requested prior to endorsement';
      }

      const modal = document.getElementById('proposalDossierModal');
      modal.classList.add('open');
      modal.style.display = 'flex';
    }

    function closeProposalDossierModal() {
      const modal = document.getElementById('proposalDossierModal');
      modal.classList.remove('open');
      modal.style.display = 'none';
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

    // Initialize on DOM load or immediate if DOM already ready
    function initProposalPage() {
      loadProposalMetrics();
      loadChapters();
      loadProposals();
    }

    if (document.readyState === 'loading') {
      window.addEventListener('DOMContentLoaded', initProposalPage);
    } else {
      initProposalPage();
    }
  </script>
</body>
</html>
