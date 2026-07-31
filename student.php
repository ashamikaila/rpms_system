<?php
session_start();

$user_name = $_SESSION['user_name'] ?? 'CEU RPMS';
$user_email = $_SESSION['user_email'] ?? 'rpms@ceu.edu.ph';
$user_role = $_SESSION['user_role'] ?? 'RPMS Administrator';
$profile_img = 'assets/images/ceu_logo1.jpg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students | CEU RPMS Workload Assistant</title>
    <script>
        try {
            if (localStorage.getItem('prismTheme') === 'dark') document.documentElement.classList.add('dark-theme');
        } catch (_) {}
    </script>
    <link rel="icon" type="image/png" href="assets/images/prismicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/student.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body data-student-user="<?php echo htmlspecialchars(hash('sha256', $user_email), ENT_QUOTES, 'UTF-8'); ?>">
<div class="container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="assets/images/prismlogo1.png?v=2" alt="PRISM Assistant logo" class="sidebar-brand-logo">
            <div class="sidebar-brand-copy">
                <strong>IERB Progress &amp; Reporting System</strong>
                <span>Centro Escolar University - Malolos &bull; RPMS</span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="dashboard.php"><i class="fa-solid fa-chart-line"></i><span>Dashboard</span></a></li>
            <li class="active"><a href="student.php"><i class="fa-solid fa-user-graduate"></i><span>Students</span></a></li>
            <li><a href="ierbprog.php"><i class="fa-solid fa-file-signature"></i><span>IERB Progress</span></a></li>
            <li><a href="documents.php"><i class="fa-solid fa-folder-open"></i><span>Documents</span></a></li>
            <li><a href="reports.php"><i class="fa-solid fa-file-pdf"></i><span>Reports</span></a></li>
            <li><a href="calendar.php"><i class="fa-solid fa-calendar-days"></i><span>Calendar</span></a></li>
        </ul>
        <div class="sidebar-bottom">
            <div class="profile-dropdown-wrapper">
                <div class="sidebar-profile" id="profileToggle">
                    <img src="<?php echo htmlspecialchars($profile_img, ENT_QUOTES, 'UTF-8'); ?>" alt="Profile picture">
                    <div class="profile-info"><h4><?php echo htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8'); ?></h4><p><?php echo htmlspecialchars($user_role, ENT_QUOTES, 'UTF-8'); ?></p></div>
                    <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
                </div>
                <div class="profile-menu" id="profileMenu">
                    <a href="#"><i class="fa-solid fa-user-gear"></i> Profile</a>
                    <a href="#"><i class="fa-solid fa-gear"></i> Settings</a>
                    <a href="#"><i class="fa-solid fa-sliders"></i> Activity Logs</a>
                    <hr>
                    <a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a>
                </div>
            </div>
        </div>
    </aside>

    <main class="main-content student-page">
        <header class="topbar">
            <div class="student-heading"><h1>Students</h1><p>Master directory for student profiles and IERB progress.</p></div>
            <div class="top-controls">
                <div class="theme-toggle" id="themeToggle" title="Toggle light or dark theme"><i class="fa-solid fa-sun light-icon"></i><i class="fa-solid fa-moon dark-icon"></i></div>
            </div>
        </header>

        <section class="student-toolbar" aria-label="Student directory controls">
            <div class="student-search"><i class="fa-solid fa-magnifying-glass"></i><input id="studentSearch" type="search" placeholder="Search name, student ID, or course" aria-label="Search students"></div>
            <div class="student-filters">
                <select id="statusFilter" aria-label="Filter by status"><option value="">All statuses</option><option>On Track</option><option>Pending</option><option>Delayed</option></select>
                <button type="button" class="export-students-button" id="exportStudentsButton"><i class="fa-solid fa-file-csv"></i> Export CSV</button>
                <button type="button" class="add-student-button" id="addStudentButton"><i class="fa-solid fa-plus"></i> Add New Student</button>
            </div>
        </section>

        <section class="student-directory" aria-labelledby="directoryTitle">
            <div class="directory-heading"><div><h2 id="directoryTitle">Student Directory</h2><p id="studentCount">0 students</p></div></div>
            <div class="student-table-wrap">
                <table class="student-table">
                    <thead><tr><th>Student Name / ID</th><th>Course / Year</th><th>IERB Current Stage</th><th>Status</th><th>Last Updated</th><th>Actions</th></tr></thead>
                    <tbody id="studentTableBody"></tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<div class="student-modal" id="studentFormModal" aria-hidden="true">
    <div class="student-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="studentFormTitle">
        <div class="student-modal-heading"><div><span>Student record</span><h2 id="studentFormTitle">Add New Student</h2></div><button type="button" class="modal-icon-close" data-close="studentFormModal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button></div>
        <form id="studentForm">
            <input type="hidden" id="studentRecordId">
            <div class="student-form-grid">
                <div><label for="studentName">Student name</label><input id="studentName" maxlength="120" required></div>
                <div><label for="studentId">Student ID</label><input id="studentId" maxlength="40" required></div>
                <div><label for="studentEmail">Email address</label><input id="studentEmail" type="email" maxlength="150" required></div>
                <div><label for="studentGroupId">Research group ID</label><input id="studentGroupId" maxlength="40" placeholder="e.g. GRP-001" required></div>
                <div><label for="studentCourse">Course</label><input id="studentCourse" maxlength="80" required></div>
                <div><label for="studentYear">Year level</label><select id="studentYear" required><option value="">Select year</option><option>1st Year</option><option>2nd Year</option><option>3rd Year</option><option>4th Year</option><option>5th Year</option><option>Graduate</option></select></div>
                <div class="student-form-wide"><label for="studentResearchTitle">Research title</label><input id="studentResearchTitle" maxlength="250" required></div>
                <div class="student-form-wide research-members-field">
                    <div class="members-field-heading"><label>Research members</label><button type="button" id="addResearchMember"><i class="fa-solid fa-plus"></i> Add member</button></div>
                    <div class="research-member-rows" id="researchMemberRows"></div>
                </div>
                <div><label for="studentStage">IERB current stage</label><select id="studentStage" required><option>Stage 1</option><option>Stage 2</option><option>Stage 3</option><option>Stage 4</option><option>Stage 5</option><option>Completed</option></select></div>
                <div><label for="studentStatus">Status</label><select id="studentStatus" required><option>On Track</option><option>Pending</option><option>Delayed</option></select></div>
                <div><label for="studentProgress">Overall progress (%)</label><input id="studentProgress" type="number" min="0" max="100" value="0" required></div>
                <div><label for="studentRequirements">Pending requirements</label><input id="studentRequirements" maxlength="180" placeholder="None or required documents"></div>
            </div>
            <div class="student-modal-actions"><button type="button" class="student-secondary" data-close="studentFormModal">Cancel</button><button type="submit" class="student-primary"><i class="fa-solid fa-floppy-disk"></i> Save student</button></div>
        </form>
    </div>
</div>

<div class="student-modal" id="studentProfileModal" aria-hidden="true">
    <div class="student-modal-dialog profile-dialog" role="dialog" aria-modal="true" aria-labelledby="profileStudentName">
        <div class="student-modal-heading"><div><span>Student profile</span><h2 id="profileStudentName"></h2></div><button type="button" class="modal-icon-close" data-close="studentProfileModal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button></div>
        <div id="studentProfileContent"></div>
    </div>
</div>

<script src="assets/js/student.js"></script>
</body>
</html>
