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
      <div class="profile-avatar">--</div>
      <div class="profile-info font-roboto">
        <h4>Loading...</h4>
        <p>Authenticating...</p>
      </div>
      <i class="ph ph-caret-down" style="color: #6b7280; font-size: 0.875rem;"></i>
    </button>

    <div id="profile-dropdown" class="dropdown-menu font-roboto">
      <div class="dropdown-header">
        <h4 id="topbar-dropdown-user-name" style="font-size: 0.875rem; color: #0f172a; font-weight: bold;">Loading...</h4>
        <p id="topbar-dropdown-user-email" style="font-size: 0.75rem; color: #64748b;">user@ytr.org</p>
      </div>
      <a href="#" class="dropdown-item" onclick="openProfileModal(event)"><i class="ph ph-user"></i> My Profile</a>
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
      <a href="login.php" class="dropdown-item" onclick="ytrGlobalSignOut(event)"><i class="ph ph-arrows-clockwise"></i> Switch Account</a>
      <a href="login.php" class="dropdown-item text-rose" onclick="ytrGlobalSignOut(event)"><i class="ph ph-sign-out"></i> Sign Out</a>
    </div>
  </div>
</header>


<!-- TOPBAR USER PROFILE MODAL -->
<div id="topbarProfileModal" class="modal-overlay hidden" style="display: none; z-index: 9999;">
  <div class="modal-md" style="max-width: 560px; width: 95%;">
    <!-- Header -->
    <div class="modal-header-banner" style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); color: #ffffff; padding: 1.15rem 1.4rem; border-top-left-radius: 12px; border-top-right-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
      <div style="display: flex; align-items: center; gap: 0.85rem;">
        <div id="profileModalAvatarLarge" style="width: 44px; height: 44px; border-radius: 50%; background: #ffffff; color: #1e3a8a; font-weight: 800; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
          US
        </div>
        <div>
          <h3 id="profileModalHeaderTitle" style="color: #ffffff; margin: 0; font-size: 1.05rem; font-weight: 700;">Account Profile</h3>
          <p id="profileModalHeaderSubtitle" style="color: #bfdbfe; margin: 0.15rem 0 0; font-size: 0.75rem;">Manage your profile credentials and digital signature</p>
        </div>
      </div>
      <button type="button" onclick="closeProfileModal()" style="background: rgba(255,255,255,0.15); border: none; color: #ffffff; width: 30px; height: 30px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
        <i class="ph ph-x"></i>
      </button>
    </div>

    <!-- Body -->
    <div class="modal-body-scroll" style="background: #f8fafc; padding: 1.25rem 1.4rem; max-height: calc(85vh - 120px); overflow-y: auto;">
      <!-- Alerts -->
      <div id="profileModalAlert" style="display: none; padding: 0.7rem 0.9rem; border-radius: 8px; margin-bottom: 0.9rem; font-size: 0.825rem; font-weight: 600;"></div>

      <!-- Account Badge Row -->
      <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.75rem 0.95rem; margin-bottom: 0.9rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <div>
          <span style="font-size: 0.68rem; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Role &amp; Assignment</span>
          <div style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.2rem;">
            <span id="profileModalRoleBadge" class="badge" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-weight: 700; padding: 2px 7px; font-size: 0.72rem;">National Admin</span>
            <span id="profileModalChapterBadge" class="badge" style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-weight: 700; padding: 2px 7px; font-size: 0.72rem;">National Office</span>
          </div>
        </div>
        <div style="text-align: right;">
          <span style="font-size: 0.68rem; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Account Status</span>
          <div style="margin-top: 0.2rem;">
            <span id="profileModalStatusBadge" class="badge" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-weight: 700; padding: 2px 7px; font-size: 0.72rem;">
              <i class="ph-fill ph-check-circle"></i> Active
            </span>
          </div>
        </div>
      </div>

      <!-- Profile Form -->
      <form id="topbarProfileForm" onsubmit="handleSaveProfile(event)">
        <!-- Profile Picture Upload Card -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.95rem 1.1rem; margin-bottom: 0.9rem;">
          <h4 style="margin: 0 0 0.75rem; font-size: 0.85rem; color: #1e293b; font-weight: 700; display: flex; align-items: center; gap: 0.4rem;">
            <i class="ph-bold ph-user-circle" style="color: #2563eb;"></i> Profile Picture
          </h4>
          <input type="file" id="profilePhotoFileInput" accept="image/*" style="display: none;" onchange="handleProfilePhotoSelect(event)">
          <div style="display: flex; align-items: center; gap: 1.1rem;">
            <div style="position: relative; width: 68px; height: 68px; flex-shrink: 0;">
              <div id="profilePhotoPreviewCircle" style="width: 68px; height: 68px; border-radius: 50%; background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); color: #ffffff; font-weight: 800; font-size: 1.35rem; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 2px solid #bfdbfe; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <span id="profilePhotoInitials">US</span>
                <img id="profilePhotoImg" src="" alt="Profile Photo" style="display: none; width: 100%; height: 100%; object-fit: cover;">
              </div>
              <button type="button" onclick="document.getElementById('profilePhotoFileInput').click()" title="Change Profile Picture" style="position: absolute; bottom: -2px; right: -2px; background: #2563eb; color: #ffffff; border: 2px solid #ffffff; width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.8rem; box-shadow: 0 2px 4px rgba(0,0,0,0.15);">
                <i class="ph-bold ph-camera"></i>
              </button>
            </div>
            <div style="flex: 1;">
              <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <button type="button" class="btn btn-sm btn-primary font-roboto" onclick="document.getElementById('profilePhotoFileInput').click()" style="padding: 0.35rem 0.8rem; font-size: 0.78rem;">
                  <i class="ph-bold ph-upload-simple"></i> Upload Photo
                </button>
                <button type="button" id="btnRemoveProfilePhoto" class="btn btn-sm btn-outline font-roboto" onclick="removeProfilePhoto()" style="display: none; padding: 0.35rem 0.8rem; font-size: 0.78rem; color: #dc2626; border-color: #fecdd3;">
                  Remove
                </button>
              </div>
              <p style="font-size: 0.7rem; color: #64748b; margin: 0.35rem 0 0;">Upload a personal avatar (PNG, JPG, WEBP). It will appear across your topbar, reports, and activity logs.</p>
            </div>
          </div>
        </div>

        <!-- Personal Details Card -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.95rem 1.1rem; margin-bottom: 0.9rem;">
          <h4 style="margin: 0 0 0.75rem; font-size: 0.85rem; color: #1e293b; font-weight: 700; display: flex; align-items: center; gap: 0.4rem;">
            <i class="ph-bold ph-identification-card" style="color: #2563eb;"></i> Personal Details
          </h4>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
            <div>
              <label style="display: block; font-size: 0.72rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Full Name *</label>
              <input type="text" id="profileModalFullName" required style="width: 100%; box-sizing: border-box; padding: 0.5rem 0.7rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.825rem; color: #0f172a; outline: none;">
            </div>
            <div>
              <label style="display: block; font-size: 0.72rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Email Address</label>
              <input type="email" id="profileModalEmail" disabled style="width: 100%; box-sizing: border-box; padding: 0.5rem 0.7rem; border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 6px; font-size: 0.825rem; color: #64748b; cursor: not-allowed;">
            </div>
          </div>

          <div style="margin-top: 0.75rem;">
            <label style="display: block; font-size: 0.72rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Official Position / Designation *</label>
            <input type="text" id="profileModalPosition" required placeholder="e.g. Chapter Coordinator, Executive Director" style="width: 100%; box-sizing: border-box; padding: 0.5rem 0.7rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.825rem; color: #0f172a; outline: none;">
          </div>
        </div>

        <!-- Security / Password Accordion -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.95rem 1.1rem; margin-bottom: 0.9rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onclick="toggleProfilePasswordFields()">
            <h4 style="margin: 0; font-size: 0.85rem; color: #1e293b; font-weight: 700; display: flex; align-items: center; gap: 0.4rem;">
              <i class="ph-bold ph-key" style="color: #f59e0b;"></i> Change Password
            </h4>
            <span id="profilePassToggleText" style="font-size: 0.75rem; color: #2563eb; font-weight: 700;">Show +</span>
          </div>

          <div id="profilePasswordFields" style="display: none; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px dashed #e2e8f0;">
            <div style="margin-bottom: 0.65rem;">
              <label style="display: block; font-size: 0.72rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Current Password</label>
              <input type="password" id="profileModalCurrentPass" placeholder="Enter current password" style="width: 100%; box-sizing: border-box; padding: 0.5rem 0.7rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.825rem;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
              <div>
                <label style="display: block; font-size: 0.72rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">New Password</label>
                <input type="password" id="profileModalNewPass" placeholder="Min. 8 characters" style="width: 100%; box-sizing: border-box; padding: 0.5rem 0.7rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.825rem;">
              </div>
              <div>
                <label style="display: block; font-size: 0.72rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Confirm New Password</label>
                <input type="password" id="profileModalConfirmPass" placeholder="Repeat new password" style="width: 100%; box-sizing: border-box; padding: 0.5rem 0.7rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.825rem;">
              </div>
            </div>
            <p style="font-size: 0.68rem; color: #64748b; margin: 0.35rem 0 0;">Leave password fields blank if you only want to update your name or signature.</p>
          </div>
        </div>

        <!-- Digital E-Signature Section -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.95rem 1.1rem; margin-bottom: 0.9rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <h4 style="margin: 0; font-size: 0.85rem; color: #1e293b; font-weight: 700; display: flex; align-items: center; gap: 0.4rem;">
              <i class="ph-bold ph-pen-nib" style="color: #7c3aed;"></i> Official Digital Signature
            </h4>
            <button type="button" onclick="clearProfileSignatureCanvas()" style="background: transparent; border: none; font-size: 0.72rem; color: #dc2626; cursor: pointer; font-weight: 700;">
              Clear / Redraw
            </button>
          </div>
          <p style="font-size: 0.7rem; color: #64748b; margin: 0 0 0.5rem;">
            Applied automatically when submitting proposals, validating event reports, and endorsing documentation.
          </p>
          <div style="background: #ffffff; border: 1px dashed #94a3b8; border-radius: 8px; position: relative; text-align: center;">
            <canvas id="profileSignatureCanvas" width="460" height="110" style="width: 100%; height: 110px; touch-action: none; cursor: crosshair; display: block;"></canvas>
            <div id="profileSignaturePlaceholder" style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; pointer-events: none; color: #94a3b8; font-size: 0.78rem;">
              <span><i class="ph ph-pencil-simple-line"></i> Draw your signature here using mouse or touch</span>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div style="display: flex; justify-content: flex-end; gap: 0.6rem; margin-top: 0.9rem;">
          <button type="button" class="btn btn-outline" onclick="closeProfileModal()" style="padding: 0.5rem 1.15rem; font-size: 0.825rem;">Cancel</button>
          <button type="submit" id="btnSaveProfileModal" class="btn btn-primary" style="padding: 0.5rem 1.4rem; font-size: 0.825rem; display: flex; align-items: center; gap: 0.4rem;">
            <i class="ph-bold ph-floppy-disk"></i> <span>Save Changes</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

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
    });    // Sync Topbar with Logged In User
    try {
      const rawUser = localStorage.getItem('ytr_user');
      if (!rawUser) {
        ytrGlobalSignOut();
        return;
      }
      const u = JSON.parse(rawUser);
      if (!u || !u.id) {
        ytrGlobalSignOut();
        return;
      }

      const initials = u.full_name ? u.full_name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : 'US';
      const avatar = document.querySelector('.profile-avatar');
      if (avatar) {
        if (u.profile_picture) {
          avatar.innerHTML = `<img src="${u.profile_picture}" alt="${u.full_name || 'Profile'}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
        } else {
          avatar.textContent = initials;
        }
      }

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

      const ddName = document.getElementById('topbar-dropdown-user-name');
      if (ddName) ddName.textContent = u.full_name || 'User';

      const ddEmail = document.getElementById('topbar-dropdown-user-email');
      if (ddEmail) ddEmail.textContent = u.email || 'user@ytr.org';

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
              <h4 style="font-size: 0.875rem; color: #0f172a; font-weight: bold;">${escapeHtml(u.full_name || 'Chapter Admin')}</h4>
              <p style="font-size: 0.75rem; color: #64748b;">${escapeHtml(u.position || 'Chapter Officer')} (${escapeHtml(u.chapter_name || 'Chapter')})</p>
            </div>
            <a href="#" class="dropdown-item" onclick="openProfileModal(event)"><i class="ph ph-user"></i> My Profile</a>
            <a href="ChapterDashboard.php" class="dropdown-item"><i class="ph ph-squares-four"></i> Chapter Dashboard</a>
            <a href="ChapterProposal.php" class="dropdown-item"><i class="ph ph-file-plus"></i> My Event Proposals</a>
            <a href="ChapterReport.php" class="dropdown-item"><i class="ph ph-file-arrow-up"></i> Submit Event Report</a>
            <a href="ChapterRequest.php" class="dropdown-item"><i class="ph ph-image"></i> My Poster Requests</a>
            <div class="dropdown-divider"></div>
            <a href="login.php" class="dropdown-item" onclick="ytrGlobalSignOut(event)"><i class="ph ph-arrows-clockwise"></i> Switch Account</a>
            <a href="login.php" class="dropdown-item text-rose" onclick="ytrGlobalSignOut(event)"><i class="ph ph-sign-out"></i> Sign Out</a>
          `;
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

  // ==========================================
  // PROFILE MODAL LOGIC & CONTROLLER
  // ==========================================
  let selectedProfilePhotoData = null;

  function handleProfilePhotoSelect(e) {
    const file = e.target.files && e.target.files[0];
    if (!file) return;
    if (!file.type.startsWith('image/')) {
      alert('Please choose an image file (PNG, JPG, WEBP).');
      return;
    }
    if (file.size > 5 * 1024 * 1024) {
      alert('Photo must be less than 5MB.');
      return;
    }
    const reader = new FileReader();
    reader.onload = function(evt) {
      const dataUrl = evt.target.result;
      selectedProfilePhotoData = dataUrl;
      const pPhotoImg = document.getElementById('profilePhotoImg');
      const pPhotoInitials = document.getElementById('profilePhotoInitials');
      const btnRemove = document.getElementById('btnRemoveProfilePhoto');
      if (pPhotoImg) { pPhotoImg.src = dataUrl; pPhotoImg.style.display = 'block'; }
      if (pPhotoInitials) pPhotoInitials.style.display = 'none';
      if (btnRemove) btnRemove.style.display = 'inline-flex';
    };
    reader.readAsDataURL(file);
  }

  function removeProfilePhoto() {
    selectedProfilePhotoData = 'REMOVE';
    const pPhotoImg = document.getElementById('profilePhotoImg');
    const pPhotoInitials = document.getElementById('profilePhotoInitials');
    const btnRemove = document.getElementById('btnRemoveProfilePhoto');
    const input = document.getElementById('profilePhotoFileInput');
    if (input) input.value = '';
    if (pPhotoImg) { pPhotoImg.src = ''; pPhotoImg.style.display = 'none'; }
    if (pPhotoInitials) pPhotoInitials.style.display = 'block';
    if (btnRemove) btnRemove.style.display = 'none';
  }

  let profileCanvasInited = false;
  let profileCanvasIsDrawing = false;
  let profileCanvasHasSignature = false;

  function openProfileModal(e) {
    if (e) e.preventDefault();
    const dropdown = document.getElementById('profile-dropdown');
    if (dropdown) dropdown.classList.remove('show');

    const modal = document.getElementById('topbarProfileModal');
    if (!modal) return;

    let user = null;
    try {
      const raw = localStorage.getItem('ytr_user');
      if (raw) user = JSON.parse(raw);
    } catch(err) {}

    if (!user) {
      alert('Please sign in to view your profile.');
      return;
    }

    // Populate Modal Fields
    const initials = user.full_name ? user.full_name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : 'US';
    const av = document.getElementById('profileModalAvatarLarge');
    if (av) {
      if (user.profile_picture) {
        av.innerHTML = `<img src="${user.profile_picture}" alt="${user.full_name}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
      } else {
        av.textContent = initials;
      }
    }

    // Populate photo section
    const pPhotoImg = document.getElementById('profilePhotoImg');
    const pPhotoInitials = document.getElementById('profilePhotoInitials');
    const btnRemove = document.getElementById('btnRemoveProfilePhoto');
    if (user.profile_picture) {
      if (pPhotoImg) { pPhotoImg.src = user.profile_picture; pPhotoImg.style.display = 'block'; }
      if (pPhotoInitials) pPhotoInitials.style.display = 'none';
      if (btnRemove) btnRemove.style.display = 'inline-flex';
      selectedProfilePhotoData = user.profile_picture;
    } else {
      if (pPhotoImg) { pPhotoImg.src = ''; pPhotoImg.style.display = 'none'; }
      if (pPhotoInitials) { pPhotoInitials.textContent = initials; pPhotoInitials.style.display = 'block'; }
      if (btnRemove) btnRemove.style.display = 'none';
      selectedProfilePhotoData = null;
    }

    document.getElementById('profileModalFullName').value = user.full_name || '';
    document.getElementById('profileModalEmail').value = user.email || '';
    document.getElementById('profileModalPosition').value = user.position || '';

    const roleBadge = document.getElementById('profileModalRoleBadge');
    if (roleBadge) {
      const isCh = user.role === 'chapter_admin';
      roleBadge.textContent = isCh ? 'Chapter Admin' : 'National Admin';
      roleBadge.style.cssText = isCh 
        ? 'background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-weight: 700; padding: 2px 7px; font-size: 0.72rem;'
        : 'background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-weight: 700; padding: 2px 7px; font-size: 0.72rem;';
    }

    const chapterBadge = document.getElementById('profileModalChapterBadge');
    if (chapterBadge) {
      chapterBadge.textContent = user.chapter_name || (user.role === 'chapter_admin' ? 'Chapter Branch' : 'National Secretariat');
    }

    // Reset password fields and collapse
    document.getElementById('profileModalCurrentPass').value = '';
    document.getElementById('profileModalNewPass').value = '';
    document.getElementById('profileModalConfirmPass').value = '';
    const passFields = document.getElementById('profilePasswordFields');
    if (passFields) passFields.style.display = 'none';
    const toggleTxt = document.getElementById('profilePassToggleText');
    if (toggleTxt) toggleTxt.textContent = 'Show +';

    // Clear alert
    const alertBox = document.getElementById('profileModalAlert');
    if (alertBox) {
      alertBox.style.display = 'none';
      alertBox.textContent = '';
    }

    modal.style.display = 'flex';
    modal.classList.remove('hidden');

    // Init canvas
    setTimeout(() => {
      initProfileSignatureCanvas(user.signature_image);
    }, 100);
  }

  function closeProfileModal() {
    const modal = document.getElementById('topbarProfileModal');
    if (modal) {
      modal.style.display = 'none';
      modal.classList.add('hidden');
    }
  }

  function toggleProfilePasswordFields() {
    const fields = document.getElementById('profilePasswordFields');
    const toggleTxt = document.getElementById('profilePassToggleText');
    if (!fields) return;
    const isHidden = fields.style.display === 'none' || !fields.style.display;
    fields.style.display = isHidden ? 'block' : 'none';
    if (toggleTxt) toggleTxt.textContent = isHidden ? 'Hide -' : 'Show +';
  }

  function initProfileSignatureCanvas(existingSignature) {
    const canvas = document.getElementById('profileSignatureCanvas');
    const placeholder = document.getElementById('profileSignaturePlaceholder');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    ctx.lineWidth = 2.5;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = '#0f172a';

    // If existing signature exists, render it
    if (existingSignature && existingSignature.startsWith('data:image')) {
      const img = new Image();
      img.onload = () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        profileCanvasHasSignature = true;
        if (placeholder) placeholder.style.display = 'none';
      };
      img.src = existingSignature;
    } else {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      profileCanvasHasSignature = false;
      if (placeholder) placeholder.style.display = 'flex';
    }

    if (profileCanvasInited) return;
    profileCanvasInited = true;

    function getCoords(e) {
      const rect = canvas.getBoundingClientRect();
      const scaleX = canvas.width / rect.width;
      const scaleY = canvas.height / rect.height;
      const clientX = e.touches ? e.touches[0].clientX : e.clientX;
      const clientY = e.touches ? e.touches[0].clientY : e.clientY;
      return {
        x: (clientX - rect.left) * scaleX,
        y: (clientY - rect.top) * scaleY
      };
    }

    function startDraw(e) {
      e.preventDefault();
      profileCanvasIsDrawing = true;
      profileCanvasHasSignature = true;
      if (placeholder) placeholder.style.display = 'none';
      const p = getCoords(e);
      ctx.beginPath();
      ctx.moveTo(p.x, p.y);
    }

    function draw(e) {
      if (!profileCanvasIsDrawing) return;
      e.preventDefault();
      const p = getCoords(e);
      ctx.lineTo(p.x, p.y);
      ctx.stroke();
    }

    function stopDraw(e) {
      if (profileCanvasIsDrawing) {
        profileCanvasIsDrawing = false;
        ctx.closePath();
      }
    }

    canvas.addEventListener('mousedown', startDraw);
    canvas.addEventListener('mousemove', draw);
    window.addEventListener('mouseup', stopDraw);

    canvas.addEventListener('touchstart', startDraw, { passive: false });
    canvas.addEventListener('touchmove', draw, { passive: false });
    window.addEventListener('touchend', stopDraw);
  }

  function clearProfileSignatureCanvas() {
    const canvas = document.getElementById('profileSignatureCanvas');
    const placeholder = document.getElementById('profileSignaturePlaceholder');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    profileCanvasHasSignature = false;
    if (placeholder) placeholder.style.display = 'flex';
  }

  async function handleSaveProfile(e) {
    e.preventDefault();
    const alertBox = document.getElementById('profileModalAlert');
    const submitBtn = document.getElementById('btnSaveProfileModal');

    let user = null;
    try {
      const raw = localStorage.getItem('ytr_user');
      if (raw) user = JSON.parse(raw);
    } catch(err) {}

    if (!user || !user.id) {
      alert('Session expired. Please log in again.');
      return;
    }

    const fullName = (document.getElementById('profileModalFullName').value || '').trim();
    const position = (document.getElementById('profileModalPosition').value || '').trim();
    const currentPass = document.getElementById('profileModalCurrentPass').value;
    const newPass = document.getElementById('profileModalNewPass').value;
    const confirmPass = document.getElementById('profileModalConfirmPass').value;

    if (!fullName) {
      showProfileAlert('Full Name is required.', 'error');
      return;
    }

    if (newPass) {
      if (newPass.length < 8) {
        showProfileAlert('New password must be at least 8 characters long.', 'error');
        return;
      }
      if (newPass !== confirmPass) {
        showProfileAlert('New passwords do not match. Please verify.', 'error');
        return;
      }
      if (!currentPass) {
        showProfileAlert('Please enter your current password to set a new password.', 'error');
        return;
      }
    }

    // Get Signature data URL if drawn
    let sigDataUrl = null;
    if (profileCanvasHasSignature) {
      const canvas = document.getElementById('profileSignatureCanvas');
      if (canvas) sigDataUrl = canvas.toDataURL('image/png');
    }

    const payload = {
      user_id: user.id,
      email: user.email,
      full_name: fullName,
      position: position
    };
    if (selectedProfilePhotoData) {
      payload.profile_picture = selectedProfilePhotoData;
    }
    if (newPass) {
      payload.current_password = currentPass;
      payload.new_password = newPass;
    }
    if (sigDataUrl) {
      payload.signature_image = sigDataUrl;
    }

    try {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Saving...';

      let result = null;
      if (window.YTR_API && window.YTR_API.auth && window.YTR_API.auth.updateProfile) {
        result = await window.YTR_API.auth.updateProfile(payload);
      } else {
        const cloudUrl = (window.YTR_API && window.YTR_API.baseUrl) ? window.YTR_API.baseUrl : 'https://ytr-backend.onrender.com/api';
        const res = await fetch(`${cloudUrl}/auth/profile`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        result = await res.json();
      }

      if (result && (result.status === 'success' || result.ok)) {
        showProfileAlert('Profile updated successfully!', 'success');
        
        // Merge updated user data into localStorage
        const updatedUser = (result.data && result.data.user) ? result.data.user : {
          ...user,
          full_name: fullName,
          position: position,
          signature_image: sigDataUrl || user.signature_image
        };
        localStorage.setItem('ytr_user', JSON.stringify(updatedUser));

        // Re-sync topbar UI immediately
        syncTopbarUserProfile(updatedUser);

        setTimeout(() => {
          closeProfileModal();
        }, 1200);
      } else {
        showProfileAlert(result.message || 'Failed to update profile. Please try again.', 'error');
      }
    } catch(err) {
      console.error('Save profile error:', err);
      showProfileAlert('Failed to communicate with cloud server. Please check your network connection.', 'error');
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="ph-bold ph-floppy-disk"></i> <span>Save Changes</span>';
    }
  }

  function showProfileAlert(msg, type) {
    const alertBox = document.getElementById('profileModalAlert');
    if (!alertBox) return;
    alertBox.style.display = 'block';
    alertBox.textContent = msg;
    if (type === 'success') {
      alertBox.style.background = '#f0fdf4';
      alertBox.style.color = '#15803d';
      alertBox.style.border = '1px solid #bbf7d0';
    } else {
      alertBox.style.background = '#fef2f2';
      alertBox.style.color = '#b91c1c';
      alertBox.style.border = '1px solid #fecdd3';
    }
  }

  function ytrGlobalSignOut(e) {
    if (e && e.preventDefault) e.preventDefault();
    try {
      localStorage.removeItem('ytr_user');
      localStorage.removeItem('ytr_role');
      localStorage.removeItem('ytr_chapter_id');
      sessionStorage.clear();
      document.cookie = "ytr_role=; path=/; max-age=0;";
      document.cookie = "ytr_chapter_id=; path=/; max-age=0;";
      document.cookie = "ytr_user_id=; path=/; max-age=0;";
    } catch(err) {}
    window.location.href = 'login.php';
  }

  function syncTopbarUserProfile(u) {
    if (!u) {
      ytrGlobalSignOut();
      return;
    }
    let chName = u.chapter_name || localStorage.getItem('ytr_chapter_name');
    if (!chName && u.chapter_id) {
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
    if (chName) {
      const sbCh = document.getElementById('sidebar-chapter-name');
      if (sbCh) sbCh.textContent = chName.toUpperCase();
      const portalTitle = document.getElementById('chapter-portal-title');
      if (portalTitle) portalTitle.textContent = `${chName} Workspace`;
      localStorage.setItem('ytr_chapter_name', chName);
      document.cookie = "ytr_chapter_name=" + encodeURIComponent(chName) + "; path=/; max-age=86400";
    }
    const initials = u.full_name ? u.full_name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : 'US';
    const avatar = document.querySelector('.profile-avatar');
    if (avatar) {
      if (u.profile_picture) {
        avatar.innerHTML = `<img src="${u.profile_picture}" alt="${u.full_name || 'Profile'}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
      } else {
        avatar.textContent = initials;
      }
    }
    
    const pName = document.querySelector('.profile-info h4');
    if (pName) pName.textContent = u.full_name || 'User';
    
    const pRole = document.querySelector('.profile-info p');
    if (pRole) pRole.textContent = u.position || (u.role === 'chapter_admin' ? (u.chapter_name || 'Chapter Admin') : 'National Administrator');

    const ddName = document.getElementById('topbar-dropdown-user-name');
    if (ddName) ddName.textContent = u.full_name || 'User';

    const ddEmail = document.getElementById('topbar-dropdown-user-email');
    if (ddEmail) ddEmail.textContent = u.email || 'user@ytr.org';

    const topbarBadge = document.querySelector('.topbar-left .role-badge');
    if (topbarBadge) {
      if (u.role === 'chapter_admin') {
        topbarBadge.textContent = (u.chapter_name || 'Chapter') + (u.position ? ' • ' + u.position : '');
        topbarBadge.style.cssText = 'background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-weight: 700;';
      } else {
        topbarBadge.textContent = 'National' + (u.position ? ' • ' + u.position : '');
      }
    }
  }

</script>