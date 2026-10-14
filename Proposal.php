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
              <p class="metric-value font-roboto">18</p>
              <p class="metric-sub font-roboto">Across all chapters</p>
            </div>
          </div>
          <div class="metric-card metric-card-row">
            <div class="metric-icon amber"><i class="ph-fill ph-clock"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Pending Review</h5>
              <p class="metric-value font-roboto">5</p>
              <p class="metric-sub font-roboto">Awaiting National evaluation</p>
            </div>
          </div>
          <div class="metric-card metric-card-row">
            <div class="metric-icon green"><i class="ph-fill ph-check-circle"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Approved Proposals</h5>
              <p class="metric-value font-roboto">11</p>
              <p class="metric-sub font-roboto">Cleared for poster creation</p>
            </div>
          </div>
          <div class="metric-card metric-card-row">
            <div class="metric-icon rose"><i class="ph-fill ph-warning-circle"></i></div>
            <div>
              <h5 class="metric-title font-roboto">Needs Revision</h5>
              <p class="metric-value font-roboto">2</p>
              <p class="metric-sub font-roboto">Notes returned to chapters</p>
            </div>
          </div>
        </div>

        <!-- 2-COLUMN LAYOUT -->
        <div class="two-column-layout">
          
          <!-- LEFT COLUMN: CHAPTER PROPOSALS LIST -->
          <div class="layout-main">
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
                  <tbody>
                    <!-- Row 1: Laguna -->
                    <tr class="proposal-row" data-status="pending" data-chapter="Laguna Chapter">
                      <td>
                        <div class="td-user">
                          <div style="width: 36px; height: 36px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0;">
                            <i class="ph-fill ph-compass"></i>
                          </div>
                          <div>
                            <span class="proposal-name font-roboto" style="font-weight: bold; color: var(--primary); font-size: 0.875rem; display: block;">Annual Youth Leadership Camp</span>
                            <span class="td-sub font-roboto">Fellowship & Capacity Building</span>
                          </div>
                        </div>
                      </td>
                      <td><span class="badge chapter-admin font-roboto">Laguna Chapter</span></td>
                      <td>
                        <p class="font-roboto" style="font-size: 0.8rem; font-weight: bold; color: var(--text-main);">Nov 15-17, 2026</p>
                        <p class="td-sub font-roboto"><i class="ph ph-map-pin"></i> Los Baños Assembly Center</p>
                      </td>
                      <td style="text-align: center;">
                        <p class="font-roboto" style="font-weight: bold; font-size: 0.85rem;"><i class="ph-fill ph-users" style="color: #60a5fa;"></i> 120</p>
                        <p class="td-sub font-roboto">₱18,000</p>
                      </td>
                      <td style="text-align: center;"><span class="badge pending font-roboto">Pending Review</span></td>
                      <td style="text-align: center;">
                        <button class="btn btn-primary btn-sm font-roboto" style="margin: 0 auto;" onclick="selectProposal('Annual Youth Leadership Camp', 'Laguna Chapter', 'Nov 15-17, 2026', 'Los Baños Assembly Center, Laguna', '120 participants', '₱18,000.00', 'Youth Fellowship & Spiritual Alignment', 'Three-day immersive leadership and spiritual retreat focused on youth empowerment and discipleship across collegiate units.', 'pending')">
                          <i class="ph ph-eye"></i> Review
                        </button>
                      </td>
                    </tr>

                    <!-- Row 2: NCR -->
                    <tr class="proposal-row" data-status="approved" data-chapter="NCR Chapter">
                      <td>
                        <div class="td-user">
                          <div style="width: 36px; height: 36px; border-radius: 8px; background: #f0fdf4; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0;">
                            <i class="ph-fill ph-fire"></i>
                          </div>
                          <div>
                            <span class="proposal-name font-roboto" style="font-weight: bold; color: var(--primary); font-size: 0.875rem; display: block;">Ignite Metro Youth Convergence</span>
                            <span class="td-sub font-roboto">Regional Summit</span>
                          </div>
                        </div>
                      </td>
                      <td><span class="badge chapter-admin font-roboto">NCR Chapter</span></td>
                      <td>
                        <p class="font-roboto" style="font-size: 0.8rem; font-weight: bold; color: var(--text-main);">Dec 05, 2026</p>
                        <p class="td-sub font-roboto"><i class="ph ph-map-pin"></i> Quezon City Multi-Purpose Hall</p>
                      </td>
                      <td style="text-align: center;">
                        <p class="font-roboto" style="font-weight: bold; font-size: 0.85rem;"><i class="ph-fill ph-users" style="color: #60a5fa;"></i> 250</p>
                        <p class="td-sub font-roboto">₱35,000</p>
                      </td>
                      <td style="text-align: center;"><span class="badge chapter-admin font-roboto" style="background: #f0fdf4; color: #16a34a; border-color: #bbf7d0;">Approved</span></td>
                      <td style="text-align: center;">
                        <button class="btn btn-outline btn-sm font-roboto" style="margin: 0 auto;" onclick="selectProposal('Ignite Metro Youth Convergence', 'NCR Chapter', 'Dec 05, 2026', 'Quezon City Multi-Purpose Hall', '250 participants', '₱35,000.00', 'Metro Convergence & Worship Night', 'Mega youth gathering bringing together university and high school youth chapters across Metro Manila for worship and vision casting.', 'approved')">
                          <i class="ph ph-eye"></i> View
                        </button>
                      </td>
                    </tr>

                    <!-- Row 3: Cavite -->
                    <tr class="proposal-row" data-status="pending" data-chapter="Cavite Chapter">
                      <td>
                        <div class="td-user">
                          <div style="width: 36px; height: 36px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0;">
                            <i class="ph-fill ph-heart-straight"></i>
                          </div>
                          <div>
                            <span class="proposal-name font-roboto" style="font-weight: bold; color: var(--primary); font-size: 0.875rem; display: block;">Community Care & Feeding Mission</span>
                            <span class="td-sub font-roboto">Community Outreach</span>
                          </div>
                        </div>
                      </td>
                      <td><span class="badge chapter-admin font-roboto">Cavite Chapter</span></td>
                      <td>
                        <p class="font-roboto" style="font-size: 0.8rem; font-weight: bold; color: var(--text-main);">Dec 12, 2026</p>
                        <p class="td-sub font-roboto"><i class="ph ph-map-pin"></i> Tagaytay Covered Court</p>
                      </td>
                      <td style="text-align: center;">
                        <p class="font-roboto" style="font-weight: bold; font-size: 0.85rem;"><i class="ph-fill ph-users" style="color: #60a5fa;"></i> 90</p>
                        <p class="td-sub font-roboto">₱15,000</p>
                      </td>
                      <td style="text-align: center;"><span class="badge pending font-roboto">Pending Review</span></td>
                      <td style="text-align: center;">
                        <button class="btn btn-primary btn-sm font-roboto" style="margin: 0 auto;" onclick="selectProposal('Community Care & Feeding Mission', 'Cavite Chapter', 'Dec 12, 2026', 'Tagaytay Covered Court, Cavite', '90 participants', '₱15,000.00', 'Social Responsibility Outreach', 'Outreach program providing warm meals, hygiene kits, and basic educational materials to underprivileged children.', 'pending')">
                          <i class="ph ph-eye"></i> Review
                        </button>
                      </td>
                    </tr>

                    <!-- Row 4: Pampanga -->
                    <tr class="proposal-row" data-status="revision" data-chapter="Pampanga Chapter">
                      <td>
                        <div class="td-user">
                          <div style="width: 36px; height: 36px; border-radius: 8px; background: #fff1f2; color: #f43f5e; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0;">
                            <i class="ph-fill ph-music-notes"></i>
                          </div>
                          <div>
                            <span class="proposal-name font-roboto" style="font-weight: bold; color: var(--primary); font-size: 0.875rem; display: block;">Pampanga Praise & Worship Night</span>
                            <span class="td-sub font-roboto">Music & Worship Night</span>
                          </div>
                        </div>
                      </td>
                      <td><span class="badge chapter-admin font-roboto">Pampanga Chapter</span></td>
                      <td>
                        <p class="font-roboto" style="font-size: 0.8rem; font-weight: bold; color: var(--text-main);">Dec 18, 2026</p>
                        <p class="td-sub font-roboto"><i class="ph ph-map-pin"></i> San Fernando Gym</p>
                      </td>
                      <td style="text-align: center;">
                        <p class="font-roboto" style="font-weight: bold; font-size: 0.85rem;"><i class="ph-fill ph-users" style="color: #60a5fa;"></i> 180</p>
                        <p class="td-sub font-roboto">₱28,000</p>
                      </td>
                      <td style="text-align: center;"><span class="badge pending font-roboto" style="background: #fff1f2; color: #e11d48; border-color: #fecdd3;">Needs Revision</span></td>
                      <td style="text-align: center;">
                        <button class="btn btn-outline btn-sm font-roboto" style="margin: 0 auto;" onclick="selectProposal('Pampanga Praise & Worship Night', 'Pampanga Chapter', 'Dec 18, 2026', 'San Fernando Gym, Pampanga', '180 participants', '₱28,000.00', 'Music Ministry Concert', 'Concert and worship night featuring regional youth band teams. Needs itemized breakdown for sound rental.', 'revision')">
                          <i class="ph ph-eye"></i> Review
                        </button>
                      </td>
                    </tr>

                    <!-- Row 5: Batangas -->
                    <tr class="proposal-row" data-status="approved" data-chapter="Batangas Chapter">
                      <td>
                        <div class="td-user">
                          <div style="width: 36px; height: 36px; border-radius: 8px; background: #f0fdf4; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0;">
                            <i class="ph-fill ph-graduation-cap"></i>
                          </div>
                          <div>
                            <span class="proposal-name font-roboto" style="font-weight: bold; color: var(--primary); font-size: 0.875rem; display: block;">Campus Discipleship Forum</span>
                            <span class="td-sub font-roboto">Campus Ministry</span>
                          </div>
                        </div>
                      </td>
                      <td><span class="badge chapter-admin font-roboto">Batangas Chapter</span></td>
                      <td>
                        <p class="font-roboto" style="font-size: 0.8rem; font-weight: bold; color: var(--text-main);">Dec 22, 2026</p>
                        <p class="td-sub font-roboto"><i class="ph ph-map-pin"></i> BSU Amphitheater</p>
                      </td>
                      <td style="text-align: center;">
                        <p class="font-roboto" style="font-weight: bold; font-size: 0.85rem;"><i class="ph-fill ph-users" style="color: #60a5fa;"></i> 140</p>
                        <p class="td-sub font-roboto">₱12,500</p>
                      </td>
                      <td style="text-align: center;"><span class="badge chapter-admin font-roboto" style="background: #f0fdf4; color: #16a34a; border-color: #bbf7d0;">Approved</span></td>
                      <td style="text-align: center;">
                        <button class="btn btn-outline btn-sm font-roboto" style="margin: 0 auto;" onclick="selectProposal('Campus Discipleship Forum', 'Batangas Chapter', 'Dec 22, 2026', 'BSU Amphitheater, Batangas City', '140 participants', '₱12,500.00', 'University Leadership Orientation', 'Campus ambassadors forum teaching youth life skills, mental health resilience, and faith-based values.', 'approved')">
                          <i class="ph ph-eye"></i> View
                        </button>
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
                  <div class="review-detail-group">
                    <label class="font-roboto"><i class="ph-fill ph-calendar-blank"></i> Proposed Schedule & Venue</label>
                    <p id="detailProposalSchedule" class="font-roboto" style="font-size: 0.75rem; color: var(--text-main);">-</p>
                  </div>

                  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; background: #f8fafc; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border); margin: 0.25rem 0;">
                    <div>
                      <span style="font-size: 0.65rem; color: var(--text-muted); display: block;">Turnout</span>
                      <strong id="detailProposalTurnout" style="color: var(--primary); font-size: 0.95rem;">-</strong>
                    </div>
                    <div>
                      <span style="font-size: 0.65rem; color: var(--text-muted); display: block;">Est. Budget</span>
                      <strong id="detailProposalBudget" style="color: #2563eb; font-size: 0.95rem;">-</strong>
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
              <input type="text" class="form-input font-roboto" placeholder="e.g. Regional Youth Leadership Camp" required>
            </div>
            <div class="form-group">
              <label class="form-label font-roboto">Host Chapter <span style="color: #f43f5e;">*</span></label>
              <select class="form-select font-roboto" required>
                <option value="" disabled selected>Select Chapter</option>
                <option value="Laguna Chapter">Laguna Chapter</option>
                <option value="NCR Chapter">NCR Chapter</option>
                <option value="Cavite Chapter">Cavite Chapter</option>
                <option value="Pampanga Chapter">Pampanga Chapter</option>
                <option value="Batangas Chapter">Batangas Chapter</option>
              </select>
            </div>
          </div>

          <div class="form-grid-2">
            <div class="form-group">
              <label class="form-label font-roboto">Event Category <span style="color: #f43f5e;">*</span></label>
              <select class="form-select font-roboto" required>
                <option value="youth_camp">Youth Camp & Fellowship</option>
                <option value="leadership">Leadership Conference</option>
                <option value="community_outreach">Community Outreach</option>
                <option value="anniversary">Anniversary Celebration</option>
                <option value="other">Other Activity</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label font-roboto">Expected Turnout <span style="color: #f43f5e;">*</span></label>
              <input type="number" class="form-input font-roboto" placeholder="e.g. 150" min="1" required>
            </div>
          </div>

          <div class="form-grid-2">
            <div class="form-group">
              <label class="form-label font-roboto">Proposed Start Date <span style="color: #f43f5e;">*</span></label>
              <input type="date" class="form-input font-roboto" required>
            </div>
            <div class="form-group">
              <label class="form-label font-roboto">Proposed End Date <span style="color: #f43f5e;">*</span></label>
              <input type="date" class="form-input font-roboto" required>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label font-roboto">Proposed Venue / Address <span style="color: #f43f5e;">*</span></label>
            <input type="text" class="form-input font-roboto" placeholder="e.g. YTR Assembly Center, Los Baños" required>
          </div>

          <div class="form-grid-2">
            <div class="form-group">
              <label class="form-label font-roboto">Estimated Budget (PHP)</label>
              <input type="number" class="form-input font-roboto" placeholder="e.g. 20000" min="0">
            </div>
            <div class="form-group">
              <label class="form-label font-roboto">Contact Coordinator</label>
              <input type="text" class="form-input font-roboto" placeholder="e.g. Rayvhen P. Muñoz">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label font-roboto">Objectives & Description <span style="color: #f43f5e;">*</span></label>
            <textarea class="form-textarea font-roboto" rows="3" placeholder="State the goals and expected outcomes of the event..." required></textarea>
          </div>

          <div class="form-group">
            <label class="form-label font-roboto">Attach Proposal Document (.PDF, .DOCX)</label>
            <input type="file" class="form-input font-roboto" accept=".pdf,.doc,.docx">
          </div>

          <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem;">
            <button type="button" class="btn btn-outline font-roboto" onclick="closeProposalModal()">Cancel</button>
            <button type="submit" class="btn btn-primary font-roboto"><i class="ph ph-paper-plane-tilt"></i> Submit Proposal</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Interactive JavaScript Logic -->
  <script>
    // Select proposal to review in sidebar
    function selectProposal(title, chapter, date, venue, turnout, budget, theme, notes, status) {
      document.getElementById('emptyProposalState').style.display = 'none';
      document.getElementById('activeProposalContent').style.display = 'block';

      document.getElementById('detailProposalTitle').textContent = title;
      document.getElementById('detailProposalChapter').textContent = chapter;
      document.getElementById('detailProposalSchedule').textContent = date + ' • ' + venue;
      document.getElementById('detailProposalTurnout').textContent = turnout;
      document.getElementById('detailProposalBudget').textContent = budget;
      document.getElementById('detailProposalNotes').textContent = notes;
    }

    // Filter proposals by status tab
    function filterProposals(status, clickedBtn) {
      document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
      clickedBtn.classList.add('active');

      const rows = document.querySelectorAll('.proposal-row');
      rows.forEach(row => {
        if (status === 'all' || row.dataset.status === status) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    // Filter proposals by Chapter dropdown
    function filterByChapter(chapter) {
      const rows = document.querySelectorAll('.proposal-row');
      rows.forEach(row => {
        if (chapter === 'all' || row.dataset.chapter === chapter) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    // Live search proposals
    function searchProposals() {
      const query = document.getElementById('proposalSearchInput').value.toLowerCase();
      const rows = document.querySelectorAll('.proposal-row');
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
      });
    }

    // Modal controls
    function openProposalModal() {
      document.getElementById('proposalModal').classList.add('open');
    }
    function closeProposalModal() {
      document.getElementById('proposalModal').classList.remove('open');
    }

    function handleModalProposalSubmit(e) {
      e.preventDefault();
      alert('Event proposal has been submitted successfully and added to the review queue!');
      closeProposalModal();
      e.target.reset();
    }
  </script>
</body>
</html>
