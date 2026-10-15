<?php include 'includes/auth_guard.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Poster Approval | YTR Portal</title>
    <link rel="stylesheet" href="assets/National.css">
    <!-- Include Phosphor Icons for icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="assets/api-client.js"></script>
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar Include -->
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-wrapper">
            <!-- Topbar Include -->
            <?php include 'includes/topbar.php'; ?>

            <div class="page-content">
                
                <div class="page-header flex-between" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                    <div class="header-title-group">
                        <h1 class="font-roboto">Event Poster Approval</h1>
                        <p style="color: var(--text-muted); margin-top: 0.25rem;">Review, endorse, and manage promotional media requests and visual collateral across all chapters.</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn btn-primary" onclick="openNewPosterModal()">
                            <i class="ph ph-plus-circle"></i> Request Event Poster
                        </button>
                    </div>
                </div>

                <!-- Pending Review Visual Banner -->
                <div id="pending-alert" class="pending-alert-banner" style="display: none;">
                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                        <i class="ph-fill ph-bell-ringing" style="font-size: 1.6rem; color: #b45309;"></i>
                        <div>
                            <strong style="color: #92400e; font-size: 0.95rem;">Pending Poster Review Action Required</strong>
                            <p style="margin: 0; color: #b45309; font-size: 0.85rem;" id="pending-alert-text">You have submissions awaiting design endorsement.</p>
                        </div>
                    </div>
                    <button class="btn btn-primary" style="background: #d97706; border-color: #b45309; padding: 0.4rem 0.85rem; font-size: 0.825rem;" onclick="switchToQueueTab()">
                        Review In-Queue (<span id="alert-queue-count">0</span>)
                    </button>
                </div>

                <!-- 1. Metric Cards Row -->
                <div class="metrics-grid">
                    <div class="metric-card">
                        <div class="metric-icon blue"><i class="ph ph-calendar-check"></i></div>
                        <div>
                            <span class="metric-title font-roboto">Total Active Events</span>
                            <div class="metric-value font-roboto" id="metric-active">0</div>
                            <p class="metric-sub">Total approved events requiring media assets</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon orange"><i class="ph ph-file-text"></i></div>
                        <div>
                            <span class="metric-title font-roboto">Pending Poster Request</span>
                            <div class="metric-value font-roboto" id="metric-pending">0</div>
                            <p class="metric-sub">New applications awaiting design review</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon purple"><i class="ph ph-hourglass-high"></i></div>
                        <div>
                            <span class="metric-title font-roboto">Awaiting Final Asset</span>
                            <div class="metric-value font-roboto" id="metric-awaiting">0</div>
                            <p class="metric-sub">Designs currently in production pipeline</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon green"><i class="ph ph-check-circle"></i></div>
                        <div>
                            <span class="metric-title font-roboto">Concluded Media</span>
                            <div class="metric-value font-roboto" id="metric-concluded">0</div>
                            <p class="metric-sub">Posters finalized, released, and approved</p>
                        </div>
                    </div>
                </div>

                <!-- 2. Tabs and Filters -->
                <div class="tab-controls-wrapper">
                    <div class="tabs-container font-roboto">
                        <button class="tab-btn active font-roboto" id="tab-btn-accepted" onclick="switchTab('accepted', this)">
                            <i class="ph ph-check-circle"></i> Accepted (<span id="count-accepted">0</span>)
                        </button>
                        <button class="tab-btn font-roboto" id="tab-btn-queue" onclick="switchTab('queue', this)">
                            <i class="ph ph-clock"></i> In Queue (<span id="count-queue">0</span>)
                        </button>
                    </div>
                    <div class="filters" style="display: flex; gap: 0.75rem; align-items: center;">
                        <div class="search-bar" style="margin: 0; min-width: 240px;">
                            <i class="ph ph-magnifying-glass"></i>
                            <input type="text" id="poster-search" placeholder="Search title, chapter..." oninput="handleSearch()">
                        </div>
                    </div>
                </div>

                <!-- 3. Dynamic Card Grids -->
                
                <!-- TAB CONTENT: ACCEPTED -->
                <div id="accepted-grid" class="poster-grid tab-content active">
                    <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: var(--text-muted);">
                        <i class="ph ph-spinner ph-spin" style="font-size: 2rem;"></i>
                        <p style="margin-top: 0.5rem;">Loading accepted poster collaterals...</p>
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
    <div id="new-poster-modal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 12px; max-width: 620px; width: 90%; max-height: 90vh; overflow-y: auto; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; margin-bottom: 1.25rem;">
                <h3 class="font-roboto" style="margin: 0; color: #0f172a; font-size: 1.35rem; font-weight: 700;">
                    <i class="ph ph-image-square" style="color: var(--primary);"></i> Request New Event Poster
                </h3>
                <button type="button" onclick="closeNewPosterModal()" style="background: none; border: none; font-size: 1.4rem; cursor: pointer; color: #64748b;">
                    <i class="ph ph-x"></i>
                </button>
            </div>
            <form id="new-poster-form" onsubmit="submitNewPoster(event)">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Chapter *</label>
                        <select id="np-chapter-id" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                            <option value="">Select Chapter...</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Target Event Date *</label>
                        <input type="date" id="np-event-date" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Event Title *</label>
                    <input type="text" id="np-event-title" required placeholder="e.g. Youth Camp 2026: Unshakeable Faith" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Venue / Location *</label>
                    <input type="text" id="np-venue" required placeholder="e.g. LSPU Los Baños Campus" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Theme / Visual Concept *</label>
                    <input type="text" id="np-theme" required placeholder="e.g. Modern geometric, spiritual revival, high-energy sports" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Poster Release Deadline (Due Date) *</label>
                    <input type="date" id="np-deadline" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                    <small style="color: #64748b; font-size: 0.8rem;">Ensure at least 5-7 days before event date for review and revisions.</small>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Additional Notes / Speaker Names / Required Logos</label>
                    <textarea id="np-notes" rows="3" placeholder="Specify any tagline, scripture references, or guest minister names..." style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;"></textarea>
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

    <!-- MODAL: VIEW POSTER DETAILS -->
    <div id="view-poster-modal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 12px; max-width: 580px; width: 90%; max-height: 90vh; overflow-y: auto; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; margin-bottom: 1.25rem;">
                <h3 class="font-roboto" style="margin: 0; color: #0f172a; font-size: 1.25rem; font-weight: 700;">
                    <i class="ph ph-info" style="color: var(--primary);"></i> Poster Request Details
                </h3>
                <button type="button" onclick="closeViewPosterModal()" style="background: none; border: none; font-size: 1.4rem; cursor: pointer; color: #64748b;">
                    <i class="ph ph-x"></i>
                </button>
            </div>
            <div id="view-poster-content" style="font-size: 0.95rem; line-height: 1.6;">
                <!-- Dynamically populated -->
            </div>
            <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
                <button type="button" class="btn btn-primary" onclick="closeViewPosterModal()">Close</button>
            </div>
        </div>
    </div>

    <!-- JavaScript Data Binding & Logic -->
    <script>
        let currentTab = 'accepted';
        let acceptedCache = [];
        let queueCache = [];

        document.addEventListener('DOMContentLoaded', () => {
            loadMetrics();
            loadChaptersDropdown();
            loadPosterData('accepted');
            loadPosterData('queue');
        });

        async function loadMetrics() {
            try {
                const res = await YTR_API.posterRequests.getMetrics();
                if (res.ok && res.data) {
                    const m = res.data;
                    document.getElementById('metric-active').textContent = m.total_active_events || 0;
                    document.getElementById('metric-pending').textContent = m.pending_poster_request || 0;
                    document.getElementById('metric-awaiting').textContent = m.awaiting_report || 0;
                    document.getElementById('metric-concluded').textContent = m.concluded_events || 0;

                    if (m.pending_poster_request > 0) {
                        const alert = document.getElementById('pending-alert');
                        alert.style.display = 'flex';
                        document.getElementById('alert-queue-count').textContent = m.pending_poster_request;
                        document.getElementById('pending-alert-text').textContent = `You have ${m.pending_poster_request} new poster application(s) awaiting review.`;
                    } else {
                        document.getElementById('pending-alert').style.display = 'none';
                    }
                }
            } catch (err) {
                console.error('Failed to load poster metrics:', err);
            }
        }

        async function loadChaptersDropdown() {
            try {
                const res = await YTR_API.chapters.getAll();
                if (res.ok && res.data) {
                    const sel = document.getElementById('np-chapter-id');
                    sel.innerHTML = '<option value="">Select Chapter...</option>';
                    res.data.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.id;
                        opt.textContent = `${c.chapter_name} (${c.chapter_code})`;
                        sel.appendChild(opt);
                    });
                }
            } catch (err) {
                console.error('Failed to load chapters:', err);
            }
        }

        async function loadPosterData(tab) {
            try {
                const res = await YTR_API.posterRequests.list(tab);
                if (res.ok && res.data) {
                    if (tab === 'accepted') {
                        acceptedCache = res.data;
                        document.getElementById('count-accepted').textContent = res.data.length;
                        renderAcceptedGrid(res.data);
                    } else {
                        queueCache = res.data;
                        document.getElementById('count-queue').textContent = res.data.length;
                        renderQueueGrid(res.data);
                    }
                }
            } catch (err) {
                console.error(`Failed to load ${tab} posters:`, err);
            }
        }

        function renderAcceptedGrid(items) {
            const grid = document.getElementById('accepted-grid');
            if (!items || items.length === 0) {
                grid.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: #fff; border-radius: 8px; border: 1px dashed #cbd5e1;">
                        <i class="ph ph-image-square" style="font-size: 2.5rem; color: #94a3b8;"></i>
                        <h4 style="color: #334155; margin: 0.5rem 0 0.25rem;">No Accepted Posters Yet</h4>
                        <p style="color: #64748b; font-size: 0.9rem;">Approved poster collateral will appear here once endorsed.</p>
                    </div>`;
                return;
            }

            grid.innerHTML = items.map(p => {
                const dueBadgeHtml = p.due_badge 
                    ? `<span class="${p.due_badge.cls}" style="margin-left: 0.4rem;"><i class="ph ph-clock"></i> ${p.due_badge.label}</span>` 
                    : '';
                const timerBadgeHtml = p.approval_timer 
                    ? `<span class="${p.approval_timer.cls}" style="margin-left: 0.4rem;"><i class="ph ph-timer"></i> ${p.approval_timer.label}</span>` 
                    : '';

                const statusLabel = p.status === 'completed' ? 'Event Accomplished' : 'Production In-Progress';
                const statusCls = p.status === 'completed' ? 'green' : 'blue';

                return `
                    <div class="poster-card type-accepted">
                        <div class="card-hero" style="position: relative;">
                            <img src="assets/images/poster-banner.jpg" alt="${escapeHtml(p.event_title)}" onerror="this.src='https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=600&q=80'">
                            <div style="position: absolute; top: 12px; left: 12px; display: flex; flex-wrap: wrap; gap: 4px;">
                                <span class="status-badge ${statusCls}"><i class="ph ph-check-circle"></i> ${statusLabel}</span>
                                ${timerBadgeHtml}
                                ${dueBadgeHtml}
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="event-title font-roboto" style="color: var(--primary); font-weight: 700; margin-bottom: 0.5rem;">${escapeHtml(p.event_title)}</h4>
                            <ul class="event-bullets">
                                <li><i class="ph ph-calendar"></i> ${escapeHtml(p.event_date || 'Date TBD')}</li>
                                <li><i class="ph ph-map-pin"></i> ${escapeHtml(p.venue || 'Venue TBD')}</li>
                                <li><i class="ph ph-buildings"></i> ${escapeHtml(p.chapter_name || 'Chapter')}</li>
                            </ul>
                            <p class="event-desc" style="color: #475569; font-size: 0.875rem;">
                                ${escapeHtml(p.theme_or_concept || p.review_note || 'Official event banner approved and queued for distribution.')}
                            </p>
                        </div>
                        <div class="card-footer dual-btn">
                            <button class="btn btn-outline" onclick="triggerDownload('${escapeHtml(p.event_title)}')">
                                <i class="ph ph-download-simple"></i> Download
                            </button>
                            <button class="btn btn-primary" onclick="viewPosterDetails(${p.id}, 'accepted')">
                                View Details
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
                        <h4 style="color: #334155; margin: 0.5rem 0 0.25rem;">All Clear in Queue</h4>
                        <p style="color: #64748b; font-size: 0.9rem;">There are no pending poster design submissions awaiting review.</p>
                    </div>`;
                return;
            }

            grid.innerHTML = items.map(p => {
                const dueBadgeHtml = p.due_badge 
                    ? `<span class="${p.due_badge.cls}" style="margin-left: 0.4rem;"><i class="ph ph-clock"></i> ${p.due_badge.label}</span>` 
                    : '';

                return `
                    <div class="poster-card type-queue" id="poster-card-${p.id}">
                        <div class="card-header flex-between" style="border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                            <div class="chapter-info" style="display: flex; align-items: center; gap: 0.75rem;">
                                <div class="avatar-placeholder"><i class="ph ph-users"></i></div>
                                <div class="info-group">
                                    <span class="label">Chapter name</span>
                                    <span class="value" style="font-weight: 700; color: #0f172a;">${escapeHtml(p.chapter_name || 'Chapter')}</span>
                                </div>
                            </div>
                            <div class="info-group text-right">
                                <span class="label">Requested Date</span>
                                <span class="value">${escapeHtml(p.submitted_at ? p.submitted_at.substring(0, 10) : 'Recent')}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="info-group mb-3" style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <span class="label">Requester / Contact:</span>
                                    <span class="value" style="font-weight: 600;">${escapeHtml(p.requester_name || 'Chapter Admin')}</span>
                                </div>
                                <div>
                                    ${dueBadgeHtml}
                                </div>
                            </div>
                            <div class="event-preview">
                                <img src="assets/images/poster-thumb.jpg" alt="Poster Thumbnail" class="poster-thumb" onerror="this.src='https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=150&q=80'">
                                <div class="preview-details">
                                    <div class="info-group mb-2">
                                        <span class="label">Event Title:</span>
                                        <span class="value font-roboto" style="font-weight: 700; color: var(--primary);">${escapeHtml(p.event_title)}</span>
                                    </div>
                                    <div class="info-group">
                                        <span class="label">Concept & Venue:</span>
                                        <span class="value text-sm">${escapeHtml(p.theme_or_concept || 'Theme not specified')}<br><small style="color: #64748b;">${escapeHtml(p.venue || '')}</small></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer dual-btn">
                            <button class="btn btn-outline-danger" onclick="reviewPoster(${p.id}, 'reject')">
                                <i class="ph ph-x"></i> Reject
                            </button>
                            <button class="btn btn-primary" onclick="reviewPoster(${p.id}, 'approve')">
                                <i class="ph ph-check"></i> Approve
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function switchTab(tabId, clickedBtn) {
            currentTab = tabId;
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            document.getElementById(tabId + '-grid').style.display = 'grid';
            if (clickedBtn) {
                clickedBtn.classList.add('active');
            }
        }

        function switchToQueueTab() {
            const btn = document.getElementById('tab-btn-queue');
            switchTab('queue', btn);
        }

        function handleSearch() {
            const q = document.getElementById('poster-search').value.toLowerCase().trim();
            if (currentTab === 'accepted') {
                const filtered = acceptedCache.filter(p => 
                    (p.event_title && p.event_title.toLowerCase().includes(q)) ||
                    (p.chapter_name && p.chapter_name.toLowerCase().includes(q)) ||
                    (p.venue && p.venue.toLowerCase().includes(q))
                );
                renderAcceptedGrid(filtered);
            } else {
                const filtered = queueCache.filter(p => 
                    (p.event_title && p.event_title.toLowerCase().includes(q)) ||
                    (p.chapter_name && p.chapter_name.toLowerCase().includes(q)) ||
                    (p.requester_name && p.requester_name.toLowerCase().includes(q))
                );
                renderQueueGrid(filtered);
            }
        }

        async function reviewPoster(id, action) {
            const isApprove = action === 'approve';
            const actionText = isApprove ? 'APPROVE this poster request for design production' : 'REJECT this poster request';
            const promptMsg = isApprove 
                ? 'Enter optional endorsement note or design instructions:' 
                : 'Enter reason for rejection:';
            
            const note = prompt(promptMsg, isApprove ? 'Approved by National Admin' : 'Incomplete details, please revise');
            if (note === null) return; // user cancelled

            try {
                let res;
                if (isApprove) {
                    res = await YTR_API.posterRequests.approve(id, note);
                } else {
                    res = await YTR_API.posterRequests.reject(id, note);
                }

                if (res.ok) {
                    alert(`Poster request successfully ${isApprove ? 'approved' : 'rejected'}!`);
                    loadMetrics();
                    loadPosterData('accepted');
                    loadPosterData('queue');
                } else {
                    alert('Error: ' + (res.message || 'Operation failed.'));
                }
            } catch (err) {
                console.error('Review error:', err);
                alert('Network error communicating with the Python API.');
            }
        }

        function openNewPosterModal() {
            document.getElementById('new-poster-modal').style.display = 'flex';
        }

        function closeNewPosterModal() {
            document.getElementById('new-poster-modal').style.display = 'none';
        }

        async function submitNewPoster(e) {
            e.preventDefault();
            const btn = document.getElementById('np-submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Submitting...';

            const payload = {
                chapter_id: parseInt(document.getElementById('np-chapter-id').value),
                event_title: document.getElementById('np-event-title').value.trim(),
                event_date: document.getElementById('np-event-date').value,
                venue: document.getElementById('np-venue').value.trim(),
                theme_or_concept: document.getElementById('np-theme').value.trim(),
                requested_deadline: document.getElementById('np-deadline').value,
                additional_notes: document.getElementById('np-notes').value.trim()
            };

            try {
                const res = await YTR_API.posterRequests.create(payload);
                if (res.ok) {
                    alert('Poster request submitted successfully to the national review queue!');
                    document.getElementById('new-poster-form').reset();
                    closeNewPosterModal();
                    loadMetrics();
                    loadPosterData('accepted');
                    loadPosterData('queue');
                    switchToQueueTab();
                } else {
                    alert('Failed to submit: ' + (res.message || 'Unknown error'));
                }
            } catch (err) {
                console.error('Submission error:', err);
                alert('Network error submitting to Python API.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="ph ph-paper-plane-tilt"></i> Submit Poster Request';
            }
        }

        function viewPosterDetails(id, tab) {
            const list = tab === 'accepted' ? acceptedCache : queueCache;
            const p = list.find(item => item.id === id);
            if (!p) return;

            const content = `
                <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border: 1px solid #e2e8f0;">
                    <h4 style="margin: 0 0 0.5rem; color: #0f172a; font-size: 1.15rem;">${escapeHtml(p.event_title)}</h4>
                    <span class="badge" style="background: #0284c7; color: #fff;">Status: ${p.status.toUpperCase()}</span>
                    ${p.approval_timer ? `<span class="badge timer" style="margin-left: 0.5rem;"><i class="ph ph-timer"></i> ${p.approval_timer.label}</span>` : ''}
                    ${p.due_badge ? `<span class="${p.due_badge.cls}" style="margin-left: 0.5rem;">${p.due_badge.label}</span>` : ''}
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                    <tr><td style="padding: 6px 0; color: #64748b; width: 140px;"><strong>Chapter:</strong></td><td>${escapeHtml(p.chapter_name || 'N/A')}</td></tr>
                    <tr><td style="padding: 6px 0; color: #64748b;"><strong>Event Date:</strong></td><td>${escapeHtml(p.event_date || 'N/A')}</td></tr>
                    <tr><td style="padding: 6px 0; color: #64748b;"><strong>Release Deadline:</strong></td><td><strong>${escapeHtml(p.requested_deadline || 'N/A')}</strong></td></tr>
                    <tr><td style="padding: 6px 0; color: #64748b;"><strong>Venue:</strong></td><td>${escapeHtml(p.venue || 'N/A')}</td></tr>
                    <tr><td style="padding: 6px 0; color: #64748b;"><strong>Concept / Theme:</strong></td><td>${escapeHtml(p.theme_or_concept || 'None')}</td></tr>
                    <tr><td style="padding: 6px 0; color: #64748b;"><strong>Requester:</strong></td><td>${escapeHtml(p.requester_name || 'N/A')}</td></tr>
                    <tr><td style="padding: 6px 0; color: #64748b;"><strong>Review Notes:</strong></td><td>${escapeHtml(p.review_note || 'None recorded')}</td></tr>
                </table>
            `;

            document.getElementById('view-poster-content').innerHTML = content;
            document.getElementById('view-poster-modal').style.display = 'flex';
        }

        function closeViewPosterModal() {
            document.getElementById('view-poster-modal').style.display = 'none';
        }

        function triggerDownload(title) {
            alert(`Initiating download for promotional poster bundle: "${title}". File packaging in progress.`);
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