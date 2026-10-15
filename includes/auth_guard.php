<?php
/**
 * Youth on the Rock (YTR) - Role-Based Access Guard
 * Enforces that Chapter Administrators can ONLY access Chapter Workspace pages.
 * If a Chapter Admin attempts to access a National Admin page, they are blocked
 * and redirected immediately to ChapterDashboard.php.
 */

// 1. Server-Side Cookie Guard
$is_chapter_admin = false;
if (isset($_COOKIE['ytr_role']) && $_COOKIE['ytr_role'] === 'chapter_admin') {
    $is_chapter_admin = true;
}

if ($is_chapter_admin) {
    header('Location: ChapterDashboard.php');
    exit;
}
?>
<!-- 2. Client-Side Instant Guard: Blocks Chapter Admins detected in localStorage -->
<script>
(function() {
  try {
    const rawUser = localStorage.getItem('ytr_user');
    const role = localStorage.getItem('ytr_role');
    let userRole = role;
    if (rawUser) {
      const u = JSON.parse(rawUser);
      if (u && u.role) userRole = u.role;
    }
    if (userRole === 'chapter_admin') {
      // Force cookie so subsequent requests are guarded at PHP level too
      document.cookie = "ytr_role=chapter_admin; path=/; max-age=86400";
      window.location.replace('ChapterDashboard.php');
    }
  } catch (e) {
    console.error('Role guard error:', e);
  }
})();
</script>
