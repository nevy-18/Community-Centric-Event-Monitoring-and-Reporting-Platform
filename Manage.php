<?php include 'includes/auth_guard.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>YTR | Manage Accounts</title>
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <link rel="stylesheet" href="assets/National.css">
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
            <div class="header-icon"><i class="ph-fill ph-user"></i></div>
            <div>
              <h1 class="font-roboto">Manage Account</h1>
              <p>Review account requests, assign roles, and manage system access.</p>
            </div>
          </div>
          <div style="display: flex; gap: 0.5rem; align-items: center;">
            <button type="button" id="btnHeaderRetrieve" class="btn btn-outline font-roboto" onclick="openSystemFlushModal('archives')" style="display: none; border-color: #a7f3d0; color: #047857; background: #ecfdf5; font-weight: 600; padding: 0.55rem 1.15rem; border-radius: 8px; cursor: pointer; align-items: center; gap: 0.5rem; transition: all 0.2s;">
              <i class="ph-bold ph-clock-counter-clockwise" style="font-size: 1.1rem; color: #059669;"></i>
              <span>Retrieve Flushed Data</span>
            </button>
            <button type="button" id="btnHeaderFlush" class="btn btn-outline font-roboto" onclick="openSystemFlushModal('flush')" style="display: none; border-color: #fca5a5; color: #b91c1c; background: #fff5f5; font-weight: 600; padding: 0.55rem 1.15rem; border-radius: 8px; cursor: pointer; align-items: center; gap: 0.5rem; transition: all 0.2s;">
              <i class="ph-bold ph-trash" style="font-size: 1.1rem; color: #dc2626;"></i>
              <span>Flush System Data</span>
            </button>
          </div>
        </div>

        <!-- 2-Column Responsive Layout -->
        <div class="two-column-layout">
          
          <!-- LEFT COLUMN (Metrics & Table) -->
          <div class="layout-main">
            
            <!-- Metrics -->
            <div class="metrics-grid">
              <div class="metric-card metric-card-row">
                <div class="metric-icon blue"><i class="ph-fill ph-users"></i></div>
                <div>
                  <h5 class="metric-title font-roboto">Total Accounts</h5>
                  <p class="metric-value font-roboto" id="metricTotal">...</p>
                  <p class="metric-sub font-roboto">Registered across all tiers</p>
                </div>
              </div>
              <div class="metric-card metric-card-row">
                <div class="metric-icon amber"><i class="ph-fill ph-clock"></i></div>
                <div>
                  <h5 class="metric-title font-roboto">Pending Request</h5>
                  <p class="metric-value font-roboto" id="metricPending">...</p>
                  <p class="metric-sub font-roboto">Awaiting administrator review</p>
                </div>
              </div>
              <div class="metric-card metric-card-row">
                <div class="metric-icon green"><i class="ph-fill ph-shield-check"></i></div>
                <div>
                  <h5 class="metric-title font-roboto">National Admin</h5>
                  <p class="metric-value font-roboto" id="metricNational">...</p>
                  <p class="metric-sub font-roboto">With full system access</p>
                </div>
              </div>
              <div class="metric-card metric-card-row">
                <div class="metric-icon purple"><i class="ph-fill ph-user-gear"></i></div>
                <div>
                  <h5 class="metric-title font-roboto">Chapter Admins</h5>
                  <p class="metric-value font-roboto" id="metricChapter">...</p>
                  <p class="metric-sub font-roboto">Manage assigned chapters</p>
                </div>
              </div>
            </div>

            <!-- Table Section -->
            <div class="table-section">
              
              <!-- Tabs -->
              <div class="tabs-container font-roboto">
                <button class="tab-btn" onclick="switchAccountTab('all', this)"><i class="ph ph-users"></i> All Accounts</button>
                <button class="tab-btn active" onclick="switchAccountTab('pending', this)"><i class="ph ph-clock"></i> Pending Requests</button>
                <button class="tab-btn" onclick="switchAccountTab('password_resets', this)" id="tabPasswordResets">
                  <i class="ph-bold ph-key"></i> Password Resets
                  <span class="badge rose font-roboto" id="badgeResetCount" style="margin-left: 6px; font-size: 0.72rem; padding: 2px 7px; display: none;">0</span>
                </button>
                <button class="tab-btn" onclick="switchAccountTab('national_admins', this)"><i class="ph ph-shield-check"></i> National Admins</button>
                <button class="tab-btn" onclick="switchAccountTab('chapter_admins', this)"><i class="ph ph-user-gear"></i> Chapter Admins</button>
                <button class="tab-btn" onclick="switchAccountTab('chapter_directors', this)"><i class="ph ph-buildings"></i> Chapter Regional Directors</button>
                <button class="tab-btn" onclick="switchAccountTab('disabled', this)"><i class="ph ph-prohibit"></i> Disabled</button>
              </div>

              <!-- Data Table -->
              <div class="table-responsive">
                <table class="font-roboto">
                  <thead id="accountsTableHead">
                    <tr>
                      <th>Applicant</th>
                      <th>Email</th>
                      <th>Chapter</th>
                      <th style="text-align: center;">Requested Role</th>
                      <th>Date Requested</th>
                      <th style="text-align: center;">Status</th>
                      <th style="text-align: center;">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="accountsTableBody">
                    <tr>
                      <td colspan="7" style="text-align: center; padding: 2rem; color: #64748b;">
                        Loading accounts from Python backend...
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- RIGHT COLUMN (Review Panel) -->
          <div class="layout-sidebar">
            <h2 class="review-panel-title font-roboto">Review</h2>

            <!-- Card 1: Account Access Request -->
            <div class="review-card">
              <div class="review-header">
                <div class="review-header-icon"><i class="ph-fill ph-user"></i></div>
                <div class="review-header-text">
                  <h4 class="font-roboto">Account Access Request</h4>
                  <p>Review the details and approve or reject this account request.</p>
                </div>
              </div>

              <div class="review-user-info">
                <div class="review-user-profile">
                  <img src="https://placehold.co/40x40/e2e8f0/64748b" alt="User Avatar">
                  <div>
                    <h5 class="font-roboto" id="reviewUserName">Select an account</h5>
                    <p id="reviewUserEmail">Click "Review" on any account</p>
                  </div>
                </div>
                <span class="badge pending font-roboto" id="reviewUserBadge">--</span>
              </div>

              <div class="review-details">
                <div class="review-detail-group">
                  <label class="font-roboto"><i class="ph-fill ph-flag"></i> Chapter</label>
                  <p class="font-roboto" id="reviewUserChapter">--</p>
                </div>
                <div class="review-detail-group">
                  <label class="font-roboto"><i class="ph-fill ph-user-circle"></i> Requested Role</label>
                  <p class="font-roboto" id="reviewUserRole">--</p>
                </div>
                <div class="review-detail-group">
                  <label class="font-roboto"><i class="ph-fill ph-file-text"></i> Reason for Request</label>
                  <div class="reason-box font-roboto" id="reviewUserReason">
                    Select a user from the table to view their account details, assign role, and review access.
                  </div>
                </div>
              </div>
            </div>

            <!-- Card 2: Access & Role -->
            <div class="review-card">
              <div class="review-header">
                <div class="review-header-icon"><i class="ph-fill ph-shield-check"></i></div>
                <div class="review-header-text">
                  <h4 class="font-roboto">Access & Role</h4>
                  <p>Manage user permissions, security clearance level, and account access controls.</p>
                </div>
              </div>

              <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                  <label class="form-label font-roboto" style="margin-bottom: 0;">Assign Role &amp; Tier</label>
                  <div style="display: flex; align-items: center; gap: 0.35rem;">
                    <span id="roleRequestedBadge" style="display: none; font-size: 0.7rem; padding: 2px 7px; border-radius: 4px; font-weight: 600;"></span>
                    <a href="#" id="linkReassignTier" onclick="toggleRoleTierOverride(event)" style="display: none; font-size: 0.72rem; color: #2563eb; text-decoration: underline; font-weight: 600;">Change Tier</a>
                  </div>
                </div>
                <div class="radio-group font-roboto">
                  <label class="radio-label" id="labelRoleNational">
                    <input type="radio" name="assign_role" id="roleNational" value="national_admin" onchange="syncRolePositionOptions('national_admin')"> National Admin
                  </label>
                  <label class="radio-label" id="labelRoleChapter">
                    <input type="radio" name="assign_role" id="roleChapter" value="chapter_admin" checked onchange="syncRolePositionOptions('chapter_admin')"> Chapter Admin
                  </label>
                </div>
              </div>

              <div class="form-group">
                <label class="form-label font-roboto">Specific Position / Title</label>
                <select class="form-select font-roboto" id="assignPositionSelect" onchange="handlePositionSelectChange()">
                  <optgroup label="── CHAPTER POSITIONS ──" id="optGrpChapter">
                    <option value="Chapter Coordinator">Chapter Coordinator</option>
                    <option value="Chapter President">Chapter President</option>
                    <option value="Regional Director (Chapter)">Regional Director (Chapter)</option>
                  </optgroup>
                  <optgroup label="── NATIONAL POSITIONS ──" id="optGrpNational">
                    <option value="Chief Executive Operation">Chief Executive Operation</option>
                    <option value="Executive Director">Executive Director</option>
                    <option value="Regional Director (National)">Regional Director (National)</option>
                    <option value="National Youth Officer">National Youth Officer</option>
                  </optgroup>
                </select>
              </div>

              <div class="form-group" id="assignChapterGroup">
                <label class="form-label font-roboto">Assign Chapter</label>
                <select class="form-select font-roboto" id="assignChapterSelect" onchange="handleChapterSelectChange()">
                  <option value="">Loading chapters...</option>
                </select>
              </div>

              <div class="button-group">
                <button class="btn btn-outline-danger font-roboto" id="btnDecline" onclick="handleDeclineAccount()">
                  <i class="ph ph-x"></i> Decline
                </button>
                <button class="btn btn-primary font-roboto" id="btnApprove" onclick="handleApproveAccount()">
                  <i class="ph ph-check"></i> Approve
                </button>
              </div>

              <div style="margin-top: 0.85rem; padding-top: 0.85rem; border-top: 1px solid #e2e8f0;">
                <button type="button" class="btn btn-outline font-roboto" id="btnOpenResetModal" onclick="openResetPasswordModalForSelected()" style="width: 100%; border-color: #fca5a5; color: #b91c1c; background: #fff5f5; display: inline-flex; align-items: center; justify-content: center; gap: 0.45rem; padding: 0.52rem 0.85rem; border-radius: 8px; font-weight: 600; font-size: 0.825rem; cursor: pointer; transition: all 0.2s;">
                  <i class="ph-bold ph-key"></i> <span>Provide Reset Password</span>
                </button>
              </div>
            </div>

          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- ASSIGN REGIONAL DIRECTOR MODAL -->
  <div id="assignDirectorModal" class="modal-overlay hidden" style="display: none;">
    <div class="modal-md" style="max-width: 520px;">
      <div class="modal-header-banner" style="background: #f0fdf4; border-bottom: 1px solid #bbf7d0;">
        <div style="display: flex; align-items: center; gap: 0.65rem;">
          <div style="background: #dcfce7; color: #16a34a; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
            <i class="ph-bold ph-user-check"></i>
          </div>
          <div>
            <h3 style="color: #166534; margin: 0; font-size: 1rem;">Assign Regional Director</h3>
            <p style="color: #15803d; margin: 0; font-size: 0.75rem;">Designate official Regional Director for chapter report endorsement and print certification.</p>
          </div>
        </div>
        <button type="button" onclick="closeAssignDirectorModal()"><i class="ph ph-x"></i></button>
      </div>

      <div class="modal-body-scroll" style="background: #ffffff; padding: 1.25rem;">
        <input type="hidden" id="assignDirectorChapterId">
        
        <div style="margin-bottom: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem;">
          <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Selected Chapter</div>
          <div id="assignDirectorChapterTitle" style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin-top: 0.2rem;">Laguna Chapter</div>
          <div id="assignDirectorChapterRegion" style="font-size: 0.8rem; color: #475569;">Region IV-A · Laguna</div>
        </div>

        <div class="form-group" style="margin-bottom: 1rem;">
          <label class="form-label font-roboto" style="display: block; font-weight: 700; margin-bottom: 0.35rem; font-size: 0.825rem;">Choose from Registered Officials or Enter Name</label>
          <select id="quickSelectDirector" class="form-select font-roboto" onchange="onQuickSelectDirector(this.value)" style="width: 100%; padding: 0.55rem; border-radius: 6px; border: 1px solid #cbd5e1; margin-bottom: 0.65rem; font-size: 0.85rem;">
            <option value="">── Choose registered leader or type name below ──</option>
          </select>
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
          <label class="form-label font-roboto" style="display: block; font-weight: 700; margin-bottom: 0.35rem; font-size: 0.825rem;">Regional Director Full Name</label>
          <input type="text" id="assignDirectorNameInput" class="form-control font-roboto" placeholder="e.g. Dr. Elena Reyes or Pastor Mark Bautista" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.875rem;">
          <small style="color: #64748b; font-size: 0.72rem; display: block; margin-top: 0.25rem;">This name will automatically appear on official post-event reports and print certifications for this chapter.</small>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.65rem;">
          <button type="button" class="btn btn-outline font-roboto" onclick="closeAssignDirectorModal()">Cancel</button>
          <button type="button" class="btn btn-primary font-roboto" id="btnSaveDirectorAssignment" onclick="saveDirectorAssignment()" style="background: #16a34a; border-color: #15803d;">
            <i class="ph ph-check"></i> Save Assignment
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- RESET PASSWORD MODAL -->
  <div id="resetPasswordModal" class="modal-overlay hidden" style="display: none;">
    <div class="modal-md" style="max-width: 520px;">
      <div class="modal-header-banner" style="background: #fff1f2; border-bottom: 1px solid #fecdd3;">
        <div style="display: flex; align-items: center; gap: 0.65rem;">
          <div style="background: #fee2e2; color: #dc2626; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
            <i class="ph-bold ph-key"></i>
          </div>
          <div>
            <h3 style="color: #991b1b; margin: 0; font-size: 1rem;">Provide Reset Password</h3>
            <p style="color: #b91c1c; margin: 0; font-size: 0.75rem;">Issue a new temporary or custom password for this account.</p>
          </div>
        </div>
        <button type="button" onclick="closeResetPasswordModal()"><i class="ph ph-x"></i></button>
      </div>

      <div class="modal-body-scroll" style="background: #ffffff; padding: 1.25rem;">
        <input type="hidden" id="resetUserId">

        <!-- User Summary Card -->
        <div style="margin-bottom: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
              <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Account Selected</div>
              <div id="resetModalUserName" style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin-top: 0.15rem;">--</div>
              <div id="resetModalUserEmail" style="font-size: 0.825rem; color: #2563eb; font-weight: 600;">--</div>
            </div>
            <span class="badge chapter-admin font-roboto" id="resetModalUserRole">--</span>
          </div>
          <div style="margin-top: 0.5rem; font-size: 0.8rem; color: #475569;" id="resetModalUserChapter">
            <i class="ph ph-buildings"></i> Chapter: --
          </div>
          <div id="resetModalNoteBox" style="margin-top: 0.5rem; background: #fff7ed; border: 1px solid #fed7aa; padding: 0.5rem 0.75rem; border-radius: 6px; font-size: 0.8rem; color: #9a3412; display: none;">
            <strong style="display: block; margin-bottom: 2px;"><i class="ph-bold ph-note"></i> User Request Note:</strong>
            <span id="resetModalNoteText"></span>
          </div>
        </div>

        <!-- Password Inputs -->
        <div class="form-group" style="margin-bottom: 1rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
            <label class="form-label font-roboto" style="margin: 0; font-weight: 700; font-size: 0.825rem;">New Temporary Password</label>
            <button type="button" class="btn btn-outline btn-sm font-roboto" onclick="generateRandomTempPassword()" style="padding: 2px 8px; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 4px; border-color: #cbd5e1; background: #f8fafc; color: #1e293b;">
              <i class="ph ph-arrows-clockwise"></i> Auto-Generate
            </button>
          </div>
          <div style="position: relative;">
            <input type="text" id="resetNewPasswordInput" class="form-control font-roboto" placeholder="Minimum 8 characters" style="width: 100%; padding: 0.55rem 2.5rem 0.55rem 0.75rem; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.95rem; font-family: monospace; letter-spacing: 0.5px; font-weight: 700; color: #0f172a;">
            <button type="button" onclick="copyInputPassword()" title="Copy password" style="position: absolute; right: 6px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #64748b; cursor: pointer; padding: 4px; font-size: 1rem;">
              <i class="ph ph-copy" id="iconCopyInput"></i>
            </button>
          </div>
          <small style="color: #64748b; font-size: 0.73rem; display: block; margin-top: 0.25rem;">You can use this auto-generated secure password or enter a custom one (min. 8 characters).</small>
        </div>

        <div class="form-group" style="margin-bottom: 1rem;">
          <label class="form-label font-roboto" style="display: block; font-weight: 700; margin-bottom: 0.35rem; font-size: 0.825rem;">Administrative Note (Optional)</label>
          <input type="text" id="resetAdminNotesInput" class="form-control font-roboto" placeholder="e.g. Issued temporary password to user via phone/email" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.85rem;">
        </div>

        <!-- Success Notification Box (Initially Hidden) -->
        <div id="resetSuccessCallout" style="display: none; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 0.85rem; margin-bottom: 1rem;">
          <div style="display: flex; align-items: center; gap: 0.5rem; color: #065f46; font-weight: 700; font-size: 0.85rem; margin-bottom: 0.4rem;">
            <i class="ph-fill ph-check-circle" style="font-size: 1.15rem; color: #059669;"></i>
            <span>Password Reset Successfully Issued!</span>
          </div>
          <p style="font-size: 0.78rem; color: #047857; margin: 0 0 0.5rem;">Copy and convey the temporary password below to the user:</p>
          <div style="display: flex; gap: 0.5rem; align-items: center;">
            <div id="issuedPasswordDisplay" style="flex: 1; background: #ffffff; border: 1px dashed #059669; padding: 0.45rem 0.75rem; border-radius: 6px; font-family: monospace; font-size: 1rem; font-weight: 800; color: #064e3b; letter-spacing: 1px;">
              --
            </div>
            <button type="button" class="btn btn-primary font-roboto btn-sm" id="btnCopyIssuedPassword" onclick="copyIssuedPassword()" style="background: #059669; border-color: #047857; display: inline-flex; align-items: center; gap: 4px; padding: 0.45rem 0.85rem;">
              <i class="ph ph-copy"></i> <span id="copyIssuedText">Copy</span>
            </button>
          </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.65rem;">
          <button type="button" class="btn btn-outline font-roboto" onclick="closeResetPasswordModal()">Close</button>
          <button type="button" class="btn btn-danger font-roboto" id="btnConfirmResetPassword" onclick="confirmResetPassword()" style="background: #dc2626; border-color: #b91c1c; color: #ffffff; display: inline-flex; align-items: center; gap: 0.45rem; font-weight: 600;">
            <i class="ph-bold ph-key"></i> <span id="textConfirmReset">Issue Password</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- SYSTEM DATA FLUSH & RETRIEVAL MODAL -->
  <div id="systemFlushModal" class="modal-overlay hidden" style="display: none;">
    <div class="modal-md modal-flush-dialog">
      <!-- Modal Header Banner -->
      <div class="modal-header-banner" id="manageFlushHeaderBanner" style="background: #fff1f2; border-bottom: 1px solid #fecdd3;">
        <div style="display: flex; align-items: center; gap: 0.65rem;">
          <div id="manageFlushIconBox" style="background: #fee2e2; color: #dc2626; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
            <i class="ph-bold ph-warning-octagon" id="manageFlushIcon"></i>
          </div>
          <div>
            <h3 style="color: #991b1b; margin: 0; font-size: 1rem;" id="manageFlushTitle">System Maintenance: Data Flush &amp; Retrieval</h3>
            <p style="color: #b91c1c; margin: 0; font-size: 0.75rem;" id="manageFlushSubtitle">Safely archive operational activity or retrieve previously flushed records.</p>
          </div>
        </div>
        <button type="button" onclick="closeSystemFlushModal()"><i class="ph ph-x"></i></button>
      </div>

      <!-- Navigation Tabs -->
      <div class="flush-modal-tabs">
        <button type="button" class="flush-tab-btn active flush-mode-danger" id="tabBtnFlushManage" onclick="switchFlushModalTab('flush')">
          <i class="ph-bold ph-trash"></i>
          <span>Flush &amp; Archive Data</span>
        </button>
        <button type="button" class="flush-tab-btn" id="tabBtnArchivesManage" onclick="switchFlushModalTab('archives')">
          <i class="ph-bold ph-archive-box"></i>
          <span>Archived Data &amp; Retrieval</span>
          <span class="flush-tab-badge" id="flushArchiveBadgeManage">0</span>
        </button>
      </div>

      <!-- TAB 1: FLUSH & ARCHIVE PANEL -->
      <div id="tabPanelFlushManage" class="modal-body-scroll" style="background: #ffffff;">
        <div class="flush-safe-notice">
          <i class="ph-bold ph-shield-check"></i>
          <div>
            <strong>Safe Archival Protection Active:</strong>
            Operational data is not permanently deleted. All flushed events, reports, poster requests, and files are automatically preserved in archive tables and hidden from active views. You can retrieve and restore them at any time in the <em>Archived Data &amp; Retrieval</em> tab.
          </div>
        </div>

        <div style="font-size: 0.825rem; color: #475569; margin-bottom: 0.5rem;">
          <strong>Current Active System Records:</strong>
        </div>
        <div class="flush-stat-grid" id="flushStatGrid">
          <div class="flush-stat-pill">
            <strong id="statEvents">--</strong>
            <span>Events</span>
          </div>
          <div class="flush-stat-pill">
            <strong id="statRequests">--</strong>
            <span>Requests</span>
          </div>
          <div class="flush-stat-pill">
            <strong id="statReports">--</strong>
            <span>Reports</span>
          </div>
          <div class="flush-stat-pill">
            <strong id="statUsers">--</strong>
            <span>Users</span>
          </div>
          <div class="flush-stat-pill">
            <strong id="statFiles">--</strong>
            <span>Files</span>
          </div>
        </div>

        <div style="margin-top: 1rem;">
          <label style="font-size: 0.825rem; font-weight: 700; color: #1e293b;">Choose Flush Mode:</label>
          <div class="flush-option-list">
            <label class="flush-option-label active" id="optLabelActivity" onclick="selectFlushMode('activity')">
              <input type="radio" name="flush_mode_radio" value="activity" checked>
              <div class="flush-option-content">
                <h5>🧹 Flush Operational Activity (Recommended)</h5>
                <p>Archives all events, proposals, poster requests, timeline deliverables, reports, notifications, and audit logs. <strong>User accounts and registered chapters remain safe and intact.</strong></p>
              </div>
            </label>

            <label class="flush-option-label" id="optLabelFull" onclick="selectFlushMode('full')">
              <input type="radio" name="flush_mode_radio" value="full">
              <div class="flush-option-content">
                <h5>🔄 Full Factory Reset</h5>
                <p>Archives current data and resets all database tables to pristine baseline state (default chapters, default template steps, and default administrative logins).</p>
              </div>
            </label>

            <label class="flush-option-label" id="optLabelDemo" onclick="selectFlushMode('demo')">
              <input type="radio" name="flush_mode_radio" value="demo">
              <div class="flush-option-content">
                <h5>🚀 Full Reset &amp; Seed Demo Data</h5>
                <p>Archives current data, resets tables, and immediately populates realistic sample proposals, poster requests, and conducted events with 2-week compliance timers for testing.</p>
              </div>
            </label>
          </div>
        </div>

        <div class="flush-confirm-box">
          <label><i class="ph-bold ph-shield-warning"></i> Safety Verification: Type "FLUSH" to unlock</label>
          <input type="text" id="flushConfirmationInput" class="flush-confirm-input" placeholder="Type FLUSH to confirm" oninput="checkFlushConfirmation()">
        </div>
      </div>

      <!-- TAB 2: ARCHIVED DATA & RETRIEVAL PANEL -->
      <div id="tabPanelArchivesManage" class="modal-body-scroll" style="background: #f8fafc; display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <div>
            <h4 style="margin: 0; font-size: 0.92rem; color: #0f172a;">Archived System Batches</h4>
            <p style="margin: 0; font-size: 0.75rem; color: #64748b;">Browse flushed data snapshots and restore records directly back into active tables.</p>
          </div>
          <button type="button" class="btn btn-outline btn-sm font-roboto" onclick="loadArchivesList()" style="background: #ffffff; gap: 0.35rem; display: inline-flex; align-items: center; cursor: pointer;">
            <i class="ph-bold ph-arrows-clockwise"></i> Refresh
          </button>
        </div>

        <div id="archivesListContainer">
          <div style="text-align: center; padding: 2rem; color: #64748b;">
            <i class="ph ph-spinner-gap ph-spin" style="font-size: 1.5rem;"></i>
            <p style="margin-top: 0.5rem; font-size: 0.8rem;">Loading archived snapshots...</p>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div id="manageFlushFooter" style="padding: 0.85rem 1.25rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 0.5rem;">
        <button type="button" class="btn btn-outline font-roboto" onclick="closeSystemFlushModal()">Close</button>
        <button type="button" class="btn btn-danger font-roboto" id="btnExecuteFlush" disabled onclick="executeSystemFlush()" style="background: #dc2626; border-color: #b91c1c; color: white; display: inline-flex; align-items: center; gap: 0.4rem; opacity: 0.5; cursor: not-allowed;">
          <i class="ph-bold ph-trash"></i> <span id="flushBtnText">Execute Flush &amp; Archive</span>
        </button>
      </div>
    </div>
  </div>

  <script src="assets/api-client.js"></script>
  <script>
    let activeFilter = 'pending';
    let selectedUserId = null;
    let chaptersList = [];

    // Load Metrics from Python API
    async function loadMetrics() {
      const res = await YTR_API.accounts.getMetrics();
      if (res.status === 'success' && res.data) {
        document.getElementById('metricTotal').textContent = res.data.total_accounts || 0;
        document.getElementById('metricPending').textContent = res.data.pending_requests || 0;
        document.getElementById('metricNational').textContent = res.data.national_admins || 0;
        document.getElementById('metricChapter').textContent = res.data.chapter_admins || 0;

        const badgeReset = document.getElementById('badgeResetCount');
        if (badgeReset) {
          const count = res.data.password_resets || 0;
          badgeReset.textContent = count;
          badgeReset.style.display = count > 0 ? 'inline-block' : 'none';
        }
      }
    }

    // Load Chapters for Dropdown
    async function loadChapterOptions() {
      const res = await YTR_API.chapters.getAll();
      const select = document.getElementById('assignChapterSelect');
      if (res.status === 'success' && Array.isArray(res.data)) {
        chaptersList = res.data;
        select.innerHTML = '<option value="">Select Chapter (for Chapter Admin)</option>';
        res.data.forEach(ch => {
          const opt = document.createElement('option');
          opt.value = ch.id;
          opt.textContent = ch.chapter_name + (ch.province ? ` (${ch.province})` : '');
          select.appendChild(opt);
        });
      }
    }

    let allAccountsCache = [];

    // Load and Render Accounts Table
    async function loadAccounts(filter = activeFilter) {
      const thead = document.getElementById('accountsTableHead');
      if (thead) {
        thead.innerHTML = `
          <tr>
            <th>Applicant</th>
            <th>Email</th>
            <th>Chapter</th>
            <th style="text-align: center;">Requested Role</th>
            <th>Date Requested</th>
            <th style="text-align: center;">Status</th>
            <th style="text-align: center;">Actions</th>
          </tr>
        `;
      }

      const tbody = document.getElementById('accountsTableBody');
      tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 2rem; color: #64748b;">Loading accounts...</td></tr>';

      const res = await YTR_API.accounts.list(filter);
      if (res.status === 'success' && Array.isArray(res.data)) {
        allAccountsCache = res.data;
        if (res.data.length === 0) {
          const emptyMsg = filter === 'password_resets'
            ? 'No pending password reset requests. All user accounts are up to date.'
            : 'No accounts found for this filter.';
          tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 2rem; color: #64748b;">${emptyMsg}</td></tr>`;
          return;
        }

        tbody.innerHTML = '';
        res.data.forEach((user, index) => {
          const row = document.createElement('tr');
          const roleLabel = user.role === 'national_admin' ? 'National Admin' : 'Chapter Admin';
          const roleBadgeClass = user.role === 'national_admin' ? 'badge national-admin' : 'badge chapter-admin';
          
          let statusBadgeClass = 'badge pending';
          if (user.account_status === 'active') statusBadgeClass = 'badge approved';
          else if (user.account_status === 'rejected') statusBadgeClass = 'badge rose';

          const reqDate = user.requested_at ? user.requested_at.replace('T', '<br>') : 'N/A';
          const hasPendingReset = Number(user.password_reset_pending) === 1;

          row.innerHTML = `
            <td>
              <div class="td-user">
                <img src="https://placehold.co/32x32/e2e8f0/64748b" alt="Avatar">
                <div>
                  <span class="font-roboto">${escapeHtml(user.full_name)}</span>
                  ${hasPendingReset ? `
                    <div style="margin-top: 3px;">
                      <span class="badge rose font-roboto" style="display: inline-flex; align-items: center; gap: 3px; font-size: 0.68rem; padding: 2px 6px;">
                        <i class="ph-bold ph-key"></i> Reset Requested
                      </span>
                    </div>
                  ` : ''}
                  ${user.reset_notes ? `
                    <div style="font-size: 0.72rem; color: #991b1b; font-style: italic; margin-top: 2px; max-width: 200px;">
                      "${escapeHtml(user.reset_notes)}"
                    </div>
                  ` : ''}
                </div>
              </div>
            </td>
            <td class="td-sub">${escapeHtml(user.email)}</td>
            <td class="td-sub">${escapeHtml(user.chapter_name || 'National HQ')}</td>
            <td style="text-align: center;">
              <span class="${roleBadgeClass} font-roboto">${roleLabel}</span>
              ${user.position ? `<div style="font-size: 0.72rem; color: #475569; font-weight: 600; margin-top: 3px;"><i class="ph ph-identification-badge"></i> ${escapeHtml(user.position)}</div>` : ''}
            </td>
            <td class="td-sub" style="font-size: 0.8rem;">${reqDate}</td>
            <td style="text-align: center;"><span class="${statusBadgeClass} font-roboto">${user.account_status.toUpperCase()}</span></td>
            <td style="text-align: center;">
              <div style="display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem;">
                <button class="btn btn-primary btn-sm font-roboto" onclick='selectUserForReview(${JSON.stringify(user)})'>Review</button>
                <button class="btn btn-outline btn-sm font-roboto" title="Provide Reset Password" onclick='openResetPasswordModal(${JSON.stringify(user)})' style="padding: 0.32rem 0.55rem; color: #b91c1c; border-color: #fca5a5; background: #fff5f5; display: inline-flex; align-items: center; justify-content: center;">
                  <i class="ph-bold ph-key"></i>
                </button>
              </div>
            </td>
          `;
          tbody.appendChild(row);

          // Select first user by default
          if (index === 0 && !selectedUserId) {
            selectUserForReview(user);
          }
        });
      }
    }

    // Load and Render Chapter Regional Directors
    async function loadChapterDirectors() {
      const thead = document.getElementById('accountsTableHead');
      const tbody = document.getElementById('accountsTableBody');
      
      if (thead) {
        thead.innerHTML = `
          <tr>
            <th>Chapter Branch</th>
            <th>Chapter Code</th>
            <th>Region / Province</th>
            <th>Assigned Regional Director</th>
            <th style="text-align: center;">Status</th>
            <th style="text-align: center;">Actions</th>
          </tr>
        `;
      }
      tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem; color: #64748b;">Loading chapters and assigned directors...</td></tr>';

      const res = await YTR_API.chapters.getAll();
      if (res.status === 'success' && Array.isArray(res.data)) {
        chaptersList = res.data;
        if (res.data.length === 0) {
          tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem; color: #64748b;">No active chapters registered.</td></tr>';
          return;
        }

        tbody.innerHTML = '';
        res.data.forEach(ch => {
          const row = document.createElement('tr');
          const hasDirector = ch.regional_director_name && ch.regional_director_name.trim().length > 0;

          row.innerHTML = `
            <td>
              <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div style="width: 32px; height: 32px; border-radius: 6px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                  <i class="ph-fill ph-buildings"></i>
                </div>
                <strong class="font-roboto" style="color: #0f172a;">${escapeHtml(ch.chapter_name)}</strong>
              </div>
            </td>
            <td><span class="badge font-roboto" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">${escapeHtml(ch.chapter_code)}</span></td>
            <td class="td-sub">${escapeHtml(ch.region || 'Region')} · ${escapeHtml(ch.province || '')}</td>
            <td>
              ${hasDirector 
                ? `<div style="display: flex; align-items: center; gap: 0.4rem; color: #0f172a; font-weight: 700;">
                     <i class="ph-fill ph-seal-check" style="color: #16a34a; font-size: 1.1rem;"></i>
                     <span>${escapeHtml(ch.regional_director_name)}</span>
                   </div>`
                : `<span class="badge pending font-roboto"><i class="ph ph-clock"></i> Unassigned</span>`
              }
            </td>
            <td style="text-align: center;"><span class="badge approved font-roboto">ACTIVE</span></td>
            <td style="text-align: center;">
              <button class="btn btn-outline font-roboto" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; margin: 0 auto; display: inline-flex; align-items: center; gap: 0.35rem;" onclick='openAssignDirectorModal(${JSON.stringify(ch)})'>
                <i class="ph ph-pencil-simple"></i> Assign Director
              </button>
            </td>
          `;
          tbody.appendChild(row);
        });
      }
    }

    // Modal Handlers for Regional Director Assignment
    function openAssignDirectorModal(ch) {
      document.getElementById('assignDirectorChapterId').value = ch.id;
      document.getElementById('assignDirectorChapterTitle').textContent = ch.chapter_name + ` (${ch.chapter_code})`;
      document.getElementById('assignDirectorChapterRegion').textContent = `${ch.region || 'Region'} · ${ch.province || ''} · ${ch.city_municipality || ''}`;
      document.getElementById('assignDirectorNameInput').value = ch.regional_director_name || '';

      const quickSel = document.getElementById('quickSelectDirector');
      quickSel.innerHTML = '<option value="">── Choose registered official or type name below ──</option>';
      if (Array.isArray(allAccountsCache)) {
        allAccountsCache.filter(u => u.account_status === 'active').forEach(u => {
          const opt = document.createElement('option');
          opt.value = u.full_name;
          opt.textContent = `${u.full_name} (${u.position || u.role} · ${u.chapter_name || 'National HQ'})`;
          quickSel.appendChild(opt);
        });
      }

      const modal = document.getElementById('assignDirectorModal');
      modal.classList.remove('hidden');
      modal.style.display = 'flex';
    }

    function onQuickSelectDirector(val) {
      if (val) {
        document.getElementById('assignDirectorNameInput').value = val;
      }
    }

    function closeAssignDirectorModal() {
      const modal = document.getElementById('assignDirectorModal');
      modal.classList.add('hidden');
      modal.style.display = 'none';
    }

    async function saveDirectorAssignment() {
      const chId = document.getElementById('assignDirectorChapterId').value;
      const dirName = document.getElementById('assignDirectorNameInput').value.trim();
      const btn = document.getElementById('btnSaveDirectorAssignment');

      if (!dirName) {
        alert('Please enter or select a Regional Director name.');
        return;
      }

      btn.disabled = true;
      btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Saving...';

      try {
        const res = await YTR_API.chapters.assignRegionalDirector(chId, dirName);
        if (res.status === 'success' || res.ok) {
          alert('Regional Director assigned successfully!');
          closeAssignDirectorModal();
          loadChapterDirectors();
        } else {
          alert(res.message || 'Error assigning Regional Director.');
        }
      } catch (err) {
        alert('Network error communicating with Python API.');
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="ph ph-check"></i> Save Assignment';
      }
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

    // Switch Tab
    function switchAccountTab(filter, btn) {
      activeFilter = filter;
      document.querySelectorAll('.tabs-container .tab-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      if (filter === 'chapter_directors') {
        loadChapterDirectors();
      } else {
        loadAccounts(filter);
      }
    }

    const NATIONAL_POSITIONS = [
      'Chief Executive Operation',
      'Executive Director',
      'Regional Director (National)',
      'National Youth Officer'
    ];

    const CHAPTER_ROLES = [
      'Chapter Coordinator',
      'Chapter President',
      'Regional Director (Chapter)'
    ];

    function populatePositionOptions(role, selectedPosition = null) {
      const select = document.getElementById('assignPositionSelect');
      if (!select) return;
      select.innerHTML = '';

      if (role === 'national_admin') {
        const grp = document.createElement('optgroup');
        grp.label = '── NATIONAL POSITIONS ──';
        NATIONAL_POSITIONS.forEach(pos => {
          const opt = document.createElement('option');
          opt.value = pos;
          opt.textContent = pos;
          if (pos === selectedPosition) opt.selected = true;
          grp.appendChild(opt);
        });
        select.appendChild(grp);
        if (!selectedPosition || !NATIONAL_POSITIONS.includes(selectedPosition)) {
          select.value = 'Executive Director';
        }
      } else {
        const grp = document.createElement('optgroup');
        grp.label = '── CHAPTER POSITIONS ──';
        CHAPTER_ROLES.forEach(pos => {
          const opt = document.createElement('option');
          opt.value = pos;
          opt.textContent = pos;
          if (pos === selectedPosition) opt.selected = true;
          grp.appendChild(opt);
        });
        select.appendChild(grp);
        if (!selectedPosition || !CHAPTER_ROLES.includes(selectedPosition)) {
          select.value = 'Chapter Coordinator';
        }
      }
    }

    function lockRoleToChapter(reason = 'Account requested for Chapter') {
      const rNat = document.getElementById('roleNational');
      const rChap = document.getElementById('roleChapter');
      const lblNat = document.getElementById('labelRoleNational');
      const lblChap = document.getElementById('labelRoleChapter');
      const chGroup = document.getElementById('assignChapterGroup');

      if (rChap) {
        rChap.checked = true;
        rChap.disabled = false;
      }
      if (rNat) {
        rNat.checked = false;
        rNat.disabled = true;
      }

      if (lblNat) {
        lblNat.style.opacity = '0.45';
        lblNat.style.cursor = 'not-allowed';
        lblNat.title = reason + '; only Chapter Admin role can be selected.';
      }
      if (lblChap) {
        lblChap.style.opacity = '1';
        lblChap.style.cursor = 'pointer';
        lblChap.title = '';
      }
      if (chGroup) chGroup.style.display = 'block';
    }

    function lockRoleToNational(reason = 'Account requested for National HQ') {
      const rNat = document.getElementById('roleNational');
      const rChap = document.getElementById('roleChapter');
      const lblNat = document.getElementById('labelRoleNational');
      const lblChap = document.getElementById('labelRoleChapter');
      const chGroup = document.getElementById('assignChapterGroup');
      const chSelect = document.getElementById('assignChapterSelect');

      if (rNat) {
        rNat.checked = true;
        rNat.disabled = false;
      }
      if (rChap) {
        rChap.checked = false;
        rChap.disabled = true;
      }

      if (lblChap) {
        lblChap.style.opacity = '0.45';
        lblChap.style.cursor = 'not-allowed';
        lblChap.title = reason + '; only National Admin role can be selected.';
      }
      if (lblNat) {
        lblNat.style.opacity = '1';
        lblNat.style.cursor = 'pointer';
        lblNat.title = '';
      }
      if (chGroup) chGroup.style.display = 'none';
      if (chSelect) chSelect.value = '';
    }

    function toggleRoleTierOverride(e) {
      if (e && e.preventDefault) e.preventDefault();
      const rNat = document.getElementById('roleNational');
      const rChap = document.getElementById('roleChapter');
      const lblNat = document.getElementById('labelRoleNational');
      const lblChap = document.getElementById('labelRoleChapter');
      const badge = document.getElementById('roleRequestedBadge');
      const link = document.getElementById('linkReassignTier');

      if (rNat) rNat.disabled = false;
      if (rChap) rChap.disabled = false;
      if (lblNat) { lblNat.style.opacity = '1'; lblNat.style.cursor = 'pointer'; lblNat.title = ''; }
      if (lblChap) { lblChap.style.opacity = '1'; lblChap.style.cursor = 'pointer'; lblChap.title = ''; }
      if (badge) {
        badge.textContent = 'Tier Unlocked';
        badge.style.color = '#b45309';
        badge.style.background = '#fef3c7';
        badge.style.border = '1px solid #fde68a';
      }
      if (link) link.style.display = 'none';
    }

    function syncRolePositionOptions(role, selectedPosition = null) {
      const chGroup = document.getElementById('assignChapterGroup');
      const posSelect = document.getElementById('assignPositionSelect');
      const currentPos = selectedPosition || (posSelect ? posSelect.value : null);

      if (role === 'national_admin') {
        if (chGroup) chGroup.style.display = 'none';
        populatePositionOptions('national_admin', currentPos);
      } else {
        if (chGroup) chGroup.style.display = 'block';
        populatePositionOptions('chapter_admin', currentPos);
      }
    }

    function handlePositionSelectChange() {
      const val = document.getElementById('assignPositionSelect').value;
      if (CHAPTER_ROLES.includes(val)) {
        lockRoleToChapter('Chapter position selected');
      } else if (NATIONAL_POSITIONS.includes(val)) {
        lockRoleToNational('National position selected');
      }
    }

    function handleChapterSelectChange() {
      const chSelect = document.getElementById('assignChapterSelect');
      if (chSelect && chSelect.value !== '') {
        lockRoleToChapter('Chapter selected');
        populatePositionOptions('chapter_admin', document.getElementById('assignPositionSelect').value);
      }
    }

    // Select User into Review Panel
    function selectUserForReview(user) {
      selectedUserId = user.id;
      document.getElementById('reviewUserName').textContent = user.full_name;
      document.getElementById('reviewUserEmail').textContent = user.email;
      document.getElementById('reviewUserBadge').textContent = user.account_status.toUpperCase();
      document.getElementById('reviewUserChapter').textContent = user.chapter_name || 'National HQ';
      document.getElementById('reviewUserRole').textContent = (user.position ? user.position + ' • ' : '') + (user.role === 'national_admin' ? 'National Admin' : 'Chapter Admin');
      document.getElementById('reviewUserReason').textContent = `Account request submitted for ${user.full_name} (${user.email}). Position: ${user.position || 'Not specified'}. Review authorization and confirm chapter assignment below.`;

      const badge = document.getElementById('roleRequestedBadge');
      const link = document.getElementById('linkReassignTier');
      const chSelect = document.getElementById('assignChapterSelect');

      // Determine if account requested/belongs to a chapter
      const isChapter = (user.role === 'chapter_admin') ||
                        (Boolean(user.chapter_id) && user.chapter_name !== 'National HQ') ||
                        CHAPTER_ROLES.includes(user.position);

      if (isChapter) {
        lockRoleToChapter('Account requested for Chapter');
        populatePositionOptions('chapter_admin', user.position);
        if (badge) {
          badge.style.display = 'inline-block';
          badge.textContent = 'Chapter Account';
          badge.style.color = '#047857';
          badge.style.background = '#ecfdf5';
          badge.style.border = '1px solid #a7f3d0';
        }
        if (link) {
          link.style.display = 'inline-block';
          link.textContent = 'Change Tier';
        }
        if (user.chapter_id && chSelect) {
          chSelect.value = user.chapter_id;
        }
      } else {
        lockRoleToNational('Account requested for National HQ');
        populatePositionOptions('national_admin', user.position);
        if (badge) {
          badge.style.display = 'inline-block';
          badge.textContent = 'National Account';
          badge.style.color = '#1e40af';
          badge.style.background = '#eff6ff';
          badge.style.border = '1px solid #bfdbfe';
        }
        if (link) {
          link.style.display = 'inline-block';
          link.textContent = 'Change Tier';
        }
        if (chSelect) {
          chSelect.value = '';
        }
      }

      const approveBtn = document.getElementById('btnApprove');
      const declineBtn = document.getElementById('btnDecline');

      if (user.account_status === 'active') {
        approveBtn.textContent = 'Save Changes';
        declineBtn.textContent = 'Suspend';
      } else {
        approveBtn.innerHTML = '<i class="ph ph-check"></i> Approve';
        declineBtn.innerHTML = '<i class="ph ph-x"></i> Decline';
      }
    }

    // Handle Approve
    async function handleApproveAccount() {
      if (!selectedUserId) {
        alert('Please select an account from the table to review.');
        return;
      }

      const roleRadio = document.querySelector('input[name="assign_role"]:checked');
      if (!roleRadio) {
        alert('Please select a role (National Admin or Chapter Admin).');
        return;
      }
      const role = roleRadio.value;
      const position = document.getElementById('assignPositionSelect').value;
      const chapterId = document.getElementById('assignChapterSelect').value;

      if (role === 'chapter_admin') {
        if (!chapterId) {
          alert('Please select an assigned chapter for Chapter Admin role.');
          return;
        }
        if (!CHAPTER_ROLES.includes(position)) {
          alert('Please select a valid Chapter position for Chapter Admin role.');
          return;
        }
      } else if (role === 'national_admin') {
        if (!NATIONAL_POSITIONS.includes(position)) {
          alert('Please select a valid National position for National Admin role.');
          return;
        }
      }

      const res = await YTR_API.accounts.approve(selectedUserId, role, role === 'national_admin' ? null : chapterId, position);
      if (res.status === 'success') {
        alert('Account saved / approved successfully!');
        loadMetrics();
        loadAccounts(activeFilter);
      } else {
        alert(res.message || 'Error approving account.');
      }
    }

    // Handle Decline
    async function handleDeclineAccount() {
      if (!selectedUserId) {
        alert('Please select an account from the table to review.');
        return;
      }

      const reason = prompt('Please enter a note for declining this account (optional):', 'Application declined by administrator.');
      if (reason === null) return; // Cancelled

      const res = await YTR_API.accounts.decline(selectedUserId, reason);
      if (res.status === 'success') {
        alert('Account has been declined.');
        loadMetrics();
        loadAccounts(activeFilter);
      } else {
        alert(res.message || 'Error declining account.');
      }
    }

    // ==========================================
    // Password Reset Handlers
    // ==========================================
    let selectedResetUser = null;

    function generateRandomTempPassword() {
      const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
      let rand = '';
      for (let i = 0; i < 4; i++) {
        rand += chars.charAt(Math.floor(Math.random() * chars.length));
      }
      const pwd = `YTR-${rand}#26`;
      const input = document.getElementById('resetNewPasswordInput');
      if (input) input.value = pwd;
      return pwd;
    }

    function openResetPasswordModal(user) {
      if (!user) return;
      selectedResetUser = user;
      document.getElementById('resetUserId').value = user.id;
      document.getElementById('resetModalUserName').textContent = user.full_name;
      document.getElementById('resetModalUserEmail').textContent = user.email;
      document.getElementById('resetModalUserRole').textContent = (user.position ? user.position + ' • ' : '') + (user.role === 'national_admin' ? 'National Admin' : 'Chapter Admin');
      document.getElementById('resetModalUserChapter').innerHTML = `<i class="ph ph-buildings"></i> Chapter: <strong>${escapeHtml(user.chapter_name || 'National HQ')}</strong>`;
      
      const noteBox = document.getElementById('resetModalNoteBox');
      const noteText = document.getElementById('resetModalNoteText');
      if (user.reset_notes && user.reset_notes.trim().length > 0) {
        noteText.textContent = user.reset_notes;
        noteBox.style.display = 'block';
      } else {
        noteBox.style.display = 'none';
      }

      document.getElementById('resetAdminNotesInput').value = '';
      document.getElementById('resetSuccessCallout').style.display = 'none';
      document.getElementById('textConfirmReset').textContent = 'Issue Password';
      document.getElementById('btnConfirmResetPassword').disabled = false;
      document.getElementById('copyIssuedText').textContent = 'Copy';

      generateRandomTempPassword();

      const modal = document.getElementById('resetPasswordModal');
      modal.classList.remove('hidden');
      modal.style.display = 'flex';
    }

    function openResetPasswordModalForSelected() {
      if (!selectedUserId) {
        alert('Please select an account from the table first.');
        return;
      }
      const user = allAccountsCache.find(u => Number(u.id) === Number(selectedUserId));
      if (user) {
        openResetPasswordModal(user);
      } else {
        alert('Could not find account details for the selected user.');
      }
    }

    function closeResetPasswordModal() {
      const modal = document.getElementById('resetPasswordModal');
      modal.classList.add('hidden');
      modal.style.display = 'none';
      selectedResetUser = null;
    }

    async function confirmResetPassword() {
      if (!selectedResetUser) return;
      const pwdInput = document.getElementById('resetNewPasswordInput');
      const notesInput = document.getElementById('resetAdminNotesInput');
      const newPassword = (pwdInput.value || '').trim();
      const notes = (notesInput.value || '').trim();

      if (!newPassword || newPassword.length < 8) {
        alert('Password must be at least 8 characters long.');
        pwdInput.focus();
        return;
      }

      const btn = document.getElementById('btnConfirmResetPassword');
      const textSpan = document.getElementById('textConfirmReset');
      btn.disabled = true;
      textSpan.textContent = 'Saving...';

      try {
        const res = await YTR_API.accounts.resetPassword(selectedResetUser.id, newPassword, notes);
        if (res.status === 'success' || res.ok) {
          const issued = res.data?.temporary_password || newPassword;
          document.getElementById('issuedPasswordDisplay').textContent = issued;
          document.getElementById('resetSuccessCallout').style.display = 'block';
          textSpan.textContent = 'Issued!';
          
          // Refresh accounts list and metrics in background
          loadMetrics();
          loadAccounts(activeFilter);
        } else {
          alert(res.message || 'Error resetting password.');
          btn.disabled = false;
          textSpan.textContent = 'Issue Password';
        }
      } catch (err) {
        alert('Network error connecting to Python backend.');
        btn.disabled = false;
        textSpan.textContent = 'Issue Password';
      }
    }

    function copyInputPassword() {
      const input = document.getElementById('resetNewPasswordInput');
      if (!input || !input.value) return;
      navigator.clipboard.writeText(input.value).then(() => {
        const icon = document.getElementById('iconCopyInput');
        if (icon) {
          icon.className = 'ph-bold ph-check';
          setTimeout(() => { icon.className = 'ph ph-copy'; }, 1500);
        }
      });
    }

    function copyIssuedPassword() {
      const text = document.getElementById('issuedPasswordDisplay').textContent.trim();
      if (!text || text === '--') return;
      navigator.clipboard.writeText(text).then(() => {
        const span = document.getElementById('copyIssuedText');
        if (span) {
          span.textContent = 'Copied!';
          setTimeout(() => { span.textContent = 'Copy'; }, 1800);
        }
      });
    }

    // ==========================================
    // System Maintenance & Data Flush Handlers
    // ==========================================
    let currentFlushMode = 'activity';
    let currentActiveFlushTab = 'flush';

    function switchFlushModalTab(tab) {
      currentActiveFlushTab = tab;
      const tabFlush = document.getElementById('tabBtnFlushManage');
      const tabArchives = document.getElementById('tabBtnArchivesManage');
      const panelFlush = document.getElementById('tabPanelFlushManage');
      const panelArchives = document.getElementById('tabPanelArchivesManage');
      const btnFlush = document.getElementById('btnExecuteFlush');
      const banner = document.getElementById('manageFlushHeaderBanner');
      const iconBox = document.getElementById('manageFlushIconBox');
      const icon = document.getElementById('manageFlushIcon');
      const title = document.getElementById('manageFlushTitle');
      const subtitle = document.getElementById('manageFlushSubtitle');

      if (tab === 'archives') {
        if (tabFlush) {
          tabFlush.classList.remove('active', 'flush-mode-danger');
        }
        if (tabArchives) {
          tabArchives.classList.add('active', 'flush-mode-archive');
        }
        if (panelFlush) panelFlush.style.display = 'none';
        if (panelArchives) panelArchives.style.display = 'block';
        if (btnFlush) btnFlush.style.display = 'none';

        if (banner) {
          banner.style.background = '#f0fdf4';
          banner.style.borderBottom = '1px solid #bbf7d0';
        }
        if (iconBox) {
          iconBox.style.background = '#dcfce7';
          iconBox.style.color = '#15803d';
        }
        if (icon) icon.className = 'ph-bold ph-archive-box';
        if (title) {
          title.textContent = 'System Archives & Data Retrieval Ledger';
          title.style.color = '#166534';
        }
        if (subtitle) {
          subtitle.textContent = 'Inspect safely archived system data snapshots and restore records back into active tables.';
          subtitle.style.color = '#15803d';
        }

        loadArchivesList();
      } else {
        if (tabFlush) {
          tabFlush.classList.add('active', 'flush-mode-danger');
        }
        if (tabArchives) {
          tabArchives.classList.remove('active', 'flush-mode-archive');
        }
        if (panelFlush) panelFlush.style.display = 'block';
        if (panelArchives) panelArchives.style.display = 'none';
        if (btnFlush) btnFlush.style.display = 'inline-flex';

        if (banner) {
          banner.style.background = '#fff1f2';
          banner.style.borderBottom = '1px solid #fecdd3';
        }
        if (iconBox) {
          iconBox.style.background = '#fee2e2';
          iconBox.style.color = '#dc2626';
        }
        if (icon) icon.className = 'ph-bold ph-warning-octagon';
        if (title) {
          title.textContent = 'System Maintenance: Data Flush & Retrieval';
          title.style.color = '#991b1b';
        }
        if (subtitle) {
          subtitle.textContent = 'Safely archive operational activity or retrieve previously flushed records.';
          subtitle.style.color = '#b91c1c';
        }

        loadSystemSummaryStats();
      }
    }

    async function loadSystemSummaryStats() {
      try {
        const res = await YTR_API.system.getSummary();
        if (res.status === 'success' && res.data && res.data.table_counts) {
          const tc = res.data.table_counts;
          const setT = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
          setT('statEvents', tc.events || 0);
          setT('statRequests', tc.poster_requests || 0);
          setT('statReports', tc.event_reports || 0);
          setT('statUsers', tc.users || 0);
          setT('statFiles', res.data.uploaded_files_count || 0);
          setT('flushArchiveBadgeManage', res.data.archive_batches_count || 0);
        }
      } catch (err) {
        console.warn('Could not load system summary:', err);
      }
    }

    async function openSystemFlushModal(initialTab = 'flush') {
      const modal = document.getElementById('systemFlushModal');
      const input = document.getElementById('flushConfirmationInput');
      
      if (input) input.value = '';
      selectFlushMode('activity');
      checkFlushConfirmation();

      modal.style.display = 'flex';
      modal.classList.remove('hidden');

      switchFlushModalTab(initialTab);
      loadSystemSummaryStats();
    }

    function closeSystemFlushModal() {
      const modal = document.getElementById('systemFlushModal');
      if (modal) {
        modal.style.display = 'none';
        modal.classList.add('hidden');
      }
    }

    function selectFlushMode(mode) {
      currentFlushMode = mode;
      ['activity', 'full', 'demo'].forEach(m => {
        const lbl = document.getElementById('optLabel' + m.charAt(0).toUpperCase() + m.slice(1));
        const radio = lbl ? lbl.querySelector('input[type="radio"]') : null;
        if (lbl) {
          if (m === mode) {
            lbl.classList.add('active');
            if (radio) radio.checked = true;
          } else {
            lbl.classList.remove('active');
            if (radio) radio.checked = false;
          }
        }
      });
    }

    function checkFlushConfirmation() {
      const input = document.getElementById('flushConfirmationInput');
      const btn = document.getElementById('btnExecuteFlush');
      const text = (input ? input.value : '').trim().toUpperCase();
      if (btn) {
        if (text === 'FLUSH') {
          btn.disabled = false;
          btn.style.opacity = '1';
          btn.style.cursor = 'pointer';
        } else {
          btn.disabled = true;
          btn.style.opacity = '0.5';
          btn.style.cursor = 'not-allowed';
        }
      }
    }

    async function executeSystemFlush() {
      const input = document.getElementById('flushConfirmationInput');
      const btn = document.getElementById('btnExecuteFlush');
      const btnText = document.getElementById('flushBtnText');
      const text = (input ? input.value : '').trim().toUpperCase();

      if (text !== 'FLUSH') {
        alert('Please type "FLUSH" to confirm this action.');
        return;
      }

      btn.disabled = true;
      btn.style.opacity = '0.6';
      btnText.textContent = 'Archiving & Flushing...';

      try {
        const res = await YTR_API.system.flush(currentFlushMode, 'FLUSH');
        if (res.status === 'success') {
          alert('Success! ' + (res.message || 'System data has been safely flushed and archived.'));
          // Reload page state
          loadMetrics();
          loadAccounts(activeFilter);
          loadChapterOptions();
          loadSystemSummaryStats();
          // Switch to archives tab to show the archived batch
          switchFlushModalTab('archives');
        } else {
          alert('Flush Failed: ' + (res.message || 'An error occurred during flush.'));
        }
      } catch (err) {
        alert('Flush Error: ' + err.message);
      } finally {
        btn.disabled = false;
        btn.style.opacity = '1';
        btnText.textContent = 'Execute Flush & Archive';
        checkFlushConfirmation();
      }
    }

    async function loadArchivesList() {
      const container = document.getElementById('archivesListContainer');
      if (!container) return;

      container.innerHTML = `
        <div style="text-align: center; padding: 2rem; color: #64748b;">
          <i class="ph ph-spinner-gap ph-spin" style="font-size: 1.5rem;"></i>
          <p style="margin-top: 0.5rem; font-size: 0.8rem;">Loading archived snapshots...</p>
        </div>
      `;

      try {
        const res = await YTR_API.system.getArchives();
        if (res.status !== 'success' || !Array.isArray(res.data) || res.data.length === 0) {
          container.innerHTML = `
            <div class="flush-empty-state">
              <i class="ph-bold ph-archive-box" style="font-size: 2.2rem; color: #94a3b8; display: block; margin-bottom: 0.5rem;"></i>
              <h5 style="margin: 0; color: #334155; font-size: 0.95rem; font-weight: 700;">No Archived Batches Found</h5>
              <p style="margin: 0.35rem 0 0; color: #64748b; font-size: 0.8rem; max-width: 420px; margin-inline: auto;">
                When system data is flushed, records and uploaded files will be safely moved here and made retrievable at any time.
              </p>
            </div>
          `;
          const badge = document.getElementById('flushArchiveBadgeManage');
          if (badge) badge.textContent = '0';
          return;
        }

        const batches = res.data;
        const badge = document.getElementById('flushArchiveBadgeManage');
        if (badge) badge.textContent = batches.length;

        container.innerHTML = batches.map(b => {
          const modeLabel = b.flushed_mode === 'activity' ? 'Operational Activity' :
                            b.flushed_mode === 'full' ? 'Factory Reset' :
                            b.flushed_mode === 'demo' ? 'Reset & Demo Seed' : b.flushed_mode;
          
          const modeBadgeStyle = b.flushed_mode === 'activity' ? 'background: #fef3c7; color: #92400e; border: 1px solid #fde68a;' :
                                 b.flushed_mode === 'full' ? 'background: #fee2e2; color: #991b1b; border: 1px solid #fecdd3;' :
                                 'background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe;';

          const isRestored = b.is_restored;
          const statusBadge = isRestored ? 
            `<span class="archive-batch-badge archive-badge-restored"><i class="ph-bold ph-check-circle"></i> Restored to Active</span>` :
            `<span class="archive-batch-badge archive-badge-archived"><i class="ph-bold ph-archive"></i> Archived (Hidden)</span>`;

          const flushedDate = b.flushed_at ? new Date(b.flushed_at).toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'Unknown date';
          const restoredDate = b.restored_at ? new Date(b.restored_at).toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : null;

          const tb = b.table_breakdown || {};
          const samples = (b.sample_events && b.sample_events.length > 0) ? 
            `<div class="archive-batch-samples"><i class="ph-bold ph-info"></i> Sample Events: <strong>${b.sample_events.map(s => escapeHtml(s)).join(', ')}</strong></div>` : '';

          return `
            <div class="archive-batch-card" id="archiveCard_${b.batch_id}">
              <div class="archive-batch-header">
                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                  <span class="archive-batch-id">${escapeHtml(b.batch_id)}</span>
                  <span class="archive-batch-badge" style="${modeBadgeStyle}">${modeLabel}</span>
                  ${statusBadge}
                </div>
                <div style="font-size: 0.72rem; color: #64748b; font-weight: 500;">
                  <i class="ph-bold ph-clock"></i> ${flushedDate}
                </div>
              </div>

              <div class="archive-pill-list">
                <span class="archive-count-pill"><strong>${tb.events || 0}</strong> Events</span>
                <span class="archive-count-pill"><strong>${tb.poster_requests || 0}</strong> Requests</span>
                <span class="archive-count-pill"><strong>${tb.event_reports || 0}</strong> Reports</span>
                <span class="archive-count-pill"><strong>${b.files_archived || 0}</strong> Files</span>
                <span class="archive-count-pill"><strong>${b.total_records || 0}</strong> Total Items</span>
              </div>

              ${samples}

              ${restoredDate ? `<div style="font-size: 0.72rem; color: #059669; margin-bottom: 0.4rem;"><i class="ph-bold ph-arrow-counter-clockwise"></i> Last restored: ${restoredDate}</div>` : ''}

              <div class="archive-card-actions">
                <button type="button" class="btn-inspect-batch" onclick="toggleArchiveDetails('${b.batch_id}')">
                  <i class="ph-bold ph-list-dashes"></i> Inspect Records
                </button>
                <button type="button" class="btn-restore-batch" id="btnRestore_${b.batch_id}" onclick="restoreArchiveBatch('${b.batch_id}')">
                  <i class="ph-bold ph-arrow-counter-clockwise"></i> ${isRestored ? 'Retrieve & Restore Again' : 'Retrieve / Restore Data'}
                </button>
              </div>

              <div id="archiveDetails_${b.batch_id}" style="display: none; margin-top: 0.75rem; border-top: 1px dashed #e2e8f0; padding-top: 0.65rem;"></div>
            </div>
          `;
        }).join('');

      } catch (err) {
        console.error('Failed to load archives:', err);
        container.innerHTML = `
          <div style="text-align: center; padding: 2rem; color: #dc2626;">
            <i class="ph-bold ph-warning-circle" style="font-size: 1.5rem;"></i>
            <p style="margin-top: 0.5rem; font-size: 0.825rem;">Failed to load system archives: ${escapeHtml(err.message)}</p>
          </div>
        `;
      }
    }

    async function toggleArchiveDetails(batchId) {
      const targetDiv = document.getElementById('archiveDetails_' + batchId);
      if (!targetDiv) return;

      if (targetDiv.style.display !== 'none') {
        targetDiv.style.display = 'none';
        return;
      }

      targetDiv.style.display = 'block';
      targetDiv.innerHTML = `
        <div style="text-align: center; padding: 1rem; color: #64748b; font-size: 0.75rem;">
          <i class="ph ph-spinner-gap ph-spin"></i> Fetching itemized records...
        </div>
      `;

      try {
        const res = await YTR_API.system.getArchiveDetails(batchId);
        if (res.status !== 'success' || !res.data) {
          targetDiv.innerHTML = `<div style="color: #dc2626; font-size: 0.75rem;">Could not load details.</div>`;
          return;
        }

        const data = res.data;
        const records = data.records || [];
        const files = data.files || [];

        let rowsHtml = '';
        if (records.length === 0) {
          rowsHtml = `<tr><td colspan="3" style="text-align: center; color: #94a3b8; padding: 0.5rem;">No serialized records found.</td></tr>`;
        } else {
          rowsHtml = records.slice(0, 30).map(r => {
            const rd = r.record_data || {};
            const titleOrName = rd.title || rd.name || rd.event_name || rd.subject || rd.filename || `ID: ${r.record_id}`;
            return `
              <tr style="border-bottom: 1px solid #f1f5f9; font-size: 0.75rem;">
                <td style="padding: 0.35rem 0.5rem; font-weight: 600; color: #334155;">${escapeHtml(r.table_name)}</td>
                <td style="padding: 0.35rem 0.5rem; color: #64748b; font-family: monospace;">#${escapeHtml(String(r.record_id))}</td>
                <td style="padding: 0.35rem 0.5rem; color: #0f172a;">${escapeHtml(titleOrName)}</td>
              </tr>
            `;
          }).join('');
          if (records.length > 30) {
            rowsHtml += `<tr><td colspan="3" style="text-align: center; color: #64748b; font-size: 0.72rem; padding: 0.4rem;">...and ${records.length - 30} more archived records</td></tr>`;
          }
        }

        targetDiv.innerHTML = `
          <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; padding: 0.5rem;">
            <div style="font-size: 0.78rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem; display: flex; justify-content: space-between;">
              <span>Itemized Archive Contents (${records.length} records, ${files.length} files)</span>
              <a href="#" onclick="event.preventDefault(); document.getElementById('archiveDetails_${batchId}').style.display='none';" style="color: #64748b; font-size: 0.72rem;">Hide</a>
            </div>
            <div style="max-height: 180px; overflow-y: auto;">
              <table style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead>
                  <tr style="background: #f8fafc; font-size: 0.7rem; color: #64748b; text-transform: uppercase;">
                    <th style="padding: 0.3rem 0.5rem;">Table</th>
                    <th style="padding: 0.3rem 0.5rem;">Record ID</th>
                    <th style="padding: 0.3rem 0.5rem;">Title / Identifier</th>
                  </tr>
                </thead>
                <tbody>${rowsHtml}</tbody>
              </table>
            </div>
          </div>
        `;
      } catch (err) {
        targetDiv.innerHTML = `<div style="color: #dc2626; font-size: 0.75rem;">Error loading details: ${escapeHtml(err.message)}</div>`;
      }
    }

    async function restoreArchiveBatch(batchId) {
      const confirmed = confirm(
        `Are you sure you want to retrieve and restore batch "${batchId}"?\n\n` +
        `This will copy all archived events, reports, poster requests, deliverables, and files directly back into the active database tables.`
      );
      if (!confirmed) return;

      const btn = document.getElementById('btnRestore_' + batchId);
      const originalText = btn ? btn.innerHTML : '';
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<i class="ph ph-spinner-gap ph-spin"></i> Retrieving...`;
      }

      try {
        const res = await YTR_API.system.restoreArchive(batchId);
        if (res.status === 'success') {
          alert('Success! ' + (res.message || `Archive batch ${batchId} was successfully retrieved and restored.`));
          // Refresh active data
          loadMetrics();
          loadAccounts(activeFilter);
          loadChapterOptions();
          loadSystemSummaryStats();
          loadArchivesList();
        } else {
          alert('Restoration Failed: ' + (res.message || 'An error occurred during retrieval.'));
          if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalText;
          }
        }
      } catch (err) {
        alert('Restoration Error: ' + err.message);
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = originalText;
        }
      }
    }

    function checkExecutiveAccess() {
      try {
        const rawUser = localStorage.getItem('ytr_user');
        if (rawUser) {
          const u = JSON.parse(rawUser);
          const execPositions = ['Executive Director', 'Regional Director (National)'];
          const isExec = execPositions.includes(u.position) || u.email === 'admin@ytr.org';
          const flushBtn = document.getElementById('btnHeaderFlush');
          const retrieveBtn = document.getElementById('btnHeaderRetrieve');
          if (flushBtn) {
            flushBtn.style.display = isExec ? 'inline-flex' : 'none';
          }
          if (retrieveBtn) {
            retrieveBtn.style.display = isExec ? 'inline-flex' : 'none';
          }
        }
      } catch (e) {}
    }

    // Initialize on page load
    window.addEventListener('DOMContentLoaded', () => {
      checkExecutiveAccess();
      loadMetrics();
      loadChapterOptions();
      loadAccounts('pending');
    });
  </script>
</body>
</html>