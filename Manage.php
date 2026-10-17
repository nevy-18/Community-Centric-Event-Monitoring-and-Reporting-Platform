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
          <div>
            <button type="button" class="btn btn-outline font-roboto" onclick="openSystemFlushModal()" style="border-color: #fca5a5; color: #b91c1c; background: #fff5f5; font-weight: 600; padding: 0.55rem 1.15rem; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s;">
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
                <button class="tab-btn" onclick="switchAccountTab('national_admins', this)"><i class="ph ph-shield-check"></i> National Admins</button>
                <button class="tab-btn" onclick="switchAccountTab('chapter_admins', this)"><i class="ph ph-user-gear"></i> Chapter Admins</button>
                <button class="tab-btn" onclick="switchAccountTab('disabled', this)"><i class="ph ph-prohibit"></i> Disabled</button>
              </div>

              <!-- Data Table -->
              <div class="table-responsive">
                <table class="font-roboto">
                  <thead>
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
                <label class="form-label font-roboto">Assign Role</label>
                <div class="radio-group font-roboto">
                  <label class="radio-label">
                    <input type="radio" name="assign_role" id="roleNational" value="national_admin"> National Admin
                  </label>
                  <label class="radio-label">
                    <input type="radio" name="assign_role" id="roleChapter" value="chapter_admin" checked> Chapter Admin
                  </label>
                </div>
              </div>

              <div class="form-group">
                <label class="form-label font-roboto">Assign Chapter</label>
                <select class="form-select font-roboto" id="assignChapterSelect">
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
            </div>

          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- SYSTEM DATA FLUSH MODAL -->
  <div id="systemFlushModal" class="modal-overlay hidden" style="display: none;">
    <div class="modal-md">
      <!-- Modal Header -->
      <div class="modal-header-banner" style="background: #fff1f2; border-bottom: 1px solid #fecdd3;">
        <div style="display: flex; align-items: center; gap: 0.65rem;">
          <div style="background: #fee2e2; color: #dc2626; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
            <i class="ph-bold ph-warning-octagon"></i>
          </div>
          <div>
            <h3 style="color: #991b1b; margin: 0; font-size: 1rem;">System Maintenance: Data Flush</h3>
            <p style="color: #b91c1c; margin: 0; font-size: 0.75rem;">Purge operational activity, reset test records, or restore clean demo state.</p>
          </div>
        </div>
        <button type="button" onclick="closeSystemFlushModal()"><i class="ph ph-x"></i></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body-scroll" style="background: #ffffff;">
        <div style="font-size: 0.825rem; color: #475569; margin-bottom: 0.5rem;">
          <strong>Current System Records:</strong>
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
                <p>Clears all events, proposals, poster requests, timeline deliverables, reports, notifications, and audit logs. <strong>User accounts and registered chapters remain safe and intact.</strong></p>
              </div>
            </label>

            <label class="flush-option-label" id="optLabelFull" onclick="selectFlushMode('full')">
              <input type="radio" name="flush_mode_radio" value="full">
              <div class="flush-option-content">
                <h5>🔄 Full Factory Reset</h5>
                <p>Completely resets all database tables to pristine baseline state (default chapters, default template steps, and default administrative logins).</p>
              </div>
            </label>

            <label class="flush-option-label" id="optLabelDemo" onclick="selectFlushMode('demo')">
              <input type="radio" name="flush_mode_radio" value="demo">
              <div class="flush-option-content">
                <h5>🚀 Full Reset &amp; Seed Demo Data</h5>
                <p>Resets the database and immediately populates realistic sample proposals, poster requests, and conducted events with 2-week compliance timers for testing.</p>
              </div>
            </label>
          </div>
        </div>

        <div class="flush-confirm-box">
          <label><i class="ph-bold ph-shield-warning"></i> Safety Verification: Type "FLUSH" to unlock</label>
          <input type="text" id="flushConfirmationInput" class="flush-confirm-input" placeholder="Type FLUSH to confirm" oninput="checkFlushConfirmation()">
        </div>
      </div>

      <!-- Modal Footer -->
      <div style="padding: 0.85rem 1.25rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 0.5rem;">
        <button type="button" class="btn btn-outline font-roboto" onclick="closeSystemFlushModal()">Cancel</button>
        <button type="button" class="btn btn-danger font-roboto" id="btnExecuteFlush" disabled onclick="executeSystemFlush()" style="background: #dc2626; border-color: #b91c1c; color: white; display: inline-flex; align-items: center; gap: 0.4rem; opacity: 0.5; cursor: not-allowed;">
          <i class="ph-bold ph-trash"></i> <span id="flushBtnText">Execute Flush</span>
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

    // Load and Render Accounts Table
    async function loadAccounts(filter = activeFilter) {
      const tbody = document.getElementById('accountsTableBody');
      tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 2rem; color: #64748b;">Loading accounts...</td></tr>';

      const res = await YTR_API.accounts.list(filter);
      if (res.status === 'success' && Array.isArray(res.data)) {
        if (res.data.length === 0) {
          tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 2rem; color: #64748b;">No accounts found for this filter.</td></tr>';
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

          row.innerHTML = `
            <td>
              <div class="td-user">
                <img src="https://placehold.co/32x32/e2e8f0/64748b" alt="Avatar">
                <span class="font-roboto">${user.full_name}</span>
              </div>
            </td>
            <td class="td-sub">${user.email}</td>
            <td class="td-sub">${user.chapter_name || 'National HQ'}</td>
            <td style="text-align: center;"><span class="${roleBadgeClass} font-roboto">${roleLabel}</span></td>
            <td class="td-sub" style="font-size: 0.8rem;">${reqDate}</td>
            <td style="text-align: center;"><span class="${statusBadgeClass} font-roboto">${user.account_status.toUpperCase()}</span></td>
            <td style="text-align: center;">
              <button class="btn btn-primary btn-sm font-roboto" style="margin: 0 auto;" onclick='selectUserForReview(${JSON.stringify(user)})'>Review</button>
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

    // Switch Tab
    function switchAccountTab(filter, btn) {
      activeFilter = filter;
      document.querySelectorAll('.tabs-container .tab-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      loadAccounts(filter);
    }

    // Select User into Review Panel
    function selectUserForReview(user) {
      selectedUserId = user.id;
      document.getElementById('reviewUserName').textContent = user.full_name;
      document.getElementById('reviewUserEmail').textContent = user.email;
      document.getElementById('reviewUserBadge').textContent = user.account_status.toUpperCase();
      document.getElementById('reviewUserChapter').textContent = user.chapter_name || 'National HQ';
      document.getElementById('reviewUserRole').textContent = user.role === 'national_admin' ? 'National Admin' : 'Chapter Admin';
      document.getElementById('reviewUserReason').textContent = `Account request submitted for ${user.full_name} (${user.email}). Review authorization and confirm chapter assignment below.`;

      if (user.role === 'national_admin') {
        document.getElementById('roleNational').checked = true;
      } else {
        document.getElementById('roleChapter').checked = true;
      }

      if (user.chapter_id) {
        document.getElementById('assignChapterSelect').value = user.chapter_id;
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

      const role = document.querySelector('input[name="assign_role"]:checked').value;
      const chapterId = document.getElementById('assignChapterSelect').value;

      if (role === 'chapter_admin' && !chapterId) {
        alert('Please select a chapter for the Chapter Admin.');
        return;
      }

      const res = await YTR_API.accounts.approve(selectedUserId, role, chapterId);
      if (res.status === 'success') {
        alert('Account approved successfully!');
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
    // System Maintenance & Data Flush Handlers
    // ==========================================
    let currentFlushMode = 'activity';

    async function openSystemFlushModal() {
      const modal = document.getElementById('systemFlushModal');
      const input = document.getElementById('flushConfirmationInput');
      
      input.value = '';
      selectFlushMode('activity');
      checkFlushConfirmation();

      modal.style.display = 'flex';
      modal.classList.remove('hidden');

      // Fetch live counts
      const res = await YTR_API.system.getSummary();
      if (res.status === 'success' && res.data && res.data.table_counts) {
        const tc = res.data.table_counts;
        document.getElementById('statEvents').textContent = tc.events || 0;
        document.getElementById('statRequests').textContent = tc.poster_requests || 0;
        document.getElementById('statReports').textContent = tc.event_reports || 0;
        document.getElementById('statUsers').textContent = tc.users || 0;
        document.getElementById('statFiles').textContent = res.data.uploaded_files_count || 0;
      }
    }

    function closeSystemFlushModal() {
      const modal = document.getElementById('systemFlushModal');
      modal.style.display = 'none';
      modal.classList.add('hidden');
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
      const text = (input.value || '').trim().toUpperCase();
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

    async function executeSystemFlush() {
      const input = document.getElementById('flushConfirmationInput');
      const btn = document.getElementById('btnExecuteFlush');
      const btnText = document.getElementById('flushBtnText');
      const text = (input.value || '').trim().toUpperCase();

      if (text !== 'FLUSH') {
        alert('Please type "FLUSH" to confirm this action.');
        return;
      }

      btn.disabled = true;
      btn.style.opacity = '0.6';
      btnText.textContent = 'Flushing...';

      try {
        const res = await YTR_API.system.flush(currentFlushMode, 'FLUSH');
        if (res.status === 'success') {
          alert('Success! ' + (res.message || 'System data has been flushed.'));
          closeSystemFlushModal();
          // Reload page state
          loadMetrics();
          loadAccounts(activeFilter);
          loadChapterOptions();
        } else {
          alert('Flush Failed: ' + (res.message || 'An error occurred during flush.'));
        }
      } catch (err) {
        alert('Flush Error: ' + err.message);
      } finally {
        btn.disabled = false;
        btn.style.opacity = '1';
        btnText.textContent = 'Execute Flush';
        checkFlushConfirmation();
      }
    }

    // Initialize on page load
    window.addEventListener('DOMContentLoaded', () => {
      loadMetrics();
      loadChapterOptions();
      loadAccounts('pending');
    });
  </script>
</body>
</html>