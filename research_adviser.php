<?php
session_start();
$adviserName = $_SESSION['user_name'] ?? 'Research Adviser';
$adviserEmail = $_SESSION['user_email'] ?? '';
$adviserId = (string) ($_SESSION['user_id'] ?? $_SESSION['username'] ?? $adviserEmail);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Research Adviser | PRISM</title>
    <script>try{if(localStorage.getItem('prismTheme')==='dark')document.documentElement.classList.add('dark-theme')}catch(_){}</script>
    <link rel="icon" href="assets/images/prismicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/role-portal.css">
    <link rel="stylesheet" href="assets/css/role-topnav.css">
    <link rel="stylesheet" href="assets/css/student-navigation.css">
    <link rel="stylesheet" href="assets/css/research-resources.css">
    <link rel="stylesheet" href="assets/css/ceu-footer.css">
    <link rel="stylesheet" href="assets/css/adviser-portal.css">
    <link rel="stylesheet" href="assets/css/support.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body data-role="Research Adviser" data-adviser-id="<?= htmlspecialchars($adviserId, ENT_QUOTES) ?>" data-name="<?= htmlspecialchars($adviserName, ENT_QUOTES) ?>" data-email="<?= htmlspecialchars($adviserEmail, ENT_QUOTES) ?>">
<nav class="portal-navbar" aria-label="Research adviser navigation">
    <button class="portal-brand" type="button" data-page="dashboard" aria-label="Adviser dashboard"><img src="assets/images/prismlogo1.png?v=2" alt="PRISM"></button>
    <ul class="portal-nav-links" id="adviserNav">
        <li class="active"><button type="button" data-page="dashboard">Dashboard</button></li>
        <li><button type="button" data-page="students">Assigned Students</button></li>
        <li><button type="button" data-page="reviews">Document Reviews</button></li>
        <li><details class="portal-nav-group"><summary>Research Resources <i class="fa-solid fa-chevron-down" aria-hidden="true"></i></summary><div class="portal-nav-submenu">
            <a href="https://ceu-ierb.wixsite.com/ierb" target="_blank" rel="noopener noreferrer">IERB Portal ↗ <span class="portal-sr-only">(opens in a new tab)</span></a>
            <button type="button" data-page="sdg">Sustainable Development Goals</button>
            <button type="button" data-page="agenda">Research Agenda</button>
        </div></details></li>
    </ul>
    <div class="portal-nav-right"><button class="portal-help" type="button" id="adviserSupportButton" title="Help &amp; Support" aria-label="Help &amp; Support"><i class="fa-regular fa-circle-question" aria-hidden="true"></i></button><button class="portal-notification" type="button" data-page="notifications" aria-label="Notifications" title="Notifications"><i class="fa-solid fa-bell" aria-hidden="true"></i><b class="nav-badge" id="adviserNotificationBadge" hidden>0</b></button><div class="portal-profile-menu"><button class="portal-profile-btn" type="button" aria-label="Open profile menu" aria-haspopup="true"><img id="adviserNavAvatar" src="assets/images/default-avatar.svg" alt="Profile picture"><span><strong id="adviserNavName"><?= htmlspecialchars($adviserName) ?></strong><small>Research Adviser</small></span><i class="fa-solid fa-chevron-down" aria-hidden="true"></i></button><div class="portal-profile-dropdown"><button type="button" data-page="profile"><i class="fa-solid fa-user" aria-hidden="true"></i>Profile</button><a href="login.php"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>Log out</a></div></div></div>
</nav>
<div class="container"><main class="main-content portal-main adviser-main">
    <header class="topbar"><div><h1 id="adviserPageTitle">Adviser Dashboard</h1><p id="adviserPageSubtitle">Manage reviews for your assigned students.</p></div><button type="button" class="theme-toggle" id="themeToggle" aria-label="Toggle theme"><i class="fa-solid fa-sun light-icon"></i><i class="fa-solid fa-moon dark-icon"></i></button></header>
    <section data-section="dashboard" class="adviser-page">
        <div class="welcome-card"><div><span>Research Adviser workspace</span><h2>Welcome back, <b id="adviserWelcomeName"><?= htmlspecialchars($adviserName) ?></b>!</h2><p class="dashboard-greeting-date"><i class="fa-regular fa-calendar" aria-hidden="true"></i><b id="adviserWelcomeDate"></b></p><p>Support your students through feedback, revisions, and preparation for RPMS submission.</p></div><button class="primary-btn" type="button" data-summary="pending">Review documents</button></div>
        <div class="adviser-summary" aria-label="Adviser summary">
            <button type="button" data-summary="students"><i class="fa-solid fa-user-group" aria-hidden="true"></i><strong id="assignedCount">0</strong><span>Assigned Students</span></button>
            <button type="button" data-summary="pending"><i class="fa-regular fa-clipboard" aria-hidden="true"></i><strong id="pendingCount">0</strong><span>Pending Review</span></button>
            <button type="button" data-summary="revision"><i class="fa-regular fa-pen-to-square" aria-hidden="true"></i><strong id="revisionCount">0</strong><span>For Revision</span></button>
            <button type="button" data-summary="approved"><i class="fa-regular fa-circle-check" aria-hidden="true"></i><strong id="approvedCount">0</strong><span>Approved</span><small>Approved for RPMS submission</small></button>
        </div>
        <article class="panel adviser-panel"><div class="panel-head"><h2>Documents to Review</h2><span class="adviser-chip pending" id="pendingBadge">0 pending</span></div><div class="adviser-table-wrap"><table><thead><tr><th>Student</th><th>Document</th><th>Submission date</th><th>Status</th><th><span class="portal-sr-only">Action</span></th></tr></thead><tbody id="pendingReviewRows"></tbody></table></div></article>
        <article class="panel adviser-panel"><div class="panel-head"><h2>Recent Resubmissions</h2></div><div class="adviser-table-wrap"><table><thead><tr><th>Student</th><th>Document</th><th>Resubmission date</th><th>Status</th><th>Action</th></tr></thead><tbody id="resubmissionRows"></tbody></table></div></article>
        <div class="adviser-dashboard-columns">
            <article class="panel adviser-panel"><div class="panel-head"><h2>Assigned Students</h2><button type="button" data-page="students">View All Students</button></div><div id="dashboardAssignedStudents"></div></article>
            <article class="panel adviser-panel"><div class="panel-head"><h2>Recent Review Activity</h2></div><div id="recentReviewActivity"></div></article>
        </div>
        <article class="panel adviser-panel"><div class="panel-head"><h2>Notifications</h2><button type="button" data-page="notifications">View All</button></div><div id="dashboardAdviserNotifications"></div></article>

    </section>
    <section data-section="students" class="adviser-page" hidden><article class="panel adviser-panel"><div class="panel-head"><h2>Assigned Students</h2><input id="studentSearch" type="search" placeholder="Search assigned students" aria-label="Search assigned students"></div><div class="adviser-table-wrap"><table><thead><tr><th>Student</th><th>Research title</th><th>Protocol code</th><th>Documents</th></tr></thead><tbody id="assignedStudentRows"></tbody></table></div></article></section>
    <section data-section="reviews" class="adviser-page" hidden><article class="panel adviser-panel"><div class="panel-head"><h2>Document Reviews</h2><select id="reviewFilter" aria-label="Filter document reviews"><option value="all">All documents</option><option value="pending">Pending reviews</option><option value="revision">Returned for revision</option><option value="approved">Approved for RPMS Submission</option></select></div><div class="adviser-table-wrap"><table><thead><tr><th>Student</th><th>Document</th><th>Submission date</th><th>Status</th><th><span class="portal-sr-only">Action</span></th></tr></thead><tbody id="allReviewRows"></tbody></table></div></article></section>
    <section data-section="sdg" class="adviser-page" hidden><figure class="resource-figure"><div class="resource-image-surface"><img src="assets/images/SDG.jpg" alt="The 17 United Nations Sustainable Development Goals" loading="lazy"></div><figcaption>United Nations · Sustainable Development Goals</figcaption></figure></section>
    <section data-section="agenda" class="adviser-page" hidden><figure class="resource-figure resource-agenda"><div class="resource-image-surface"><img src="assets/images/Research_Matrix.png" alt="CEU Malolos Research Agenda 2023–2028" loading="lazy"></div><figcaption>CEU Malolos · Research Agenda 2023–2028</figcaption></figure></section>
    <section data-section="notifications" class="adviser-page" hidden><article class="panel adviser-panel"><div class="panel-head"><h2>Notifications</h2><button type="button" id="adviserMarkAllRead">Mark all as read</button></div><div id="allAdviserNotifications"></div></article></section>
    <section data-section="profile" class="adviser-page" hidden><article class="panel adviser-panel"><h2>My Profile</h2><form id="adviserProfileForm" class="form-panel"><div class="profile-photo-control"><img id="adviserProfileAvatar" src="assets/images/default-avatar.svg" alt="Profile photo"><label class="profile-photo-button">Edit photo<input type="file" id="adviserAvatarFile" accept="image/png,image/jpeg,image/webp" hidden></label><button type="button" id="removeAdviserAvatar">Remove</button></div><label>Full name<input id="adviserProfileName" required maxlength="120"></label><label>Email<input id="adviserProfileEmail" type="email" readonly></label><p id="adviserProfileStatus" role="status"></p><button class="primary-btn" type="submit">Save profile</button></form></article></section>
    <?php require __DIR__ . '/ceu_footer.php'; ?>
</main></div>
<dialog class="adviser-review-dialog" id="reviewDialog" aria-labelledby="reviewTitle">
    <form method="dialog" class="adviser-dialog-heading"><h2 id="reviewTitle">Document Review</h2><button aria-label="Close document review">×</button></form>
    <p id="reviewStudent"></p><p id="reviewStatus"></p><div id="documentPreview"></div>
    <form id="reviewForm"><label for="adviserFeedback">Feedback</label><textarea id="adviserFeedback" rows="5" maxlength="2000" placeholder="Write feedback for the student"></textarea><p id="reviewMessage" role="status"></p><div class="adviser-review-buttons"><button type="submit" name="decision" value="Approved" class="primary-btn">Approve for RPMS Submission</button><button type="submit" name="decision" value="Revision Requested" class="adviser-secondary">Return for Revision</button></div><p class="adviser-local-note">Frontend preview: review changes are saved in this browser only.</p></form>
</dialog>
<script src="assets/js/adviser-portal.js"></script>
<dialog id="adviserSupportDialog" class="support-dialog" aria-labelledby="adviserSupportTitle">
    <div class="support-inbox-heading"><div><h2 id="adviserSupportTitle">Help &amp; Support</h2><p>Ask RPMS staff a question or share a concern.</p></div><button type="button" id="closeAdviserSupport" aria-label="Close Help &amp; Support">×</button></div>
    <form id="adviserSupportForm"><label>How can we help?<select id="adviserSupportType" required><option>Ask RPMS staff</option><option>Report a problem</option><option>Feedback or concern</option></select></label><label>Subject<input id="adviserSupportSubject" maxlength="120" required placeholder="Briefly describe your concern"></label><label>Message<textarea id="adviserSupportMessage" rows="5" maxlength="1000" required placeholder="Add details for RPMS staff"></textarea></label><p id="adviserSupportStatus" role="status"></p><div class="support-form-actions"><button type="button" id="cancelAdviserSupport">Cancel</button><button type="submit">Submit request</button></div><p class="support-storage-note">Requests are saved in this browser for the RPMS Help &amp; Support inbox.</p></form>
</dialog>
<script src="assets/js/support.js"></script>
</body></html>
