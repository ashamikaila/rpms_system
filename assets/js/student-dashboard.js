(function (root) {
    const list = value => Array.isArray(value) ? value : [];
    const time = value => Date.parse(value) || 0;
    function review(doc, profile = {}) {
        const explicit = doc.adviserReviewStatus || doc.adviserStatus;
        const assigned = Boolean(doc.adviserId || profile.adviserId || explicit);
        const status = explicit || (assigned ? doc.status : '');
        const revision = ['Revision Requested', 'Revision Required'].includes(status);
        const approved = ['Approved', 'Approved for RPMS Submission'].includes(status);
        const waiting = ['Submitted', 'Under Review', 'Waiting for Review'].includes(status);
        const sent = Boolean(doc.rpmsSubmittedAt);
        return { revision, approved, waiting: waiting && !sent, sent,
            label: revision ? 'Revision Required' : approved ? 'Approved for RPMS Submission' : waiting ? 'Waiting for Review' : 'No adviser review recorded',
            next: sent ? 'Waiting for RPMS update' : revision ? 'Upload revised document' : approved ? 'Ready to Submit to RPMS' : waiting ? 'Waiting for adviser review' : 'Await adviser assignment or review update',
            feedback: doc.adviserFeedback || (assigned && doc.remarks !== 'Awaiting reviewer remarks' ? doc.remarks : '') || '',
            reviewedAt: doc.adviserReviewedAt || (assigned ? doc.reviewedAt : '') || '' };
    }
    function summarize(state, reminders = {}, now = new Date()) {
        const docs = list(state.documents).slice().sort((a,b) => time(b.date) - time(a.date));
        const official = state.official || {}, profile = state.profile || {};
        const active = docs.filter(d => !d.supersededBy && !d.archived);
        const reviews = active.filter(d => review(d, profile).waiting);
        const revisions = active.filter(d => review(d, profile).revision && !review(d, profile).sent);
        const current = active.find(d => review(d, profile).revision && !review(d, profile).sent)
            || active.find(d => !review(d, profile).sent) || active[0] || null;
        const start = new Date(now); start.setHours(0,0,0,0);
        const deadlines = [];
        function add(title, date, completed) {
            if (completed || !/^\d{4}-\d{2}-\d{2}$/.test(date || '')) return;
            const due = new Date(`${date}T00:00:00`);
            if (!Number.isFinite(due.getTime()) || due < start || due.getDate() !== Number(date.slice(8))) return;
            deadlines.push({title, date});
        }
        const pending = list(official.pending);
        pending.forEach(item => add(typeof item === 'string' ? item : item.title || item.name || 'Pending requirement', typeof item === 'string' ? official.deadline : item.dueDate || item.deadline || official.deadline, item.completed));
        if (!pending.length) add('IERB requirement deadline', official.deadline, ['Completed','Approved','Rejected'].includes(official.status));
        Object.entries(reminders || {}).forEach(([date,items]) => list(items).forEach(item => add(item.title, date, item.completed)));
        deadlines.sort((a,b) => a.date.localeCompare(b.date));
        return { docs, reviews, revisions, current, deadlines, official,
            notifications: list(state.notifications).slice().sort((a,b) => time(b.date)-time(a.date)),
            counts: [docs.length, reviews.length, revisions.length, deadlines.length] };
    }
    const esc = value => String(value ?? '').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const date = value => time(value) ? new Date(value).toLocaleString('en-PH',{dateStyle:'medium',timeStyle:'short'}) : 'Not available';
    function render(state, reminders) {
        const host = document.getElementById('studentDashboard'); if (!host) return;
        const data = summarize(state, reminders), doc = data.current, r = doc ? review(doc,state.profile) : null;
        host.querySelectorAll('[data-summary-count]').forEach((node,i)=>node.textContent=data.counts[i]);
        const button = (action,label) => `<button type="button" class="action-btn" data-student-action="${action}" data-document="${esc(doc.id)}">${label}</button>`;
        document.getElementById('currentSubmissionContent').innerHTML = doc ? `<div class="student-current-heading"><i class="fa-regular fa-file-lines" aria-hidden="true"></i><div><h3>${esc(doc.name)}</h3><p>Protocol code: ${esc(doc.protocolCode || 'Not provided')}</p></div></div><dl class="student-detail-grid"><div><dt>Adviser Review</dt><dd>${esc(r.label)}</dd></div><div><dt>Next Action</dt><dd>${esc(r.next)}</dd></div></dl><div class="student-actions">${doc.data ? button('view','View') : ''}${r.feedback ? button('feedback','Feedback') : ''}${r.revision && !r.sent ? button('revision','Upload Revision') : ''}${r.approved && !r.sent ? '<button type="button" class="action-btn" disabled>Submit to RPMS</button><span class="student-action-note">RPMS submission is not connected yet.</span>' : ''}</div>` : '<p class="student-empty">No documents uploaded yet.</p>';
        document.getElementById('adviserReviewContent').innerHTML = `<p class="student-status">${esc(r?.label || 'No adviser review recorded')}</p><p class="student-feedback">${esc(r?.feedback || 'No adviser feedback available.')}</p>${r?.reviewedAt ? `<p>Last reviewed: ${esc(date(r.reviewedAt))}</p>` : ''}${r?.feedback ? button('feedback','View Feedback') : ''}`;
        const o = data.official, stages = ['Initial Submission','Technical Review','IERB Review','Revision / Compliance','Final Decision'];
        const updated = o.updatedAt || list(o.history).slice().sort((a,b)=>time(b.date)-time(a.date))[0]?.date;
        document.getElementById('studentIerbContent').innerHTML = `<dl class="student-detail-grid"><div><dt>Current Stage</dt><dd>${esc(stages[o.stage-1] ? `Stage ${o.stage} — ${stages[o.stage-1]}` : 'Not started')}</dd></div><div><dt>Current Status</dt><dd>${esc(o.status || 'Not started')}</dd></div><div><dt>Pending Requirements</dt><dd>${list(o.pending).length ? list(o.pending).map(p=>esc(typeof p==='string'?p:p.title||p.name||'Pending requirement')).join('<br>') : 'No pending requirements'}</dd></div>${updated ? `<div><dt>Last Updated</dt><dd>${esc(date(updated))}</dd></div>` : ''}</dl>`;
        document.getElementById('studentDeadlines').innerHTML = data.deadlines.slice(0,4).map(item=>`<div class="list-item"><i class="fa-regular fa-calendar" aria-hidden="true"></i><div><strong>${esc(item.title)}</strong><span>${esc(new Date(`${item.date}T00:00:00`).toLocaleDateString('en-PH',{dateStyle:'medium'}))}</span></div></div>`).join('') || '<p class="student-empty">No upcoming deadlines</p>';
        document.getElementById('studentNotifications').innerHTML = data.notifications.slice(0,4).map(n=>`<div class="list-item"><i class="fa-regular fa-bell" aria-hidden="true"></i><div><strong>${esc(n.type)}</strong><span>${esc(n.message)}</span><time>${esc(date(n.date))}</time></div></div>`).join('') || '<p class="student-empty">No recent notifications</p>';
    }
    const api = {review,summarize,render};
    if (typeof module !== 'undefined' && module.exports) module.exports=api;
    else root.StudentDashboard=api;
})(typeof window !== 'undefined' ? window : this);
