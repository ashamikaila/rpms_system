(() => {
    const list = document.getElementById('workspaceActivity');
    if (!list) return;
    const user = document.body.dataset.workspaceUser;
    const render = () => {
        const entries = [];
        for (const prefix of ['prismStudents', 'prismFaculty']) {
            try {
                const records = JSON.parse(localStorage.getItem(`${prefix}:${user}`) || '[]');
                if (!Array.isArray(records)) continue;
                for (const record of records) {
                    if (!Array.isArray(record.history)) continue;
                    for (const event of record.history) {
                        if (event && event.message) entries.push({ ...event, name: record.name || 'Record' });
                    }
                }
            } catch (_) {}
        }
        entries.sort((a, b) => (Date.parse(b.at) || 0) - (Date.parse(a.at) || 0));
        list.replaceChildren();
        document.getElementById('activityEmpty').hidden = entries.length > 0;
        for (const entry of entries) {
            const item = document.createElement('li');
            const time = document.createElement('time');
            item.textContent = `${entry.name}: ${entry.message}`;
            const timestamp = Date.parse(entry.at);
            time.textContent = Number.isFinite(timestamp) ? new Date(timestamp).toLocaleString() : 'Date not recorded';
            if (Number.isFinite(timestamp)) time.dateTime = new Date(timestamp).toISOString();
            item.append(time);
            list.append(item);
        }
    };
    render();
    window.addEventListener('pageshow', render);
    window.addEventListener('storage', render);
})();
