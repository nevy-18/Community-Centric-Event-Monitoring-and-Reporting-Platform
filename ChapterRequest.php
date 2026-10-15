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
    <div id="new-poster-modal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 12px; max-width: 620px; width: 90%; max-height: 90vh; overflow-y: auto; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; margin-bottom: 1.25rem;">
                <h3 class="font-roboto" style="margin: 0; color: #0f172a; font-size: 1.25rem; font-weight: 700;">
                    <i class="ph ph-image-square" style="color: var(--primary);"></i> Request Chapter Event Poster
                </h3>
                <button type="button" onclick="closeNewPosterModal()" style="background: none; border: none; font-size: 1.4rem; cursor: pointer; color: #64748b;">
                    <i class="ph ph-x"></i>
                </button>
            </div>
            <form id="new-poster-form" onsubmit="submitNewPoster(event)">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Event Title *</label>
                    <input type="text" id="np-event-title" required placeholder="e.g. Youth Camp 2026: Unshakeable Faith" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Target Event Date *</label>
                        <input type="date" id="np-event-date" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Poster Release Deadline *</label>
                        <input type="date" id="np-deadline" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Venue / Location *</label>
                    <input type="text" id="np-venue" required placeholder="e.g. LSPU Los Baños Campus" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Theme / Visual Concept *</label>
                    <input type="text" id="np-theme" required placeholder="e.g. Modern dynamic youth aesthetic with high contrast neon accents" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Special Notes / Scripture Theme / Guest Speakers</label>
                    <textarea id="np-notes" rows="3" placeholder="Specify any theme verses, taglines, or chapter-specific requirements..." style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;"></textarea>
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

                return `
                    <div class="poster-card type-accepted">
                        <div class="card-hero" style="position: relative;">
                            <img src="assets/images/poster-banner.jpg" alt="${escapeHtml(p.event_title)}" onerror="this.src='https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=600&q=80'">
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
                            <button class="btn btn-primary" onclick="alert('Downloading promotional poster pack for: ${escapeHtml(p.event_title)}')">
                                <i class="ph ph-download-simple"></i> Download Artwork
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

            grid.innerHTML = items.map(p => `
                <div class="poster-card type-queue">
                    <div class="card-header flex-between" style="border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                        <span class="badge pending"><i class="ph ph-clock"></i> Awaiting National Review</span>
                        <span style="font-size: 0.8rem; color: #64748b;">Requested: ${escapeHtml(p.submitted_at ? p.submitted_at.substring(0, 10) : 'Recent')}</span>
                    </div>
                    <div class="card-body">
                        <h4 class="event-title font-roboto" style="color: #0f172a; font-weight: 700; margin: 0.5rem 0;">${escapeHtml(p.event_title)}</h4>
                        <div style="font-size: 0.875rem; color: #475569; margin-bottom: 0.5rem;">
                            <strong>Venue:</strong> ${escapeHtml(p.venue || 'N/A')}<br>
                            <strong>Target Date:</strong> ${escapeHtml(p.event_date || 'N/A')}<br>
                            <strong>Needed By:</strong> ${escapeHtml(p.requested_deadline || 'N/A')}
                        </div>
                        <p class="event-desc" style="font-size: 0.85rem; color: #64748b;">
                            <strong>Concept:</strong> ${escapeHtml(p.theme_or_concept || 'None')}
                        </p>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-outline" style="width: 100%;" onclick="alert('This request is currently in the national queue. National Media will assign a designer shortly.')">
                            <i class="ph ph-info"></i> Queue Status: In Review
                        </button>
                    </div>
                </div>
            `).join('');
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

        async function submitNewPoster(e) {
            e.preventDefault();
            const btn = document.getElementById('np-submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Submitting...';

            const payload = {
                chapter_id: chapterId,
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
                    alert('Poster request submitted successfully to National Media Team!');
                    document.getElementById('new-poster-form').reset();
                    closeNewPosterModal();
                    loadPosterData('accepted');
                    loadPosterData('queue');
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
