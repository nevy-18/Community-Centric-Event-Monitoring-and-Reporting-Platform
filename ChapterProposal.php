<?php include 'includes/auth_guard.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chapter Event Proposals | YTR Portal</title>
    <link rel="stylesheet" href="assets/Chapter.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
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

      /* Unhide the proposal dossier modal container and render it as normal document flow */
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
      #proposalDossierModal > div > div:first-child,
      #proposalDossierModal .print-action-bar,
      #proposalDossierModal .print-status-banner,
      #proposalDossierModal button {
        display: none !important;
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
        color: #000000 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
      #printableProposalDossier table {
        width: 100% !important;
        border-collapse: collapse !important;
        page-break-inside: auto;
      }
      #printableProposalDossier tr {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
      }
      #printableProposalDossier th,
      #printableProposalDossier td {
        border: 1px solid #94a3b8 !important;
        color: #000000 !important;
        padding: 6px 10px !important;
      }
      #printableProposalDossier th {
        background: #1a2f63 !important;
        color: #ffffff !important;
      }
      #dossierTitle,
      #dossierProgram,
      #dossierAudience,
      #dossierChapter,
      #dossierEventType,
      #dossierSchedule,
      #dossierVenue,
      #dossierTurnout,
      #dossierBudget,
      #dossierThreshold,
      #dossierDueDate,
      #dossierDescription {
        color: #000000 !important;
        visibility: visible !important;
        font-weight: 700 !important;
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
      #dossierSubmitterName,
      #dossierSubmitterTitle,
      #dossierSubmitterStamp,
      #dossierReviewerName,
      #dossierReviewerTitle,
      #dossierReviewNotes {
        display: block !important;
        visibility: visible !important;
        color: #000000 !important;
      }
      #dossierSubmitterName,
      #dossierReviewerName {
        font-size: 0.95rem !important;
        font-weight: 900 !important;
      }
      #dossierSubmitterTitle,
      #dossierReviewerTitle {
        font-size: 0.8rem !important;
        font-weight: 700 !important;
      }
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
                            <h1 class="font-roboto" style="margin: 0; color: #0f172a; font-weight: 800;">Chapter Event Proposals</h1>
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
                    <div class="filters">
                        <div class="search-bar" style="margin: 0; min-width: 240px;">
                            <i class="ph ph-magnifying-glass"></i>
                            <input type="text" id="proposal-search" placeholder="Search event title..." oninput="handleSearch()">
                        </div>
                    </div>
                </div>

                <!-- 3. Proposals Table Section -->
                <div class="table-section">
                    <div class="table-responsive">
                        <table>
                            <thead class="font-roboto">
                                <tr>
                                    <th>Event Details</th>
                                    <th>Schedule & Location</th>
                                    <th>Turnout Threshold</th>
                                    <th>Budget Allocation</th>
                                    <th>Status & Review Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="proposals-tbody">
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
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
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; margin-bottom: 1.25rem;">
                <h3 class="font-roboto" style="margin: 0; color: #0f172a; font-size: 1.15rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="ph-bold ph-file-text" style="color: #2563eb;"></i> Official Event Proposal Dossier (Soft Copy)
                </h3>
                <button type="button" onclick="closeProposalDossierModal()" style="background: none; border: none; font-size: 1.3rem; color: #64748b; cursor: pointer;"><i class="ph ph-x"></i></button>
            </div>

            <!-- Toolbar banner -->
            <div style="display: flex; align-items: center; justify-content: space-between; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 0.65rem 1rem; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
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
                <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px double #1a2f63; padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <h2 style="font-size: 1.35rem; font-weight: 900; color: #1a2f63; margin: 0; text-transform: uppercase; letter-spacing: 0.04em;">YOUTH ON THE ROCK NATIONAL MOVEMENT</h2>
                        <p style="font-size: 0.8rem; color: #475569; margin: 3px 0 0; text-transform: uppercase; font-weight: 700;">Community-Centric Event Monitoring &amp; Reporting Platform</p>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 1px 0 0;">Official Chapter Event Proposal Dossier &amp; Executive Clearance Record</p>
                    </div>
                    <div style="text-align: right;">
                        <span id="dossierRefCode" style="display: inline-block; padding: 4px 10px; background: #eff6ff; color: #1e3a8a; font-weight: 800; font-size: 0.78rem; border-radius: 4px; border: 1px solid #bfdbfe;">
                            PROP #YTR-2026-0001
                        </span>
                        <p id="dossierSubmissionDate" style="font-size: 0.7rem; color: #64748b; margin-top: 4px;">Submitted: Oct 14, 2026</p>
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
                        <div style="height: 38px; display: flex; align-items: flex-end; margin-bottom: 4px;">
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
                        <p id="dossierReviewerName" style="font-size: 0.825rem; font-weight: 800; margin: 0; color: #0f172a;">Hamilton Jones Dela</p>
                        <p id="dossierReviewerTitle" style="font-size: 0.72rem; color: #1e3a8a; font-weight: 700; margin: 1px 0 0;">Chief Executive Operation</p>
                        <p id="dossierReviewNotes" style="font-size: 0.65rem; color: #64748b; margin: 1px 0 0;">Cleared for Chapter Execution</p>
                    </div>
                </div>

            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.25rem;">
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
                        <td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748b;">
                            <i class="ph ph-file-dashed" style="font-size: 2rem; color: #94a3b8;"></i>
                            <p style="margin-top: 0.5rem;">No proposals found under this filter. Click <strong>Submit Event Proposal</strong> above to propose an event.</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = filtered.map(p => {
                let statusBadge = '';
                let timerHtml = '';
                if (p.approval_timer) {
                    const timerText = typeof p.approval_timer === 'object' ? (p.approval_timer.label || p.approval_timer.text) : p.approval_timer;
                    const timerCls = (typeof p.approval_timer === 'object' && p.approval_timer.cls) ? p.approval_timer.cls : 'badge timer';
                    timerHtml = `<div style="margin-top: 4px;"><span class="${timerCls}"><i class="ph ph-timer"></i> ${escapeHtml(timerText)}</span></div>`;
                }

                let dueHtml = '';
                if (p.due_badge) {
                    const dueText = typeof p.due_badge === 'object' ? (p.due_badge.label || p.due_badge.text) : p.due_badge;
                    const dueCls = (typeof p.due_badge === 'object' && p.due_badge.cls) ? p.due_badge.cls : 'badge timer';
                    dueHtml = `<span class="${dueCls}" style="margin-left: 4px;"><i class="ph ph-clock"></i> ${escapeHtml(dueText)}</span>`;
                }

                if (p.status === 'approved') {
                    statusBadge = `<span class="badge approved"><i class="ph ph-check"></i> Approved</span>`;
                } else if (p.status === 'pending_approval') {
                    statusBadge = `<span class="badge pending"><i class="ph ph-clock"></i> In Review</span>`;
                } else if (p.status === 'draft') {
                    statusBadge = `<span class="badge draft"><i class="ph ph-arrow-counter-clockwise"></i> Revision Needed</span>`;
                } else {
                    statusBadge = `<span class="badge">${p.status}</span>`;
                }

                let actions = '';
                const softCopyBtn = `
                    <button class="btn btn-outline" style="padding: 0.3rem 0.55rem; font-size: 0.75rem; color: #1e3a8a; border-color: #cbd5e1; display: inline-flex; align-items: center; gap: 0.25rem;" onclick="openProposalDossier(${p.id})" title="View and Download Official Proposal Soft Copy">
                        <i class="ph-bold ph-file-pdf"></i> Soft Copy
                    </button>
                `;

                if (p.status === 'approved') {
                    actions = `
                        <div style="display: flex; gap: 0.35rem; flex-wrap: wrap; align-items: center;">
                            ${softCopyBtn}
                            <a href="ChapterRequest.php" class="btn btn-outline" style="padding: 0.3rem 0.5rem; font-size: 0.75rem; text-decoration: none;" title="Request Poster">
                                <i class="ph ph-image"></i> Poster
                            </a>
                            <a href="ChapterReport.php?event_id=${p.id}" class="btn btn-primary" style="background: #16a34a; border-color: #15803d; padding: 0.3rem 0.5rem; font-size: 0.75rem; text-decoration: none;" title="Submit Report">
                                <i class="ph ph-file-arrow-up"></i> Report
                            </a>
                        </div>
                    `;
                } else {
                    actions = `
                        <div style="display: flex; gap: 0.35rem; flex-wrap: wrap; align-items: center;">
                            ${softCopyBtn}
                            <button class="btn btn-outline" style="padding: 0.3rem 0.5rem; font-size: 0.75rem;" onclick="openProposalDossier(${p.id})">Details</button>
                        </div>
                    `;
                }
                let progBadge = '';
                if (p.ytr_program) {
                    let pBg = '#eff6ff', pCol = '#1d4ed8', pBorder = '#bfdbfe';
                    if (p.ytr_program.startsWith('Rise up')) {
                        pBg = '#fffbeb'; pCol = '#b45309'; pBorder = '#fde68a';
                    } else if (p.ytr_program.startsWith('Build up')) {
                        pBg = '#f0fdf4'; pCol = '#15803d'; pBorder = '#bbf7d0';
                    } else if (p.ytr_program.startsWith('Gear up')) {
                        pBg = '#faf5ff'; pCol = '#7e22ce'; pBorder = '#e9d5ff';
                    }
                    progBadge = `<span class="badge" style="background: ${pBg}; color: ${pCol}; border: 1px solid ${pBorder}; font-size: 0.72rem; padding: 2px 7px; font-weight: 700;">
                        <i class="ph-bold ph-compass"></i> ${escapeHtml(p.ytr_program)}
                    </span>`;
                }

                const audienceBadge = `<span class="badge" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; font-size: 0.72rem; padding: 2px 7px; font-weight: 600;">
                    <i class="ph ph-users-three"></i> ${escapeHtml(p.target_audience || 'Open for All')}
                </span>`;

                let docBadges = '';
                if (p.attachment_path) {
                    docBadges += `<span class="badge" style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-size: 0.7rem; padding: 2px 6px;" title="Proposal Soft Copy Attached"><i class="ph-bold ph-paperclip"></i> Soft Copy</span>`;
                }
                if (p.drive_link) {
                    docBadges += `<a href="${escapeHtml(p.drive_link)}" target="_blank" rel="noopener noreferrer" class="badge" style="background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; font-size: 0.7rem; padding: 2px 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;" title="Open Cloud / Google Drive Proposal"><i class="ph-bold ph-google-drive-logo"></i> Drive</a>`;
                }

                return `
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: #0f172a;">${escapeHtml(p.title)}</div>
                            <div style="display: flex; gap: 0.35rem; align-items: center; flex-wrap: wrap; margin-top: 3px;">
                                <small style="color: #64748b; text-transform: uppercase;">${escapeHtml(p.event_type || 'Event')}</small>
                                ${progBadge}
                                ${audienceBadge}
                                ${docBadges}
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; color: #1e293b;">${escapeHtml(p.start_datetime ? p.start_datetime.substring(0, 10) : 'Date TBD')}</div>
                            <small style="color: #64748b;">${escapeHtml(p.venue || 'Venue TBD')}</small>
                        </td>
                        <td>
                            <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-weight: 700;">
                                <i class="ph ph-users"></i> Target: ${p.target_turnout || 100}
                            </span>
                        </td>
                        <td>
                            <span style="font-weight: 600; color: #0f172a;">PHP ${(p.budget_amount || 0).toLocaleString()}</span>
                            ${dueHtml}
                        </td>
                        <td>
                            ${statusBadge}
                            ${timerHtml}
                            ${p.review_note ? `<p style="margin: 4px 0 0; font-size: 0.75rem; color: #64748b; font-style: italic;">"${escapeHtml(p.review_note)}"</p>` : ''}
                        </td>
                        <td>${actions}</td>
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
            let p = proposalsData.find(item => item.id == proposalId);
            if (!p) {
                try {
                    const res = await YTR_API.proposals.getById(proposalId);
                    if (res.ok && res.data) p = res.data;
                } catch(err) {
                    console.error('Failed to fetch proposal details:', err);
                }
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
                attHtml += `
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; margin-bottom: ${p.drive_link ? '0.75rem' : '0'};">
                        <div>
                            <div style="font-weight: 700; color: #166534;"><i class="ph-bold ph-file-check"></i> Official Proposal Soft Copy Attached</div>
                            <div style="font-size: 0.72rem; color: #475569; margin-top: 2px;">Verified digital proposal dossier filed with chapter submission.</div>
                        </div>
                        <a href="${p.attachment_path}" download class="btn btn-primary btn-sm font-roboto print-no-print" style="background: #16a34a; border-color: #15803d; text-decoration: none; padding: 0.4rem 0.75rem;">
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
                    <div style="color: #475569; font-style: italic;">
                        <i class="ph ph-info"></i> No external document or drive link attached. This electronic dossier comprises your complete official proposal record.
                    </div>
                `;
            }

            attInfo.innerHTML = attHtml;

            document.getElementById('dossierSubmitterName').textContent = (currentUser && currentUser.full_name) ? currentUser.full_name : (p.requester_name || 'Chapter Coordinator');
            document.getElementById('dossierSubmitterTitle').textContent = 'Chapter Administrator';
            document.getElementById('dossierSubmitterStamp').textContent = 'Verified Chapter Submission';

            const statusBadge = document.getElementById('dossierStatusBadge');
            const isApproved = ['approved', 'published', 'ongoing', 'completed'].includes(p.status);
            const isPending = p.status === 'pending_approval';
            if (isApproved) {
                statusBadge.className = 'badge approved font-roboto';
                statusBadge.textContent = 'OFFICIALLY APPROVED';
            } else if (isPending) {
                statusBadge.className = 'badge pending font-roboto';
                statusBadge.textContent = 'PENDING NATIONAL REVIEW';
            } else {
                statusBadge.className = 'badge rose font-roboto';
                statusBadge.textContent = 'REVISION REQUESTED';
            }

            document.getElementById('dossierReviewerName').textContent = p.reviewer_name || 'Hamilton Jones Dela';
            document.getElementById('dossierReviewerTitle').textContent = p.reviewer_position || 'Chief Executive Operation';
            document.getElementById('dossierReviewNotes').textContent = p.review_note ? `Note: "${p.review_note}"` : (isApproved ? 'Cleared for Chapter Execution' : 'Awaiting Review Outcome');

            document.getElementById('proposalDossierModal').style.display = 'flex';
        }

        function closeProposalDossierModal() {
            document.getElementById('proposalDossierModal').style.display = 'none';
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
