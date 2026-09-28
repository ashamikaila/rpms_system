const test=require('node:test');
const assert=require('node:assert/strict');
const {summarize,applyReview}=require('../assets/js/adviser-portal.js');
function records(){return {students:[{id:'s1',adviserId:'a1'},{id:'s2',adviserId:'a2'}],documents:[{id:'d1',studentId:'s1',status:'Submitted'},{id:'d2',studentId:'s2',status:'Submitted'}],official:{stage:2,status:'Under Review'}};}
test('adviser dashboard contains only assigned student documents',()=>{
    const data=summarize(records(),'a1');assert.equal(data.students.length,1);assert.deepEqual(data.pending.map(d=>d.id),['d1']);assert.equal(summarize(records(),'').documents.length,0);
});
test('approval records adviser review without changing official IERB data',()=>{
    const state=records();applyReview(state,'a1','d1','Approved','Ready for RPMS');assert.equal(summarize(state,'a1').approved.length,1);assert.equal(state.documents[0].adviserFeedback,'Ready for RPMS');assert.deepEqual(state.official,{stage:2,status:'Under Review'});
});
test('revision requires feedback and review rejects unassigned documents',()=>{
    const state=records();assert.throws(()=>applyReview(state,'a1','d1','Revision Requested',' '));assert.throws(()=>applyReview(state,'a1','d2','Approved',''));applyReview(state,'a1','d1','Revision Requested','Revise consent form');assert.equal(summarize(state,'a1').revision.length,1);assert.throws(()=>applyReview(state,'a1','d1','Approved',''));
});
test('empty adviser dashboard does not fabricate counts',()=>{
    const data=summarize({},'a1');for(const field of ['students','pending','revision','approved'])assert.equal(data[field].length,0);
});
test('resubmissions require explicit workflow evidence and a recorded date',()=>{
    const state=records();state.documents.push(
        {id:'revision',studentId:'s1',revisionOf:'d1',date:'2026-09-29',status:'Submitted'},
        {id:'filename-only',studentId:'s1',name:'Revised protocol.pdf',date:'2026-09-30',status:'Submitted'},
        {id:'undated',studentId:'s1',revisionOf:'d1',status:'Submitted'},
        {id:'other',studentId:'s2',revisionOf:'d2',date:'2026-09-30',status:'Submitted'}
    );
    assert.deepEqual(summarize(state,'a1').resubmissions.map(d=>d.id),['revision']);
});
test('activity is attributed to the current reviewer and notifications remain scoped',()=>{
    const state=records();applyReview(state,'a1','d1','Approved','Ready',new Date('2026-09-28T10:00:00Z'));
    state.notifications=[{id:'own',adviserId:'a1',studentId:'s1',documentId:'d1'},{id:'wrong-student',studentId:'s2'},{id:'wrong-adviser',adviserId:'a2'},{id:'wrong-document',documentId:'d2'}];
    const data=summarize(state,'a1');assert.equal(data.activity[0].action,'Approved for RPMS Submission');assert.deepEqual(data.notifications.map(n=>n.id),['own']);
    state.documents[0].adviserReviewedBy='a2';assert.equal(summarize(state,'a1').activity.length,0);
});
test('RPMS-submitted documents are excluded from the pending review queue',()=>{
    const state=records();state.documents[0].rpmsSubmittedAt='2026-09-28';assert.equal(summarize(state,'a1').pending.length,0);assert.throws(()=>applyReview(state,'a1','d1','Approved',''));
});
