<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>YTR | Chapter Event Proposals</title>
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <link rel="stylesheet" href="assets/dashboard.css">
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
          <button class="btn btn-primary font-roboto" onclick="openProposalModal()">
            <i class="ph ph-plus"></i> Submit Event Proposal
          </button>
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

        <!-- 2-COLUMN LAYOUT -->
        <div class="two-column-layout">
          
          <!-- LEFT COLUMN: CHAPTER PROPOSALS LIST -->
          <div class="layout-main">
            
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
                        <br>Loading chapter proposals from Python backend...
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- RIGHT COLUMN: PROPOSAL REVIEW & DETAILS PANEL -->
          <div class="layout-sidebar">
            <h2 class="review-panel-title font-roboto">Proposal Evaluation</h2>

            <div id="proposalDetailCard" class="review-card">
              <!-- Empty state before click -->
              <div id="emptyProposalState" style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
                <div style="width: 52px; height: 52px; border-radius: 50%; background: #eff6ff; color: #2563eb; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 0.85rem;">
                  <i class="ph ph-file-text"></i>
                </div>
                <h4 class="font-roboto" style="color: var(--primary); font-size: 0.95rem; font-weight: bold; margin-bottom: 0.35rem;">Select a Proposal</h4>
                <p style="font-size: 0.75rem; line-height: 1.4;">Click <strong>Review</strong> or <strong>View</strong> on any chapter proposal to inspect schedule, budget, objectives, and submit approval decisions.</p>
              </div>

              <!-- Populated State -->
              <div id="activeProposalContent" style="display: none;">
                <div class="review-header">
                  <div class="review-header-icon"><i class="ph-fill ph-file-plus"></i></div>
                  <div class="review-header-text">
                    <h4 id="detailProposalTitle" class="font-roboto">Annual Youth Leadership Camp</h4>
                    <p id="detailProposalChapter" class="font-roboto">Laguna Chapter</p>
                  </div>
                </div>

                <div class="review-details">
                  <!-- Badges Container (Due status, Timer upon approval) -->
                  <div id="detailProposalBadges" style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.25rem;"></div>

                  <div class="review-detail-group">
                    <label class="font-roboto"><i class="ph-fill ph-calendar-blank"></i> Proposed Schedule & Venue</label>
                    <p id="detailProposalSchedule" class="font-roboto" style="font-size: 0.8rem; color: #0f172a; font-weight: bold;">-</p>
                    <p id="detailApprovalTimer" style="font-size: 0.75rem; color: #166534; font-weight: 700; margin-top: 2px;"></p>
                  </div>

                  <!-- Threshold Cards: Target Turnout & Budget -->
                  <div style="display: flex; flex-direction: column; gap: 0.5rem; margin: 0.25rem 0;">
                    <div style="background: #f8fafc; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border);">
                      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                        <span style="font-size: 0.7rem; color: #334155; font-weight: bold;">Attendance Threshold</span>
                        <strong id="detailProposalTurnout" style="color: #1a2f63; font-size: 0.85rem;">100 Target</strong>
                      </div>
                      <div class="threshold-bar">
                        <div id="detailTurnoutFill" class="threshold-fill met" style="width: 100%;"></div>
                      </div>
                    </div>

                    <div style="background: #f8fafc; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border);">
                      <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.7rem; color: #334155; font-weight: bold;">Budget Threshold</span>
                        <span id="detailProposalBudget" class="badge threshold-met font-roboto">₱0 / ₱25,000 Cap</span>
                      </div>
                    </div>
                  </div>

                  <div class="review-detail-group">
                    <label class="font-roboto"><i class="ph-fill ph-article"></i> Objectives & Description</label>
                    <div id="detailProposalNotes" class="reason-box font-roboto" style="font-size: 0.75rem; max-height: 120px; overflow-y: auto;">
                      -
                    </div>
                  </div>

                  <div class="review-detail-group">
                    <label class="font-roboto"><i class="ph-fill ph-paperclip"></i> Attached Proposal Document</label>
                    <a href="#" onclick="alert('Proposal PDF file download started.'); return false;" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: #2563eb; text-decoration: none; font-weight: bold;">
                      <i class="ph ph-file-pdf" style="font-size: 1.15rem; color: #ef4444;"></i> Download Event_Proposal_2026.pdf
                    </a>
                  </div>

                  <!-- Decision Actions -->
                  <div id="proposalActionButtons" style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                      <button class="btn btn-outline-danger font-roboto" onclick="alert('Revision feedback request has been sent to the chapter coordinator.');">
                        <i class="ph ph-pencil-simple"></i> Request Revision
                      </button>
                      <button class="btn btn-primary font-roboto" onclick="alert('Proposal has been officially approved! Poster creation request created.');">
                        <i class="ph ph-check"></i> Approve
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Guidelines Card -->
            <div class="review-card" style="margin-top: 1rem;">
              <div class="review-header">
                <div class="review-header-icon" style="background: #f0fdf4; color: #16a34a;"><i class="ph-fill ph-shield-check"></i></div>
                <div class="review-header-text">
                  <h4 class="font-roboto">National Review Criteria</h4>
                  <p>Standard evaluation policy</p>
                </div>
              </div>
              <div style="font-size: 0.75rem; color: var(--text-muted); line-height: 1.5; display: flex; flex-direction: column; gap: 0.5rem;">
                <p>✓ Minimum 3 weeks lead time prior to date.</p>
                <p>✓ Complete itemized budget calculation.</p>
                <p>✓ Verified chapter leadership endorsement.</p>
              </div>
            </div>

          </div>

        </div>

      </main>
    </div>
  </div>

  <!-- MODAL: SUBMIT NEW PROPOSAL -->
  <div id="proposalModal" class="modal-overlay">
    <div class="modal-box">
      <div class="modal-header">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
          <div style="width: 36px; height: 36px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
            <i class="ph ph-file-plus"></i>
          </div>
          <h3 class="font-roboto" style="font-size: 1.1rem; color: var(--primary); font-weight: bold;">Submit Chapter Event Proposal</h3>
        </div>
        <button onclick="closeProposalModal()" style="background: none; border: none; font-size: 1.25rem; color: var(--text-muted); cursor: pointer;"><i class="ph ph-x"></i></button>
      </div>

      <div class="modal-body">
        <form id="newProposalModalForm" onsubmit="handleModalProposalSubmit(event)">
          <div class="form-grid-2">
            <div class="form-group">
              <label class="form-label font-roboto">Event Title <span style="color: #f43f5e;">*</span></label>
              <input type="text" id="formPropTitle" class="form-input font-roboto" placeholder="e.g. Regional Youth Leadership Camp" required>
            </div>
            <div class="form-group">
              <label class="form-label font-roboto">Host Chapter <span style="color: #f43f5e;">*</span></label>
              <select id="formPropChapter" class="form-select font-roboto" required>
                <option value="" disabled selected>Select Chapter</option>
              </select>
            </div>
          </div>

          <div class="form-grid-2">
            <div class="form-group">
              <label class="form-label font-roboto">Event Category <span style="color: #f43f5e;">*</span></label>
              <select id="formPropCategory" class="form-select font-roboto" required>
                <option value="chapter">Youth Camp & Fellowship</option>
                <option value="national">Leadership Conference</option>
                <option value="community">Community Outreach</option>
                <option value="other">Other Activity</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label font-roboto">Expected Turnout</label>
              <input type="number" id="formPropTurnout" class="form-input font-roboto" placeholder="e.g. 150" min="1">
            </div>
          </div>

          <div class="form-grid-2">
            <div class="form-group">
              <label class="form-label font-roboto">Proposed Start Date <span style="color: #f43f5e;">*</span></label>
              <input type="date" id="formPropStartDate" class="form-input font-roboto" required>
            </div>
            <div class="form-group">
              <label class="form-label font-roboto">Proposed End Date</label>
              <input type="date" id="formPropEndDate" class="form-input font-roboto">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label font-roboto">Proposed Venue / Address <span style="color: #f43f5e;">*</span></label>
            <input type="text" id="formPropVenue" class="form-input font-roboto" placeholder="e.g. YTR Assembly Center, Los Baños" required>
          </div>

          <div class="form-grid-2">
            <div class="form-group">
              <label class="form-label font-roboto">Estimated Budget (PHP)</label>
              <input type="number" id="formPropBudget" class="form-input font-roboto" placeholder="e.g. 20000" min="0">
            </div>
            <div class="form-group">
              <label class="form-label font-roboto">Milestone Due Date (Lead Time Deadline)</label>
              <input type="date" id="formPropDueDate" class="form-input font-roboto">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label font-roboto">Contact Coordinator</label>
            <input type="text" id="formPropCoordinator" class="form-input font-roboto" placeholder="e.g. Rayvhen P. Muñoz">
          </div>

          <div class="form-group">
            <label class="form-label font-roboto">Objectives & Description <span style="color: #f43f5e;">*</span></label>
            <textarea id="formPropDescription" class="form-textarea font-roboto" rows="3" placeholder="State the goals and expected outcomes of the event..." required></textarea>
          </div>

          <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem;">
            <button type="button" class="btn btn-outline font-roboto" onclick="closeProposalModal()">Cancel</button>
            <button type="submit" id="btnSubmitProposal" class="btn btn-primary font-roboto"><i class="ph ph-paper-plane-tilt"></i> Submit Proposal</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="assets/api-client.js"></script>
  <!-- Interactive JavaScript Logic -->
  <script>
    let activeProposalId = null;
    let currentStatus = 'all';
    let currentChapter = 'all';

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

    // Load Proposals list
    async function loadProposals() {
      const tbody = document.getElementById('proposalsTableBody');
      const search = document.getElementById('proposalSearchInput').value.trim();

      const res = await YTR_API.proposals.list(currentStatus, currentChapter, search);
      if (res.status === 'success' && Array.isArray(res.data)) {
        if (res.data.length === 0) {
          tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748b;">No proposals match the selected filters.</td></tr>';
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
            const badgeClass = p.due_badge.type === 'overdue' ? 'badge overdue' : (p.due_badge.type === 'due_soon' ? 'badge due-soon' : 'badge timer');
            const icon = p.due_badge.type === 'overdue' ? 'ph-warning-octagon' : 'ph-clock';
            dueBadgeHtml = `<span class="${badgeClass} font-roboto" style="margin-top: 4px; display: inline-flex;"><i class="ph ${icon}"></i> ${p.due_badge.text}</span>`;
          }

          // Approval Timer Badge
          let timerBadgeHtml = '';
          if (p.approval_timer) {
            timerBadgeHtml = `<span class="badge timer font-roboto" style="margin-top: 4px; display: inline-flex;"><i class="ph ph-hourglass-high"></i> ${p.approval_timer}</span>`;
          }

          const startDate = p.start_datetime ? p.start_datetime.substring(0, 10) : 'TBD';
          const budgetFormatted = Number(p.budget_amount || 0).toLocaleString();
          const targetTurnout = p.target_turnout || 100;

          row.className = 'proposal-row';
          row.innerHTML = `
            <td>
              <div class="td-user">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0;">
                  <i class="ph-fill ph-compass"></i>
                </div>
                <div>
                  <span class="proposal-name font-roboto" style="font-weight: 800; color: #0f172a; font-size: 0.9rem; display: block;">${p.title}</span>
                  <span class="td-sub font-roboto" style="font-weight: 600; color: #2563eb;">${p.event_type.toUpperCase()} EVENT</span>
                  <div>${timerBadgeHtml} ${dueBadgeHtml}</div>
                </div>
              </div>
            </td>
            <td><span class="badge chapter-admin font-roboto">${p.chapter_name || 'All Chapters'}</span></td>
            <td>
              <p class="font-roboto" style="font-size: 0.825rem; font-weight: bold; color: #0f172a;">${startDate}</p>
              <p class="td-sub font-roboto"><i class="ph ph-map-pin"></i> ${p.venue || 'TBD'}</p>
            </td>
            <td style="text-align: center;">
              <p class="font-roboto" style="font-weight: bold; font-size: 0.85rem; color: #0f172a;"><i class="ph-fill ph-users" style="color: #60a5fa;"></i> ${targetTurnout} Target</p>
              <p class="td-sub font-roboto">₱${budgetFormatted}</p>
            </td>
            <td style="text-align: center;">${statusBadge}</td>
            <td style="text-align: center;">
              <button class="btn btn-primary btn-sm font-roboto" style="margin: 0 auto;" onclick='selectProposalRecord(${JSON.stringify(p)})'>
                <i class="ph ph-eye"></i> ${isPending ? 'Review' : 'View'}
              </button>
            </td>
          `;
          tbody.appendChild(row);

          if (idx === 0 && !activeProposalId) {
            selectProposalRecord(p);
          }
        });
      }
    }

    // Select proposal to review in sidebar
    function selectProposalRecord(p) {
      activeProposalId = p.id;
      document.getElementById('emptyProposalState').style.display = 'none';
      document.getElementById('activeProposalContent').style.display = 'block';

      document.getElementById('detailProposalTitle').textContent = p.title;
      document.getElementById('detailProposalChapter').textContent = p.chapter_name || 'National HQ';
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
        note = prompt('Please specify the revision notes for the chapter coordinator:');
        if (note === null) return;
      }

      const res = await YTR_API.proposals.review(activeProposalId, status, note);
      if (res.status === 'success') {
        alert(status === 'approved' ? 'Proposal approved successfully! Approval timer and poster creation activated.' : 'Revision request sent to chapter.');
        loadProposalMetrics();
        loadProposals();
      } else {
        alert(res.message || 'Error updating proposal.');
      }
    }

    // Modal controls
    function openProposalModal() {
      document.getElementById('proposalModal').classList.add('open');
    }
    function closeProposalModal() {
      document.getElementById('proposalModal').classList.remove('open');
    }

    // Submit New Proposal to Python API
    async function handleModalProposalSubmit(e) {
      e.preventDefault();
      const submitBtn = document.getElementById('btnSubmitProposal');
      submitBtn.disabled = true;

      const payload = {
        title: document.getElementById('formPropTitle').value.trim(),
        chapter_id: document.getElementById('formPropChapter').value,
        event_type: document.getElementById('formPropCategory').value,
        start_date: document.getElementById('formPropStartDate').value,
        end_date: document.getElementById('formPropEndDate').value,
        venue: document.getElementById('formPropVenue').value.trim(),
        target_turnout: document.getElementById('formPropTurnout').value || 100,
        budget_amount: document.getElementById('formPropBudget').value || 0,
        due_date: document.getElementById('formPropDueDate').value || document.getElementById('formPropStartDate').value,
        description: document.getElementById('formPropDescription').value.trim()
      };

      try {
        const res = await YTR_API.proposals.create(payload);
        if (res.status === 'success') {
          alert('Event proposal has been submitted successfully with threshold milestones!');
          closeProposalModal();
          e.target.reset();
          loadProposalMetrics();
          loadProposals();
        } else {
          alert(res.message || 'Error submitting event proposal.');
        }
      } catch (err) {
        alert('Could not submit proposal. Make sure the Python backend is running at http://127.0.0.1:5000.');
      } finally {
        submitBtn.disabled = false;
      }
    }

    // Wire action buttons in sidebar
    window.addEventListener('DOMContentLoaded', () => {
      loadProposalMetrics();
      loadChapters();
      loadProposals();

      // Hook up sidebar approve / revision buttons
      const actionBox = document.getElementById('proposalActionButtons');
      if (actionBox) {
        actionBox.innerHTML = `
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
            <button class="btn btn-outline-danger font-roboto" onclick="handleReviewProposal('draft')">
              <i class="ph ph-pencil-simple"></i> Request Revision
            </button>
            <button class="btn btn-primary font-roboto" onclick="handleReviewProposal('approved')">
              <i class="ph ph-check"></i> Approve
            </button>
          </div>
        `;
      }
    });
  </script>
</body>
</html>
