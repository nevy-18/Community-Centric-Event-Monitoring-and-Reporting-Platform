<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chapter Poster Requests | YTR Portal</title>
    <link rel="stylesheet" href="assets/Chapter.css">
    <!-- Include Phosphor Icons for icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="assets/api-client.js"></script>
</head>
<body>
    <div class="app-wrapper">
        <!-- Dedicated Chapter Sidebar Include -->
        <?php include 'includes/sidebar_chapter.php'; ?>

        <div class="main-wrapper">
            <!-- Topbar Include -->
            <?php include 'includes/topbar.php'; ?>

            <div class="page-content">
                
                <div class="page-header flex-between" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.4rem;">
                            <h1 class="font-roboto" style="margin: 0; color: #0f172a; font-weight: 800;">Chapter Poster Requests</h1>
                            <span class="portal-indicator chapter"><i class="ph-fill ph-buildings"></i> Chapter Branch Portal</span>
                        </div>
                        <p style="color: var(--text-muted); margin: 0;">Request official publicity posters and download approved promotional media for your chapter's events.</p>
                    </div>
                    <div>
                        <button class="btn btn-primary" onclick="openNewPosterModal()">
                            <i class="ph ph-plus-circle"></i> Request Event Poster
                        </button>
                    </div>
                </div>

                <!-- 1. Metric Cards Row -->
                <div class="metrics-grid">
                    <div class="metric-card">
                        <div class="metric-icon blue"><i class="ph ph-image"></i></div>
                        <div>
                            <span class="metric-title font-roboto">Total Media Requests</span>
                            <div class="metric-value font-roboto" id="metric-total">0</div>
                            <p class="metric-sub">All poster orders submitted by your chapter</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon orange"><i class="ph ph-hourglass-high"></i></div>
                        <div>
                            <span class="metric-title font-roboto">In National Queue</span>
                            <div class="metric-value font-roboto" id="metric-queue">0</div>
                            <p class="metric-sub">Awaiting national design review</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon purple"><i class="ph ph-paint-brush"></i></div>
                        <div>
                            <span class="metric-title font-roboto">In Production</span>
                            <div class="metric-value font-roboto" id="metric-production">0</div>
                            <p class="metric-sub">Graphic artists drafting collateral</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon green"><i class="ph ph-check-circle"></i></div>
                        <div>
                            <span class="metric-title font-roboto">Ready to Download</span>
                            <div class="metric-value font-roboto" id="metric-ready">0</div>
                            <p class="metric-sub">Completed designs approved for release</p>
                        </div>
                    </div>
                </div>

                <!-- 2. Tabs and Filters -->
                <div class="tab-controls-wrapper">
                    <div class="tabs-container font-roboto">
                        <button class="tab-btn active font-roboto" id="tab-btn-accepted" onclick="switchTab('accepted', this)">
                            <i class="ph ph-check-circle"></i> Completed & In-Production (<span id="count-accepted">0</span>)
                        </button>
                        <button class="tab-btn font-roboto" id="tab-btn-queue" onclick="switchTab('queue', this)">
                            <i class="ph ph-clock"></i> In Queue (<span id="count-queue">0</span>)
                        </button>
                    </div>
                    <div class="filters">
                        <div class="search-bar" style="margin: 0; min-width: 240px;">
                            <i class="ph ph-magnifying-glass"></i>
                            <input type="text" id="poster-search" placeholder="Search event title..." oninput="handleSearch()">
                        </div>
                    </div>
                </div>

                <!-- 3. Dynamic Card Grids -->
                
                <!-- TAB CONTENT: ACCEPTED -->
                <div id="accepted-grid" class="poster-grid tab-content active">
                    <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: var(--text-muted);">
                        <i class="ph ph-spinner ph-spin" style="font-size: 2rem;"></i>
                        <p style="margin-top: 0.5rem;">Loading approved poster collaterals...</p>
                    </div>
                </div>

                <!-- TAB CONTENT: IN QUEUE -->
                <div id="queue-grid" class="poster-grid tab-content" style="display: none;">
                    <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: var(--text-muted);">
                        <i class="ph ph-spinner ph-spin" style="font-size: 2rem;"></i>
                        <p style="margin-top: 0.5rem;">Loading in-queue poster requests...</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL: REQUEST NEW POSTER -->
    <!-- MODAL 1: REQUEST NEW POSTER (Comprehensive with all criteria) -->
    <div id="new-poster-modal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.65); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 14px; max-width: 680px; width: 92%; max-height: 90vh; overflow-y: auto; padding: 2rem; box-shadow: 0 25px 50px -12px rgba(15,23,42,0.25);">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; margin-bottom: 1.25rem;">
                <div>
                    <h3 class="font-roboto" style="margin: 0; color: #0f172a; font-size: 1.25rem; font-weight: 800;">
                        <i class="ph-fill ph-image-square" style="color: var(--primary);"></i> Request Chapter Event Poster
                    </h3>
                    <p style="margin: 0.2rem 0 0; font-size: 0.8rem; color: #64748b;">Submit event details and reference mockup for National Creative production</p>
                </div>
                <button type="button" onclick="closeNewPosterModal()" style="background: none; border: none; font-size: 1.4rem; cursor: pointer; color: #64748b;">
                    <i class="ph ph-x"></i>
                </button>
            </div>
            <form id="new-poster-form" onsubmit="submitNewPoster(event)">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.825rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">Event Title *</label>
                    <input type="text" id="np-event-title" required placeholder="e.g. Greatest of All Time (G.O.A.T.) Rally" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.875rem;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.825rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">Target Event Date *</label>
                        <input type="date" id="np-event-date" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.825rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">Event Time & Duration *</label>
                        <input type="text" id="np-event-time" required placeholder="e.g. 2:00 PM - 6:00 PM" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.875rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.825rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">Venue / Platform *</label>
                        <input type="text" id="np-venue" required placeholder="e.g. Gymnasium / Facebook Live" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.825rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">Target Attendees Quota *</label>
                        <input type="text" id="np-participants" required placeholder="e.g. 150 Participants" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.875rem;">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.825rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">Subtitle / Theme / Concept *</label>
                    <input type="text" id="np-theme" required placeholder="e.g. Level up or Stay the Game: True Greatness through Faith" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.875rem;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.825rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">Registration Link / Form URL</label>
                        <input type="url" id="np-reglink" placeholder="https://bit.ly/goat-rally-reg" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.825rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">Poster Release Deadline *</label>
                        <input type="date" id="np-deadline" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.875rem;">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.825rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">Publicity Caption & Blurb</label>
                    <textarea id="np-caption" rows="2" placeholder="Official announcement blurb to be published alongside the poster..." style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.875rem;"></textarea>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.825rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
                        Upload Reference Poster / Layout Draft
                    </label>
                    <div class="proof-dropzone" onclick="document.getElementById('np-reference-poster').click()">
                        <i class="ph ph-image" style="font-size: 2rem; color: #3b82f6;"></i>
                        <p style="margin: 0.25rem 0 0; font-size: 0.825rem; font-weight: 700; color: #1e293b;">Click to upload draft/mockup</p>
                        <p style="margin: 0; font-size: 0.725rem; color: #64748b;">JPG, PNG or PDF reference design</p>
                        <input type="file" id="np-reference-poster" accept="image/*,application/pdf" onchange="handleNewPosterFile(this)">
                    </div>
                    <div id="np-file-display" style="display:none; margin-top: 0.4rem; font-size: 0.78rem; color: #16a34a; font-weight: 600;">
                        <i class="ph-fill ph-file-check"></i> <span id="np-file-name"></span>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.825rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">Special Instructions / Guest Speaker / Theme Verses</label>
                    <textarea id="np-notes" rows="2" placeholder="Specify any specific color palettes, logos, or chapter branding notes..." style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.875rem;"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn btn-outline" onclick="closeNewPosterModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="np-submit-btn">
                        <i class="ph ph-paper-plane-tilt"></i> Submit Poster Request
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- MODAL 2: CHAPTER EVENT TIMELINE & ASSET DOWNLOAD MODAL -->
    <div id="chapter-timeline-modal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.65); z-index: 1000; align-items: center; justify-content: center;">
        <div class="modal-xl" style="background: #ffffff;">
            <!-- Modal Header -->
            <div class="modal-header-banner">
                <div>
                    <h3 class="font-roboto">
                        <i class="ph-fill ph-kanban" style="color: var(--primary);"></i> Event Poster Details & Progress
                    </h3>
                    <p>Track your media collateral progress, download approved posters, and view milestone updates</p>
                </div>
                <button type="button" onclick="closeChapterTimelineModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #64748b; padding: 4px;">
                    <i class="ph ph-x"></i>
                </button>
            </div>

            <!-- Modal Body (3 Columns) -->
            <div class="modal-body-scroll">
                <div class="timeline-grid">
                    <!-- Column 1: Poster Preview & Download -->
                    <div class="timeline-left-card">
                        <div class="poster-media-box" style="height: 320px;">
                            <img id="ctl-preview-poster-img" src="assets/images/background.jpg" alt="Official Poster" onerror="this.src='assets/images/background.jpg'">
                            <button type="button" class="poster-zoom-btn" onclick="openZoomImage(document.getElementById('ctl-preview-poster-img').src)">
                                <i class="ph ph-magnifying-glass-plus"></i> View Full Poster
                            </button>
                        </div>
                        <div>
                            <span class="badge" id="ctl-chapter-status-pill" style="background: #dbeafe; color: #1e40af; font-weight: 700; margin-bottom: 0.5rem; display: inline-block;">
                                Production In-Progress
                            </span>
                            <h3 class="font-roboto" id="ctl-chapter-title" style="font-size: 1.15rem; color: #0f172a; font-weight: 800; margin: 0 0 0.35rem;">
                                Event Title
                            </h3>
                            <p id="ctl-chapter-theme" style="color: #64748b; font-size: 0.85rem; margin-bottom: 0.75rem;">
                                Theme / Concept
                            </p>
                            <div style="font-size: 0.8rem; color: #334155; line-height: 1.6; border-top: 1px solid #f1f5f9; padding-top: 0.5rem;">
                                <div><strong>Date & Time:</strong> <span id="ctl-chapter-datetime">--</span></div>
                                <div><strong>Venue:</strong> <span id="ctl-chapter-venue">--</span></div>
                                <div><strong>Target Attendees:</strong> <span id="ctl-chapter-attendees">--</span></div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary" id="btn-chapter-download-poster" style="width: 100%; background: #16a34a; border-color: #15803d;" onclick="downloadChapterPoster()">
                            <i class="ph ph-download-simple"></i> Download Official Poster
                        </button>
                    </div>

                    <!-- Column 2: Event Timeline (Numbered Vertical Pipeline) -->
                    <div class="timeline-center-card">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 0.5rem;">
                            <h4 class="font-roboto" style="margin: 0; color: #0f172a; font-weight: 800; font-size: 1.1rem;">
                                Event Timeline
                            </h4>
                            <span style="font-size: 0.75rem; color: #64748b; font-weight: 600;">5 Milestone Stages</span>
                        </div>

                        <div class="timeline-pipeline" id="chapter-pipeline-container">
                            <!-- Populated dynamically -->
                        </div>
                    </div>

                    <!-- Column 3: Chapter Next Step Action & Deliverables -->
                    <div class="timeline-right-card">
                        <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">
                            <h4 class="font-roboto" style="margin: 0; color: #0f172a; font-weight: 800; font-size: 1.1rem;">
                                Chapter Action Center
                            </h4>
                            <p style="margin: 0.2rem 0 0; color: #64748b; font-size: 0.78rem;">Submit your milestone deliverables as requested by National Media</p>
                        </div>

                        <!-- Dynamic Next Step Notice Box -->
                        <div id="chapter-action-box" class="chapter-next-step-box">
                            <div style="display: flex; gap: 0.6rem; align-items: flex-start;">
                                <i class="ph-fill ph-sparkle" style="font-size: 1.35rem; color: #16a34a; flex-shrink: 0; margin-top: 2px;"></i>
                                <div>
                                    <h5 style="margin: 0 0 0.2rem; color: #14532d; font-size: 0.85rem; font-weight: 700;" id="chapter-step-headline">
                                        Milestone Update
                                    </h5>
                                    <p style="margin: 0; color: #166534; font-size: 0.78rem; line-height: 1.4;" id="chapter-step-instructions">
                                        Your request is currently being handled by National Creative.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Chapter Promotional Video Submission Form -->
                        <form id="chapter-submit-video-form" onsubmit="submitChapterVideoProof(event)">
                            <div style="font-size: 0.825rem; font-weight: 700; color: #0f172a; margin-bottom: 0.4rem;">
                                Submit Promotional Video Deliverable
                            </div>
                            <div class="proof-dropzone" style="padding: 0.75rem;" onclick="document.getElementById('ch-video-file').click()">
                                <i class="ph ph-video-camera" style="font-size: 1.5rem; color: #2563eb;"></i>
                                <p style="margin: 0.2rem 0 0; font-size: 0.78rem; font-weight: 700;">Upload MP4 Teaser / Clip</p>
                                <p style="margin: 0; font-size: 0.7rem; color: #64748b;">Max 50MB</p>
                                <input type="file" id="ch-video-file" accept="video/mp4,video/*" onchange="handleChapterVideoSelected(this)">
                            </div>
                            <div id="ch-video-display" style="display:none; margin-top: 0.35rem; font-size: 0.75rem; color: #16a34a; font-weight: 600;">
                                <i class="ph-fill ph-file-check"></i> <span id="ch-video-name"></span>
                            </div>

                            <div style="margin: 0.6rem 0;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Or Video Cloud Link (Google Drive / YouTube / FB):</label>
                                <input type="url" id="ch-video-url" placeholder="https://..." style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.8rem;">
                            </div>

                            <button type="submit" class="btn btn-primary" id="btn-submit-video" style="width: 100%; font-size: 0.8rem; padding: 0.5rem;">
                                <i class="ph ph-paper-plane-tilt"></i> Submit Promotional Video
                            </button>
                        </form>

                        <!-- Attached Deliverables List -->
                        <div style="border-top: 1px solid #f1f5f9; padding-top: 0.85rem; margin-top: 0.85rem;">
                            <h5 class="font-roboto" style="margin: 0 0 0.5rem; color: #0f172a; font-weight: 700; font-size: 0.85rem;">
                                Released Media & Proofs
                            </h5>
                            <div id="chapter-recent-proofs-list" style="max-height: 160px; overflow-y: auto;">
                                <!-- Dynamically populated -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        let currentUser = null;
        try {
            const raw = localStorage.getItem('ytr_user');
            if (raw) currentUser = JSON.parse(raw);
        } catch(e) {}
        let chapterId = parseInt((currentUser && currentUser.chapter_id) ? currentUser.chapter_id : (localStorage.getItem('ytr_chapter_id') || '1'));
        let currentTab = 'accepted';
        let acceptedCache = [];
        let queueCache = [];
        let activeChapterRequest = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadPosterData('accepted');
            loadPosterData('queue');
        });

        async function loadPosterData(tab) {
            try {
                const res = await YTR_API.posterRequests.list(tab);
                if (res.ok && res.data) {
                    // Filter specifically for this chapter
                    const filtered = res.data.filter(p => p.chapter_id === chapterId);
                    if (tab === 'accepted') {
                        acceptedCache = filtered;
                        document.getElementById('count-accepted').textContent = filtered.length;
                        renderAcceptedGrid(filtered);
                    } else {
                        queueCache = filtered;
                        document.getElementById('count-queue').textContent = filtered.length;
                        renderQueueGrid(filtered);
                    }
                    updateMetrics();
                }
            } catch (err) {
                console.error(`Failed to load ${tab} posters:`, err);
            }
        }

        function updateMetrics() {
            const total = acceptedCache.length + queueCache.length;
            const inQueue = queueCache.length;
            const inProd = acceptedCache.filter(p => p.status === 'in_progress').length;
            const ready = acceptedCache.filter(p => p.status === 'completed').length;

            document.getElementById('metric-total').textContent = total;
            document.getElementById('metric-queue').textContent = inQueue;
            document.getElementById('metric-production').textContent = inProd;
            document.getElementById('metric-ready').textContent = ready;
        }

        function renderAcceptedGrid(items) {
            const grid = document.getElementById('accepted-grid');
            if (!items || items.length === 0) {
                grid.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: #fff; border-radius: 8px; border: 1px dashed #cbd5e1;">
                        <i class="ph ph-image-square" style="font-size: 2.5rem; color: #94a3b8;"></i>
                        <h4 style="color: #334155; margin: 0.5rem 0 0.25rem;">No Completed Posters Yet</h4>
                        <p style="color: #64748b; font-size: 0.9rem;">Once National Media approves your request, posters will appear here for download.</p>
                    </div>`;
                return;
            }

            grid.innerHTML = items.map(p => {
                const isReady = p.status === 'completed';
                const statusLabel = isReady ? 'Ready for Promotion' : 'In Production';
                const statusCls = isReady ? 'green' : 'blue';
                const posterImg = p.final_poster_path || p.reference_poster_path || 'assets/images/background.jpg';

                return `
                    <div class="poster-card type-accepted">
                        <div class="card-hero" style="position: relative;">
                            <img src="${escapeHtml(posterImg)}" alt="${escapeHtml(p.event_title)}" onerror="this.src='assets/images/background.jpg'">
                            <div style="position: absolute; top: 12px; left: 12px;">
                                <span class="status-badge ${statusCls}"><i class="ph ${isReady ? 'ph-check-circle' : 'ph-paint-brush'}"></i> ${statusLabel}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="event-title font-roboto" style="color: var(--primary); font-weight: 700; margin-bottom: 0.5rem;">${escapeHtml(p.event_title)}</h4>
                            <ul class="event-bullets">
                                <li><i class="ph ph-calendar"></i> ${escapeHtml(p.event_date || 'Date TBD')}</li>
                                <li><i class="ph ph-map-pin"></i> ${escapeHtml(p.venue || 'Venue TBD')}</li>
                                <li><i class="ph ph-clock"></i> Deadline: ${escapeHtml(p.requested_deadline || 'N/A')}</li>
                            </ul>
                            <p class="event-desc" style="color: #475569; font-size: 0.875rem;">
                                ${escapeHtml(p.theme_or_concept || p.review_note || 'Approved promotional artwork.')}
                            </p>
                        </div>
                        <div class="card-footer dual-btn">
                            <button class="btn btn-outline" onclick="triggerChapterDownload('${escapeHtml(p.event_title)}', '${escapeHtml(posterImg)}')">
                                <i class="ph ph-download-simple"></i> Download
                            </button>
                            <button class="btn btn-primary" onclick="openChapterTimelineModal(${p.id})">
                                View Timeline & Assets
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function renderQueueGrid(items) {
            const grid = document.getElementById('queue-grid');
            if (!items || items.length === 0) {
                grid.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: #fff; border-radius: 8px; border: 1px dashed #cbd5e1;">
                        <i class="ph ph-check-circle" style="font-size: 2.5rem; color: #16a34a;"></i>
                        <h4 style="color: #334155; margin: 0.5rem 0 0.25rem;">No Pending Requests in Queue</h4>
                        <p style="color: #64748b; font-size: 0.9rem;">Click <strong>Request Event Poster</strong> above to submit a design request.</p>
                    </div>`;
                return;
            }

            grid.innerHTML = items.map(p => {
                const posterImg = p.reference_poster_path || 'assets/images/background.jpg';
                return `
                    <div class="poster-card type-queue">
                        <div class="card-header flex-between" style="border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                            <span class="badge pending"><i class="ph ph-clock"></i> Awaiting National Review</span>
                            <span style="font-size: 0.8rem; color: #64748b;">Requested: ${escapeHtml(p.submitted_at ? p.submitted_at.substring(0, 10) : 'Recent')}</span>
                        </div>
                        <div class="card-body">
                            <div class="event-preview" style="margin-bottom: 0.5rem;">
                                <img src="${escapeHtml(posterImg)}" alt="Reference Thumbnail" class="poster-thumb" onerror="this.src='assets/images/background.jpg'">
                                <div class="preview-details">
                                    <h4 class="event-title font-roboto" style="color: #0f172a; font-weight: 700; margin: 0 0 0.25rem;">${escapeHtml(p.event_title)}</h4>
                                    <div style="font-size: 0.8rem; color: #475569;">
                                        <strong>Date:</strong> ${escapeHtml(p.event_date || 'N/A')}<br>
                                        <strong>Venue:</strong> ${escapeHtml(p.venue || 'N/A')}
                                    </div>
                                </div>
                            </div>
                            <p class="event-desc" style="font-size: 0.85rem; color: #64748b;">
                                <strong>Concept:</strong> ${escapeHtml(p.theme_or_concept || 'None')}
                            </p>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-outline" style="width: 100%;" onclick="openChapterTimelineModal(${p.id})">
                                <i class="ph ph-info"></i> View Submitted Progress
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function switchTab(tabId, clickedBtn) {
            currentTab = tabId;
            document.querySelectorAll('.tab-content').forEach(content => content.style.display = 'none');
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(tabId + '-grid').style.display = 'grid';
            if (clickedBtn) clickedBtn.classList.add('active');
        }

        function handleSearch() {
            const q = document.getElementById('poster-search').value.toLowerCase().trim();
            if (currentTab === 'accepted') {
                const filtered = acceptedCache.filter(p => (p.event_title || '').toLowerCase().includes(q));
                renderAcceptedGrid(filtered);
            } else {
                const filtered = queueCache.filter(p => (p.event_title || '').toLowerCase().includes(q));
                renderQueueGrid(filtered);
            }
        }

        function openNewPosterModal() {
            document.getElementById('new-poster-modal').style.display = 'flex';
        }

        function closeNewPosterModal() {
            document.getElementById('new-poster-modal').style.display = 'none';
        }

        function handleNewPosterFile(input) {
            if (input.files && input.files[0]) {
                const f = input.files[0];
                document.getElementById('np-file-name').textContent = `${f.name} (${(f.size / 1024 / 1024).toFixed(2)} MB)`;
                document.getElementById('np-file-display').style.display = 'block';
            }
        }

        async function submitNewPoster(e) {
            e.preventDefault();
            const btn = document.getElementById('np-submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Submitting...';

            const formData = new FormData();
            formData.append('chapter_id', chapterId);
            formData.append('event_title', document.getElementById('np-event-title').value.trim());
            formData.append('event_date', document.getElementById('np-event-date').value);
            formData.append('event_time', document.getElementById('np-event-time').value.trim());
            formData.append('venue', document.getElementById('np-venue').value.trim());
            formData.append('target_participants', document.getElementById('np-participants').value.trim());
            formData.append('theme_or_concept', document.getElementById('np-theme').value.trim());
            formData.append('registration_link', document.getElementById('np-reglink').value.trim());
            formData.append('requested_deadline', document.getElementById('np-deadline').value);
            formData.append('caption', document.getElementById('np-caption').value.trim());
            formData.append('additional_notes', document.getElementById('np-notes').value.trim());

            const fileInput = document.getElementById('np-reference-poster');
            if (fileInput.files && fileInput.files[0]) {
                formData.append('reference_poster', fileInput.files[0]);
            }

            try {
                const res = await YTR_API.posterRequests.create(formData);
                if (res.ok) {
                    alert('Poster request submitted successfully to National Media Team!');
                    document.getElementById('new-poster-form').reset();
                    document.getElementById('np-file-display').style.display = 'none';
                    closeNewPosterModal();
                    await loadPosterData('accepted');
                    await loadPosterData('queue');
                    switchTab('queue', document.getElementById('tab-btn-queue'));
                } else {
                    alert('Failed to submit: ' + (res.message || 'Error'));
                }
            } catch (err) {
                console.error('Error submitting poster:', err);
                alert('Network error connecting to Python API.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="ph ph-paper-plane-tilt"></i> Submit Poster Request';
            }
        }

        // ==========================================
        // CHAPTER EVENT TIMELINE MODAL
        // ==========================================
        async function openChapterTimelineModal(id) {
            document.getElementById('chapter-timeline-modal').style.display = 'flex';
            const pipelineEl = document.getElementById('chapter-pipeline-container');
            pipelineEl.innerHTML = `
                <div style="text-align: center; padding: 2rem; color: #64748b;">
                    <i class="ph ph-spinner ph-spin" style="font-size: 1.8rem;"></i>
                    <p style="margin-top: 0.5rem; font-size: 0.85rem;">Loading progress...</p>
                </div>`;

            try {
                const res = await YTR_API.posterRequests.getTimeline(id);
                if (res.ok && res.data) {
                    const req = res.data.request;
                    activeChapterRequest = req;

                    const posterSrc = req.final_poster_path || req.reference_poster_path || 'assets/images/background.jpg';
                    document.getElementById('ctl-preview-poster-img').src = posterSrc;
                    document.getElementById('ctl-chapter-title').textContent = req.event_title || 'Untitled Event';
                    document.getElementById('ctl-chapter-theme').textContent = req.theme_or_concept || 'Theme not specified';
                    
                    const timeStr = req.event_time ? ` • ${req.event_time}` : '';
                    document.getElementById('ctl-chapter-datetime').textContent = (req.event_date || 'Date TBD') + timeStr;
                    document.getElementById('ctl-chapter-venue').textContent = req.venue || 'Venue TBD';
                    document.getElementById('ctl-chapter-attendees').textContent = req.target_participants || '100+ Participants';

                    const currentStepNum = req.current_step || 1;
                    const isDone = req.status === 'completed';
                    document.getElementById('ctl-chapter-status-pill').textContent = isDone ? 'Event Concluded & Accomplished' : `Stage ${currentStepNum} Active`;

                    // Render steps
                    renderChapterPipeline(res.data.steps || []);

                    // Update Next Step Notice Box
                    updateChapterNextStepBox(req, res.data.steps || []);

                    // Render released proofs
                    renderChapterProofs(res.data.updates || []);
                }
            } catch (err) {
                console.error('Error fetching chapter timeline:', err);
                pipelineEl.innerHTML = `<p style="color: #ef4444; padding: 1rem;">Network error fetching timeline.</p>`;
            }
        }

        function closeChapterTimelineModal() {
            document.getElementById('chapter-timeline-modal').style.display = 'none';
        }

        function renderChapterPipeline(steps) {
            const pipelineEl = document.getElementById('chapter-pipeline-container');
            const stepDescriptions = {
                1: 'Communications, Marketing & Creative Team drafts and finalized official graphics.',
                2: 'Official publication on social media page with live engagement link.',
                3: 'Promotional teaser video clip produced and distributed.',
                4: 'Post-activity attendance, photo documentation, and accomplishment report.',
                5: 'Final event audit approved and collateral archived.'
            };

            pipelineEl.innerHTML = steps.map(s => {
                const isCompleted = s.status === 'completed';
                const isInProg = s.status === 'in_progress';
                const stepClass = isCompleted ? 'completed' : (isInProg ? 'in-progress' : 'pending');

                let badgeCls = 'pending';
                let badgeText = 'Pending';
                if (isCompleted) { badgeCls = 'completed'; badgeText = 'Completed'; }
                else if (isInProg) { badgeCls = 'in_progress'; badgeText = 'In Progress'; }

                const desc = s.notes || stepDescriptions[s.step_order] || 'Stage in progress';
                let proofPillHtml = '';
                if (s.proof_file_path) {
                    const fname = s.proof_file_path.split('/').pop();
                    proofPillHtml = `
                        <a href="${escapeHtml(s.proof_file_path)}" target="_blank" class="step-attachment-pill">
                            <i class="ph ph-file-arrow-down"></i> ${escapeHtml(fname)}
                        </a>
                    `;
                }

                return `
                    <div class="timeline-step ${stepClass}">
                        <div class="step-marker">
                            ${isCompleted ? '<i class="ph-bold ph-check"></i>' : s.step_order}
                        </div>
                        <div class="step-content">
                            <div class="step-header">
                                <span class="step-title font-roboto">${escapeHtml(s.step_title)}</span>
                                <span class="step-badge ${badgeCls}">${badgeText}</span>
                            </div>
                            <div class="step-subtext">
                                ${escapeHtml(desc)}
                            </div>
                            ${proofPillHtml}
                        </div>
                    </div>
                `;
            }).join('');
        }

        function updateChapterNextStepBox(req, steps) {
            const headlineEl = document.getElementById('chapter-step-headline');
            const instructionsEl = document.getElementById('chapter-step-instructions');
            const cur = req.current_step || 1;

            if (req.status === 'submitted' || req.status === 'under_review') {
                headlineEl.textContent = 'Awaiting National Review & Verification';
                instructionsEl.textContent = 'National Admin is currently reviewing your submitted event requirements and mockups.';
            } else if (cur === 2 || cur === 3) {
                headlineEl.textContent = '🚀 Official Poster Released! Next Action: Promotional Video';
                instructionsEl.textContent = 'The official poster has been approved and published. Please upload or link your chapter promotional video clip below.';
            } else if (cur === 4) {
                headlineEl.textContent = '📸 Event Execution: Submit Post-Event Documentation';
                instructionsEl.textContent = 'Conduct your event smoothly. Once concluded, navigate to the Chapter Reports tab to submit attendee figures, documentation, and the final accomplishment report.';
            } else if (cur === 5 || req.status === 'completed') {
                headlineEl.textContent = '🎉 All Event Milestones Accomplished';
                instructionsEl.textContent = 'All collateral and post-event reports have been received, verified, and successfully archived!';
            }
        }

        function renderChapterProofs(updates) {
            const listEl = document.getElementById('chapter-recent-proofs-list');
            const proofUpdates = (updates || []).filter(u => u.proof_file_path || u.update_type === 'proof_uploaded');

            if (proofUpdates.length === 0) {
                listEl.innerHTML = `
                    <div style="text-align: center; padding: 0.8rem; color: #94a3b8; font-size: 0.78rem;">
                        <p style="margin: 0;">No official collateral files attached yet.</p>
                    </div>`;
                return;
            }

            listEl.innerHTML = proofUpdates.map(u => {
                const path = u.proof_file_path || '';
                const fname = path.split('/').pop() || 'Deliverable_File';
                const isImg = fname.match(/\.(jpg|jpeg|png|webp|gif)$/i);
                const isVid = fname.match(/\.(mp4|mov|avi)$/i);
                const icon = isImg ? 'ph-image' : (isVid ? 'ph-video-camera' : 'ph-file-text');

                return `
                    <div class="recent-proof-item">
                        <div class="recent-proof-left">
                            <i class="ph ${icon}" style="font-size: 1.25rem; color: #2563eb;"></i>
                            <div class="proof-file-name" title="${escapeHtml(fname)}">${escapeHtml(fname)}</div>
                        </div>
                        <a href="${escapeHtml(path)}" target="_blank" download class="btn btn-outline" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">
                            <i class="ph ph-download-simple"></i> Download
                        </a>
                    </div>
                `;
            }).join('');
        }

        function handleChapterVideoSelected(input) {
            if (input.files && input.files[0]) {
                const f = input.files[0];
                document.getElementById('ch-video-name').textContent = `${f.name} (${(f.size / 1024 / 1024).toFixed(2)} MB)`;
                document.getElementById('ch-video-display').style.display = 'block';
            }
        }

        async function submitChapterVideoProof(e) {
            e.preventDefault();
            if (!activeChapterRequest) return;

            const btn = document.getElementById('btn-submit-video');
            btn.disabled = true;
            btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Submitting...';

            const videoUrl = document.getElementById('ch-video-url').value.trim();
            const fileInput = document.getElementById('ch-video-file');

            const formData = new FormData();
            formData.append('step_order', 3);
            formData.append('step_status', 'completed');
            formData.append('notes', videoUrl ? `Promotional video link: ${videoUrl}` : 'Promotional video teaser uploaded by chapter.');
            formData.append('updated_by', chapterId);

            if (fileInput.files && fileInput.files[0]) {
                formData.append('proof_file', fileInput.files[0]);
            }

            try {
                const res = await YTR_API.posterRequests.updateTimelineStep(activeChapterRequest.id, formData);
                if (res.ok) {
                    alert('Promotional video deliverable successfully submitted to National Media!');
                    document.getElementById('chapter-submit-video-form').reset();
                    document.getElementById('ch-video-display').style.display = 'none';
                    await openChapterTimelineModal(activeChapterRequest.id);
                    await loadPosterData('accepted');
                } else {
                    alert('Submission error: ' + (res.message || 'Error'));
                }
            } catch (err) {
                console.error('Error submitting video:', err);
                alert('Network error submitting video.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="ph ph-paper-plane-tilt"></i> Submit Promotional Video';
            }
        }

        function downloadChapterPoster() {
            if (!activeChapterRequest) return;
            const src = activeChapterRequest.final_poster_path || activeChapterRequest.reference_poster_path || 'assets/images/background.jpg';
            triggerChapterDownload(activeChapterRequest.event_title, src);
        }

        function triggerChapterDownload(title, path) {
            if (path) {
                const link = document.createElement('a');
                link.href = path;
                link.download = `Poster_${title.replace(/\s+/g, '_')}.jpg`;
                link.target = '_blank';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                alert(`Initiating download for promotional poster bundle: "${title}". File packaging in progress.`);
            }
        }

        function openZoomImage(url) {
            window.open(url, '_blank');
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
    </script>
</body>
</html>
