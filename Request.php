<?php include 'includes/auth_guard.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Poster Approval | YTR Portal</title>
    <link rel="stylesheet" href="assets/National.css">
    <!-- Include Phosphor Icons for icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="assets/api-client.js"></script>
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar Include -->
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-wrapper">
            <!-- Topbar Include -->
            <?php include 'includes/topbar.php'; ?>

            <div class="page-content">
                
                <div class="page-header flex-between" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                    <div class="header-title-group">
                        <h1 class="font-roboto">Event Poster Approval</h1>
                        <p style="color: var(--text-muted); margin-top: 0.25rem;">Review, endorse, and manage promotional media requests and visual collateral across all chapters.</p>
                    </div>
                    <div class="header-actions">
                        <span class="portal-indicator national" style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 0.8rem; font-weight: 700; padding: 0.45rem 0.9rem; border-radius: 9999px; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <i class="ph-fill ph-shield-check"></i> National Review & Approval Portal
                        </span>
                    </div>
                </div>

                <!-- Pending Review Visual Banner -->
                <div id="pending-alert" class="pending-alert-banner" style="display: none;">
                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                        <i class="ph-fill ph-bell-ringing" style="font-size: 1.6rem; color: #b45309;"></i>
                        <div>
                            <strong style="color: #92400e; font-size: 0.95rem;">Pending Poster Review Action Required</strong>
                            <p style="margin: 0; color: #b45309; font-size: 0.85rem;" id="pending-alert-text">You have submissions awaiting design endorsement.</p>
                        </div>
                    </div>
                    <button class="btn btn-primary" style="background: #d97706; border-color: #b45309; padding: 0.4rem 0.85rem; font-size: 0.825rem;" onclick="switchToQueueTab()">
                        Review In-Queue (<span id="alert-queue-count">0</span>)
                    </button>
                </div>

                <!-- Post-Event Compliance Urgent Alert Banner -->
                <div id="national-compliance-alert" class="pending-alert-banner" style="display: none; background: #fff1f2; border: 1px solid #fecdd3; border-left: 5px solid #e11d48; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                        <i class="ph-bold ph-warning-octagon" style="font-size: 1.6rem; color: #e11d48;"></i>
                        <div>
                            <strong style="color: #9f1239; font-size: 0.95rem;">2-Week Post-Event Compliance Alert</strong>
                            <p style="margin: 0; color: #be123c; font-size: 0.85rem;" id="compliance-alert-text">Pending post-event documentation reports due or overdue.</p>
                        </div>
                    </div>
                    <button class="btn btn-primary" style="background: #e11d48; border-color: #be123c; padding: 0.4rem 0.85rem; font-size: 0.825rem;" onclick="filterUrgentEvents()">
                        View Pending Reports
                    </button>
                </div>

                <!-- 1. Metric Cards Row -->
                <div class="metrics-grid">
                    <div class="metric-card">
                        <div class="metric-icon blue"><i class="ph ph-calendar-check"></i></div>
                        <div>
                            <span class="metric-title font-roboto">Total Active Events</span>
                            <div class="metric-value font-roboto" id="metric-active">0</div>
                            <p class="metric-sub">Total approved events requiring media assets</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon orange"><i class="ph ph-file-text"></i></div>
                        <div>
                            <span class="metric-title font-roboto">Pending Poster Request</span>
                            <div class="metric-value font-roboto" id="metric-pending">0</div>
                            <p class="metric-sub">New applications awaiting design review</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon purple"><i class="ph ph-hourglass-high"></i></div>
                        <div>
                            <span class="metric-title font-roboto">Awaiting Final Asset</span>
                            <div class="metric-value font-roboto" id="metric-awaiting">0</div>
                            <p class="metric-sub">Designs currently in production pipeline</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon green"><i class="ph ph-check-circle"></i></div>
                        <div>
                            <span class="metric-title font-roboto">Concluded Media</span>
                            <div class="metric-value font-roboto" id="metric-concluded">0</div>
                            <p class="metric-sub">Posters finalized, released, and approved</p>
                        </div>
                    </div>
                </div>

                <!-- 2. Tabs and Filters -->
                <div class="tab-controls-wrapper">
                    <div class="tabs-container font-roboto">
                        <button class="tab-btn active font-roboto" id="tab-btn-accepted" onclick="switchTab('accepted', this)">
                            <i class="ph ph-check-circle"></i> Accepted (<span id="count-accepted">0</span>)
                        </button>
                        <button class="tab-btn font-roboto" id="tab-btn-queue" onclick="switchTab('queue', this)">
                            <i class="ph ph-clock"></i> In Queue (<span id="count-queue">0</span>)
                        </button>
                    </div>
                    <div class="filters" style="display: flex; gap: 0.75rem; align-items: center;">
                        <div class="search-bar" style="margin: 0; min-width: 240px;">
                            <i class="ph ph-magnifying-glass"></i>
                            <input type="text" id="poster-search" placeholder="Search title, chapter..." oninput="handleSearch()">
                        </div>
                    </div>
                </div>

                <!-- 3. Dynamic Card Grids -->
                
                <!-- TAB CONTENT: ACCEPTED -->
                <div id="accepted-grid" class="poster-grid tab-content active">
                    <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: var(--text-muted);">
                        <i class="ph ph-spinner ph-spin" style="font-size: 2rem;"></i>
                        <p style="margin-top: 0.5rem;">Loading accepted poster collaterals...</p>
                    </div>
                </div>

                <!-- TAB CONTENT: IN QUEUE -->
                <div id="queue-grid" class="poster-grid tab-content" style="display: none;">
                    <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: var(--text-muted);">
                        <i class="ph ph-spinner ph-spin" style="font-size: 2rem;"></i>
                        <p style="margin-top: 0.5rem;">Loading in-queue poster requests...</p>
                    </div>
                </div>

            </div>
        </div>
    </div>



    <!-- MODAL 1: PRE-APPROVAL VERIFICATION REVIEW (Image 1) -->
    <div id="verification-modal" class="modal-overlay" style="display: none;">
        <div class="modal-xl">
            <!-- Modal Header -->
            <div class="modal-header-banner">
                <div>
                    <h3 class="font-roboto">
                        <i class="ph-fill ph-check-circle" style="color: #16a34a;"></i> Event Poster Details & Verification
                    </h3>
                    <p>Review necessary details and verify requirements before approving public materials</p>
                </div>
                <button type="button" onclick="closeVerificationModal()" aria-label="Close modal">
                    <i class="ph ph-x"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body-scroll">
                <div class="verification-grid">
                    <!-- Left: Reference Poster & Details -->
                    <div class="verification-preview-card">
                        <div class="poster-media-box">
                            <img id="verif-poster-img" src="assets/images/background.jpg" alt="Reference Poster" onerror="this.src='assets/images/background.jpg'">
                            <button type="button" class="poster-zoom-btn" onclick="openZoomImage(document.getElementById('verif-poster-img').src)">
                                <i class="ph ph-magnifying-glass-plus"></i> View Reference Poster
                            </button>
                        </div>
                        <div>
                            <span class="badge" id="verif-chapter-badge" style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-weight: 700; margin-bottom: 0.5rem; display: inline-block;">
                                Chapter Submission
                            </span>
                            <h2 class="font-roboto" id="verif-event-title" style="font-size: 1.35rem; color: #0f172a; font-weight: 800; margin: 0 0 0.25rem;">
                                Event Title
                            </h2>
                            <p id="verif-theme" style="color: #475569; font-size: 0.9rem; font-style: italic; margin-bottom: 1rem;">
                                Theme / Concept
                            </p>
                        </div>

                        <div class="meta-key-value-list">
                            <div class="meta-row">
                                <span class="meta-label">Event Date & Time:</span>
                                <span class="meta-val" id="verif-datetime">--</span>
                            </div>
                            <div class="meta-row">
                                <span class="meta-label">Venue / Platform:</span>
                                <span class="meta-val" id="verif-venue">--</span>
                            </div>
                            <div class="meta-row">
                                <span class="meta-label">Target Attendees:</span>
                                <span class="meta-val" id="verif-participants">--</span>
                            </div>
                            <div class="meta-row">
                                <span class="meta-label">Chapter Name:</span>
                                <span class="meta-val" id="verif-chapter-name">--</span>
                            </div>
                            <div class="meta-row">
                                <span class="meta-label">Caption & Link:</span>
                                <span class="meta-val" id="verif-caption-link" style="word-break: break-all;">--</span>
                            </div>
                            <div class="meta-row">
                                <span class="meta-label">Requested Deadline:</span>
                                <span class="meta-val" id="verif-deadline" style="color: #dc2626; font-weight: 700;">--</span>
                            </div>
                            <div class="meta-row">
                                <span class="meta-label">Special Notes:</span>
                                <span class="meta-val" id="verif-notes">None</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Poster Requirements Checklist -->
                    <div class="checklist-card">
                        <h4 class="checklist-title">Poster Requirements</h4>
                        <p class="checklist-subtitle">Verify all mandatory media requirements are present before approving for production</p>

                        <div class="checklist-items" id="verification-checklist-container">
                            <label class="checklist-item verified" onclick="toggleCheckItem(this)">
                                <input type="checkbox" id="chk-datetime" checked>
                                <div class="checklist-text">
                                    <div class="checklist-name">Date and Time</div>
                                    <div class="checklist-sub">Schedule and duration clearly stated</div>
                                </div>
                                <i class="ph-fill ph-check-circle" style="color: #16a34a; font-size: 1.25rem;"></i>
                            </label>

                            <label class="checklist-item verified" onclick="toggleCheckItem(this)">
                                <input type="checkbox" id="chk-venue" checked>
                                <div class="checklist-text">
                                    <div class="checklist-name">Venue / Platform</div>
                                    <div class="checklist-sub">Physical address or virtual stream link</div>
                                </div>
                                <i class="ph-fill ph-check-circle" style="color: #16a34a; font-size: 1.25rem;"></i>
                            </label>

                            <label class="checklist-item verified" onclick="toggleCheckItem(this)">
                                <input type="checkbox" id="chk-participants" checked>
                                <div class="checklist-text">
                                    <div class="checklist-name">Target number of participants</div>
                                    <div class="checklist-sub">Expected attendance quota confirmed</div>
                                </div>
                                <i class="ph-fill ph-check-circle" style="color: #16a34a; font-size: 1.25rem;"></i>
                            </label>

                            <label class="checklist-item verified" onclick="toggleCheckItem(this)">
                                <input type="checkbox" id="chk-chapter" checked>
                                <div class="checklist-text">
                                    <div class="checklist-name">Chapter Name</div>
                                    <div class="checklist-sub">Branch identity & official logo placement</div>
                                </div>
                                <i class="ph-fill ph-check-circle" style="color: #16a34a; font-size: 1.25rem;"></i>
                            </label>

                            <label class="checklist-item verified" onclick="toggleCheckItem(this)">
                                <input type="checkbox" id="chk-visuals" checked>
                                <div class="checklist-text">
                                    <div class="checklist-name">Relevant Visuals and Design elements</div>
                                    <div class="checklist-sub">Draft mockup or reference assets provided</div>
                                </div>
                                <i class="ph-fill ph-check-circle" style="color: #16a34a; font-size: 1.25rem;"></i>
                            </label>

                            <label class="checklist-item verified" onclick="toggleCheckItem(this)">
                                <input type="checkbox" id="chk-caption" checked>
                                <div class="checklist-text">
                                    <div class="checklist-name">Caption & Registration Link</div>
                                    <div class="checklist-sub">Publicity blurb and signup URL attached</div>
                                </div>
                                <i class="ph-fill ph-check-circle" style="color: #16a34a; font-size: 1.25rem;"></i>
                            </label>
                        </div>

                        <div style="margin-top: 1.25rem; padding: 0.75rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; text-align: center;">
                            <span id="verif-check-summary" style="font-weight: 700; color: #16a34a; font-size: 0.85rem;">
                                <i class="ph-fill ph-check-circle"></i> All 6 Requirements Confirmed
                            </span>
                        </div>

                        <!-- Provide Official Poster Graphic Upload Area -->
                        <div style="margin-top: 1.25rem; border-top: 1px dashed #cbd5e1; padding-top: 1rem;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #0f172a; margin-bottom: 0.25rem;">
                                <i class="ph-bold ph-image" style="color: #2563eb;"></i> Provide Official Poster Graphic
                            </label>
                            <p style="font-size: 0.725rem; color: #64748b; margin-bottom: 0.6rem;">
                                Upload the finalized official poster designed by National Creative. The Chapter will receive and download this collateral immediately upon approval.
                            </p>
                            <div class="proof-dropzone" style="padding: 1rem;" onclick="document.getElementById('verif-poster-file').click()">
                                <i class="ph ph-cloud-arrow-up" style="font-size: 1.75rem; color: #2563eb;"></i>
                                <p style="margin: 0.25rem 0 0; font-size: 0.825rem; font-weight: 700; color: #1e293b;">Click to upload official poster</p>
                                <p style="margin: 0; font-size: 0.7rem; color: #64748b;">PNG, JPG, WEBP up to 50MB</p>
                                <input type="file" id="verif-poster-file" accept="image/*" onchange="handleVerifPosterChange(this)">
                            </div>
                            <div id="verif-file-display" style="display: none; margin-top: 0.45rem; font-size: 0.78rem; color: #16a34a; font-weight: 600;">
                                <i class="ph-fill ph-file-check"></i> <span id="verif-file-name"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Banner -->
                <div class="verification-notice-bar">
                    <div style="display: flex; align-items: center; gap: 0.65rem;">
                        <i class="ph-fill ph-info" style="font-size: 1.35rem; color: #2563eb;"></i>
                        <span style="font-size: 0.85rem; font-weight: 500;">
                            Approving releases the official poster to the chapter and moves the pipeline to Stage 2: Posted on Page.
                        </span>
                    </div>
                    <div style="display: flex; gap: 0.75rem;">
                        <button type="button" class="btn btn-outline" onclick="closeVerificationModal()">Cancel</button>
                        <button type="button" class="btn btn-outline-danger" onclick="rejectCurrentInQueue()">Reject Request</button>
                        <button type="button" class="btn btn-primary" id="verif-approve-btn" onclick="submitVerifyApprove()" style="background: #16a34a; border-color: #15803d;">
                            <i class="ph-fill ph-check-circle"></i> Approve & Provide Poster
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- MODAL 2: MULTI-STAGE EVENT TIMELINE & PROOF MANAGEMENT (Image 2) -->
    <div id="timeline-modal" class="modal-overlay" style="display: none;">
        <div class="modal-xl">
            <!-- Modal Header -->
            <div class="modal-header-banner">
                <div>
                    <h3 class="font-roboto">
                        <i class="ph-fill ph-kanban" style="color: var(--primary);"></i> Event Poster Details & Progress
                    </h3>
                    <p>View the complete information, milestone deliverables, and progress of this chapter event</p>
                </div>
                <button type="button" onclick="closeTimelineModal()" aria-label="Close modal">
                    <i class="ph ph-x"></i>
                </button>
            </div>

            <!-- Modal Body (3 Columns) -->
            <div class="modal-body-scroll">
                <div class="timeline-grid">
                    <!-- Column 1: Event Poster Preview Card -->
                    <div class="timeline-left-card">
                        <div class="poster-media-box">
                            <img id="tl-poster-img" src="assets/images/background.jpg" alt="Official Poster" onerror="this.src='assets/images/background.jpg'">
                            <button type="button" class="poster-zoom-btn" onclick="openZoomImage(document.getElementById('tl-poster-img').src)">
                                <i class="ph ph-magnifying-glass-plus"></i> View Full Poster
                            </button>
                        </div>
                        <div>
                            <span class="badge" id="tl-status-pill" style="background: #dbeafe; color: #1e40af; font-weight: 700; margin-bottom: 0.5rem; display: inline-block; font-size: 0.78rem; padding: 4px 10px; border-radius: 6px;">
                                Stage 1: Poster Request
                            </span>
                            <h3 class="font-roboto" id="tl-event-title" style="font-size: 1.15rem; color: #0f172a; font-weight: 800; margin: 0 0 0.35rem; line-height: 1.35;">
                                Event Title
                            </h3>
                            <p id="tl-event-theme" style="color: #64748b; font-size: 0.875rem; margin-bottom: 0.75rem; line-height: 1.45;">
                                Subtitle / Theme
                            </p>
                            <div style="font-size: 0.875rem; color: #334155; line-height: 1.6; border-top: 1px solid #f1f5f9; padding-top: 0.65rem; display: flex; flex-direction: column; gap: 0.35rem;">
                                <div><strong>Chapter:</strong> <span id="tl-chapter-name">--</span></div>
                                <div><strong>Date:</strong> <span id="tl-date">--</span></div>
                                <div><strong>Venue:</strong> <span id="tl-venue">--</span></div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline" style="width: 100%; margin-top: auto; padding: 0.65rem 1rem; font-size: 0.875rem; font-weight: 600; border-radius: 8px;" onclick="downloadCurrentPoster()">
                            <i class="ph ph-download-simple"></i> Download Poster Collateral
                        </button>
                    </div>

                    <!-- Column 2: Event Timeline (Numbered Vertical Pipeline) -->
                    <div class="timeline-center-card">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 0.75rem;">
                            <h4 class="font-roboto" style="margin: 0; color: #0f172a; font-weight: 800; font-size: 1.15rem;">
                                Event Timeline
                            </h4>
                            <span style="font-size: 0.825rem; color: #64748b; font-weight: 600;">5 Core Production Milestones</span>
                        </div>

                        <div class="timeline-pipeline" id="tl-pipeline-container">
                            <!-- Dynamically populated 5 stages -->
                        </div>
                    </div>

                    <!-- Column 3: Admin Controls & Proof Ledger -->
                    <div class="timeline-right-card">
                        <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 0.75rem;">
                            <h4 class="font-roboto" style="margin: 0; color: #0f172a; font-weight: 800; font-size: 1.15rem;">
                                Admin Controls
                            </h4>
                            <p style="margin: 0.25rem 0 0; color: #64748b; font-size: 0.85rem;">Upload deliverables, advance pipeline, and attach proofs</p>
                        </div>

                        <!-- Stage 4: Documentation & Conducted Audit Review Card -->
                        <div id="ctl-doc-audit-card" style="display: none; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; padding: 1.15rem; margin-bottom: 1.15rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.65rem;">
                                <h5 class="font-roboto" style="margin: 0; font-size: 1rem; font-weight: 800; color: #0f172a;">
                                    <i class="ph-bold ph-clipboard-text" style="color: #2563eb;"></i> Event Documentation Review
                                </h5>
                                <span id="audit-status-badge" class="badge" style="background: #fef3c7; color: #92400e; font-weight: 700; font-size: 0.75rem; padding: 4px 10px; border-radius: 6px;">Pending</span>
                            </div>

                            <div id="audit-timer-notice" style="display: none; font-size: 0.85rem; margin-bottom: 0.75rem; padding: 0.5rem 0.75rem; border-radius: 6px;"></div>

                            <div id="audit-details-container" style="font-size: 0.875rem; line-height: 1.6; color: #334155;">
                                <!-- Populated dynamically -->
                            </div>

                            <div id="audit-action-container" style="margin-top: 0.85rem;">
                                <button type="button" class="btn btn-primary" id="btn-approve-audit" onclick="submitApproveDocumentation()" style="width: 100%; background: #16a34a; border-color: #15803d; font-size: 0.875rem; font-weight: 700; padding: 0.65rem 1rem; border-radius: 8px;">
                                    <i class="ph-bold ph-check-circle"></i> Approve Documentation & Conclude Event
                                </button>
                            </div>
                        </div>

                        <form id="admin-timeline-form" onsubmit="submitTimelineUpdate(event)">
                            <div style="margin-bottom: 0.85rem;">
                                <label style="display: block; font-size: 0.875rem; font-weight: 700; color: #1e293b; margin-bottom: 0.4rem;">
                                    Update Timeline Status
                                </label>
                                <select id="ctl-stage-select" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem; background: #fff;" onchange="handleStageSelectChange()">
                                    <option value="1">1. Poster Request</option>
                                    <option value="2">2. Posted on Page</option>
                                    <option value="3">3. Promotional Video</option>
                                    <option value="4">4. Documentation and Report</option>
                                    <option value="5">5. Event Concluded and Reported</option>
                                </select>
                            </div>

                            <div style="margin-bottom: 0.85rem;">
                                <label style="display: block; font-size: 0.875rem; font-weight: 700; color: #1e293b; margin-bottom: 0.4rem;">
                                    Stage State
                                </label>
                                <select id="ctl-status-select" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem; background: #fff;">
                                    <option value="completed">Completed</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>

                            <div style="margin-bottom: 0.85rem;">
                                <label style="display: block; font-size: 0.875rem; font-weight: 700; color: #1e293b; margin-bottom: 0.4rem;">
                                    Upload Proof / Official Media
                                </label>
                                <div class="proof-dropzone" onclick="document.getElementById('ctl-proof-file').click()">
                                    <i class="ph ph-cloud-arrow-up" style="font-size: 2.2rem; color: #2563eb;"></i>
                                    <p style="margin: 0.35rem 0 0; font-size: 0.875rem; font-weight: 700; color: #1e293b;">Click to upload proof</p>
                                    <p style="margin: 0.15rem 0 0; font-size: 0.78rem; color: #64748b;">Images, PDF, MP4 up to 50MB</p>
                                    <input type="file" id="ctl-proof-file" accept="image/*,video/mp4,application/pdf" onchange="handleProofFileChange(this)">
                                </div>
                                <div id="ctl-file-name-display" style="display: none; margin-top: 0.5rem; font-size: 0.85rem; color: #16a34a; font-weight: 600;">
                                    <i class="ph-fill ph-file-check"></i> <span id="ctl-file-name-text"></span>
                                </div>
                                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #475569; margin-top: 0.6rem; cursor: pointer;">
                                    <input type="checkbox" id="ctl-is-final-poster" checked> Set as official poster for Chapter download
                                </label>
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label style="display: block; font-size: 0.875rem; font-weight: 700; color: #1e293b; margin-bottom: 0.4rem;">
                                    Add Notes (Optional)
                                </label>
                                <textarea id="ctl-notes" rows="2" placeholder="e.g. Published to official FB page, post link: https://..." style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem; min-height: 65px;"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary" id="ctl-submit-btn" style="width: 100%; padding: 0.7rem 1.25rem; font-size: 0.9rem; font-weight: 700; border-radius: 8px;">
                                <i class="ph ph-floppy-disk"></i> Save Update
                            </button>
                        </form>

                        <!-- Recent Proofs Ledger -->
                        <div style="border-top: 1px solid #f1f5f9; padding-top: 1rem; margin-top: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                <h5 class="font-roboto" style="margin: 0; color: #0f172a; font-weight: 700; font-size: 0.95rem;">Recent Proofs</h5>
                                <span id="ctl-proofs-count" style="font-size: 0.8rem; color: #64748b; font-weight: 600;">0 attached</span>
                            </div>
                            <div id="ctl-recent-proofs-list" style="max-height: 220px; overflow-y: auto;">
                                <!-- Populated dynamically -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- JavaScript Data Binding & Interactive Workflow -->
    <script>
        let currentTab = 'accepted';
        let acceptedCache = [];
        let queueCache = [];
        let activeRequest = null;
        let activeTimelineData = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadMetrics();
            loadPosterData('accepted');
            loadPosterData('queue');

            // Close modals when clicking on overlay backdrop
            document.querySelectorAll('.modal-overlay').forEach(overlay => {
                overlay.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.style.display = 'none';
                    }
                });
            });

            // Close open modals on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.modal-overlay').forEach(overlay => {
                        overlay.style.display = 'none';
                    });
                }
            });
        });

        async function loadMetrics() {
            try {
                const res = await YTR_API.posterRequests.getMetrics();
                if (res.ok && res.data) {
                    const m = res.data;
                    document.getElementById('metric-active').textContent = m.total_active_events || 0;
                    document.getElementById('metric-pending').textContent = m.pending_poster_request || 0;
                    document.getElementById('metric-awaiting').textContent = m.awaiting_report || 0;
                    document.getElementById('metric-concluded').textContent = m.concluded_events || 0;

                    if (m.pending_poster_request > 0) {
                        const alert = document.getElementById('pending-alert');
                        alert.style.display = 'flex';
                        document.getElementById('alert-queue-count').textContent = m.pending_poster_request;
                        document.getElementById('pending-alert-text').textContent = `You have ${m.pending_poster_request} new poster application(s) awaiting review.`;
                    } else {
                        document.getElementById('pending-alert').style.display = 'none';
                    }
                }
            } catch (err) {
                console.error('Failed to load poster metrics:', err);
            }
        }

        async function loadPosterData(tab) {
            try {
                const res = await YTR_API.posterRequests.list(tab);
                if (res.ok && res.data) {
                    if (tab === 'accepted') {
                        acceptedCache = res.data;
                        document.getElementById('count-accepted').textContent = res.data.length;
                        renderAcceptedGrid(res.data);
                    } else {
                        queueCache = res.data;
                        document.getElementById('count-queue').textContent = res.data.length;
                        renderQueueGrid(res.data);
                    }
                }
            } catch (err) {
                console.error(`Failed to load ${tab} posters:`, err);
            }
        }

        function renderAcceptedGrid(items) {
            const grid = document.getElementById('accepted-grid');
            if (!items || items.length === 0) {
                grid.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: #fff; border-radius: 8px; border: 1px dashed #cbd5e1;">
                        <i class="ph ph-image-square" style="font-size: 2.5rem; color: #94a3b8;"></i>
                        <h4 style="color: #334155; margin: 0.5rem 0 0.25rem;">No Accepted Posters Yet</h4>
                        <p style="color: #64748b; font-size: 0.9rem;">Approved poster collateral will appear here once endorsed.</p>
                    </div>`;
                return;
            }

            // Check if any event has urgent or overdue 2-week post-event report deadline
            const urgentOrOverdue = items.filter(p => p.report_timer && (p.report_timer.status === 'urgent' || p.report_timer.status === 'overdue'));
            const alertBanner = document.getElementById('national-compliance-alert');
            if (alertBanner) {
                if (urgentOrOverdue.length > 0) {
                    alertBanner.style.display = 'flex';
                    document.getElementById('compliance-alert-text').textContent = `${urgentOrOverdue.length} chapter event(s) have passed with 2-week post-event documentation reports pending or overdue!`;
                } else {
                    alertBanner.style.display = 'none';
                }
            }

            grid.innerHTML = items.map(p => {
                const dueBadgeHtml = p.due_badge 
                    ? `<span class="${p.due_badge.cls}"><i class="ph ph-clock"></i> ${p.due_badge.label}</span>` 
                    : '';
                const timerBadgeHtml = p.report_timer 
                    ? `<span class="${p.report_timer.badge_cls}"><i class="ph-bold ph-timer"></i> ${p.report_timer.label}</span>` 
                    : (p.approval_timer ? `<span class="${p.approval_timer.cls}"><i class="ph ph-timer"></i> ${p.approval_timer.label}</span>` : '');

                const isCompleted = p.status === 'completed';
                const statusLabel = isCompleted ? 'Event Accomplished' : (p.current_step ? `Stage ${p.current_step} Active` : 'Production In-Progress');
                const statusCls = isCompleted ? 'green' : 'blue';

                const posterImg = p.final_poster_path || p.reference_poster_path || 'assets/images/background.jpg';

                return `
                    <div class="poster-card type-accepted">
                        <div class="card-hero">
                            <img src="${escapeHtml(posterImg)}" alt="${escapeHtml(p.event_title)}" onerror="this.src='assets/images/background.jpg'">
                            <div class="card-hero-badges">
                                <span class="status-badge ${statusCls}"><i class="ph ${isCompleted ? 'ph-check-circle' : 'ph-clock'}"></i> ${statusLabel}</span>
                                ${timerBadgeHtml}
                                ${dueBadgeHtml}
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="event-title font-roboto" style="color: var(--primary); font-weight: 700; margin-bottom: 0.5rem;">${escapeHtml(p.event_title)}</h4>
                            <ul class="event-bullets">
                                <li><i class="ph ph-calendar"></i> ${escapeHtml(p.event_date || 'Date TBD')}</li>
                                <li><i class="ph ph-map-pin"></i> ${escapeHtml(p.venue || 'Venue TBD')}</li>
                                <li><i class="ph ph-buildings"></i> ${escapeHtml(p.chapter_name || 'Chapter')}</li>
                            </ul>
                            <p class="event-desc" style="color: #475569; font-size: 0.875rem;">
                                ${escapeHtml(p.theme_or_concept || p.review_note || 'Official event banner approved and queued for distribution.')}
                            </p>
                        </div>
                        <div class="card-footer dual-btn">
                            <button class="btn btn-outline" onclick="triggerDownload('${escapeHtml(p.event_title)}', '${escapeHtml(posterImg)}')">
                                <i class="ph ph-download-simple"></i> Download
                            </button>
                            <button class="btn btn-primary" onclick="openTimelineModal(${p.id})">
                                View Details
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function renderQueueGrid(items) {
            const grid = document.getElementById('queue-grid');
            if (!items || items.length === 0) {
                grid.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: #fff; border-radius: 8px; border: 1px dashed #cbd5e1;">
                        <i class="ph ph-check-circle" style="font-size: 2.5rem; color: #16a34a;"></i>
                        <h4 style="color: #334155; margin: 0.5rem 0 0.25rem;">All Clear in Queue</h4>
                        <p style="color: #64748b; font-size: 0.9rem;">There are no pending poster design submissions awaiting review.</p>
                    </div>`;
                return;
            }

            grid.innerHTML = items.map(p => {
                const dueBadgeHtml = p.due_badge 
                    ? `<span class="${p.due_badge.cls}" style="margin-left: 0.4rem;"><i class="ph ph-clock"></i> ${p.due_badge.label}</span>` 
                    : '';
                const posterImg = p.reference_poster_path || 'assets/images/background.jpg';

                return `
                    <div class="poster-card type-queue" id="poster-card-${p.id}">
                        <div class="card-header flex-between" style="border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                            <div class="chapter-info" style="display: flex; align-items: center; gap: 0.75rem;">
                                <div class="avatar-placeholder"><i class="ph ph-users"></i></div>
                                <div class="info-group">
                                    <span class="label">Chapter name</span>
                                    <span class="value" style="font-weight: 700; color: #0f172a;">${escapeHtml(p.chapter_name || 'Chapter')}</span>
                                </div>
                            </div>
                            <div class="info-group text-right">
                                <span class="label">Requested Date</span>
                                <span class="value">${escapeHtml(p.submitted_at ? p.submitted_at.substring(0, 10) : 'Recent')}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="info-group mb-3" style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <span class="label">Requester / Contact:</span>
                                    <span class="value" style="font-weight: 600;">${escapeHtml(p.requester_name || 'Chapter Admin')}</span>
                                </div>
                                <div>
                                    ${dueBadgeHtml}
                                </div>
                            </div>
                            <div class="event-preview" style="cursor: pointer;" onclick="openVerificationModal(${p.id})">
                                <img src="${escapeHtml(posterImg)}" alt="Poster Thumbnail" class="poster-thumb" onerror="this.src='assets/images/background.jpg'">
                                <div class="preview-details">
                                    <div class="info-group mb-2">
                                        <span class="label">Event Title:</span>
                                        <span class="value font-roboto" style="font-weight: 700; color: var(--primary);">${escapeHtml(p.event_title)}</span>
                                    </div>
                                    <div class="info-group">
                                        <span class="label">Concept & Venue:</span>
                                        <span class="value text-sm">${escapeHtml(p.theme_or_concept || 'Theme not specified')}<br><small style="color: #64748b;">${escapeHtml(p.venue || '')}</small></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer dual-btn">
                            <button class="btn btn-outline-danger" onclick="rejectQueueItem(${p.id})">
                                <i class="ph ph-x"></i> Reject
                            </button>
                            <button class="btn btn-primary" onclick="openVerificationModal(${p.id})">
                                <i class="ph ph-check"></i> Approve
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function switchTab(tabId, clickedBtn) {
            currentTab = tabId;
            document.querySelectorAll('.tab-content').forEach(content => content.style.display = 'none');
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(tabId + '-grid').style.display = 'grid';
            if (clickedBtn) clickedBtn.classList.add('active');
        }

        function switchToQueueTab() {
            const btn = document.getElementById('tab-btn-queue');
            switchTab('queue', btn);
        }

        function handleSearch() {
            const q = document.getElementById('poster-search').value.toLowerCase().trim();
            if (currentTab === 'accepted') {
                const filtered = acceptedCache.filter(p => 
                    (p.event_title && p.event_title.toLowerCase().includes(q)) ||
                    (p.chapter_name && p.chapter_name.toLowerCase().includes(q)) ||
                    (p.venue && p.venue.toLowerCase().includes(q))
                );
                renderAcceptedGrid(filtered);
            } else {
                const filtered = queueCache.filter(p => 
                    (p.event_title && p.event_title.toLowerCase().includes(q)) ||
                    (p.chapter_name && p.chapter_name.toLowerCase().includes(q)) ||
                    (p.requester_name && p.requester_name.toLowerCase().includes(q))
                );
                renderQueueGrid(filtered);
            }
        }

        // ==========================================
        // 1. PRE-APPROVAL VERIFICATION MODAL (Image 1)
        // ==========================================
        function openVerificationModal(id) {
            const item = queueCache.find(p => p.id === id) || acceptedCache.find(p => p.id === id);
            if (!item) return;

            activeRequest = item;

            // Populate preview details
            const posterPath = item.reference_poster_path || 'assets/images/background.jpg';
            document.getElementById('verif-poster-img').src = posterPath;
            document.getElementById('verif-chapter-badge').textContent = (item.chapter_name || 'Chapter') + ' • Verification Review';
            document.getElementById('verif-event-title').textContent = item.event_title || 'Untitled Event';
            document.getElementById('verif-theme').textContent = item.theme_or_concept || 'Theme not specified';
            
            const timeStr = item.event_time ? ` • ${item.event_time}` : '';
            document.getElementById('verif-datetime').textContent = (item.event_date || 'Date TBD') + timeStr;
            document.getElementById('verif-venue').textContent = item.venue || 'Venue TBD';
            document.getElementById('verif-participants').textContent = item.target_participants || '100+ Participants';
            document.getElementById('verif-chapter-name').textContent = item.chapter_name || 'National Chapter';
            
            const capText = item.caption ? item.caption : 'Official promo blurb prepared by Chapter.';
            const regText = item.registration_link ? ` | Link: ${item.registration_link}` : '';
            document.getElementById('verif-caption-link').textContent = capText + regText;
            document.getElementById('verif-deadline').textContent = item.requested_deadline || 'Urgent Production';
            document.getElementById('verif-notes').textContent = item.additional_notes || item.review_note || 'None recorded';

            // Auto-check checklist items based on provided details
            document.getElementById('chk-datetime').checked = !!item.event_date;
            document.getElementById('chk-venue').checked = !!item.venue;
            document.getElementById('chk-participants').checked = !!item.target_participants;
            document.getElementById('chk-chapter').checked = !!item.chapter_name;
            document.getElementById('chk-visuals').checked = true;
            document.getElementById('chk-caption').checked = !!(item.caption || item.registration_link);

            // Reset official poster input and dropzone styling
            const fileInput = document.getElementById('verif-poster-file');
            if (fileInput) fileInput.value = '';
            const fileDisplay = document.getElementById('verif-file-display');
            if (fileDisplay) fileDisplay.style.display = 'none';
            const dropzone = fileInput ? (fileInput.closest('.proof-dropzone') || fileInput.parentElement) : null;
            if (dropzone) {
                dropzone.style.borderColor = '#cbd5e1';
                dropzone.style.backgroundColor = '';
            }

            updateChecklistCount();
            document.getElementById('verification-modal').style.display = 'flex';
        }

        function handleVerifPosterChange(input) {
            if (input.files && input.files[0]) {
                const f = input.files[0];
                document.getElementById('verif-file-name').textContent = `${f.name} (${(f.size / 1024 / 1024).toFixed(2)} MB)`;
                document.getElementById('verif-file-display').style.display = 'block';
                const dropzone = input.closest('.proof-dropzone') || input.parentElement;
                if (dropzone) {
                    dropzone.style.borderColor = '#cbd5e1';
                    dropzone.style.backgroundColor = '';
                }
            }
        }

        function closeVerificationModal() {
            document.getElementById('verification-modal').style.display = 'none';
        }

        function toggleCheckItem(labelEl) {
            const chk = labelEl.querySelector('input[type="checkbox"]');
            // Timeout to allow browser click event to sync
            setTimeout(() => {
                if (chk.checked) {
                    labelEl.classList.add('verified');
                    labelEl.querySelector('i').className = 'ph-fill ph-check-circle';
                    labelEl.querySelector('i').style.color = '#16a34a';
                } else {
                    labelEl.classList.remove('verified');
                    labelEl.querySelector('i').className = 'ph ph-circle';
                    labelEl.querySelector('i').style.color = '#94a3b8';
                }
                updateChecklistCount();
            }, 10);
        }

        function updateChecklistCount() {
            const checkboxes = document.querySelectorAll('#verification-checklist-container input[type="checkbox"]');
            let checked = 0;
            checkboxes.forEach(c => { if (c.checked) checked++; });
            const summary = document.getElementById('verif-check-summary');
            if (checked === checkboxes.length) {
                summary.innerHTML = '<i class="ph-fill ph-check-circle"></i> All 6 Requirements Confirmed';
                summary.style.color = '#16a34a';
            } else {
                summary.innerHTML = `<i class="ph ph-warning-circle"></i> ${checked} of ${checkboxes.length} Requirements Verified`;
                summary.style.color = '#d97706';
            }
        }

        async function submitVerifyApprove() {
            if (!activeRequest) return;

            const posterInput = document.getElementById('verif-poster-file');
            const hasPoster = posterInput && posterInput.files && posterInput.files[0];

            if (!hasPoster) {
                const dropzone = posterInput ? (posterInput.closest('.proof-dropzone') || posterInput.parentElement) : null;
                if (dropzone) {
                    dropzone.style.borderColor = '#ef4444';
                    dropzone.style.backgroundColor = '#fef2f2';
                }
                alert('Please upload the official poster graphic before approving this request.');
                return;
            }

            const btn = document.getElementById('verif-approve-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Approving...';

            const checklist = [];
            if (document.getElementById('chk-datetime').checked) checklist.push('date_time');
            if (document.getElementById('chk-venue').checked) checklist.push('venue');
            if (document.getElementById('chk-participants').checked) checklist.push('participants');
            if (document.getElementById('chk-chapter').checked) checklist.push('chapter_name');
            if (document.getElementById('chk-visuals').checked) checklist.push('visuals');
            if (document.getElementById('chk-caption').checked) checklist.push('caption_link');

            const payload = new FormData();
            payload.append('checklist', JSON.stringify(checklist));
            payload.append('review_note', 'Verified and official poster released by National Admin.');
            payload.append('official_poster', posterInput.files[0]);

            try {
                const res = await YTR_API.posterRequests.verifyApprove(activeRequest.id, payload);

                if (res.ok) {
                    const approvedId = activeRequest.id;
                    closeVerificationModal();
                    
                    // Reload data & switch to Accepted tab
                    await loadMetrics();
                    await loadPosterData('accepted');
                    await loadPosterData('queue');
                    switchTab('accepted', document.getElementById('tab-btn-accepted'));

                    // Automatically open the Event Timeline & Proof Management Modal (Image 2)
                    openTimelineModal(approvedId);
                } else {
                    alert('Approval failed: ' + (res.message || 'Error'));
                }
            } catch (err) {
                console.error('Verify Approve error:', err);
                alert('Network error connecting to API.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="ph-fill ph-check-circle"></i> Approve & Provide Poster';
            }
        }

        async function rejectCurrentInQueue() {
            if (!activeRequest) return;
            rejectQueueItem(activeRequest.id);
        }

        async function rejectQueueItem(id) {
            const reason = prompt('Enter reason for rejecting this poster request:', 'Missing required schedule or venue details.');
            if (reason === null) return;

            try {
                const res = await YTR_API.posterRequests.reject(id, reason);
                if (res.ok) {
                    alert('Poster request rejected.');
                    closeVerificationModal();
                    loadMetrics();
                    loadPosterData('accepted');
                    loadPosterData('queue');
                } else {
                    alert('Rejection failed: ' + (res.message || 'Error'));
                }
            } catch (err) {
                console.error('Reject error:', err);
            }
        }

        // ==========================================
        // 2. EVENT TIMELINE & PROOF MODAL (Image 2)
        // ==========================================
        async function openTimelineModal(id) {
            document.getElementById('timeline-modal').style.display = 'flex';
            const pipelineEl = document.getElementById('tl-pipeline-container');
            pipelineEl.innerHTML = `
                <div style="text-align: center; padding: 2rem; color: #64748b;">
                    <i class="ph ph-spinner ph-spin" style="font-size: 1.8rem;"></i>
                    <p style="margin-top: 0.5rem; font-size: 0.85rem;">Loading timeline progress...</p>
                </div>`;

            try {
                const res = await YTR_API.posterRequests.getTimeline(id);
                if (res.ok && res.data) {
                    activeTimelineData = res.data;
                    const req = res.data.request;
                    activeRequest = req;

                    // Left Column Details
                    const posterSrc = req.final_poster_path || req.reference_poster_path || 'assets/images/background.jpg';
                    document.getElementById('tl-poster-img').src = posterSrc;
                    document.getElementById('tl-event-title').textContent = req.event_title || 'Untitled Event';
                    document.getElementById('tl-event-theme').textContent = req.theme_or_concept || 'Theme not specified';
                    document.getElementById('tl-chapter-name').textContent = req.chapter_name || 'Chapter';
                    document.getElementById('tl-date').textContent = req.event_date || 'Date TBD';
                    document.getElementById('tl-venue').textContent = req.venue || 'Venue TBD';

                    const currentStepNum = req.current_step || 1;
                    const isDone = req.status === 'completed';
                    document.getElementById('tl-status-pill').textContent = isDone ? 'All Milestones Concluded' : `Active Stage: ${currentStepNum} of 5`;
                    document.getElementById('tl-status-pill').style.background = isDone ? '#dcfce7' : '#dbeafe';
                    document.getElementById('tl-status-pill').style.color = isDone ? '#15803d' : '#1e40af';

                    // Center Column: Render 5 Steps
                    renderPipelineSteps(res.data.steps || []);

                    // Right Column: Documentation Audit Card Logic
                    const auditCard = document.getElementById('ctl-doc-audit-card');
                    const auditTimer = document.getElementById('audit-timer-notice');
                    const auditDetails = document.getElementById('audit-details-container');
                    const auditStatusBadge = document.getElementById('audit-status-badge');
                    const auditActionBtn = document.getElementById('btn-approve-audit');

                    if (req.report_timer) {
                        auditTimer.style.display = 'block';
                        auditTimer.className = req.report_timer.badge_cls;
                        auditTimer.innerHTML = `<i class="ph-bold ph-timer"></i> ${escapeHtml(req.report_timer.message || req.report_timer.label)}`;
                    } else {
                        auditTimer.style.display = 'none';
                    }

                    const docStatus = req.documentation_status || 'none';
                    if (docStatus === 'submitted' || docStatus === 'approved' || req.current_step >= 4 || req.is_event_conducted) {
                        auditCard.style.display = 'block';

                        if (docStatus === 'approved') {
                            auditStatusBadge.textContent = 'Report Validated & Concluded';
                            auditStatusBadge.style.background = '#dcfce7';
                            auditStatusBadge.style.color = '#15803d';
                            auditActionBtn.style.display = 'none';
                        } else if (docStatus === 'submitted') {
                            auditStatusBadge.textContent = 'Report Submitted — Needs Validation';
                            auditStatusBadge.style.background = '#fef3c7';
                            auditStatusBadge.style.color = '#92400e';
                            auditActionBtn.style.display = 'block';
                            auditActionBtn.innerHTML = '<i class="ph-bold ph-seal-check"></i> Validate Report & Conclude Timeline';
                        } else {
                            auditStatusBadge.textContent = 'Awaiting Chapter Report';
                            auditStatusBadge.style.background = '#f1f5f9';
                            auditStatusBadge.style.color = '#64748b';
                            auditActionBtn.style.display = 'none';
                        }

                        let proofDownloadHtml = '<span style="color: #94a3b8; font-style: italic;">No attachment uploaded</span>';
                        if (req.documentation_proof_path) {
                            const fname = req.documentation_proof_path.split('/').pop();
                            proofDownloadHtml = `
                                <a href="${escapeHtml(req.documentation_proof_path)}" target="_blank" class="step-attachment-pill" style="margin-top: 0.25rem;">
                                    <i class="ph-bold ph-file-arrow-down"></i> ${escapeHtml(fname)}
                                </a>
                            `;
                        }

                        let conductedDateStr = 'Yes';
                        if (req.event_conducted_at) {
                            conductedDateStr = req.event_conducted_at.replace('T', ' ').substring(0, 16);
                        }

                        auditDetails.innerHTML = `
                            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 0.75rem; font-size: 0.875rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem;">
                                    <strong>Conducted Status:</strong>
                                    <span>${req.is_event_conducted ? '<span style="color:#16a34a; font-weight:700;"><i class="ph-bold ph-check"></i> Conducted (' + escapeHtml(conductedDateStr) + ')</span>' : '<span style="color:#64748b;">Not confirmed yet</span>'}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem;">
                                    <strong>Total Attendees:</strong>
                                    <span style="font-weight:700; color: #0f172a;">${req.attendees_count !== null && req.attendees_count !== undefined ? req.attendees_count : '--'}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem;">
                                    <strong>First-Timers:</strong>
                                    <span style="font-weight:600; color: #475569;">${req.first_timers_count !== null && req.first_timers_count !== undefined ? req.first_timers_count : '--'}</span>
                                </div>
                                <div style="margin-top: 0.6rem; padding-top: 0.6rem; border-top: 1px dashed #e2e8f0;">
                                    <strong style="display: block; margin-bottom: 0.25rem;">Summary & Highlights:</strong>
                                    <p style="margin: 0; color: #475569; font-size: 0.85rem; line-height: 1.5;">${escapeHtml(req.report_summary || 'No summary submitted yet.')}</p>
                                </div>
                                <div style="margin-top: 0.6rem; padding-top: 0.6rem; border-top: 1px dashed #e2e8f0;">
                                    <strong style="display: block; margin-bottom: 0.25rem;">Photos / Attendance Sheet:</strong>
                                    <div>${proofDownloadHtml}</div>
                                </div>
                            </div>
                        `;
                    } else {
                        auditCard.style.display = 'none';
                    }

                    // Right Column: Render Recent Proofs & reset form
                    renderRecentProofs(res.data.updates || []);
                    document.getElementById('admin-timeline-form').reset();
                    document.getElementById('ctl-stage-select').value = String(currentStepNum <= 5 ? currentStepNum : 5);
                    document.getElementById('ctl-status-select').value = 'completed';
                    document.getElementById('ctl-file-name-display').style.display = 'none';
                    document.getElementById('ctl-proof-file').value = '';
                } else {
                    pipelineEl.innerHTML = `<p style="color: #ef4444; padding: 1rem;">Failed to load timeline.</p>`;
                }
            } catch (err) {
                console.error('Error fetching timeline:', err);
                pipelineEl.innerHTML = `<p style="color: #ef4444; padding: 1rem;">Network error fetching timeline.</p>`;
            }
        }

        function closeTimelineModal() {
            document.getElementById('timeline-modal').style.display = 'none';
        }

        function renderPipelineSteps(steps) {
            const pipelineEl = document.getElementById('tl-pipeline-container');
            if (!steps || steps.length === 0) {
                pipelineEl.innerHTML = '<p style="color: #64748b;">No milestones configured.</p>';
                return;
            }

            const stepDescriptions = {
                1: 'Communications, Marketing & Creative Team drafts and finalized official graphics.',
                2: 'Official publication on social media page with live engagement link.',
                3: 'Promotional teaser video clip produced and distributed.',
                4: 'Post-activity attendance, photo documentation, and accomplishment report.',
                5: 'Final event audit approved and collateral archived.'
            };

            pipelineEl.innerHTML = steps.map(s => {
                const isCompleted = s.status === 'completed';
                const isInProg = s.status === 'in_progress';
                const stepClass = isCompleted ? 'completed' : (isInProg ? 'in-progress' : 'pending');

                let badgeCls = 'pending';
                let badgeText = 'Pending';
                if (isCompleted) { badgeCls = 'completed'; badgeText = 'Completed'; }
                else if (isInProg) { badgeCls = 'in_progress'; badgeText = 'In Progress'; }

                const desc = s.notes || stepDescriptions[s.step_order] || 'Production stage';
                
                let proofPillHtml = '';
                if (s.proof_file_path) {
                    const fname = s.proof_file_path.split('/').pop();
                    proofPillHtml = `
                        <a href="${escapeHtml(s.proof_file_path)}" target="_blank" class="step-attachment-pill">
                            <i class="ph ph-file-arrow-down"></i> ${escapeHtml(fname)}
                        </a>
                    `;
                }

                return `
                    <div class="timeline-step ${stepClass}">
                        <div class="step-marker">
                            ${isCompleted ? '<i class="ph-bold ph-check"></i>' : s.step_order}
                        </div>
                        <div class="step-content">
                            <div class="step-header">
                                <span class="step-title font-roboto">${escapeHtml(s.step_title)}</span>
                                <span class="step-badge ${badgeCls}">${badgeText}</span>
                            </div>
                            <div class="step-subtext">
                                ${escapeHtml(desc)}
                            </div>
                            ${proofPillHtml}
                        </div>
                    </div>
                `;
            }).join('');
        }

        function renderRecentProofs(updates) {
            const listEl = document.getElementById('ctl-recent-proofs-list');
            const proofUpdates = (updates || []).filter(u => u.proof_file_path || u.update_type === 'proof_uploaded');
            document.getElementById('ctl-proofs-count').textContent = `${proofUpdates.length} attached`;

            if (proofUpdates.length === 0) {
                listEl.innerHTML = `
                    <div style="text-align: center; padding: 1rem; color: #94a3b8; font-size: 0.8rem;">
                        <i class="ph ph-folder-open" style="font-size: 1.5rem;"></i>
                        <p style="margin-top: 0.25rem;">No files uploaded yet.</p>
                    </div>`;
                return;
            }

            listEl.innerHTML = proofUpdates.map(u => {
                const path = u.proof_file_path || '';
                const fname = path.split('/').pop() || 'Proof_Attachment';
                const isImg = fname.match(/\.(jpg|jpeg|png|webp|gif)$/i);
                const isVid = fname.match(/\.(mp4|mov|avi)$/i);
                const icon = isImg ? 'ph-image' : (isVid ? 'ph-video-camera' : 'ph-file-text');
                const timeStr = u.created_at ? u.created_at.substring(0, 16).replace('T', ' ') : 'Recent';

                return `
                    <div class="recent-proof-item">
                        <div class="recent-proof-left">
                            <i class="ph ${icon}" style="font-size: 1.3rem; color: #2563eb;"></i>
                            <div>
                                <div class="proof-file-name" title="${escapeHtml(fname)}">${escapeHtml(fname)}</div>
                                <div style="font-size: 0.7rem; color: #64748b;">${escapeHtml(timeStr)}</div>
                            </div>
                        </div>
                        <a href="${escapeHtml(path)}" target="_blank" download class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                            <i class="ph ph-download-simple"></i>
                        </a>
                    </div>
                `;
            }).join('');
        }

        function handleProofFileChange(input) {
            if (input.files && input.files[0]) {
                const f = input.files[0];
                document.getElementById('ctl-file-name-text').textContent = `${f.name} (${(f.size / 1024 / 1024).toFixed(2)} MB)`;
                document.getElementById('ctl-file-name-display').style.display = 'block';
            }
        }

        function handleStageSelectChange() {
            const stg = document.getElementById('ctl-stage-select').value;
            // When selecting stage 1, check "Set as official poster" by default
            document.getElementById('ctl-is-final-poster').checked = (stg === '1');
        }

        function filterUrgentEvents() {
            switchTab('accepted', document.getElementById('tab-btn-accepted'));
            const urgentItems = acceptedCache.filter(p => p.report_timer && (p.report_timer.status === 'urgent' || p.report_timer.status === 'overdue'));
            renderAcceptedGrid(urgentItems);
        }

        async function submitApproveDocumentation() {
            if (!activeRequest) return;
            const btn = document.getElementById('btn-approve-audit');
            const note = prompt('Enter audit approval notes or remarks:', 'Event documentation and attendance verified. Event concluded successfully.');
            if (note === null) return;

            btn.disabled = true;
            btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Approving...';

            try {
                const res = await YTR_API.posterRequests.approveDocumentation(activeRequest.id, note);
                if (res.ok) {
                    alert('Event documentation approved! Milestone 4 & 5 marked as completed and event concluded.');
                    await openTimelineModal(activeRequest.id);
                    await loadMetrics();
                    await loadPosterData('accepted');
                    await loadPosterData('queue');
                } else {
                    alert('Approval failed: ' + (res.message || 'Error'));
                }
            } catch (err) {
                console.error('Documentation approval error:', err);
                alert('Network error communicating with API.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="ph-bold ph-check-circle"></i> Approve Documentation & Conclude Event';
            }
        }

        async function submitTimelineUpdate(e) {
            e.preventDefault();
            if (!activeRequest) return;

            const btn = document.getElementById('ctl-submit-btn');
            const stageOrder = document.getElementById('ctl-stage-select').value;
            const stageStatus = document.getElementById('ctl-status-select').value;
            const notes = document.getElementById('ctl-notes').value.trim();
            const isFinalPoster = document.getElementById('ctl-is-final-poster').checked;
            const fileInput = document.getElementById('ctl-proof-file');

            // Client-side Stage Completion Checks
            if (stageStatus === 'completed') {
                if (stageOrder === '1') {
                    const hasPoster = (fileInput.files && fileInput.files[0]) || activeRequest.final_poster_path;
                    if (!hasPoster) {
                        alert('Cannot complete Stage 1 (Poster Request) without uploading the official poster graphic file.');
                        return;
                    }
                } else if (stageOrder === '2') {
                    const hasProof = (fileInput.files && fileInput.files[0]) || notes;
                    if (!hasProof) {
                        alert('Cannot complete Stage 2 (Posted on Page) without uploading proof screenshot or providing social media link/notes.');
                        return;
                    }
                } else if (stageOrder === '4' || stageOrder === '5') {
                    if (activeRequest.documentation_status !== 'approved') {
                        alert('Cannot conclude timeline: When the event is concluded, the Chapter must report using the Report tab, and the report must be validated and approved by National Admin before the timeline can conclude. Please use "Validate Report & Conclude Timeline" above once the report is submitted.');
                        return;
                    }
                }
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Saving...';

            const formData = new FormData();
            formData.append('step_order', stageOrder);
            formData.append('step_status', stageStatus);
            formData.append('notes', notes);
            formData.append('is_final_poster', isFinalPoster ? '1' : '0');

            if (fileInput.files && fileInput.files[0]) {
                formData.append('proof_file', fileInput.files[0]);
            }

            try {
                const res = await YTR_API.posterRequests.updateTimelineStep(activeRequest.id, formData);
                if (res.ok) {
                    alert('Milestone and proof updated successfully!');
                    // Refresh current modal and grid caches
                    await openTimelineModal(activeRequest.id);
                    await loadMetrics();
                    await loadPosterData('accepted');
                    await loadPosterData('queue');
                } else {
                    alert('Update failed: ' + (res.message || 'Error'));
                }
            } catch (err) {
                console.error('Save timeline update error:', err);
                alert('Network error updating timeline.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="ph ph-floppy-disk"></i> Save Update';
            }
        }

        function downloadCurrentPoster() {
            if (!activeRequest) return;
            const src = activeRequest.final_poster_path || activeRequest.reference_poster_path || 'assets/images/background.jpg';
            triggerDownload(activeRequest.event_title, src);
        }

        function triggerDownload(title, path) {
            if (path) {
                const link = document.createElement('a');
                link.href = path;
                link.download = `Poster_${title.replace(/\s+/g, '_')}.jpg`;
                link.target = '_blank';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                alert(`Downloading promotional poster bundle for: "${title}". Packaging file...`);
            }
        }

        function openZoomImage(url) {
            window.open(url, '_blank');
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