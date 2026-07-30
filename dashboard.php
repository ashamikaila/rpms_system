<?php
session_start();

$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'CEU RPMS';
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'rpms@ceu.edu.ph';
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'RPMS Administrator';
$profile_img = 'assets/images/ceu_logo1.jpg';

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

$total_researchers = 0;
$pending_ierb = 0;
$approved_ethics = 0;
$delayed_submissions = 0;

$ierb_records = [];
$reminders = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM Dashboard | CEU RPMS Workload Assistant</title>
    <link rel="icon" type="image/jpeg" href="assets/images/ceu_logo1.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>

<div class="container">
    <!-- SIDEBAR WITH EASY-TO-UNDERSTAND LABELS -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="assets/images/ceu_logo2.jpg" alt="CEU Logo" class="sidebar-brand-logo">
            <h3>PRISM Assistant</h3>
        </div>

        <ul class="nav-links">
            <li class="active">
                <a href="dashboard.php">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fa-solid fa-user-graduate"></i>
                    <span>Students</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fa-solid fa-file-signature"></i>
                    <span>IERB Progress</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fa-solid fa-folder-open"></i>
                    <span>Documents</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li>
                <a href="calendar.php">
                    <i class="fa-solid fa-calendar-days"></i>
                    <span>Calendar</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-bottom">
            <div class="profile-dropdown-wrapper">
                <div class="sidebar-profile" id="profileToggle">
                    <img src="<?php echo htmlspecialchars($profile_img); ?>" alt="Profile Picture">
                    <div class="profile-info">
                        <h4><?php echo htmlspecialchars($user_name); ?></h4>
                        <p><?php echo htmlspecialchars($user_role); ?></p>
                    </div>
                    <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
                </div>

                <div class="profile-menu" id="profileMenu">
                    <a href="#"><i class="fa-solid fa-user-gear"></i> Profile</a>
                    <a href="#"><i class="fa-solid fa-sliders"></i> Activity Logs</a>
                    <hr>
                    <a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <header class="topbar">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Search groups, ethics stage, or document title...">
            </div>

            <div class="top-controls">
                <div class="theme-toggle" id="themeToggle" title="Toggle Light/Dark Theme">
                    <i class="fa-solid fa-sun light-icon"></i>
                    <i class="fa-solid fa-moon dark-icon"></i>
                </div>

                <div class="notification-icon" title="Automated Follow-ups Sent">
                    <i class="fa-solid fa-bell"></i>
                    <span class="badge"></span>
                </div>
            </div>
        </header>

        <section class="welcome-header">
            <h1><?php echo $greeting; ?>, <span><?php echo htmlspecialchars($user_name); ?></span>! 👋</h1>
            <p class="current-date"><i class="fa-regular fa-calendar"></i> <?php echo $current_date_formatted; ?></p>
        </section>

        <!-- AI SUMMARY BANNER -->
        <section class="ai-summary-banner">
            <div class="ai-summary-content">
                <div class="ai-badge">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> AI Workload Insights
                </div>
                <h2>No workload insights available</h2>
                <p>Insights will appear here after research data has been added.</p>
            </div>
            <button class="ai-action-btn" onclick="openReportModal()"><i class="fa-solid fa-file-pdf"></i> Generate AI PDF Report</button>
        </section>

        <!-- STATISTICAL METRICS CARDS -->
        <section class="cards">
            <div class="card">
                <i class="fa-solid fa-user-graduate"></i>
                <h1><?php echo $total_researchers; ?></h1>
                <p>Total Student Researchers</p>
            </div>
            <div class="card">
                <i class="fa-solid fa-hourglass-half"></i>
                <h1><?php echo $pending_ierb; ?></h1>
                <p>Pending Ethics Review</p>
            </div>
            <div class="card">
                <i class="fa-solid fa-circle-check"></i>
                <h1><?php echo $approved_ethics; ?></h1>
                <p>IERB Approved</p>
            </div>
            <div class="card card-alert">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <h1><?php echo $delayed_submissions; ?></h1>
                <p>Delayed Submissions</p>
            </div>
        </section>

        <!-- CONTENT GRID -->
        <section class="content">
            <div class="left-column">
                
                <!-- STAGE PIPELINE FUNNEL WIDGET -->
                <div class="pipeline-card">
                    <div class="pipeline-title">
                        <i class="fa-solid fa-filter"></i> Research Stage Distribution
                    </div>
                    <div class="pipeline-steps">
                        <div class="step-item">
                            <span>Initial Submission</span>
                            <strong>0 Groups</strong>
                        </div>
                        <div class="step-item">
                            <span>Ethics Review</span>
                            <strong>0 Groups</strong>
                        </div>
                        <div class="step-item delayed">
                            <span>Revision Phase</span>
                            <strong>0 Delayed</strong>
                        </div>
                        <div class="step-item approved">
                            <span>Board Approved</span>
                            <strong>0 Groups</strong>
                        </div>
                    </div>
                </div>

                <!-- REAL-TIME IERB TRACKING TABLE -->
                <div class="content-box">
                    <div class="table-header">
                        <h3><i class="fa-solid fa-list-check"></i> IERB Progress Monitor</h3>
                        <div class="header-actions">
                            <select class="table-filter" id="courseFilter" aria-label="Filter by course">
                                <option value="">All courses</option>
                            </select>
                            <select class="table-filter" id="progressSort" aria-label="Sort by group progress">
                                <option value="default">Sort: Group progress</option>
                                <option value="high-to-low">Progress: High to low</option>
                                <option value="low-to-high">Progress: Low to high</option>
                            </select>
                            <button class="btn-secondary-sm"><i class="fa-solid fa-file-csv"></i> Import CSV</button>
                            <button class="small-btn"><i class="fa-solid fa-plus"></i> Add Entry</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Group ID</th>
                                <th>Course</th>
                                <th>Research Title</th>
                                <th>Stage</th>
                                <th>Pending Requirements</th>
                                <th>Overall Progress</th>
                                <th>Status & Email Log</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ierb_records as $row): ?>
                                <tr data-course="<?php echo htmlspecialchars($row['course'] ?? ''); ?>" data-progress="<?php echo htmlspecialchars($row['overall_progress'] ?? ''); ?>">
                                    <td><strong><?php echo $row['group_id']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['course'] ?? '—'); ?></td>
                                    <td>
                                        <div class="title-cell">
                                            <span><?php echo $row['title']; ?></span>
                                            <small>Lead: <?php echo $row['lead']; ?></small>
                                        </div>
                                    </td>
                                    <td><span class="stage-tag"><?php echo $row['stage']; ?></span></td>
                                    <td><?php echo htmlspecialchars($row['pending_requirements'] ?? '—'); ?></td>
                                    <td><span class="progress-value"><?php echo htmlspecialchars($row['overall_progress'] ?? '—'); ?></span></td>
                                    <td>
                                        <span class="status-badge <?php echo strtolower($row['status']); ?>">
                                            <?php echo $row['status']; ?>
                                        </span>
                                        <span class="email-status-text"><i class="fa-regular fa-paper-plane"></i> <?php echo $row['last_email']; ?></span>
                                    </td>
                                    <td>
                                        <button class="icon-btn" title="Send Follow-up Alert" onclick="alert('Automated follow-up sent to student lead!')"><i class="fa-solid fa-paper-plane"></i></button>
                                        <button class="icon-btn" title="View Quick Summary" onclick="openSummaryModal('<?php echo $row['group_id']; ?>')"><i class="fa-solid fa-file-lines"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($ierb_records)): ?>
                                <tr><td colspan="8">No IERB records available.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- DOCUMENT REPOSITORY SUMMARY -->
                <div class="content-box">
                    <h3><i class="fa-solid fa-folder-tree"></i> Recent Repository Uploads</h3>
                    <div class="repo-list">
                        <p>No repository uploads available.</p>
                    </div>
                </div>
            </div>

            <div class="right-column">
                <!-- INTERACTIVE CALENDAR WITH MONTH AND YEAR VIEWS -->
                <div class="content-box calendar-box">
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
                            <h4><i class="fa-solid fa-calendar-check"></i> Tasks & Deadlines</h4>
                            <button class="add-btn" title="Add Alert"><i class="fa-solid fa-plus"></i></button>
                        </div>
                        <ul class="reminder-list">
                            <?php foreach($reminders as $item): ?>
                                <li class="reminder-item">
                                    <div class="reminder-details">
                                        <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                        <span><i class="fa-regular fa-clock"></i> <?php echo htmlspecialchars($item['date']); ?> • <?php echo htmlspecialchars($item['time']); ?></span>
                                    </div>
                                    <span class="reminder-tag <?php echo strtolower(str_replace(' ', '-', $item['tag'])); ?>"><?php echo htmlspecialchars($item['tag']); ?></span>
                                </li>
                            <?php endforeach; ?>
                            <?php if (empty($reminders)): ?>
                                <li class="reminder-item">No scheduled alerts.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <div class="content-box">
                    <h3><i class="fa-solid fa-envelope-circle-check"></i> Notification & Confirmation Status</h3>
                    <p class="empty-state">No status updates, email confirmations, or automated notifications to display.</p>
                </div>
            </div>
        </section>
    </main>
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
            <button class="small-btn" onclick="alert('Generating PDF Report...'); closeReportModal();"><i class="fa-solid fa-download"></i> Download PDF</button>
        </div>
    </div>
</div>

<script>
    // Profile Dropdown Toggle
    const profileToggle = document.getElementById('profileToggle');
    const profileMenu = document.getElementById('profileMenu');

    profileToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        profileMenu.classList.toggle('show');
    });

    document.addEventListener('click', () => {
        profileMenu.classList.remove('show');
    });

    // Dark/Light Mode Switcher
    const themeToggle = document.getElementById('themeToggle');
    themeToggle.addEventListener('click', () => {
        document.body.classList.toggle('dark-theme');
    });

    // Course filter and group-progress sorter
    const courseFilter = document.getElementById('courseFilter');
    const progressSort = document.getElementById('progressSort');
    const progressTableBody = document.querySelector('.data-table tbody');

    if (courseFilter && progressSort && progressTableBody) {
        const progressRows = () => Array.from(progressTableBody.querySelectorAll('tr[data-course]'));
        const courses = [...new Set(progressRows().map(row => row.dataset.course).filter(Boolean))].sort();

        courses.forEach(course => {
            const option = document.createElement('option');
            option.value = course;
            option.textContent = course;
            courseFilter.appendChild(option);
        });

        const progressNumber = value => parseFloat(String(value).replace('%', '')) || 0;

        function filterAndSortProgress() {
            const selectedCourse = courseFilter.value;
            const rows = progressRows();

            rows.forEach(row => {
                row.style.display = !selectedCourse || row.dataset.course === selectedCourse ? '' : 'none';
            });

            if (progressSort.value !== 'default') {
                const direction = progressSort.value === 'high-to-low' ? -1 : 1;
                rows
                    .sort((a, b) => direction * (progressNumber(a.dataset.progress) - progressNumber(b.dataset.progress)))
                    .forEach(row => progressTableBody.appendChild(row));
            }
        }

        courseFilter.addEventListener('change', filterAndSortProgress);
        progressSort.addEventListener('change', filterAndSortProgress);
    }

    // Modal Control Handlers
    function openSummaryModal(targetName) {
        document.getElementById('summaryModalText').innerText = `No AI summary is available for [${targetName}] yet.`;
        document.getElementById('summaryModal').style.display = 'flex';
    }
    function closeSummaryModal() {
        document.getElementById('summaryModal').style.display = 'none';
    }
    function openReportModal() {
        document.getElementById('reportModal').style.display = 'flex';
    }
    function closeReportModal() {
        document.getElementById('reportModal').style.display = 'none';
    }

    /* CALENDAR ENGINE */
    let currentDate = new Date(2026, 6, 28);
    let currentView = 'month';

    const monthNames = ["January", "February", "March", "April", "May", "June", 
                        "July", "August", "September", "October", "November", "December"];

    const calendarLabel = document.getElementById('calendarLabel');
    const calendarDays = document.getElementById('calendarDays');
    const standardView = document.getElementById('standardCalendarView');
    const yearView = document.getElementById('yearCalendarView');

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
            let hasEvent = (i === 28 || i === 29 || i === 30) && month === 6 && year === 2026 ? 'has-event' : '';
            calendarDays.innerHTML += `<div class="day ${isToday} ${hasEvent}">${i}</div>`;
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

    renderCalendar();
</script>

</body>
</html>
