<?php $current_page = strtolower(basename($_SERVER['PHP_SELF'])); ?>

<!-- Dark overlay for mobile -->
<div id="mobile-overlay" class="mobile-overlay"></div>

<aside id="sidebar" class="sidebar">
  <!-- Mobile Close Button -->
  <button id="sidebar-close" class="sidebar-close" type="button" aria-label="Close navigation"><i class="ph ph-x"></i></button>

  <div class="sidebar-header">
    <img src="assets/images/LOGO.jpg" alt="YTR Logo" class="sidebar-logo">
    <div class="sidebar-title">
      <h1 class="font-roboto" style="font-size: 1.1rem; line-height: 1.2; margin: 0 0 2px 0;">Youth on The Rock</h1>
      <p style="color: #38bdf8; font-weight: 600; font-size: 0.75rem; margin: 0; line-height: 1.2;">National Portal</p>
    </div>
  </div>

  <nav class="nav-menu font-roboto">
    <a href="dashboard.php" class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
      <i class="ph ph-squares-four"></i> Dashboard
    </a>
    <a href="Announcement.php" class="nav-link <?php echo ($current_page == 'announcement.php') ? 'active' : ''; ?>">
      <i class="ph ph-megaphone"></i> Announcements
    </a>
    <a href="Request.php" class="nav-link <?php echo ($current_page == 'request.php') ? 'active' : ''; ?>">
      <i class="ph ph-file-text"></i> Poster Request
    </a>
    <a href="Proposal.php" class="nav-link <?php echo ($current_page == 'proposal.php') ? 'active' : ''; ?>">
      <i class="ph ph-file-plus"></i> Event Proposal
    </a>
    <a href="Report.php" class="nav-link <?php echo ($current_page == 'report.php') ? 'active' : ''; ?>">
      <i class="ph ph-chart-bar"></i> Chapter Reports
    </a>
    <a href="Manage.php" class="nav-link <?php echo ($current_page == 'manage.php') ? 'active' : ''; ?>">
      <i class="ph ph-user-gear"></i> Manage Account
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="admin-badge">
      <img src="assets/images/LOGO.jpg" alt="YTR" class="icon" style="object-fit: contain; padding: 2px;">
      <div class="sidebar-title">
        <h1 class="font-roboto" style="font-size: 0.85rem; color: #ffffff; margin: 0; line-height: 1.2;">YTR NATIONAL</h1>
        <p style="color: #f59e0b; font-size: 0.75rem; margin: 0; line-height: 1.2;">System Admin</p>
      </div>
    </div>
    <a href="login.php" class="sign-out" onclick="if(typeof ytrGlobalSignOut==='function'){ytrGlobalSignOut(event);}else{localStorage.clear();document.cookie='ytr_role=; path=/; max-age=0;';document.cookie='ytr_user_id=; path=/; max-age=0;';}">
      <div class="icon"><i class="ph ph-sign-out"></i></div>
      Sign Out
    </a>
  </div>
</aside>