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
      <div class="dropdown-divider"></div>
      <a href="login.php" class="dropdown-item text-rose"><i class="ph ph-sign-out"></i> Sign Out</a>
    </div>
  </div>
</header>

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
          if (pRole) pRole.textContent = u.role === 'chapter_admin' ? (u.chapter_name || 'Chapter Admin') : 'National Administrator';

          const topbarBadge = document.querySelector('.topbar-left .role-badge');
          if (topbarBadge) {
            if (u.role === 'chapter_admin') {
              topbarBadge.textContent = 'Chapter: ' + (u.chapter_name || 'Branch');
              topbarBadge.style.cssText = 'background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-weight: 700;';
            } else {
              topbarBadge.textContent = 'National Admin';
            }
          }

          // If Chapter Admin, customize profile dropdown links
          if (u.role === 'chapter_admin') {
            const dropdown = document.getElementById('profile-dropdown');
            if (dropdown) {
              dropdown.innerHTML = `
                <div class="dropdown-header">
                  <h4 style="font-size: 0.875rem; color: #0f172a; font-weight: bold;">${u.full_name}</h4>
                  <p style="font-size: 0.75rem; color: #64748b;">${u.email} (${u.chapter_name || 'Chapter'})</p>
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

    const toggleSidebar = () => {
      sidebar.classList.toggle('open');
      overlay.classList.toggle('open');
    };

    if(mobileMenuBtn) mobileMenuBtn.addEventListener('click', toggleSidebar);
    if(sidebarClose) sidebarClose.addEventListener('click', toggleSidebar);
    if(overlay) overlay.addEventListener('click', toggleSidebar);

    // Close menus on outside click
    window.addEventListener('click', (e) => {
      if (!profileBtn.contains(e.target)) profileDropdown.classList.remove('show');
      if (!notifBtn.contains(e.target)) notifDropdown.classList.remove('show');
    });
  });
</script>