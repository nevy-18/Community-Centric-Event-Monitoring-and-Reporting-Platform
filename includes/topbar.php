<header class="topbar">
  <div class="topbar-left font-roboto">
    <button id="mobile-menu-btn" class="mobile-menu-btn"><i class="ph ph-list"></i></button>
    <span class="role-badge">National</span>
  </div>

  <div class="topbar-right">
    <div class="topbar-icons">
      <!-- Interactive Notification Bell -->
      <div style="position: relative;">
        <button id="notification-bell-btn" class="icon-btn" title="View Due Dates & Notifications">
          <i class="ph ph-bell"></i>
          <span id="notif-badge-dot" class="notification-dot" style="display: none;"></span>
        </button>

        <!-- Notification Dropdown Menu -->
        <div id="notifications-dropdown" class="dropdown-menu font-roboto" style="width: 20rem; right: -5rem;">
          <div class="dropdown-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
              <h4 style="font-size: 0.85rem; color: #0f172a; font-weight: 800;">Milestones & Due Dates</h4>
              <p style="font-size: 0.7rem; color: #64748b;">Pending actions & deadlines</p>
            </div>
            <span id="notif-total-badge" class="badge due-soon" style="font-size: 0.65rem;">0</span>
          </div>
          <div id="notif-items-container" style="max-height: 280px; overflow-y: auto;">
            <div style="padding: 1.5rem 1rem; text-align: center; color: #64748b; font-size: 0.75rem;">
              <i class="ph ph-check-circle" style="font-size: 1.5rem; color: #10b981; margin-bottom: 0.25rem;"></i>
              <p>All milestones on track.</p>
            </div>
          </div>
          <div class="dropdown-divider"></div>
          <a href="Proposal.php" class="dropdown-item" style="font-size: 0.75rem; text-align: center; justify-content: center; font-weight: 700; color: #2563eb;">
            View All Proposals & Deadlines →
          </a>
        </div>
      </div>

      <button class="icon-btn"><i class="ph ph-chat-circle"></i></button>
    </div>

    <button id="profile-btn" class="profile-btn">
      <div class="profile-avatar">HJ</div>
      <div class="profile-info font-roboto">
        <h4>Hamilton Jones Dela</h4>
        <p>Project Coordinator</p>
      </div>
      <i class="ph ph-caret-down" style="color: #6b7280; font-size: 0.875rem;"></i>
    </button>

    <div id="profile-dropdown" class="dropdown-menu font-roboto">
      <div class="dropdown-header">
        <h4 style="font-size: 0.875rem; color: #0f172a; font-weight: bold;">Hamilton Jones Dela</h4>
        <p style="font-size: 0.75rem; color: #64748b;">admin@ytr.org</p>
      </div>
      <a href="Manage.php" class="dropdown-item"><i class="ph ph-user-circle"></i> Manage Accounts</a>
      <a href="Proposal.php" class="dropdown-item"><i class="ph ph-file-plus"></i> Chapter Proposals</a>
      <a href="Report.php" class="dropdown-item"><i class="ph ph-file-text"></i> Event Reports</a>

      <!-- Executive Tools (Restricted to Executive Director & Regional Director (National)) -->
      <div id="executive-tools-dropdown-section" style="display: none;">
        <div class="dropdown-divider"></div>
        <div style="padding: 0.35rem 0.75rem 0.2rem; font-size: 0.68rem; font-weight: 800; color: #dc2626; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.35rem;">
          <i class="ph-bold ph-shield-check"></i> Executive Control
        </div>
        <a href="#" class="dropdown-item" onclick="triggerSystemBackup(event)" style="color: #1e3a8a;">
          <i class="ph-bold ph-download-simple" style="color: #2563eb;"></i> Backup System Data
        </a>
        <a href="#" class="dropdown-item text-rose" onclick="openTopLevelFlushModal(event)">
          <i class="ph-bold ph-arrow-counter-clockwise" style="color: #dc2626;"></i> Reset System Data
        </a>
      </div>

      <div class="dropdown-divider"></div>
      <a href="login.php" class="dropdown-item text-rose" onclick="localStorage.removeItem('ytr_user'); localStorage.removeItem('ytr_role'); document.cookie='ytr_role=; path=/; max-age=0;'"><i class="ph ph-sign-out"></i> Sign Out</a>
    </div>
  </div>
</header>

<!-- TOPBAR SYSTEM DATA FLUSH MODAL (Accessible by Executive Director & Regional Director) -->
<div id="topbarSystemFlushModal" class="modal-overlay hidden" style="display: none;">
  <div class="modal-md">
    <!-- Modal Header -->
    <div class="modal-header-banner" style="background: #fff1f2; border-bottom: 1px solid #fecdd3;">
      <div style="display: flex; align-items: center; gap: 0.65rem;">
        <div style="background: #fee2e2; color: #dc2626; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
          <i class="ph-bold ph-warning-octagon"></i>
        </div>
        <div>
          <h3 style="color: #991b1b; margin: 0; font-size: 1rem;">Executive Control: Reset System Data</h3>
          <p style="color: #b91c1c; margin: 0; font-size: 0.75rem;">Executive Director / Regional Director (National) Authorization</p>
        </div>
      </div>
      <button type="button" onclick="closeTopLevelFlushModal()"><i class="ph ph-x"></i></button>
    </div>

    <!-- Modal Body -->
    <div class="modal-body-scroll" style="background: #ffffff;">
      <div style="font-size: 0.825rem; color: #475569; margin-bottom: 0.5rem;">
        <strong>Live Database Records:</strong>
      </div>
      <div class="flush-stat-grid" id="topbarFlushStatGrid">
        <div class="flush-stat-pill"><strong id="tbStatEvents">--</strong><span>Events</span></div>
        <div class="flush-stat-pill"><strong id="tbStatRequests">--</strong><span>Requests</span></div>
        <div class="flush-stat-pill"><strong id="tbStatReports">--</strong><span>Reports</span></div>
        <div class="flush-stat-pill"><strong id="tbStatUsers">--</strong><span>Users</span></div>
        <div class="flush-stat-pill"><strong id="tbStatFiles">--</strong><span>Files</span></div>
      </div>

      <div style="margin-top: 1rem;">
        <label style="font-size: 0.825rem; font-weight: 700; color: #1e293b;">Choose Reset Mode:</label>
        <div class="flush-option-list">
          <label class="flush-option-label active" id="tbOptLabelActivity" onclick="selectTopbarFlushMode('activity')">
            <input type="radio" name="tb_flush_mode" value="activity" checked>
            <div class="flush-option-content">
              <h5>🧹 Flush Operational Activity</h5>
              <p>Wipes events, proposals, requests, milestones, reports, and logs. <strong>User accounts and chapters stay intact.</strong></p>
            </div>
          </label>

          <label class="flush-option-label" id="tbOptLabelFull" onclick="selectTopbarFlushMode('full')">
            <input type="radio" name="tb_flush_mode" value="full">
            <div class="flush-option-content">
              <h5>🔄 Factory Reset to Baseline</h5>
              <p>Restores original pristine chapters and default administrative accounts.</p>
            </div>
          </label>

          <label class="flush-option-label" id="tbOptLabelDemo" onclick="selectTopbarFlushMode('demo')">
            <input type="radio" name="tb_flush_mode" value="demo">
            <div class="flush-option-content">
              <h5>🚀 Reset &amp; Seed Demo Data</h5>
              <p>Populates fresh demonstration events, compliance countdowns, and proposals.</p>
            </div>
          </label>
        </div>
      </div>

      <div class="flush-confirm-box">
        <label><i class="ph-bold ph-shield-warning"></i> Security Check: Type "FLUSH" to proceed</label>
        <input type="text" id="topbarFlushConfirmInput" class="flush-confirm-input" placeholder="Type FLUSH to confirm" oninput="checkTopbarFlushInput()">
      </div>
    </div>

    <!-- Modal Footer -->
    <div style="padding: 0.85rem 1.25rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 0.5rem;">
      <button type="button" class="btn btn-outline font-roboto" onclick="closeTopLevelFlushModal()">Cancel</button>
      <button type="button" class="btn btn-danger font-roboto" id="btnTopbarExecuteFlush" disabled onclick="executeTopbarFlush()" style="background: #dc2626; border-color: #b91c1c; color: white; display: inline-flex; align-items: center; gap: 0.4rem; opacity: 0.5; cursor: not-allowed;">
        <i class="ph-bold ph-trash"></i> <span id="tbFlushBtnText">Confirm Reset</span>
      </button>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Profile Dropdown
    const profileBtn = document.getElementById('profile-btn');
    const profileDropdown = document.getElementById('profile-dropdown');
    
    profileBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      notifDropdown.classList.remove('show');
      profileDropdown.classList.toggle('show');
    });

    // Notification Dropdown
    const notifBtn = document.getElementById('notification-bell-btn');
    const notifDropdown = document.getElementById('notifications-dropdown');
    const notifDot = document.getElementById('notif-badge-dot');
    const notifTotalBadge = document.getElementById('notif-total-badge');
    const notifContainer = document.getElementById('notif-items-container');

    notifBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      profileDropdown.classList.remove('show');
      notifDropdown.classList.toggle('show');
    });

    // Sync Topbar with Logged In User
    try {
      const rawUser = localStorage.getItem('ytr_user');
      if (rawUser) {
        const u = JSON.parse(rawUser);
        if (u) {
          const initials = u.full_name ? u.full_name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : 'US';
          const avatar = document.querySelector('.profile-avatar');
          if (avatar) avatar.textContent = initials;
          
          const pName = document.querySelector('.profile-info h4');
          if (pName) pName.textContent = u.full_name || 'User';
          
          const pRole = document.querySelector('.profile-info p');
          if (pRole) pRole.textContent = u.position || (u.role === 'chapter_admin' ? (u.chapter_name || 'Chapter Admin') : 'National Administrator');

          const topbarBadge = document.querySelector('.topbar-left .role-badge');
          if (topbarBadge) {
            if (u.role === 'chapter_admin') {
              topbarBadge.textContent = (u.chapter_name || 'Chapter') + (u.position ? ' • ' + u.position : '');
              topbarBadge.style.cssText = 'background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-weight: 700;';
            } else {
              topbarBadge.textContent = 'National' + (u.position ? ' • ' + u.position : '');
            }
          }

          // Check Executive Authorization for Reset & Backup:
          // Restricted to: Executive Director, Regional Director (National), or root admin
          const execPositions = ['Executive Director', 'Regional Director (National)'];
          const isExec = execPositions.includes(u.position) || u.email === 'admin@ytr.org';
          const execSection = document.getElementById('executive-tools-dropdown-section');
          if (execSection) {
            execSection.style.display = isExec ? 'block' : 'none';
          }

          // If Chapter Admin, customize profile dropdown links
          if (u.role === 'chapter_admin') {
            const dropdown = document.getElementById('profile-dropdown');
            if (dropdown) {
              dropdown.innerHTML = `
                <div class="dropdown-header">
                  <h4 style="font-size: 0.875rem; color: #0f172a; font-weight: bold;">${u.full_name}</h4>
                  <p style="font-size: 0.75rem; color: #64748b;">${u.position || 'Chapter Officer'} (${u.chapter_name || 'Chapter'})</p>
                </div>
                <a href="ChapterDashboard.php" class="dropdown-item"><i class="ph ph-squares-four"></i> Chapter Dashboard</a>
                <a href="ChapterProposal.php" class="dropdown-item"><i class="ph ph-file-plus"></i> My Event Proposals</a>
                <a href="ChapterReport.php" class="dropdown-item"><i class="ph ph-file-arrow-up"></i> Submit Event Report</a>
                <a href="ChapterRequest.php" class="dropdown-item"><i class="ph ph-image"></i> My Poster Requests</a>
                <div class="dropdown-divider"></div>
                <a href="login.php" class="dropdown-item text-rose" onclick="localStorage.removeItem('ytr_user'); localStorage.removeItem('ytr_role'); document.cookie='ytr_role=; path=/; max-age=0;'"><i class="ph ph-sign-out"></i> Sign Out</a>
              `;
            }
          }
        }
      }
    } catch (e) {
      console.warn('Could not sync user profile in topbar:', e);
    }

    // Load Live Notification Alerts from Python Backend
    async function loadNotifications() {
      try {
        const client = window.YTR_API;
        if (!client) return;
        const res = await client.notifications.getSummary();
        if (res.status === 'success' && res.data) {
          const count = res.data.unread_count || 0;
          if (count > 0) {
            notifDot.style.display = 'block';
            notifTotalBadge.textContent = count;
            notifTotalBadge.style.display = 'inline-block';
            
            notifContainer.innerHTML = '';
            res.data.alerts.forEach(item => {
              const div = document.createElement('a');
              div.href = item.link || '#';
              div.className = 'dropdown-item';
              div.style.cssText = 'padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; display: flex; flex-direction: column; align-items: flex-start; gap: 0.25rem; text-decoration: none;';
              
              div.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                  <strong style="font-size: 0.75rem; color: #0f172a;">${item.title}</strong>
                  <span class="${item.badge_class || 'badge pending'}" style="font-size: 0.6rem;">${item.badge}</span>
                </div>
                <p style="font-size: 0.7rem; color: #475569; margin: 0; line-height: 1.3;">${item.message}</p>
              `;
              notifContainer.appendChild(div);
            });
          } else {
            notifDot.style.display = 'none';
            notifTotalBadge.style.display = 'none';
          }
        }
      } catch (err) {
        console.warn('Could not load notifications:', err);
      }
    }

    loadNotifications();

    // Mobile Sidebar
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const sidebar = document.getElementById('sidebar');
    const sidebarClose = document.getElementById('sidebar-close');
    const overlay = document.getElementById('mobile-overlay');

    const toggleSidebar = (forceState) => {
      if (!sidebar) return;
      const willOpen = typeof forceState === 'boolean' ? forceState : !sidebar.classList.contains('open');
      sidebar.classList.toggle('open', willOpen);
      if (overlay) overlay.classList.toggle('open', willOpen);
      document.body.classList.toggle('sidebar-mobile-open', willOpen);
    };

    if(mobileMenuBtn) mobileMenuBtn.addEventListener('click', () => toggleSidebar());
    if(sidebarClose) sidebarClose.addEventListener('click', () => toggleSidebar(false));
    if(overlay) overlay.addEventListener('click', () => toggleSidebar(false));

    window.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && sidebar && sidebar.classList.contains('open')) {
        toggleSidebar(false);
      }
    });

    // Close menus on outside click
    window.addEventListener('click', (e) => {
      if (profileBtn && !profileBtn.contains(e.target)) profileDropdown.classList.remove('show');
      if (notifBtn && !notifBtn.contains(e.target)) notifDropdown.classList.remove('show');
    });
  });

  // ==========================================
  // Executive Backup & System Reset Handlers
  // (Available to Executive Director / Regional Director (National))
  // ==========================================
  let currentTopbarFlushMode = 'activity';

  function triggerSystemBackup(e) {
    if (e) e.preventDefault();
    const dropdown = document.getElementById('profile-dropdown');
    if (dropdown) dropdown.classList.remove('show');

    // Confirm download format
    const choice = confirm("Download System Backup as JSON?\n(Click 'OK' for JSON archive, or 'Cancel' for SQL dump format)");
    const fmt = choice ? 'json' : 'sql';

    const url = (window.YTR_API && window.YTR_API.system)
      ? window.YTR_API.system.getBackupUrl(fmt)
      : `https://ytr-backend.onrender.com/api/system/backup?format=${fmt}&download=1`;

    const a = document.createElement('a');
    a.href = url;
    a.download = '';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  }

  async function openTopLevelFlushModal(e) {
    if (e) e.preventDefault();
    const dropdown = document.getElementById('profile-dropdown');
    if (dropdown) dropdown.classList.remove('show');

    if (typeof openSystemFlushModal === 'function') {
      openSystemFlushModal();
      return;
    }

    const modal = document.getElementById('topbarSystemFlushModal');
    if (!modal) return;

    document.getElementById('topbarFlushConfirmInput').value = '';
    selectTopbarFlushMode('activity');
    checkTopbarFlushInput();

    modal.style.display = 'flex';
    modal.classList.remove('hidden');

    try {
      if (window.YTR_API && window.YTR_API.system) {
        const res = await window.YTR_API.system.getSummary();
        if (res.status === 'success' && res.data && res.data.table_counts) {
          const tc = res.data.table_counts;
          document.getElementById('tbStatEvents').textContent = tc.events || 0;
          document.getElementById('tbStatRequests').textContent = tc.poster_requests || 0;
          document.getElementById('tbStatReports').textContent = tc.event_reports || 0;
          document.getElementById('tbStatUsers').textContent = tc.users || 0;
          document.getElementById('tbStatFiles').textContent = res.data.uploaded_files_count || 0;
        }
      }
    } catch (err) {
      console.warn('Could not load summary for flush modal:', err);
    }
  }

  function closeTopLevelFlushModal() {
    const modal = document.getElementById('topbarSystemFlushModal');
    if (modal) {
      modal.style.display = 'none';
      modal.classList.add('hidden');
    }
  }

  function selectTopbarFlushMode(mode) {
    currentTopbarFlushMode = mode;
    ['activity', 'full', 'demo'].forEach(m => {
      const lbl = document.getElementById('tbOptLabel' + m.charAt(0).toUpperCase() + m.slice(1));
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

  function checkTopbarFlushInput() {
    const input = document.getElementById('topbarFlushConfirmInput');
    const btn = document.getElementById('btnTopbarExecuteFlush');
    const text = (input ? input.value : '').trim().toUpperCase();
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

  async function executeTopbarFlush() {
    const input = document.getElementById('topbarFlushConfirmInput');
    const btn = document.getElementById('btnTopbarExecuteFlush');
    const btnText = document.getElementById('tbFlushBtnText');
    const text = (input ? input.value : '').trim().toUpperCase();

    if (text !== 'FLUSH') {
      alert('Please type "FLUSH" to confirm this action.');
      return;
    }

    btn.disabled = true;
    btn.style.opacity = '0.6';
    btnText.textContent = 'Resetting...';

    try {
      const res = await window.YTR_API.system.flush(currentTopbarFlushMode, 'FLUSH');
      if (res.status === 'success') {
        alert('Success! ' + (res.message || 'System data has been reset.'));
        closeTopLevelFlushModal();
        window.location.reload();
      } else {
        alert('Reset Failed: ' + (res.message || 'An error occurred during reset.'));
      }
    } catch (err) {
      alert('Reset Error: ' + err.message);
    } finally {
      btn.disabled = false;
      btn.style.opacity = '1';
      btnText.textContent = 'Confirm Reset';
      checkTopbarFlushInput();
    }
  }
</script>