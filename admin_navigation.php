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
<div class="admin-profile" id="adminProfile">
    <button type="button" id="profileToggle" aria-label="Open profile menu" aria-expanded="false" aria-controls="profileMenu">
        <img src="assets/images/default-avatar.svg" alt="">
        <span class="admin-profile-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'CEU RPMS', ENT_QUOTES, 'UTF-8') ?></span>
        <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
    </button>
    <div class="profile-menu" id="profileMenu"><button type="button" id="editAdminProfile"><i class="fa-solid fa-user-pen" aria-hidden="true"></i> Edit Profile</button></div>
</div>
<dialog class="admin-preferences" id="adminPreferences">
    <form id="adminProfileForm">
        <div class="preferences-heading"><h2 id="preferencesTitle">Edit Profile</h2><button type="button" id="closePreferences" aria-label="Close"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button></div>
        <div id="profileFields"><label for="adminDisplayName">Display name</label><input id="adminDisplayName" name="name" required maxlength="120" value="<?= htmlspecialchars($_SESSION['user_name'] ?? 'CEU RPMS', ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['profile_csrf'], ENT_QUOTES, 'UTF-8') ?>"><p id="profileSaveStatus" role="status"></p><button class="preferences-save" type="submit">Save changes</button></div>
        <div id="settingsFields" hidden><label for="adminThemePreference">Appearance</label><select id="adminThemePreference"><option value="light">Light</option><option value="dark">Dark</option></select></div>
    </form>
</dialog>
