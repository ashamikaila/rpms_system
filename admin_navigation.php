<?php
$_SESSION['profile_csrf'] ??= bin2hex(random_bytes(32));
$navigationPage = basename($_SERVER['SCRIPT_NAME']);
$navigationView = is_string($_GET['view'] ?? null) ? $_GET['view'] : '';
$navigationSections = [
    'Overview' => [['dashboard.php', 'house', 'Dashboard']],
    'Research Management' => [
        ['admin_students.php', 'user-graduate', 'Student Records'],
        ['advisers', 'chalkboard-user', 'Research Advisers', [
            ['admin_faculty.php', 'Adviser Directory'],
            ['admin_students.php?view=assignments', 'Student Assignments'],
            ['admin_workspace.php?view=reviews', 'Review Monitoring'],
        ]],
        ['documents.php', 'folder-open', 'Document Submissions'],
        ['monitoring', 'chart-line', 'IERB Monitoring', [
            ['ierbprog.php', 'IERB Progress'],
            ['ierbprog.php?view=pending', 'Pending Requirements'],
            ['calendar.php', 'Deadlines'],
        ]],
    ],
    'Reporting & Communication' => [
        ['reports.php', 'file-pdf', 'AI Reports & Summaries'],
        ['admin_notifications.php', 'bell', 'Notifications'],
    ],
    'Administration' => [
        ['admin_workspace.php?view=users', 'users-gear', 'User Management'],
        ['admin_workspace.php?view=overrides', 'shield-halved', 'Workflow Overrides'],
        ['admin_workspace.php?view=activity', 'clock-rotate-left', 'Activity Logs'],
    ],
    'Resources' => [
        ['resources', 'book-open', 'Research Resources', [
            ['https://ceu-ierb.wixsite.com/ierb', 'IERB Portal ↗'],
            ['research_resources.php?view=sdg', 'Sustainable Development Goals'],
            ['research_resources.php?view=agenda', 'Research Agenda'],
        ]],
    ],
];
$navigationActive = static function ($url) use ($navigationPage, $navigationView) {
    $parts = parse_url($url);
    parse_str($parts['query'] ?? '', $query);
    return ($parts['path'] ?? '') === $navigationPage && ($query['view'] ?? '') === $navigationView;
};
?>
<aside class="sidebar dashboard-sidebar" aria-label="Main navigation">
    <div class="sidebar-header"><img src="assets/images/prismlogo1.png?v=2" alt="PRISM" class="sidebar-brand-logo"></div>
    <nav class="sidebar-sections" aria-label="Dashboard sections">
    <?php foreach ($navigationSections as $heading => $items): ?>
        <section class="navigation-section" aria-label="<?= htmlspecialchars($heading) ?>">
            <h2 class="navigation-heading"><?= htmlspecialchars($heading) ?></h2>
            <ul class="nav-links">
            <?php foreach ($items as $item): [$url, $icon, $label] = $item; ?>
                <?php if (isset($item[3])): $groupActive = count(array_filter($item[3], static fn($child) => $navigationActive($child[0]))) > 0; ?>
                <li class="navigation-group <?= $groupActive ? 'contains-active' : '' ?>">
                    <button type="button" class="navigation-group-toggle" aria-expanded="<?= $groupActive ? 'true' : 'false' ?>" aria-controls="nav-<?= $url ?>" aria-label="<?= $label ?>" title="<?= $label ?>"><i class="fa-solid fa-<?= $icon ?>" aria-hidden="true"></i><span><?= $label ?></span><i class="fa-solid fa-chevron-down navigation-chevron" aria-hidden="true"></i></button>
                    <ul class="navigation-submenu" id="nav-<?= $url ?>" <?= $groupActive ? '' : 'hidden' ?>>
                    <?php foreach ($item[3] as [$childUrl, $childLabel]): $external = str_starts_with($childUrl, 'https://') || str_starts_with($childUrl, 'assets/'); ?>
                        <li class="<?= $navigationActive($childUrl) ? 'active' : '' ?>"><a href="<?= htmlspecialchars($childUrl) ?>" <?= $external ? 'target="_blank" rel="noopener noreferrer"' : '' ?>><?= htmlspecialchars($childLabel) ?></a></li>
                    <?php endforeach; ?>
                    </ul>
                </li>
                <?php else: ?>
                <li class="<?= $navigationActive($url) ? 'active' : '' ?>"><a href="<?= htmlspecialchars($url) ?>"><i class="fa-solid fa-<?= $icon ?>" aria-hidden="true"></i><span><?= htmlspecialchars($label) ?></span></a></li>
                <?php endif; ?>
            <?php endforeach; ?>
            </ul>
        </section>
    <?php endforeach; ?>
    </nav>
    <div class="sidebar-bottom sidebar-utilities">
        <button type="button" id="navigationSettings" title="Settings" aria-label="Settings"><i class="fa-solid fa-gear" aria-hidden="true"></i><span>Settings</span></button>
        <a href="logout.php" title="Log out" aria-label="Log out"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i><span>Log Out</span></a>
    </div>
</aside>
<div class="admin-profile" id="adminProfile" data-profile-key="<?= htmlspecialchars(hash('sha256', $_SESSION['user_email'] ?? 'rpms@ceu.edu.ph'), ENT_QUOTES, 'UTF-8') ?>">
    <button type="button" id="profileToggle" aria-label="Open profile menu" aria-expanded="false" aria-controls="profileMenu">
        <img src="<?= htmlspecialchars($_SESSION['profile_photo'] ?? 'assets/images/default-avatar.svg', ENT_QUOTES, 'UTF-8') ?>" alt="">
        <span class="admin-profile-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'CEU RPMS', ENT_QUOTES, 'UTF-8') ?></span>
        <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
    </button>
    <div class="profile-menu" id="profileMenu"><button type="button" id="editAdminProfile"><i class="fa-solid fa-user-pen" aria-hidden="true"></i> Edit Profile</button></div>
</div>
<dialog class="admin-preferences" id="adminPreferences" aria-labelledby="preferencesTitle">
    <form id="adminProfileForm">
        <div class="preferences-heading"><h2 id="preferencesTitle">Edit Profile</h2><button type="button" id="closePreferences" aria-label="Close"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button></div>
        <div id="profileFields">
            <p class="profile-intro">Update your personal information and profile photo. Changes are saved in this browser only.</p>
            <div class="profile-photo-editor">
                <img id="adminPhotoPreview" src="<?= htmlspecialchars($_SESSION['profile_photo'] ?? 'assets/images/default-avatar.svg', ENT_QUOTES, 'UTF-8') ?>" alt="Profile photo preview">
                <div>
                    <label for="adminProfilePhoto">Profile photo</label>
                    <input id="adminProfilePhoto" name="photo" type="file" accept="image/jpeg,image/png,image/webp" aria-describedby="profilePhotoHelp">
                    <small id="profilePhotoHelp">JPG, PNG, or WebP. Maximum 2 MB.</small>
                    <button class="profile-text-button" type="button" id="removeAdminPhoto">Remove photo</button>
                </div>
            </div>
            <div class="profile-field-grid">
                <div class="profile-full-width"><label for="adminDisplayName">Full name</label><input id="adminDisplayName" name="name" autocomplete="name" required maxlength="120" value="<?= htmlspecialchars($_SESSION['user_name'] ?? 'CEU RPMS', ENT_QUOTES, 'UTF-8') ?>"></div>
                <div class="profile-full-width"><label for="adminEmail">Email address</label><input id="adminEmail" name="email" type="email" autocomplete="email" required maxlength="190" value="<?= htmlspecialchars($_SESSION['profile_email'] ?? $_SESSION['user_email'] ?? 'rpms@ceu.edu.ph', ENT_QUOTES, 'UTF-8') ?>"></div>
                <div><label for="adminCourse">Course</label><input id="adminCourse" name="course" maxlength="120" placeholder="e.g. BS Information Technology" value="<?= htmlspecialchars($_SESSION['profile_course'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
                <div><label for="adminDepartment">Department</label><input id="adminDepartment" name="department" maxlength="120" placeholder="e.g. AMT Department" value="<?= htmlspecialchars($_SESSION['profile_department'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            </div>
            <p id="profileSaveStatus" role="status" aria-live="polite"></p>
            <button class="preferences-save" type="submit">Save changes</button>
        </div>
        <div id="settingsFields" hidden><label for="adminThemePreference">Appearance</label><select id="adminThemePreference"><option value="light">Light</option><option value="dark">Dark</option></select></div>
    </form>
    <section class="profile-password-section" id="profilePasswordSection" aria-labelledby="profilePasswordTitle">
        <h3 id="profilePasswordTitle">Change password</h3>
        <p class="profile-intro" id="profilePasswordHelp">Preview only. This form does not change your sign-in password. Use at least 8 characters for the new password.</p>
        <form id="adminPasswordForm">
            <div class="profile-field-grid">
                <div class="profile-full-width"><label for="adminCurrentPassword">Current password</label><input id="adminCurrentPassword" type="password" autocomplete="current-password" required aria-describedby="profilePasswordHelp"></div>
                <div><label for="adminNewPassword">New password</label><input id="adminNewPassword" type="password" autocomplete="new-password" minlength="8" required></div>
                <div><label for="adminConfirmPassword">Confirm new password</label><input id="adminConfirmPassword" type="password" autocomplete="new-password" minlength="8" required></div>
            </div>
            <label class="profile-show-passwords"><input id="adminShowPasswords" type="checkbox"> Show passwords</label>
            <p id="passwordSaveStatus" role="status" aria-live="polite"></p>
            <button class="preferences-save" type="submit">Change password</button>
        </form>
    </section>
</dialog>
