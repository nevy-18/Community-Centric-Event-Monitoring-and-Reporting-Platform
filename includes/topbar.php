<header class="topbar">
  <div class="topbar-left font-roboto">
    <button id="mobile-menu-btn" class="mobile-menu-btn"><i class="ph ph-list"></i></button>
    <span class="role-badge">National</span>
  </div>

  <div class="topbar-right">
    <div class="topbar-icons">
      <button class="icon-btn">
        <i class="ph ph-bell"></i>
        <span class="notification-dot"></span>
      </button>
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

    <div id="profile-dropdown" class="dropdown-menu">
      <div class="dropdown-header">
        <h4 style="font-size: 0.875rem; color: #111827; font-weight: bold;">Hamilton Jones Dela</h4>
        <p style="font-size: 0.75rem; color: #6b7280;">hamilton.dela@ytr.org</p>
      </div>
      <a href="#" class="dropdown-item"><i class="ph ph-user-circle"></i> My Profile</a>
      <a href="#" class="dropdown-item"><i class="ph ph-gear"></i> Account Settings</a>
      <div class="dropdown-divider"></div>
      <a href="login.php" class="dropdown-item text-rose"><i class="ph ph-sign-out"></i> Sign Out</a>
    </div>
  </div>
</header>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Dropdown Logic
    const profileBtn = document.getElementById('profile-btn');
    const profileDropdown = document.getElementById('profile-dropdown');
    
    profileBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      profileDropdown.classList.toggle('show');
    });

    // Mobile Sidebar Logic
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

    // Close menus when clicking outside
    window.addEventListener('click', (e) => {
      if (!profileBtn.contains(e.target)) profileDropdown.classList.remove('show');
    });
  });
</script>