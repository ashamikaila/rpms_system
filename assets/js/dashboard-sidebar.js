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
    const profileForm = document.getElementById('adminProfileForm');
    const profileStatus = document.getElementById('profileSaveStatus');
    const profileSubmit = profileForm.querySelector('[type="submit"]');
    const photoInput = document.getElementById('adminProfilePhoto');
    const photoPreview = document.getElementById('adminPhotoPreview');
    const defaultPhoto = 'assets/images/default-avatar.svg';
    const profileKey = 'prismProfilePreview:' + profileContainer.dataset.profileKey;
    const fields = {
        name: document.getElementById('adminDisplayName'),
        email: document.getElementById('adminEmail'),
        course: document.getElementById('adminCourse'),
        department: document.getElementById('adminDepartment')
    };
    let savedProfile = Object.fromEntries(Object.entries(fields).map(([key, input]) => [key, input.value]));
    savedProfile.photo = '';
    try {
        const stored = JSON.parse(localStorage.getItem(profileKey));
        if (stored && typeof stored === 'object' && !Array.isArray(stored)) {
            for (const key of Object.keys(fields)) {
                if (typeof stored[key] === 'string') savedProfile[key] = stored[key];
            }
            if (typeof stored.photo === 'string' && /^data:image\/(jpeg|png|webp);base64,[A-Za-z0-9+/=]+$/.test(stored.photo)) savedProfile.photo = stored.photo;
        }
    } catch (_) {}
    let draftPhoto = savedProfile.photo;
    let photoVersion = 0;
    const updateProfileDisplay = () => {
        profileContainer.querySelector('.admin-profile-name').textContent = savedProfile.name;
        profileContainer.querySelector('img').src = savedProfile.photo || defaultPhoto;
        const welcomeName = document.querySelector('.welcome-header h1 span');
        if (welcomeName) welcomeName.textContent = savedProfile.name;
    };
    const resetProfileDraft = () => {
        photoVersion++;
        for (const [key, input] of Object.entries(fields)) {
            input.value = savedProfile[key];
            input.setCustomValidity('');
        }
        draftPhoto = savedProfile.photo;
        photoPreview.src = draftPhoto || defaultPhoto;
        photoInput.value = '';
        photoInput.setCustomValidity('');
        profileSubmit.disabled = false;
        profileStatus.textContent = '';
    };
    const passwordForm = document.getElementById('adminPasswordForm');
    const currentPassword = document.getElementById('adminCurrentPassword');
    const newPassword = document.getElementById('adminNewPassword');
    const confirmPassword = document.getElementById('adminConfirmPassword');
    const passwordStatus = document.getElementById('passwordSaveStatus');
    const resetPasswordDraft = () => {
        passwordForm.reset();
        for (const input of [currentPassword, newPassword, confirmPassword]) {
            input.type = 'password';
            input.setCustomValidity('');
        }
        passwordStatus.textContent = '';
    };
    const openPreferences = settings => {
        closeProfile();
        resetProfileDraft();
        resetPasswordDraft();
        preferences.classList.toggle('is-settings', settings);
        document.getElementById('preferencesTitle').textContent = settings ? 'Settings' : 'Edit Profile';
        document.getElementById('profileFields').hidden = settings;
        document.getElementById('profilePasswordSection').hidden = settings;
        document.getElementById('settingsFields').hidden = !settings;
        document.getElementById('adminThemePreference').value = document.documentElement.classList.contains('dark-theme') ? 'dark' : 'light';
        preferences.showModal();
    };
    document.getElementById('editAdminProfile').addEventListener('click', () => openPreferences(false));
    document.getElementById('navigationSettings').addEventListener('click', () => openPreferences(true));
    document.getElementById('closePreferences').addEventListener('click', () => preferences.close());
    preferences.addEventListener('close', () => { resetProfileDraft(); resetPasswordDraft(); });
    document.getElementById('adminThemePreference').addEventListener('change', event => {
        document.documentElement.classList.toggle('dark-theme', event.target.value === 'dark');
        try { localStorage.setItem('prismTheme', event.target.value); } catch (_) {}
    });
    fields.name.addEventListener('input', () => fields.name.setCustomValidity(fields.name.value.trim() ? '' : 'Enter your full name.'));
    photoInput.addEventListener('change', () => {
        const version = ++photoVersion;
        const file = photoInput.files[0];
        photoInput.setCustomValidity('');
        profileStatus.textContent = '';
        profileSubmit.disabled = false;
        if (!file) return;
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 2 * 1024 * 1024) {
            photoInput.value = '';
            profileStatus.textContent = 'Choose a JPG, PNG, or WebP image of 2 MB or less.';
            return;
        }
        profileSubmit.disabled = true;
        const reader = new FileReader();
        const fail = () => {
            if (version !== photoVersion) return;
            photoInput.value = '';
            profileSubmit.disabled = false;
            profileStatus.textContent = 'This image could not be opened. Please choose another photo.';
        };
        reader.onerror = fail;
        reader.onload = () => {
            const image = new Image();
            image.onerror = fail;
            image.onload = () => {
                if (version !== photoVersion) return;
                draftPhoto = reader.result;
                photoPreview.src = draftPhoto;
                profileSubmit.disabled = false;
            };
            image.src = reader.result;
        };
        reader.readAsDataURL(file);
    });
    document.getElementById('removeAdminPhoto').addEventListener('click', () => {
        photoVersion++;
        draftPhoto = '';
        photoInput.value = '';
        photoPreview.src = defaultPhoto;
        profileSubmit.disabled = false;
        profileStatus.textContent = 'Photo removed from preview. Save changes to keep this change.';
    });
    profileForm.addEventListener('submit', event => {
        event.preventDefault();
        fields.name.setCustomValidity(fields.name.value.trim() ? '' : 'Enter your full name.');
        if (profileSubmit.disabled || !profileForm.reportValidity()) return;
        const nextProfile = Object.fromEntries(Object.entries(fields).map(([key, input]) => [key, input.value.trim()]));
        nextProfile.photo = draftPhoto;
        try {
            localStorage.setItem(profileKey, JSON.stringify(nextProfile));
            savedProfile = nextProfile;
            updateProfileDisplay();
            profileStatus.textContent = 'Profile saved in this browser. Your sign-in details have not changed.';
        } catch (_) {
            profileStatus.textContent = 'Could not save the preview. Try a smaller photo or enable browser storage.';
        }
    });
    document.getElementById('adminShowPasswords').addEventListener('change', event => {
        for (const input of [currentPassword, newPassword, confirmPassword]) input.type = event.target.checked ? 'text' : 'password';
    });
    const validatePasswords = () => {
        newPassword.setCustomValidity(newPassword.value && newPassword.value.length < 8 ? 'Use at least 8 characters.' : newPassword.value && newPassword.value === currentPassword.value ? 'Choose a different new password.' : '');
        confirmPassword.setCustomValidity(confirmPassword.value && confirmPassword.value !== newPassword.value ? 'Passwords must match.' : '');
    };
    passwordForm.addEventListener('input', () => { validatePasswords(); passwordStatus.textContent = ''; });
    passwordForm.addEventListener('submit', event => {
        event.preventDefault();
        validatePasswords();
        if (!passwordForm.reportValidity()) return;
        // Frontend preview only: never store, log, or send password values.
        resetPasswordDraft();
        passwordStatus.textContent = 'Preview validated. Your password has not been changed.';
    });
    updateProfileDisplay();
})();
