<?php
/**
 * Youth on the Rock (YTR) - Central Authentication & Role Access Guard
 * Protects all portal pages.
 * If unauthenticated or forced login is requested, redirects to login.php immediately.
 * Enforces role isolation between National Admin and Chapter Admin portals.
 */

// If ?force_login=1 or ?logout=1 is present in URL, clear session and redirect to login
if (isset($_GET['force_login']) || isset($_GET['logout'])) {
    setcookie('ytr_role', '', time() - 3600, '/');
    setcookie('ytr_chapter_id', '', time() - 3600, '/');
    setcookie('ytr_user_id', '', time() - 3600, '/');
    header('Location: login.php');
    exit;
}

$current_script = strtolower(basename($_SERVER['PHP_SELF']));
$national_admin_pages = ['dashboard.php', 'manage.php', 'proposal.php', 'report.php', 'request.php'];
$chapter_pages = ['chapterdashboard.php', 'chapterproposal.php', 'chapterreport.php', 'chapterrequest.php'];

// 1. Server-Side Cookie Guard: Require active login
$has_auth_cookie = isset($_COOKIE['ytr_role']) && !empty($_COOKIE['ytr_role']) && 
                   isset($_COOKIE['ytr_user_id']) && !empty($_COOKIE['ytr_user_id']);

if (!$has_auth_cookie) {
    setcookie('ytr_role', '', time() - 3600, '/');
    setcookie('ytr_chapter_id', '', time() - 3600, '/');
    setcookie('ytr_user_id', '', time() - 3600, '/');
    header('Location: login.php');
    exit;
}

$user_role = $_COOKIE['ytr_role'];
$is_chapter_admin = ($user_role === 'chapter_admin');

// 2. Role Isolation: Chapter Admins CANNOT access National Admin pages
if ($is_chapter_admin && in_array($current_script, $national_admin_pages)) {
    header('Location: ChapterDashboard.php');
    exit;
}
?>
<!-- 3. Client-Side Instant Guard: Checks localStorage and keeps cookies in sync -->
<script>
(function() {
  try {
    const rawUser = localStorage.getItem('ytr_user');
    const role = localStorage.getItem('ytr_role');

    // If localStorage has no authenticated user, purge cookies and redirect to login
    if (!rawUser || !role) {
      document.cookie = "ytr_role=; path=/; max-age=0;";
      document.cookie = "ytr_chapter_id=; path=/; max-age=0;";
      document.cookie = "ytr_user_id=; path=/; max-age=0;";
      window.location.replace('login.php');
      return;
    }

    const u = JSON.parse(rawUser);
    if (!u || !u.id || !u.role) {
      localStorage.removeItem('ytr_user');
      localStorage.removeItem('ytr_role');
      localStorage.removeItem('ytr_chapter_id');
      document.cookie = "ytr_role=; path=/; max-age=0;";
      document.cookie = "ytr_chapter_id=; path=/; max-age=0;";
      document.cookie = "ytr_user_id=; path=/; max-age=0;";
      window.location.replace('login.php');
      return;
    }

    // Keep cookies synchronized with authenticated user
    document.cookie = "ytr_role=" + encodeURIComponent(u.role) + "; path=/; max-age=86400";
    document.cookie = "ytr_chapter_id=" + encodeURIComponent(u.chapter_id || '') + "; path=/; max-age=86400";
    document.cookie = "ytr_user_id=" + encodeURIComponent(u.id) + "; path=/; max-age=86400";

    const currentPage = window.location.pathname.split('/').pop().toLowerCase();
    const nationalPages = ['dashboard.php', 'manage.php', 'proposal.php', 'report.php', 'request.php'];
    if (u.role === 'chapter_admin' && nationalPages.includes(currentPage)) {
      window.location.replace('ChapterDashboard.php');
    }
  } catch(e) {
    console.error('Auth guard error:', e);
    window.location.replace('login.php');
  }
})();
</script>
