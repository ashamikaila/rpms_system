(() => {
    const sidebar = document.querySelector('.dashboard-sidebar');
    if (!sidebar) return;
    sidebar.id ||= 'adminNavigation';
    const toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.className = 'sidebar-collapse-toggle';
    toggle.setAttribute('aria-controls', sidebar.id);
    sidebar.prepend(toggle);
    const logo = sidebar.querySelector('.sidebar-brand-logo');
    const expandedLogo = logo?.getAttribute('src');
    const setCollapsed = collapsed => {
        sidebar.classList.toggle('is-collapsed', collapsed);
        toggle.innerHTML = `<i class="fa-solid fa-angles-${collapsed ? 'right' : 'left'}" aria-hidden="true"></i>`;
        toggle.setAttribute('aria-expanded', String(!collapsed));
        toggle.setAttribute('aria-label', collapsed ? 'Expand navigation' : 'Collapse navigation');
        toggle.title = collapsed ? 'Expand navigation' : 'Collapse navigation';
        if (logo) logo.src = collapsed ? 'assets/images/prismicon.png' : expandedLogo;
        try { sessionStorage.setItem('prismNavigationMinimized', String(collapsed)); } catch (_) {}
    };
    toggle.addEventListener('click', () => setCollapsed(!sidebar.classList.contains('is-collapsed')));
    let savedCollapsed = false;
    try { savedCollapsed = sessionStorage.getItem('prismNavigationMinimized') === 'true'; } catch (_) {}
    setCollapsed(savedCollapsed);
    sidebar.querySelectorAll('.navigation-group-toggle').forEach(button => {
        button.addEventListener('click', () => {
            const submenu = document.getElementById(button.getAttribute('aria-controls'));
            const wasCollapsed = sidebar.classList.contains('is-collapsed');
            if (wasCollapsed) setCollapsed(false);
            const open = wasCollapsed || button.getAttribute('aria-expanded') !== 'true';
            button.setAttribute('aria-expanded', String(open));
            submenu.hidden = !open;
        });
    });
    sidebar.querySelectorAll('.nav-links a').forEach(link => {
        const label = link.textContent.trim();
        link.title = label;
        link.setAttribute('aria-label', label);
    });
    sidebar.querySelectorAll('.nav-links li.active a').forEach(link => {
        link.setAttribute('aria-current', 'page');
    });
    const profile = document.getElementById('profileToggle');
    const profileMenu = document.getElementById('profileMenu');
    const profileContainer = document.getElementById('adminProfile');
    const topbar = document.querySelector('.main-content .topbar');
    if (location.pathname.endsWith('/admin_students.php') && new URLSearchParams(location.search).get('view') === 'assignments') {
        const heading = topbar?.querySelector('h1');
        if (heading) heading.textContent = 'Student Assignments';
        const description = topbar?.querySelector('p');
        if (description) description.textContent = 'Edit a student record to assign a research adviser and research group.';
    }
    if (topbar && profileContainer) {
        let controls = topbar.querySelector('.top-controls');
        if (!controls) {
            controls = document.createElement('div');
            controls.className = 'top-controls';
            const theme = topbar.querySelector('#themeToggle');
            if (theme) controls.append(theme);
            topbar.append(controls);
        }
        controls.append(profileContainer);
    }
    const closeProfile = () => {
        profileMenu.classList.remove('show');
        profile.setAttribute('aria-expanded', 'false');
    };
    // Capture prevents legacy page-specific profile toggles from firing twice.
    profile.addEventListener('click', event => {
        event.stopImmediatePropagation();
        const open = profileMenu.classList.toggle('show');
        profile.setAttribute('aria-expanded', String(open));
    }, true);
    document.addEventListener('click', event => {
        if (!profileContainer.contains(event.target)) closeProfile();
    });
    profileContainer.addEventListener('keydown', event => {
        if (event.key === 'Escape') { closeProfile(); profile.focus(); }
    });
    const preferences = document.getElementById('adminPreferences');
    const openPreferences = settings => {
        closeProfile();
        document.getElementById('preferencesTitle').textContent = settings ? 'Settings' : 'Edit Profile';
        document.getElementById('profileFields').hidden = settings;
        document.getElementById('settingsFields').hidden = !settings;
        document.getElementById('adminThemePreference').value = document.documentElement.classList.contains('dark-theme') ? 'dark' : 'light';
        preferences.showModal();
    };
    document.getElementById('editAdminProfile').addEventListener('click', () => openPreferences(false));
    document.getElementById('navigationSettings').addEventListener('click', () => openPreferences(true));
    document.getElementById('closePreferences').addEventListener('click', () => preferences.close());
    document.getElementById('adminThemePreference').addEventListener('change', event => {
        document.documentElement.classList.toggle('dark-theme', event.target.value === 'dark');
        try { localStorage.setItem('prismTheme', event.target.value); } catch (_) {}
    });
    document.getElementById('adminProfileForm').addEventListener('submit', async event => {
        event.preventDefault();
        const status = document.getElementById('profileSaveStatus');
        const submit = event.target.querySelector('[type="submit"]');
        submit.disabled = true;
        status.textContent = 'Saving…';
        try {
            const response = await fetch('admin_profile.php', { method: 'POST', body: new FormData(event.target) });
            const result = await response.json();
            if (!response.ok) throw new Error(result.error || 'Unable to save changes.');
            profileContainer.querySelector('.admin-profile-name').textContent = result.name;
            status.textContent = 'Display name saved for this session.';
        } catch (error) { status.textContent = error.message; }
        finally { submit.disabled = false; }
    });
})();
