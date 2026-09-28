<?php
session_start();
$views = [
    'users' => ['User Management', 'Manage student records and research adviser accounts.'],
    'reviews' => ['Review Monitoring', 'Track adviser review before formal RPMS submission.'],
    'overrides' => ['Workflow Overrides', 'Administrative intervention in delayed adviser reviews.'],
    'activity' => ['Activity Logs', 'Recorded student and IERB monitoring activity in this browser.'],
    'support' => ['Help & Support', 'View feedback, questions, and concerns submitted by students and research advisers.'],
];
$view = is_string($_GET['view'] ?? null) && isset($views[$_GET['view']]) ? $_GET['view'] : 'users';
[$title, $description] = $views[$view];
$userKey = hash('sha256', $_SESSION['user_email'] ?? 'rpms@ceu.edu.ph');
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $title ?> | PRISM</title>
<script>try{if(localStorage.getItem('prismTheme')==='dark')document.documentElement.classList.add('dark-theme')}catch(_){}</script>
<link rel="icon" href="assets/images/prismicon.png">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/dashboard.css"><link rel="stylesheet" href="assets/css/dashboard-sidebar.css">
<link rel="stylesheet" href="assets/css/support.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head><body data-workspace-user="<?= $userKey ?>">
<div class="container"><?php require __DIR__ . '/admin_navigation.php'; ?>
<main class="main-content <?= $view === 'support' ? 'support-workspace' : '' ?>"><header class="topbar"><div><h1><?= $title ?></h1><p><?= $description ?></p></div></header>
<div class="workspace-cards">
<?php if ($view === 'users'): ?>
<section class="workspace-card"><h2>Student Records</h2><p>Maintain student information, research groups, and adviser assignments.</p><a href="admin_students.php">Manage students</a></section>
<section class="workspace-card"><h2>Research Advisers</h2><p>Maintain adviser records, assigned groups, and account status.</p><a href="admin_faculty.php">Manage research advisers</a></section>
<?php elseif ($view === 'support'): ?>
<section class="workspace-card support-inbox"><div class="support-inbox-heading"><div><h2>Feedback &amp; Concerns</h2><p id="supportRequestCount" role="status">Loading requests…</p></div><button type="button" id="refreshSupport">Refresh</button></div><div class="support-filters"><label>Search requests<input type="search" id="supportSearch" placeholder="Name, subject, or message"></label><label>Role<select id="supportRoleFilter"><option value="">All roles</option><option>Student</option><option>Research Adviser</option></select></label><label>Request type<select id="supportTypeFilter"><option value="">All types</option><option>Ask RPMS staff</option><option>Report a problem</option><option>Feedback or concern</option></select></label></div><div id="supportRequestList"></div><p class="support-storage-note">This frontend preview shows requests saved in this browser.</p></section>
<?php elseif ($view === 'activity'): ?>
<section class="workspace-card"><h2>Record history</h2><p id="activityEmpty">No recorded activity yet.</p><ol id="workspaceActivity" class="workspace-log"></ol></section>
<?php elseif ($view === 'reviews'): ?>
<section class="workspace-card"><h2>Adviser review workflow</h2><p>Students submit documents for their assigned adviser's feedback, approval, or return for revision before formal RPMS submission.</p><p>A consolidated adviser-review queue is not yet connected. Use the available records to check assignments and submitted documents.</p><a href="admin_students.php?view=assignments">Student assignments</a><a href="documents.php">Document submissions</a></section>
<?php else: ?>
<section class="workspace-card"><h2>Controlled administrative intervention</h2><p>Workflow overrides allow authorized RPMS personnel to address delayed adviser review. They must record the reason and responsible person, and do not constitute IERB approval.</p><p>The authorized override workflow and its audit trail are not yet connected. No submission can be overridden from this page.</p><a href="documents.php">View document submissions</a></section>
<?php endif; ?>
</div></main></div>
<script src="assets/js/dashboard-sidebar.js"></script>
<script src="assets/js/admin-workspace.js"></script>
<?php if ($view === 'support'): ?><script src="assets/js/support.js"></script><?php endif; ?>
</body></html>
