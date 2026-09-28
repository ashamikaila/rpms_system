const test = require('node:test');
const assert = require('node:assert/strict');
const { summarize, render } = require('../assets/js/dashboard-overview.js');

test('deadline window includes today through day seven and excludes completed or invalid records', () => {
    const records = [
        { id: 'today', stage: 'Stage 1', deadline: '2026-09-28' },
        { id: 'last', stage: 'Stage 2', deadline: '2026-10-04' },
        { id: 'later', stage: 'Stage 2', deadline: '2026-10-05' },
        { id: 'overdue', stage: 'Stage 2', deadline: '2026-09-27' },
        { id: 'done', stage: 'Completed', deadline: '2026-09-29', status: 'Delayed' },
        { id: 'invalid', stage: 'Stage 1', deadline: '2026-02-30' }
    ];
    const result = summarize(records, [], new Date(2026, 8, 28));
    assert.equal(result.active.length, 5);
    assert.deepEqual(result.deadlines.map(record => record.id), ['today', 'last']);
    assert.equal(result.followup, 1);
    assert.equal(result.preview[0].id, 'overdue');
    assert.equal(result.attention[0].issue, 'Overdue deadline · 2026-09-27');
});

test('outstanding requirements and delays remain distinct from RPMS document reviews', () => {
    const records = [
        { stage: 'Stage 1', status: 'Pending', requirements: 'Consent form' },
        { stage: 'Stage 2', status: 'Delayed', requirements: 'Revised protocol' },
        { stage: 'Stage 1', status: 'On Track', requirements: 'None' }
    ];
    const documents = [
        { reviewStatus: 'Submitted' }, { reviewStatus: 'Under Review' },
        { reviewStatus: 'Verified' }, { reviewStatus: 'Received' },
        { reviewStatus: 'Resubmission Requested' }
    ];
    const result = summarize(records, documents);
    assert.equal(result.pending, 2);
    assert.equal(result.followup, 1);
    assert.equal(result.inProgress, 1);
    assert.equal(result.rpmsPending, 2);
    assert.equal(result.attention.length, 5);
    assert.equal(summarize([], null).rpmsPending, null);
    assert.equal(summarize([], []).rpmsPending, 0);
});

test('activity is sorted by event time, including document reviews', () => {
    const result = summarize([{ history: [null, { message: 'Stage updated', at: '2026-09-28T01:00:00Z' }] }], [
        { uploadedAt: '2026-09-27T01:00:00Z', reviewedAt: '2026-09-28T02:00:00Z', reviewStatus: 'Verified' }
    ]);
    assert.equal(result.activity.length, 3);
    assert.equal(result.activity[0].at, '2026-09-28T02:00:00Z');
});

test('render targets exist in the dashboard and user text is escaped', () => {
    const html = require('node:fs').readFileSync(require('node:path').join(__dirname, '../dashboard.php'), 'utf8');
    const ids = new Map([...html.matchAll(/id="([^"]+)"/g)].map(match => [match[1], {
        innerHTML: '', textContent: '', hidden: false,
        insertAdjacentHTML(_, value) { this.innerHTML += value; }
    }]));
    global.document = { getElementById(id) { assert.ok(ids.has(id), `Missing dashboard target: ${id}`); return ids.get(id); } };
    try {
        render([{ id: '1', name: '<img src=x onerror=alert(1)>', stage: 'Stage 1', requirements: '<script>bad()</script>' }], []);
        assert.ok(ids.get('ierbMonitorBody').innerHTML.includes('&lt;script&gt;'));
        assert.ok(!ids.get('attentionList').innerHTML.includes('<script>'));
        render([], null, { recordsUnavailable: true, documentsUnavailable: true });
        assert.equal(ids.get('activeIerbMetric').textContent, '—');
        assert.equal(ids.get('rpmsPendingMetric').textContent, '—');
        assert.equal(ids.get('overviewDataStatus').hidden, false);
    } finally {
        delete global.document;
    }
});
