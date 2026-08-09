document.addEventListener('DOMContentLoaded', () => {
    const pad = value => String(value).padStart(2, '0');
    const dateKey = date => `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
    const fromKey = key => { const [y, m, d] = key.split('-').map(Number); return new Date(y, m - 1, d); };
    const storageKey = `prismPortalCalendar:${document.body.dataset.portalKey}`;
    const portalKey = `prismPortal:${document.body.dataset.portalKey}`;
    let reminders = {};
    try { reminders = JSON.parse(localStorage.getItem(storageKey)) || {}; } catch (_) {}
    const officialDeadline = () => { try { return JSON.parse(localStorage.getItem(portalKey))?.official?.deadline || '2026-09-15'; } catch (_) { return '2026-09-15'; } };
    const today = new Date(); today.setHours(0, 0, 0, 0);
    let selected = dateKey(today), visible = new Date(today.getFullYear(), today.getMonth(), 1);
    const grid = document.getElementById('monthGrid'), form = document.getElementById('reminderForm');
    const $ = id => document.getElementById(id);
    const esc = value => String(value ?? '').replace(/[&<>"']/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char]);
    const tasks = key => Array.isArray(reminders[key]) ? reminders[key] : [];
    const officialFor = key => key === officialDeadline() ? [{ id: 'official-deadline', title: 'IERB requirement deadline', notes: 'Official RPMS/IERB deadline', official: true }] : [];
    const allEvents = key => [...officialFor(key), ...tasks(key)].sort((a, b) => (a.time || '99:99').localeCompare(b.time || '99:99'));
    const formatDate = key => fromKey(key).toLocaleDateString('en-PH', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
    const formatTime = time => time ? new Date(`2000-01-01T${time}`).toLocaleTimeString('en-PH', { hour: 'numeric', minute: '2-digit' }) : '';
    const save = () => localStorage.setItem(storageKey, JSON.stringify(reminders));
    function resetForm() { form.reset(); $('editingId').value = ''; $('cancelEdit').hidden = true; $('saveLabel').textContent = 'Add reminder'; }
    function renderCalendar() {
        $('monthLabel').textContent = visible.toLocaleDateString('en-PH', { month: 'long', year: 'numeric' }); grid.replaceChildren();
        const first = new Date(visible.getFullYear(), visible.getMonth(), 1), start = new Date(first); start.setDate(1 - first.getDay());
        for (let i = 0; i < 42; i++) {
            const date = new Date(start); date.setDate(start.getDate() + i); const key = dateKey(date), events = allEvents(key);
            const button = document.createElement('button'); button.type = 'button'; button.className = 'calendar-day';
            if (date.getMonth() !== visible.getMonth()) button.classList.add('outside'); if (key === dateKey(today)) button.classList.add('today'); if (key === selected) button.classList.add('selected');
            button.innerHTML = `<span class="day-number">${date.getDate()}</span><div class="day-tasks">${events.slice(0, 2).map(event => `<div class="day-task ${event.official ? 'official-event' : ''}">${event.time ? esc(formatTime(event.time)) + ' · ' : ''}${esc(event.title)}</div>`).join('')}${events.length > 2 ? `<div class="more-tasks">+${events.length - 2} more</div>` : ''}</div>`;
            button.addEventListener('click', () => { selected = key; if (date.getMonth() !== visible.getMonth()) visible = new Date(date.getFullYear(), date.getMonth(), 1); resetForm(); renderAll(); }); grid.appendChild(button);
        }
    }
    function renderTasks() {
        const events = allEvents(selected); $('selectedDateLabel').textContent = formatDate(selected); $('taskCount').textContent = `${events.length} ${events.length === 1 ? 'event' : 'events'}`;
        $('taskList').innerHTML = events.length ? events.map(event => `<article class="task-item ${event.official ? 'official' : ''}"><div class="task-item-head"><div><h3>${esc(event.title)}</h3>${event.time ? `<div class="task-time"><i class="fa-regular fa-clock"></i> ${esc(formatTime(event.time))}</div>` : ''}</div>${event.official ? '<span class="eyebrow">Official</span>' : `<div class="task-buttons"><button class="task-action" data-edit="${esc(event.id)}" title="Edit"><i class="fa-solid fa-pen"></i></button><button class="task-action" data-delete="${esc(event.id)}" title="Delete"><i class="fa-solid fa-trash"></i></button></div>`}</div>${event.notes ? `<p class="task-notes">${esc(event.notes)}</p>` : ''}</article>`).join('') : '<div class="empty-tasks"><i class="fa-regular fa-calendar-check"></i>No events yet.<br>Add a personal reminder above.</div>';
    }
    function renderAll() { renderCalendar(); renderTasks(); }
    form.addEventListener('submit', event => { event.preventDefault(); const item = { id: $('editingId').value || `${Date.now()}`, title: $('taskTitle').value.trim(), time: $('taskTime').value, notes: $('taskNotes').value.trim() }; const list = tasks(selected), index = list.findIndex(x => x.id === item.id); if (index < 0) list.push(item); else list[index] = item; reminders[selected] = list; save(); resetForm(); renderAll(); });
    $('taskList').addEventListener('click', event => { const button = event.target.closest('button'); if (!button) return; const id = button.dataset.edit || button.dataset.delete, item = tasks(selected).find(x => x.id === id); if (button.dataset.delete !== undefined) { if (confirm('Delete this reminder?')) { reminders[selected] = tasks(selected).filter(x => x.id !== id); save(); renderAll(); } } else if (item) { $('editingId').value = item.id; $('taskTitle').value = item.title; $('taskTime').value = item.time || ''; $('taskNotes').value = item.notes || ''; $('cancelEdit').hidden = false; $('saveLabel').textContent = 'Save changes'; } });
    $('cancelEdit').addEventListener('click', resetForm); $('previousMonth').addEventListener('click', () => { visible.setMonth(visible.getMonth() - 1); renderCalendar(); }); $('nextMonth').addEventListener('click', () => { visible.setMonth(visible.getMonth() + 1); renderCalendar(); }); $('todayButton').addEventListener('click', () => { selected = dateKey(today); visible = new Date(today.getFullYear(), today.getMonth(), 1); resetForm(); renderAll(); }); renderAll();
});
