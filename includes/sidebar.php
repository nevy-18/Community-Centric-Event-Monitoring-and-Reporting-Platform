<?php $current_page = strtolower(basename($_SERVER['PHP_SELF'])); ?>

<!-- Dark overlay for mobile -->
<div id="mobile-overlay" class="mobile-overlay"></div>

<aside id="sidebar" class="sidebar">
  <!-- Mobile Close Button -->
  <button id="sidebar-close" class="sidebar-close"><i class="ph ph-x"></i></button>

  <div class="sidebar-header">
    <img src="assets/images/LOGO.jpg" alt="YTR Logo" class="sidebar-logo">
    <div class="sidebar-title">
      <h1 class="font-roboto">Youth on The Rock</h1>
      <p>Changing A nation by Changing a Generation</p>
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
      <img src="assets/images/LOGO.jpg" alt="YTR" class="icon">
      <div class="sidebar-title">
        <h1 class="font-roboto">YTR NATIONAL</h1>
        <p style="color: #f59e0b;">System Admin</p>
      </div>
    </div>
    <a href="ChapterDashboard.php" class="role-switch-link" title="Switch to Chapter Admin Portal">
      <i class="ph ph-buildings"></i> Chapter Portal View
    </a>
    <a href="login.php" class="sign-out">
      <div class="icon"><i class="ph ph-sign-out"></i></div>
      Sign Out
    </a>
  </div>
</aside>