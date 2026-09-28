<?php
session_start();

$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'CEU RPMS';
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'rpms@ceu.edu.ph';
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'RPMS Administrator';
$profile_img = 'assets/images/default-avatar.svg';

date_default_timezone_set('Asia/Manila');
$current_hour = (int)date('H');

if ($current_hour >= 5 && $current_hour < 12) {
    $greeting = "Good morning";
} elseif ($current_hour >= 12 && $current_hour < 18) {
    $greeting = "Good afternoon";
} else {
    $greeting = "Good evening";
}

$current_date_formatted = date('l, F j, Y');


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM | Dashboard</title>
    <script>
        try {
            if (localStorage.getItem('prismTheme') === 'dark') {
                document.documentElement.classList.add('dark-theme');
            }
        } catch (_) {}
    </script>
    <link rel="icon" type="image/png" href="assets/images/prismicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/dashboard-sidebar.css">
    <link rel="stylesheet" href="assets/css/dashboard-overview.css">
    <link rel="stylesheet" href="assets/css/ceu-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body class="dashboard-page" data-reminder-user="<?php echo htmlspecialchars(hash('sha256', $user_email), ENT_QUOTES, 'UTF-8'); ?>">

<div class="container">
    <!-- SIDEBAR WITH EASY-TO-UNDERSTAND LABELS -->
    <?php require __DIR__ . '/admin_navigation.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <header class="topbar">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Search groups, ethics stage, or document title...">
            </div>

            <div class="top-controls">
                <div class="quick-actions-menu-wrap">
                    <button type="button" class="quick-actions-toggle" id="quickActionsToggle" title="Quick actions" aria-label="Quick actions" aria-expanded="false">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </button>
                    <div class="quick-actions-dropdown" id="quickActionsDropdown">
                        <div class="quick-dropdown-heading">
                            <div><span>AI workspace</span><strong>Quick Actions</strong></div>
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                        </div>
                        <button type="button" class="quick-action" onclick="openReportModal()">
                            <span class="quick-action-icon report"><i class="fa-solid fa-file-pdf"></i></span>
                            <span class="quick-action-copy"><strong>Generate AI Report</strong><small>Create a consolidated RPMS PDF report</small></span>
                            <i class="fa-solid fa-chevron-right action-arrow"></i>
                        </button>
                        <button type="button" class="quick-action" onclick="openSummaryModal('uploaded document')">
                            <span class="quick-action-icon summary"><i class="fa-solid fa-file-lines"></i></span>
                            <span class="quick-action-copy"><strong>Summarize Document</strong><small>Open the document summary workspace</small></span>
                            <i class="fa-solid fa-chevron-right action-arrow"></i>
                        </button>
                        <div class="recent-ai-reports" id="recentAiReports">
                            <div class="recent-reports-title"><strong>Recent AI Reports</strong><i class="fa-solid fa-clock-rotate-left"></i></div>
                            <ul id="recentAiReportList"></ul>
                        </div>
                    </div>
                </div>

                <div class="theme-toggle" id="themeToggle" title="Toggle Light/Dark Theme">
                    <i class="fa-solid fa-sun light-icon"></i>
                    <i class="fa-solid fa-moon dark-icon"></i>
                </div>

                <div class="notification-menu-wrap">
                    <button type="button" class="notification-icon" id="notificationToggle" title="Notifications and confirmations" aria-label="Notifications and confirmations" aria-expanded="false">
                        <i class="fa-solid fa-bell"></i>
                    </button>
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-dropdown-heading">
                            <div><span>Status center</span><strong>Notifications &amp; Confirmations</strong></div>
                            <i class="fa-solid fa-envelope-circle-check"></i>
                        </div>
                        <div class="notification-empty">
                            <i class="fa-regular fa-bell-slash"></i>
                            <p>No status updates, email confirmations, or automated notifications to display.</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <section class="welcome-header">
            <h1><?php echo $greeting; ?>, <span><?php echo htmlspecialchars($user_name); ?></span>! 👋</h1>
            <p class="current-date"><i class="fa-regular fa-calendar"></i> <?php echo $current_date_formatted; ?></p>
        </section>

        <!-- AI SUMMARY BANNER -->
        <section class="ai-summary-banner" aria-labelledby="aiSummaryTitle">
            <div class="ai-summary-content">
                <div class="ai-badge">
                    <i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i> AI Workload Insights
                </div>
                <h2 id="aiSummaryTitle">No workload insights available</h2>
                <p>Insights will appear here after research data has been added.</p>
            </div>
            <button type="button" class="ai-action-btn" onclick="openReportModal()"><i class="fa-solid fa-file-pdf" aria-hidden="true"></i> Generate AI PDF Report</button>
        </section>

        <div class="overview-heading">
            <div><h2>Dashboard overview</h2></div>
            <a class="overview-link" href="reports.php"><i class="fa-regular fa-file-lines" aria-hidden="true"></i> Reports &amp; summaries</a>
        </div>
        <p id="overviewDataStatus" class="overview-data-status" role="status" hidden></p>
        <section class="cards overview-cards" aria-label="Dashboard overview">
            <a class="card" href="ierbprog.php">
                <i class="fa-regular fa-folder-open" aria-hidden="true"></i>
                <strong class="metric-value" id="activeIerbMetric">0</strong>
                <p>Active IERB Records</p><small>Records not yet completed</small>
            </a>
            <a class="card" href="documents.php">
                <i class="fa-regular fa-file-lines" aria-hidden="true"></i>
                <strong class="metric-value" id="rpmsPendingMetric">&mdash;</strong>
                <p>RPMS Pending</p><small>Documents awaiting RPMS review</small>
            </a>
            <a class="card" href="#adviserReviewStatus">
                <i class="fa-regular fa-clock" aria-hidden="true"></i>
                <strong class="metric-value">&mdash;</strong>
                <p>Adviser Pending</p><small>Review data not connected</small>
            </a>
            <a class="card" href="#upcomingDeadlines">
                <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                <strong class="metric-value" id="upcomingDeadlinesMetric">0</strong>
                <p>Upcoming Deadlines</p><small>Official deadlines &middot; next 7 days</small>
            </a>
        </section>

        <section class="content overview-content">
            <div class="left-column">
                <section class="content-box overview-panel" aria-labelledby="ierbOverviewTitle">
                    <div class="overview-panel-heading">
                        <div><h2 id="ierbOverviewTitle">IERB monitoring overview</h2></div>
                        <a class="overview-link" href="ierbprog.php">View all records <span aria-hidden="true">&rarr;</span></a>
                    </div>
                    <div class="overview-status-grid" aria-label="Active record statuses">
                        <div><span>In progress</span><strong id="ierbInProgressCount">0</strong></div>
                        <div><span>Pending requirements</span><strong id="ierbPendingCount">0</strong></div>
                        <div><span>Requires follow-up</span><strong id="ierbFollowupCount">0</strong></div>
                    </div>
                    <div class="overview-table-scroll">
                        <table class="data-table overview-table">
                            <thead><tr><th scope="col">Protocol Code</th><th scope="col">Stage</th><th scope="col">Requirements</th><th scope="col">Status</th></tr></thead>
                            <tbody id="ierbMonitorBody"></tbody>
                        </table>
                    </div>
                    <p class="overview-caption" id="ierbPreviewCount"></p>
                </section>

                <section class="content-box overview-panel" id="adviserReviewStatus" aria-labelledby="adviserReviewTitle">
                    <div class="overview-panel-heading">
                        <div><h2 id="adviserReviewTitle">Adviser review status</h2></div>
                        <a class="overview-link" href="admin_workspace.php?view=reviews">View reviews <span aria-hidden="true">&rarr;</span></a>
                    </div>
                    <div class="overview-status-grid">
                        <div><span>Pending review</span><strong aria-label="Not available">&mdash;</strong></div>
                        <div><span>Returned for revision</span><strong aria-label="Not available">&mdash;</strong></div>
                        <div><span>Approved for submission</span><strong aria-label="Not available">&mdash;</strong></div>
                    </div>
                    <p class="overview-note">Adviser review data is not connected yet. Adviser approval allows formal RPMS submission; it does not indicate IERB approval.</p>
                </section>

                <section class="content-box overview-panel" aria-labelledby="recentActivityTitle">
                    <div class="overview-panel-heading">
                        <div><h2 id="recentActivityTitle">Recent activity</h2></div>
                        <a class="overview-link" href="admin_workspace.php?view=activity">View record history <span aria-hidden="true">&rarr;</span></a>
                    </div>
                    <ol class="overview-activity" id="recentActivityList"></ol>
                </section>
            </div>

            <div class="right-column">
                <section class="content-box overview-panel" aria-labelledby="attentionTitle">
                    <div class="overview-panel-heading"><div><h2 id="attentionTitle">Requires attention</h2></div><span class="overview-count" id="attentionCount">0</span></div>
                    <ul class="overview-action-list" id="attentionList"></ul>
                </section>
                <section class="content-box overview-panel" id="upcomingDeadlines" aria-labelledby="deadlinesTitle">
                    <div class="overview-panel-heading"><div><h2 id="deadlinesTitle">Upcoming deadlines</h2></div></div>
                    <ul class="overview-action-list" id="officialDeadlineList"></ul>
                </section>
                <!-- INTERACTIVE CALENDAR WITH MONTH AND YEAR VIEWS -->
                <div class="content-box calendar-box overview-panel">
                    <div class="calendar-top-bar">
                        <div class="cal-nav">
                            <button class="cal-nav-btn" id="prevBtn" title="Previous"><i class="fa-solid fa-chevron-left"></i></button>
                            <span class="month-year" id="calendarLabel">July 2026</span>
                            <button class="cal-nav-btn" id="nextBtn" title="Next"><i class="fa-solid fa-chevron-right"></i></button>
                        </div>
                        <div class="cal-view-selector">
                            <button class="view-btn active" id="viewMonth">Month</button>
                            <button class="view-btn" id="viewYear">Year</button>
                        </div>
                    </div>

                    <!-- Month View Area -->
                    <div id="standardCalendarView">
                        <div class="calendar-grid" id="calendarGridHeader">
                            <div class="day-name">Sun</div>
                            <div class="day-name">Mon</div>
                            <div class="day-name">Tue</div>
                            <div class="day-name">Wed</div>
                            <div class="day-name">Thu</div>
                            <div class="day-name">Fri</div>
                            <div class="day-name">Sat</div>
                        </div>
                        <div class="calendar-grid" id="calendarDays"></div>
                    </div>

                    <!-- Year View Area -->
                    <div id="yearCalendarView" class="year-grid"></div>

                    <!-- REMINDERS AND FOLLOW-UPS -->
                    <div class="reminders-section">
                        <div class="reminders-header">
                            <h4><i class="fa-solid fa-calendar-check"></i> Personal reminders</h4>
                            <a class="add-btn" href="calendar.php" title="Add reminder" aria-label="Add reminder"><i class="fa-solid fa-plus"></i></a>
                        </div>
                        <ul class="reminder-list" id="dashboardReminderList"></ul>
                    </div>
                </div>

            </div>
        </section>
        <?php require __DIR__ . '/ceu_footer.php'; ?>
    </main>
</div>

<!-- MODAL: DASHBOARD DAY TASKS -->
<div class="modal-overlay" id="dashboardDayModal">
    <div class="modal-card dashboard-day-modal" role="dialog" aria-modal="true" aria-labelledby="dashboardDayTitle">
        <div class="day-modal-heading">
            <div><span>Selected date</span><h3 id="dashboardDayTitle">Tasks &amp; Notes</h3></div>
            <button type="button" class="day-modal-close" onclick="closeDashboardDay()" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="dashboardTaskForm" autocomplete="off">
            <label for="dashboardTaskTitle">Task reminder</label>
            <input type="text" id="dashboardTaskTitle" maxlength="120" placeholder="What needs to be done?" required>
            <div class="day-form-row">
                <div><label for="dashboardTaskTime">Time <span>(optional)</span></label><input type="time" id="dashboardTaskTime"></div>
            </div>
            <label for="dashboardTaskNotes">Notes <span>(optional)</span></label>
            <textarea id="dashboardTaskNotes" rows="3" maxlength="500" placeholder="Add helpful details..."></textarea>
            <div class="modal-actions">
                <button type="button" class="btn-secondary-sm" onclick="closeDashboardDay()">Close</button>
                <button type="submit" class="small-btn"><i class="fa-solid fa-plus"></i> Add task</button>
            </div>
        </form>
        <div class="dashboard-day-tasks" id="dashboardDayTasks"></div>
    </div>
</div>

<!-- MODAL: AI DOCUMENT SUMMARY -->
<div class="modal-overlay" id="summaryModal">
    <div class="modal-card">
        <h3><i class="fa-solid fa-wand-magic-sparkles"></i> AI Key Takeaways Summary</h3>
        <p id="summaryModalText">No document summary is available.</p>
        <div class="ai-disclaimer">
            <i class="fa-solid fa-circle-info"></i> Note: AI summaries are generated for quick administrative reference and must be verified by authorized RPMS personnel.
        </div>
        <div class="modal-actions">
            <button class="btn-secondary-sm" onclick="closeSummaryModal()">Close</button>
        </div>
    </div>
</div>

<!-- MODAL: AI PDF REPORT OPTIONS -->
<div class="modal-overlay" id="reportModal">
    <div class="modal-card">
        <h3><i class="fa-solid fa-file-pdf"></i> Export AI Consolidated PDF Report</h3>
        <p>Select parameters for AI PDF generation:</p>
        <div class="report-options">
            <label><input type="checkbox" checked> Include Delayed Submissions Only</label><br>
            <label><input type="checkbox" checked> Include Stage Distribution Metrics</label><br>
            <label><input type="checkbox" checked> Include AI Administrative Insights</label>
        </div>
        <div class="modal-actions">
            <button class="btn-secondary-sm" onclick="closeReportModal()">Cancel</button>
            <button class="small-btn" onclick="generateAIReport()"><i class="fa-solid fa-download"></i> Download PDF</button>
        </div>
    </div>
</div>

<script src="assets/js/dashboard-sidebar.js"></script>
<script src="assets/js/dashboard-overview.js"></script>
<script>
    // Profile Dropdown Toggle
    const profileToggle = document.getElementById('profileToggle');
    const profileMenu = document.getElementById('profileMenu');

    profileToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        profileMenu.classList.toggle('show');
        profileToggle.setAttribute('aria-expanded', profileMenu.classList.contains('show'));
    });

    document.addEventListener('click', () => {
        profileMenu.classList.remove('show');
        profileToggle.setAttribute('aria-expanded', 'false');
    });

    const quickActionsToggle = document.getElementById('quickActionsToggle');
    const quickActionsDropdown = document.getElementById('quickActionsDropdown');

    function closeQuickActions() {
        quickActionsDropdown.classList.remove('show');
        quickActionsToggle.setAttribute('aria-expanded', 'false');
    }

    quickActionsToggle.addEventListener('click', event => {
        event.stopPropagation();
        closeNotificationStatus();
        const isOpen = quickActionsDropdown.classList.toggle('show');
        quickActionsToggle.setAttribute('aria-expanded', String(isOpen));
    });
    quickActionsDropdown.addEventListener('click', event => event.stopPropagation());
    document.addEventListener('click', closeQuickActions);
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') closeQuickActions();
    });

    const notificationToggle = document.getElementById('notificationToggle');
    const notificationDropdown = document.getElementById('notificationDropdown');

    function closeNotificationStatus() {
        notificationDropdown.classList.remove('show');
        notificationToggle.setAttribute('aria-expanded', 'false');
    }

    notificationToggle.addEventListener('click', event => {
        event.stopPropagation();
        closeQuickActions();
        const isOpen = notificationDropdown.classList.toggle('show');
        notificationToggle.setAttribute('aria-expanded', String(isOpen));
    });
    notificationDropdown.addEventListener('click', event => event.stopPropagation());
    document.addEventListener('click', closeNotificationStatus);
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') closeNotificationStatus();
    });

    // Dark/Light Mode Switcher
    const themeToggle = document.getElementById('themeToggle');
    themeToggle.addEventListener('click', () => {
        const isDark = document.documentElement.classList.toggle('dark-theme');
        try {
            localStorage.setItem('prismTheme', isDark ? 'dark' : 'light');
        } catch (_) {}
    });

    const studentStorageKey = `prismStudents:${document.body.dataset.reminderUser || 'default'}`;
    let monitorStudents = [];
    let monitorDocuments = null;
    let recordsUnavailable = false;
    let documentsUnavailable = false;

    function loadMonitorStudents() {
        try {
            const stored = JSON.parse(localStorage.getItem(studentStorageKey) || '[]');
            if (!Array.isArray(stored)) throw new Error('Invalid records');
            monitorStudents = stored.filter(record => record && typeof record === 'object');
            recordsUnavailable = false;
        } catch (_) {
            monitorStudents = [];
            recordsUnavailable = true;
        }
    }

    function renderIerbMonitor() {
        PrismDashboard.render(monitorStudents, monitorDocuments, { recordsUnavailable, documentsUnavailable });
    }

    async function loadDashboardDocuments() {
        try {
            const response = await fetch('documents_api.php?action=list', { cache: 'no-store' });
            const result = await response.json();
            if (!response.ok || !result.ok || !Array.isArray(result.documents)) throw new Error('Documents unavailable');
            monitorDocuments = result.documents.filter(record => record && typeof record === 'object');
            documentsUnavailable = false;
        } catch (_) {
            monitorDocuments = null;
            documentsUnavailable = true;
        }
        renderIerbMonitor();
    }

    // Modal Control Handlers
    function openSummaryModal(targetName) {
        closeQuickActions();
        document.getElementById('summaryModalText').innerText = `No AI summary is available for [${targetName}] yet.`;
        document.getElementById('summaryModal').style.display = 'flex';
    }
    function closeSummaryModal() {
        document.getElementById('summaryModal').style.display = 'none';
    }
    function openReportModal() {
        closeQuickActions();
        document.getElementById('reportModal').style.display = 'flex';
    }
    function closeReportModal() {
        document.getElementById('reportModal').style.display = 'none';
    }

    const reportHistoryKey = `prismAiReports:${document.body.dataset.reminderUser || 'default'}`;

    function getReportHistory() {
        try {
            const history = JSON.parse(localStorage.getItem(reportHistoryKey));
            return Array.isArray(history) ? history : [];
        } catch (_) {
            return [];
        }
    }

    function renderReportHistory() {
        const list = document.getElementById('recentAiReportList');
        list.replaceChildren();
        const history = getReportHistory().slice(0, 3);
        if (!history.length) {
            const empty = document.createElement('li');
            empty.className = 'recent-report-empty';
            empty.textContent = 'No AI reports generated yet.';
            list.appendChild(empty);
            return;
        }

        history.forEach(report => {
            const item = document.createElement('li');
            const icon = document.createElement('i');
            icon.className = 'fa-regular fa-file-pdf';
            const copy = document.createElement('span');
            const title = document.createElement('strong');
            title.textContent = report.title;
            const date = document.createElement('small');
            date.textContent = new Date(report.createdAt).toLocaleString('en-PH', {
                month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit'
            });
            copy.append(title, date);
            item.append(icon, copy);
            list.appendChild(item);
        });
    }

    function generateAIReport() {
        const history = getReportHistory();
        history.unshift({ title: 'AI Consolidated RPMS Report', createdAt: new Date().toISOString() });
        try {
            localStorage.setItem(reportHistoryKey, JSON.stringify(history.slice(0, 10)));
        } catch (_) {}
        closeReportModal();
        renderReportHistory();
        alert('Generating PDF Report...');
    }

    /* CALENDAR ENGINE */
    const reminderStorageKey = `prismReminders:${document.body.dataset.reminderUser || 'default'}`;
    let dashboardReminders = {};

    function loadDashboardReminders() {
        try {
            const stored = JSON.parse(localStorage.getItem(reminderStorageKey));
            dashboardReminders = stored && typeof stored === 'object' ? stored : {};
        } catch (_) {
            dashboardReminders = {};
        }
    }

    const dateKey = (year, month, day) =>
        `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

    let currentDate = new Date();
    let currentView = 'month';

    const monthNames = ["January", "February", "March", "April", "May", "June", 
                        "July", "August", "September", "October", "November", "December"];

    const calendarLabel = document.getElementById('calendarLabel');
    const calendarDays = document.getElementById('calendarDays');
    const standardView = document.getElementById('standardCalendarView');
    const yearView = document.getElementById('yearCalendarView');
    const dashboardReminderList = document.getElementById('dashboardReminderList');

    const viewMonthBtn = document.getElementById('viewMonth');
    const viewYearBtn = document.getElementById('viewYear');

    document.getElementById('prevBtn').addEventListener('click', () => navigateCalendar(-1));
    document.getElementById('nextBtn').addEventListener('click', () => navigateCalendar(1));

    viewMonthBtn.addEventListener('click', () => setView('month'));
    viewYearBtn.addEventListener('click', () => setView('year'));

    function setView(view) {
        currentView = view;
        [viewMonthBtn, viewYearBtn].forEach(b => b.classList.remove('active'));
        if(view === 'month') viewMonthBtn.classList.add('active');
        if(view === 'year') viewYearBtn.classList.add('active');
        renderCalendar();
    }

    function navigateCalendar(direction) {
        if (currentView === 'month') {
            currentDate.setMonth(currentDate.getMonth() + direction);
        } else if (currentView === 'year') {
            currentDate.setFullYear(currentDate.getFullYear() + direction);
        }
        renderCalendar();
    }

    function renderCalendar() {
        if (currentView === 'year') {
            standardView.style.display = 'none';
            yearView.style.display = 'grid';
            calendarLabel.textContent = currentDate.getFullYear();
            renderYearView();
        } else {
            standardView.style.display = 'block';
            yearView.style.display = 'none';
            calendarLabel.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
            renderMonthView();
        }
    }

    function renderMonthView() {
        calendarDays.innerHTML = '';
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        const firstDayIndex = new Date(year, month, 1).getDay();
        const lastDate = new Date(year, month + 1, 0).getDate();
        const prevMonthLastDate = new Date(year, month, 0).getDate();

        for (let x = firstDayIndex; x > 0; x--) {
            calendarDays.innerHTML += `<div class="day text-muted">${prevMonthLastDate - x + 1}</div>`;
        }

        const today = new Date();
        for (let i = 1; i <= lastDate; i++) {
            let isToday = (i === today.getDate() && month === today.getMonth() && year === today.getFullYear()) ? 'active-day' : '';
            const key = dateKey(year, month, i);
            const hasEvent = Array.isArray(dashboardReminders[key]) && dashboardReminders[key].length ? 'has-event' : '';
            calendarDays.innerHTML += `<button type="button" class="day ${isToday} ${hasEvent}" onclick="openDashboardDay('${key}')" title="View tasks for ${key}">${i}</button>`;
        }

        const totalSlots = calendarDays.children.length;
        const remainingSlots = (Math.ceil(totalSlots / 7) * 7) - totalSlots;
        for (let j = 1; j <= remainingSlots; j++) {
            calendarDays.innerHTML += `<div class="day text-muted">${j}</div>`;
        }
    }

    function renderYearView() {
        yearView.innerHTML = '';
        const year = currentDate.getFullYear();
        const today = new Date();

        for (let m = 0; m < 12; m++) {
            let monthCard = document.createElement('div');
            monthCard.className = 'year-month-card';

            let monthHTML = `<span class="month-title">${monthNames[m]}</span>`;
            monthHTML += `<div class="mini-calendar-grid">`;
            
            const dayHeaders = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
            dayHeaders.forEach(day => {
                monthHTML += `<div class="mini-day-name">${day}</div>`;
            });

            const firstDayIndex = new Date(year, m, 1).getDay();
            const lastDate = new Date(year, m + 1, 0).getDate();
            const prevMonthLastDate = new Date(year, m, 0).getDate();

            for (let x = firstDayIndex; x > 0; x--) {
                monthHTML += `<div class="mini-day text-muted">${prevMonthLastDate - x + 1}</div>`;
            }

            for (let d = 1; d <= lastDate; d++) {
                let isToday = (d === today.getDate() && m === today.getMonth() && year === today.getFullYear()) ? 'active-day' : '';
                monthHTML += `<div class="mini-day ${isToday}">${d}</div>`;
            }

            const totalRendered = firstDayIndex + lastDate;
            const remainingSlots = (Math.ceil(totalRendered / 7) * 7) - totalRendered;
            for (let j = 1; j <= remainingSlots; j++) {
                monthHTML += `<div class="mini-day text-muted">${j}</div>`;
            }

            monthHTML += `</div>`;
            monthCard.innerHTML = monthHTML;
            
            monthCard.addEventListener('click', () => {
                currentDate.setMonth(m);
                setView('month');
            });

            yearView.appendChild(monthCard);
        }
    }

    function renderDashboardReminders() {
        dashboardReminderList.replaceChildren();
        const now = new Date();
        const todayKey = dateKey(now.getFullYear(), now.getMonth(), now.getDate());
        const upcoming = Object.entries(dashboardReminders)
            .filter(([key, tasks]) => key >= todayKey && Array.isArray(tasks))
            .flatMap(([key, tasks]) => tasks.map(task => ({ ...task, date: key })))
            .sort((a, b) => `${a.date} ${a.time || '99:99'}`.localeCompare(`${b.date} ${b.time || '99:99'}`))
            .slice(0, 6);

        if (!upcoming.length) {
            const empty = document.createElement('li');
            empty.className = 'reminder-empty';
            empty.textContent = 'No upcoming task reminders.';
            dashboardReminderList.appendChild(empty);
            return;
        }

        upcoming.forEach(reminder => {
            const item = document.createElement('li');
            const link = document.createElement('button');
            link.type = 'button';
            link.className = 'reminder-item';
            link.addEventListener('click', () => openDashboardDay(reminder.date));

            const details = document.createElement('div');
            details.className = 'reminder-details';
            const title = document.createElement('strong');
            title.textContent = reminder.title;
            const meta = document.createElement('span');
            const parsedDate = new Date(`${reminder.date}T00:00:00`);
            const formattedDate = parsedDate.toLocaleDateString('en-PH', { month: 'short', day: 'numeric' });
            meta.innerHTML = `<i class="fa-regular fa-clock"></i> ${formattedDate}${reminder.time ? ` &bull; ${reminder.time}` : ''}`;
            details.append(title, meta);

            const tag = document.createElement('span');
            tag.className = 'reminder-tag';
            tag.textContent = reminder.date === todayKey ? 'Today' : 'Upcoming';
            link.append(details, tag);
            item.appendChild(link);
            dashboardReminderList.appendChild(item);
        });
    }

    let dashboardSelectedDate = '';
    const dashboardDayModal = document.getElementById('dashboardDayModal');
    const dashboardDayTitle = document.getElementById('dashboardDayTitle');
    const dashboardTaskForm = document.getElementById('dashboardTaskForm');
    const dashboardTaskTitle = document.getElementById('dashboardTaskTitle');
    const dashboardTaskTime = document.getElementById('dashboardTaskTime');
    const dashboardTaskNotes = document.getElementById('dashboardTaskNotes');
    const dashboardDayTasks = document.getElementById('dashboardDayTasks');

    function openDashboardDay(key) {
        dashboardSelectedDate = key;
        dashboardDayTitle.textContent = new Date(`${key}T00:00:00`).toLocaleDateString('en-PH', {
            weekday: 'long', month: 'long', day: 'numeric', year: 'numeric'
        });
        dashboardTaskForm.reset();
        renderDashboardDayTasks();
        dashboardDayModal.style.display = 'flex';
        dashboardTaskTitle.focus();
    }

    function closeDashboardDay() {
        dashboardDayModal.style.display = 'none';
        dashboardSelectedDate = '';
        dashboardTaskForm.reset();
    }

    function saveDashboardReminders() {
        try {
            localStorage.setItem(reminderStorageKey, JSON.stringify(dashboardReminders));
        } catch (_) {}
    }

    function renderDashboardDayTasks() {
        dashboardDayTasks.replaceChildren();
        const tasks = Array.isArray(dashboardReminders[dashboardSelectedDate])
            ? [...dashboardReminders[dashboardSelectedDate]].sort((a, b) => (a.time || '99:99').localeCompare(b.time || '99:99'))
            : [];

        if (!tasks.length) {
            const empty = document.createElement('p');
            empty.className = 'dashboard-day-empty';
            empty.textContent = 'No tasks or notes for this date yet.';
            dashboardDayTasks.appendChild(empty);
            return;
        }

        tasks.forEach(task => {
            const item = document.createElement('article');
            const content = document.createElement('div');
            const title = document.createElement('strong');
            title.textContent = task.title;
            content.appendChild(title);
            if (task.time) {
                const time = document.createElement('span');
                time.innerHTML = `<i class="fa-regular fa-clock"></i> ${task.time}`;
                content.appendChild(time);
            }
            if (task.notes) {
                const notes = document.createElement('p');
                notes.textContent = task.notes;
                content.appendChild(notes);
            }
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.title = 'Delete task';
            remove.setAttribute('aria-label', 'Delete task');
            remove.innerHTML = '<i class="fa-solid fa-trash"></i>';
            remove.addEventListener('click', () => deleteDashboardTask(task.id));
            item.append(content, remove);
            dashboardDayTasks.appendChild(item);
        });
    }

    function deleteDashboardTask(id) {
        if (!confirm('Delete this reminder?')) return;
        dashboardReminders[dashboardSelectedDate] = dashboardReminders[dashboardSelectedDate].filter(task => task.id !== id);
        if (!dashboardReminders[dashboardSelectedDate].length) delete dashboardReminders[dashboardSelectedDate];
        saveDashboardReminders();
        renderDashboardDayTasks();
        renderCalendar();
        renderDashboardReminders();
    }

    dashboardTaskForm.addEventListener('submit', event => {
        event.preventDefault();
        const title = dashboardTaskTitle.value.trim();
        if (!title || !dashboardSelectedDate) return;
        const tasks = Array.isArray(dashboardReminders[dashboardSelectedDate]) ? dashboardReminders[dashboardSelectedDate] : [];
        tasks.push({
            id: `${Date.now()}-${Math.random().toString(16).slice(2)}`,
            title,
            time: dashboardTaskTime.value,
            notes: dashboardTaskNotes.value.trim()
        });
        dashboardReminders[dashboardSelectedDate] = tasks;
        saveDashboardReminders();
        dashboardTaskForm.reset();
        renderDashboardDayTasks();
        renderCalendar();
        renderDashboardReminders();
        dashboardTaskTitle.focus();
    });

    dashboardDayModal.addEventListener('click', event => {
        if (event.target === dashboardDayModal) closeDashboardDay();
    });

    loadDashboardReminders();
    loadMonitorStudents();
    renderIerbMonitor();
    renderCalendar();
    renderDashboardReminders();
    renderReportHistory();

    window.addEventListener('pageshow', () => {
        loadDashboardDocuments();
        loadDashboardReminders();
        loadMonitorStudents();
        renderIerbMonitor();
        renderCalendar();
        renderDashboardReminders();
    });
    window.addEventListener('storage', event => {
        if (event.key === reminderStorageKey) {
            loadDashboardReminders();
            renderCalendar();
            renderDashboardReminders();
        }
        if (event.key === studentStorageKey || event.key === null) {
            loadMonitorStudents();
            renderIerbMonitor();
        }
    });
</script>

</body>
</html>
