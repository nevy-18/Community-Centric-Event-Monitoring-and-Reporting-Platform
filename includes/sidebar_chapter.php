<?php 
$current_page = strtolower(basename($_SERVER['PHP_SELF'])); 
$cookie_ch_name = '';
if (isset($_COOKIE['ytr_chapter_name']) && !empty($_COOKIE['ytr_chapter_name'])) {
    $cookie_ch_name = htmlspecialchars(strtoupper($_COOKIE['ytr_chapter_name']));
}
?>

<!-- Dark overlay for mobile -->
<div id="mobile-overlay" class="mobile-overlay"></div>

<aside id="sidebar" class="sidebar">
  <!-- Mobile Close Button -->
  <button id="sidebar-close" class="sidebar-close" type="button" aria-label="Close navigation"><i class="ph ph-x"></i></button>

  <div class="sidebar-header">
    <img src="assets/images/LOGO.jpg" alt="YTR Logo" class="sidebar-logo">
    <div class="sidebar-title">
      <h1 class="font-roboto">Youth on The Rock</h1>
      <p style="color: #38bdf8; font-weight: 600;">Chapter Portal</p>
    </div>
  </div>

  <nav class="nav-menu font-roboto">
    <a href="ChapterDashboard.php" class="nav-link <?php echo ($current_page == 'chapterdashboard.php') ? 'active' : ''; ?>">
      <i class="ph ph-squares-four"></i> Chapter Overview
    </a>
    <a href="Announcement.php" class="nav-link <?php echo ($current_page == 'announcement.php') ? 'active' : ''; ?>">
      <i class="ph ph-megaphone"></i> Announcements
    </a>
    <a href="ChapterRequest.php" class="nav-link <?php echo ($current_page == 'chapterrequest.php') ? 'active' : ''; ?>">
      <i class="ph ph-image"></i> Poster Requests
    </a>
    <a href="ChapterProposal.php" class="nav-link <?php echo ($current_page == 'chapterproposal.php') ? 'active' : ''; ?>">
      <i class="ph ph-file-plus"></i> Event Proposals
    </a>
    <a href="ChapterReport.php" class="nav-link <?php echo ($current_page == 'chapterreport.php') ? 'active' : ''; ?>">
      <i class="ph ph-file-arrow-up"></i> Submit Event Report
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="admin-badge" style="background: rgba(30, 58, 138, 0.5); border: 1px solid rgba(59, 130, 246, 0.3);">
      <div class="icon" style="background: #1e40af; color: #93c5fd; display: flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 6px;">
        <i class="ph-fill ph-buildings" style="font-size: 1.25rem;"></i>
      </div>
      <div class="sidebar-title">
        <h1 class="font-roboto" id="sidebar-chapter-name" style="font-size: 0.85rem; color: #ffffff;"><?php echo $cookie_ch_name ?: 'CHAPTER WORKSPACE'; ?></h1>
        <p style="color: #38bdf8; font-size: 0.75rem;">Chapter Admin</p>
      </div>
    </div>

    <a href="login.php" class="sign-out" onclick="if(typeof ytrGlobalSignOut==='function'){ytrGlobalSignOut(event);}else{localStorage.clear();document.cookie='ytr_role=; path=/; max-age=0;';document.cookie='ytr_user_id=; path=/; max-age=0;';}">
      <div class="icon"><i class="ph ph-sign-out"></i></div>
      Sign Out
    </a>
  </div>
</aside>

<script>
(function() {
  function syncChapterSidebarName() {
    try {
      let chName = localStorage.getItem('ytr_chapter_name');
      const raw = localStorage.getItem('ytr_user');
      if (raw) {
        const u = JSON.parse(raw);
        if (u.chapter_name) {
          chName = u.chapter_name;
        } else if (u.chapter_id) {
          const map = {
            1: 'Laguna Chapter',
            2: 'Batangas Chapter',
            3: 'Cavite Chapter',
            4: 'Rizal Chapter',
            5: 'Quezon Chapter',
            6: 'Manila Central Chapter'
          };
          if (map[u.chapter_id]) chName = map[u.chapter_id];
        }
      }
      if (chName) {
        const el = document.getElementById('sidebar-chapter-name');
        if (el) el.textContent = chName.toUpperCase();
        localStorage.setItem('ytr_chapter_name', chName);
        document.cookie = "ytr_chapter_name=" + encodeURIComponent(chName) + "; path=/; max-age=86400";
      }
    } catch(e) {}
  }
  syncChapterSidebarName();
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', syncChapterSidebarName);
  }
})();
</script>
