<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chapter Event Proposals | YTR Portal</title>
    <link rel="stylesheet" href="assets/dashboard.css">
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="assets/api-client.js"></script>
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar Include for Chapter -->
        <?php include 'includes/sidebar_chapter.php'; ?>

        <div class="main-wrapper">
            <!-- Topbar Include -->
            <?php include 'includes/topbar.php'; ?>

            <div class="page-content">
                
                <div class="page-header flex-between" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.4rem;">
                            <h1 class="font-roboto" style="margin: 0; color: #0f172a; font-weight: 800;">Chapter Event Proposals</h1>
                            <span class="portal-indicator chapter"><i class="ph-fill ph-buildings"></i> Chapter Branch Portal</span>
                        </div>
                        <p style="color: var(--text-muted); margin: 0;">Author, submit, and track the evaluation status of event proposals submitted to the National Board.</p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.65rem;">
                        <button class="btn btn-primary" onclick="openProposalModal()">
                            <i class="ph ph-plus-circle"></i> Submit Event Proposal
                        </button>
                    </div>
                </div>

                <!-- 1. Metric Cards Row -->
                <div class="metrics-grid">
                    <div class="metric-card">
                        <div class="metric-icon blue"><i class="ph ph-files"></i></div>
                        <div>
                            <span class="metric-title font-roboto">Total Chapter Proposals</span>
                            <div class="metric-value font-roboto" id="metric-total">0</div>
                            <p class="metric-sub">All event applications authored by your chapter</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon orange"><i class="ph ph-hourglass-high"></i></div>
                        <div>
                            <span class="metric-title font-roboto">Pending National Review</span>
                            <div class="metric-value font-roboto" id="metric-pending">0</div>
                            <p class="metric-sub">Submissions currently awaiting national decision</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon green"><i class="ph ph-check-circle"></i></div>
                        <div>
                            <span class="metric-title font-roboto">Approved Events</span>
                            <div class="metric-value font-roboto" id="metric-approved">0</div>
                            <p class="metric-sub">Cleared for poster requests & execution</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon rose"><i class="ph ph-arrow-counter-clockwise"></i></div>
                        <div>
                            <span class="metric-title font-roboto">Needs Revision</span>
                            <div class="metric-value font-roboto" id="metric-revision">0</div>
                            <p class="metric-sub">Proposals returned with administrative notes</p>
                        </div>
                    </div>
                </div>

                <!-- 2. Tabs and Filters -->
                <div class="tab-controls-wrapper">
                    <div class="tabs-container font-roboto">
                        <button class="tab-btn active" id="tab-all" onclick="filterByStatus('all', this)"><i class="ph ph-list"></i> All Proposals</button>
                        <button class="tab-btn" id="tab-pending" onclick="filterByStatus('pending_approval', this)"><i class="ph ph-clock"></i> In Review</button>
                        <button class="tab-btn" id="tab-approved" onclick="filterByStatus('approved', this)"><i class="ph ph-check-circle"></i> Approved</button>
                        <button class="tab-btn" id="tab-revision" onclick="filterByStatus('draft', this)"><i class="ph ph-warning-circle"></i> Needs Revision</button>
                    </div>
                    <div class="filters">
                        <div class="search-bar" style="margin: 0; min-width: 240px;">
                            <i class="ph ph-magnifying-glass"></i>
                            <input type="text" id="proposal-search" placeholder="Search event title..." oninput="handleSearch()">
                        </div>
                    </div>
                </div>

                <!-- 3. Proposals Table Section -->
                <div class="table-section">
                    <div class="table-responsive">
                        <table>
                            <thead class="font-roboto">
                                <tr>
                                    <th>Event Details</th>
                                    <th>Schedule & Location</th>
                                    <th>Turnout Threshold</th>
                                    <th>Budget Allocation</th>
                                    <th>Status & Review Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="proposals-tbody">
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                        <i class="ph ph-spinner ph-spin" style="font-size: 1.5rem;"></i> Loading chapter proposals...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL: SUBMIT CHAPTER EVENT PROPOSAL -->
    <div id="proposal-modal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 12px; max-width: 650px; width: 90%; max-height: 90vh; overflow-y: auto; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; margin-bottom: 1.25rem;">
                <h3 class="font-roboto" style="margin: 0; color: #0f172a; font-size: 1.25rem; font-weight: 700;">
                    <i class="ph ph-file-plus" style="color: var(--primary);"></i> Submit Chapter Event Proposal
                </h3>
                <button type="button" onclick="closeProposalModal()" style="background: none; border: none; font-size: 1.4rem; cursor: pointer; color: #64748b;">
                    <i class="ph ph-x"></i>
                </button>
            </div>

            <form id="chapter-proposal-form" onsubmit="submitChapterProposal(event)">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Event Title *</label>
                    <input type="text" id="p-title" required placeholder="e.g. Metro Youth Leadership Summit 2026" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Event Type *</label>
                        <select id="p-type" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                            <option value="youth_camp">Youth Camp</option>
                            <option value="fellowship">Fellowship & Worship</option>
                            <option value="leadership_training">Leadership Training</option>
                            <option value="outreach">Community Outreach</option>
                            <option value="sports_fest">Youth Sportsfest</option>
                            <option value="anniversary">Chapter Anniversary</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Venue / Location *</label>
                        <input type="text" id="p-venue" required placeholder="e.g. LSPU Los Baños Campus" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Start Date & Time *</label>
                        <input type="datetime-local" id="p-start" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">End Date & Time *</label>
                        <input type="datetime-local" id="p-end" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
                            <i class="ph ph-users"></i> Target Turnout (Attendees) *
                        </label>
                        <input type="number" id="p-turnout" required min="10" placeholder="e.g. 150" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                        <small style="color: #64748b; font-size: 0.75rem;">Minimum attendance goal to satisfy performance threshold.</small>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
                            <i class="ph ph-currency-circle-dollar"></i> Proposed Budget (PHP) *
                        </label>
                        <input type="number" id="p-budget" required min="0" step="100" placeholder="e.g. 25000" style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                        <small style="color: #64748b; font-size: 0.75rem;">Standard chapter ceiling is PHP 25,000.</small>
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Target Due Date for Post-Event Report *</label>
                    <input type="date" id="p-due" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.35rem;">Event Concept, Objectives & Program *</label>
                    <textarea id="p-desc" rows="3" required placeholder="Describe the purpose, theme, speaker, and spiritual or community objectives of the event..." style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn btn-outline" onclick="closeProposalModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="p-submit-btn">
                        <i class="ph ph-paper-plane-tilt"></i> Submit Proposal to National
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let chapterId = parseInt(localStorage.getItem('ytr_chapter_id') || '1');
        let proposalsData = [];
        let currentStatusFilter = 'all';

        document.addEventListener('DOMContentLoaded', () => {
            loadChapterProposals();
        });

        async function loadChapterProposals() {
            try {
                // Fetch proposals specifically for this chapter
                const res = await YTR_API.proposals.list('all', chapterId);
                if (res.ok && res.data) {
                    proposalsData = res.data;
                    updateMetrics(proposalsData);
                    renderProposalsTable();
                }
            } catch (err) {
                console.error('Failed to load chapter proposals:', err);
            }
        }

        function updateMetrics(items) {
            document.getElementById('metric-total').textContent = items.length;
            document.getElementById('metric-pending').textContent = items.filter(p => p.status === 'pending_approval').length;
            document.getElementById('metric-approved').textContent = items.filter(p => p.status === 'approved').length;
            document.getElementById('metric-revision').textContent = items.filter(p => p.status === 'draft' || p.status === 'rejected').length;
        }

        function filterByStatus(status, btn) {
            currentStatusFilter = status;
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');
            renderProposalsTable();
        }

        function handleSearch() {
            renderProposalsTable();
        }

        function renderProposalsTable() {
            const tbody = document.getElementById('proposals-tbody');
            const search = (document.getElementById('proposal-search').value || '').toLowerCase().trim();

            let filtered = proposalsData.filter(p => {
                if (currentStatusFilter !== 'all' && p.status !== currentStatusFilter) return false;
                if (search && !((p.title || '').toLowerCase().includes(search) || (p.venue || '').toLowerCase().includes(search))) {
                    return false;
                }
                return true;
            });

            if (filtered.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748b;">
                            <i class="ph ph-file-dashed" style="font-size: 2rem; color: #94a3b8;"></i>
                            <p style="margin-top: 0.5rem;">No proposals found under this filter. Click <strong>Submit Event Proposal</strong> above to propose an event.</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = filtered.map(p => {
                let statusBadge = '';
                let timerHtml = p.approval_timer ? `<div style="margin-top: 4px;"><span class="${p.approval_timer.cls}"><i class="ph ph-timer"></i> ${p.approval_timer.label}</span></div>` : '';
                let dueHtml = p.due_badge ? `<span class="${p.due_badge.cls}" style="margin-left: 4px;"><i class="ph ph-clock"></i> ${p.due_badge.label}</span>` : '';

                if (p.status === 'approved') {
                    statusBadge = `<span class="badge approved"><i class="ph ph-check"></i> Approved</span>`;
                } else if (p.status === 'pending_approval') {
                    statusBadge = `<span class="badge pending"><i class="ph ph-clock"></i> In Review</span>`;
                } else if (p.status === 'draft') {
                    statusBadge = `<span class="badge draft"><i class="ph ph-arrow-counter-clockwise"></i> Revision Needed</span>`;
                } else {
                    statusBadge = `<span class="badge">${p.status}</span>`;
                }

                let actions = '';
                if (p.status === 'approved') {
                    actions = `
                        <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                            <a href="ChapterRequest.php" class="btn btn-outline" style="padding: 0.3rem 0.5rem; font-size: 0.75rem; text-decoration: none;" title="Request Poster">
                                <i class="ph ph-image"></i> Poster
                            </a>
                            <a href="ChapterReport.php?event_id=${p.id}" class="btn btn-primary" style="background: #16a34a; border-color: #15803d; padding: 0.3rem 0.5rem; font-size: 0.75rem; text-decoration: none;" title="Submit Report">
                                <i class="ph ph-file-arrow-up"></i> Report
                            </a>
                        </div>
                    `;
                } else {
                    actions = `<button class="btn btn-outline" style="padding: 0.3rem 0.5rem; font-size: 0.75rem;" onclick="alert('Proposal currently in national review queue. Decision notes will appear here.')">Details</button>`;
                }

                return `
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: #0f172a;">${escapeHtml(p.title)}</div>
                            <small style="color: #64748b; text-transform: uppercase;">${escapeHtml(p.event_type || 'Event')}</small>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; color: #1e293b;">${escapeHtml(p.start_datetime ? p.start_datetime.substring(0, 10) : 'Date TBD')}</div>
                            <small style="color: #64748b;">${escapeHtml(p.venue || 'Venue TBD')}</small>
                        </td>
                        <td>
                            <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-weight: 700;">
                                <i class="ph ph-users"></i> Target: ${p.target_turnout || 100}
                            </span>
                        </td>
                        <td>
                            <span style="font-weight: 600; color: #0f172a;">PHP ${(p.budget_amount || 0).toLocaleString()}</span>
                            ${dueHtml}
                        </td>
                        <td>
                            ${statusBadge}
                            ${timerHtml}
                            ${p.review_note ? `<p style="margin: 4px 0 0; font-size: 0.75rem; color: #64748b; font-style: italic;">"${escapeHtml(p.review_note)}"</p>` : ''}
                        </td>
                        <td>${actions}</td>
                    </tr>
                `;
            }).join('');
        }

        function openProposalModal() {
            document.getElementById('proposal-modal').style.display = 'flex';
        }

        function closeProposalModal() {
            document.getElementById('proposal-modal').style.display = 'none';
        }

        async function submitChapterProposal(e) {
            e.preventDefault();
            const btn = document.getElementById('p-submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Submitting...';

            const payload = {
                chapter_id: chapterId,
                title: document.getElementById('p-title').value.trim(),
                event_type: document.getElementById('p-type').value,
                venue: document.getElementById('p-venue').value.trim(),
                start_datetime: document.getElementById('p-start').value,
                end_datetime: document.getElementById('p-end').value,
                target_turnout: parseInt(document.getElementById('p-turnout').value),
                budget_amount: parseFloat(document.getElementById('p-budget').value),
                due_date: document.getElementById('p-due').value,
                description: document.getElementById('p-desc').value.trim()
            };

            try {
                const res = await YTR_API.proposals.create(payload);
                if (res.ok) {
                    alert('Proposal submitted successfully! It is now pending National Board evaluation.');
                    document.getElementById('chapter-proposal-form').reset();
                    closeProposalModal();
                    loadChapterProposals();
                } else {
                    alert('Submission error: ' + (res.message || 'Failed to submit proposal.'));
                }
            } catch (err) {
                console.error('Error submitting proposal:', err);
                alert('Network error connecting to Python API.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="ph ph-paper-plane-tilt"></i> Submit Proposal to National';
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
