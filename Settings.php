<?php 
include 'includes/auth_guard.php'; 

$current_page = 'settings.php';
$user_role = $_SESSION['user_role'] ?? ($_COOKIE['ytr_role'] ?? 'national_admin');
$user_position = $_SESSION['user_position'] ?? 'Administrator';
$user_name = $_SESSION['user_name'] ?? 'User';
$is_chapter = ($user_role === 'chapter_admin');
$is_ceo = false;
$ceo_titles = ['chief executive officer', 'chief executive operation', 'ceo', 'chief executive'];
if (!$is_chapter && (in_array(strtolower(trim($user_position)), $ceo_titles) || in_array(strtolower(trim($_SESSION['user_email'] ?? '')), ['admin@ytr.org', 'ytr.org@gmail.com']))) {
    $is_ceo = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>YTR | System Settings</title>
  
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <link rel="stylesheet" href="assets/<?= $is_chapter ? 'Chapter.css' : 'National.css' ?>?v=<?= time() ?>">
  <link rel="stylesheet" href="assets/dark-mode.css?v=<?= time() ?>">
  <script src="assets/api-client.js"></script>

  <!-- Instant Anti-FOUC Theme Initialization -->
  <script>
    (function() {
      const savedTheme = localStorage.getItem('ytr_theme') || (document.cookie.match(/(?:^|;\s*)ytr_theme=([^;]+)/) || [])[1] || 'light';
      if (savedTheme === 'dark' || (savedTheme === 'system' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark-theme');
      }
      const customLogo = localStorage.getItem('ytr_custom_logo');
      if (customLogo) {
        document.addEventListener('DOMContentLoaded', function() {
      syncCEOBrandingPermissions();
      const currentTimeoutPref = localStorage.getItem('ytr_session_timeout') || '30';
      updateWatchdogBadge(parseInt(currentTimeoutPref, 10));
          document.querySelectorAll('.sidebar-logo, .icon img, #topbar-ytr-brand-logo, #LOGO, .ytr-system-logo').forEach(function(img) {
            if (img && img.tagName === 'IMG') img.src = customLogo;
          });
        });
      }
    })();
  </script>

  <style>
    /* Settings Page Specific Layout & Animations */
    .settings-container {
      max-width: 1140px;
      margin: 0 auto;
      padding-bottom: 3rem;
    }
    .settings-nav {
      display: flex;
      gap: 0.5rem;
      border-bottom: 2px solid #e2e8f0;
      margin-bottom: 1.5rem;
      overflow-x: auto;
      padding-bottom: 4px;
    }
    html.dark-theme .settings-nav {
      border-bottom-color: #1e293b;
    }
    .settings-nav-btn {
      background: none;
      border: none;
      padding: 0.65rem 1.15rem;
      font-size: 0.875rem;
      font-weight: 600;
      color: #64748b;
      cursor: pointer;
      border-radius: 8px 8px 0 0;
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      transition: all 0.15s ease;
      white-space: nowrap;
      position: relative;
    }
    .settings-nav-btn:hover {
      color: #0f172a;
      background: #f1f5f9;
    }
    html.dark-theme .settings-nav-btn:hover {
      color: #f8fafc;
      background: #1e293b;
    }
    .settings-nav-btn.active {
      color: #2563eb;
      font-weight: 700;
      background: #eff6ff;
    }
    html.dark-theme .settings-nav-btn.active {
      color: #60a5fa;
      background: #1e293b;
    }
    .settings-nav-btn.active::after {
      content: '';
      position: absolute;
      bottom: -6px;
      left: 0;
      right: 0;
      height: 3px;
      background: #2563eb;
      border-radius: 3px 3px 0 0;
    }
    html.dark-theme .settings-nav-btn.active::after {
      background: #3b82f6;
    }

    /* Settings Cards */
    .settings-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    html.dark-theme .settings-card {
      background: #111a2e;
      border-color: #1e293b;
      box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.45);
    }
    .settings-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #f1f5f9;
      padding-bottom: 1rem;
      margin-bottom: 1.25rem;
    }
    html.dark-theme .settings-card-header {
      border-bottom-color: #1e293b;
    }
    .settings-card-header h3 {
      font-size: 1.05rem;
      font-weight: 700;
      color: #0f172a;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    html.dark-theme .settings-card-header h3 {
      color: #f8fafc;
    }

    /* Theme Option Pickers */
    .theme-picker-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1rem;
      margin-top: 1rem;
    }
    .theme-option-box {
      border: 2px solid #e2e8f0;
      border-radius: 10px;
      padding: 1rem;
      cursor: pointer;
      transition: all 0.2s ease;
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
      background: #ffffff;
      position: relative;
    }
    html.dark-theme .theme-option-box {
      background: #0f172a;
      border-color: #1e293b;
    }
    .theme-option-box:hover {
      border-color: #93c5fd;
      transform: translateY(-2px);
    }
    .theme-option-box.active {
      border-color: #2563eb !important;
      background: #eff6ff !important;
      box-shadow: 0 0 0 1px #2563eb;
    }
    html.dark-theme .theme-option-box.active {
      border-color: #3b82f6 !important;
      background: #1e293b !important;
      box-shadow: 0 0 0 1px #3b82f6;
    }

    /* Logo Upload Dropzone */
    .logo-upload-dropzone {
      border: 2px dashed #cbd5e1;
      border-radius: 12px;
      padding: 2rem 1.5rem;
      text-align: center;
      background: #f8fafc;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    html.dark-theme .logo-upload-dropzone {
      background: #0d1527;
      border-color: #334155;
    }
    .logo-upload-dropzone:hover {
      border-color: #3b82f6;
      background: #eff6ff;
    }
    html.dark-theme .logo-upload-dropzone:hover {
      border-color: #60a5fa;
      background: #1e293b;
    }

    /* CEO Exclusive Banner */
    .ceo-exclusive-badge {
      background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
      color: #ffffff;
      padding: 0.35rem 0.75rem;
      border-radius: 6px;
      font-size: 0.72rem;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }
    .ceo-lock-banner {
      background: #fff1f2;
      border: 1px solid #fecdd3;
      color: #9f1239;
      border-radius: 8px;
      padding: 0.85rem 1rem;
      font-size: 0.825rem;
      display: flex;
      align-items: center;
      gap: 0.65rem;
      font-weight: 600;
    }
    html.dark-theme .ceo-lock-banner {
      background: #4c0519;
      border-color: #881337;
      color: #fecdd3;
    }

    /* Switch toggles */
    .toggle-switch-wrap {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0.75rem 0;
      border-bottom: 1px solid #f1f5f9;
    }
    html.dark-theme .toggle-switch-wrap {
      border-bottom-color: #1e293b;
    }
    .toggle-switch {
      position: relative;
      display: inline-block;
      width: 44px;
      height: 24px;
      flex-shrink: 0;
    }
    .toggle-switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }
    .toggle-slider {
      position: absolute;
      cursor: pointer;
      inset: 0;
      background-color: #cbd5e1;
      transition: .2s;
      border-radius: 24px;
    }
    html.dark-theme .toggle-slider {
      background-color: #334155;
    }
    .toggle-slider:before {
      position: absolute;
      content: "";
      height: 18px;
      width: 18px;
      left: 3px;
      bottom: 3px;
      background-color: white;
      transition: .2s;
      border-radius: 50%;
    }
    input:checked + .toggle-slider {
      background-color: #2563eb;
    }
    input:checked + .toggle-slider:before {
      transform: translateX(20px);
    }
  </style>
</head>
<body>

  <div class="app-wrapper">
    <!-- Dynamic Sidebar -->
    <?php if ($is_chapter): ?>
      <?php include 'includes/sidebar_chapter.php'; ?>
    <?php else: ?>
      <?php include 'includes/sidebar.php'; ?>
    <?php endif; ?>

    <div class="main-wrapper">
      <?php include 'includes/topbar.php'; ?>

      <main class="page-content font-roboto">
        <div class="settings-container">

          <!-- Page Header -->
          <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
              <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main, #0f172a); margin: 0;" class="font-roboto">System Settings</h1>
                <span class="badge" style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 0.72rem; padding: 2px 8px;">
                  <?= htmlspecialchars($user_position) ?>
                </span>
                <?php if ($is_ceo): ?>
                  <span class="ceo-exclusive-badge font-roboto">
                    <i class="ph-bold ph-shield-check"></i> CEO Clearance
                  </span>
                <?php endif; ?>
              </div>
              <p style="color: #64748b; font-size: 0.85rem; margin: 0;">
                Configure system theme appearance, organization branding, dark mode, alert preferences, and security management.
              </p>
            </div>
            
            <div style="display: flex; align-items: center; gap: 0.5rem;">
              <button type="button" class="btn btn-outline font-roboto" onclick="purgeAppCache()" title="Purge local state and reload clean session" style="font-size: 0.8rem; padding: 0.5rem 0.85rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="ph-bold ph-arrow-counter-clockwise"></i> Clear Cache
              </button>
            </div>
          </div>

          <!-- Settings Tab Bar -->
          <div class="settings-nav">
            <button type="button" class="settings-nav-btn active" id="tab-btn-appearance" onclick="switchSettingsTab('appearance')">
              <i class="ph-bold ph-palette"></i> Appearance &amp; Dark Mode
            </button>
            <button type="button" class="settings-nav-btn" id="tab-btn-branding" onclick="switchSettingsTab('branding')">
              <i class="ph-bold ph-image"></i> Brand &amp; Logo <?= $is_ceo ? '<span style="color:#f59e0b;font-size:0.7rem;">★</span>' : '' ?>
            </button>
            <button type="button" class="settings-nav-btn" id="tab-btn-notifications" onclick="switchSettingsTab('notifications')">
              <i class="ph-bold ph-bell-ringing"></i> Notifications &amp; Alerts
            </button>
            <button type="button" class="settings-nav-btn" id="tab-btn-security" onclick="switchSettingsTab('security')">
              <i class="ph-bold ph-shield-check"></i> Security &amp; Session
            </button>
            <button type="button" class="settings-nav-btn" id="tab-btn-diagnostics" onclick="switchSettingsTab('diagnostics')">
              <i class="ph-bold ph-activity"></i> System Diagnostics
            </button>
            <button type="button" class="settings-nav-btn ceo-only-tab" id="tab-btn-executive" onclick="switchSettingsTab('executive')" style="display: none; color: #dc2626;">
              <i class="ph-bold ph-shield-star" style="color: #dc2626;"></i> Chief Executive Control
            </button>
          </div>

          <!-- ============================================================
               TAB 1: APPEARANCE & DARK MODE
               ============================================================ -->
          <div id="tab-pane-appearance" class="settings-tab-pane">
            <div class="settings-card">
              <div class="settings-card-header">
                <div>
                  <h3><i class="ph-bold ph-moon-stars" style="color: #6366f1;"></i> Interface Theme &amp; Dark Mode</h3>
                  <p style="color: #64748b; font-size: 0.8rem; margin: 2px 0 0;">Select your preferred display theme. Theme changes apply instantly and persist across all devices.</p>
                </div>
                <span id="activeThemeBadge" class="badge" style="background: #f1f5f9; color: #475569; font-weight: 700;">Light Theme Active</span>
              </div>

              <!-- Theme Option Pickers -->
              <div class="theme-picker-grid">
                <!-- 1. Light Mode Box -->
                <div class="theme-option-box" id="theme-box-light" onclick="chooseTheme('light')">
                  <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; color: #0f172a;">
                      <i class="ph-bold ph-sun" style="color: #f59e0b; font-size: 1.25rem;"></i> Light Mode
                    </div>
                    <span class="badge" style="background: #f0fdf4; color: #166534; font-size: 0.65rem;">Default</span>
                  </div>
                  <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0.65rem; height: 60px; display: flex; flex-direction: column; justify-content: center; gap: 4px;">
                    <div style="height: 6px; width: 45%; background: #0f172a; border-radius: 3px;"></div>
                    <div style="height: 5px; width: 75%; background: #94a3b8; border-radius: 3px;"></div>
                    <div style="height: 5px; width: 60%; background: #3b82f6; border-radius: 3px;"></div>
                  </div>
                  <p style="color: #64748b; font-size: 0.75rem; margin: 0;">Clean bright slate aesthetics optimized for daytime readability.</p>
                </div>

                <!-- 2. Dark Mode Box -->
                <div class="theme-option-box" id="theme-box-dark" onclick="chooseTheme('dark')">
                  <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; color: #0f172a;">
                      <i class="ph-bold ph-moon" style="color: #6366f1; font-size: 1.25rem;"></i> Dark Mode
                    </div>
                    <span class="badge" style="background: #312e81; color: #c7d2fe; font-size: 0.65rem;">Midnight</span>
                  </div>
                  <div style="background: #090d16; border: 1px solid #1e293b; border-radius: 6px; padding: 0.65rem; height: 60px; display: flex; flex-direction: column; justify-content: center; gap: 4px;">
                    <div style="height: 6px; width: 45%; background: #f8fafc; border-radius: 3px;"></div>
                    <div style="height: 5px; width: 75%; background: #64748b; border-radius: 3px;"></div>
                    <div style="height: 5px; width: 60%; background: #60a5fa; border-radius: 3px;"></div>
                  </div>
                  <p style="color: #64748b; font-size: 0.75rem; margin: 0;">Deep midnight canvas reduces eye strain during low-light sessions.</p>
                </div>

                <!-- 3. System Sync Box -->
                <div class="theme-option-box" id="theme-box-system" onclick="chooseTheme('system')">
                  <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; color: #0f172a;">
                      <i class="ph-bold ph-desktop" style="color: #10b981; font-size: 1.25rem;"></i> System Auto
                    </div>
                    <span class="badge" style="background: #eff6ff; color: #1e40af; font-size: 0.65rem;">Adaptive</span>
                  </div>
                  <div style="background: linear-gradient(90deg, #f8fafc 50%, #090d16 50%); border: 1px solid #cbd5e1; border-radius: 6px; padding: 0.65rem; height: 60px; display: flex; align-items: center; justify-content: space-around;">
                    <i class="ph-bold ph-sun" style="color: #f59e0b;"></i>
                    <i class="ph-bold ph-moon" style="color: #6366f1;"></i>
                  </div>
                  <p style="color: #64748b; font-size: 0.75rem; margin: 0;">Automatically switches mode matching your operating system setting.</p>
                </div>
              </div>

              <!-- Live Preview Widget -->
              <div style="margin-top: 1.5rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem 1.25rem;" id="livePreviewContainer">
                <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 0.5rem;">
                  Live Component Theme Preview
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; background: #ffffff; padding: 0.85rem 1.15rem; border-radius: 8px; border: 1px solid #e2e8f0;" id="previewCardInner">
                  <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                      <i class="ph-bold ph-calendar-check"></i>
                    </div>
                    <div>
                      <strong style="font-size: 0.9rem; color: #0f172a; display: block;" id="previewCardTitle">Youth Leadership Bootcamp &amp; Summit 2026</strong>
                      <small style="color: #64748b;">Laguna Chapter · Nov 20, 2026</small>
                    </div>
                  </div>
                  <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span class="status-pill-fixed approved font-roboto">
                      <i class="ph-bold ph-check"></i> Approved
                    </span>
                    <button type="button" class="action-btn-uniform btn-details font-roboto">
                      <i class="ph ph-info"></i> Details
                    </button>
                  </div>
                </div>
              </div>

            </div>

            <!-- Table Density & Sound Settings -->
            <div class="settings-card">
              <div class="settings-card-header">
                <div>
                  <h3><i class="ph-bold ph-rows" style="color: #0284c7;"></i> Layout Density &amp; Sound Preferences</h3>
                  <p style="color: #64748b; font-size: 0.8rem; margin: 2px 0 0;">Customize table row spacing and feedback audio chimes.</p>
                </div>
              </div>

              <!-- Table Density Switch -->
              <div class="toggle-switch-wrap">
                <div>
                  <strong style="font-size: 0.875rem; color: #0f172a; display: block;">Compact Table Density</strong>
                  <span style="font-size: 0.75rem; color: #64748b;">Reduces vertical table row padding for viewing more event records without scrolling.</span>
                </div>
                <label class="toggle-switch">
                  <input type="checkbox" id="settingCompactDensity" onchange="toggleTableDensity(this.checked)">
                  <span class="toggle-slider"></span>
                </label>
              </div>

              <!-- Sound Chimes Switch -->
              <div class="toggle-switch-wrap" style="border-bottom: none;">
                <div>
                  <strong style="font-size: 0.875rem; color: #0f172a; display: block;">Sound Notification Chimes</strong>
                  <span style="font-size: 0.75rem; color: #64748b;">Play a pleasant audio chime when action alerts, proposal approvals, or reminders trigger.</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                  <button type="button" class="btn btn-outline btn-sm font-roboto" onclick="playChimeSound()" style="font-size: 0.75rem; padding: 0.25rem 0.6rem;">
                    <i class="ph-bold ph-speaker-high"></i> Test Sound
                  </button>
                  <label class="toggle-switch">
                    <input type="checkbox" id="settingSoundToggle" checked onchange="toggleSoundPreference(this.checked)">
                    <span class="toggle-slider"></span>
                  </label>
                </div>
              </div>

            </div>
          </div>

          <!-- ============================================================
               TAB 2: BRAND & MOVEMENT LOGO (CEO ONLY)
               ============================================================ -->
          <div id="tab-pane-branding" class="settings-tab-pane" style="display: none;">
            
            <!-- Non-CEO Notice Banner (Shown dynamically if not Chief Executive) -->
            <div id="ceoLockBanner" class="ceo-lock-banner" style="display: none; margin-bottom: 1.5rem;">
              <i class="ph-bold ph-lock-key" style="font-size: 1.5rem; flex-shrink: 0;"></i>
              <div>
                <strong style="display: block; font-size: 0.875rem;">Chief Executive Officer Clearance Required</strong>
                <span>Modifying the official Youth on the Rock movement logo and core organizational identity is strictly restricted to the Chief Executive Officer. You may view the active identity below.</span>
              </div>
            </div>

            <div class="settings-card">
              <div class="settings-card-header">
                <div>
                  <h3><i class="ph-bold ph-shield-check" style="color: #2563eb;"></i> Official Movement Logo Customizer</h3>
                  <p id="logoCardSubtitle" style="color: #64748b; font-size: 0.8rem; margin: 2px 0 0;">
                    Upload and deploy a new official organization logo across all portals, dossiers, headers, and login screens.
                  </p>
                </div>
                <div>
                  <span id="ceoClearanceBadge" class="ceo-exclusive-badge font-roboto" style="display: none;"><i class="ph-bold ph-shield-check"></i> CEO Clearance</span>
                  <span id="nonCeoBadge" class="badge" style="display: none; background: #f1f5f9; color: #475569;"><i class="ph-bold ph-lock-key"></i> Read Only</span>
                </div>
              </div>

              <!-- Active Logo Display & Preview -->
              <div style="display: flex; gap: 2rem; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap;">
                <div style="background: #ffffff; border: 2px solid #e2e8f0; border-radius: 12px; padding: 1rem; width: 140px; height: 140px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.08); flex-shrink: 0;" id="logoDisplayContainer">
                  <img src="assets/images/LOGO.jpg" alt="Active YTR Logo" id="currentActiveLogoPreview" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 6px;">
                </div>

                <div style="flex: 1; min-width: 260px;">
                  <h4 style="font-size: 1rem; color: #0f172a; margin: 0 0 0.35rem 0;" id="logoStatusHeading">Active Movement Logo</h4>
                  <p style="color: #64748b; font-size: 0.8rem; line-height: 1.5; margin-bottom: 0.75rem;" id="logoStatusText">
                    Currently utilizing official YTR master emblem. Synchronized across the National Portal, Chapter Portals, printable PDF dossiers, and authentication interfaces.
                  </p>
                  
                  <div id="ceoLogoActionControls" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <button type="button" class="btn btn-outline font-roboto btn-sm" onclick="document.getElementById('ceoLogoFileInput').click()" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                      <i class="ph-bold ph-upload-simple"></i> Choose New Logo
                    </button>
                    <button type="button" class="btn btn-outline font-roboto btn-sm text-rose" onclick="resetToDefaultLogo()" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.35rem; color: #dc2626; border-color: #fecdd3;">
                      <i class="ph-bold ph-arrow-counter-clockwise"></i> Reset to Default
                    </button>
                  </div>
                </div>
              </div>

              <!-- Drag and Drop Dropzone -->
              <input type="file" id="ceoLogoFileInput" accept="image/png, image/jpeg, image/webp, image/svg+xml" style="display: none;" onchange="handleLogoFileSelect(event)">
              
              <div id="ceoDropzone" class="logo-upload-dropzone" onclick="document.getElementById('ceoLogoFileInput').click()" ondragover="event.preventDefault(); this.style.borderColor='#2563eb';" ondragleave="this.style.borderColor='';" ondrop="handleLogoDrop(event)">
                <i class="ph-bold ph-cloud-arrow-up" style="font-size: 2.5rem; color: #2563eb; margin-bottom: 0.5rem;"></i>
                <h4 style="font-size: 0.95rem; font-weight: 700; color: #0f172a; margin: 0 0 0.25rem 0;">Click or drag new logo image to upload</h4>
                <p style="color: #64748b; font-size: 0.75rem; margin: 0;">Supported formats: PNG, JPG, JPEG, SVG, WebP (Max size: 5MB). Transparent background recommended.</p>
              </div>

              <!-- Pending Save Action Bar -->
              <div id="pendingLogoActionContainer" style="display: none; margin-top: 1.25rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 1rem; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.65rem;">
                  <i class="ph-bold ph-check-circle" style="font-size: 1.5rem; color: #2563eb;"></i>
                  <div>
                    <strong style="font-size: 0.85rem; color: #1e40af; display: block;">New Logo Ready to Deploy</strong>
                    <span style="font-size: 0.75rem; color: #3b82f6;">Preview displayed above. Click 'Save &amp; Deploy Logo' to publish system-wide.</span>
                  </div>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                  <button type="button" class="btn btn-outline btn-sm font-roboto" onclick="cancelPendingLogo()">Cancel</button>
                  <button type="button" class="btn btn-primary btn-sm font-roboto" id="btnDeployLogo" onclick="deployNewLogo()" style="background: #2563eb; border: none; font-weight: 600;">
                    <i class="ph-bold ph-floppy-disk"></i> Save &amp; Deploy Logo
                  </button>
                </div>
              </div>

            </div>

            <!-- Organization Identity Details -->
            <div class="settings-card">
              <div class="settings-card-header">
                <div>
                  <h3><i class="ph-bold ph-buildings" style="color: #059669;"></i> Movement Identity &amp; Platform Profile</h3>
                  <p style="color: #64748b; font-size: 0.8rem; margin: 2px 0 0;">Official organization titles shown on printable dossiers and platform metadata.</p>
                </div>
              </div>

              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem;">
                <div class="form-group">
                  <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem; display: block;">Organization Full Name</label>
                  <input type="text" id="orgNameInput" class="form-control font-roboto ceo-editable-input" value="Youth on the Rock National Movement" style="width: 100%; padding: 0.55rem; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.875rem;">
                </div>
                <div class="form-group">
                  <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem; display: block;">Operational Tagline</label>
                  <input type="text" id="orgTaglineInput" class="form-control font-roboto ceo-editable-input" value="Community-Centric Event Monitoring & Reporting Platform" style="width: 100%; padding: 0.55rem; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.875rem;">
                </div>
                <div class="form-group">
                  <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem; display: block;">Official Contact Email</label>
                  <input type="email" id="orgEmailInput" class="form-control font-roboto ceo-editable-input" value="ytr.national@gmail.com" style="width: 100%; padding: 0.55rem; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.875rem;">
                </div>
                <div class="form-group">
                  <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem; display: block;">Fiscal / Operational Year</label>
                  <input type="text" id="orgFiscalYearInput" class="form-control font-roboto ceo-editable-input" value="2026 - 2027" style="width: 100%; padding: 0.55rem; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.875rem;">
                </div>
              </div>

              <div id="btnSaveOrgDetailsContainer" style="margin-top: 1.25rem; display: flex; justify-content: flex-end;">
                <button type="button" class="btn btn-primary font-roboto" onclick="saveOrgIdentityDetails()" style="padding: 0.55rem 1.15rem; font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.45rem;">
                  <i class="ph-bold ph-floppy-disk"></i> Save Organization Details
                </button>
              </div>
            </div>

          </div>

          <!-- ============================================================
               TAB 3: NOTIFICATIONS & ALERT RULES
               ============================================================ -->
          <div id="tab-pane-notifications" class="settings-tab-pane" style="display: none;">
            <div class="settings-card">
              <div class="settings-card-header">
                <div>
                  <h3><i class="ph-bold ph-bell-ringing" style="color: #f59e0b;"></i> Milestone &amp; Deadline Alert Rules</h3>
                  <p style="color: #64748b; font-size: 0.8rem; margin: 2px 0 0;">Configure lead times for reminder alerts and overdue event notifications.</p>
                </div>
              </div>

              <!-- Reminder Window Setting -->
              <div style="padding: 0.85rem 0; border-bottom: 1px solid #f1f5f9;">
                <label style="font-weight: 700; font-size: 0.875rem; color: #0f172a; margin-bottom: 0.35rem; display: block;">
                  Post-Event Report Reminder Lead Time
                </label>
                <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.75rem;">
                  Determines when upcoming event reporting deadlines transition to 'Due Soon' and qualify for automated chapter reminders.
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                  <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; cursor: pointer;">
                    <input type="radio" name="leadDays" value="3" checked onchange="saveReminderLeadDays(3)">
                    <span>3 Days Before Deadline (Standard)</span>
                  </label>
                  <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; cursor: pointer;">
                    <input type="radio" name="leadDays" value="5" onchange="saveReminderLeadDays(5)">
                    <span>5 Days Before Deadline</span>
                  </label>
                  <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; cursor: pointer;">
                    <input type="radio" name="leadDays" value="7" onchange="saveReminderLeadDays(7)">
                    <span>7 Days Before Deadline (Early Warning)</span>
                  </label>
                </div>
              </div>

              <!-- Overdue Alert Highlighting -->
              <div class="toggle-switch-wrap">
                <div>
                  <strong style="font-size: 0.875rem; color: #0f172a; display: block;">Urgent Overdue Highlighting</strong>
                  <span style="font-size: 0.75rem; color: #64748b;">Highlight past-due milestone rows with prominent red warning indicators on overview tables.</span>
                </div>
                <label class="toggle-switch">
                  <input type="checkbox" id="settingOverdueHighlight" checked onchange="localStorage.setItem('ytr_overdue_highlight', this.checked ? '1' : '0')">
                  <span class="toggle-slider"></span>
                </label>
              </div>

              <!-- Proposal Clearance Alerts -->
              <div class="toggle-switch-wrap">
                <div>
                  <strong style="font-size: 0.875rem; color: #0f172a; display: block;">Proposal Evaluation Alerts</strong>
                  <span style="font-size: 0.75rem; color: #64748b;">Surface topbar milestone alerts when National Secretariat clears or returns chapter proposals.</span>
                </div>
                <label class="toggle-switch">
                  <input type="checkbox" id="settingProposalAlerts" checked onchange="localStorage.setItem('ytr_proposal_alerts', this.checked ? '1' : '0')">
                  <span class="toggle-slider"></span>
                </label>
              </div>

              <!-- Desktop Push Permission -->
              <div class="toggle-switch-wrap" style="border-bottom: none;">
                <div>
                  <strong style="font-size: 0.875rem; color: #0f172a; display: block;">Browser Desktop Push Notifications</strong>
                  <span style="font-size: 0.75rem; color: #64748b;">Allow browser alerts for urgent chapter notifications even when the browser tab is minimized.</span>
                </div>
                <button type="button" class="btn btn-outline btn-sm font-roboto" id="btnRequestBrowserPush" onclick="requestBrowserPush()" style="font-size: 0.78rem;">
                  <i class="ph-bold ph-bell"></i> Request Permission
                </button>
              </div>

            </div>
          </div>

          <!-- ============================================================
               TAB 4: SECURITY & SESSION CONTROLS
               ============================================================ -->
          <div id="tab-pane-security" class="settings-tab-pane" style="display: none;">
            <div class="settings-card">
              <div class="settings-card-header">
                <div>
                  <h3><i class="ph-bold ph-shield-check" style="color: #10b981;"></i> Security &amp; Session Management</h3>
                  <p style="color: #64748b; font-size: 0.8rem; margin: 2px 0 0;">Manage your session expiration, local application state, and account security.</p>
                </div>
              </div>

              <!-- Active Session Details -->
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; margin-bottom: 1.25rem;">
                <h4 style="font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase; margin: 0 0 0.5rem 0;">Current Active Session</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.75rem; font-size: 0.85rem;">
                  <div>
                    <span style="color: #64748b; font-size: 0.75rem; display: block;">Authenticated As:</span>
                    <strong><?= htmlspecialchars($user_name) ?></strong>
                  </div>
                  <div>
                    <span style="color: #64748b; font-size: 0.75rem; display: block;">Role &amp; Jurisdiction:</span>
                    <span style="color: #1e40af; font-weight: 600;"><?= htmlspecialchars($user_position) ?></span>
                  </div>
                  <div>
                    <span style="color: #64748b; font-size: 0.75rem; display: block;">Branch / Scope:</span>
                    <span style="color: #166534; font-weight: 600;"><?= $is_chapter ? 'Chapter Branch Network' : 'National Headquarters' ?></span>
                  </div>
                </div>
              </div>

              <!-- Session Idle Timeout Watchdog Controls -->
              <div style="padding: 0.85rem 0; border-bottom: 1px solid #f1f5f9;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.35rem;">
                  <div>
                    <label style="font-weight: 700; font-size: 0.875rem; color: var(--text-main, #0f172a); margin-bottom: 0.2rem; display: block;">Session Idle Expiration</label>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 0;">Select the duration of inactivity before the platform safely prompts re-authentication.</p>
                  </div>
                  <span id="idleWatchdogStatusBadge" class="badge" style="background: #ecfdf5; color: #047857; font-weight: 700; border: 1px solid #a7f3d0; padding: 0.3rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <i class="ph-bold ph-shield-check"></i> Watchdog Active: 30 mins
                  </span>
                </div>
                
                <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap; margin-top: 0.5rem;">
                  <select id="sessionTimeoutSelect" class="form-select font-roboto" onchange="setSessionTimeout(this.value)" style="max-width: 280px; padding: 0.55rem; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.875rem;">
                    <option value="15">15 Minutes Inactivity</option>
                    <option value="30" selected>30 Minutes Inactivity</option>
                    <option value="60">1 Hour Inactivity</option>
                    <option value="120">2 Hours (Standard)</option>
                    <option value="480">8 Hours (Full Shift)</option>
                    <option value="0">Keep Signed In (Trusted Device)</option>
                  </select>

                  <button type="button" class="btn btn-outline font-roboto btn-sm" onclick="startIdleTestSimulation()" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem; color: #2563eb; border-color: #bfdbfe; background: #eff6ff;">
                    <i class="ph-bold ph-timer"></i> Test 5-Second Inactivity Auto-Signout
                  </button>
                </div>
                <p id="idleCountdownInfo" style="font-size: 0.725rem; color: #64748b; margin-top: 0.45rem; margin-bottom: 0;">
                  <i class="ph ph-info"></i> Inactivity timer continuously tracks mouse, keyboard, and touch interactions across all tabs.
                </p>
              </div>

              <!-- Password Update Shortcut -->
              <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid #f1f5f9; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                  <strong style="font-size: 0.875rem; color: #0f172a; display: block;">Account Password &amp; Profile</strong>
                  <span style="font-size: 0.75rem; color: #64748b;">Update your personal profile details, display avatar, or reset password credentials.</span>
                </div>
                <button type="button" class="btn btn-outline font-roboto" onclick="openProfileModal(event)" style="font-size: 0.8rem; padding: 0.45rem 0.85rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                  <i class="ph-bold ph-key"></i> Update Credentials
                </button>
              </div>

              <!-- Cache Purge -->
              <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                  <strong style="font-size: 0.875rem; color: #0f172a; display: block;">Purge Offline Cache &amp; Reset Workspace</strong>
                  <span style="font-size: 0.75rem; color: #64748b;">Clear cached proposals, attendee rosters, and view preferences to force a fresh database sync.</span>
                </div>
                <button type="button" class="btn btn-outline font-roboto text-rose" onclick="purgeAppCache()" style="color: #dc2626; border-color: #fecdd3; font-size: 0.8rem; padding: 0.45rem 0.85rem;">
                  <i class="ph-bold ph-trash"></i> Purge App Cache
                </button>
              </div>

            </div>
          </div>

          <!-- ============================================================
               TAB 5: SYSTEM HEALTH & DIAGNOSTICS
               ============================================================ -->
          <div id="tab-pane-diagnostics" class="settings-tab-pane" style="display: none;">
            <div class="settings-card">
              <div class="settings-card-header">
                <div>
                  <h3><i class="ph-bold ph-cpu" style="color: #3b82f6;"></i> Cloud Infrastructure &amp; Diagnostics</h3>
                  <p style="color: #64748b; font-size: 0.8rem; margin: 2px 0 0;">Real-time service health check across database, API proxy, and storage tiers.</p>
                </div>
                <button type="button" class="btn btn-outline btn-sm font-roboto" onclick="checkSystemHealth()" style="font-size: 0.78rem;">
                  <i class="ph-bold ph-arrows-clockwise"></i> Ping Services
                </button>
              </div>

              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem;">
                <!-- 1. Database Tier -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem;">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.4rem; font-weight: 700; color: #0f172a; font-size: 0.875rem;">
                      <i class="ph-bold ph-database" style="color: #0284c7;"></i> Cloud Managed MySQL
                    </div>
                    <span id="healthDbBadge" class="badge approved" style="font-size: 0.65rem;">CONNECTED</span>
                  </div>
                  <p style="font-size: 0.75rem; color: #64748b; margin: 0 0 0.5rem 0;">Aiven Cloud Managed High-Availability Cluster.</p>
                  <small style="color: #334155; font-family: monospace; font-size: 0.72rem; display: block; word-break: break-all;">
                    ytr-mysql-munozrayvhen-f1e7.a.aivencloud.com:17373
                  </small>
                </div>

                <!-- 2. Python API Backend -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem;">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.4rem; font-weight: 700; color: #0f172a; font-size: 0.875rem;">
                      <i class="ph-bold ph-brackets-curly" style="color: #16a34a;"></i> Flask API Engine
                    </div>
                    <span id="healthApiBadge" class="badge approved" style="font-size: 0.65rem;">OPERATIONAL</span>
                  </div>
                  <p style="font-size: 0.75rem; color: #64748b; margin: 0 0 0.5rem 0;">Python 3.x WSGI Microservice (:5000) reverse-proxied via Apache.</p>
                  <small style="color: #334155; font-family: monospace; font-size: 0.72rem; display: block;" id="healthServerTime">
                    Checking server clock...
                  </small>
                </div>

                <!-- 3. Storage System -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem;">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.4rem; font-weight: 700; color: #0f172a; font-size: 0.875rem;">
                      <i class="ph-bold ph-hard-drives" style="color: #d97706;"></i> Media &amp; Upload Storage
                    </div>
                    <span class="badge approved" style="font-size: 0.65rem;">WRITABLE</span>
                  </div>
                  <p style="font-size: 0.75rem; color: #64748b; margin: 0 0 0.5rem 0;">Persistent file ledger storing digital signatures, attendance sheets, and proposals.</p>
                  <small style="color: #334155; font-family: monospace; font-size: 0.72rem; display: block;">
                    assets/uploads/ (posters, attendance, logos, signatures)
                  </small>
                </div>
              </div>

              <?php if ($is_ceo): ?>
                <!-- CEO Executive Actions Link -->
                <div style="margin-top: 1.5rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                  <div style="display: flex; align-items: center; gap: 0.65rem;">
                    <i class="ph-bold ph-shield-check" style="font-size: 1.5rem; color: #2563eb;"></i>
                    <div>
                      <strong style="font-size: 0.85rem; color: #1e40af; display: block;">Chief Executive Officer Operations</strong>
                      <span style="font-size: 0.75rem; color: #3b82f6;">Export live data backup, view archived records, or perform system flush.</span>
                    </div>
                  </div>
                  <div style="display: flex; gap: 0.5rem;">
                    <button type="button" class="btn btn-outline btn-sm font-roboto" onclick="triggerSystemBackup(event)" style="background: #fff; font-size: 0.78rem;">
                      <i class="ph-bold ph-download-simple"></i> Download Backup
                    </button>
                    <button type="button" class="btn btn-primary btn-sm font-roboto" onclick="openTopLevelFlushModal(event, 'archives')" style="font-size: 0.78rem;">
                      <i class="ph-bold ph-archive"></i> Flushed Archives
                    </button>
                  </div>
                </div>
              <?php endif; ?>

            </div>
          </div>

        

          <!-- ============================================================
               TAB 6: CHIEF EXECUTIVE CONTROL (CEO ONLY)
               ============================================================ -->
          <div id="tab-pane-executive" class="settings-tab-pane" style="display: none;">
            
            <!-- Executive Clearance Banner -->
            <div class="settings-card" style="border-left: 4px solid #dc2626; margin-bottom: 1.5rem;">
              <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.85rem;">
                  <div style="width: 44px; height: 44px; border-radius: 10px; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                    <i class="ph-bold ph-shield-star"></i>
                  </div>
                  <div>
                    <h3 style="margin: 0; font-size: 1.15rem; color: var(--text-main, #0f172a); font-weight: 800;">Chief Executive Officer Control Center</h3>
                    <p style="margin: 0.2rem 0 0; font-size: 0.8rem; color: #64748b;">Exclusive administrative authority: Download full system archives, retrieve flushed history, and manage baseline restoration.</p>
                  </div>
                </div>
                <span class="ceo-exclusive-badge font-roboto" style="display: inline-flex;"><i class="ph-bold ph-shield-check"></i> Chief Executive Clearance</span>
              </div>
            </div>

            <!-- Card 1: System Data Backup -->
            <div class="settings-card">
              <div class="settings-card-header">
                <div>
                  <h3><i class="ph-bold ph-download-simple" style="color: #2563eb;"></i> System Data Backup &amp; Snapshot Export</h3>
                  <p style="color: #64748b; font-size: 0.8rem; margin: 2px 0 0;">Generate and download a complete cryptographic export of all platform tables, proposals, attendance records, and poster queues.</p>
                </div>
                <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-weight: 700;">Live Production Export</span>
              </div>

              <!-- Live Database Statistics -->
              <div style="font-size: 0.825rem; color: #475569; margin-bottom: 0.65rem;">
                <strong>Current Active Database Records:</strong>
              </div>
              <div class="flush-stat-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 0.75rem; margin-bottom: 1.25rem;">
                <div class="flush-stat-pill" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem; text-align: center;">
                  <strong id="execStatEvents" style="font-size: 1.4rem; color: #1e3a8a; display: block;">--</strong>
                  <span style="font-size: 0.72rem; color: #64748b; font-weight: 600;">Events</span>
                </div>
                <div class="flush-stat-pill" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem; text-align: center;">
                  <strong id="execStatRequests" style="font-size: 1.4rem; color: #d97706; display: block;">--</strong>
                  <span style="font-size: 0.72rem; color: #64748b; font-weight: 600;">Poster Requests</span>
                </div>
                <div class="flush-stat-pill" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem; text-align: center;">
                  <strong id="execStatReports" style="font-size: 1.4rem; color: #16a34a; display: block;">--</strong>
                  <span style="font-size: 0.72rem; color: #64748b; font-weight: 600;">Event Reports</span>
                </div>
                <div class="flush-stat-pill" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem; text-align: center;">
                  <strong id="execStatUsers" style="font-size: 1.4rem; color: #7c3aed; display: block;">--</strong>
                  <span style="font-size: 0.72rem; color: #64748b; font-weight: 600;">Accounts</span>
                </div>
                <div class="flush-stat-pill" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem; text-align: center;">
                  <strong id="execStatFiles" style="font-size: 1.4rem; color: #0284c7; display: block;">--</strong>
                  <span style="font-size: 0.72rem; color: #64748b; font-weight: 600;">Uploaded Files</span>
                </div>
              </div>

              <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <button type="button" class="btn btn-primary font-roboto" onclick="triggerSystemBackup(event)" style="padding: 0.55rem 1.15rem; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.45rem; background: #2563eb;">
                  <i class="ph-bold ph-download-simple"></i> Download JSON Archive
                </button>
                <button type="button" class="btn btn-outline font-roboto" onclick="downloadBackupSqlDump()" style="padding: 0.55rem 1.15rem; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.45rem;">
                  <i class="ph-bold ph-database"></i> Download SQL Database Dump
                </button>
              </div>
            </div>

            <!-- Card 2: Archived Data & Retrieval Ledger -->
            <div class="settings-card">
              <div class="settings-card-header">
                <div>
                  <h3><i class="ph-bold ph-archive-box" style="color: #059669;"></i> System Archives &amp; Data Retrieval Ledger</h3>
                  <p style="color: #64748b; font-size: 0.8rem; margin: 2px 0 0;">Inspect safely preserved historical snapshots and restore records directly back into active production tables.</p>
                </div>
                <button type="button" class="btn btn-outline btn-sm font-roboto" onclick="loadExecutiveArchivesList()" style="gap: 0.35rem; display: inline-flex; align-items: center; font-size: 0.78rem;">
                  <i class="ph-bold ph-arrows-clockwise"></i> Refresh Archives
                </button>
              </div>

              <div id="execArchivesListContainer">
                <div style="text-align: center; padding: 2rem; color: #64748b;">
                  <i class="ph ph-spinner-gap ph-spin" style="font-size: 1.5rem;"></i>
                  <p style="margin-top: 0.5rem; font-size: 0.8rem;">Loading archived snapshots...</p>
                </div>
              </div>
            </div>

            <!-- Card 3: Non-Destructive System Flush & Baseline Reset -->
            <div class="settings-card" style="border: 1px solid #fecdd3;">
              <div class="settings-card-header" style="border-bottom-color: #fecdd3;">
                <div>
                  <h3 style="color: #b91c1c;"><i class="ph-bold ph-warning-octagon" style="color: #dc2626;"></i> Reset &amp; Flush System Data</h3>
                  <p style="color: #b91c1c; font-size: 0.8rem; margin: 2px 0 0;">All flushed records are automatically archived before clearing, enabling complete non-destructive restoration.</p>
                </div>
                <span class="badge rose" style="font-weight: 700;">Executive Reset Engine</span>
              </div>

              <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1rem; font-size: 0.825rem; color: #991b1b; display: flex; align-items: center; gap: 0.65rem;">
                <i class="ph-bold ph-shield-check" style="font-size: 1.5rem; color: #dc2626; flex-shrink: 0;"></i>
                <div>
                  <strong>Automated Non-Destructive Archiving:</strong> A complete snapshot batch is automatically created before any tables are touched.
                </div>
              </div>

              <div style="margin-bottom: 1.25rem;">
                <label style="font-size: 0.85rem; font-weight: 700; color: var(--text-main, #0f172a); margin-bottom: 0.5rem; display: block;">Select Reset Mode:</label>
                <div class="flush-option-list" style="display: flex; flex-direction: column; gap: 0.65rem;">
                  <label class="flush-option-label active" id="execOptLabelActivity" onclick="selectExecFlushMode('activity')" style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.85rem 1rem; border: 1.5px solid #2563eb; background: #eff6ff; border-radius: 8px; cursor: pointer;">
                    <input type="radio" name="exec_flush_mode" value="activity" checked style="margin-top: 3px;">
                    <div>
                      <strong style="color: #1e40af; font-size: 0.875rem; display: block;">🧹 Flush Operational Activity</strong>
                      <span style="color: #475569; font-size: 0.78rem;">Archives all events, proposals, requests, milestones, and reports. <strong>User accounts and partner chapters remain intact.</strong></span>
                    </div>
                  </label>

                  <label class="flush-option-label" id="execOptLabelFull" onclick="selectExecFlushMode('full')" style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.85rem 1rem; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer;">
                    <input type="radio" name="exec_flush_mode" value="full" style="margin-top: 3px;">
                    <div>
                      <strong style="color: #0f172a; font-size: 0.875rem; display: block;">🔄 Factory Reset to Baseline</strong>
                      <span style="color: #475569; font-size: 0.78rem;">Archives current records and restores original baseline chapters and default administrative accounts.</span>
                    </div>
                  </label>

                  <label class="flush-option-label" id="execOptLabelDemo" onclick="selectExecFlushMode('demo')" style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.85rem 1rem; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer;">
                    <input type="radio" name="exec_flush_mode" value="demo" style="margin-top: 3px;">
                    <div>
                      <strong style="color: #0f172a; font-size: 0.875rem; display: block;">🚀 Reset &amp; Seed Demo Data</strong>
                      <span style="color: #475569; font-size: 0.78rem;">Archives current records, resets tables, and populates fresh demonstration events, compliance countdowns, and proposals.</span>
                    </div>
                  </label>
                </div>
              </div>

              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.825rem; font-weight: 700; color: #dc2626; margin-bottom: 0.35rem;">
                  <i class="ph-bold ph-shield-warning"></i> Security Check: Type "FLUSH" to proceed
                </label>
                <input type="text" id="execFlushConfirmInput" placeholder="Type FLUSH to confirm" oninput="checkExecFlushInput()" style="width: 100%; max-width: 320px; padding: 0.55rem 0.75rem; border-radius: 6px; border: 1.5px solid #cbd5e1; font-weight: 700; font-size: 0.875rem;">
              </div>

              <button type="button" class="btn btn-primary font-roboto" id="btnExecExecuteFlush" onclick="executeSettingsFlush()" disabled style="background: #dc2626; border-color: #b91c1c; font-weight: 700; padding: 0.6rem 1.25rem; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 0.45rem; opacity: 0.5; cursor: not-allowed;">
                <i class="ph-bold ph-trash"></i> Execute System Reset &amp; Archive
              </button>
            </div>

          </div>

        </div>
      </main>
    </div>
  </div>

  <!-- Client-side Logic for Settings & Theme Sync -->
  <script>
    let pendingLogoData = null;

    document.addEventListener('DOMContentLoaded', () => {
      initSettingsPage();
      loadSystemSettings();
      checkSystemHealth();
    });

    function initSettingsPage() {
      // 1. Theme sync
      const savedTheme = localStorage.getItem('ytr_theme') || 'light';
      updateThemeSelectorUI(savedTheme);

      // 2. Table density
      const density = localStorage.getItem('ytr_table_density') || 'comfortable';
      const densityBox = document.getElementById('settingCompactDensity');
      if (densityBox) densityBox.checked = (density === 'compact');

      // 3. Sound setting
      const soundPref = localStorage.getItem('ytr_sound_notifications');
      const soundBox = document.getElementById('settingSoundToggle');
      if (soundBox && soundPref !== null) soundBox.checked = (soundPref === '1');

      // 4. Reminder Lead Days
      const lead = localStorage.getItem('ytr_lead_days') || '3';
      const rad = document.querySelector(`input[name="leadDays"][value="${lead}"]`);
      if (rad) rad.checked = true;

      // 5. Session Timeout
      const timeout = localStorage.getItem('ytr_session_timeout') || '120';
      const sel = document.getElementById('sessionTimeoutSelect');
      if (sel) sel.value = timeout;
    }

    function switchSettingsTab(tabName) {
      if (tabName === 'executive' && !isChiefExecutiveUser()) {
        alert('Access Denied: Chief Executive Officer authorization required.');
        return;
      }
      document.querySelectorAll('.settings-nav-btn').forEach(b => b.classList.remove('active'));
      document.querySelectorAll('.settings-tab-pane').forEach(p => p.style.display = 'none');

      const btn = document.getElementById(`tab-btn-${tabName}`);
      const pane = document.getElementById(`tab-pane-${tabName}`);
      if (btn) btn.classList.add('active');
      if (pane) pane.style.display = 'block';

      if (tabName === 'executive') {
        loadExecutiveSummaryStats();
        loadExecutiveArchivesList();
      }
    }

    // ==========================================
    // THEME & DARK MODE MANAGEMENT
    // ==========================================
    function chooseTheme(theme) {
      localStorage.setItem('ytr_theme', theme);
      document.cookie = `ytr_theme=${theme}; path=/; max-age=31536000; SameSite=Lax`;
      
      const shouldBeDark = (theme === 'dark') || (theme === 'system' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
      
      if (shouldBeDark) {
        document.documentElement.classList.add('dark-theme');
        document.body.classList.add('dark-theme');
      } else {
        document.documentElement.classList.remove('dark-theme');
        document.body.classList.remove('dark-theme');
      }

      updateThemeSelectorUI(theme);
      if (typeof updateTopbarThemeIcon === 'function') {
        updateTopbarThemeIcon(shouldBeDark ? 'dark' : 'light');
      }
    }

    function updateThemeSelectorUI(theme) {
      document.querySelectorAll('.theme-option-box').forEach(b => b.classList.remove('active'));
      const activeBox = document.getElementById(`theme-box-${theme}`);
      if (activeBox) activeBox.classList.add('active');

      const badge = document.getElementById('activeThemeBadge');
      if (badge) {
        if (theme === 'dark') {
          badge.textContent = 'Dark Mode Active';
          badge.style.background = '#312e81';
          badge.style.color = '#c7d2fe';
        } else if (theme === 'system') {
          badge.textContent = 'System Adaptive';
          badge.style.background = '#f0fdf4';
          badge.style.color = '#166534';
        } else {
          badge.textContent = 'Light Mode Active';
          badge.style.background = '#f1f5f9';
          badge.style.color = '#475569';
        }
      }
    }

    function toggleTableDensity(isCompact) {
      localStorage.setItem('ytr_table_density', isCompact ? 'compact' : 'comfortable');
      if (isCompact) {
        document.body.classList.add('table-density-compact');
      } else {
        document.body.classList.remove('table-density-compact');
      }
    }

    function toggleSoundPreference(enabled) {
      localStorage.setItem('ytr_sound_notifications', enabled ? '1' : '0');
    }

    // Audio synthesizer chime using Web Audio API (zero external asset dependencies)
    function playChimeSound() {
      try {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (!AudioContext) return;
        const ctx = new AudioContext();
        
        // Tone 1: C5 (523Hz)
        const osc1 = ctx.createOscillator();
        const gain1 = ctx.createGain();
        osc1.type = 'sine';
        osc1.frequency.setValueAtTime(523.25, ctx.currentTime);
        gain1.gain.setValueAtTime(0.15, ctx.currentTime);
        gain1.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
        osc1.connect(gain1);
        gain1.connect(ctx.destination);
        osc1.start(ctx.currentTime);
        osc1.stop(ctx.currentTime + 0.4);

        // Tone 2: E5 (659.25Hz) after slight delay
        setTimeout(() => {
          try {
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(659.25, ctx.currentTime);
            gain2.gain.setValueAtTime(0.18, ctx.currentTime);
            gain2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(ctx.currentTime);
            osc2.stop(ctx.currentTime + 0.5);
          } catch(e) {}
        }, 80);
      } catch(e) {
        console.warn('AudioContext not allowed or supported:', e);
      }
    }

    // ==========================================
    // LOGO & BRANDING (CEO ONLY)
    // ==========================================
    async function loadSystemSettings() {
      try {
        const res = await YTR_API.system.getSettings();
        if (res.status === 'success' && res.data) {
          const s = res.data;
          if (s.logo_path) {
            applySystemLogoToDOM(s.logo_path);
          }
          if (document.getElementById('orgNameInput') && s.organization_name) {
            document.getElementById('orgNameInput').value = s.organization_name;
          }
          if (document.getElementById('orgTaglineInput') && s.tagline) {
            document.getElementById('orgTaglineInput').value = s.tagline;
          }
          if (document.getElementById('orgEmailInput') && s.contact_email) {
            document.getElementById('orgEmailInput').value = s.contact_email;
          }
          if (document.getElementById('orgFiscalYearInput') && s.fiscal_year) {
            document.getElementById('orgFiscalYearInput').value = s.fiscal_year;
          }
        }
      } catch (err) {
        console.warn('Could not load system settings from backend:', err);
      }
    }

    function applySystemLogoToDOM(logoUrl) {
      if (!logoUrl) return;
      localStorage.setItem('ytr_custom_logo', logoUrl);

      const preview = document.getElementById('currentActiveLogoPreview');
      if (preview) preview.src = logoUrl;

      // Update all logo elements across topbar, sidebars, headers
      document.querySelectorAll('.sidebar-logo, .icon img, #topbar-ytr-brand-logo, #LOGO, .ytr-system-logo').forEach(img => {
        if (img && img.tagName === 'IMG') {
          img.src = logoUrl;
        }
      });
    }

    function handleLogoFileSelect(e) {
      const file = e.target.files && e.target.files[0];
      if (file) stageLogoFile(file);
    }

    function handleLogoDrop(e) {
      e.preventDefault();
      const dropzone = document.querySelector('.logo-upload-dropzone');
      if (dropzone) dropzone.style.borderColor = '';
      const file = e.dataTransfer.files && e.dataTransfer.files[0];
      if (file) stageLogoFile(file);
    }

    function stageLogoFile(file) {
      if (file.size > 5 * 1024 * 1024) {
        alert('File size exceeds the 5MB limit. Please choose a smaller image.');
        return;
      }
      const reader = new FileReader();
      reader.onload = function(evt) {
        pendingLogoData = {
          file: file,
          base64: evt.target.result
        };
        const preview = document.getElementById('currentActiveLogoPreview');
        if (preview) preview.src = evt.target.result;
        
        const actionBox = document.getElementById('pendingLogoActionContainer');
        if (actionBox) actionBox.style.display = 'flex';
      };
      reader.readAsDataURL(file);
    }

    function cancelPendingLogo() {
      pendingLogoData = null;
      const actionBox = document.getElementById('pendingLogoActionContainer');
      if (actionBox) actionBox.style.display = 'none';

      const origLogo = localStorage.getItem('ytr_custom_logo') || 'assets/images/LOGO.jpg';
      const preview = document.getElementById('currentActiveLogoPreview');
      if (preview) preview.src = origLogo;
    }

    async function deployNewLogo() {
      if (!pendingLogoData) return;
      const btn = document.getElementById('btnDeployLogo');
      btn.disabled = true;
      btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Deploying Logo...';

      try {
        const formData = new FormData();
        formData.append('logo', pendingLogoData.file);
        
        const res = await YTR_API.system.uploadLogo(formData);
        if (res.status === 'success' && res.data) {
          applySystemLogoToDOM(res.data.logo_url);
          pendingLogoData = null;
          const actionBox = document.getElementById('pendingLogoActionContainer');
          if (actionBox) actionBox.style.display = 'none';
          alert('Success: Organization logo deployed system-wide!');
        } else {
          alert('Failed to deploy logo: ' + (res.message || 'Unknown error'));
        }
      } catch (err) {
        alert('Network Error deploying logo: ' + err.message);
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="ph-bold ph-floppy-disk"></i> Save &amp; Deploy Logo';
      }
    }

    async function resetToDefaultLogo() {
      if (!confirm("Are you sure you want to reset the organization logo to the default YTR emblem?")) {
        return;
      }
      try {
        const res = await YTR_API.system.resetLogo();
        if (res.status === 'success') {
          applySystemLogoToDOM('assets/images/LOGO.jpg');
          alert('Organization logo has been restored to default.');
        } else {
          alert('Failed to reset logo: ' + (res.message || 'Unknown error'));
        }
      } catch (err) {
        alert('Error: ' + err.message);
      }
    }

    async function saveOrgIdentityDetails() {
      const payload = {
        organization_name: (document.getElementById('orgNameInput')?.value || '').trim(),
        tagline: (document.getElementById('orgTaglineInput')?.value || '').trim(),
        contact_email: (document.getElementById('orgEmailInput')?.value || '').trim(),
        fiscal_year: (document.getElementById('orgFiscalYearInput')?.value || '').trim()
      };
      try {
        const res = await YTR_API.system.saveSettings(payload);
        if (res.status === 'success') {
          alert('Organization details updated successfully.');
        } else {
          alert('Update failed: ' + (res.message || 'Unknown error'));
        }
      } catch(err) {
        alert('Network error: ' + err.message);
      }
    }

    // ==========================================
    // NOTIFICATIONS & SECURITY
    // ==========================================
    function saveReminderLeadDays(days) {
      localStorage.setItem('ytr_lead_days', String(days));
      YTR_API.system.saveSettings({ reminder_lead_days: String(days) }).catch(() => {});
    }

    // ==========================================
    // CEO Clearance Detection & Dynamic DOM Sync
    // ==========================================
    function isChiefExecutiveUser() {
      try {
        const raw = localStorage.getItem('ytr_user');
        if (!raw) return false;
        const u = JSON.parse(raw);
        if (!u) return false;
        const pos = (u.position || '').trim().toLowerCase();
        const email = (u.email || '').trim().toLowerCase();
        const ceoTitles = [
          'chief executive officer',
          'chief executive operation',
          'chief executive',
          'ceo',
          'executive director'
        ];
        return ceoTitles.includes(pos) || 
               email === 'admin@ytr.org' || 
               email === 'ytr.org@gmail.com' || 
               email === 'ytr.national@gmail.com' ||
               String(u.id) === '1' ||
               (u.role === 'national_admin' && pos.includes('executive'));
      } catch(e) { return false; }
    }

    function syncCEOBrandingPermissions() {
      const isCEO = isChiefExecutiveUser();
      const lockBanner = document.getElementById('ceoLockBanner');
      const ceoBadge = document.getElementById('ceoClearanceBadge');
      const nonCeoBadge = document.getElementById('nonCeoBadge');
      const ceoLogoControls = document.getElementById('ceoLogoActionControls');
      const ceoDropzone = document.getElementById('ceoDropzone');
      const saveOrgBtn = document.getElementById('btnSaveOrgDetailsContainer');
      const orgInputs = document.querySelectorAll('.ceo-editable-input');
      const subtitle = document.getElementById('logoCardSubtitle');

      const execTabBtn = document.getElementById('tab-btn-executive');
      if (execTabBtn) {
        execTabBtn.style.display = isCEO ? 'inline-flex' : 'none';
      }

      if (isCEO) {
        if (lockBanner) lockBanner.style.display = 'none';
        if (ceoBadge) ceoBadge.style.display = 'inline-flex';
        if (nonCeoBadge) nonCeoBadge.style.display = 'none';
        if (ceoLogoControls) ceoLogoControls.style.display = 'flex';
        if (ceoDropzone) ceoDropzone.style.display = 'block';
        if (saveOrgBtn) saveOrgBtn.style.display = 'flex';
        if (subtitle) subtitle.textContent = 'Upload and deploy a new official organization logo across all portals, dossiers, headers, and login screens.';
        orgInputs.forEach(inp => {
          inp.removeAttribute('readonly');
          inp.style.cursor = 'text';
          inp.style.backgroundColor = '';
        });
      } else {
        if (lockBanner) lockBanner.style.display = 'flex';
        if (ceoBadge) ceoBadge.style.display = 'none';
        if (nonCeoBadge) nonCeoBadge.style.display = 'inline-flex';
        if (ceoLogoControls) ceoLogoControls.style.display = 'none';
        if (ceoDropzone) ceoDropzone.style.display = 'none';
        if (saveOrgBtn) saveOrgBtn.style.display = 'none';
        if (subtitle) subtitle.textContent = 'Current active organization logo deployed across all portals (Read Only).';
        orgInputs.forEach(inp => {
          inp.setAttribute('readonly', 'true');
          inp.style.cursor = 'not-allowed';
          inp.style.backgroundColor = '#f1f5f9';
        });
      }
    }

    // ==========================================
    // Session Watchdog Handler & Simulation
    // ==========================================
    function setSessionTimeout(mins) {
      const m = parseInt(mins, 10);
      localStorage.setItem('ytr_session_timeout', String(m));
      localStorage.setItem('ytr_last_activity', String(Date.now()));
      updateWatchdogBadge(m);
      if (m > 0) {
        alert('Session idle timeout set to ' + m + ' minutes.\n\nThe platform watchdog is now active and will automatically sign you out after ' + m + ' minutes of total inactivity.');
      } else {
        alert('Session idle timeout disabled. Your account will remain signed in indefinitely on this device.');
      }
    }

    function updateWatchdogBadge(m) {
      const badge = document.getElementById('idleWatchdogStatusBadge');
      if (!badge) return;
      if (m > 0) {
        badge.style.background = '#ecfdf5';
        badge.style.color = '#047857';
        badge.style.borderColor = '#a7f3d0';
        badge.innerHTML = '<i class="ph-bold ph-shield-check"></i> Watchdog Active: ' + m + ' mins';
      } else {
        badge.style.background = '#f1f5f9';
        badge.style.color = '#64748b';
        badge.style.borderColor = '#cbd5e1';
        badge.innerHTML = '<i class="ph-bold ph-prohibit"></i> Watchdog Disabled';
      }
    }

    function startIdleTestSimulation() {
      if (!confirm("Test Idle Expiration Simulation?\n\nThis will simulate your selected idle inactivity and trigger the automated sign-out process after a 5-second countdown.")) {
        return;
      }
      let remainingSecs = 5;
      const overlay = document.createElement('div');
      overlay.id = 'idleSimOverlay';
      overlay.style.cssText = 'position: fixed; inset: 0; background: rgba(15, 23, 42, 0.82); z-index: 999999; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(4px); font-family: Inter, sans-serif;';
      overlay.innerHTML = `
        <div style="background: #ffffff; border-radius: 12px; padding: 2rem; max-width: 440px; text-align: center; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
          <div style="width: 56px; height: 56px; border-radius: 50%; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 1rem;">
            <i class="ph-bold ph-hourglass-high"></i>
          </div>
          <h3 style="margin: 0 0 0.5rem; font-size: 1.15rem; color: #0f172a; font-weight: 800;">Simulating Inactivity Expiration</h3>
          <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1.25rem;">
            Inactivity watchdog triggered. Simulating session idle threshold. Auto-signing out in:
          </p>
          <div id="idleSimCountdown" style="font-size: 2.25rem; font-weight: 900; color: #dc2626; margin-bottom: 1.25rem;">5s</div>
          <button type="button" class="btn btn-outline btn-sm font-roboto" onclick="document.body.removeChild(document.getElementById('idleSimOverlay')); clearInterval(window.__idleSimInterval);" style="padding: 0.4rem 0.85rem;">Cancel Simulation</button>
        </div>
      `;
      document.body.appendChild(overlay);

      window.__idleSimInterval = setInterval(() => {
        remainingSecs--;
        const cnt = document.getElementById('idleSimCountdown');
        if (cnt) cnt.textContent = remainingSecs + 's';
        if (remainingSecs <= 0) {
          clearInterval(window.__idleSimInterval);
          const ov = document.getElementById('idleSimOverlay');
          if (ov && ov.parentNode) ov.parentNode.removeChild(ov);
          const currentTimeout = localStorage.getItem('ytr_session_timeout') || '30';
          if (typeof window.ytrHandleIdleTimeout === 'function') {
            window.ytrHandleIdleTimeout(parseInt(currentTimeout, 10));
          } else {
            alert('Session Expired: You have been signed out due to ' + currentTimeout + ' minutes of inactivity.');
            window.location.href = 'login.php?timeout=1&mins=' + encodeURIComponent(currentTimeout);
          }
        }
      }, 1000);
    }

    function requestBrowserPush() {
      if (!('Notification' in window)) {
        alert('This browser does not support desktop notifications.');
        return;
      }
      Notification.requestPermission().then(permission => {
        const btn = document.getElementById('btnRequestBrowserPush');
        if (permission === 'granted') {
          if (btn) {
            btn.innerHTML = '<i class="ph-bold ph-check"></i> Permission Granted';
            btn.className = 'btn btn-outline btn-sm font-roboto text-emerald';
          }
          new Notification('Youth on the Rock Movement', {
            body: 'Desktop notifications are successfully enabled for your session.',
            icon: localStorage.getItem('ytr_custom_logo') || 'assets/images/LOGO.jpg'
          });
        } else {
          alert('Notification permission: ' + permission);
        }
      });
    }

    function purgeAppCache() {
      if (confirm('Clear local application cache and reload clean workspace?')) {
        const keepUser = localStorage.getItem('ytr_user');
        const keepToken = localStorage.getItem('ytr_token');
        const keepTheme = localStorage.getItem('ytr_theme');
        const keepLogo = localStorage.getItem('ytr_custom_logo');

        localStorage.clear();

        if (keepUser) localStorage.setItem('ytr_user', keepUser);
        if (keepToken) localStorage.setItem('ytr_token', keepToken);
        if (keepTheme) localStorage.setItem('ytr_theme', keepTheme);
        if (keepLogo) localStorage.setItem('ytr_custom_logo', keepLogo);

        window.location.reload();
      }
    }

    async function checkSystemHealth() {
      try {
        const res = await YTR_API.system.getHealth();
        if (res.status === 'success' && res.data) {
          const d = res.data;
          const timeEl = document.getElementById('healthServerTime');
          if (timeEl && d.server_time) {
            timeEl.textContent = 'UTC: ' + d.server_time.replace('T', ' ').substring(0, 19);
          }
        }
      } catch (err) {
        console.warn('Health check unreachable:', err);
      }
    }
  </script>

</body>
</html>
