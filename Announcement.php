<?php
// Role determination: Check if user is Chapter Admin
$is_chapter_admin = false;
if (isset($_COOKIE['ytr_role']) && $_COOKIE['ytr_role'] === 'chapter_admin') {
    $is_chapter_admin = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>YTR | Announcements & Events</title>
  
  <!-- Phosphor Icons -->
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  
  <!-- Centralized CSS based on Role -->
  <?php if ($is_chapter_admin): ?>
    <link rel="stylesheet" href="assets/Chapter.css">
  <?php else: ?>
    <link rel="stylesheet" href="assets/National.css">
  <?php endif; ?>
  <script src="assets/api-client.js"></script>

  <style>
    /* Modal styles */
    .modal-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.65);
      z-index: 1000;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
    }
    .modal-box {
      background: #ffffff;
      border-radius: 12px;
      width: 100%;
      max-width: 600px;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.25);
      border: 1px solid #e2e8f0;
      padding: 1.75rem;
    }
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #e2e8f0;
      padding-bottom: 1rem;
      margin-bottom: 1.25rem;
    }
    .modal-header h3 {
      font-size: 1.2rem;
      font-weight: 700;
      color: #0f172a;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .modal-close-btn {
      background: none;
      border: none;
      font-size: 1.4rem;
      color: #64748b;
      cursor: pointer;
    }
    .modal-close-btn:hover { color: #0f172a; }
  </style>
</head>
<body>

  <div class="app-wrapper">
    <!-- INJECT SIDEBAR ACCORDING TO ROLE -->
    <?php if ($is_chapter_admin): ?>
      <?php include 'includes/sidebar_chapter.php'; ?>
    <?php else: ?>
      <?php include 'includes/sidebar.php'; ?>
    <?php endif; ?>

    <!-- MAIN CONTENT -->
    <div class="main-wrapper">
      <!-- INJECT TOPBAR -->
      <?php include 'includes/topbar.php'; ?>

      <main class="page-content">
        
        <!-- Header Section -->
        <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
          <div>
            <h1 class="font-roboto">Upcoming Events & Announcements</h1>
            <p style="color: var(--text-muted); margin-top: 0.25rem;">Official broadcasts, spiritual gatherings, and key dates across Youth on the Rock.</p>
          </div>
          <div id="announcement-header-action">
            <?php if (!$is_chapter_admin): ?>
              <button id="btnCreateAnnouncement" class="btn btn-primary font-roboto" onclick="openCreateAnnouncementModal()">
                <i class="ph ph-plus"></i> Create Announcement
              </button>
            <?php else: ?>
              <div id="chapterAnnouncementNotice" style="display: flex; align-items: center; gap: 0.5rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 9999px; padding: 0.45rem 0.9rem; font-size: 0.8rem; color: #166534; font-weight: 700;">
                <i class="ph-fill ph-broadcast" style="font-size: 1.1rem; color: #16a34a;"></i> Chapter Broadcast Feed (Read-Only)
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Filter Tabs -->
        <div class="tab-controls-wrapper">
          <div class="tabs-container font-roboto">
            <button class="tab-btn active font-roboto" onclick="filterAnnouncements('all', this)"><i class="ph ph-megaphone"></i> All Announcements</button>
            <button class="tab-btn font-roboto" onclick="filterAnnouncements('national', this)"><i class="ph ph-flag"></i> National Events</button>
            <button class="tab-btn font-roboto" onclick="filterAnnouncements('chapter', this)"><i class="ph ph-users"></i> Chapter Gatherings</button>
          </div>
        </div>

        <!-- Events Grid -->
        <div id="announcement-grid" class="announcement-grid">
          <!-- Dynamically populated -->
        </div>

      </main>
    </div>
  </div>

  <!-- MODAL: CREATE ANNOUNCEMENT (FOR NATIONAL ADMIN ONLY) -->
  <div id="createAnnouncementModal" class="modal-overlay">
    <div class="modal-box">
      <div class="modal-header">
        <h3 class="font-roboto">
          <i class="ph ph-megaphone-simple" style="color: #2563eb;"></i> Publish New Announcement
        </h3>
        <button type="button" class="modal-close-btn" onclick="closeCreateAnnouncementModal()"><i class="ph ph-x"></i></button>
      </div>
      <form id="createAnnouncementForm" onsubmit="handleCreateAnnouncementSubmit(event)">
        <div style="margin-bottom: 1rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Event / Announcement Title *</label>
          <input type="text" id="ann-title" class="form-input font-roboto" required placeholder="e.g. Annual National Youth Conference 2026" style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Category *</label>
            <select id="ann-category" class="form-select font-roboto" required style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
              <option value="national">National Event</option>
              <option value="chapter">Chapter Gathering</option>
            </select>
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Event Date *</label>
            <input type="date" id="ann-date" class="form-input font-roboto" required style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Venue / Location *</label>
            <input type="text" id="ann-venue" class="form-input font-roboto" required placeholder="e.g. SJB Learning Center, Calamba" style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Host Chapter / Region</label>
            <input type="text" id="ann-chapter" class="form-input font-roboto" placeholder="e.g. Laguna Chapter or National HQ" style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
          </div>
        </div>

        <div style="margin-bottom: 1rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Target Expected Attendees</label>
          <input type="number" id="ann-participants" class="form-input font-roboto" placeholder="e.g. 150" min="1" style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Announcement Summary & Highlights *</label>
          <textarea id="ann-description" class="form-textarea font-roboto" rows="3" required placeholder="Describe the program schedule, registration details, and guest ministers..." style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;"></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
          <button type="button" class="btn btn-outline font-roboto" onclick="closeCreateAnnouncementModal()">Cancel</button>
          <button type="submit" class="btn btn-primary font-roboto" id="btnSubmitAnnouncement">
            <i class="ph ph-broadcast"></i> Publish Announcement
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL: VIEW ANNOUNCEMENT DETAILS (AVAILABLE TO ALL USERS) -->
  <div id="viewAnnouncementModal" class="modal-overlay">
    <div class="modal-box" style="max-width: 620px;">
      <div class="modal-header">
        <h3 class="font-roboto">
          <i class="ph ph-calendar-check" style="color: #2563eb;"></i> Announcement Details
        </h3>
        <button type="button" class="modal-close-btn" onclick="closeViewAnnouncementModal()"><i class="ph ph-x"></i></button>
      </div>
      <div id="viewAnnouncementBody" style="line-height: 1.6; font-size: 0.9rem;">
        <!-- Populated via JS -->
      </div>
      <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
        <button type="button" class="btn btn-primary font-roboto" onclick="closeViewAnnouncementModal()">Close</button>
      </div>
    </div>
  </div>

  <script>
    // Initial Announcement Data
    const defaultAnnouncements = [
      {
        id: 1,
        title: "Tremendous Momentum: Youth Camp 2026",
        category: "national",
        date: "May 21, 2026",
        venue: "SJB Learning Center, Calamba",
        target: 100,
        chapter: "Laguna Chapter",
        image: "https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=500&q=80",
        excerpt: "Join us for the ultimate showcase of the year. The 'Greatest of All Time' of Youth Tech Revolution will feature live workshops, breakout prayer sessions, and spiritual empowerment.",
        details: "Tremendous Momentum brings together student leaders and campus ministry coordinators for an intensive three-day spiritual conference. Includes guest worship leaders, itemized break-out clusters, and youth discipleship mentoring."
      },
      {
        id: 2,
        title: "29th YTR Anniversary Celebration",
        category: "national",
        date: "Sept 26, 2026",
        venue: "SJB Learning Center, Calamba",
        target: 250,
        chapter: "National Secretariat",
        image: "https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=500&q=80",
        excerpt: "Celebrating nearly three decades of passionate youth transformation, leadership empowerment, and nation-building across every Philippine province.",
        details: "Commemorating 29 years of faithful community impact. All chapters nationwide are invited to join the main thanksgiving fellowship and awards ceremony honoring outstanding youth leaders."
      },
      {
        id: 3,
        title: "National Youth Leadership Summit",
        category: "national",
        date: "Dec 19-20, 2026",
        venue: "SMX Convention Center, Pasay City",
        target: 400,
        chapter: "National HQ",
        image: "https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=500&q=80",
        excerpt: "An executive gathering of chapter administrators, youth pastoring councils, and ministry executives to strategize vision 2027 and annual mission milestones.",
        details: "Two-day national leadership conference covering strategic planning, digital event monitoring governance, budget threshold liquidation, and youth mobilization across chapters."
      },
      {
        id: 4,
        title: "Greatest of All Time: Regional Fellowship",
        category: "chapter",
        date: "Sept 28, 2026",
        venue: "SJB Learning Center, Calamba",
        target: 120,
        chapter: "Laguna Chapter",
        image: "https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&w=500&q=80",
        excerpt: "High-energy praise, worship revival, and inter-campus fellowship bringing university students together for inspiration and community outreach.",
        details: "Laguna Chapter host gathering featuring guest worship teams, creative arts presentations, and community outreach testimony nights."
      }
    ];

    let announcements = [];
    let currentFilter = 'all';

    // Role Security Check
    let isChapterAdminUser = false;

    function checkUserRole() {
      try {
        const rawUser = localStorage.getItem('ytr_user');
        const role = localStorage.getItem('ytr_role');
        let userRole = role;
        if (rawUser) {
          const u = JSON.parse(rawUser);
          if (u && u.role) userRole = u.role;
        }

        if (userRole === 'chapter_admin') {
          isChapterAdminUser = true;
          // Ensure cookie is aligned
          document.cookie = "ytr_role=chapter_admin; path=/; max-age=86400";
          
          // Strictly remove or hide Create Announcement button
          const btn = document.getElementById('btnCreateAnnouncement');
          if (btn) btn.style.display = 'none';

          // Show Read-Only notice for Chapter Admin
          const actionArea = document.getElementById('announcement-header-action');
          if (actionArea && !document.getElementById('chapterAnnouncementNotice')) {
            actionArea.innerHTML = `
              <div id="chapterAnnouncementNotice" style="display: flex; align-items: center; gap: 0.5rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 9999px; padding: 0.45rem 0.9rem; font-size: 0.8rem; color: #166534; font-weight: 700;">
                <i class="ph-fill ph-broadcast" style="font-size: 1.1rem; color: #16a34a;"></i> Chapter Broadcast Feed (Read-Only)
              </div>
            `;
          }
        }
      } catch (e) {
        console.error('Role check error:', e);
      }
    }

    function loadAnnouncements() {
      const stored = localStorage.getItem('ytr_announcements');
      if (stored) {
        try {
          announcements = JSON.parse(stored);
        } catch (e) {
          announcements = [...defaultAnnouncements];
        }
      } else {
        announcements = [...defaultAnnouncements];
        localStorage.setItem('ytr_announcements', JSON.stringify(announcements));
      }
      renderAnnouncements();
    }

    function renderAnnouncements() {
      const grid = document.getElementById('announcement-grid');
      const filtered = announcements.filter(a => {
        if (currentFilter === 'all') return true;
        return a.category === currentFilter;
      });

      if (filtered.length === 0) {
        grid.innerHTML = `
          <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: #ffffff; border-radius: 12px; border: 1px dashed #cbd5e1;">
            <i class="ph ph-megaphone" style="font-size: 2.5rem; color: #94a3b8;"></i>
            <h4 style="color: #334155; margin: 0.5rem 0 0.25rem;">No Announcements in this Category</h4>
            <p style="color: #64748b; font-size: 0.85rem;">Check back later for newly published broadcasts.</p>
          </div>
        `;
        return;
      }

      grid.innerHTML = filtered.map(a => `
        <div class="announcement-card">
          <div class="announcement-poster">
            <img src="${escapeHtml(a.image)}" alt="${escapeHtml(a.title)}" onerror="this.src='https://placehold.co/400x500/e2e8f0/64748b?text=YTR+Event'">
          </div>
          <h3 class="font-roboto">${escapeHtml(a.title)}</h3>
          <div class="announcement-meta font-roboto">
            <div class="announcement-meta-item"><i class="ph ph-calendar-blank"></i> ${escapeHtml(a.date)}</div>
            <div class="announcement-meta-item"><i class="ph ph-map-pin"></i> ${escapeHtml(a.venue)}</div>
            <div class="announcement-meta-item"><i class="ph ph-users"></i> ${a.target || 100} Target Participants</div>
            <div class="announcement-meta-item"><i class="ph ph-flag"></i> ${escapeHtml(a.chapter || 'All Chapters')}</div>
          </div>
          <p class="announcement-excerpt font-roboto">${escapeHtml(a.excerpt || a.details || '')}</p>
          <button class="announcement-view-btn font-roboto" onclick="openViewAnnouncementModal(${a.id})">
            <i class="ph ph-eye"></i> View Details
          </button>
        </div>
      `).join('');
    }

    function filterAnnouncements(category, btn) {
      currentFilter = category;
      document.querySelectorAll('.tabs-container .tab-btn').forEach(b => b.classList.remove('active'));
      if (btn) btn.classList.add('active');
      renderAnnouncements();
    }

    function openViewAnnouncementModal(id) {
      const a = announcements.find(item => item.id === id);
      if (!a) return;

      const body = document.getElementById('viewAnnouncementBody');
      body.innerHTML = `
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.25rem; margin-bottom: 1.25rem;">
          <h2 style="font-size: 1.25rem; font-weight: 800; color: #1e3a8a; margin: 0 0 0.5rem;">${escapeHtml(a.title)}</h2>
          <span class="badge" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
            ${a.category === 'national' ? 'National Event' : 'Chapter Gathering'}
          </span>
        </div>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 1.25rem; font-size: 0.85rem;">
          <tr><td style="padding: 6px 0; color: #64748b; width: 140px;"><strong><i class="ph ph-calendar"></i> Event Date:</strong></td><td style="color: #0f172a; font-weight: 600;">${escapeHtml(a.date)}</td></tr>
          <tr><td style="padding: 6px 0; color: #64748b;"><strong><i class="ph ph-map-pin"></i> Venue / Location:</strong></td><td style="color: #0f172a;">${escapeHtml(a.venue)}</td></tr>
          <tr><td style="padding: 6px 0; color: #64748b;"><strong><i class="ph ph-buildings"></i> Host Chapter:</strong></td><td style="color: #0f172a;">${escapeHtml(a.chapter || 'All Chapters')}</td></tr>
          <tr><td style="padding: 6px 0; color: #64748b;"><strong><i class="ph ph-users"></i> Expected Turnout:</strong></td><td style="color: #0f172a; font-weight: 600;">${a.target || 100} Attendees Goal</td></tr>
        </table>
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 1rem;">
          <h5 style="margin: 0 0 0.5rem; font-size: 0.8rem; color: #475569; text-transform: uppercase; letter-spacing: 0.05em;">Announcement Description</h5>
          <p style="margin: 0; color: #334155; line-height: 1.6; font-size: 0.875rem;">${escapeHtml(a.details || a.excerpt || '')}</p>
        </div>
      `;
      document.getElementById('viewAnnouncementModal').style.display = 'flex';
    }

    function closeViewAnnouncementModal() {
      document.getElementById('viewAnnouncementModal').style.display = 'none';
    }

    // National Admin Create Announcement Controls
    function openCreateAnnouncementModal() {
      if (isChapterAdminUser) {
        alert('Permission Denied: Only National Administrators can publish announcements.');
        return;
      }
      document.getElementById('createAnnouncementModal').style.display = 'flex';
    }

    function closeCreateAnnouncementModal() {
      document.getElementById('createAnnouncementModal').style.display = 'none';
    }

    function handleCreateAnnouncementSubmit(e) {
      e.preventDefault();
      if (isChapterAdminUser) {
        alert('Permission Denied: Only National Administrators can publish announcements.');
        return;
      }

      const newAnn = {
        id: Date.now(),
        title: document.getElementById('ann-title').value.trim(),
        category: document.getElementById('ann-category').value,
        date: document.getElementById('ann-date').value,
        venue: document.getElementById('ann-venue').value.trim(),
        chapter: document.getElementById('ann-chapter').value.trim() || 'National HQ',
        target: parseInt(document.getElementById('ann-participants').value) || 100,
        image: 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=500&q=80',
        excerpt: document.getElementById('ann-description').value.trim().substring(0, 140) + '...',
        details: document.getElementById('ann-description').value.trim()
      };

      announcements.unshift(newAnn);
      localStorage.setItem('ytr_announcements', JSON.stringify(announcements));
      alert('Announcement published successfully to all chapters!');
      document.getElementById('createAnnouncementForm').reset();
      closeCreateAnnouncementModal();
      renderAnnouncements();
    }

    function escapeHtml(str) {
      if (!str) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    // Initialize
    window.addEventListener('DOMContentLoaded', () => {
      checkUserRole();
      loadAnnouncements();
    });
  </script>

</body>
</html>