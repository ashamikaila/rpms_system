const test = require('node:test');
const assert = require('node:assert/strict');
const {summarize,review} = require('../assets/js/student-dashboard.js');
test('current submission actions follow review state and never submit to RPMS locally',()=>{
    const nodes=new Map();
    global.document={getElementById(id){if(!nodes.has(id))nodes.set(id,{innerHTML:'',querySelectorAll:()=>[]});return nodes.get(id);}};
    const {render}=require('../assets/js/student-dashboard.js');
    try {
        const doc={id:'d1',name:'Protocol',data:'data:application/pdf;base64,',adviserId:'a'};
        const markup=status=>{render({documents:[{...doc,status}],profile:{}});return nodes.get('currentSubmissionContent').innerHTML;};
        assert(!markup('Under Review').includes('Upload Revision'));
        assert(markup('Revision Requested').includes('Upload Revision'));
        assert(!markup('Revision Requested').includes('Submit to RPMS'));
        const approved=markup('Approved');
        assert(approved.includes('Approved for RPMS Submission'));
        assert(approved.includes('disabled>Submit to RPMS'));
        assert(!approved.includes('Upload Revision'));
        assert(!approved.includes('data-student-action="feedback"'));
    } finally {delete global.document;}
});
test('empty student records show zero counts without sample records',()=>{
    const data=summarize({});
    assert.deepEqual(data.counts,[0,0,0,0]);
    assert.equal(data.current,null);
    assert.deepEqual(data.notifications,[]);
});
test('review counts require adviser evidence and exclude superseded or RPMS-submitted documents',()=>{
    const documents=[
        {id:'unassigned',status:'Submitted'},
        {id:'waiting',status:'Under Review',adviserId:'a'},
        {id:'revision',status:'Revision Requested',adviserId:'a'},
        {id:'old',status:'Revision Requested',adviserId:'a',supersededBy:'revision'},
        {id:'sent',status:'Approved',adviserId:'a',rpmsSubmittedAt:'2026-09-28'},
    ];
    const data=summarize({documents});
    assert.deepEqual(data.counts,[5,1,1,0]);
    assert.equal(data.current.id,'revision');
    assert.equal(review(documents[0]).waiting,false);
    assert.equal(review(documents[4]).label,'Approved for RPMS Submission');
    assert.equal(review(documents[4]).next,'Waiting for RPMS update');
    assert.equal(review({status:'Approved'}).approved,false);
});
test('current submission uses newest active record unless revision needs action',()=>{
    const documents=[{id:'old',date:'2026-09-01'},{id:'new',date:'2026-09-28'},{id:'archived',date:'2026-09-29',archived:true}];
    assert.equal(summarize({documents}).current.id,'new');
});
test('deadlines include dated pending requirements and active reminders only',()=>{
    const data=summarize({official:{pending:['Consent form'],deadline:'2026-09-29'}},{
        '2026-09-28':[{title:'Today'}], '2026-09-27':[{title:'Past'}],
        '2026-09-30':[{title:'Done',completed:true}], '2026-02-30':[{title:'Invalid'}]
    },new Date(2026,8,28));
    assert.equal(data.counts[3],2);
    assert.deepEqual(data.deadlines.map(d=>d.title),['Today','Consent form']);
});
test('notifications are existing records ordered newest first',()=>{
    const notices=[{message:'Earlier',date:'2026-09-01'},{message:'Latest',date:'2026-09-28'}];
    assert.equal(summarize({notifications:notices}).notifications[0].message,'Latest');
    assert.equal(notices[0].message,'Earlier');
});
