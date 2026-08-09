<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$portalRole = isset($portalRole) && in_array($portalRole, ['Student', 'Faculty'], true) ? $portalRole : 'Student';
$userName = $_SESSION['user_name'] ?? $portalRole . ' User';
$userEmail = $_SESSION['user_email'] ?? strtolower($portalRole) . '@ceu.edu.ph';
$userId = $_SESSION['user_id'] ?? $_SESSION['username'] ?? $userEmail;
$profileImg = 'assets/images/ceu_logo1.jpg';
$identityKey = hash('sha256', $portalRole . '|' . $userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($portalRole); ?> Portal | PRISM</title>
    <script>try{if(localStorage.getItem('prismTheme')==='dark')document.documentElement.classList.add('dark-theme')}catch(_){}</script>
    <link rel="icon" type="image/png" href="assets/images/prismicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/role-portal.css">
    <link rel="stylesheet" href="assets/css/calendar.css">
    <link rel="stylesheet" href="assets/css/role-topnav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body data-portal-key="<?php echo htmlspecialchars($identityKey, ENT_QUOTES); ?>" data-role="<?php echo htmlspecialchars($portalRole, ENT_QUOTES); ?>" data-name="<?php echo htmlspecialchars($userName, ENT_QUOTES); ?>" data-email="<?php echo htmlspecialchars($userEmail, ENT_QUOTES); ?>">
<nav class="portal-navbar" aria-label="Portal navigation">
    <button class="portal-brand" type="button" data-go="dashboard" aria-label="PRISM dashboard"><img src="assets/images/prismlogo1.png?v=2" alt="PRISM Logo"></button>
    <ul class="portal-nav-links" id="portalNav">
        <li class="active"><button data-page="dashboard">Dashboard</button></li>
        <li><button data-page="progress">IERB Progress</button></li>
        <li><button data-page="submit">Document Submission</button></li>
        <li><button data-page="documents">My Documents</button></li>
        <li><button data-page="calendar">Calendar</button></li>
    </ul>
    <div class="portal-nav-right">
        <button class="portal-notification" type="button" data-go="notifications" title="Notifications" aria-label="Notifications"><i class="fa-solid fa-bell"></i><b class="nav-badge" id="navBadge">0</b></button>
        <div class="portal-profile-menu">
            <button class="portal-profile-btn" type="button" aria-haspopup="true"><img id="navProfileImage" src="<?php echo htmlspecialchars($profileImg, ENT_QUOTES); ?>" alt="Profile picture"><span><strong id="sideName"><?php echo htmlspecialchars($userName); ?></strong><small><?php echo htmlspecialchars($portalRole); ?></small></span><i class="fa-solid fa-chevron-down"></i></button>
            <div class="portal-profile-dropdown">
                <button type="button" data-go="profile"><i class="fa-solid fa-user"></i> Profile</button>
                <a href="login.php"><i class="fa-solid fa-right-from-bracket"></i> Log out</a>
            </div>
        </div>
    </div>
</nav>
<div class="container">

    <main class="main-content portal-main">
        <header class="topbar"><div><h1 id="pageTitle">Dashboard</h1><p id="pageSubtitle">Your research and IERB progress at a glance.</p></div><button class="theme-toggle" id="themeToggle" title="Toggle theme"><i class="fa-solid fa-sun light-icon"></i><i class="fa-solid fa-moon dark-icon"></i></button></header>

        <section class="portal-page active" data-section="dashboard">
            <div class="welcome-card"><div><span><?php echo htmlspecialchars($portalRole); ?> workspace</span><h2>Welcome back, <b id="welcomeName"><?php echo htmlspecialchars($userName); ?></b></h2><p id="researchTitle">Add your research details from Profile to complete your dashboard.</p></div><button class="primary-btn" data-go="submit"><i class="fa-solid fa-upload"></i> Submit document</button></div>
            <div class="stat-grid"><article><i class="fa-solid fa-users"></i><span>Research Group</span><strong id="groupValue">Not assigned</strong></article><article><i class="fa-solid fa-shield-halved"></i><span>Current IERB Status</span><strong id="statusValue">For Initial Review</strong></article><article><i class="fa-solid fa-chart-simple"></i><span>Overall Progress</span><strong id="progressValue">20%</strong></article><article><i class="fa-solid fa-clipboard-list"></i><span>Pending Requirements</span><strong id="pendingValue">3</strong></article></div>
            <div class="portal-two-col"><article class="panel"><div class="panel-head"><h2>Recent submissions</h2><button data-go="documents">View all</button></div><div id="recentSubmissions"></div></article><article class="panel"><div class="panel-head"><h2>Deadlines &amp; notifications</h2><button data-go="notifications">View all</button></div><div id="dashboardNotices"></div></article></div>
        </section>

        <section class="portal-page" data-section="progress">
            <div class="progress-summary panel"><div><span>Current official stage</span><h2 id="currentStage">Stage 1 — Initial Submission</h2><p>Official status is managed by RPMS/IERB and cannot be changed from this portal.</p></div><div class="progress-ring" id="progressRing"><b>20%</b></div></div>
            <div class="stage-list" id="stageList"></div>
            <div class="portal-two-col requirement-panels"><article class="panel"><div class="panel-head"><h2>Completed requirements</h2></div><div id="completedRequirements"></div></article><article class="panel"><div class="panel-head"><h2>Pending requirements</h2></div><div id="pendingRequirements"></div></article></div>
            <article class="panel"><div class="panel-head"><h2>Progress history &amp; remarks</h2></div><div id="progressHistory"></div></article>
        </section>

        <section class="portal-page" data-section="submit">
            <article class="panel form-panel"><h2>Submit a document</h2><p>Upload a requirement for review. PDF, DOC, DOCX, PNG, or JPG up to 1.5 MB.</p><form id="submissionForm"><div class="form-grid"><label>Document type<select id="documentType" required><option value="">Select a document type</option><option>Research Protocol</option><option>Informed Consent Form</option><option>Data Collection Instrument</option><option>Revision Letter</option><option>Ethics Training Certificate</option><option>Other Supporting Document</option></select></label><label>File<input id="documentFile" type="file" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg" required></label><label class="wide">Notes for reviewer<textarea id="documentNotes" rows="4" maxlength="500" placeholder="Optional context about this submission"></textarea></label></div><button class="primary-btn" type="submit"><i class="fa-solid fa-paper-plane"></i> Submit for review</button></form></article>
        </section>

        <section class="portal-page" data-section="documents">
            <article class="panel"><div class="panel-head"><div><h2>My Documents</h2><p>Track, preview, download, and resubmit your files.</p></div><select id="documentFilter"><option value="">All statuses</option><option>Submitted</option><option>Under Review</option><option>Revision Requested</option><option>Approved</option></select></div><div class="document-table-wrap"><table><thead><tr><th>Document</th><th>Type</th><th>Submitted</th><th>Status</th><th>Remarks</th><th>Actions</th></tr></thead><tbody id="documentRows"></tbody></table></div></article>
        </section>

        <section class="portal-page" data-section="notifications">
            <article class="panel"><div class="panel-head"><div><h2>Notifications</h2><p>Submission confirmations, updates, reminders, and follow-ups.</p></div><button id="markAllRead">Mark all as read</button></div><div id="notificationList"></div></article>
        </section>

        <section class="portal-page" data-section="calendar">
            <div class="calendar-page portal-calendar">
                <div class="calendar-page-actions"><div><h2>Research Calendar</h2><p>Track official deadlines and manage your personal reminders.</p></div><button class="today-button" id="todayButton">Today</button></div>
                <div class="calendar-layout">
                    <div class="full-calendar-card"><div class="calendar-toolbar"><button class="calendar-nav" id="previousMonth" aria-label="Previous month"><i class="fa-solid fa-chevron-left"></i></button><h2 id="monthLabel"></h2><button class="calendar-nav" id="nextMonth" aria-label="Next month"><i class="fa-solid fa-chevron-right"></i></button></div><div class="weekday-row" aria-hidden="true"><span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span></div><div class="month-grid" id="monthGrid"></div></div>
                    <aside class="reminder-panel"><div class="panel-title"><div><span class="eyebrow">Selected date</span><h2 id="selectedDateLabel"></h2></div><span class="task-count" id="taskCount">0 events</span></div><form id="reminderForm" autocomplete="off"><input type="hidden" id="editingId"><label for="taskTitle">Personal reminder</label><input id="taskTitle" maxlength="120" placeholder="What needs to be done?" required><label for="taskTime">Time <span>(optional)</span></label><input id="taskTime" type="time"><label for="taskNotes">Notes <span>(optional)</span></label><textarea id="taskNotes" rows="3" maxlength="500" placeholder="Add helpful details..."></textarea><div class="form-actions"><button type="button" class="cancel-edit" id="cancelEdit" hidden>Cancel</button><button type="submit" class="save-task"><i class="fa-solid fa-plus"></i><span id="saveLabel">Add reminder</span></button></div></form><div class="task-list" id="taskList"></div></aside>
                </div>
            </div>
        </section>

        <section class="portal-page" data-section="profile">
            <div class="portal-two-col"><article class="panel form-panel"><h2>Personal information</h2><p>Your role and account email are shown for reference.</p><form id="profileForm"><div class="profile-photo-control"><img id="profileImagePreview" src="<?php echo htmlspecialchars($profileImg, ENT_QUOTES); ?>" alt="Profile preview"><label class="profile-photo-button"><i class="fa-solid fa-camera"></i> Change profile picture<input id="profileImageInput" type="file" accept="image/png,image/jpeg,image/webp" hidden></label><button id="removeProfileImage" type="button">Remove</button></div><div class="form-grid"><label>Full name<input id="profileName" required maxlength="120"></label><label>Email<input id="profileEmail" type="email" readonly></label><label>Role<input id="profileRole" readonly></label><label>Student / Employee ID<input id="profileId" maxlength="40"></label><label class="wide">Research title<input id="profileResearch" maxlength="250"></label><label class="wide">Research group / members<input id="profileGroup" maxlength="250" placeholder="Names or group ID"></label><label>Contact number<input id="profilePhone" maxlength="30"></label></div><button class="primary-btn" type="submit">Save permitted information</button></form></article><article class="panel form-panel"><h2>Change password</h2><p>Use at least 8 characters.</p><form id="passwordForm"><label>Current password<input id="currentPassword" type="password" required></label><label>New password<input id="newPassword" type="password" minlength="8" required></label><label>Confirm new password<input id="confirmPassword" type="password" minlength="8" required></label><button class="primary-btn" type="submit">Change password</button></form></article></div>
        </section>
    </main>
</div>
<div class="toast" id="toast" role="status"></div>
<script src="assets/js/role-portal.js"></script>
<script src="assets/js/role-calendar.js"></script>
</body>
</html>
