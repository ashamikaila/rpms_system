document.addEventListener('DOMContentLoaded', () => {
    const storageKey = `prismStudents:${document.body.dataset.ierbUser || 'default'}`;
    const stages = ['Stage 1', 'Stage 2', 'Stage 3', 'Stage 4', 'Stage 5', 'Completed'];
    let students = [];
    let actionStudentId = '';
    let actionMode = '';

    const tableBody = document.getElementById('ierbTableBody');
    const search = document.getElementById('ierbSearch');
    const stageFilter = document.getElementById('stageFilter');
    const statusFilter = document.getElementById('ierbStatusFilter');
    const modal = document.getElementById('ierbActionModal');
    const entryModal = document.getElementById('ierbEntryModal');
    const entryForm = document.getElementById('ierbEntryForm');
    const actionForm = document.getElementById('ierbActionForm');
    const actionText = document.getElementById('ierbActionText');

    function load() {
        try {
            const stored = JSON.parse(localStorage.getItem(storageKey));
            students = Array.isArray(stored) ? stored : [];
        } catch (_) { students = []; }
    }
    const save = () => localStorage.setItem(storageKey, JSON.stringify(students));
    const esc = value => String(value ?? '').replace(/[&<>'"]/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' })[char]);
    const dateOnly = value => new Date(value).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
    const completedCount = student => student.stage === 'Completed' ? 5 : Math.max(0, stages.indexOf(student.stage));
    const delayState = student => student.status === 'Delayed' ? ['overdue', 'Overdue'] : student.status === 'Pending' ? ['warning', 'Warning'] : ['on-track', 'On Track'];

    function filteredStudents() {
        const query = search.value.trim().toLowerCase();
        return students.filter(student => {
            const haystack = `${student.name} ${student.studentId} ${student.groupId || ''} ${student.researchTitle || ''}`.toLowerCase();
            return (!query || haystack.includes(query)) && (!stageFilter.value || student.stage === stageFilter.value) && (!statusFilter.value || student.status === statusFilter.value);
        });
    }

    function renderChart() {
        const chart = document.getElementById('stageChart');
        const counts = stages.map(stage => students.filter(student => student.stage === stage).length);
        const max = Math.max(1, ...counts);
        chart.replaceChildren();
        stages.forEach((stage, index) => {
            const column = document.createElement('div');
            column.className = 'stage-column';
            const area = document.createElement('div');
            area.className = 'stage-bar-area';
            const bar = document.createElement('div');
            bar.className = 'stage-bar';
            bar.style.height = `${Math.max(4, (counts[index] / max) * 125)}px`;
            bar.innerHTML = `<strong>${counts[index]}</strong>`;
            area.appendChild(bar);
            const label = document.createElement('span');
            label.textContent = stage;
            column.append(area, label);
            chart.appendChild(column);
        });
        document.getElementById('overviewTotal').textContent = `${students.length} ${students.length === 1 ? 'student' : 'students'}`;
    }

    function stageChecks(student) {
        const count = completedCount(student);
        return Array.from({ length: 5 }, (_, index) => `<span class="stage-check ${index < count ? 'done' : ''}" title="Stage ${index + 1}">${index < count ? '<i class="fa-solid fa-check"></i>' : index + 1}</span>`).join('');
    }

    function submissionDates(student) {
        const dates = student.submissionDates && typeof student.submissionDates === 'object' ? student.submissionDates : {};
        const entries = Object.entries(dates).sort(([a], [b]) => a.localeCompare(b));
        return entries.length ? entries.map(([stage, date]) => `<span>${esc(stage)}: ${esc(dateOnly(date))}</span>`).join('') : '<span>No submissions recorded</span>';
    }

    function renderTable() {
        const records = filteredStudents();
        document.getElementById('ierbRecordCount').textContent = `${records.length} ${records.length === 1 ? 'record' : 'records'}`;
        tableBody.replaceChildren();
        if (!records.length) {
            const row = document.createElement('tr');
            row.innerHTML = `<td colspan="7" class="ierb-empty"><i class="fa-solid fa-file-signature"></i>${students.length ? 'No progress records match the filters.' : 'No IERB progress records yet.'}</td>`;
            tableBody.appendChild(row);
            return;
        }
        records.sort((a, b) => a.name.localeCompare(b.name)).forEach(student => {
            const delay = delayState(student);
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><div class="student-cell"><strong>${esc(student.name)}</strong><span>${esc(student.studentId)}${student.groupId ? ` - ${esc(student.groupId)}` : ''}</span></div></td>
                <td><span class="current-stage">${esc(student.stage || 'Stage 1')}</span></td>
                <td><div class="completed-stages">${stageChecks(student)}</div></td>
                <td><div class="requirements-text ${student.requirements ? '' : 'clear'}">${esc(student.requirements || 'No pending requirements')}</div></td>
                <td><div class="submission-dates">${submissionDates(student)}</div></td>
                <td><span class="delay-indicator ${delay[0]}">${delay[1]}</span></td>
                <td><div class="ierb-actions">
                    <button class="ierb-action" data-action="complete" title="Mark stage complete" aria-label="Mark stage complete"><i class="fa-solid fa-check-double"></i></button>
                    <button class="ierb-action" data-action="requirement" title="Flag requirement" aria-label="Flag requirement"><i class="fa-solid fa-flag"></i></button>
                    <button class="ierb-action" data-action="note" title="Add internal note" aria-label="Add internal note"><i class="fa-solid fa-note-sticky"></i></button>
                    <button class="ierb-action" data-action="deadline" title="Set deadline" aria-label="Set deadline"><i class="fa-solid fa-calendar-plus"></i></button>
                    <button class="ierb-action" data-action="status" title="Update official stage and status" aria-label="Update official stage and status"><i class="fa-solid fa-pen-to-square"></i></button>
                    <button class="ierb-action" data-action="followup" title="Send follow-up" aria-label="Send follow-up"><i class="fa-solid fa-paper-plane"></i></button>
                    <button class="ierb-action delete" data-action="delete" title="Delete IERB record" aria-label="Delete IERB record"><i class="fa-solid fa-trash"></i></button>
                </div></td>`;
            row.querySelector('[data-action="complete"]').addEventListener('click', () => completeStage(student.id));
            row.querySelector('[data-action="requirement"]').addEventListener('click', () => openAction(student.id, 'requirement'));
            row.querySelector('[data-action="note"]').addEventListener('click', () => openAction(student.id, 'note'));
            row.querySelector('[data-action="deadline"]').addEventListener('click', () => openAction(student.id, 'deadline'));
            row.querySelector('[data-action="status"]').addEventListener('click', () => updateOfficialStatus(student.id));
            row.querySelector('[data-action="followup"]').addEventListener('click', () => sendFollowup(student.id));
            row.querySelector('[data-action="delete"]').addEventListener('click', () => deleteIerbRecord(student.id));
            tableBody.appendChild(row);
        });
    }

    function render() { renderChart(); renderTable(); }

    function openEntryModal() {
        entryForm.reset();
        entryModal.classList.add('show');
        entryModal.setAttribute('aria-hidden', 'false');
        document.getElementById('entryStudentName').focus();
    }

    function closeEntryModal() {
        entryModal.classList.remove('show');
        entryModal.setAttribute('aria-hidden', 'true');
        entryForm.reset();
    }

    entryForm.addEventListener('submit', event => {
        event.preventDefault();
        const studentId = document.getElementById('entryStudentId').value.trim();
        if (students.some(student => String(student.studentId).toLowerCase() === studentId.toLowerCase())) {
            alert('That student ID already has an IERB record.');
            document.getElementById('entryStudentId').focus();
            return;
        }
        const now = new Date().toISOString();
        const stage = document.getElementById('entryStage').value;
        const submissionDate = document.getElementById('entrySubmissionDate').value;
        const stageIndex = stages.indexOf(stage);
        const submissionDates = submissionDate ? { [stage]: `${submissionDate}T00:00:00` } : {};
        students.push({
            id: `${Date.now()}-${Math.random().toString(16).slice(2)}`,
            name: document.getElementById('entryStudentName').value.trim(), studentId,
            email: document.getElementById('entryEmail').value.trim(),
            groupId: document.getElementById('entryGroupId').value.trim(),
            course: document.getElementById('entryCourse').value.trim(), year: '',
            researchTitle: document.getElementById('entryResearchTitle').value.trim(), researchMembers: [],
            stage, requirements: document.getElementById('entryRequirements').value.trim(),
            status: document.getElementById('entryStatus').value,
            progress: stage === 'Completed' ? '100' : String(Math.max(0, Math.round((stageIndex / 5) * 100))),
            submissionDates, notes: [], updatedAt: now,
            history: [{ message: 'IERB progress record created.', at: now }]
        });
        save(); closeEntryModal(); render();
    });

    function addHistory(student, message) {
        student.history = Array.isArray(student.history) ? student.history : [];
        student.history.unshift({ message, at: new Date().toISOString() });
        student.updatedAt = new Date().toISOString();
    }

    function deleteIerbRecord(id) {
        const student = students.find(item => item.id === id);
        if (!student || !confirm(`Delete the IERB record for ${student.name}? This cannot be undone.`)) return;
        students = students.filter(item => item.id !== id);
        save();
        render();
    }

    function completeStage(id) {
        const student = students.find(item => item.id === id); if (!student) return;
        if (student.stage === 'Completed') { alert('All IERB stages are already completed.'); return; }
        if (!confirm(`Mark ${student.stage} complete for ${student.name}?`)) return;
        const currentIndex = Math.max(0, stages.indexOf(student.stage));
        student.submissionDates = student.submissionDates && typeof student.submissionDates === 'object' ? student.submissionDates : {};
        student.submissionDates[student.stage] = new Date().toISOString();
        const completedStage = student.stage;
        student.stage = stages[Math.min(currentIndex + 1, stages.length - 1)];
        student.progress = student.stage === 'Completed' ? '100' : String(Math.round(((currentIndex + 1) / 5) * 100));
        student.requirements = '';
        student.status = 'On Track';
        addHistory(student, `${completedStage} marked complete; advanced to ${student.stage}.`);
        save(); render();
    }

    function openAction(id, mode) {
        const student = students.find(item => item.id === id); if (!student) return;
        actionStudentId = id; actionMode = mode;
        const requirement = mode === 'requirement', deadline = mode === 'deadline';
        document.getElementById('ierbActionEyebrow').textContent = requirement ? 'Missing document' : deadline ? 'Official schedule' : 'RPMS internal record';
        document.getElementById('ierbActionTitle').textContent = requirement ? 'Flag Requirement' : deadline ? 'Set Deadline' : 'Add Internal Note';
        document.getElementById('ierbActionLabel').textContent = requirement ? 'Pending requirement' : deadline ? 'Deadline (YYYY-MM-DD)' : 'Internal comment';
        actionText.placeholder = requirement ? 'e.g. Missing Ethics Consent Form' : deadline ? 'e.g. 2026-10-24' : 'Write an internal note for RPMS staff...';
        actionText.value = requirement ? (student.requirements || '') : deadline ? (student.deadline || '') : '';
        modal.classList.add('show'); modal.setAttribute('aria-hidden', 'false'); actionText.focus();
    }

    function closeAction() { modal.classList.remove('show'); modal.setAttribute('aria-hidden', 'true'); actionForm.reset(); actionStudentId = ''; actionMode = ''; }

    actionForm.addEventListener('submit', event => {
        event.preventDefault();
        const student = students.find(item => item.id === actionStudentId);
        const text = actionText.value.trim();
        if (!student || !text) return;
        if (actionMode === 'requirement') {
            student.requirements = text; student.status = 'Pending';
            addHistory(student, `Requirement flagged: ${text}`);
        } else if (actionMode === 'deadline') {
            if (!/^\d{4}-\d{2}-\d{2}$/.test(text)) { alert('Enter the deadline as YYYY-MM-DD.'); return; }
            student.deadline = text;
            addHistory(student, `Official deadline set to ${text}.`);
        } else {
            student.notes = Array.isArray(student.notes) ? student.notes : [];
            student.notes.unshift({ text, at: new Date().toISOString() });
            addHistory(student, `Internal note added: ${text}`);
        }
        save(); closeAction(); render();
    });

    function updateOfficialStatus(id) {
        const student = students.find(item => item.id === id); if (!student) return;
        const stage = prompt('Official stage (Stage 1–Stage 5 or Completed):', student.stage || 'Stage 1');
        if (stage === null || !stages.includes(stage.trim())) { if (stage !== null) alert('Enter a valid official stage.'); return; }
        const status = prompt('Official status (On Track, Pending, or Delayed):', student.status || 'Pending');
        if (status === null || !['On Track','Pending','Delayed'].includes(status.trim())) { if (status !== null) alert('Enter a valid official status.'); return; }
        student.stage = stage.trim(); student.status = status.trim();
        const index = stages.indexOf(student.stage); student.progress = student.stage === 'Completed' ? '100' : String(Math.round((index / 5) * 100));
        addHistory(student, `Official IERB update: ${student.stage} — ${student.status}.`); save(); render();
    }

    async function sendFollowup(id) {
        const student = students.find(item => item.id === id); if (!student) return;
        if (!confirm(`Send an automated follow-up email to ${student.name}?`)) return;
        try {
            const response = await fetch('send_followup.php', {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name: student.name, studentId: student.studentId, email: student.email, stage: student.stage, status: student.status, requirements: student.requirements || '' })
            });
            const result = await response.json();
            if (!response.ok || !result.ok) throw new Error(result.message || 'Follow-up could not be sent.');
            addHistory(student, 'Automated IERB follow-up email sent.'); save(); render();
            alert(result.message);
        } catch (error) {
            alert(error.message || 'Follow-up could not be sent. Check the email server configuration.');
        }
    }

    search.addEventListener('input', renderTable); stageFilter.addEventListener('change', renderTable); statusFilter.addEventListener('change', renderTable);
    document.getElementById('closeIerbModal').addEventListener('click', closeAction);
    document.getElementById('cancelIerbAction').addEventListener('click', closeAction);
    modal.addEventListener('click', event => { if (event.target === modal) closeAction(); });
    document.getElementById('addIerbEntry').addEventListener('click', openEntryModal);
    document.getElementById('closeIerbEntry').addEventListener('click', closeEntryModal);
    document.getElementById('cancelIerbEntry').addEventListener('click', closeEntryModal);
    entryModal.addEventListener('click', event => { if (event.target === entryModal) closeEntryModal(); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') { closeAction(); closeEntryModal(); } });

    const themeToggle = document.getElementById('themeToggle');
    themeToggle.addEventListener('click', () => { const dark = document.documentElement.classList.toggle('dark-theme'); try { localStorage.setItem('prismTheme', dark ? 'dark' : 'light'); } catch (_) {} });
    const profileToggle = document.getElementById('profileToggle');
    const profileMenu = document.getElementById('profileMenu');
    profileToggle.addEventListener('click', event => { event.stopPropagation(); profileMenu.classList.toggle('show'); });
    document.addEventListener('click', () => profileMenu.classList.remove('show'));
    window.addEventListener('pageshow', () => { load(); render(); });
    window.addEventListener('storage', event => { if (event.key === storageKey) { load(); render(); } });
    load(); render();
});
