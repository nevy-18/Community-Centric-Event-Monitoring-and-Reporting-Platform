<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>YTR | Announcements</title>
  
  <!-- Phosphor Icons -->
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  
  <!-- Centralized CSS -->
  <link rel="stylesheet" href="assets/dashboard.css">
</head>
<body>

  <div class="app-wrapper">
    <!-- INJECT SIDEBAR -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-wrapper">
      <!-- INJECT TOPBAR -->
      <?php include 'includes/topbar.php'; ?>

      <main class="page-content">
        
        <!-- Header Section -->
        <div class="page-header">
          <div>
            <h1 class="font-roboto">Upcoming National Events</h1>
            <p>Mark your calendars for our largest gatherings of the year.</p>
          </div>
          <button class="btn btn-primary font-roboto"><i class="ph ph-plus"></i> Create Announcement</button>
        </div>

        <!-- Tabs -->
        <div class="tab-controls-wrapper">
          <div class="tabs-container font-roboto">
            <button class="tab-btn active"><i class="ph ph-megaphone"></i> All Announcements</button>
            <button class="tab-btn"><i class="ph ph-flag"></i> National Events</button>
            <button class="tab-btn"><i class="ph ph-users"></i> Chapter Gatherings</button>
          </div>
        </div>

        <!-- Events Grid -->
        <div class="announcement-grid">
          
          <!-- Card 1 -->
          <div class="announcement-card">
            <div class="announcement-poster">
              <img src="https://placehold.co/400x500/e2e8f0/64748b?text=Tremendous+Momentum" alt="Event Poster">
            </div>
            <h3 class="font-roboto">Tremendous Momentum</h3>
            <div class="announcement-meta">
              <div class="announcement-meta-item"><i class="ph ph-calendar-blank"></i> May 21, 2026</div>
              <div class="announcement-meta-item"><i class="ph ph-map-pin"></i> SJB Learning Center Calamba</div>
              <div class="announcement-meta-item"><i class="ph ph-users"></i> 100 Target Participants</div>
              <div class="announcement-meta-item"><i class="ph ph-flag"></i> Laguna Chapter</div>
            </div>
            <p class="announcement-excerpt">Join us for the ultimate showcase of the year. The 'Greatest of All Time' of Youth Tech Revolution will feature live algorithm optimization challenges...</p>
            <button class="announcement-view-btn font-roboto"><i class="ph ph-eye"></i> View Details</button>
          </div>

          <!-- Card 2 -->
          <div class="announcement-card">
            <div class="announcement-poster">
              <img src="https://placehold.co/400x500/e2e8f0/64748b?text=29th+Anniversary" alt="Event Poster">
            </div>
            <h3 class="font-roboto">29th YTR Anniversary</h3>
            <div class="announcement-meta">
              <div class="announcement-meta-item"><i class="ph ph-calendar-blank"></i> Sept 26, 2026</div>
              <div class="announcement-meta-item"><i class="ph ph-map-pin"></i> SJB Learning Center Calamba</div>
              <div class="announcement-meta-item"><i class="ph ph-users"></i> 100 Target Participants</div>
              <div class="announcement-meta-item"><i class="ph ph-flag"></i> Laguna Chapter</div>
            </div>
            <p class="announcement-excerpt">Join us for the ultimate showcase of the year. The 'Greatest of All Time' of Youth Tech Revolution will feature live algorithm optimization challenges...</p>
            <button class="announcement-view-btn font-roboto"><i class="ph ph-eye"></i> View Details</button>
          </div>

          <!-- Card 3 -->
          <div class="announcement-card">
            <div class="announcement-poster">
              <img src="https://placehold.co/400x500/e2e8f0/64748b?text=Leadership+Summit" alt="Event Poster">
            </div>
            <h3 class="font-roboto">National Leadership Summit</h3>
            <div class="announcement-meta">
              <div class="announcement-meta-item"><i class="ph ph-calendar-blank"></i> Dec 19-20, 2026</div>
              <div class="announcement-meta-item"><i class="ph ph-map-pin"></i> SJB Learning Center Calamba</div>
              <div class="announcement-meta-item"><i class="ph ph-users"></i> 100 Target Participants</div>
              <div class="announcement-meta-item"><i class="ph ph-flag"></i> Laguna Chapter</div>
            </div>
            <p class="announcement-excerpt">Join us for the ultimate showcase of the year. The 'Greatest of All Time' of Youth Tech Revolution will feature live algorithm optimization challenges...</p>
            <button class="announcement-view-btn font-roboto"><i class="ph ph-eye"></i> View Details</button>
          </div>

          <!-- Card 4 -->
          <div class="announcement-card">
            <div class="announcement-poster">
              <img src="https://placehold.co/400x500/e2e8f0/64748b?text=G.O.A.T+Level+Up" alt="Event Poster">
            </div>
            <h3 class="font-roboto">Greatest of All Time</h3>
            <div class="announcement-meta">
              <div class="announcement-meta-item"><i class="ph ph-calendar-blank"></i> Sept 28, 2026</div>
              <div class="announcement-meta-item"><i class="ph ph-map-pin"></i> SJB Learning Center Calamba</div>
              <div class="announcement-meta-item"><i class="ph ph-users"></i> 100 Target Participants</div>
              <div class="announcement-meta-item"><i class="ph ph-flag"></i> Laguna Chapter</div>
            </div>
            <p class="announcement-excerpt">Join us for the ultimate showcase of the year. The 'Greatest of All Time' of Youth Tech Revolution will feature live algorithm optimization challenges...</p>
            <button class="announcement-view-btn font-roboto"><i class="ph ph-eye"></i> View Details</button>
          </div>

        </div>
      </main>
    </div>
  </div>

</body>
</html>