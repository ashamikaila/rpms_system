/* Dashboard summaries use the same records as IERB Monitoring and Document Submissions. */
(() => {
    const esc = value => String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    })[char]);
    const dayKey = date => `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    const deadlineKey = value => {
        if (!/^\d{4}-\d{2}-\d{2}$/.test(String(value || ''))) return '';
        const parsed = new Date(`${value}T00:00:00`);
        return Number.isFinite(parsed.getTime()) && dayKey(parsed) === value ? value : '';
    };
    const hasRequirements = record => {
        const text = String(record.requirements || '').trim();
        return text !== '' && !/^(none|n\/a|no pending requirements)$/i.test(text);
    };
    const recordName = record => record.groupId || record.studentId || record.name || 'IERB record';
    const recordLink = record => `ierbprog.php?record=${encodeURIComponent(record.id || '')}`;
    const documentLink = record => `documents.php?record=${encodeURIComponent(record.id || '')}`;

    function summarize(records, documents, now = new Date()) {
        const today = dayKey(now);
        const end = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 6);
        const lastDay = dayKey(end);
        const active = records.filter(record => record.stage !== 'Completed');
        const overdue = record => !!deadlineKey(record.deadline) && record.deadline < today;
        const followup = record => record.status === 'Delayed' || overdue(record);
        const pending = record => record.status === 'Pending' || hasRequirements(record);
        const deadlines = active.filter(record => deadlineKey(record.deadline) && record.deadline >= today && record.deadline <= lastDay)
            .sort((a, b) => a.deadline.localeCompare(b.deadline));
        const attention = [];
        active.forEach(record => {
            if (followup(record)) {
                attention.push({ name: recordName(record), issue: overdue(record) ? `Overdue deadline · ${record.deadline}` : 'Progress marked as delayed',
                    detail: hasRequirements(record) ? record.requirements : 'Check progress and coordinate a follow-up.',
                    action: 'Review IERB record', href: recordLink(record), priority: 0 });
            } else if (hasRequirements(record)) {
                attention.push({ name: recordName(record), issue: 'Outstanding requirements', detail: record.requirements,
                    action: 'Review requirements', href: recordLink(record), priority: 1 });
            }
        });
        const docs = documents || [];
        const rpmsPending = docs.filter(doc => ['Submitted', 'Under Review'].includes(doc.reviewStatus || 'Submitted'));
        docs.forEach(doc => {
            if (doc.reviewStatus === 'Resubmission Requested') {
                attention.push({ name: doc.student || doc.originalName, issue: 'RPMS resubmission requested',
                    detail: doc.reviewRemarks || 'Coordinate the revised document with the student.',
                    action: 'View document', href: documentLink(doc), priority: 1 });
            } else if (rpmsPending.includes(doc)) {
                attention.push({ name: doc.student || doc.originalName, issue: 'Awaiting RPMS review',
                    detail: doc.originalName, action: 'Review document', href: documentLink(doc), priority: 2 });
            }
        });
        attention.sort((a, b) => a.priority - b.priority || String(a.name).localeCompare(String(b.name)));
        const activity = [];
        records.forEach(record => {
            (Array.isArray(record.history) ? record.history : []).forEach(event => {
                if (event && event.message) activity.push({ name: recordName(record), message: event.message, at: event.at, href: recordLink(record) });
            });
        });
        docs.forEach(doc => {
            if (doc.uploadedAt) activity.push({ name: doc.student || 'Document submission', message: `Uploaded ${doc.originalName}`, at: doc.uploadedAt, href: documentLink(doc) });
            if (doc.reviewedAt) activity.push({ name: doc.reviewedBy || 'RPMS review', message: `${doc.originalName}: ${doc.reviewStatus}`, at: doc.reviewedAt, href: documentLink(doc) });
        });
        activity.sort((a, b) => (Date.parse(b.at) || 0) - (Date.parse(a.at) || 0));
        return {
            active, deadlines, attention, activity,
            rpmsPending: documents === null ? null : rpmsPending.length,
            inProgress: active.filter(record => !pending(record) && !followup(record)).length,
            pending: active.filter(pending).length,
            followup: active.filter(followup).length,
            preview: [...active].sort((a, b) => Number(followup(b)) - Number(followup(a)) || (Date.parse(b.updatedAt) || 0) - (Date.parse(a.updatedAt) || 0)).slice(0, 5),
            status: record => followup(record) ? 'Requires follow-up' : pending(record) ? 'Pending' : 'In progress'
        };
    }

    function render(records, documents, { recordsUnavailable = false, documentsUnavailable = false } = {}) {
        const snapshot = summarize(records, documents);
        const set = (id, text) => { document.getElementById(id).textContent = text; };
        const empty = text => `<li class="overview-empty">${esc(text)}</li>`;
        set('activeIerbMetric', recordsUnavailable ? '—' : snapshot.active.length);
        set('rpmsPendingMetric', snapshot.rpmsPending ?? '—');
        set('upcomingDeadlinesMetric', recordsUnavailable ? '—' : snapshot.deadlines.length);
        set('ierbInProgressCount', recordsUnavailable ? '—' : snapshot.inProgress);
        set('ierbPendingCount', recordsUnavailable ? '—' : snapshot.pending);
        set('ierbFollowupCount', recordsUnavailable ? '—' : snapshot.followup);
        const notices = [];
        if (recordsUnavailable) notices.push('IERB records could not be loaded.');
        if (documentsUnavailable) notices.push('Document reviews could not be loaded.');
        const notice = document.getElementById('overviewDataStatus');
        notice.hidden = notices.length === 0;
        notice.textContent = `${notices.join(' ')} Some dashboard information may be unavailable. Refresh to try again.`;
        document.getElementById('ierbMonitorBody').innerHTML = snapshot.preview.map(record => `<tr>
            <td><a class="overview-record-link" href="${recordLink(record)}">${esc(String(record.protocolCode || '').trim() || 'Not assigned')}</a><small>${esc(record.researchTitle || 'Research title not set')}</small></td>
            <td>${esc(record.stage || 'Not recorded')}</td>
            <td>${esc(hasRequirements(record) ? record.requirements : 'No pending requirements')}</td>
            <td><span class="overview-status">${esc(snapshot.status(record))}</span></td>
        </tr>`).join('') || (recordsUnavailable ? '<tr><td colspan="4" class="overview-empty">IERB records are unavailable.</td></tr>' : '');
        set('ierbPreviewCount', snapshot.active.length ? `Showing ${snapshot.preview.length} of ${snapshot.active.length} active records. Follow-ups appear first.` : '');
        set('attentionCount', snapshot.attention.length);
        document.getElementById('attentionList').innerHTML = snapshot.attention.slice(0, 5).map(item => `<li>
            <span class="overview-item-label">${esc(item.name)}</span><strong>${esc(item.issue)}</strong><p>${esc(item.detail)}</p>
            <a class="overview-link" href="${item.href}">${esc(item.action)} <span aria-hidden="true">&rarr;</span></a>
        </li>`).join('') || (recordsUnavailable || documentsUnavailable ? empty('Attention items are unavailable for sources that could not be loaded.') : '');
        if (snapshot.attention.length > 5) document.getElementById('attentionList').insertAdjacentHTML('beforeend', empty(`Showing 5 of ${snapshot.attention.length} issues. Open IERB Monitoring or Document Submissions for the full queues.`));
        document.getElementById('officialDeadlineList').innerHTML = snapshot.deadlines.map(record => `<li class="overview-deadline">
            <time datetime="${esc(record.deadline)}">${esc(new Date(`${record.deadline}T00:00:00`).toLocaleDateString('en-PH', { month: 'short', day: 'numeric' }))}</time>
            <div><a class="overview-record-link" href="${recordLink(record)}">${esc(recordName(record))}</a><p>${esc(hasRequirements(record) ? record.requirements : record.researchTitle || 'IERB submission')}</p></div>
        </li>`).join('') || (recordsUnavailable ? empty('Official deadlines are unavailable.') : '');
        document.getElementById('recentActivityList').innerHTML = snapshot.activity.slice(0, 6).map(event => {
            const timestamp = Date.parse(event.at);
            const date = Number.isFinite(timestamp) ? new Date(timestamp).toLocaleString('en-PH', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit' }) : 'Date not recorded';
            return `<li><span class="activity-dot" aria-hidden="true"></span><div><a class="overview-record-link" href="${event.href}">${esc(event.name)}</a><p>${esc(event.message)}</p><time${Number.isFinite(timestamp) ? ` datetime="${esc(new Date(timestamp).toISOString())}"` : ''}>${esc(date)}</time></div></li>`;
        }).join('') || (recordsUnavailable || documentsUnavailable ? empty('Activity is unavailable for sources that could not be loaded.') : '');
    }

    const api = { summarize, render };
    if (typeof module !== 'undefined' && module.exports) module.exports = api;
    else window.PrismDashboard = api;
})();
