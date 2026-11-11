<?php include 'includes/auth_guard.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chapter Event Proposals | YTR Portal</title>
    <link rel="stylesheet" href="assets/Chapter.css?v=<?= time() ?>">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="assets/api-client.js"></script>
    <style>
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
      .card,
      #proposal-modal,
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

    /* Table Simplification & Strict Pixel-Perfect Alignment */
    .table-responsive {
      overflow-x: auto !important;
      -webkit-overflow-scrolling: touch !important;
      width: 100% !important;
    }
    #chapterProposalsTable {
      width: 100% !important;
      border-collapse: collapse !important;
      table-layout: fixed !important;
      min-width: 860px !important;
    }
    #chapterProposalsTable th,
    #chapterProposalsTable td {
      white-space: nowrap !important;
      vertical-align: middle !important;
      padding: 0.9rem 1rem !important;
      box-sizing: border-box !important;
    }
    #chapterProposalsTable th:nth-child(1), #chapterProposalsTable td:nth-child(1) { width: 45%; text-align: left; }
    #chapterProposalsTable th:nth-child(2), #chapterProposalsTable td:nth-child(2) { width: 12%; text-align: center; }
    #chapterProposalsTable th:nth-child(3), #chapterProposalsTable td:nth-child(3) { width: 11%; text-align: center; }
    #chapterProposalsTable th:nth-child(4), #chapterProposalsTable td:nth-child(4) { width: 15%; text-align: center; }
    #chapterProposalsTable th:nth-child(5), #chapterProposalsTable td:nth-child(5) { width: 17%; text-align: right; }

    /* Uniform Status Badges - Identical width, height & alignment */
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
    </style>
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar Include for Chapter -->
        <?php include 'includes/sidebar_chapter.php'; ?>

        <div class="main-wrapper">
            <!-- Topbar Include -->
            <?php include 'includes/topbar.php'; ?>

            <div class="page-content">
                
                <div class="page-header flex-between" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.4rem;">
                            <h1 class="font-roboto" style="margin: 0; color: var(--text-main, #0f172a); font-weight: 800;">Chapter Event Proposals</h1>
                            <span class="portal-indicator chapter"><i class="ph-fill ph-buildings"></i> Chapter Branch Portal</span>
                        </div>
                        <p style="color: var(--text-muted); margin: 0;">Author, submit, and track the evaluation status of event proposals submitted to the National Board.</p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.65rem;">
                        <button class="btn btn-primary" onclick="openProposalModal()">
                            <i class="ph ph-plus-circle"></i> Submit Event Proposal
                        </button>
                    </div>
                </div>

                <!-- 1. Metric Cards Row -->
                <div class="metrics-grid">
                    <div class="metric-card">
                        <div class="metric-icon blue"><i class="ph ph-files"></i></div>
                        <div>
                            <span class="metric-title font-roboto">Total Chapter Proposals</span>
                            <div class="metric-value font-roboto" id="metric-total">0</div>
                            <p class="metric-sub">All event applications authored by your chapter</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon orange"><i class="ph ph-hourglass-high"></i></div>
                        <div>
                            <span class="metric-title font-roboto">Pending National Review</span>
                            <div class="metric-value font-roboto" id="metric-pending">0</div>
                            <p class="metric-sub">Submissions currently awaiting national decision</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon green"><i class="ph ph-check-circle"></i></div>
                        <div>
                            <span class="metric-title font-roboto">Approved Events</span>
                            <div class="metric-value font-roboto" id="metric-approved">0</div>
                            <p class="metric-sub">Cleared for poster requests & execution</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon rose"><i class="ph ph-arrow-counter-clockwise"></i></div>
                        <div>
                            <span class="metric-title font-roboto">Needs Revision</span>
                            <div class="metric-value font-roboto" id="metric-revision">0</div>
                            <p class="metric-sub">Proposals returned with administrative notes</p>
                        </div>
                    </div>
                </div>

                <!-- 2. Tabs and Filters -->
                <div class="tab-controls-wrapper">
                    <div class="tabs-container font-roboto">
                        <button class="tab-btn active" id="tab-all" onclick="filterByStatus('all', this)"><i class="ph ph-list"></i> All Proposals</button>
                        <button class="tab-btn" id="tab-pending" onclick="filterByStatus('pending_approval', this)"><i class="ph ph-clock"></i> In Review</button>
                        <button class="tab-btn" id="tab-approved" onclick="filterByStatus('approved', this)"><i class="ph ph-check-circle"></i> Approved</button>
                        <button class="tab-btn" id="tab-revision" onclick="filterByStatus('draft', this)"><i class="ph ph-warning-circle"></i> Needs Revision</button>
                    </div>
                    <div class="filters" style="flex: 1; max-width: 480px; min-width: 280px;">
                        <div class="search-bar" style="margin: 0; width: 100%;">
                            <i class="ph ph-magnifying-glass"></i>
                            <input type="text" id="proposal-search" placeholder="Search event title..." oninput="handleSearch()" style="width: 100%; flex: 1; min-width: 0;">
                        </div>
                    </div>
                </div>

                <!-- 3. Proposals Table Section -->
                <div class="table-section">
                    <div class="table-responsive">
                        <table id="chapterProposalsTable">
                            <thead class="font-roboto">
                                <tr>
                                    <th style="width: 45%; text-align: left;">Event Title</th>
                                    <th style="width: 12%; text-align: center;">Schedule</th>
                                    <th style="width: 11%; text-align: center;">Budget</th>
                                    <th style="width: 15%; text-align: center;">Status</th>
                                    <th style="width: 17%; text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="proposals-tbody">
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                        <i class="ph ph-spinner ph-spin" style="font-size: 1.5rem;"></i> Loading chapter proposals...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL: SUBMIT CHAPTER EVENT PROPOSAL -->
    <div id="proposal-modal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 12px; max-width: 650px; width: 90%; max-height: 90vh; overflow-y: auto; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; margin-bottom: 1.25rem;">
                <h3 class="font-roboto" style="margin: 0; color: #0f172a; font-size: 1.25rem; font-weight: 700;">
                    <i class="ph ph-file-plus" style="color: var(--primary);"></i> Submit Chapter Event Proposal
                </h3>
                <button type="button" onclick="closeProposalModal()" style="background: none; border: none; font-size: 1.4rem; cursor: pointer; color: #64748b;">
                    <i class="ph ph-x"></i>
                </button>
            </div>

            <form id="chapter-proposal-form" onsubmit="submitChapterProposal(event)">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Event Title *</label>
                    <input type="text" id="p-title" required placeholder="e.g. Metro Youth Leadership Summit 2026" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
                        <i class="ph-bold ph-compass" style="color: #2563eb;"></i> YTR Program Pillar *
                    </label>
                    <select id="p-program" required style="width: 100%; padding: 0.6rem; border: 1.5px solid #3b82f6; border-radius: 6px; font-size: 0.9rem; background: #eff6ff; font-weight: 600; color: #1e40af;">
                        <option value="Lead up (Youth Leadership)">Lead up (Youth Leadership)</option>
                        <option value="Rise up (Youth Excellence)">Rise up (Youth Excellence)</option>
                        <option value="Build up (Youth Personality Development)">Build up (Youth Personality Development)</option>
                        <option value="Gear up (Youth preparation and empowerment)">Gear up (Youth preparation and empowerment)</option>
                    </select>
                    <small style="color: #64748b; font-size: 0.75rem; margin-top: 4px; display: block;">Designate which of the 4 core YTR initiative frameworks this event fulfills.</small>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
                        <i class="ph-bold ph-users-three" style="color: #2563eb;"></i> Target Audience / Demographic *
                    </label>
                    <select id="p-target-audience" required style="width: 100%; padding: 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; background: #fff; font-weight: 600; color: #1e293b;" onchange="handleProposalAudienceChange(this.value)">
                        <option value="Open for All (General Youth)">Open for All (General Youth)</option>
                        <option value="BSIT Students">BSIT Students</option>
                        <option value="College / University Students">College / University Students</option>
                        <option value="Senior High School (SHS) Students">Senior High School (SHS) Students</option>
                        <option value="Junior High School (JHS) Students">Junior High School (JHS) Students</option>
                        <option value="Youth Leaders &amp; Campus Officers">Youth Leaders &amp; Campus Officers</option>
                        <option value="Other">Other (Specify)</option>
                    </select>
                    <div id="p-audience-custom-wrap" style="display: none; margin-top: 0.45rem;">
                        <input type="text" id="p-target-audience-custom" placeholder="Specify target demographic (e.g. BSIT 3rd Year, Engineering Dept, etc.)..." style="width: 100%; padding: 0.55rem; border: 1.5px solid #3b82f6; border-radius: 6px; font-size: 0.85rem; background: #eff6ff;">
                    </div>
                    <small style="color: #64748b; font-size: 0.75rem; margin-top: 4px; display: block;">Specify if this event is tailored for BSIT students, campus youth, or open for all.</small>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Event Type *</label>
                        <select id="p-type" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                            <option value="youth_camp">Youth Camp</option>
                            <option value="fellowship">Fellowship & Worship</option>
                            <option value="leadership_training">Leadership Training</option>
                            <option value="outreach">Community Outreach</option>
                            <option value="sports_fest">Youth Sportsfest</option>
                            <option value="anniversary">Chapter Anniversary</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Venue / Location *</label>
                        <input type="text" id="p-venue" required placeholder="e.g. LSPU Los Baños Campus" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Start Date & Time *</label>
                        <input type="datetime-local" id="p-start" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">End Date & Time *</label>
                        <input type="datetime-local" id="p-end" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
                            <i class="ph ph-users"></i> Target Turnout (Attendees) *
                        </label>
                        <input type="number" id="p-turnout" required min="10" placeholder="e.g. 150" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                        <small style="color: #64748b; font-size: 0.75rem;">Minimum attendance goal to satisfy performance threshold.</small>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
                            <i class="ph ph-currency-circle-dollar"></i> Proposed Budget (PHP) *
                        </label>
                        <input type="number" id="p-budget" required min="0" step="100" placeholder="e.g. 25000" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                        <small style="color: #64748b; font-size: 0.75rem;">Standard chapter ceiling is PHP 25,000.</small>
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Target Due Date for Post-Event Report *</label>
                    <input type="date" id="p-due" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Event Concept, Objectives &amp; Program *</label>
                    <textarea id="p-desc" rows="3" required placeholder="Describe the purpose, theme, speaker, and spiritual or community objectives of the event..." style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;"></textarea>
                </div>

                <div style="margin-bottom: 1rem; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
                        <i class="ph ph-file-arrow-up" style="color: #2563eb;"></i> Upload Proposal Document / Soft Copy (Optional)
                    </label>
                    <input type="file" id="p-attachment" accept=".pdf,.doc,.docx,.pptx,.xlsx,.png,.jpg,.jpeg" style="width: 100%; font-size: 0.85rem; color: #475569;">
                    <small style="color: #64748b; font-size: 0.72rem; display: block; margin-top: 0.25rem;">
                        Attach an official proposal dossier, program flow, presentation, or budget breakdown (PDF, DOCX, etc.). The National Secretariat will be able to download this soft copy directly.
                    </small>
                </div>

                <!-- Google Drive / Cloud Soft Copy Link -->
                <div style="margin-bottom: 1.5rem; background: #f0f9ff; border: 1.5px solid #bae6fd; border-radius: 8px; padding: 1rem;">
                    <label style="display: flex; align-items: center; gap: 0.45rem; font-size: 0.85rem; font-weight: 700; color: #0369a1; margin-bottom: 0.35rem;">
                        <i class="ph-bold ph-google-drive-logo" style="font-size: 1.2rem; color: #0284c7;"></i>
                        <span>Google Drive / Cloud Proposal Link (Optional)</span>
                    </label>
                    <div style="position: relative;">
                        <input type="url" id="p-drive-link" placeholder="https://drive.google.com/drive/folders/... (Google Drive, Docs, Slides, or Sheets)" style="width: 100%; padding: 0.6rem 0.75rem 0.6rem 2.4rem; border: 1px solid #7dd3fc; border-radius: 6px; font-size: 0.875rem; background: #ffffff; color: #0f172a;">
                        <div style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #0284c7; pointer-events: none;">
                            <i class="ph-bold ph-link"></i>
                        </div>
                    </div>
                    <small style="color: #0369a1; font-size: 0.75rem; margin-top: 0.35rem; display: block;">
                        Provide a shared Google Drive folder, Google Doc proposal, or presentation deck link. Accessible to the National Secretariat for evaluation.
                    </small>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn btn-outline" onclick="closeProposalModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="p-submit-btn">
                        <i class="ph ph-paper-plane-tilt"></i> Submit Proposal to National
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- PROPOSAL DOSSIER & SOFT COPY MODAL FOR CHAPTER -->
    <div id="proposalDossierModal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 1050; align-items: center; justify-content: center; padding: 1.5rem;">
        <div style="background: #ffffff; border-radius: 12px; max-width: 820px; width: 100%; max-height: 90vh; overflow-y: auto; padding: 1.75rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
            <div class="print-no-print modal-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; margin-bottom: 1.25rem;">
                <h3 class="font-roboto" style="margin: 0; color: #0f172a; font-size: 1.15rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="ph-bold ph-file-text" style="color: #2563eb;"></i> Official Event Proposal Dossier (Soft Copy)
                </h3>
                <button type="button" onclick="closeProposalDossierModal()" style="background: none; border: none; font-size: 1.3rem; color: #64748b; cursor: pointer;"><i class="ph ph-x"></i></button>
            </div>

            <!-- Toolbar banner -->
            <div class="print-no-print print-status-banner" style="display: flex; align-items: center; justify-content: space-between; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 0.65rem 1rem; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.65rem;">
                    <div style="width: 32px; height: 32px; border-radius: 6px; background: #dbeafe; color: #1d4ed8; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0;">
                        <i class="ph-bold ph-download-simple"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.85rem; font-weight: 700; color: #1e40af;">Official Proposal Soft Copy Ready for Download</div>
                        <div style="font-size: 0.72rem; color: #475569;">Save this complete dossier as PDF via browser print, or download your uploaded attachment.</div>
                    </div>
                </div>
                <div id="dossierAttachmentDownloadBtnContainer"></div>
            </div>

            <!-- Printable Document Sheet -->
            <div id="printableProposalDossier" class="print-document-sheet" style="background: #ffffff; padding: 1.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: 'Roboto', sans-serif;">
                
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
                <div class="print-signatures-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 2px solid #1a2f63;">
                    <!-- Prepared By -->
                    <div style="display: flex; flex-direction: column; justify-content: flex-end; min-height: 95px; text-align: left;">
                        <div style="font-size: 0.65rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px;">
                            Prepared &amp; Submitted by:
                        </div>
                        <div style="height: 38px; display: flex; align-items: flex-end; margin-bottom: 4px;" id="dossierSubmitterSigContainer">
                            <span style="font-size: 0.72rem; color: #16a34a; font-weight: 700;"><i class="ph ph-check-circle"></i> Digital Chapter Endorsement</span>
                        </div>
                        <div style="border-top: 1.5px solid #0f172a; width: 100%; margin-bottom: 4px;"></div>
                        <p id="dossierSubmitterName" style="font-size: 0.825rem; font-weight: 800; margin: 0; color: #0f172a;">Chapter Coordinator</p>
                        <p id="dossierSubmitterTitle" style="font-size: 0.72rem; color: #475569; margin: 1px 0 0;">Chapter Administrator</p>
                        <p id="dossierSubmitterStamp" style="font-size: 0.65rem; color: #64748b; margin: 1px 0 0;">Official Chapter Submission</p>
                    </div>

                    <!-- Evaluated By -->
                    <div style="display: flex; flex-direction: column; justify-content: flex-end; min-height: 95px; text-align: left;">
                        <div style="font-size: 0.65rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px;">
                            National Secretariat Evaluation:
                        </div>
                        <div style="height: 38px; display: flex; align-items: flex-end; margin-bottom: 4px;">
                            <span id="dossierStatusBadge" class="badge approved font-roboto" style="font-size: 0.75rem; padding: 3px 8px;">
                                APPROVED
                            </span>
                        </div>
                        <div style="border-top: 1.5px solid #0f172a; width: 100%; margin-bottom: 4px;"></div>
                        <p id="dossierReviewerName" style="font-size: 0.825rem; font-weight: 800; margin: 0; color: #0f172a;">Hanskee Joyce Jose</p>
                        <p id="dossierReviewerTitle" style="font-size: 0.72rem; color: #1e3a8a; font-weight: 700; margin: 1px 0 0;">Chief Executive Officer</p>
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

    <script>
        let currentUser = null;
        try {
            const raw = localStorage.getItem('ytr_user');
            if (raw) currentUser = JSON.parse(raw);
        } catch(e) {}
        let rawChId = (currentUser && currentUser.chapter_id) ? currentUser.chapter_id : (localStorage.getItem('ytr_chapter_id') || '1');
        let chapterId = parseInt(rawChId, 10);
        if (isNaN(chapterId) || chapterId <= 0) {
            chapterId = 1;
        }
        let proposalsData = [];
        let currentStatusFilter = 'all';

        document.addEventListener('DOMContentLoaded', () => {
            loadChapterProposals();
        });

        async function loadChapterProposals() {
            try {
                // Fetch proposals specifically for this chapter
                const res = await YTR_API.proposals.list('all', chapterId);
                if ((res.status === 'success' || res.ok) && Array.isArray(res.data)) {
                    proposalsData = res.data;
                    updateMetrics(proposalsData);
                    renderProposalsTable();
                } else {
                    proposalsData = [];
                    updateMetrics([]);
                    renderProposalsTable();
                }
            } catch (err) {
                console.error('Failed to load chapter proposals:', err);
                proposalsData = [];
                updateMetrics([]);
                renderProposalsTable();
            }
        }

        function updateMetrics(items) {
            document.getElementById('metric-total').textContent = items.length;
            document.getElementById('metric-pending').textContent = items.filter(p => p.status === 'pending_approval').length;
            document.getElementById('metric-approved').textContent = items.filter(p => p.status === 'approved').length;
            document.getElementById('metric-revision').textContent = items.filter(p => p.status === 'draft' || p.status === 'rejected').length;
        }

        function filterByStatus(status, btn) {
            currentStatusFilter = status;
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');
            renderProposalsTable();
        }

        function handleSearch() {
            renderProposalsTable();
        }

        function renderProposalsTable() {
            const tbody = document.getElementById('proposals-tbody');
            const search = (document.getElementById('proposal-search').value || '').toLowerCase().trim();

            let filtered = proposalsData.filter(p => {
                if (currentStatusFilter !== 'all' && p.status !== currentStatusFilter) return false;
                if (search && !((p.title || '').toLowerCase().includes(search) || (p.venue || '').toLowerCase().includes(search))) {
                    return false;
                }
                return true;
            });

            if (filtered.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2.5rem; color: #64748b;">
                            <i class="ph ph-file-dashed" style="font-size: 2rem; color: #94a3b8;"></i>
                            <p style="margin-top: 0.5rem;">No proposals found under this filter. Click <strong>Submit Event Proposal</strong> above to propose an event.</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = filtered.map(p => {
                let statusBadge = '';
                if (p.status === 'approved') {
                    statusBadge = `<span class="status-pill-fixed approved font-roboto"><i class="ph-bold ph-check"></i> Approved</span>`;
                } else if (p.status === 'pending_approval') {
                    statusBadge = `<span class="status-pill-fixed pending font-roboto"><i class="ph-bold ph-clock"></i> In Review</span>`;
                } else if (p.status === 'draft') {
                    statusBadge = `<span class="status-pill-fixed revision font-roboto"><i class="ph-bold ph-arrow-counter-clockwise"></i> Revision Needed</span>`;
                } else {
                    statusBadge = `<span class="status-pill-fixed neutral font-roboto">${escapeHtml(p.status)}</span>`;
                }

                const startDate = p.start_datetime ? p.start_datetime.substring(0, 10) : 'Date TBD';
                const budgetFormatted = (p.budget_amount || 0).toLocaleString();

                let primaryAction = '';
                if (p.status === 'approved') {
                    primaryAction = `
                        <a href="ChapterReport.php?event_id=${p.id}" class="action-btn-uniform font-roboto" style="background: #16a34a; border: 1px solid #15803d; color: #ffffff;" title="Submit Post-Event Report">
                            <i class="ph ph-file-arrow-up"></i> Report
                        </a>
                    `;
                } else if (p.status === 'draft') {
                    primaryAction = `
                        <button type="button" class="action-btn-uniform font-roboto" onclick="openProposalDossier(${p.id})" style="background: #e11d48; border: 1px solid #e11d48; color: #ffffff;" title="Revise proposal">
                            <i class="ph ph-pencil-simple"></i> Revise
                        </button>
                    `;
                }

                return `
                    <tr>
                        <td data-label="Event Title" style="text-align: left; vertical-align: middle;">
                            <span class="font-roboto" style="font-weight: 700; color: #0f172a; font-size: 0.92rem; display: block; width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${escapeHtml(p.title)}">
                                ${escapeHtml(p.title)}
                            </span>
                        </td>
                        <td data-label="Schedule" style="white-space: nowrap; font-size: 0.85rem; color: #1e293b; font-weight: 500; text-align: center; vertical-align: middle;">
                            ${escapeHtml(startDate)}
                        </td>
                        <td data-label="Budget" style="white-space: nowrap; font-size: 0.85rem; font-weight: 700; color: #0f172a; text-align: center; vertical-align: middle;">
                            PHP ${budgetFormatted}
                        </td>
                        <td data-label="Status" style="white-space: nowrap; text-align: center; vertical-align: middle;">
                            ${statusBadge}
                        </td>
                        <td data-label="Action" style="text-align: right; white-space: nowrap; vertical-align: middle;">
                            <div style="display: inline-flex; align-items: center; gap: 0.45rem; justify-content: flex-end; white-space: nowrap;">
                                <button type="button" class="action-btn-uniform btn-details font-roboto" onclick="openProposalDossier(${p.id})" title="View proposal details">
                                    <i class="ph ph-info"></i> Details
                                </button>
                                ${primaryAction}
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function handleProposalAudienceChange(val) {
            const wrap = document.getElementById('p-audience-custom-wrap');
            const customInput = document.getElementById('p-target-audience-custom');
            if (val === 'Other') {
                wrap.style.display = 'block';
                customInput.required = true;
                customInput.focus();
            } else {
                wrap.style.display = 'none';
                customInput.required = false;
                customInput.value = '';
            }
        }

        function openProposalModal() {
            document.getElementById('proposal-modal').style.display = 'flex';
        }

        function closeProposalModal() {
            document.getElementById('proposal-modal').style.display = 'none';
        }

        async function submitChapterProposal(e) {
            e.preventDefault();
            const btn = document.getElementById('p-submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Submitting...';

            let audienceVal = document.getElementById('p-target-audience').value;
            if (audienceVal === 'Other') {
                audienceVal = document.getElementById('p-target-audience-custom').value.trim() || 'Other';
            }

            const turnoutVal = parseInt(document.getElementById('p-turnout').value, 10);
            const budgetVal = parseFloat(document.getElementById('p-budget').value);

            const formData = new FormData();
            formData.append('chapter_id', isNaN(chapterId) ? 1 : chapterId);
            if (currentUser && currentUser.id) {
                formData.append('created_by', currentUser.id);
            }
            formData.append('title', document.getElementById('p-title').value.trim());
            formData.append('ytr_program', document.getElementById('p-program').value);
            formData.append('target_audience', audienceVal);
            formData.append('event_type', document.getElementById('p-type').value);
            formData.append('venue', document.getElementById('p-venue').value.trim());
            formData.append('start_datetime', document.getElementById('p-start').value);
            formData.append('end_datetime', document.getElementById('p-end').value);
            formData.append('target_turnout', isNaN(turnoutVal) ? 100 : turnoutVal);
            formData.append('budget_amount', isNaN(budgetVal) ? 0 : budgetVal);
            formData.append('due_date', document.getElementById('p-due').value);
            formData.append('description', document.getElementById('p-desc').value.trim());

            const fileInput = document.getElementById('p-attachment');
            if (fileInput && fileInput.files && fileInput.files[0]) {
                formData.append('attachment', fileInput.files[0]);
            }

            const driveLinkVal = (document.getElementById('p-drive-link')?.value || '').trim();
            if (driveLinkVal) {
                formData.append('drive_link', driveLinkVal);
            }

            try {
                const res = await YTR_API.proposals.create(formData);
                if (res.ok) {
                    alert('Proposal submitted successfully! It is now pending National Board evaluation.');
                    document.getElementById('chapter-proposal-form').reset();
                    document.getElementById('p-audience-custom-wrap').style.display = 'none';
                    closeProposalModal();
                    loadChapterProposals();
                } else {
                    alert('Submission error: ' + (res.message || 'Failed to submit proposal.'));
                }
            } catch (err) {
                console.error('Error submitting proposal:', err);
                alert('Network error connecting to Python API.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="ph ph-paper-plane-tilt"></i> Submit Proposal to National';
            }
        }

        async function openProposalDossier(proposalId) {
            let p = null;
            try {
                const res = await YTR_API.proposals.getById(proposalId);
                if ((res.status === 'success' || res.ok) && res.data) {
                    p = res.data;
                }
            } catch(err) {
                console.warn('API getById failed, using cached proposal data:', err);
            }

            if (!p) {
                p = proposalsData.find(item => item.id == proposalId);
            }

            if (!p) {
                alert('Could not load proposal dossier.');
                return;
            }

            document.getElementById('dossierRefCode').textContent = 'PROP #YTR-2026-' + String(p.id).padStart(4, '0');
            document.getElementById('dossierSubmissionDate').textContent = 'Submitted: ' + (p.created_at ? p.created_at.substring(0, 10) : 'Recent');
            document.getElementById('dossierTitle').textContent = p.title || 'Untitled Event';
            document.getElementById('dossierProgram').textContent = p.ytr_program || 'Lead up (Youth Leadership)';
            document.getElementById('dossierAudience').textContent = p.target_audience || 'Open for All';
            document.getElementById('dossierChapter').textContent = (p.chapter_name || 'Chapter') + (p.chapter_code ? ` (${p.chapter_code})` : '');
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

            const submitterName = p.signed_by_name
              || p.report_submitter_name
              || p.effective_submitter_name
              || p.requester_name
              || (currentUser && currentUser.full_name ? currentUser.full_name : 'Chapter Coordinator');

            const submitterTitle = p.report_submitter_position
              || p.effective_submitter_position
              || p.requester_position
              || (currentUser && currentUser.position ? currentUser.position : 'Chapter Coordinator');

            const submitterSig = p.report_e_signature
              || p.effective_submitter_signature
              || (currentUser && currentUser.signature_image ? currentUser.signature_image : null);

            document.getElementById('dossierSubmitterName').textContent = submitterName;
            document.getElementById('dossierSubmitterTitle').textContent = submitterTitle;
            document.getElementById('dossierSubmitterStamp').textContent = p.report_id ? 'Official Post-Event Report Filing' : 'Verified Chapter Submission';

            const submitterSigContainer = document.getElementById('dossierSubmitterSigContainer');
            if (submitterSigContainer) {
              if (submitterSig && submitterSig.length > 5) {
                submitterSigContainer.innerHTML = `<img src="${submitterSig}" alt="Digital Chapter Signature" style="max-height: 38px; max-width: 140px; object-fit: contain;">`;
              } else {
                submitterSigContainer.innerHTML = `<span style="font-size: 0.72rem; color: #16a34a; font-weight: 700;"><i class="ph ph-check-circle"></i> Digital Chapter Endorsement</span>`;
              }
            }

            const statusBadge = document.getElementById('dossierStatusBadge');
            const isApproved = ['approved', 'published', 'ongoing', 'completed'].includes(p.status);
            const isPending = p.status === 'pending_approval';

            if (isApproved) {
                statusBadge.className = 'badge approved font-roboto';
                statusBadge.style.cssText = 'background: #dcfce7; color: #15803d; border: 1px solid #86efac; font-size: 0.75rem; padding: 3px 8px; font-weight: 800;';
                statusBadge.textContent = 'OFFICIALLY APPROVED';
                document.getElementById('dossierReviewerName').textContent = p.reviewer_name || 'Hanskee Joyce Jose';
                document.getElementById('dossierReviewerTitle').textContent = (p.reviewer_position === 'Chief Executive Operation' ? 'Chief Executive Officer' : (p.reviewer_position || 'Chief Executive Officer'));
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
    </script>
</body>
</html>
