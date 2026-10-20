<?php include 'includes/auth_guard.php'; ?>
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

    .modal-xl {
      background: #ffffff;
      border-radius: 14px;
      width: 100%;
      max-width: 980px;
      max-height: 92vh;
      display: flex;
      flex-direction: column;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      border: 1px solid #e2e8f0;
      overflow: hidden;
    }

    .announcement-edit-btn {
      padding: 0.5rem 0.85rem;
      background: #ffffff;
      color: #1e40af;
      border: 1px solid #cbd5e1;
      border-radius: 0.5rem;
      font-size: 0.75rem;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.35rem;
      transition: background 0.2s, border-color 0.2s;
    }
    .announcement-edit-btn:hover {
      background: #eff6ff;
      border-color: #93c5fd;
    }
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
                <i class="ph-fill ph-broadcast" style="font-size: 1.1rem; color: #16a34a;"></i> Announcements
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
        <input type="file" id="card-ann-poster-file" accept="image/*" style="display: none;" onchange="handleCardPosterFileSelect(event)">
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

        <!-- Event Poster Upload & Preview -->
        <div style="margin-bottom: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem;">
            <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; font-weight: 700; color: #1e293b; margin: 0;">
              <i class="ph-bold ph-image" style="color: #2563eb;"></i> Event Poster (Flyer / Banner)
            </label>
            <span style="font-size: 0.72rem; color: #64748b;">PNG, JPG, or WEBP</span>
          </div>

          <input type="file" id="ann-poster-file" accept="image/*" style="display: none;" onchange="handlePosterFileSelect(event, 'create')">

          <!-- Dropzone -->
          <div id="create-poster-dropzone" onclick="document.getElementById('ann-poster-file').click()" style="border: 2px dashed #cbd5e1; background: #ffffff; border-radius: 8px; padding: 1.15rem 1rem; text-align: center; cursor: pointer; transition: all 0.2s ease;">
            <i class="ph ph-cloud-arrow-up" style="font-size: 2rem; color: #3b82f6; display: block; margin-bottom: 0.25rem;"></i>
            <span style="font-size: 0.825rem; font-weight: 600; color: #1e293b; display: block;">Click to browse or drop event poster here</span>
            <span style="font-size: 0.72rem; color: #64748b;">Attach the official promotional image for this announcement</span>
          </div>

          <!-- Preview Box -->
          <div id="create-poster-preview-box" style="display: none; margin-top: 0.5rem; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0.65rem; align-items: center; gap: 0.85rem;">
            <img id="create-poster-preview-img" src="" alt="Poster Preview" style="width: 65px; height: 65px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0; flex-shrink: 0;">
            <div style="flex: 1; min-width: 0;">
              <div id="create-poster-filename" style="font-size: 0.8rem; font-weight: 700; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">poster.png</div>
              <div style="font-size: 0.7rem; color: #16a34a; font-weight: 600; margin-top: 2px;">
                <i class="ph-fill ph-check-circle"></i> Poster ready for publishing
              </div>
              <div style="display: flex; gap: 0.5rem; margin-top: 0.3rem;">
                <button type="button" onclick="document.getElementById('ann-poster-file').click()" style="background: none; border: none; padding: 0; color: #2563eb; font-size: 0.75rem; font-weight: 600; cursor: pointer;">Change Poster</button>
                <span style="color: #cbd5e1;">&bull;</span>
                <button type="button" onclick="clearCreatePoster()" style="background: none; border: none; padding: 0; color: #dc2626; font-size: 0.75rem; font-weight: 600; cursor: pointer;">Remove</button>
              </div>
            </div>
          </div>
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

  <!-- MODAL: VIEW ANNOUNCEMENT DETAILS (ACCORDING TO FIGMA MOCKUP) -->
  <div id="viewAnnouncementModal" class="modal-overlay">
    <div class="modal-xl">
      <!-- Modal Header -->
      <div class="modal-header-banner">
        <div style="display: flex; align-items: center; gap: 0.85rem;">
          <div style="width: 44px; height: 44px; border-radius: 10px; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #2563eb; font-size: 1.55rem; flex-shrink: 0;">
            <i class="ph-bold ph-calendar-blank"></i>
          </div>
          <div>
            <h3 class="font-roboto" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #1e40af;">
              Event Poster Details
            </h3>
            <p style="margin: 0.2rem 0 0; font-size: 0.825rem; color: #64748b;">view the complete Information and progress of this chapter event</p>
          </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.85rem;">
          <button type="button" class="btn btn-primary font-roboto" id="btnModalEditAnnouncement" onclick="openEditCurrentAnnouncement()" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: none; align-items: center; gap: 0.4rem; border-radius: 6px;">
            <i class="ph ph-note-pencil"></i> Edit Announcement
          </button>
          <button type="button" class="modal-close-btn" onclick="closeViewAnnouncementModal()" aria-label="Close modal" style="background: none; border: none; font-size: 1.35rem; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center;">
            <i class="ph ph-x"></i>
          </button>
        </div>
      </div>

      <!-- Modal Body (3 Columns) -->
      <div class="modal-body-scroll">
        <div class="event-details-grid">
          <!-- Column 1: Event Poster & Brief Card -->
          <div class="event-details-left-card">
            <div class="event-details-poster-wrap" style="position: relative; overflow: hidden; border-radius: 8px;">
              <img id="view-ann-poster-img" src="assets/images/background.jpg" alt="Event Poster" onerror="this.src='assets/images/background.jpg'">
              <?php if (!$is_chapter_admin): ?>
              <input type="file" id="instant-ann-poster-file" accept="image/*" style="display: none;" onchange="handleInstantPosterUpdate(event)">
              <button type="button" id="btnQuickChangePoster" onclick="document.getElementById('instant-ann-poster-file').click()" title="Click to upload and immediately update poster photo" style="position: absolute; bottom: 8px; right: 8px; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(4px); color: #ffffff; border: 1px solid rgba(255,255,255,0.3); border-radius: 6px; padding: 0.35rem 0.65rem; font-size: 0.75rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.35rem; transition: all 0.2s ease;">
                <i class="ph-bold ph-camera"></i> Change Photo
              </button>
              <?php endif; ?>
            </div>
            <div>
              <h4 id="view-ann-left-title" class="font-roboto" style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin: 0 0 0.5rem; text-transform: uppercase; letter-spacing: 0.02em;">
                TREMENDOUS MOMENTUM
              </h4>
              <div style="font-size: 0.78rem; color: #475569; display: flex; flex-direction: column; gap: 0.4rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                  <i class="ph ph-calendar" style="color: #2563eb; font-size: 1rem;"></i>
                  <span id="view-ann-left-date">May 21, 2026</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                  <i class="ph ph-map-pin" style="color: #2563eb; font-size: 1rem;"></i>
                  <span id="view-ann-left-venue">SJB Learning Center Canlalay</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                  <i class="ph ph-users" style="color: #2563eb; font-size: 1rem;"></i>
                  <span id="view-ann-left-participants">100 Target Participants</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                  <i class="ph ph-users-three" style="color: #2563eb; font-size: 1rem;"></i>
                  <span id="view-ann-left-chapter">Laguna Chapter</span>
                </div>
              </div>
              <p id="view-ann-left-desc" style="font-size: 0.75rem; color: #64748b; line-height: 1.45; margin: 0.65rem 0 0;">
                Join us for the ultimate showcase of the year. The "Greatest of All Time" of Youth Tech Revolution will feature live algorithm optimization challenges, full-stack architecture deep dives, and an awards ceremony for the top chapter contributors.
              </p>
            </div>
            <button type="button" class="btn-view-poster-light" onclick="openZoomImage(document.getElementById('view-ann-poster-img').src)">
              <i class="ph ph-eye"></i> View Poster
            </button>
          </div>

          <!-- Column 2: Event Details Center Card -->
          <div class="event-details-center-card">
            <h4 class="font-roboto" style="margin: 0 0 0.85rem; font-size: 1.15rem; font-weight: 800; color: #1e40af; border-bottom: 1.5px solid #e2e8f0; padding-bottom: 0.6rem;">
              Event Details
            </h4>

            <!-- About the Event -->
            <div style="margin-bottom: 1rem;">
              <div style="display: flex; align-items: center; gap: 0.45rem; font-size: 0.9rem; font-weight: 700; color: #1e40af; margin-bottom: 0.4rem;">
                <i class="ph ph-article" style="color: #2563eb; font-size: 1.15rem;"></i> About the Event
              </div>
              <p id="view-ann-about-text" style="font-size: 0.8rem; color: #475569; line-height: 1.55; margin: 0;">
                Join us for the ultimate showcase of the year. The "Greatest of All Time" of Youth Tech Revolution will feature live algorithm optimization challenges, full-stack architecture deep dives, and an awards ceremony for the top chapter contributors.
              </p>
            </div>

            <!-- Metric Stat Cards -->
            <div class="ev-stat-cards-grid">
              <div class="ev-stat-card">
                <div class="ev-stat-icon">
                  <i class="ph-fill ph-users"></i>
                </div>
                <div>
                  <div style="font-size: 0.72rem; color: #64748b; font-weight: 600;">Total Registered</div>
                  <div id="view-ann-stat-registered" style="font-size: 1.4rem; font-weight: 800; color: #1e40af; line-height: 1.1;">60</div>
                  <div style="font-size: 0.7rem; color: #94a3b8;">Participants</div>
                </div>
              </div>
              <div class="ev-stat-card">
                <div class="ev-stat-icon">
                  <i class="ph-fill ph-calendar"></i>
                </div>
                <div>
                  <div style="font-size: 0.72rem; color: #64748b; font-weight: 600;">Event Date</div>
                  <div id="view-ann-stat-date" style="font-size: 1.05rem; font-weight: 800; color: #1e40af; line-height: 1.2;">Sept 28, 2026</div>
                  <div id="view-ann-stat-day" style="font-size: 0.7rem; color: #64748b;">Monday</div>
                </div>
              </div>
            </div>

            <div style="border-top: 1px solid #f1f5f9; margin: 1rem 0;"></div>

            <!-- Chapter Section -->
            <div style="margin-bottom: 1rem;">
              <div style="display: flex; align-items: center; gap: 0.45rem; font-size: 0.9rem; font-weight: 700; color: #1e40af; margin-bottom: 0.5rem;">
                <i class="ph ph-users-three" style="color: #2563eb; font-size: 1.15rem;"></i> Chapter
              </div>
              <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 42px; height: 42px; border-radius: 50%; border: 1.5px solid #cbd5e1; display: flex; align-items: center; justify-content: center; background: #f8fafc; overflow: hidden; flex-shrink: 0;">
                  <img src="assets/images/logo.png" alt="Logo" style="width: 28px; height: 28px; object-fit: contain;" onerror="this.onerror=null; this.src='assets/images/youth.jpg';">
                </div>
                <div>
                  <div id="view-ann-chapter-name" style="font-size: 0.95rem; font-weight: 800; color: #1e3a8a;">YTR NATIONAL</div>
                  <div style="font-size: 0.72rem; color: #64748b;">Youth on the Rock</div>
                </div>
              </div>
            </div>

            <div style="border-top: 1px solid #f1f5f9; margin: 1rem 0;"></div>

            <!-- Location / Platform Section -->
            <div style="margin-bottom: 1rem;">
              <div style="display: flex; align-items: center; gap: 0.45rem; font-size: 0.9rem; font-weight: 700; color: #1e40af; margin-bottom: 0.4rem;">
                <i class="ph ph-map-pin" style="color: #2563eb; font-size: 1.15rem;"></i> Location / Platform
              </div>
              <div id="view-ann-loc-venue" style="font-weight: 700; color: #0f172a; font-size: 0.875rem; margin-bottom: 0.15rem;">SJB Learning Center Canlalay</div>
              <div id="view-ann-loc-city" style="font-size: 0.75rem; color: #64748b;">Biñan, Laguna</div>
            </div>

            <div style="border-top: 1px solid #f1f5f9; margin: 1rem 0;"></div>

            <!-- Contact Section -->
            <div>
              <div style="display: flex; align-items: center; gap: 0.45rem; font-size: 0.9rem; font-weight: 700; color: #1e40af; margin-bottom: 0.4rem;">
                <i class="ph ph-phone" style="color: #2563eb; font-size: 1.15rem;"></i> Contact
              </div>
              <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 0.75rem 0.85rem;">
                <div id="view-ann-contact-msg" style="font-size: 0.75rem; color: #1e40af; font-weight: 500; margin-bottom: 0.35rem;">
                  For inquiries, you may Contact the Chapter Officer or the <span id="view-ann-contact-team" style="font-weight: 700;">YTR - Laguna Team</span>
                </div>
                <div style="font-size: 0.75rem; color: #1e3a8a; font-weight: 600;">
                  <span id="view-ann-contact-person">Secretary: Rayvhen P. Muñoz</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Column 3: Logistics & Team Right Card -->
          <div class="event-details-right-card">
            <div>
              <h4 class="font-roboto" style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #1e40af;">
                Logistics & Team
              </h4>
              <p style="margin: 0.2rem 0 0; font-size: 0.75rem; color: #64748b; line-height: 1.4;">
                See key details about the event team, venue, and schedule.
              </p>
            </div>

            <!-- Card 1: Core Team Leads -->
            <div class="ev-info-card">
              <h5 style="margin: 0 0 0.6rem; color: #1e3a8a; font-size: 0.875rem; font-weight: 800;">Core Team Leads</h5>
              <div id="view-ann-team-leads" style="font-size: 0.75rem; color: #334155; line-height: 1.7;">
                <div>Project Manager: <span id="view-ann-team-pm" style="font-weight: 600;">Alex R.</span></div>
                <div>Creative Lead: <span id="view-ann-team-creative" style="font-weight: 600;">Maria P.</span></div>
                <div>Technical Director: <span id="view-ann-team-tech" style="font-weight: 600;">John K.</span></div>
              </div>
            </div>

            <!-- Card 2: Venue Specifics -->
            <div class="ev-info-card">
              <h5 style="margin: 0 0 0.6rem; color: #1e3a8a; font-size: 0.875rem; font-weight: 800;">Venue Specifics</h5>
              <div id="view-ann-venue-specifics" style="font-size: 0.75rem; color: #334155; line-height: 1.7;">
                <div>Room: <span id="view-ann-spec-room" style="font-weight: 600;">Auditorium A (2nd Floor)</span></div>
                <div>Parking: <span id="view-ann-spec-parking" style="font-weight: 600;">Lot B, Level-1</span></div>
                <div>Wi-Fi: <span id="view-ann-spec-wifi" style="font-weight: 600;">YTR-Guest / goat2026</span></div>
              </div>
            </div>

            <!-- Card 3: Registration Links -->
            <div class="ev-info-card">
              <h5 style="margin: 0 0 0.6rem; color: #1e3a8a; font-size: 0.875rem; font-weight: 800;">Registration Links</h5>
              <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem;">GForms:</div>
              <a id="view-ann-reg-link" href="https://forms.gle/YcZudZkgefs1dEEn7" target="_blank" style="font-size: 0.72rem; color: #2563eb; word-break: break-all; text-decoration: underline; font-weight: 600;">
                https://forms.gle/YcZudZkgefs1dEEn7
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL: EDIT ANNOUNCEMENT (NATIONAL ADMIN ONLY) -->
  <div id="editAnnouncementModal" class="modal-overlay">
    <div class="modal-box" style="max-width: 680px;">
      <div class="modal-header">
        <h3 class="font-roboto">
          <i class="ph ph-note-pencil" style="color: #2563eb;"></i> Edit Announcement & Event Details
        </h3>
        <button type="button" class="modal-close-btn" onclick="closeEditAnnouncementModal()"><i class="ph ph-x"></i></button>
      </div>
      <form id="editAnnouncementForm" onsubmit="handleEditAnnouncementSubmit(event)">
        <input type="hidden" id="edit-ann-id">

        <div style="margin-bottom: 1rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Event / Announcement Title *</label>
          <input type="text" id="edit-ann-title" class="form-input font-roboto" required style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Category *</label>
            <select id="edit-ann-category" class="form-select font-roboto" required style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
              <option value="national">National Event</option>
              <option value="chapter">Chapter Gathering</option>
            </select>
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Event Date *</label>
            <input type="text" id="edit-ann-date" class="form-input font-roboto" required placeholder="e.g. Sept 28, 2026 or 2026-09-28" style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Venue / Platform *</label>
            <input type="text" id="edit-ann-venue" class="form-input font-roboto" required placeholder="e.g. SJB Learning Center Canlalay" style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">City / Location Specifics</label>
            <input type="text" id="edit-ann-city" class="form-input font-roboto" placeholder="e.g. Biñan, Laguna" style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
          </div>
        </div>

        <div style="margin-bottom: 1rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Host Chapter / Region</label>
          <input type="text" id="edit-ann-chapter" class="form-input font-roboto" placeholder="e.g. Laguna Chapter or YTR NATIONAL" style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
        </div>

        <!-- Event Poster Manager (Upload & Live Change) -->
        <div style="margin-bottom: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem;">
            <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; font-weight: 700; color: #1e293b; margin: 0;">
              <i class="ph-bold ph-image" style="color: #2563eb;"></i> Event Poster
            </label>
            <span style="font-size: 0.72rem; color: #64748b;">Upload new image file or update path/URL</span>
          </div>

          <input type="file" id="edit-ann-poster-file" accept="image/*" style="display: none;" onchange="handlePosterFileSelect(event, 'edit')">

          <div style="display: flex; align-items: center; gap: 0.85rem; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0.65rem;">
            <img id="edit-poster-preview-img" src="assets/images/background.jpg" alt="Poster Preview" style="width: 75px; height: 75px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0; flex-shrink: 0;" onerror="this.src='assets/images/background.jpg'">
            <div style="flex: 1; min-width: 0;">
              <div style="display: flex; gap: 0.5rem; margin-bottom: 0.4rem; flex-wrap: wrap;">
                <button type="button" class="btn btn-sm btn-primary font-roboto" onclick="document.getElementById('edit-ann-poster-file').click()" style="padding: 0.35rem 0.75rem; font-size: 0.78rem;">
                  <i class="ph ph-upload-simple"></i> Upload New Poster
                </button>
                <button type="button" class="btn btn-sm btn-outline font-roboto" onclick="resetEditPosterToDefault()" style="padding: 0.35rem 0.75rem; font-size: 0.78rem;">
                  Reset Default
                </button>
              </div>
              <div>
                <label style="display: block; font-size: 0.7rem; color: #64748b; margin-bottom: 2px;">Image Path or URL:</label>
                <input type="text" id="edit-ann-image" class="form-input font-roboto" oninput="onEditPosterUrlChange(this.value)" placeholder="e.g. assets/images/tremendous_momentum.png" style="width: 100%; box-sizing: border-box; padding: 0.35rem 0.55rem; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.78rem;">
              </div>
            </div>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Target Expected Attendees</label>
            <input type="number" id="edit-ann-participants" class="form-input font-roboto" min="1" placeholder="e.g. 100" style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Total Registered Participants</label>
            <input type="number" id="edit-ann-registered" class="form-input font-roboto" min="0" placeholder="e.g. 60" style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
          </div>
        </div>

        <div style="margin-bottom: 1rem;">
          <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Announcement Summary & Highlights *</label>
          <textarea id="edit-ann-description" class="form-textarea font-roboto" rows="3" required style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;"></textarea>
        </div>

        <!-- Section: Logistics & Team Specifics -->
        <div style="border-top: 1px solid #e2e8f0; padding-top: 0.85rem; margin-bottom: 1rem;">
          <div style="font-size: 0.85rem; font-weight: 700; color: #1e40af; margin-bottom: 0.6rem; display: flex; align-items: center; gap: 0.4rem;">
            <i class="ph ph-gear"></i> Logistics, Team & Registration Specifics
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.6rem; margin-bottom: 0.75rem;">
            <div>
              <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #475569; margin-bottom: 0.2rem;">Project Manager</label>
              <input type="text" id="edit-ann-pm" class="form-input font-roboto" placeholder="e.g. Alex R." style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.825rem;">
            </div>
            <div>
              <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #475569; margin-bottom: 0.2rem;">Creative Lead</label>
              <input type="text" id="edit-ann-creative" class="form-input font-roboto" placeholder="e.g. Maria P." style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.825rem;">
            </div>
            <div>
              <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #475569; margin-bottom: 0.2rem;">Technical Director</label>
              <input type="text" id="edit-ann-tech" class="form-input font-roboto" placeholder="e.g. John K." style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.825rem;">
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.6rem; margin-bottom: 0.75rem;">
            <div>
              <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #475569; margin-bottom: 0.2rem;">Room Specifics</label>
              <input type="text" id="edit-ann-room" class="form-input font-roboto" placeholder="e.g. Auditorium A (2nd Floor)" style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.825rem;">
            </div>
            <div>
              <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #475569; margin-bottom: 0.2rem;">Parking Specifics</label>
              <input type="text" id="edit-ann-parking" class="form-input font-roboto" placeholder="e.g. Lot B, Level-1" style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.825rem;">
            </div>
            <div>
              <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #475569; margin-bottom: 0.2rem;">Wi-Fi Details</label>
              <input type="text" id="edit-ann-wifi" class="form-input font-roboto" placeholder="e.g. YTR-Guest / goat2026" style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.825rem;">
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem; margin-bottom: 0.75rem;">
            <div>
              <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #475569; margin-bottom: 0.2rem;">Registration Form Link (GForms / Site)</label>
              <input type="url" id="edit-ann-reglink" class="form-input font-roboto" placeholder="https://forms.gle/..." style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.825rem;">
            </div>
            <div>
              <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #475569; margin-bottom: 0.2rem;">Contact Officer / Secretary</label>
              <input type="text" id="edit-ann-contact-person" class="form-input font-roboto" placeholder="e.g. Secretary: Rayvhen P. Muñoz" style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.825rem;">
            </div>
          </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.25rem;">
          <button type="button" class="btn btn-outline font-roboto" onclick="deleteCurrentAnnouncement()" style="color: #dc2626; border-color: #fecaca; background: #fff1f2;">
            <i class="ph ph-trash"></i> Delete
          </button>
          <div style="display: flex; gap: 0.75rem;">
            <button type="button" class="btn btn-outline font-roboto" onclick="closeEditAnnouncementModal()">Cancel</button>
            <button type="submit" class="btn btn-primary font-roboto" id="btnUpdateAnnouncement">
              <i class="ph ph-check-circle"></i> Save Changes
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- ZOOM IMAGE MODAL -->
  <div id="zoom-modal" class="modal-overlay" style="display: none; z-index: 1100;" onclick="closeZoomModal()">
    <div style="position: relative; max-width: 90vw; max-height: 90vh;">
      <img id="zoom-img" src="" alt="Full Resolution Poster" style="max-width: 100%; max-height: 90vh; border-radius: 8px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
      <button type="button" style="position: absolute; top: -15px; right: -15px; background: #ef4444; color: #fff; border: none; border-radius: 50%; width: 36px; height: 36px; font-size: 1.2rem; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px rgba(0,0,0,0.2);">
        <i class="ph ph-x"></i>
      </button>
    </div>
  </div>

  <script>
    // Initial Announcement Data (matching mockup specifications)
    let announcements = [];
    let currentFilter = 'all';
    let isChapterAdminUser = false;
    let currentViewingAnnouncementId = null;

    // Role Security Check
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
          document.cookie = "ytr_role=chapter_admin; path=/; max-age=86400";
          
          const btn = document.getElementById('btnCreateAnnouncement');
          if (btn) btn.style.display = 'none';

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

    async function loadAnnouncements() {
      // 1. Initial render from localStorage cache (ignoring legacy demo data)
      const stored = localStorage.getItem('ytr_announcements');
      if (stored) {
        try {
          const parsed = JSON.parse(stored);
          if (Array.isArray(parsed) && parsed.some(a => a.title && a.title.includes('Tremendous Momentum'))) {
            localStorage.removeItem('ytr_announcements');
            announcements = [];
          } else if (Array.isArray(parsed)) {
            announcements = parsed;
            renderAnnouncements();
          }
        } catch (e) {
          localStorage.removeItem('ytr_announcements');
        }
      } else {
        renderAnnouncements();
      }

      // 2. Fetch live announcements from Aiven Cloud MySQL via Backend API
      try {
        if (window.YTR_API && YTR_API.announcements) {
          const res = await YTR_API.announcements.getAll();
          if (res.ok && res.data && Array.isArray(res.data.announcements)) {
            announcements = res.data.announcements;
            localStorage.setItem('ytr_announcements', JSON.stringify(announcements));
            renderAnnouncements();
          }
        }
      } catch (err) {
        console.warn('Could not load announcements from cloud API:', err);
      }
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

      grid.innerHTML = filtered.map(a => {
        const canEdit = !isChapterAdminUser;
        const editBtnHtml = canEdit ? `
          <button type="button" class="announcement-edit-btn font-roboto" onclick="event.stopPropagation(); openEditAnnouncementModal(${a.id})" title="Edit Announcement">
            <i class="ph ph-note-pencil"></i> Edit
          </button>
        ` : '';

        return `
          <div class="announcement-card">
            <div class="announcement-poster" style="position: relative;">
              <img src="${escapeHtml(a.image)}" alt="${escapeHtml(a.title)}" onerror="this.src='assets/images/background.jpg'">
              ${canEdit ? `
                <button type="button" class="btn-card-quick-photo" onclick="event.stopPropagation(); triggerCardPhotoChange(${a.id})" title="Click to upload and immediately update poster photo" style="position: absolute; top: 10px; right: 10px; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px); color: #ffffff; border: 1px solid rgba(255,255,255,0.3); border-radius: 6px; padding: 0.3rem 0.55rem; font-size: 0.72rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.3rem; transition: all 0.2s; box-shadow: 0 2px 6px rgba(0,0,0,0.25);">
                  <i class="ph-bold ph-camera"></i> Change Photo
                </button>
              ` : ''}
            </div>
            <h3 class="font-roboto">${escapeHtml(a.title)}</h3>
            <div class="announcement-meta font-roboto">
              <div class="announcement-meta-item"><i class="ph ph-calendar-blank"></i> ${escapeHtml(a.date)}</div>
              <div class="announcement-meta-item"><i class="ph ph-map-pin"></i> ${escapeHtml(a.venue)}</div>
              <div class="announcement-meta-item"><i class="ph ph-users"></i> ${a.target || 100} Target Participants</div>
              <div class="announcement-meta-item"><i class="ph ph-flag"></i> ${escapeHtml(a.chapter || 'All Chapters')}</div>
            </div>
            <p class="announcement-excerpt font-roboto">${escapeHtml(a.excerpt || a.details || '')}</p>
            <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
              <button type="button" class="announcement-view-btn font-roboto" style="margin-top: 0; flex: 1;" onclick="openViewAnnouncementModal(${a.id})">
                <i class="ph ph-eye"></i> View Details
              </button>
              ${editBtnHtml}
            </div>
          </div>
        `;
      }).join('');
    }

    function filterAnnouncements(category, btn) {
      currentFilter = category;
      document.querySelectorAll('.tabs-container .tab-btn').forEach(b => b.classList.remove('active'));
      if (btn) btn.classList.add('active');
      renderAnnouncements();
    }

    // ==========================================
    // VIEW ANNOUNCEMENT MODAL (FIGMA MOCKUP ACCORDANCE)
    // ==========================================
    function openViewAnnouncementModal(id) {
      const a = announcements.find(item => item.id === id);
      if (!a) return;

      currentViewingAnnouncementId = id;

      // Format Date & Day of Week
      let fullDateStr = a.date || 'Date TBD';
      let dayStr = '';
      if (a.date) {
        try {
          const dateForParse = a.date.includes('-') ? (a.date + 'T00:00:00') : (a.date.includes('202') ? a.date : a.date + ' 2026');
          const d = new Date(dateForParse);
          if (!isNaN(d.getTime())) {
            fullDateStr = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            dayStr = d.toLocaleDateString('en-US', { weekday: 'long' });
          }
        } catch(e) {}
      }

      // Column 1: Poster & Left Card
      const posterSrc = a.image || 'assets/images/background.jpg';
      const posterImgEl = document.getElementById('view-ann-poster-img');
      if (posterImgEl) posterImgEl.src = posterSrc;

      const titleEl = document.getElementById('view-ann-left-title');
      if (titleEl) titleEl.textContent = a.title || 'Event Title';

      const dateEl = document.getElementById('view-ann-left-date');
      if (dateEl) dateEl.textContent = a.date + (a.time ? ` • ${a.time}` : '');

      const venueEl = document.getElementById('view-ann-left-venue');
      if (venueEl) venueEl.textContent = a.venue || 'Venue TBD';

      const partEl = document.getElementById('view-ann-left-participants');
      if (partEl) {
        const pText = a.target ? String(a.target) : '100';
        partEl.textContent = pText.toLowerCase().includes('participant') ? pText : `${pText} Target Participants`;
      }

      const chapEl = document.getElementById('view-ann-left-chapter');
      if (chapEl) chapEl.textContent = a.chapter || 'Laguna Chapter';

      const leftDescEl = document.getElementById('view-ann-left-desc');
      if (leftDescEl) leftDescEl.textContent = a.excerpt || a.details || '';

      // Column 2: Center Card Event Details
      const aboutEl = document.getElementById('view-ann-about-text');
      if (aboutEl) aboutEl.textContent = a.details || a.excerpt || '';

      const regEl = document.getElementById('view-ann-stat-registered');
      if (regEl) regEl.textContent = a.registered !== undefined ? a.registered : (a.target || '60');

      const statDateEl = document.getElementById('view-ann-stat-date');
      if (statDateEl) statDateEl.textContent = fullDateStr || 'Sept 28, 2026';

      const statDayEl = document.getElementById('view-ann-stat-day');
      if (statDayEl) statDayEl.textContent = dayStr || 'Monday';

      const chapNameEl = document.getElementById('view-ann-chapter-name');
      if (chapNameEl) chapNameEl.textContent = a.chapter ? a.chapter.toUpperCase() : 'YTR NATIONAL';

      const locVenueEl = document.getElementById('view-ann-loc-venue');
      if (locVenueEl) locVenueEl.textContent = a.venue || 'SJB Learning Center Canlalay';

      const locCityEl = document.getElementById('view-ann-loc-city');
      if (locCityEl) {
        locCityEl.textContent = a.city || (a.venue && a.venue.includes(',') ? a.venue.split(',').slice(1).join(',').trim() : 'Biñan, Laguna');
      }

      const contactTeamEl = document.getElementById('view-ann-contact-team');
      if (contactTeamEl) contactTeamEl.textContent = a.contact_team || (a.chapter ? `YTR - ${a.chapter} Team` : 'YTR - Laguna Team');

      const contactPersonEl = document.getElementById('view-ann-contact-person');
      if (contactPersonEl) contactPersonEl.textContent = a.contact_person || 'Secretary: Rayvhen P. Muñoz';

      // Column 3: Logistics & Team
      const pmEl = document.getElementById('view-ann-team-pm');
      if (pmEl) pmEl.textContent = a.pm || 'Alex R.';

      const creativeEl = document.getElementById('view-ann-team-creative');
      if (creativeEl) creativeEl.textContent = a.creative_lead || 'Maria P.';

      const techEl = document.getElementById('view-ann-team-tech');
      if (techEl) techEl.textContent = a.tech_director || 'John K.';

      const roomEl = document.getElementById('view-ann-spec-room');
      if (roomEl) roomEl.textContent = a.room || 'Auditorium A (2nd Floor)';

      const parkingEl = document.getElementById('view-ann-spec-parking');
      if (parkingEl) parkingEl.textContent = a.parking || 'Lot B, Level-1';

      const wifiEl = document.getElementById('view-ann-spec-wifi');
      if (wifiEl) wifiEl.textContent = a.wifi || 'YTR-Guest / goat2026';

      const regLinkEl = document.getElementById('view-ann-reg-link');
      if (regLinkEl) {
        const link = a.reg_link || 'https://forms.gle/YcZudZkgefs1dEEn7';
        regLinkEl.href = link;
        regLinkEl.textContent = link;
      }

      // Show/Hide Edit Announcement Button based on National Admin status
      const editBtn = document.getElementById('btnModalEditAnnouncement');
      if (editBtn) {
        editBtn.style.display = isChapterAdminUser ? 'none' : 'inline-flex';
      }

      document.getElementById('viewAnnouncementModal').style.display = 'flex';
    }

    function closeViewAnnouncementModal() {
      document.getElementById('viewAnnouncementModal').style.display = 'none';
      currentViewingAnnouncementId = null;
    }

    // ==========================================
    // EDIT ANNOUNCEMENT MODAL & CONTROLS (NATIONAL ADMIN)
    // ==========================================
    function openEditCurrentAnnouncement() {
      if (currentViewingAnnouncementId) {
        const id = currentViewingAnnouncementId;
        closeViewAnnouncementModal();
        openEditAnnouncementModal(id);
      }
    }


    // ==========================================
    // POSTER UPLOAD & EDIT CONTROLLER
    // ==========================================
    let targetCardPhotoAnnouncementId = null;

    function triggerCardPhotoChange(id) {
      if (isChapterAdminUser) return;
      targetCardPhotoAnnouncementId = id;
      const input = document.getElementById('card-ann-poster-file');
      if (input) {
        input.value = '';
        input.click();
      }
    }

    function handleCardPosterFileSelect(e) {
      const file = e.target.files && e.target.files[0];
      if (!file || !targetCardPhotoAnnouncementId) return;
      if (!file.type.startsWith('image/')) {
        alert('Please choose an image file (PNG, JPG, WEBP).');
        return;
      }
      const targetId = targetCardPhotoAnnouncementId;
      const reader = new FileReader();
      reader.onload = async function(evt) {
        const dataUrl = evt.target.result;
        const idx = announcements.findIndex(item => item.id === targetId);
        if (idx !== -1) {
          announcements[idx].image = dataUrl;
          localStorage.setItem('ytr_announcements', JSON.stringify(announcements));
          renderAnnouncements();
        }
        try {
          if (window.YTR_API && YTR_API.announcements) {
            await YTR_API.announcements.updatePhoto(targetId, dataUrl);
          }
        } catch(err) {
          console.error('Error syncing poster to cloud:', err);
        }
        alert('Announcement poster photo updated successfully across all chapters and devices!');
      };
      reader.readAsDataURL(file);
    }

    function handleInstantPosterUpdate(e) {
      if (isChapterAdminUser) return;
      const file = e.target.files && e.target.files[0];
      if (!file) return;
      if (!file.type.startsWith('image/')) {
        alert('Please choose an image file (PNG, JPG, WEBP).');
        return;
      }
      const targetId = currentViewingAnnouncementId;
      if (!targetId) return;
      const reader = new FileReader();
      reader.onload = async function(evt) {
        const dataUrl = evt.target.result;
        const idx = announcements.findIndex(item => item.id === targetId);
        if (idx !== -1) {
          announcements[idx].image = dataUrl;
          localStorage.setItem('ytr_announcements', JSON.stringify(announcements));
          const viewPoster = document.getElementById('view-ann-poster-img');
          if (viewPoster) viewPoster.src = dataUrl;
          renderAnnouncements();
        }
        try {
          if (window.YTR_API && YTR_API.announcements) {
            await YTR_API.announcements.updatePhoto(targetId, dataUrl);
          }
        } catch(err) {
          console.error('Error syncing poster to cloud:', err);
        }
        alert('Poster photo updated successfully across all chapters and devices!');
      };
      reader.readAsDataURL(file);
    }

    let selectedCreatePosterData = null;
    let selectedEditPosterData = null;

    function handlePosterFileSelect(event, mode) {
      const file = event.target.files && event.target.files[0];
      if (!file) return;
      processPosterFile(file, mode);
    }

    function processPosterFile(file, mode) {
      if (!file.type.startsWith('image/')) {
        alert('Please choose an image file (PNG, JPG, JPEG, WEBP).');
        return;
      }
      if (file.size > 5 * 1024 * 1024) {
        alert('File size exceeds 5MB limit. Please choose a smaller image.');
        return;
      }

      const reader = new FileReader();
      reader.onload = function(e) {
        const dataUrl = e.target.result;
        if (mode === 'create') {
          selectedCreatePosterData = dataUrl;
          const previewImg = document.getElementById('create-poster-preview-img');
          const previewBox = document.getElementById('create-poster-preview-box');
          const dropzone = document.getElementById('create-poster-dropzone');
          const filenameEl = document.getElementById('create-poster-filename');

          if (previewImg) previewImg.src = dataUrl;
          if (filenameEl) filenameEl.textContent = file.name;
          if (dropzone) dropzone.style.display = 'none';
          if (previewBox) previewBox.style.display = 'flex';
        } else if (mode === 'edit') {
          selectedEditPosterData = dataUrl;
          const previewImg = document.getElementById('edit-poster-preview-img');
          const inputUrl = document.getElementById('edit-ann-image');
          if (previewImg) previewImg.src = dataUrl;
          if (inputUrl) inputUrl.value = dataUrl;
        }
      };
      reader.readAsDataURL(file);
    }

    function clearCreatePoster() {
      selectedCreatePosterData = null;
      const fileInput = document.getElementById('ann-poster-file');
      if (fileInput) fileInput.value = '';
      const previewBox = document.getElementById('create-poster-preview-box');
      const dropzone = document.getElementById('create-poster-dropzone');
      if (previewBox) previewBox.style.display = 'none';
      if (dropzone) dropzone.style.display = 'block';
    }

    function resetEditPosterToDefault() {
      const defaultPoster = 'assets/images/background.jpg';
      selectedEditPosterData = defaultPoster;
      const previewImg = document.getElementById('edit-poster-preview-img');
      const inputUrl = document.getElementById('edit-ann-image');
      if (previewImg) previewImg.src = defaultPoster;
      if (inputUrl) inputUrl.value = defaultPoster;
    }

    function onEditPosterUrlChange(val) {
      const trimmed = (val || '').trim();
      selectedEditPosterData = trimmed || null;
      const previewImg = document.getElementById('edit-poster-preview-img');
      if (previewImg) {
        previewImg.src = trimmed || 'assets/images/background.jpg';
      }
    }

    // Setup Drag and Drop on Create Dropzone
    document.addEventListener('DOMContentLoaded', () => {
      const dropzone = document.getElementById('create-poster-dropzone');
      if (dropzone) {
        dropzone.addEventListener('dragover', (e) => {
          e.preventDefault();
          dropzone.style.borderColor = '#2563eb';
          dropzone.style.background = '#eff6ff';
        });
        dropzone.addEventListener('dragleave', (e) => {
          e.preventDefault();
          dropzone.style.borderColor = '#cbd5e1';
          dropzone.style.background = '#ffffff';
        });
        dropzone.addEventListener('drop', (e) => {
          e.preventDefault();
          dropzone.style.borderColor = '#cbd5e1';
          dropzone.style.background = '#ffffff';
          if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            processPosterFile(e.dataTransfer.files[0], 'create');
          }
        });
      }
    });

    function openEditAnnouncementModal(id) {
      if (isChapterAdminUser) {
        alert('Permission Denied: Only National Administrators can edit announcements.');
        return;
      }
      const a = announcements.find(item => item.id === id);
      if (!a) return;

      document.getElementById('edit-ann-id').value = a.id;
      document.getElementById('edit-ann-title').value = a.title || '';
      document.getElementById('edit-ann-category').value = a.category || 'national';
      document.getElementById('edit-ann-date').value = a.date || '';
      document.getElementById('edit-ann-venue').value = a.venue || '';
      document.getElementById('edit-ann-city').value = a.city || 'Biñan, Laguna';
      document.getElementById('edit-ann-chapter').value = a.chapter || '';
      document.getElementById('edit-ann-participants').value = a.target || 100;
      document.getElementById('edit-ann-registered').value = a.registered !== undefined ? a.registered : 60;
      document.getElementById('edit-ann-image').value = a.image || '';
      selectedEditPosterData = a.image || 'assets/images/background.jpg';
      const editPreviewImg = document.getElementById('edit-poster-preview-img');
      if (editPreviewImg) editPreviewImg.src = selectedEditPosterData;
      document.getElementById('edit-ann-description').value = a.details || a.excerpt || '';
      document.getElementById('edit-ann-pm').value = a.pm || 'Alex R.';
      document.getElementById('edit-ann-creative').value = a.creative_lead || 'Maria P.';
      document.getElementById('edit-ann-tech').value = a.tech_director || 'John K.';
      document.getElementById('edit-ann-room').value = a.room || 'Auditorium A (2nd Floor)';
      document.getElementById('edit-ann-parking').value = a.parking || 'Lot B, Level-1';
      document.getElementById('edit-ann-wifi').value = a.wifi || 'YTR-Guest / goat2026';
      document.getElementById('edit-ann-reglink').value = a.reg_link || 'https://forms.gle/YcZudZkgefs1dEEn7';
      document.getElementById('edit-ann-contact-person').value = a.contact_person || 'Secretary: Rayvhen P. Muñoz';

      document.getElementById('editAnnouncementModal').style.display = 'flex';
    }

    function closeEditAnnouncementModal() {
      document.getElementById('editAnnouncementModal').style.display = 'none';
    }

    function handleEditAnnouncementSubmit(e) {
      e.preventDefault();
      if (isChapterAdminUser) {
        alert('Permission Denied: Only National Administrators can edit announcements.');
        return;
      }

      const id = Number(document.getElementById('edit-ann-id').value);
      const idx = announcements.findIndex(item => item.id === id);
      if (idx === -1) {
        alert('Announcement not found.');
        return;
      }

      const desc = document.getElementById('edit-ann-description').value.trim();
      const updated = {
        ...announcements[idx],
        title: document.getElementById('edit-ann-title').value.trim(),
        category: document.getElementById('edit-ann-category').value,
        date: document.getElementById('edit-ann-date').value.trim(),
        venue: document.getElementById('edit-ann-venue').value.trim(),
        city: document.getElementById('edit-ann-city').value.trim() || 'Biñan, Laguna',
        chapter: document.getElementById('edit-ann-chapter').value.trim(),
        target: parseInt(document.getElementById('edit-ann-participants').value) || 100,
        registered: parseInt(document.getElementById('edit-ann-registered').value) || 60,
        image: selectedEditPosterData || document.getElementById('edit-ann-image').value.trim() || 'assets/images/background.jpg',
        excerpt: desc.length > 140 ? desc.substring(0, 140) + '...' : desc,
        details: desc,
        pm: document.getElementById('edit-ann-pm').value.trim() || 'Alex R.',
        creative_lead: document.getElementById('edit-ann-creative').value.trim() || 'Maria P.',
        tech_director: document.getElementById('edit-ann-tech').value.trim() || 'John K.',
        room: document.getElementById('edit-ann-room').value.trim() || 'Auditorium A (2nd Floor)',
        parking: document.getElementById('edit-ann-parking').value.trim() || 'Lot B, Level-1',
        wifi: document.getElementById('edit-ann-wifi').value.trim() || 'YTR-Guest / goat2026',
        reg_link: document.getElementById('edit-ann-reglink').value.trim() || 'https://forms.gle/YcZudZkgefs1dEEn7',
        contact_person: document.getElementById('edit-ann-contact-person').value.trim() || 'Secretary: Rayvhen P. Muñoz'
      };

      announcements[idx] = updated;
      localStorage.setItem('ytr_announcements', JSON.stringify(announcements));
      try {
        if (window.YTR_API && YTR_API.announcements) {
          YTR_API.announcements.update(id, updated).catch(err => console.error('Cloud edit error:', err));
        }
      } catch(e) {}
      closeEditAnnouncementModal();
      renderAnnouncements();
      openViewAnnouncementModal(id);
      alert('Announcement updated successfully across all chapters!');
    }

    function deleteCurrentAnnouncement() {
      if (isChapterAdminUser) return;
      const id = Number(document.getElementById('edit-ann-id').value);
      if (!confirm('Are you sure you want to delete this announcement? This action cannot be undone.')) {
        return;
      }
      announcements = announcements.filter(item => item.id !== id);
      localStorage.setItem('ytr_announcements', JSON.stringify(announcements));
      closeEditAnnouncementModal();
      closeViewAnnouncementModal();
      renderAnnouncements();
      alert('Announcement deleted.');
    }

    // ==========================================
    // CREATE ANNOUNCEMENT (NATIONAL ADMIN)
    // ==========================================
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

      const desc = document.getElementById('ann-description').value.trim();
      const newAnn = {
        id: Date.now(),
        title: document.getElementById('ann-title').value.trim(),
        category: document.getElementById('ann-category').value,
        date: document.getElementById('ann-date').value,
        time: "9:00 AM - 5:00 PM",
        venue: document.getElementById('ann-venue').value.trim(),
        city: "Biñan, Laguna",
        chapter: document.getElementById('ann-chapter').value.trim() || 'National HQ',
        target: parseInt(document.getElementById('ann-participants').value) || 100,
        registered: 0,
        image: selectedCreatePosterData || 'assets/images/background.jpg',
        excerpt: desc.substring(0, 140) + '...',
        details: desc,
        pm: "Alex R.",
        creative_lead: "Maria P.",
        tech_director: "John K.",
        room: "Auditorium A (2nd Floor)",
        parking: "Lot B, Level-1",
        wifi: "YTR-Guest / goat2026",
        reg_link: "https://forms.gle/YcZudZkgefs1dEEn7",
        contact_team: document.getElementById('ann-chapter').value.trim() || 'National Secretariat',
        contact_person: "Secretary: Rayvhen P. Muñoz"
      };

      announcements.unshift(newAnn);
      localStorage.setItem('ytr_announcements', JSON.stringify(announcements));
      try {
        if (window.YTR_API && YTR_API.announcements) {
          YTR_API.announcements.create(newAnn).then(res => {
            if (res.ok && res.data && res.data.announcement) {
              const created = res.data.announcement;
              const idx = announcements.findIndex(item => item.id === newAnn.id);
              if (idx !== -1) {
                announcements[idx] = created;
                localStorage.setItem('ytr_announcements', JSON.stringify(announcements));
                renderAnnouncements();
              }
            }
          }).catch(err => console.error('Cloud create error:', err));
        }
      } catch(e) {}
      alert('Announcement published successfully to all chapters!');
      document.getElementById('createAnnouncementForm').reset();
      clearCreatePoster();
      closeCreateAnnouncementModal();
      renderAnnouncements();
    }

    // ==========================================
    // ZOOM IMAGE MODAL
    // ==========================================
    function openZoomImage(src) {
      if (!src) return;
      document.getElementById('zoom-img').src = src;
      document.getElementById('zoom-modal').style.display = 'flex';
    }

    function closeZoomModal() {
      document.getElementById('zoom-modal').style.display = 'none';
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